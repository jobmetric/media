<?php

namespace JobMetric\Media\ServiceTrait;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Events\UploadFileEvent;
use JobMetric\Media\Exceptions\DiskNotDefinedException;
use JobMetric\Media\Exceptions\DuplicateFileException;
use JobMetric\Media\Exceptions\FileNotSendInRequestException;
use JobMetric\Media\Exceptions\MediaCollectionNotInConfigException;
use JobMetric\Media\Exceptions\MediaMaxSizeException;
use JobMetric\Media\Exceptions\MediaMimeTypeException;
use JobMetric\Media\Exceptions\MediaNameInvalidException;
use JobMetric\Media\Exceptions\MediaNotFoundException;
use JobMetric\Media\Exceptions\MediaSameNameException;
use JobMetric\Media\Exceptions\MediaTypeNotMatchException;
use JobMetric\Media\Http\Resources\MediaResource;
use JobMetric\Media\Models\Media;
use JobMetric\Media\Models\MediaPath;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * Trait FileMedia
 *
 * @package JobMetric\Media\ServiceTrait
 *
 * @method hasFolder(int $parent_id)
 */
trait FileMedia
{
    /**
     * Upload media
     *
     * @param int|null $parent_id
     * @param string $collection
     * @param string $field
     *
     * @return array
     * @throws Throwable
     */
    public function upload(int $parent_id = null, string $collection = 'public', string $field = 'file'): array
    {
        // check parent folder
        if ($parent_id && !$this->hasFolder($parent_id)) {
            throw new MediaNotFoundException($parent_id);
        }

        // check the collection
        $config_collections = config('media.collections');

        if (!array_key_exists($collection, $config_collections)) {
            throw new MediaCollectionNotInConfigException($collection);
        }

        // check file
        if (!request()->hasFile($field)) {
            throw new FileNotSendInRequestException($field);
        }

        $file = request()->file($field);

        // check disk
        if ($config_collections[$collection]['disk'] == 'default') {
            $disk = config('filesystems.default');
        } else {
            $disk = $config_collections[$collection]['disk'];
        }

        if (!array_key_exists($disk, config('filesystems.disks'))) {
            throw new DiskNotDefinedException($disk);
        }

        // check duplicate content
        $content_id = sha1($file->getContent());

        if (!$config_collections[$collection]['duplicate_content']) {
            if (Media::query()->where([
                'collection' => $collection,
                'content_id' => $content_id
            ])->exists()) {
                throw new DuplicateFileException;
            }
        }

        // check size
        $size = $file->getSize();

        if ($config_collections[$collection]['max_size'] != -1) {
            if ($size > $config_collections[$collection]['max_size']) {
                throw new MediaMaxSizeException($config_collections[$collection]['max_size']);
            }
        }

        // check the mime type
        $mime_type = $file->getMimeType();

        $config_mime_types = [];

        if ($config_collections[$collection]['mime_type'] == 'any') {
            foreach (config('media.mime_type') as $type_group) {
                $config_mime_types = array_merge($config_mime_types, $type_group);
            }
        } else {
            $mime_type_parts = explode(',', $config_collections[$collection]['mime_type']);

            foreach ($mime_type_parts as $mime_type_part) {
                $config_mime_types = array_merge($config_mime_types, config('media.mime_type.' . $mime_type_part));
            }
        }

        if (!in_array($mime_type, $config_mime_types)) {
            throw new MediaMimeTypeException($mime_type);
        }

        // check the file name
        $original_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        if (!$this->isValidFileName($original_name)) {
            throw new MediaNameInvalidException(trans('media::base.media_type.file'), $original_name);
        }

        $uuid = (string)Str::uuid();
        $extension = $file->extension();
        $filename = $uuid . '.' . $extension;

        // check exist name in parent folder
        $exist = Media::query()->where([
            'name' => $original_name,
            'extension' => $extension,
            'parent_id' => $parent_id
        ])->exists();

        if ($exist) {
            throw new MediaSameNameException($original_name . '.' . $extension);
        }

        try {
            $file->storeAs($collection, $filename, $disk);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode());
        }

        $info = [];
        if (auth()->guard()->check()) {
            $info['user_id'] = auth()->guard()->id();
        }

        /**
         * @var Media $media
         */
        $media = Media::query()->create([
            'name' => $original_name,
            'parent_id' => $parent_id,
            'type' => MediaTypeEnum::FILE(),
            'mime_type' => $mime_type,
            'size' => $size,
            'content_id' => $content_id,
            'info' => $info,
            'disk' => $disk,
            'collection' => $collection,
            'uuid' => $uuid,
            'extension' => $extension
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

        event(new UploadFileEvent($media));

        return [
            'ok' => true,
            'message' => trans('media::base.messages.created', [
                'type' => trans('media::base.media_type.file'),
            ]),
            'data' => MediaResource::make($media),
            'status' => 201
        ];
    }

    /**
     * Download media
     *
     * @param int $media_id
     * @param array $headers
     *
     * @return StreamedResponse
     * @throws Throwable
     */
    public function download(int $media_id, array $headers = []): StreamedResponse
    {
        /**
         * @var Media $media
         */
        $media = Media::query()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::FOLDER()) {
            throw new MediaTypeNotMatchException($media_id, 'file');
        }

        return Storage::drive($media->disk)->download($media->collection . '/' . $media->uuid . '.' . $media->extension, $media->name . '.' . $media->extension, $headers);
    }

    /**
     * Temporary url for s3 partition media
     *
     * @param int $media_id
     * @param int $expire_time // minutes
     *
     * @return string
     * @throws Throwable
     */
    public function temporaryUrl(int $media_id, int $expire_time = 60): string
    {
        /**
         * @var Media $media
         */
        $media = Media::query()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::FOLDER()) {
            throw new MediaTypeNotMatchException($media_id, 'file');
        }

        return Storage::drive($media->disk)->temporaryUrl($media->collection . '/' . $media->uuid . '.' . $media->extension, now()->addMinutes($expire_time), [
            'ResponseContentType' => 'application/octet-stream',
            'ResponseContentDisposition' => 'attachment; filename=' . $media->name . '.' . $media->extension,
        ]);
    }

    /**
     * Stream media
     *
     * @param int $media_id
     *
     * @return void
     */
    public function stream(int $media_id): void
    {
    }

    /**
     * Has File
     *
     * @param int $media_id
     *
     * @return bool
     * @throws Throwable
     */
    public function hasFile(int $media_id): bool
    {
        /**
         * @var Media $media
         */
        $media = Media::query()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::File()) {
            return true;
        }

        return false;
    }

    /**
     * check file name is valid
     *
     * @param string $fileName
     *
     * @return false|int
     */
    private function isValidFileName(string $fileName): bool|int
    {
        $pattern = '/^(?!-)[a-zA-Z0-9]+(-[a-zA-Z0-9]+)*$/';

        return preg_match($pattern, $fileName);
    }
}
