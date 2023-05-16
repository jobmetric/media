<?php

namespace JobMetric\Media\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed media_id
 * @property mixed path_id
 * @property mixed level
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
}
