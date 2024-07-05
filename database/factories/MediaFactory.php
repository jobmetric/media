<?php

namespace factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Models\Media;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'parent_id' => null,
            'type' => $this->faker->shuffleArray([MediaTypeEnum::values()]),
            'mime_type' => $this->faker->mimeType(),
            'size' => $this->faker->randomNumber(5),
            'content_id' => null,
            'additional' => null,
            'disk' => $this->faker->shuffleArray(['public', 's3']),
            'collection' => $this->faker->shuffleArray(['public', 'avatar']),
            'filename' => $this->faker->uuid() . '.' . $this->faker->fileExtension(),
        ];
    }

    /**
     * set name
     *
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => $name
        ]);
    }

    /**
     * set parent_id
     *
     * @param int $parent_id
     *
     * @return static
     */
    public function setParentId(int $parent_id): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => $parent_id
        ]);
    }

    /**
     * set type
     *
     * @param string $type
     *
     * @return static
     */
    public function setType(string $type): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => $type
        ]);
    }

    /**
     * set mime_type
     *
     * @param string $mime_type
     *
     * @return static
     */
    public function setMimeType(string $mime_type): static
    {
        return $this->state(fn(array $attributes) => [
            'mime_type' => $mime_type
        ]);
    }

    /**
     * set size
     *
     * @param int $size
     *
     * @return static
     */
    public function setSize(int $size): static
    {
        return $this->state(fn(array $attributes) => [
            'size' => $size
        ]);
    }

    /**
     * set content_id
     *
     * @param string $content_id
     *
     * @return static
     */
    public function setContentId(string $content_id): static
    {
        return $this->state(fn(array $attributes) => [
            'content_id' => $content_id
        ]);
    }

    /**
     * set additional
     *
     * @param array $additional
     *
     * @return static
     */
    public function setAdditional(array $additional): static
    {
        return $this->state(fn(array $attributes) => [
            'additional' => $additional
        ]);
    }

    /**
     * set disk
     *
     * @param string $disk
     *
     * @return static
     */
    public function setDisk(string $disk): static
    {
        return $this->state(fn(array $attributes) => [
            'disk' => $disk
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

    /**
     * set filename
     *
     * @param string $filename
     *
     * @return static
     */
    public function setFilename(string $filename): static
    {
        return $this->state(fn(array $attributes) => [
            'filename' => $filename
        ]);
    }
}
