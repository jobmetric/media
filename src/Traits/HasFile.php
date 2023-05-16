<?php

namespace JobMetric\Media\Traits;

use JobMetric\Media\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @method morphToMany(string $class, string $string, string $string1)
 */
trait HasFile
{
    public function files(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'relatable', 'media_relations');
    }
}
