<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreStep5Request extends FormRequest
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

            'organization_name' => ['nullable', 'array'],
            'organization_place' => ['nullable', 'array'],
            'organization_position' => ['nullable', 'array'],
            'organization_from' => ['nullable', 'array'],
            'organization_to' => ['nullable', 'array'],

            'organization_name.*' => ['nullable', 'string'],
            'organization_place.*' => ['nullable', 'string'],
            'organization_position.*' => ['nullable', 'string'],
            'organization_from.*' => ['nullable', 'date'],
            'organization_to.*' => ['nullable', 'date'],

            //
            'reference_name' => ['nullable', 'array'],
            'reference_address' => ['nullable', 'array'],
            'reference_phone' => ['nullable', 'array'],
            'reference_company' => ['nullable', 'array'],
            'reference_position' => ['nullable', 'array'],
            'reference_relation' => ['nullable', 'array'],

            'reference_name.*' => ['nullable', 'string'],
            'reference_address.*' => ['nullable', 'string'],
            'reference_phone.*' => ['nullable', 'string'],
            'reference_company.*' => ['nullable', 'string'],
            'reference_position.*' => ['nullable', 'string'],
            'reference_relation.*' => ['nullable', 'string'],

            // 
            'insider_name' => ['nullable', 'array'],
            'insider_position' => ['nullable', 'array'],
            'insider_relation' => ['nullable', 'array'],

            'insider_name.*' => ['nullable', 'string'],
            'insider_position.*' => ['nullable', 'string'],
            'insider_relation.*' => ['nullable', 'string'],

            // 
            'psikotest_place' => ['nullable', 'array'],
            'psikotest_date' => ['nullable', 'array'],
            'psikotest_cause' => ['nullable', 'array'],

            'psikotest_place.*' => ['nullable', 'string'],
            'psikotest_date.*' => ['nullable', 'string'],
            'psikotest_cause.*' => ['nullable', 'string'],

            // 
            'emergency_name' => ['nullable', 'array'],
            'emergency_relation' => ['nullable', 'array'],
            'emergency_phone' => ['nullable', 'array'],
            'emergency_address' => ['nullable', 'array'],

            'emergency_name.*' => ['nullable', 'string'],
            'emergency_relation.*' => ['nullable', 'string'],
            'emergency_phone.*' => ['nullable', 'string'],
            'emergency_address.*' => ['nullable', 'string'],
        ];
    }
}
