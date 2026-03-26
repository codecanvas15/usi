@extends('layouts.admin.layout.index')

@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline("Edit $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <x-card-data-table>
            <x-slot name="table_content">
                <div class="progress">
                    <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="12.5" aria-valuemin="0" aria-valuemax="100" style="width: 12.5%"></div>
                </div>
            </x-slot>
        </x-card-data-table>

        <form action="{{ route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-card-data-table title="{{ 'edit ' . $main }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="NIK" label="NIK" value="{{ old('NIK') ?? $model->NIK }}" disabled="disabled" required="required" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="name" label="Nama" value="{{ old('name') ?? $model->name }}" required="required" class="text-uppercase" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="jenis_kelamin" label="gender" required>
                                    <option value="">-- PILIH JENIS KELAMIN --</option>
                                    @foreach (\App\Enums\GenderEnum::cases() as $item)
                                        <option value="{{ $item['value'] }}" @if (old('jenis_kelamin') == $item['value'] or $model->jenis_kelamin == $item['value']) selected @endif>{{ $item['value'] }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="tempat_lahir" label="Tempat lahir" value="{{ old('tempat_lahir') ?? $model->tempat_lahir }}" required="required" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="tanggal_lahir" class="datepicker-input" label="Tanggal lahir" value="{{ localDate(old('tanggal_lahir') ?? $model->tanggal_lahir) }}" required="required" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="religion" label="agama" required>
                                    <option value="">-- pilih agama --</option>
                                    @foreach (\App\Enums\ReligionEnum::cases() as $item)
                                        <option value="{{ $item['name'] }}" @if (old('religion') == $item['name'] or $model->religion == $item['name']) selected @endif>{{ Str::upper($item['value']) }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-gorup">
                                <x-select name="non_taxable_income_id" id="non_taxable_income_id" label="status pernikahan" label="status pernikahan" required>
                                    @if ($model->non_taxable_income)
                                        <option value="{{ $model->non_taxable_income_id }}">{{ $model->non_taxable_income?->note }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="hobby" label="Hobby" :value="old('hobby') ?? $model->hobby" />
                            </div>
                        </div>
                        @foreach (['weight' => 'berat badan', 'height' => 'tinggi', 'blood_type' => 'golongan darah'] as $key => $item)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="{{ $key }}" :label="$item" :value="old($key) ?? $model->$key" />
                                </div>
                            </div>
                        @endforeach
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="education_id" id="select-education_id" label="Pendidikan terakhir" required>
                                    @if ($model->education)
                                        <option value="{{ $model->education_id }}">{{ $model->education->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="degree_id" id="select-degree_id" label="Gelar" required>
                                    @if ($model->degree)
                                        <option value="{{ $model->degree_id }}">{{ $model->degree->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="file" helpers="Gambar, .jpg .png .jpeg" label="Profile" />
                            </div>
                            @if ($model->file)
                                <img src="{{ asset('/storage/' . $model->file) }}" width="136px" alt="profile">
                            @endif
                        </div>

                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        @php
                                            $optionOccupied = [
                                                'rumah_pribadi' => 'Rumah Pribadi',
                                                'rumah_orangtua' => 'Rumah Orang Tua',
                                                'kontrak' => 'Kontrak',
                                                'sewa' => 'Sewa',
                                                'indekos' => 'Indekos',
                                            ];
                                            $isSelected = 0;
                                        @endphp
                                        <x-select name="occupied_house" id="occupied_house" value="{{ old('occupied_house') ?? $model->occupied_house }}" label="Rumah yang ditempati" required>
                                            <option value="">-- pilih rumah yang ditempati --</option>
                                            @foreach ($optionOccupied as $key => $opt)
                                                @php
                                                    if ($key == $model->occupied_house) {
                                                        $isSelected = 1;
                                                    }
                                                @endphp
                                                <option value="{{ $key }}" {{ $key == $model->occupied_house ? 'selected' : '' }}>{{ $opt }}</option>
                                            @endforeach

                                            @if ($isSelected == 0)
                                                <option value="lain" selected>Lain - lain</option>
                                            @else
                                                <option value="lain">Lain - lain</option>
                                            @endif
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="wrapper_input_occupied_house">
                                    <x-input name="occupied_house_alt" label="rumah yang ditempati" :value="old('occupied_house_alt') ?? $model->occupied_house" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="no_ktp" label="nomor KTP" value="{{ old('no_ktp') ?? $model->no_ktp }}" required="required" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="ktp_file" helpers="Gambar, .jpg .png .jpeg, .pdf" label="Foto KTP" accept="image/*, application/pdf" />
                            </div>
                        </div>

                        <div class="col-md-12"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input name="alamat" label="alamat ktp" value="{{ old('alamat') ?? $model->alamat }}" required="required" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="postal_code" label="Kode Pos" value="{{ old('postal_code') ?? $model->postal_code }}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input name="alamat_domisili" label="alamat domisili" value="{{ old('alamat_domisili') ?? $model->alamat_domisili }}" required="required" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="postal_code_current_residential_address" label="Kode Pos Domisili" value="{{ old('postal_code_current_residential_address') ?? $model->current_postal_code }}" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="npwp" label="NPWP" value="{{ old('npwp') ?? $model->npwp }}" required="required" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="npwp_file" helpers="Gambar, .jpg .png .jpeg, .pdf" label="Foto NPWP" accept="image/*, application/pdf" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="email" label="email" value="{{ old('email') ?? $model->email }}" required="required" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="nomor_telepone" label="Nomor hp" value="{{ old('nomor_telepone') ?? $model->nomor_telepone }}" required="required" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="house_phone" label="Telepon Rumah" value="{{ old('house_phone') ?? $model->house_phone }}" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div class="col-md-12">
                            <label for="" class="form-label">Kendaraan</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="vehicle" helpers="kendaraan yang di pakai ke kantor" label="Jenis" :value="old('vehicle') ?? $model->vehicle" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="vehicle_brand" helpers="Merk kendaraan" label="Merk" :value="old('vehicle_brand') ?? $model->vehicle_brand" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="vehicle_year" helpers="tahun kendaraan" label="Tahun" :value="old('vehicle_year') ?? $model->vehicle_year" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="checkbox" name="is_vehicle_owner" id="is_vehicle_owner" {{ is_null($model->vehicle_ownership) ? 'checked' : '' }}>
                                        <label for="is_vehicle_owner">Pemilik Kendaraan</label>
                                    </div>
                                </div>
                                <div class="col-md-6" id="wrapper_input_vehicle_owner">
                                    <x-input name="vehicle_ownership" label="pemilik kendaraan" :value="old('vehicle_ownership') ?? $model->vehicle_ownership" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="branch_id" id="select-branch_id" label="Cabang" required>
                                    @if ($model->branch)
                                        <option value="{{ $model->branch_id }}">{{ $model->branch->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="division_id" id="select-division_id" label="Divisi" required>
                                    @if ($model->division)
                                        <option value="{{ $model->division_id }}">{{ $model->division->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="position_id" id="select-position_id" label="Posisi" required>
                                    @if ($model->position)
                                        <option value="{{ $model->position_id }}">{{ $model->position->nama }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="staff_type" label="status staff" required>
                                    <option value="">-- PILIH STATUS STAFF --</option>
                                    @foreach (\App\Enums\EmployeeTypeEnum::cases() as $item)
                                        <option value="{{ $item['name'] }}" @if (old('staff_type') == $item['name'] or $model->staff_type == $item['name']) selected @endif>{{ Str::upper($item['name']) }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="employee_status" label="status" required>
                                    <option value="">-- PILIH STATUS --</option>
                                    @foreach (\App\Enums\EmployeeStatusEnum::cases() as $item)
                                        <option value="{{ $item['name'] }}" @if (old('employee_status') == $item['name'] or $model->employee_status == $item['name']) selected @endif>{{ $item['value'] }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="employment_status_id" id="select-employment_status_id" label="Status kepegawaian" required>
                                    @if ($model->employment_status)
                                        <option value="{{ $model->employment_status_id }}">{{ $model->employment_status->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row d-none contract-form" id="NON-PKWT-FORM">
                                @foreach ([
                'join_date' => 'tanggal masuk',
                'end_date' => 'tanggal selesai',
            ] as $key => $item)
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="{{ $key }}" :label="$item" :value="localDate(old($key) ?? $model->$key)" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row d-none contract-form" id="PKWT-FORM">
                                @foreach ([
                'start_contract' => 'mulai kontrak',
                'end_contract' => 'selesai kontrak',
            ] as $key => $item)
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="{{ $key }}" :label="$item" :value="localDate(old($key) ?? $model->$key)" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>

                    <div class="row mt-20 pt-20 border-top border-primary">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="leave" helpers="jatah cuti per tahun" label="jatah cuti" :value="old('leave') ?? $model->leave" required />
                            </div>
                        </div>
                    </div>

                    <div class="row mt-20 pt-20 border-top border-primary">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input name="parents_residence_address" label="Alamat Tinggal Orangtua (jika berbeda dengan Anda)" value="{{ old('current_residential_address') ?? $model->parents_address }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="postal_code_parents_residence_address" label="Kode Pos Orangtua" value="{{ old('postal_code_parents_residence_address') ?? $model->parents_postal_code }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="parents_phone_number" label="Nomor hp" value="{{ old('parents_phone_number') ?? $model->parents_phone_number }}" required="required" />
                            </div>
                        </div>
                    </div>
                </x-slot>

            </x-card-data-table>

            <x-card-data-table title="dokumen dokumen {{ $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome label="Tambah document" id="add-document"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    <div class="mt-20" id="content-document">
                        @foreach ($model->employeeDocument->where('document_name', '!=', 'NPWP')->where('document_name', '!=', 'KTP') as $item)
                            <div class="row" id="employee-document-{{ $loop->index }}">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="hidden" name="employee_document_id[]" value="{{ $item->id }}">
                                        <x-input type="file" name="employee_document_document_file[]" label="dokumen" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="employee_document_document_name[]" :value="$item->document_name" label="nama dokumen" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="employee_document_card_number[]" :value="$item->card_number" label="nomor dokumen" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" name="employee_document_validity_period[]" :value="localDate($item->validity_period)" label="masa berlaku" />
                                            </div>
                                        </div>
                                        <div class="col-md d-flex align-self-center">
                                            <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-document-{{ $loop->index }}"></x-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="informasi keluarga {{ $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome label="Tambah informasi keluarga" id="add-family-tree"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    <div id="content-family-tree" class="mt-20">
                        @foreach ($model->employeeFamilyTrees as $family)
                            <div class="row border-top border-primary py-20" id="family-tree-{{ $loop->index }}">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="employee_family_tree_type[]" id="employee-family-tree-type-{{ $loop->index }}" label="tipe keluarga" required>
                                            <option value="">-- PILIH TIPE KELUARGA --</option>
                                            @foreach (\App\Enums\EmployeeFamilyTreeTypeEnum::cases() as $item)
                                                <option value="{{ $item['value'] }}" {{ $item['value'] == $family->type ? 'selected' : '' }}>{{ $item['name'] }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="employee_family_tree_relation[]" id="employee-family-tree-relation-{{ $loop->index }}" label="hubungan" required>
                                            <option value="">-- PILIH HUBUNGAN --</option>
                                            @if ($family->type == 'inti')
                                                @foreach (['istri/suami' => 'istri/suami', 'anak' => 'anak'] as $key => $value)
                                                    <option value="{{ $key }}" {{ $family->relation == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            @endif

                                            @if ($family->type == 'besar')
                                                @foreach (['ayah', 'ibu', 'saudara'] as $key => $value)
                                                    <option value="{{ $key }}" {{ $family->relation == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            @endif
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="employee_family_tree_name[]" :value="$family->name" label="nama" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select label="jenis kelamin" name="employee_family_tree_gender[]">
                                            <option value="">-- PILIH GENDER --</option>
                                            @foreach (\App\Enums\GenderEnum::cases() as $item)
                                                <option value="{{ $item['name'] }}" {{ $family->gender == $item['name'] ? 'selected' : '' }}>{{ $item['value'] }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="employee_family_tree_birth_place[]" :value="$family->birth_place" label="tempat lahir" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="employee_family_tree_birth_date[]" :value="localDate($family->birth_date)" label="tanggal lahir" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="employee_family_tree_education[]" :value="$family->education" label="pendidikan terakhir" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="employee_family_tree_last_position[]" :value="$family->last_position" label="posisi terakhir" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="employee_family_tree_last_company[]" :value="$family->last_company" label="perusahaan terakhir" required />
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-self-end">
                                    <div class="form-group">
                                        <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-family-tree-{{ $loop->index }}" />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="informasi kesehatan {{ $title }}">
                <x-slot name="table_content">
                    @php
                        $health = $model->employeeHealthHistory;
                    @endphp
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bagaimana kondisi kesehatan sekarang?</label>
                                <textarea name="employee_health_condition" id="" cols="30" rows="10" class="form-control">{{ $health?->condition }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Apakah pernah mengalami sakit keras/ kecelakaan berat?</label>
                                <textarea name="employee_health_description" id="" cols="30" rows="10" class="form-control">{{ $health?->description }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Apakah ada efek samping yang dirasakan hingga saat ini</label>
                                <textarea name="employee_health_description_2" id="" cols="30" rows="10" class="form-control">{{ $health?->description_2 }}</textarea>
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </x-slot>
            </x-card-data-table>

        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>

    <script>
        $(document).ready(function() {
            const handleVehicleOwner = () => {
                $('#wrapper_input_vehicle_owner').hide();

                $('#is_vehicle_owner').change(function(e) {
                    e.preventDefault();

                    if (!$(this).is(':checked')) {
                        $('#wrapper_input_vehicle_owner').show();
                    } else {
                        $('#wrapper_input_vehicle_owner').hide();
                    }
                });
            };

            const handleOccupiedHouse = () => {
                $('#wrapper_input_occupied_house').hide();

                $('#occupied_house').change(function(e) {
                    e.preventDefault();

                    if (this.value == 'lain') {
                        $('#wrapper_input_occupied_house').show();
                    } else {
                        $('#wrapper_input_occupied_house').hide();
                    }
                });
            };

            handleOccupiedHouse();
            handleVehicleOwner();

            $('#is_vehicle_owner').trigger('change');
            $('#occupied_house').trigger('change');

            const initSelect2 = () => {
                initSelect2Search(
                    `non_taxable_income_id`,
                    `${base_url}/select/non-taxable-income`, {
                        id: "id",
                        text: "note",
                    },
                    0, {},
                );

                initSelect2Search('select-branch_id', "{{ route('admin.select.branch') }}", {
                    id: "id",
                    text: "name"
                });

                initSelect2Search('select-employment_status_id', "{{ route('admin.select.employment-status') }}", {
                    id: "id",
                    text: "name"
                });

                initSelect2Search('select-division_id', "{{ route('admin.select.division') }}", {
                    id: "id",
                    text: "name"
                });

                initSelect2Search('select-position_id', "{{ route('admin.select.position') }}", {
                    id: "id",
                    text: "nama"
                });

                initSelect2Search('select-education_id', "{{ route('admin.select.education') }}", {
                    id: "id",
                    text: "name"
                });

                initSelect2Search('select-degree_id', "{{ route('admin.select.degree') }}", {
                    id: "id",
                    text: "name"
                });
            };

            const handleFamilyTree = () => {

                let indexFamily = "{{ $model->employeeFamilyTrees->count() }}";

                const addFamilyTree = (index) => {
                    let html = `
                        <div class="row border-top border-primary py-20" id="family-tree-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="employee_family_tree_type[]" id="employee-family-tree-type-${index}" label="tipe keluarga" required>
                                        <option value="">-- PILIH TIPE KELUARGA --</option>
                                        @foreach (\App\Enums\EmployeeFamilyTreeTypeEnum::cases() as $item)
                                            <option value="{{ $item['value'] }}">{{ $item['name'] }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="employee_family_tree_relation[]" id="employee-family-tree-relation-${index}" label="hubungan" required>
                                        <option value="">-- PILIH HUBUNGAN --</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="employee_family_tree_name[]" label="nama" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select label="jenis kelamin" name="employee_family_tree_gender[]">
                                        <option value="">-- PILIH GENDER --</option>
                                        @foreach (\App\Enums\GenderEnum::cases() as $item)
                                            <option value="{{ $item['name'] }}">{{ $item['value'] }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="employee_family_tree_birth_place[]" label="tempat lahir" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="employee_family_tree_birth_date[]" label="tanggal lahir" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="employee_family_tree_education[]" label="pendidikan terakhir" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="employee_family_tree_last_position[]" label="posisi terakhir" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="employee_family_tree_last_company[]" label="perusahaan terakhir" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-family-tree-${index}" />
                                </div>
                            </div>
                        </div>
                    `;

                    $('#content-family-tree').append(html);

                    initDatePicker();

                    $(`#employee-family-tree-type-${index}`).select2();
                    $(`#employee-family-tree-type-${index}`).change(function(e) {
                        e.preventDefault();

                        if (this.value == 'inti') {
                            let optionArray = [
                                'istri/suami',
                                'anak',
                            ];

                            let options = optionArray.map((item) => {
                                return `<option value="${item}">${item}</option>`;
                            }).join('');

                            $(`#employee-family-tree-relation-${index}`).html(options);

                        } else if (this.value == 'besar') {
                            let optionArray = [
                                "ayah",
                                "ibu",
                                "saudara"
                            ];

                            let options = optionArray.map((item) => {
                                return `<option value="${item}">${item}</option>`;
                            }).join('');

                            $(`#employee-family-tree-relation-${index}`).html(options);
                        } else {
                            $(`#employee-family-tree-relation-${index}`).html('<option value="">-- PILIH HUBUNGAN --</option>');
                        }

                        $(`#employee-family-tree-relation-${index}`).select2();
                    });

                    $(`#delete-family-tree-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteFamilyTree(index);
                    });
                };

                const deleteFamilyTree = (index) => {
                    $(`#family-tree-${index}`).remove();
                };

                $('#add-family-tree').click(function(e) {
                    e.preventDefault();
                    addFamilyTree(indexFamily);
                    indexFamily++;
                });
            };

            const handleEmployeeDocument = () => {
                let indexEmployeeDocument = "{{ $model->employeeDocument->count() }}";

                const addEmployeeDocument = (index) => {
                    let html = `
                        <div class="row" id="employee-document-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="file" name="employee_document_document_file[]" label="dokumen" required />
                                    </div>
                                    </div>
                                    <input type="hidden" name="employee_document_id[]" value="">
                                    <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="employee_document_document_name[]" label="nama dokumen" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="employee_document_card_number[]" label="nomor dokumen" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="employee_document_validity_period[]" label="masa berlaku" />
                                        </div>
                                    </div>
                                    <div class="col-md d-flex align-self-center">
                                        <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-document-${index}"></x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#content-document').append(html);

                    initDatePicker();

                    $(`#delete-document-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteEmployeeDocument(index);
                    });
                };

                const deleteEmployeeDocument = (index) => {
                    $(`#employee-document-${index}`).remove();
                };

                $('#add-document').click(function(e) {
                    e.preventDefault();
                    addEmployeeDocument(indexEmployeeDocument);
                    indexEmployeeDocument++;
                });
            };

            const init = () => {
                initSelect2();
                handleFamilyTree();
                handleEmployeeDocument();
            };

            init();
        });
    </script>

    @foreach ($model->employeeDocument as $item)
        <script>
            $(document).ready(function() {
                let index = '{{ $loop->index }}';

                $(`#delete-document-${index}`).click(function(e) {
                    e.preventDefault();
                    deleteEmployeeDocument(index);
                });

                const deleteEmployeeDocument = (index) => {
                    $(`#employee-document-${index}`).remove();
                };
            });
        </script>
    @endforeach

    @foreach ($model->employeeFamilyTrees as $item)
        <script>
            $(document).ready(function() {
                var index = '{{ $loop->index }}';

                $(`#employee-family-tree-type-${index}`).select2();
                $(`#employee-family-tree-type-${index}`).change(function(e) {
                    e.preventDefault();

                    if (this.value == 'inti') {
                        let optionArray = [
                            'istri/suami',
                            'anak',
                        ];

                        let options = optionArray.map((item) => {
                            return `<option value="${item}">${item}</option>`;
                        }).join('');

                        $(`#employee-family-tree-relation-${index}`).html(options);

                    } else if (this.value == 'besar') {
                        let optionArray = [
                            "ayah",
                            "ibu",
                            "saudara"
                        ];

                        let options = optionArray.map((item) => {
                            return `<option value="${item}">${item}</option>`;
                        }).join('');

                        $(`#employee-family-tree-relation-${index}`).html(options);
                    } else {
                        $(`#employee-family-tree-relation-${index}`).html('<option value="">-- PILIH HUBUNGAN --</option>');
                    }

                    $(`#employee-family-tree-relation-${index}`).select2();
                });

                $(`#employee-family-tree-type-${index}`).trigger('change');

                $(`#delete-family-tree-${index}`).click(function(e) {
                    e.preventDefault();
                    deleteFamilyTree(index);
                });

                const deleteFamilyTree = (index) => {
                    $(`#family-tree-${index}`).remove();
                };
            });
        </script>
    @endforeach

    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-employee-sidebar');
        sidebarActive('#employee-sidebar');
        $('body').addClass('sidebar-collapse');
    </script>
@endsection
