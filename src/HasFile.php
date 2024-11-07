<?php

namespace JobMetric\Media;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Exceptions\CollectionNotInMediaAllowCollectionMethodException;
use JobMetric\Media\Exceptions\MediaCollectionNotMatchException;
use JobMetric\Media\Exceptions\MediaNotFoundException;
use JobMetric\Media\Exceptions\MediaRelationNotFoundException;
use JobMetric\Media\Exceptions\MediaTypeNotMatchException;
use JobMetric\Media\Exceptions\ModelMediaContractNotFoundException;
use JobMetric\Media\Http\Resources\MediaResource;
use JobMetric\Media\Models\Media;
use Throwable;

/**
 * Trait HasFile
 *
 * @package JobMetric\Media
 *
 * @property Media[] files
 *
 * @method getKey()
 * @method morphToMany(string $class, string $string, string $string1)
 */
trait HasFile
{
    /**
     * boot has file
     *
     * @return void
     * @throws Throwable
     */
    public static function bootHasFile(): void
    {
        if (!in_array('JobMetric\Media\Contracts\MediaContract', class_implements(self::class))) {
            throw new ModelMediaContractNotFoundException(self::class);
        }
    }

    /**
     * media has many relationships
     *
     * @return MorphToMany
     */
    public function files(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediaable', config('media.tables.media_relation'))
            ->withPivot('collection')
            ->withTimestamps(['created_at']);
    }

    /**
     * attach media
     *
     * @param int $media_id
     * @param string $collection
     *
     * @return array
     * @throws Throwable
     */
    public function attachMedia(int $media_id, string $collection = 'base'): array
    {
        $collections = $this->mediaAllowCollections();

        /**
         * @var Media $media
         */
        $media = Media::find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if (!array_key_exists($collection, $collections)) {
            throw new CollectionNotInMediaAllowCollectionMethodException($collection);
        }

        if ($media->type != MediaTypeEnum::FILE()) {
            throw new MediaTypeNotMatchException($media_id, MediaTypeEnum::FILE());
        }

        if ($media->collection != $collections[$collection]['media_collection'] ?? 'public') {
            throw new MediaCollectionNotMatchException($media_id, $media->collection, $collection);
        }

        if ($this->files()->wherePivot('media_id', $media_id)->wherePivot('collection', $collection)->exists()) {
            return [
                'ok' => true,
                'message' => trans('media::base.messages.already_attached'),
                'data' => MediaResource::make($media),
                'status' => 200
            ];
        }

        $this->files()->attach($media_id, [
            'collection' => $collection
        ]);

        return [
            'ok' => true,
            'message' => trans('media::base.messages.attached'),
            'data' => MediaResource::make($media),
            'status' => 200
        ];
    }

    /**
     * detach media
     *
     * @param int $media_id
     *
     * @return array
     * @throws Throwable
     */
    public function detachMedia(int $media_id): array
    {
        foreach ($this->files as $file) {
            if ($file->id == $media_id) {
                $data = MediaResource::make($file);

                $this->files()->detach($media_id);

                return [
                    'ok' => true,
                    'message' => trans('media::base.messages.detached'),
                    'data' => $data,
                    'status' => 200
                ];
            }
        }

        throw new MediaRelationNotFoundException(self::class, $this->getKey(), $media_id);
    }

    /**
     * Get media by collection
     *
     * @param string $collection
     *
     * @return MorphToMany
     */
    public function getMediaByCollection(string $collection = 'base'): MorphToMany
    {
        return $this->files()->wherePivot('collection', $collection);
    }

    /**
     * Get the media data for the object
     *
     * @return array
     */
    public function getMediaDataForObject(): array
    {
        $data = [];
        foreach ($this->files as $item) {
            $data[$item->pivot->collection][] = $item->id;
        }

        return $data;
    }
}
