<?php

namespace JobMetric\Media\ServiceType;

use Illuminate\Support\Traits\Macroable;
use JobMetric\CustomField\Exceptions\OptionEmptyLabelException;
use Throwable;

class MediaBuilder
{
    use Macroable;

    /**
     * The media instances
     *
     * @var array $media
     */
    protected array $media;

    /**
     * The collection variable.
     *
     * @var string|null $collection
     */
    protected ?string $collection = null;

    /**
     * The media collection variable.
     *
     * @var string $mediaCollection
     */
    protected string $mediaCollection = 'public';

    /**
     * The multiple status.
     *
     * @var bool $multiple
     */
    protected bool $multiple = false;

    /**
     * The mime types variable.
     *
     * @var array $mimeTypes
     */
    protected array $mimeTypes = ['image'];

    /**
     * The size variable.
     *
     * @var array $size
     */
    protected array $size = [];

    /**
     * Set collection.
     *
     * @param string $collection
     *
     * @return static
     */
    public function collection(string $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Set media collection.
     *
     * @param string $mediaCollection
     *
     * @return static
     */
    public function mediaCollection(string $mediaCollection = 'public'): static
    {
        $this->mediaCollection = $mediaCollection;

        return $this;
    }

    /**
     * Set multiple.
     *
     * @return static
     */
    public function multiple(): static
    {
        $this->multiple = true;

        return $this;
    }

    /**
     * Set mime types.
     *
     * @param array $mimeTypes
     *
     * @return static
     */
    public function mimeTypes(array $mimeTypes): static
    {
        $this->mimeTypes = $mimeTypes;

        return $this;
    }

    /**
     * Set size.
     *
     * @param string $name
     * @param int $width
     * @param int $height
     *
     * @return static
     */
    public function size(string $name, int $width, int $height): static
    {
        $this->size[$name] = [
            'w' => $width,
            'h' => $height,
        ];

        return $this;
    }

    /**
     * Build the media.
     *
     * @return Media
     * @throws Throwable
     */
    public function build(): Media
    {
        if (is_null($this->collection)) {
            throw new OptionEmptyLabelException;
        }

        $media = new Media($this->collection, $this->mediaCollection, $this->multiple, $this->mimeTypes, $this->size);

        $this->media[] = $media;

        return $media;
    }

    /**
     * Execute the callback to build the media.
     *
     * @return array
     */
    public function get(): array
    {
        return $this->media;
    }
}
