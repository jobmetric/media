<?php

namespace JobMetric\Media\ServiceType;

use BadMethodCallException;
use Throwable;

/**
 * Class Media
 *
 * @package JobMetric\Media\Media
 *
 * @method getCollection()
 * @method getMediaCollection()
 * @method getMultiple()
 * @method getMimeTypes()
 * @method getSize()
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

    /**
     * get the property of the media
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     * @throws Throwable
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (str_starts_with($name, 'get')) {
            return $this->get(lcfirst(substr($name, 3)));
        } else {
            throw new BadMethodCallException("Method '$name' does not exist");
        }
    }

    /**
     * get the property of the media
     *
     * @param string $property
     *
     * @return mixed
     * @throws Throwable
     */
    public function get(string $property): mixed
    {
        if (!property_exists($this, $property)) {
            throw new BadMethodCallException("Property '$property' does not exist");
        }

        return $this->$property;
    }

    /**
     * Render the media as HTML
     *
     * @param array|string|int|null $value
     * @param string $name
     *
     * @return string
     * @throws Throwable
     */
    public function render(array|string|int|null $value = null, string $name = ''): string
    {
        return view('media::media-field', [
            'value' => $value,
            'name' => $name,
            'media' => $this,
        ])->render();
    }
}
