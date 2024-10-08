<?php

namespace JobMetric\Media\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteRequest extends FormRequest
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
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:media,id',
            'parent_id' => 'sometimes|nullable|integer|exists:media,id',
            'mode' => 'sometimes|string|in:normal,trash',
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
            'parent_id' => $this->parent_id ?? null,
            'mode' => $this->mode ?? 'normal',
        ]);
    }
}
