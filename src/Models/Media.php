<?php

namespace JobMetric\Media\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\PackageCore\Models\HasUuid;

/**
 * JobMetric\Media\Models\Media
 *
 * @property mixed id
 * @property mixed name
 * @property mixed parent_id
 * @property mixed type
 * @property mixed mime_type
 * @property mixed size
 * @property mixed content_id
 * @property mixed info
 * @property mixed disk
 * @property mixed collection
 * @property mixed uuid
 * @property mixed extension
 * @property mixed filename
 * @property mixed deleted_at
 * @property mixed created_at
 * @property mixed updated_at
 *
 * @property Media parent
 * @property Media[] children
 * @property MediaRelation[] mediaRelations
 *
 * @method static Builder ofCollection(string $collection)
 * @method static find(int $media_id)
 */
class Media extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'name',
        'parent_id',
        'type',
        'mime_type',
        'size',
        'content_id',
        'info',
        'disk',
        'collection',
        'uuid',
        'extension'
    ];

    protected $casts = [
        'name' => 'string',
        'parent_id' => 'integer',
        'type' => 'string',
        'mime_type' => 'string',
        'size' => 'integer',
        'content_id' => 'string',
        'info' => 'array',
        'disk' => 'string',
        'collection' => 'string',
        'uuid' => 'string',
        'extension' => 'string'
    ];

    public function getTable()
    {
        return config('media.tables.media', parent::getTable());
    }

    public function children(): HasMany
    {
        return $this->hasMany(Media::class, 'parent_id', 'id')->with('children');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'parent_id');
    }

    public function mediaRelations(): HasMany
    {
        return $this->hasMany(MediaRelation::class, 'media_id', 'id');
    }

    public function paths(): HasMany
    {
        return $this->hasMany(MediaPath::class, 'media_id', 'id')->orderBy('level');
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
     * Get the filename attribute.
     *
     * @return string|null
     */
    public function getFilenameAttribute(): ?string
    {
        if($this->type === MediaTypeEnum::FOLDER()) {
            return null;
        }

        return $this->uuid . '.' . $this->extension;
    }
}
