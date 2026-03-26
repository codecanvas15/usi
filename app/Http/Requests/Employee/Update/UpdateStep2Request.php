<?php

namespace App\Http\Requests\Employee\Update;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStep2Request extends FormRequest
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
            // 
            'education_level' => ['nullable', 'array'],
            'education_name' => ['nullable', 'array'],
            'education_city' => ['nullable', 'array'],
            'education_faculty' => ['nullable', 'array'],
            'education_major' => ['nullable', 'array'],
            'education_from' => ['nullable', 'array'],
            'education_to' => ['nullable', 'array'],
            'education_gpa' => ['nullable', 'array'],
            'education_graduate' => ['nullable', 'array'],

            'education_level.*' => ['nullable', 'string'],
            'education_name.*' => ['nullable', 'string'],
            'education_city.*' => ['nullable', 'string'],
            'education_faculty.*' => ['nullable', 'string'],
            'education_major.*' => ['nullable', 'string'],
            'education_from.*' => ['nullable', 'date'],
            'education_to.*' => ['nullable', 'date'],
            'education_gpa.*' => ['nullable', 'string'],
            'education_graduate.*' => ['nullable', 'string'],

            // 
            'informal_education_name' => ['nullable', 'array'],
            'informal_education_initiator' => ['nullable', 'array'],
            'informal_education_lama' => ['nullable', 'array'],
            'informal_education_year' => ['nullable', 'array'],
            'informal_education_financed_by' => ['nullable', 'array'],

            'informal_education_name.*' => ['nullable', 'string'],
            'informal_education_initiator.*' => ['nullable', 'string'],
            'informal_education_lama.*' => ['nullable', 'string'],
            'informal_education_year.*' => ['nullable', 'string'],
            'informal_education_financed_by.*' => ['nullable', 'string'],

            // 
            'language_language' => ['nullable', 'array'],
            'language_speak' => ['nullable', 'array'],
            'language_listening' => ['nullable', 'array'],
            'language_write' => ['nullable', 'array'],
            'language_read' => ['nullable', 'array'],

            'language_language.*' => ['string', 'required'],
            'language_speak.*' => ['string', 'required'],
            'language_listening.*' => ['string', 'required'],
            'language_write.*' => ['string', 'required'],
            'language_read.*' => ['string', 'required'],

            // 
            'special_education_name' => ['nullable', 'array'],
            'special_education_year' => ['nullable', 'array'],

            'special_education_name.*' => ['nullable', 'string'],
            'special_education_year.*' => ['nullable', 'string'],

            'reason_for_choosing_the_major' => ['nullable', 'string'],
            'thesis_topic' => ['nullable', 'string'],
            'reason_for_not_passing' => ['nullable', 'string'],
        ];
    }
}
