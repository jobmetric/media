<?php

namespace JobMetric\Media\Support;

use Closure;
use Illuminate\Support\Collection;
use Throwable;

/**
 * Trait HasMediaType
 *
 * @package JobMetric\Media
 */
trait HasMediaType
{
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

            $mediaItems = [$builder->build()];
        } else {
            $mediaItems = [];
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

                $mediaItems[] = $builder->build();
            }
        }

        $this->appendTypeParam('media', $mediaItems);

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
