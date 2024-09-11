<?php

namespace JobMetric\Media\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Models\Media;

class MediaMostFileRule implements ValidationRule
{
    private array $groupMimeTypes;

    public function __construct(array $groupMimeTypes = ['image', 'svg'])
    {
        $this->groupMimeTypes = $groupMimeTypes;
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $media = Media::find($value);

        if ($media->type !== MediaTypeEnum::FILE()) {
            $fail(__('media::base.validation.rules.media_most_file'));
        }

        $configMimeTypes = config('media.mime_types');

        $anyType = false;
        $mimeTypes = [];

        foreach ($this->groupMimeTypes as $item) {
            if ($item === 'any') {
                $anyType = true;
                break;
            }

            $mimeTypes = array_merge($mimeTypes, $configMimeTypes[$item]);
        }

        if ($anyType) {
            return;
        }

        if (!in_array($media->mime_type, $mimeTypes)) {
            $fail(__('media::base.validation.rules.media_most_file_mime_type', [
                'mimeTypes' => implode(', ', $this->groupMimeTypes)
            ]));
        }
    }
}
