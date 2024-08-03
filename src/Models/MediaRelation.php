<?php

namespace JobMetric\Media\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use JobMetric\Media\Events\MediaableResourceEvent;

/**
 * JobMetric\Media\Models\MediaRelation
 *
 * @property mixed media_id
 * @property mixed mediaable_type
 * @property mixed mediaable_id
 * @property mixed collection
 * @property mixed created_at
 *
 * @property Media media
 * @property mixed mediaable
 * @property mixed mediaable_resource
 *
 * @method static Builder ofCollection(string $collection)
 */
class MediaRelation extends Pivot
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'media_id',
        'mediaable_type',
        'mediaable_id',
        'collection'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'media_id' => 'integer',
        'mediaable_type' => 'string',
        'mediaable_id' => 'integer',
        'collection' => 'string'
    ];

    public function getTable()
    {
        return config('media.tables.media_relation', parent::getTable());
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function mediaable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include categories of a given collection.
     *
     * @param Builder $query
     * @param string $collection
     *
     * @return Builder
     */
    public function scopeOfCollection(Builder $query, string $collection): Builder
    {
        return $query->where('collection', $collection);
    }

    /**
     * Get the mediaable resource attribute.
     *
     * @return mixed|null
     */
    public function getMediaableResourceAttribute(): mixed
    {
        $event = new MediaableResourceEvent($this->mediaable);
        event($event);

        return $event->resource;
    }
}
