<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreStep3Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from' => ['nullable', 'array'],
            'to' => ['nullable', 'array'],
            'name' => ['nullable', 'array'],
            'phone' => ['nullable', 'array'],
            'employee_count' => ['nullable', 'array'],
            'type' => ['nullable', 'array'],
            'position' => ['nullable', 'array'],
            'beginning_position' => ['nullable', 'array'],
            'end_position' => ['nullable', 'array'],
            'supervisor' => ['nullable', 'array'],
            'reason_for_leaving' => ['nullable', 'array'],

            'from.*' => ['nullable', 'date'],
            'to.*' => ['nullable', 'date'],
            'name.*' => ['nullable', 'string'],
            'phone.*' => ['nullable', 'string'],
            'employee_count.*' => ['nullable', 'string'],
            'type.*' => ['nullable', 'string'],
            'position.*' => ['nullable', 'string'],
            'beginning_position.*' => ['nullable', 'string'],
            'end_position.*' => ['nullable', 'string'],
            'supervisor.*' => ['nullable', 'string'],
            'reason_for_leaving.*' => ['nullable', 'string'],
        ];
    }
}
