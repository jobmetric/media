<?php

namespace JobMetric\Media\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed media_id
 * @property mixed path_id
 * @property mixed level
 *
 * @property mixed media
 * @property mixed path
 */
class MediaPathResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'media_id' => $this->media_id,
            'path_id' => $this->path_id,
            'level' => $this->level,

            'media' => MediaResource::make($this->media),
            'path' => MediaResource::make($this->path),
        ];
    }
}
