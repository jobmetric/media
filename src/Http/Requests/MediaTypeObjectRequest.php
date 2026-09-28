<?php

namespace JobMetric\Media\Http\Requests;

use Illuminate\Support\Collection;
use JobMetric\Media\Rules\MediaMostFileRule;
use JobMetric\Media\Support\Media;

trait MediaTypeObjectRequest
{
    public function renderMediaFiled(
        array      &$rules,
        bool       $hasBaseMedia = true,
        Collection $media = null,
    ): void
    {
        $rules['media'] = 'array|sometimes';

        if ($hasBaseMedia) {
            $rules['media.base'] = [
                'integer',
                'nullable',
                'sometimes',
                new MediaMostFileRule
            ];
        }

        foreach ($media ?? [] as $item) {
            /**
             * @var Media $item
             */
            $collection = $item->getCollection();
            $multiple = $item->getMultiple();
            $mimeTypes = $item->getMimeTypes();

            if ($multiple) {
                $rules["media.$collection"] = 'array|nullable|sometimes';
                $rules["media.$collection.*"] = [
                    'integer',
                    'nullable',
                    'sometimes',
                    new MediaMostFileRule($mimeTypes)
                ];
            } else {
                $rules["media.$collection"] = [
                    'integer',
                    'nullable',
                    'sometimes',
                    new MediaMostFileRule($mimeTypes)
                ];
            }
        }
    }
}
