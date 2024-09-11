<?php

namespace JobMetric\Media\Http\Requests;

use JobMetric\Media\Rules\MediaMostFileRule;

trait MediaTypeObjectRequest
{
    public function renderMediaFiled(
        array &$rules,
        array $object_type,
    ): void
    {
        if (isset($object_type['has_base_media'])) {
            $rules['media'] = 'array|sometimes';
            $rules['media.base'] = [
                'integer',
                'nullable',
                'sometimes',
                new MediaMostFileRule
            ];
        }

        if (isset($object_type['media'])) {
            if (!array_key_exists('media', $rules)) {
                $rules['media'] = 'array|sometimes';
            }
            foreach ($object_type['media'] as $media_key => $media_value) {
                $multiple = $media_value['multiple'] ?? false;
                $mimeTypes = $media_value['mime_types'] ?? ['image', 'svg'];

                if ($multiple) {
                    $rules['media.' . $media_key] = 'array|nullable|sometimes';
                    $rules['media.' . $media_key . '.*'] = [
                        'integer',
                        'nullable',
                        'sometimes',
                        new MediaMostFileRule($mimeTypes)
                    ];
                } else {
                    $rules['media.' . $media_key] = [
                        'integer',
                        'nullable',
                        'sometimes',
                        new MediaMostFileRule($mimeTypes)
                    ];
                }
            }
        }
    }
}
