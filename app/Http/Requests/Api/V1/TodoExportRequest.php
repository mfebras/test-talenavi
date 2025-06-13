<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class TodoExportRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'    => 'nullable|string',
            'assignee' => 'nullable|string',
            'start'    => 'nullable|required_with:end|date|date_format:Y-m-d',
            'end'      => 'nullable|required_with:start|date|date_format:Y-m-d',
            'min'      => 'nullable|required_with:max|numeric',
            'max'      => 'nullable|required_with:min|numeric',
            'status'   => 'nullable|string',
            'priority' => 'nullable|string',
        ];
    }
}
