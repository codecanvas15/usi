<?php

namespace App\Services;

use App\Http\Requests\Employee\StoreStep3Request;
use App\Http\Requests\Employee\StoreStep4Request;
use App\Http\Requests\Employee\StoreStep6Request;
use App\Http\Requests\Employee\StoreUpdateRequest;
use App\Http\Requests\Employee\StoreUpdateStep2Request;
use App\Http\Helpers\UploadFileHelpers;
use App\Http\Requests\Employee\StoreStep5Request;
use App\Http\Requests\Employee\Update\UpdateRequest;
use App\Http\Requests\Employee\Update\UpdateStep2Request;
use App\Http\Requests\Employee\Update\UpdateStep3Request;
use App\Http\Requests\Employee\Update\UpdateStep4Request;
use App\Http\Requests\Employee\Update\UpdateStep5Request;
use App\Http\Requests\Employee\Update\UpdateStep6Request;
use App\Http\Requests\Employee\Update\UpdateStep7Request;
use App\Models\EmployeeDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Str;

class EmployeeService
{
    use UploadFileHelpers;

    /**
     * datatables
     */
    public function datatables(Request $request)
    {
        $data = \App\Models\Employee::with(['branch', 'position', 'employment_status'])
            ->select('employees.*')
            ->when($request->position, function ($query, $position) {
                $query->where('position_id', $position);
            })
            ->when($request->employment_status, function ($query, $employment_status) {
                $query->where('employment_status_id', $employment_status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($branch) use ($request) {
                $branch->where('branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($branch) {
                $branch->where('branch_id', get_current_branch()->id);
            })
            ->when($request->division_id, function ($division) use ($request) {
                $division->where('division_id', $request->division_id);
            })
            ->when($request->education_id, function ($education) use ($request) {
                $education->where('education_id', $request->education_id);
            })
            ->when($request->degree_id, function ($degree) use ($request) {
                $degree->where('degree_id', $request->degree_id);
            })
            ->when($request->employee_status, function ($employee_status) use ($request) {
                $employee_status->where('employee_status', $request->employee_status);
            });

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('user', function ($row) {
                $data = '';
                foreach ($row->users as $user) {
                    $data .= $user ? '<a href="' . route("admin.user.show", $user->id) . '" class="text-primary text-decoration-underline hover_text-dark">' . $user->username . '</a>' : '';
                }
                return $data;
            })
            ->editColumn('name', function ($row) {
                if (strlen($row->name) > 25) {
                    return substr($row->name, 0, 25) . '...';
                } else {
                    return $row->name;
                }
            })
            ->editColumn('employee_status', function ($row) {
                switch ($row->employee_status) {
                    case 'active':
                        return '<span class="badge badge-pill badge-sm bg-success">' . Str::headline($row->employee_status) . '</span>';
                        break;
                    case 'non_active':
                        return '<span class="badge badge-pill badge-sm bg-danger">' . Str::headline($row->employee_status) . '</span>';
                        break;
                    default:
                        return '<span class="badge badge-pill badge-sm bg-warning">' . Str::headline($row->employee_status) . '</span>';
                        break;
                }
            })
            ->editColumn('NIK', fn ($row) => view('components.datatable.detail-link', [
                'field' => $row->NIK,
                'row' => $row,
                'main' => 'employee',
            ]))
            ->addColumn('export', fn ($row) => view('components.datatable.export-button', [
                'route' => route("employee.export.id", ['id' => encryptId($row->id)]),
                'onclick' => "show_print_out_modal(event)"
            ]))
            ->editColumn('join_date', function ($row) {
                return localDate($row->join_date);
            })
            ->editColumn('start_contract', function ($row) {
                return localDate($row->start_contract);
            })
            ->editColumn('end_contract', function ($row) {
                return localDate($row->end_contract);
            })
            ->editColumn('tanggal_lahir', function ($row) {
                return localDate($row->tanggal_lahir);
            })
            ->addColumn('action', function ($row) {
                return view('components.datatable.button-datatable', [
                    'row' => $row,
                    'main' => 'employee',
                    'btn_config' => [
                        'detail' => [
                            'display' => false,
                        ],
                        'edit' => [
                            'display' => true,
                        ],
                        'delete' => [
                            'display' => true,
                        ],
                    ],
                ]);
            })
            ->editColumn('branch.name', function ($row) {
                return $row->branch?->name ?? '-';
            })
            ->editColumn('position.nama', function ($row) {
                return $row->position?->nama ?? '-';
            })
            ->editColumn('employment_status.name', function ($row) {
                return $row->employment_status?->name ?? '';
            })
            ->rawColumns(['user', 'action', 'NIK', 'employee_status'])
            ->make(true);
    }

    /**
     * store
     */
    public function store(StoreUpdateRequest $request)
    {
        $employee = new \App\Models\Employee();
        $request_all = $request->safe()
            ->except([
                'file',
                'vehicle_brand',
                'vehicle_year',
                'current_residential_address',
                'postal_code_current_residential_address',
                'parents_residence_address',
                'postal_code_parents_residence_address',
                'employee_document_document_file',
                'employee_document_document_name',
                'employee_document_card_number',
                'employee_document_validity_period',

                'employee_family_tree_type',
                'employee_family_tree_relation',
                'employee_family_tree_name',
                'employee_family_tree_gender',
                'employee_family_tree_birth_place',
                'employee_family_tree_birth_date',
                'employee_family_tree_education',
                'employee_family_tree_last_position',
                'employee_family_tree_last_company',

                'employee_health_condition',
                'employee_health_description',
                'employee_health_description_2',
            ]);

        $code = $code = generate_employee_code($request->position_id, $request->join_date ?? $request->start_contract);
        $request_all['NIK'] = $code;
        $request_all['tanggal_lahir'] = $request_all['tanggal_lahir'] ? Carbon::parse($request_all['tanggal_lahir']) : null;
        $request_all['join_date'] = $request_all['join_date'] ? Carbon::parse($request_all['join_date']) : null;
        $request_all['end_date'] = $request_all['end_date'] ? Carbon::parse($request_all['end_date']) : null;
        $request_all['start_contract'] = $request_all['start_contract'] ? Carbon::parse($request_all['start_contract']) : null;
        $request_all['end_contract'] = $request_all['end_contract'] ? Carbon::parse($request_all['end_contract']) : null;
        $request_all['occupied_house'] = $request->occupied_house == 'lain' ? $request->occupied_house_alt : $request->occupied_house;
        $request_all['marriage_date'] = isset($request_all['marriage_date']) ? Carbon::parse($request_all['marriage_date']) : null;
        $request_all['current_residential_address'] = [
            'address' => $request->current_residential_address ?? '',
            'postal_code' => $request->postal_code_current_residential_address ?? '',
        ];
        $request_all['parents_residence_address'] = [
            'address' => $request->parents_residence_address ?? '',
            'postal_code' => $request->postal_code_parents_residence_address ?? '',
        ];
        $request_all['vehicle_ownership'] = $request->is_vehicle_owner ? null : $request->vehicle_ownership;

        $employee->fill($request_all);

        if ($request->vehicle_brand || $request->vehicle_year) {
            $employee->vehicle_details = [
                'brand' => $request->vehicle_brand ?? '',
                'year' => $request->vehicle_year ?? '',
            ];
        }

        if ($request->file('file')) {
            $employee->file = $this->upload_file($request->file('file'), 'employee');
        }

        $employee->save();

        // * employee documents
        $employeeDocuments = [];

        if ($request->file('ktp_file')) {
            $path = $this->upload_file($request->file('ktp_file'), 'employee-document');
            $employeeDocuments[] = [
                'document_name' => 'KTP',
                'card_number' => $request->no_ktp,
                'validity_period' => null,
                'document_file' => $path,
            ];
        }

        if ($request->file('npwp_file')) {
            $path = $this->upload_file($request->file('npwp_file'), 'employee-document');
            $employeeDocuments[] = [
                'document_name' => 'NPWP',
                'card_number' => $request->npwp,
                'validity_period' => null,
                'document_file' => $path,
            ];
        }

        if (is_array($request->employee_document_document_name)) {

            foreach ($request->employee_document_document_name as $key => $employee_document) {
                $path = $this->upload_file($request->employee_document_document_file[$key], 'employee-document');
                $employeeDocuments[] = [
                    'document_name' => $request->employee_document_document_name[$key],
                    'card_number' => $request->employee_document_card_number[$key],
                    'validity_period' => $request->employee_document_validity_period[$key],
                    'document_file' => $path,
                ];
            }
        }

        if (!empty($employeeDocuments)) {
            try {
                $employee->employeeDocument()->createMany($employeeDocuments);
            } catch (\Throwable $th) {
                foreach ($employeeDocuments as $key => $value) {
                    $this->delete_file($value['document_file']);
                }

                throw $th;
            }
        }

        // * family tree
        $employeeFamilyTrees = [];
        if (is_array($request->employee_family_tree_type)) {

            foreach ($request->employee_family_tree_type as $key => $employee_tree) {
                $employeeFamilyTrees[] = [
                    'type' => $request->employee_family_tree_type[$key],
                    'relation' => $request->employee_family_tree_relation[$key],
                    'name' => $request->employee_family_tree_name[$key],
                    'gender' => $request->employee_family_tree_gender[$key],
                    'birth_place' => $request->employee_family_tree_birth_place[$key],
                    'birth_date' => Carbon::parse($request->employee_family_tree_birth_date[$key]),
                    'education' => $request->employee_family_tree_education[$key],
                    'last_position' => $request->employee_family_tree_last_position[$key],
                    'last_company' => $request->employee_family_tree_last_company[$key],
                ];
            }

            try {
                $employee->employeeFamilyTrees()->createMany($employeeFamilyTrees);
            } catch (\Throwable $th) {
                foreach ($employeeDocuments as $key => $value) {
                    $this->delete_file($value['document_file']);
                }

                throw $th;
            }
        }

        // * health and condition
        if (!is_null($request->employee_health_condition) && !is_null($request->employee_health_description) && !is_null($request->employee_health_description_2)) {
            try {
                $employee->employeeHealthHistory()->create([
                    'employee_id' => $employee->id,
                    'condition' => $request->employee_health_condition,
                    'description' => $request->employee_health_description,
                    'description_2' => $request->employee_health_description_2,
                ]);
            } catch (\Throwable $th) {
                foreach ($employeeDocuments as $key => $value) {
                    $this->delete_file($value['document_file']);
                }

                throw $th;
            }
        }

        return $employee;
    }

    /**
     * store step 2
     */
    public function store2(StoreUpdateStep2Request $request, \App\models\Employee $employee)
    {
        // formal education
        $employeeEducations = [];
        if (is_array($request->education_level)) {
            foreach ($request->education_level as $key => $value) {
                $employeeEducations[] = [
                    'level' => $request->education_level[$key],
                    'name' => $request->education_name[$key],
                    'city' => $request->education_city[$key],
                    'faculty' => $request->education_faculty[$key],
                    'major' => $request->education_major[$key],
                    'from' => Carbon::parse($request->education_from[$key]),
                    'to' => Carbon::parse($request->education_to[$key]),
                    'gpa' => $request->education_gpa[$key],
                    'graduate' => $request->education_graduate[$key],
                ];
            }

            $employee->employeeFormalEducations()->createMany($employeeEducations);
        }

        // informal education
        $employeeInformalEducation = [];
        if (is_array($request->informal_education_name)) {
            foreach ($request->informal_education_name as $key => $value) {
                $employeeInformalEducation[] = [
                    'name' => $request->informal_education_name[$key],
                    'initiator' => $request->informal_education_initiator[$key],
                    'lama' => $request->informal_education_lama[$key],
                    'year' => $request->informal_education_year[$key],
                    'financed_by' => $request->informal_education_financed_by[$key],
                ];
            }

            $employee->employeeInformalEducations()->createMany($employeeInformalEducation);
        }

        // language
        $employeeLanguages = [];
        if (is_array($request->language_language)) {
            foreach ($request->language_language as $key => $value) {
                $employeeLanguages[] = [
                    'language' => $request->language_language[$key],
                    'speak' => $request->language_speak[$key],
                    'listening' => $request->language_listening[$key],
                    'write' => $request->language_write[$key],
                    'read' => $request->language_read[$key],
                ];
            }

            $employee->employeeLanguages()->createMany($employeeLanguages);
        }

        // special education
        $employeeSpecialEducations = [];
        if (is_array($request->special_education_name)) {
            foreach ($request->special_education_name as $key => $value) {
                $employeeSpecialEducations[] = [
                    'name' => $request->special_education_name[$key],
                    'year' => $request->special_education_year[$key],
                ];
            }

            $employee->employeeSpecialEducations()->createMany($employeeSpecialEducations);
        }

        // employee parent
        $employee->reason_for_choosing_the_major = $request->reason_for_choosing_the_major;
        $employee->thesis_topic = $request->thesis_topic;
        $employee->reason_for_not_passing = $request->reason_for_not_passing;
        $employee->save();

        return true;
    }

    /**
     * store step 3
     */
    public function store3(StoreStep3Request $request, \App\Models\Employee $employee)
    {
        // work experience
        $workExperience = [];

        if (is_array($request->from)) {
            foreach ($request->from as $key => $item) {
                $workExperience[] = [
                    'from' => Carbon::parse($request->from[$key]),
                    'to' => Carbon::parse($request->to[$key]),
                    'name' => $request->name[$key],
                    'phone' => $request->phone[$key],
                    'employee_count' => $request->employee_count[$key],
                    'type' => $request->type[$key],
                    'position' => $request->position[$key],
                    'beginning_position' => $request->beginning_position[$key],
                    'end_position' => $request->end_position[$key],
                    'supervisor' => $request->supervisor[$key],
                    'reason_for_leaving' => $request->reason_for_leaving[$key],
                ];
            }
        }

        $employee->employeeWorkExperiences()->createMany($workExperience);

        return true;
    }

    /**
     * store step 4
     */
    public function store4(StoreStep4Request $request, \App\Models\Employee $employee)
    {
        $interest[] = [];

        if (is_array($request->interest)) {
            foreach ($request->interest as $key => $value) {

                if (is_null($request->interest[$key])) {
                    continue;
                }

                $interest[] = [
                    'interest' => $request->interest[$key],
                    'rank' => $request->rank[$key],
                ];
            }

            $employee->employeeInterests()->createMany($interest);
        }

        return true;
    }

    /**
     * store step 5
     */
    public function store5(StoreStep5Request $request, \App\Models\Employee $employee)
    {
        // organisasi
        $employeeOrganizations = [];
        if (is_array($request->organization_name)) {
            foreach ($request->organization_name as $key => $value) {
                $employeeOrganizations[] = [
                    'name' => $request->organization_name[$key],
                    'place' => $request->organization_place[$key],
                    'position' => $request->organization_position[$key],
                    'from' => Carbon::parse($request->organization_from[$key]),
                    'to' => Carbon::parse($request->organization_to[$key]),
                ];
            }

            $employee->employeeOrganizations()->createMany($employeeOrganizations);
        }

        // references
        $employeeReferences = [];
        if (is_array($request->reference_name)) {
            foreach ($request->reference_name as $key => $value) {
                $employeeReferences[] = [
                    'name' => $request->reference_name[$key],
                    'address' => $request->reference_address[$key],
                    'phone' => $request->reference_phone[$key],
                    'company' => $request->reference_company[$key],
                    'position' => $request->reference_position[$key],
                    'relation' => $request->reference_relation[$key],
                ];
            }

            $employee->employeeReferences()->createMany($employeeReferences);
        }

        // insiders
        $employeeInsiders = [];
        if (is_array($request->insider_name)) {
            foreach ($request->insider_name as $key => $value) {
                $employeeInsiders[] = [
                    'name' => $request->insider_name[$key],
                    'position' => $request->insider_position[$key],
                    'relation' => $request->insider_relation[$key],
                ];
            }

            $employee->employeeInsiders()->createMany($employeeInsiders);
        }

        // psikotest
        $employeePsikotests = [];
        if (is_array($request->psikotest_place)) {
            foreach ($request->psikotest_place as $key => $value) {
                $employeePsikotests[] = [
                    'place' => $request->psikotest_place[$key],
                    'date' => Carbon::parse($request->psikotest_date[$key]),
                    'cause' => $request->psikotest_cause[$key],
                ];
            }

            $employee->employeePsikotests()->createMany($employeePsikotests);
        }

        // emergency contact
        $employeeEmergencyContacts = [];
        if (is_array($request->emergency_name)) {
            foreach ($request->emergency_name as $key => $value) {
                $employeeEmergencyContacts[] = [
                    'nama' => $request->emergency_name[$key],
                    'hubungan' => $request->emergency_relation[$key],
                    'nomor_telepon' => $request->emergency_phone[$key],
                    'alamat' => $request->emergency_address[$key],
                ];
            }

            $employee->employee_emergency_contacts()->createMany($employeeEmergencyContacts);
        }

        return true;
    }

    /**
     * store step 6
     */
    public function store6(StoreStep6Request $request, \App\Models\Employee $employee)
    {
        // strength and weakness
        $strengthAndWeakness = [];
        if (is_array($request->type)) {
            foreach ($request->type as $key => $value) {
                $strengthAndWeakness[] = [
                    'type' => $request->type[$key],
                    'description' => $request->description[$key],
                ];
            }

            $employee->employeeStrengthAndWeaknesses()->createMany($strengthAndWeakness);
        }

        return true;
    }

    /**
     * store step 7
     */
    public function store7(Request $request, \App\Models\Employee $employee)
    {
        $employeeBanks = [];
        if (is_array($request->bank_name)) {
            foreach ($request->bank_name as $key => $value) {
                $employeeBanks[] = [
                    'bank_name' => $request->bank_name[$key],
                    'behalf_of' => $request->bank_behalf_of[$key],
                    'account_number' => $request->bank_account_number[$key],
                ];
            }

            $employee->employee_banks()->createMany($employeeBanks);
        }

        return true;
    }

    /**
     * show employee
     */
    public function show(string $employee_id)
    {
        return \App\Models\Employee::with([
            'users',
            'employee_banks',
            'position',
            'education',
            'degree',
            'branch',
            'division',
            'EmployeeHealthHistory',
            'employeeDocument',
            'employment_status',
            'roleHistory',
            'branchHistory',
            'employee_emergency_contacts',
            'employeeFamilyTrees',
            'employeeFormalEducations',
            'employeeInformalEducations',
            'employeeLanguages',
            'employeeSpecialEducations',
            'employeeWorkExperiences',
            'employeeInterests',
            'employeeReferences',
            'employeeInsiders',
            'employeePsikotests',
            'employeeOrganizations',
            'contractExtension',
            'non_taxable_income',
        ])
            ->findOrFail($employee_id);
    }

    /**
     * update
     */
    public function update(UpdateRequest $request, \App\Models\Employee $employee)
    {
        $oldEmployeeFile = $employee->file;
        $oldFiles = [];
        $newFiles = [];

        $request_all = $request->safe()
            ->except([
                'file',
                'vehicle_brand',
                'vehicle_year',
                'current_residential_address',
                'postal_code_current_residential_address',
                'parents_residence_address',
                'postal_code_parents_residence_address',
                'employee_document_id',
                'employee_document_document_file',
                'employee_document_document_file',
                'employee_document_document_name',
                'employee_document_card_number',
                'employee_document_validity_period',

                'employee_family_tree_type',
                'employee_family_tree_relation',
                'employee_family_tree_name',
                'employee_family_tree_gender',
                'employee_family_tree_birth_place',
                'employee_family_tree_birth_date',
                'employee_family_tree_education',
                'employee_family_tree_last_position',
                'employee_family_tree_last_company',

                'employee_health_condition',
                'employee_health_description',
                'employee_health_description_2',
            ]);

        $request_all['tanggal_lahir'] = $request_all['tanggal_lahir'] ? Carbon::parse($request_all['tanggal_lahir']) : null;
        $request_all['join_date'] = $request_all['join_date'] ? Carbon::parse($request_all['join_date']) : null;
        $request_all['end_date'] = $request_all['end_date'] ? Carbon::parse($request_all['end_date']) : null;
        $request_all['start_contract'] = $request_all['start_contract'] ? Carbon::parse($request_all['start_contract']) : null;
        $request_all['end_contract'] = $request_all['end_contract'] ? Carbon::parse($request_all['end_contract']) : null;
        $request_all['occupied_house'] = $request->occupied_house == 'lain' ? $request->occupied_house_alt : $request->occupied_house;
        $request_all['marriage_date'] = isset($request_all['marriage_date']) ? Carbon::parse($request_all['marriage_date']) : null;
        $request_all['current_residential_address'] = [
            'address' => $request->current_residential_address ?? '',
            'postal_code' => $request->postal_code_current_residential_address ?? '',
        ];
        $request_all['parents_residence_address'] = [
            'address' => $request->parents_residence_address ?? '',
            'postal_code' => $request->postal_code_parents_residence_address ?? '',
        ];
        $request_all['vehicle_ownership'] = $request->is_vehicle_owner ? null : $request->vehicle_ownership;

        $employee->fill($request_all);

        if ($request->vehicle_brand || $request->vehicle_year) {
            $employee->vehicle_details = [
                'brand' => $request->vehicle_brand ?? '',
                'year' => $request->vehicle_year ?? '',
            ];
        }

        if ($request->file('file')) {

            $employee->file = $this->upload_file($request->file('file'), 'employee');

            $oldFiles[] = $oldEmployeeFile;
            $newFiles[] = $employee->file;
        }

        $employee->save();

        // * delete employee document
        $employeeDocuments = \App\Models\EmployeeDocument::where('employee_id', $employee->id)
            ->whereNotIn('id', $request->employee_document_id ?? [])
            ->get();

        $employeeDocuments->each(function ($item) use (&$oldFiles) {
            $oldFiles[] = $item->document_file;
        });

        $employeeDocuments->each(function ($item) {
            $item->delete();
        });

        $newEmployeeDocuments = [];

        if ($request->file('ktp_file')) {
            $path = $this->upload_file($request->file('ktp_file'), 'employee-document');
            $stored_ktp_document = EmployeeDocument::where('employee_id', $employee->id)
                ->where('document_name', 'KTP')
                ->first();

            if ($stored_ktp_document) {
                Storage::delete($stored_ktp_document->document_file);
                $stored_ktp_document->delete();
            }

            $newEmployeeDocuments[] = [
                'document_name' => 'KTP',
                'card_number' => $request->no_ktp,
                'validity_period' => null,
                'document_file' => $path,
            ];
        }

        if ($request->file('npwp_file')) {
            $path = $this->upload_file($request->file('npwp_file'), 'employee-document');

            $stored_ktp_document = EmployeeDocument::where('employee_id', $employee->id)
                ->where('document_name', 'KTP')
                ->first();

            if ($stored_ktp_document) {
                Storage::delete($stored_ktp_document->document_file);
                $stored_ktp_document->delete();
            }

            $newEmployeeDocuments[] = [
                'document_name' => 'NPWP',
                'card_number' => $request->npwp,
                'validity_period' => null,
                'document_file' => $path,
            ];
        }

        if (is_array($request->employee_document_document_name)) {

            foreach ($request->employee_document_document_name as $key => $employee_document) {

                if (isset($request->employee_document_id[$key])) {
                    $employeeDocument = \App\Models\EmployeeDocument::findOrFail($request->employee_document_id[$key]);
                    $employeeDocument->document_name = $request->employee_document_document_name[$key];
                    $employeeDocument->card_number = $request->employee_document_card_number[$key];
                    $employeeDocument->validity_period = Carbon::parse($request->employee_document_validity_period[$key]);

                    if ($request->hasFile('employee_document_document_file.' . $key)) {
                        $oldFiles[] = $employeeDocument->document_file;
                        $employeeDocument->document_file = $this->upload_file($request->employee_document_document_file[$key], 'employee-document');
                        $newFiles[] = $employeeDocument->document_file;
                    }

                    $employeeDocument->save();
                    continue;
                } else {
                    $path = $this->upload_file($request->employee_document_document_file[$key], 'employee-document');
                    $newEmployeeDocuments[] = [
                        'document_name' => $request->employee_document_document_name[$key],
                        'card_number' => $request->employee_document_card_number[$key],
                        'validity_period' => $request->employee_document_validity_period[$key],
                        'document_file' => $path,
                    ];

                    $newFiles[] = $path;
                }
            }
        }

        if (count($newEmployeeDocuments) > 0) {
            try {
                $employee->employeeDocument()->createMany($newEmployeeDocuments);
            } catch (\Throwable $th) {
                foreach ($newFiles as $key => $value) {
                    $this->delete_file($value);
                }

                throw $th;
            }
        }

        // * delete family tree
        \App\Models\EmployeeFamilyTree::where('employee_id', $employee->id)->delete();

        // * family tree
        $employeeFamilyTrees = [];
        if (is_array($request->employee_family_tree_type)) {
            foreach ($request->employee_family_tree_type as $key => $employee_tree) {
                $employeeFamilyTrees[] = [
                    'type' => $request->employee_family_tree_type[$key],
                    'relation' => $request->employee_family_tree_relation[$key],
                    'name' => $request->employee_family_tree_name[$key],
                    'gender' => $request->employee_family_tree_gender[$key],
                    'birth_place' => $request->employee_family_tree_birth_place[$key],
                    'birth_date' => Carbon::parse($request->employee_family_tree_birth_date[$key]),
                    'education' => $request->employee_family_tree_education[$key],
                    'last_position' => $request->employee_family_tree_last_position[$key],
                    'last_company' => $request->employee_family_tree_last_company[$key],
                ];
            }

            try {
                $employee->employeeFamilyTrees()->createMany($employeeFamilyTrees);
            } catch (\Throwable $th) {
                foreach ($newFiles as $key => $value) {
                    $this->delete_file($value);
                }

                throw $th;
            }
        }

        if (!is_null($request->employee_health_condition) && !is_null($request->employee_health_description) && !is_null($request->employee_health_description_2)) {
            // * health and condition
            try {
                $employee->employeeHealthHistory()->updateOrCreate([
                    'employee_id' => $employee->id,
                ], [
                    'employee_id' => $employee->id,
                    'condition' => $request->employee_health_condition,
                    'description' => $request->employee_health_description,
                    'description_2' => $request->employee_health_description_2,
                ]);
            } catch (\Throwable $th) {
                foreach ($newFiles as $key => $value) {
                    $this->delete_file($value);
                }

                throw $th;
            }
        }

        // * delete old files
        foreach ($oldFiles as $key => $value) {
            $this->delete_file($value ?? '');
        }

        return $employee;
    }

    /**
     * update step 2
     */
    public function update2(UpdateStep2Request $request, \App\Models\Employee $employee)
    {
        // formal education
        \App\Models\EmployeeFormalEducation::where('employee_id', $employee->id)->delete();
        $employeeEducations = [];
        if (is_array($request->education_level)) {
            foreach ($request->education_level as $key => $value) {
                $employeeEducations[] = [
                    'level' => $request->education_level[$key],
                    'name' => $request->education_name[$key],
                    'city' => $request->education_city[$key],
                    'faculty' => $request->education_faculty[$key],
                    'major' => $request->education_major[$key],
                    'from' => Carbon::parse($request->education_from[$key]),
                    'to' => Carbon::parse($request->education_to[$key]),
                    'gpa' => $request->education_gpa[$key],
                    'graduate' => $request->education_graduate[$key],
                ];
            }

            $employee->employeeFormalEducations()->createMany($employeeEducations);
        }

        // informal education
        \App\Models\EmployeeInformalEducation::where('employee_id', $employee->id)->delete();
        $employeeInformalEducation = [];
        if (is_array($request->informal_education_name)) {
            foreach ($request->informal_education_name as $key => $value) {
                $employeeInformalEducation[] = [
                    'name' => $request->informal_education_name[$key],
                    'initiator' => $request->informal_education_initiator[$key],
                    'lama' => $request->informal_education_lama[$key],
                    'year' => $request->informal_education_year[$key],
                    'financed_by' => $request->informal_education_financed_by[$key],
                ];
            }

            $employee->employeeInformalEducations()->createMany($employeeInformalEducation);
        }

        // language
        \App\Models\EmployeeLanguage::where('employee_id', $employee->id)->delete();
        $employeeLanguages = [];
        if (is_array($request->language_language)) {
            foreach ($request->language_language as $key => $value) {
                $employeeLanguages[] = [
                    'language' => $request->language_language[$key],
                    'speak' => $request->language_speak[$key],
                    'listening' => $request->language_listening[$key],
                    'write' => $request->language_write[$key],
                    'read' => $request->language_read[$key],
                ];
            }

            $employee->employeeLanguages()->createMany($employeeLanguages);
        }

        // special education
        \App\Models\EmployeeSpecialEducation::where('employee_id', $employee->id)->delete();
        $employeeSpecialEducations = [];
        if (is_array($request->special_education_name)) {
            foreach ($request->special_education_name as $key => $value) {
                $employeeSpecialEducations[] = [
                    'name' => $request->special_education_name[$key],
                    'year' => $request->special_education_year[$key],
                ];
            }

            $employee->employeeSpecialEducations()->createMany($employeeSpecialEducations);
        }

        // employee parent
        $employee->reason_for_choosing_the_major = $request->reason_for_choosing_the_major;
        $employee->thesis_topic = $request->thesis_topic;
        $employee->reason_for_not_passing = $request->reason_for_not_passing;
        $employee->save();

        return true;
    }

    /**
     * update step 3
     */
    public function update3(UpdateStep3Request $request, \App\Models\Employee $employee)
    {
        // work experience
        \App\Models\EmployeeWorkExperience::where('employee_id', $employee->id)->delete();

        $workExperience = [];
        if (is_array($request->from)) {
            foreach ($request->from as $key => $item) {
                $workExperience[] = [
                    'from' => Carbon::parse($request->from[$key]),
                    'to' => Carbon::parse($request->to[$key]),
                    'name' => $request->name[$key],
                    'phone' => $request->phone[$key],
                    'employee_count' => $request->employee_count[$key],
                    'type' => $request->type[$key],
                    'position' => $request->position[$key],
                    'beginning_position' => $request->beginning_position[$key],
                    'end_position' => $request->end_position[$key],
                    'supervisor' => $request->supervisor[$key],
                    'reason_for_leaving' => $request->reason_for_leaving[$key],
                ];
            }
        }

        $employee->employeeWorkExperiences()->createMany($workExperience);

        return true;
    }

    /**
     * update step 4
     */
    public function update4(UpdateStep4Request $request, \App\Models\Employee $employee)
    {
        // interest
        \App\Models\EmployeeInterest::where('employee_id', $employee->id)->delete();

        $interest[] = [];
        if (is_array($request->interest)) {
            foreach ($request->interest as $key => $value) {
                if (!is_null($interest[$key]) or !$interest[$key] == '') {
                    $interest[] = [
                        'interest' => $request->interest[$key] ?? '',
                        'rank' => $request->rank[$key],
                    ];
                }
            }

            $employee->employeeInterests()->createMany($interest);
        }

        return true;
    }

    /**
     * update step 5
     */
    public function update5(UpdateStep5Request $request, \App\Models\Employee $employee)
    {
        // organisasi
        \App\Models\EmployeeOrganization::where('employee_id', $employee->id)->delete();
        $employeeOrganizations = [];
        if (is_array($request->organization_name)) {
            foreach ($request->organization_name as $key => $value) {
                $employeeOrganizations[] = [
                    'name' => $request->organization_name[$key],
                    'place' => $request->organization_place[$key],
                    'position' => $request->organization_position[$key],
                    'from' => Carbon::parse($request->organization_from[$key]),
                    'to' => Carbon::parse($request->organization_to[$key]),
                ];
            }

            $employee->employeeOrganizations()->createMany($employeeOrganizations);
        }

        // references
        \App\Models\EmployeeReference::where('employee_id', $employee->id)->delete();
        $employeeReferences = [];
        if (is_array($request->reference_name)) {
            foreach ($request->reference_name as $key => $value) {
                $employeeReferences[] = [
                    'name' => $request->reference_name[$key],
                    'address' => $request->reference_address[$key],
                    'phone' => $request->reference_phone[$key],
                    'company' => $request->reference_company[$key],
                    'position' => $request->reference_position[$key],
                    'relation' => $request->reference_relation[$key],
                ];
            }

            $employee->employeeReferences()->createMany($employeeReferences);
        }

        // insiders
        \App\Models\EmployeeInsider::where('employee_id', $employee->id)->delete();
        $employeeInsiders = [];
        if (is_array($request->insider_name)) {
            foreach ($request->insider_name as $key => $value) {
                $employeeInsiders[] = [
                    'name' => $request->insider_name[$key],
                    'position' => $request->insider_position[$key],
                    'relation' => $request->insider_relation[$key],
                ];
            }

            $employee->employeeInsiders()->createMany($employeeInsiders);
        }

        // psikotest
        \App\Models\EmployeePsikotest::where('employee_id', $employee->id)->delete();
        $employeePsikotests = [];
        if (is_array($request->psikotest_place)) {
            foreach ($request->psikotest_place as $key => $value) {
                $employeePsikotests[] = [
                    'place' => $request->psikotest_place[$key],
                    'date' => Carbon::parse($request->psikotest_date[$key]),
                    'cause' => $request->psikotest_cause[$key],
                ];
            }

            $employee->employeePsikotests()->createMany($employeePsikotests);
        }

        // emergency contact
        \App\Models\EmployeeEmergencyContact::where('employee_id', $employee->id)->delete();
        $employeeEmergencyContacts = [];
        if (is_array($request->emergency_name)) {
            foreach ($request->emergency_name as $key => $value) {
                $employeeEmergencyContacts[] = [
                    'nama' => $request->emergency_name[$key],
                    'hubungan' => $request->emergency_relation[$key],
                    'nomor_telepon' => $request->emergency_phone[$key],
                    'alamat' => $request->emergency_address[$key],
                ];
            }

            $employee->employee_emergency_contacts()->createMany($employeeEmergencyContacts);
        }

        return true;
    }

    /**
     * update step 6
     */
    public function update6(UpdateStep6Request $request, \App\Models\Employee $employee)
    {
        // strength and weakness
        \App\Models\EmployeeStrengthWeaknesses::where('employee_id', $employee->id)->delete();
        $strengthAndWeakness = [];
        if (is_array($request->type)) {
            foreach ($request->type as $key => $value) {
                $strengthAndWeakness[] = [
                    'type' => $request->type[$key],
                    'description' => $request->description[$key],
                ];
            }

            $employee->employeeStrengthAndWeaknesses()->createMany($strengthAndWeakness);
        }

        return true;
    }

    /**
     * update step 7
     */
    public function update7(UpdateStep7Request $request, \App\Models\Employee $employee)
    {
        // bank
        \App\Models\EmployeeBank::where('employee_id', $employee->id)->delete();

        $employeeBanks = [];
        if (is_array($request->bank_name)) {
            foreach ($request->bank_name as $key => $value) {
                $employeeBanks[] = [
                    'bank_name' => $request->bank_name[$key],
                    'behalf_of' => $request->bank_behalf_of[$key],
                    'account_number' => $request->bank_account_number[$key],
                ];
            }

            $employee->employee_banks()->createMany($employeeBanks);
        }

        return true;
    }
}
