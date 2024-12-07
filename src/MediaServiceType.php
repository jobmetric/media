<?php

namespace JobMetric\Media;

use Closure;
use Illuminate\Support\Collection;
use JobMetric\Media\ServiceType\MediaBuilder;
use Throwable;

/**
 * Trait MediaServiceType
 *
 * @package JobMetric\Media
 */
trait MediaServiceType
{
    /**
     * The media.
     *
     * @var array $media
     */
    protected array $media = [];

    /**
     * Set base media.
     *
     * @return static
     */
    public function baseMedia(): static
    {
        $this->setTypeParam('hasBaseMedia', true);

        return $this;
    }

    /**
     * Has base media.
     *
     * @return bool
     */
    public function hasBaseMedia(): bool
    {
        return $this->getTypeParam('hasBaseMedia', false);
    }

    /**
     * Set Media.
     *
     * @param Closure|array $callable
     *
     * @return static
     * @throws Throwable
     */
    public function media(Closure|array $callable): static
    {
        if ($callable instanceof Closure) {
            $callable($builder = new MediaBuilder);

            $this->media[] = $builder->build();
        } else {
            foreach ($callable as $media) {
                $builder = new MediaBuilder;

                $builder->collection($media['collection'] ?? null);
                $builder->mediaCollection($media['mediaCollection'] ?? 'public');

                if (isset($media['multiple']) && $media['multiple'] === true) {
                    $builder->multiple();
                }

                $builder->mimeTypes($media['mimeTypes'] ?? ['image']);

                foreach ($media['size'] ?? [] as $sizeName => $sizeValue) {
                    $builder->size($sizeName, $sizeValue['w'], $sizeValue['h']);
                }

                $this->media[] = $builder->build();
            }
        }

        $this->setTypeParam('media', $this->media);

        return $this;
    }

    /**
     * Get media.
     *
     * @return Collection
     */
    public function getMedia(): Collection
    {
        return collect($this->getTypeParam('media', []));
    }
}
