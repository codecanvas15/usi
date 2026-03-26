<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateRequest extends FormRequest
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
        // dd($this->all());
        return [
            'branch_id' => ['required', Rule::exists('branches', 'id')],
            'division_id' => ['required', Rule::exists('divisions', 'id')],
            'employment_status_id' => ['required', Rule::exists('employment_statuses', 'id')],
            'education_id' => ['required', Rule::exists('educations', 'id')],
            'degree_id' => ['required', Rule::exists('degrees', 'id')],
            'email' => ['required', 'string', 'max:255', 'email'],
            'name' => ['required', 'string', 'max:255'],
            // 'NIK' => ['required', 'string', 'max:255', Rule::unique('employees', 'NIK')],
            'no_ktp' => ['required', 'string', 'max:255', Rule::unique('employees', 'no_ktp')],
            'alamat' => ['required', 'string', 'max:255'],
            'alamat_domisili' => ['required', 'string', 'max:255'],
            'nomor_telepone' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'string'],
            'non_taxable_income_id' => ['required', Rule::exists('non_taxable_incomes', 'id')],
            'join_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'npwp' => ['nullable', 'string', 'max:255'],
            'start_contract' => ['nullable', 'date'],
            'end_contract' => ['nullable', 'date'],
            'employee_status' => ['nullable', 'string', 'max:255', 'in:active,non_active,calon_karyawan'],
            'leave' => ['required', 'string'],
            'position_id' => ['required', Rule::exists('positions', 'id')],
            'staff_type' => ['required', 'string', 'in:staff,crew,driver'], // staff, crew, driver
            'religion' => ['required', 'string'],
            'weight' => ['nullable', 'string'],
            'height' => ['nullable', 'string'],
            'blood_type' => ['nullable', 'string'],
            'hobby' => ['nullable', 'string'],
            'vehicle' => ['nullable', 'string'],
            'marriage_date' => ['nullable', 'date'],
            'parents_phone_number' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg', 'max:10340'],
            'occupied_house' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string'],
            'vehicle_ownership' => ['nullable', 'string'],
            'house_phone' => ['nullable', 'string', 'max:255'],
            'postal_code_current_residential_address' => ['nullable', 'integer'],
            'postal_code_parents_residence_address' => ['nullable', 'integer'],
            'postal_code' => ['nullable', 'integer'],

            //
            'employee_document_document_file' => ['nullable', 'array'],
            'employee_document_document_name' => ['nullable', 'array'],
            'employee_document_card_number' => ['nullable', 'array'],
            'employee_document_validity_period' => ['nullable', 'array'],

            'employee_document_document_file.*' => ['nullable', 'file', 'mimes:pdf,docx', 'max:10340'],
            'employee_document_document_name.*' => ['nullable', 'string'],
            'employee_document_card_number.*' => ['nullable', 'string'],
            'employee_document_validity_period.*' => ['nullable', 'date'],

            //
            'employee_family_tree_type' => ['nullable', 'array'],
            'employee_family_tree_relation' => ['nullable', 'array'],
            'employee_family_tree_name' => ['nullable', 'array'],
            'employee_family_tree_gender' => ['nullable', 'array'],
            'employee_family_tree_birth_place' => ['nullable', 'array'],
            'employee_family_tree_birth_date' => ['nullable', 'array'],
            'employee_family_tree_education' => ['nullable', 'array'],
            'employee_family_tree_last_position' => ['nullable', 'array'],
            'employee_family_tree_last_company' => ['nullable', 'array'],

            'employee_family_tree_type.*' => ['nullable', 'string',],
            'employee_family_tree_relation.*' => ['nullable', 'string'],
            'employee_family_tree_name.*' => ['nullable', 'string'],
            'employee_family_tree_gender.*' => ['nullable', 'string'],
            'employee_family_tree_birth_place.*' => ['nullable', 'string'],
            'employee_family_tree_birth_date.*' => ['nullable', 'date'],
            'employee_family_tree_education.*' => ['nullable', 'string'],
            'employee_family_tree_last_position.*' => ['nullable', 'string'],
            'employee_family_tree_last_company.*' => ['nullable', 'string'],

            //
            'employee_health_condition' => ['nullable', 'string'],
            'employee_health_description' => ['nullable', 'string'],
            'employee_health_description_2' => ['nullable', 'string'],

            'ktp_file' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10340'],
            'npwp_file' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10340'],
        ];
    }
}
