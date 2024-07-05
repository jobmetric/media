<?php

namespace factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Media\Models\MediaRelation;

/**
 * @extends Factory<MediaRelation>
 */
class MediaRelationFactory extends Factory
{
    protected $model = MediaRelation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'media_id' => null,
            'mediaable_type' => null,
            'mediaable_id' => null,
            'collection' => null
        ];
    }

    /**
     * set media_id
     *
     * @param int $media_id
     *
     * @return static
     */
    public function setMediaId(int $media_id): static
    {
        return $this->state(fn(array $attributes) => [
            'media_id' => $media_id
        ]);
    }

    /**
     * set mediaable
     *
     * @param string $mediaable_type
     * @param int $mediaable_id
     *
     * @return static
     */
    public function setMediaable(string $mediaable_type, int $mediaable_id): static
    {
        return $this->state(fn(array $attributes) => [
            'mediaable_type' => $mediaable_type,
            'mediaable_id' => $mediaable_id
        ]);
    }

    /**
     * set collection
     *
     * @param string $collection
     *
     * @return static
     */
    public function setCollection(string $collection): static
    {
        return $this->state(fn(array $attributes) => [
            'collection' => $collection
        ]);
    }
}
