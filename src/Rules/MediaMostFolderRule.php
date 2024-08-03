<?php

namespace JobMetric\Media\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Models\Media;

class MediaMostFolderRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $media = Media::find($value);

        if ($media->type !== MediaTypeEnum::FOLDER()) {
            $fail(__('media::base.validation.rules.media_most_folder'));
        }
    }
}
