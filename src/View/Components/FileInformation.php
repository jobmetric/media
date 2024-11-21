<?php

namespace JobMetric\Media\View\Components;

use JobMetric\Media\Models\Media;

trait FileInformation
{
    /**
     * Get File Information
     *
     * @param int|null $id
     *
     * @return array
     */
    public function getFileInformation(int|null $id = null): array
    {
        /**
         * @var Media $media
         */
        $media = Media::withTrashed()->where('id', $id)->first();

        if ($media) {
            $data['image_value'] = $id;
            $data['image_name'] = $media->name;
            $mime_group = getMimeGroup($media->mime_type);

            if ($mime_group === 'image') {
                $data['image_url'] = 'media/image/responsive?uuid=' . $media->uuid . '&w=400&h=400&m=cover';
            } elseif ($mime_group === 'svg') {
                $data['image_url'] = route('media.download', [
                    'media' => $media->id,
                ]);
            } else {
                $data['image_url'] = "data:image/svg+xml;base64," . base64_encode(getFileIcon($mime_group));
            }

            return $data;
        } else {
            return [
                'image_value' => null,
                'image_name' => '',
                'image_url' => '',
            ];
        }
    }
}
