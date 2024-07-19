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
use JobMetric\Media\Models\Media;
use Throwable;

/**
 * Trait HasFile
 *
 * @package JobMetric\Media
 *
 * @property Media[] files
 *
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
    public static function bootHasMedia(): void
    {
        if (!in_array('JobMetric\Media\Contracts\MediaContract', class_implements(self::class))) {
            throw new ModelMediaContractNotFoundException(self::class);
        }
    }

    /**
     * media allow collections.
     *
     * @return array
     */
    public function mediaAllowCollections(): array
    {
        return [
            'base' => [
                'media_type' => MediaTypeEnum::FILE(),
                'media_collection' => 'public',
                'size' => [
                    'default' => [
                        'w' => config('media.default_image_size.width'),
                        'h' => config('media.default_image_size.height'),
                    ]
                ]
            ],
        ];
    }

    /**
     * media has many relationships
     *
     * @return MorphToMany
     */
    public function files(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'relatable', 'media_relations');
    }

    /**
     * Store media
     *
     * @param int $media_id
     * @param string $collection
     * @param string $media_type
     *
     * @return bool
     * @throws Throwable
     */
    public function storeMedia(int $media_id, string $collection = 'base', string $media_type = 'f'): bool
    {
        $collections = $this->mediaAllowCollections();

        /**
         * @var Media $media
         */
        $media = Media::find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if (array_key_exists($collection, $collections)) {
            if ($media->type != $media_type || $media->type != $collections[$collection]['media_type'] ?? MediaTypeEnum::FILE()) {
                throw new MediaTypeNotMatchException($media_id, $media_type);
            }

            if ($media->collection != $collections[$collection]['media_collection'] ?? 'public') {
                throw new MediaCollectionNotMatchException($media_id, $media->collection, $collections[$collection]);
            }
        } else {
            throw new CollectionNotInMediaAllowCollectionMethodException($collection);
        }

        $this->files()->attach($media_id, [
            'collection' => $collection
        ]);

        return true;
    }

    /**
     * Forget media
     *
     * @param int $media_id
     *
     * @return bool
     * @throws Throwable
     */
    public function forgetMedia(int $media_id): bool
    {
        foreach ($this->files as $file) {
            if ($file->id == $media_id) {
                $this->files()->detach($media_id);

                return true;
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
}
