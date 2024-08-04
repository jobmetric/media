<?php

namespace JobMetric\Media;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Exceptions\MediaNameInvalidException;
use JobMetric\Media\Exceptions\MediaNotFoundException;
use JobMetric\Media\Exceptions\MediaSameNameException;
use JobMetric\Media\Exceptions\MediaTypeNotMatchException;
use JobMetric\Media\Http\Resources\MediaRelationResource;
use JobMetric\Media\Http\Resources\MediaResource;
use JobMetric\Media\Models\Media as MediaModel;
use JobMetric\Media\Models\MediaRelation;
use JobMetric\Media\ServiceTrait\{DeleteMedia, FileMedia, FolderMedia, ListMedia, ZipArchiveMedia};
use Throwable;

class Media
{
    use ListMedia, FolderMedia, FileMedia, DeleteMedia, ZipArchiveMedia;

    /**
     * The application instance.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * Create a new Translation instance.
     *
     * @param Application $app
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Rename media
     *
     * @param int $media_id
     * @param string $name
     *
     * @return array
     * @throws Throwable
     */
    public function rename(int $media_id, string $name): array
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::query()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::FOLDER()) {
            $mode = trans('media::base.media_type.folder');
            if (!$this->isValidFolderName($name)) {
                throw new MediaNameInvalidException($mode, $name);
            }
        } else {
            $mode = trans('media::base.media_type.file');
            if (!$this->isValidFileName($name)) {
                throw new MediaNameInvalidException($mode, $name);
            }
        }

        // check exist name in parent folder
        $exist = MediaModel::query()->where([
            'name' => $name,
            'parent_id' => $media->parent_id
        ])->where('id', '!=', $media_id)->exists();

        if ($exist) {
            throw new MediaSameNameException($name);
        }

        $media->name = $name;

        $media->save();

        return [
            'ok' => true,
            'message' => trans('media::base.messages.rename', [
                'type' => $mode,
            ]),
            'data' => MediaResource::make($media),
            'status' => 200
        ];
    }

    /**
     * Details media
     *
     * @param int $media_id
     *
     * @return array
     * @throws Throwable
     */
    public function details(int $media_id): array
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::withTrashed()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        return [
            'ok' => true,
            'message' => trans('media::base.messages.details', [
                'type' => trans('media::base.media_type.' . ($media->type == MediaTypeEnum::FOLDER() ? 'folder' : 'file')),
            ]),
            'data' => MediaResource::make($media),
            'used_in' => $this->usedIn($media_id),
            'status' => 200
        ];
    }

    /**
     * Used In media
     *
     * @param int $media_id
     *
     * @return AnonymousResourceCollection
     * @throws Throwable
     */
    public function usedIn(int $media_id): AnonymousResourceCollection
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::withTrashed()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::FOLDER()) {
            throw new MediaTypeNotMatchException($media_id, 'file');
        }

        $media_relations = MediaRelation::query()->where([
            'media_id' => $media_id
        ])->get();

        return MediaRelationResource::collection($media_relations);
    }

    /**
     * Has Used media
     *
     * @param int $media_id
     *
     * @return bool
     * @throws Throwable
     */
    public function hasUsed(int $media_id): bool
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::withTrashed()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::FOLDER()) {
            throw new MediaTypeNotMatchException($media_id, 'file');
        }

        return MediaRelation::query()->where([
            'media_id' => $media_id
        ])->exists();
    }

    /**
     * Move media
     *
     * @param int $media_id
     * @param int|null $parent_id
     *
     * @return void
     */
    public function move(int $media_id, int $parent_id = null): void
    {
    }
}
