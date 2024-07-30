<?php

namespace JobMetric\Media\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Media\Enums\MediaTypeEnum;

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
 * @property mixed uuid
 * @property mixed extension
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
        $params = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'type' => $this->type,
        ];

        if($this->type == MediaTypeEnum::FILE()) {
             $params = array_merge($params, [
                'mime_type' => $this->mime_type,
                'size' => $this->size,
                'content_id' => $this->content_id,
                'additional' => $this->additional,
                'disk' => $this->disk,
                'collection' => $this->collection,
                'extension' => $this->extension,
                'filename' => $this->filename,
             ]);
        }

        return array_merge($params, [
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
