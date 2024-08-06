<?php

namespace JobMetric\Media\ServiceTrait;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Events\ZipFileEvent;
use JobMetric\Media\Exceptions\MediaMustInSameFolderException;
use JobMetric\Media\Exceptions\MediaNameInvalidException;
use JobMetric\Media\Exceptions\MediaNotFoundException;
use JobMetric\Media\Exceptions\MediaSameNameException;
use JobMetric\Media\Http\Resources\MediaResource;
use JobMetric\Media\Models\Media;
use JobMetric\Media\Models\MediaPath;
use Throwable;
use ZipArchive;

/**
 * Trait ZipArchiveMedia
 *
 * @package JobMetric\Media\ServiceTrait
 */
trait ZipArchiveMedia
{
    /**
     * Compress media
     *
     * @param array $media_ids
     * @param string $name
     *
     * @return array
     * @throws Throwable
     */
    public function compress(array $media_ids, string $name): array
    {
        if (!$this->isValidFileName($name)) {
            throw new MediaNameInvalidException(trans('media::base.media_type.file'), $name);
        }

        $parent_id = -1;
        $media_array = [];

        foreach ($media_ids as $media_id) {
            /**
             * @var Media $media
             */
            $media = Media::query()->find($media_id);

            if (!$media) {
                throw new MediaNotFoundException($media_id);
            }

            if ($parent_id == -1) {
                $parent_id = $media->parent_id;
            } else {
                if ($parent_id != $media->parent_id) {
                    throw new MediaMustInSameFolderException;
                }
            }

            $media_array[] = $media;
        }

        // check exist name in parent folder
        $exist = Media::query()->where([
            'name' => $name,
            'extension' => 'zip',
            'parent_id' => $parent_id
        ])->exists();

        if ($exist) {
            throw new MediaSameNameException($name . '.zip');
        }

        $content_id = null;
        $folders = [];
        foreach ($media_array as $item) {
            if ($item->type == MediaTypeEnum::FOLDER()) {
                $folders[] = [
                    'type' => 'folder',
                    'name' => $item->name,
                    'sub-folder' => $this->getSubFolder($item->id, $content_id),
                ];
            } else {
                $content_id .= $item->content_id;
                $folders[] = [
                    'type' => 'file',
                    'name' => $item->name . '.' . $item->extension,
                    'disk' => $item->disk,
                    'path' => $item->collection . '/' . $item->uuid . '.' . $item->extension,
                ];
            }
        }

        $content_id = sha1($content_id);

        $media = Media::query()->where([
            'parent_id' => $parent_id,
            'content_id' => $content_id
        ])->first();

        if ($media) {
            return [
                'ok' => true,
                'message' => trans('media::base.messages.zipped'),
                'data' => MediaResource::make($media),
                'status' => 200
            ];
        }

        $uuid = (string)Str::uuid();

        // add empty zip file
        Storage::disk('media_archive')->put($uuid . '.zip', '');

        // get the path current zip file
        $zipFile = config('filesystems.disks.media_archive.root') . '/' . $uuid . '.zip';

        $zip = new ZipArchive;
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($folders as $key => $item) {
                if ($item['type'] == 'folder') {
                    $zip->addEmptyDir($item['name']);
                    if (!empty($item['sub-folder'])) {
                        $this->addFilesToZip($item['name'], $item['sub-folder'], $zip);
                    }
                } else {
                    $zip->addFromString($item['name'], Storage::disk($item['disk'])->get($item['path']));
                }
            }

            $zip->close();
        } else {
            throw new Exception('Cannot create zip file');
        }

        // update the zip file
        Storage::disk('media_archive')->put($uuid . '.zip', file_get_contents($zipFile));

        $info = [];
        if (auth()->guard()->check()) {
            $info['user_id'] = auth()->guard()->id();
        }

        $mime_type = 'application/zip';
        $size = Storage::disk('media_archive')->size($uuid . '.zip');

        /**
         * @var Media $media
         */
        $media = Media::query()->create([
            'name' => $name,
            'parent_id' => $parent_id,
            'type' => MediaTypeEnum::FILE(),
            'mime_type' => $mime_type,
            'size' => $size,
            'content_id' => $content_id,
            'info' => $info,
            'disk' => 'media_archive',
            'collection' => 'archive',
            'uuid' => $uuid,
            'extension' => 'zip'
        ]);

        $level = 0;

        $paths = MediaPath::query()->select('path_id')->where([
            'media_id' => $parent_id
        ])->orderBy('level')->get()->toArray();

        $paths[] = [
            'path_id' => $media->id
        ];

        foreach ($paths as $path) {
            $mediaPath = new MediaPath;
            $mediaPath->media_id = $media->id;
            $mediaPath->path_id = $path['path_id'];
            $mediaPath->level = $level++;
            $mediaPath->save();

            unset($mediaPath);
        }

        event(new ZipFileEvent($media));

        return [
            'ok' => true,
            'message' => trans('media::base.messages.zipped'),
            'data' => MediaResource::make($media),
            'status' => 201
        ];
    }

    /**
     * Extract media
     *
     * @param int $media_id
     *
     * @return void
     */
    public function extract(int $media_id): void
    {
    }

    private function getSubFolder(int $parent_id, string|null &$content_id): array
    {
        $folders = [];

        $media = Media::query()->where([
            'parent_id' => $parent_id
        ])->orderBy('type')->get();

        foreach ($media as $item) {
            if ($item->type == MediaTypeEnum::FOLDER()) {
                $folders[] = [
                    'type' => 'folder',
                    'name' => $item->name,
                    'sub-folder' => $this->getSubFolder($item->id, $content_id),
                ];
            } else {
                $content_id .= $item->content_id;
                $folders[] = [
                    'type' => 'file',
                    'name' => $item->name . '.' . $item->extension,
                    'disk' => $item->disk,
                    'path' => $item->collection . '/' . $item->uuid . '.' . $item->extension,
                ];
            }
        }

        return $folders;
    }

    private function addFilesToZip($folderName, $items, &$zip): void
    {
        foreach ($items as $key => $item) {
            if ($item['type'] == 'folder') {
                $zip->addEmptyDir("$folderName/" . $item['name']);
                $this->addFilesToZip("$folderName/" . $item['name'], $item['sub-folder'], $zip);
            } else {
                $zip->addFromString("$folderName/" . $item['name'], Storage::disk($item['disk'])->get($item['path']));
            }
        }
    }
}
