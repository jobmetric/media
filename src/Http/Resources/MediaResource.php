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
 * @property mixed info
 * @property mixed disk
 * @property mixed collection
 * @property mixed uuid
 * @property mixed extension
 * @property mixed filename
 * @property mixed deleted_at
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed|true $loadedParent
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
                'disk' => $this->disk,
                'collection' => $this->collection,
                'extension' => $this->extension,
                'filename' => $this->filename,
             ]);
        }

        return array_merge($params, [
            'info' => $this->info,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'parent' => $this->whenLoaded('parent', function () {
                if (!$this->loadedParent) {
                    $this->loadedParent = true;
                    return new MediaResource($this->parent);
                }
                return null;
            }),
            'children' => $this->whenLoaded('children', function () {
                return MediaResource::collection($this->children);
            }),
            'media_relations' => $this->whenLoaded('mediaRelations', function () {
                return MediaRelationResource::collection($this->mediaRelations);
            }),
            'paths' => $this->whenLoaded('paths', function () {
                return MediaPathResource::collection($this->paths);
            }),
        ]);
    }
}
