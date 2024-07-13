<?php

namespace JobMetric\Media\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * JobMetric\Media\Models\MediaPath
 *
 * @property mixed id
 * @property mixed media_id
 * @property mixed path_id
 * @property mixed level
 *
 * @property Media media
 * @property Media path
 */
class MediaPath extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'media_id',
        'path_id',
        'level'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'layout_id' => 'integer',
        'application' => 'string',
        'page' => 'string'
    ];

    public function getTable()
    {
        return config('media.tables.media_path', parent::getTable());
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function path(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'path_id');
    }
}
