<?php

namespace JobMetric\Media\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use JobMetric\Media\Enums\MediaImageResponsiveModeEnum;

class ImageResponsiveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'uuid' => 'required|uuid',
            'w' => 'sometimes|integer|required_with:h',
            'h' => 'sometimes|integer|required_with:w',
            'm' => 'sometimes|string|in:' . implode(',', MediaImageResponsiveModeEnum::values()),
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'm' => $this->m ?? MediaImageResponsiveModeEnum::SCALE()
        ]);
    }
}
