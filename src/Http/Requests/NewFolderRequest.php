<?php

namespace JobMetric\Media\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use JobMetric\Media\Rules\MediaExistRule;
use JobMetric\Media\Rules\MediaMostFolderRule;

class NewFolderRequest extends FormRequest
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
            'name' => 'required|string',
            'parent_id' => [
                'sometimes',
                'nullable',
                'integer',
                new MediaExistRule,
                new MediaMostFolderRule
            ]
        ];
    }
}
