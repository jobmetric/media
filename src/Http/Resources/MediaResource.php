<?php

namespace JobMetric\Media\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed parent_id
 * @property mixed type
 * @property mixed mime_type
 * @property mixed size
 * @property mixed content_id
 * @property mixed additional
 * @property mixed disk
 * @property mixed collection
 * @property mixed filename
 * @property mixed deleted_at
 * @property mixed created_at
 * @property mixed updated_at
 */
class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'type' => $this->type,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'content_id' => $this->content_id,
            'additional' => $this->additional,
            'disk' => $this->disk,
            'collection' => $this->collection,
            'filename' => $this->filename,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
