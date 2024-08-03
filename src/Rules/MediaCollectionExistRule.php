<?php

namespace JobMetric\Media\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class MediaCollectionExistRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!array_key_exists($value, config('media.collections'))) {
            $fail(__('media::base.validation.rules.media_collection_exist'));
        }
    }
}
