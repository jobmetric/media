<?php

namespace factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Media\Models\MediaPath;

/**
 * @extends Factory<MediaPath>
 */
class MediaPathFactory extends Factory
{
    protected $model = MediaPath::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'media_id' => null,
            'path_id' => null,
            'level' => null
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
     * set path_id
     *
     * @param int $path_id
     *
     * @return static
     */
    public function setPathId(int $path_id): static
    {
        return $this->state(fn(array $attributes) => [
            'path_id' => $path_id
        ]);
    }

    /**
     * set level
     *
     * @param int $level
     *
     * @return static
     */
    public function setLevel(int $level): static
    {
        return $this->state(fn(array $attributes) => [
            'level' => $level
        ]);
    }
}
