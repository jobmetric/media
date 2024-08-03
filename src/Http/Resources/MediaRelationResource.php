<?php

namespace JobMetric\Media\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed media_id
 * @property mixed mediaable_type
 * @property mixed mediaable_id
 * @property mixed collection
 * @property mixed created_at
 *
 * @property mixed media
 * @property mixed mediaable_resource
 */
class MediaRelationResource extends JsonResource
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
            'mediaable_type' => $this->mediaable_type,
            'mediaable_id' => $this->mediaable_id,
            'collection' => $this->collection,
            'created_at' => $this->created_at,

            'media' => $this->media,
            'mediaable' => $this?->mediaable_resource
        ];
    }
}
