<?php

namespace JobMetric\Media\ServiceTrait;

use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Events\NewFolderEvent;
use JobMetric\Media\Exceptions\MediaNameInvalidException;
use JobMetric\Media\Exceptions\MediaNotFoundException;
use JobMetric\Media\Exceptions\MediaSameNameException;
use JobMetric\Media\Exceptions\MediaTypeNotMatchException;
use JobMetric\Media\Http\Resources\MediaResource;
use JobMetric\Media\Models\Media;
use JobMetric\Media\Models\MediaPath;
use Throwable;

/**
 * Trait FolderMedia
 *
 * @package JobMetric\Media\ServiceTrait
 */
trait FolderMedia
{
    /**
     * New Folder
     *
     * @param string $name
     * @param int|null $parent_id
     *
     * @return array
     * @throws Throwable
     */
    public function newFolder(string $name, int $parent_id = null): array
    {
        if (!$this->isValidFolderName($name)) {
            throw new MediaNameInvalidException('folder', $name);
        }

        if ($parent_id) {
            /**
             * @var Media $parent
             */
            $parent = Media::query()->find($parent_id);

            if (!$parent) {
                throw new MediaNotFoundException($parent_id);
            }

            if ($parent->type != MediaTypeEnum::FOLDER()) {
                throw new MediaTypeNotMatchException($parent_id, MediaTypeEnum::FOLDER());
            }

            unset($parent);
        }

        // check exist name in parent folder
        $exist = Media::query()->where([
            'name' => $name,
            'parent_id' => $parent_id
        ])->exists();

        if ($exist) {
            throw new MediaSameNameException($name);
        }

        /**
         * @var Media $media
         */
        $media = Media::query()->create([
            'name' => $name,
            'parent_id' => $parent_id,
            'type' => MediaTypeEnum::FOLDER(),
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

        event(new NewFolderEvent($media));

        return [
            'ok' => true,
            'message' => trans('media::base.messages.created', [
                'type' => trans('media::base.media_type.folder'),
            ]),
            'data' => MediaResource::make($media),
            'status' => 201
        ];
    }

    /**
     * Has Folder
     *
     * @param int $media_id
     *
     * @return bool
     * @throws Throwable
     */
    public function hasFolder(int $media_id): bool
    {
        /**
         * @var Media $media
         */
        $media = Media::query()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::FOLDER()) {
            return true;
        }

        return false;
    }

    /**
     * check folder name is valid
     *
     * @param string $folderName
     *
     * @return false|int
     */
    private function isValidFolderName(string $folderName): bool|int
    {
        $pattern = '/^(?!-)[a-zA-Z0-9]+(-[a-zA-Z0-9]+)*$/';

        return preg_match($pattern, $folderName);
    }
}
