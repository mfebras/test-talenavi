<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\TodoPriority;
use App\Enums\TodoStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TodoRequest extends FormRequest
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
            'title'        => 'required|max:255',
            'assignee'     => 'nullable|max:255',
            'due_date'     => ['required', 'date', 'date_format:Y-m-d', Rule::date()->afterOrEqual(today())],
            'time_tracked' => 'nullable|integer',
            'status'       => ['nullable', Rule::enum(TodoStatus::class)],
            'priority'     => ['required', Rule::enum(TodoPriority::class)],
        ];
    }
}
