@extends('layouts.admin.layout.index')

@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline("tambah $title") . ' - ')

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
                        {{ Str::headline('tambah ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="progress">
                        <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="12.5" aria-valuemin="0" aria-valuemax="100" style="width: 12.5%"></div>
                    </div>
                </x-slot>
            </x-card-data-table>
            <x-card-data-table title="{{ 'Informasi personal ' . $title }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    @csrf

                    {{-- <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="NIK" label="NIK" :value="old('NIK')" required />
                            </div>
                        </div>
                    </div> --}}

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="name" label="Nama" :value="old('name')" required class="text-uppercase" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="jenis_kelamin" label="jenis kelamin" required>
                                    <option value="">-- PILIH JENIS KELAMIN --</option>
                                    @foreach (\App\Enums\GenderEnum::cases() as $item)
                                        <option value="{{ $item['name'] }}" @if (old('jenis_kelamin') == $item['name']) selected @endif>{{ $item['value'] }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="tempat_lahir" label="Tempat Lahir" :value="old('tempat_lahir')" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="tanggal_lahir" label="Tanggal Lahir" :value="localDate(old('tanggal_lahir'))" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="religion" label="agama" required>
                                    <option value="">-- PILIH AGAMA --</option>
                                    @foreach (\App\Enums\ReligionEnum::cases() as $item)
                                        <option value="{{ $item['name'] }}" @if (old('religion') == $item['name']) selected @endif>{{ Str::upper($item['value']) }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="non_taxable_income_id" id="non_taxable_income_id" label="status pernikahan" label="status pernikahan" required></x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="hobby" label="Hobby" :value="old('hobby')" />
                            </div>
                        </div>
                        @foreach (['weight' => 'berat badan', 'height' => 'tinggi', 'blood_type' => 'golongan darah'] as $key => $item)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="{{ $key }}" :label="$item" :value="old($key)" />
                                </div>
                            </div>
                        @endforeach
                        @foreach ([
                'education_id' => 'pendidikan terakhir',
                'degree_id' => 'gelar',
            ] as $key => $item)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select :name="$key" id="select-{{ $key }}" :label="$item" required>

                                    </x-select>
                                </div>
                            </div>
                        @endforeach
                        <div class="row mt-20 pt-20 border-top border-primary">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="file" name="file" helpers="Gambar, .jpg .png .jpeg" label="Profile" required />
                                </div>
                            </div>
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
                                                'lain' => 'Lain lain',
                                            ];
                                            $isSelected = 0;
                                        @endphp
                                        <x-select name="occupied_house" id="occupied_house" value="{{ old('occupied_house') }}" label="Rumah yang ditempati" required>
                                            <option value="">-- PILIH RUMAH YANG DITEMPATI --</option>
                                            @foreach ($optionOccupied as $key => $opt)
                                                <option value="{{ $key }}" {{ old('occupied_house') == $key ? 'selected' : '' }}>{{ $opt }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="wrapper_input_occupied_house">
                                    <x-input name="occupied_house_alt" label="rumah yang ditempati" :value="old('occupied_house_alt')" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="no_ktp" label="nomor KTP" :value="old('no_ktp')" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="ktp_file" helpers="Gambar, .jpg .png .jpeg, .pdf" label="Foto KTP" required accept="image/*, application/pdf" />
                            </div>
                        </div>

                        <div class="col-md-12"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input name="alamat" label="Alamat ktp" :value="old('alamat')" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="postal_code" label="Kode Pos" value="{{ old('postal_code') }}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input name="alamat_domisili" label="Alamat domisili" :value="old('alamat_domisili')" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="postal_code_current_residential_address" label="Kode Pos Domisili    " value="{{ old('postal_code_current_residential_address') }}" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="npwp" label="NPWP" :value="old('npwp')" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="npwp_file" helpers="Gambar, .jpg .png .jpeg, .pdf" label="Foto NPWP" required accept="image/*, application/pdf" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="email" name="email" label="Email" :value="old('email')" required class="text-lowercase" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="nomor_telepone" label="Nomor Hp" :value="old('nomor_telepone')" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="house_phone" label="Telepon Rumah" value="{{ old('house_phone') }}" />
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
                                        <x-input name="vehicle" helpers="kendaraan pribadi" label="Jenis" :value="old('vehicle')" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="vehicle_brand" helpers="Merk kendaraan" label="Merk" :value="old('vehicle_brand')" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input name="vehicle_year" helpers="tahun kendaraan" label="Tahun" :value="old('vehicle_year')" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="checkbox" name="is_vehicle_owner" id="is_vehicle_owner" checked>
                                        <label for="is_vehicle_owner">Pemilik Kendaraan</label>
                                    </div>
                                </div>
                                <div class="col-md-6" id="wrapper_input_vehicle_owner">
                                    <x-input name="vehicle_ownership" label="pemilik kendaraan" :value="old('vehicle_ownership')" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                    </div>

                    <div class="row ">
                        @foreach ([
                'branch_id' => 'cabang',
                'division_id' => 'divisi',
                'position_id' => 'posisi',
            ] as $key => $item)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select :name="$key" id="select-{{ $key }}" :label="$item" required>

                                    </x-select>
                                </div>
                            </div>
                        @endforeach

                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="staff_type" label="status staff" required>
                                    <option value="">-- PILIH STATUS STAFF --</option>
                                    @foreach (\App\Enums\EmployeeTypeEnum::cases() as $item)
                                        <option value="{{ $item['name'] }}" @if (old('staff_type') == $item['name']) selected @endif>{{ $item['name'] }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="employee_status" label="status" required>
                                    <option value="">-- PILIH STATUS --</option>
                                    @foreach (\App\Enums\EmployeeStatusEnum::cases() as $item)
                                        <option value="{{ $item['name'] }}" @if (old('employee_status') == $item['name']) selected @endif>{{ $item['value'] }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="employment_status_id" label="status kepegawaian" required id="select-employment_status_id">
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row d-none contract-form" id="NON-PKWT-FORM">
                                @foreach ([
                'join_date' => 'tanggal masuk',
                'end_date' => 'tanggal resign',
            ] as $key => $item)
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="{{ $key }}" :label="$item" :value="localDate(old($key))" />
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
                                            <x-input class="datepicker-input" name="{{ $key }}" :label="$item" :value="localDate(old($key))" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row mt-20 pt-20 border-top border-primary">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="leave" helpers="jatah cuti per tahun" label="jatah cuti" :value="old('leave')" required />
                            </div>
                        </div>
                    </div>

                    <div class="row mt-20 pt-20 border-top border-primary">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input name="parents_residence_address" label="Alamat Tinggal Orangtua (jika berbeda dengan Anda)" value="{{ old('parents_residence_address') }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="postal_code_parents_residence_address" label="Kode Pos Orangtua" value="{{ old('postal_code_parents_residence_address') }}" />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="parents_phone_number" label="Nomor Hp Ortu" :value="old('parents_phone_number')" required />
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

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="informasi keluarga {{ $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome label="Tambah informasi keluarga" id="add-family-tree"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    <div id="content-family-tree" class="mt-20">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="informasi kesehatan {{ $title }}">
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bagaimana kondisi kesehatan sekarang?</label>
                                <textarea name="employee_health_condition" id="" cols="30" rows="10" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Apakah pernah mengalami sakit keras/ kecelakaan berat?</label>
                                <textarea name="employee_health_description" id="" cols="30" rows="10" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Apakah ada efek samping yang dirasakan hingga saat ini</label>
                                <textarea name="employee_health_description_2" id="" cols="30" rows="10" class="form-control"></textarea>
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
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-employee-sidebar');
        sidebarActive('#employee-sidebar');
        $('body').addClass('sidebar-collapse');
    </script>

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

                let indexFamily = 0;

                const addFamilyTree = (index) => {
                    let html = `
                        <div class="row border-top border-primary py-20" id="family-tree-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="employee_family_tree_type[]" id="employee-family-tree-type-${index}" label="tipe keluarga" required>
                                        <option value="">-- pilih tipe keluarga --</option>
                                        @foreach (\App\Enums\EmployeeFamilyTreeTypeEnum::cases() as $item)
                                            <option value="{{ $item['value'] }}">{{ $item['name'] }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="employee_family_tree_relation[]" id="employee-family-tree-relation-${index}" label="hubungan" required>
                                        <option value="">-- pilih hubungan --</option>
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
                                        <option value="">-- PILIH JENIS KELAMIN --</option>
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
                            <div class="col-md-3">
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
                            $(`#employee-family-tree-relation-${index}`).html('<option value="">-- pilih hubungan --</option>');
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
                let indexEmployeeDocument = 0;

                const addEmployeeDocument = (index) => {
                    let html = `
                        <div class="row" id="employee-document-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="file" name="employee_document_document_file[]" label="dokumen" required />
                                </div>
                            </div>
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

                $('#select-employment_status_id').change(function() {
                    let selected_value = $(this).find('option:selected').text();
                    $('.contract-form').addClass('d-none');
                    $(`#${selected_value}-FORM`).removeClass('d-none');

                })
            };

            const init = () => {
                initSelect2();
                handleFamilyTree();
                handleEmployeeDocument();
            };

            init();
        });
    </script>

    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

@endsection
