<?php

namespace JobMetric\Media\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed disk
 * @property mixed filename
 * @property mixed parent_id
 * @property mixed type
 * @property mixed mime_type
 * @property mixed size
 * @property mixed content_id
 * @property mixed additional
 * @property mixed collection
 * @property mixed deleted_at
 * @property mixed created_at
 * @property mixed updated_at
 */
class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'disk',
        'filename',
        'parent_id',
        'type',
        'mime_type',
        'size',
        'content_id',
        'additional',
        'collection',
    ];

    protected $casts = [
        'additional' => 'array'
    ];

    /**
     * relationship
     *
     * @return BelongsToMany
     */
    public function relatable(): BelongsToMany
    {
        return $this->belongsToMany('relatable', 'media_relations');
    }
}
