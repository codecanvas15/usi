<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreStep6Request extends FormRequest
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
            'type' => ['nullable', 'array'],
            'description' => ['nullable', 'array'],
            'type.*' => ['nullable', 'string'],
            'description.*' => ['nullable', 'string'],
        ];
    }
}
