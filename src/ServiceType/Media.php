<?php

namespace JobMetric\Media\ServiceType;

/**
 * Class Media
 *
 * @package JobMetric\Media\Media
 */
class Media
{
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
     * Media constructor.
     *
     * @param string|null $collection
     * @param string $mediaCollection
     * @param bool $multiple
     * @param array $mimeTypes
     * @param array $size
     */
    public function __construct(string|null $collection, string $mediaCollection = 'public', bool $multiple = false, array $mimeTypes = ['image'], array $size = [])
    {
        $this->collection = $collection;
        $this->mediaCollection = $mediaCollection;
        $this->multiple = $multiple;
        $this->mimeTypes = $mimeTypes;
        $this->size = $size;
    }
}
