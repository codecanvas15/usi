@extends('layouts.admin.layout.index')

@php
    $main = 'labor-application';
    $title = 'lamaran pekerjaan';
@endphp

@section('title', Str::headline("tambah $title") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("tambah $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ route('admin.labor-application.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-card-data-table :title='"tambah $title"'>
                <x-slot name="header_content">
                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    @if (get_current_branch()->is_primary)
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="branch_id" label="cabang" id="branch-select" required>
                                        <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row border-primary border-bottom pb-10">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" label="tanggal" id="date" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="employee_id" label="pegawai yang merekrut" id="employee-select">
                                    @if (auth()->user()->employee)
                                        @php
                                            $employee = auth()->user()->employee;
                                        @endphp

                                        <option value="{{ $employee->id }}" selected>{{ $employee->name }} - {{ $employee->NIK }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        @if ($labor_demand_detail)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="labor_demand_id" label="rekrutment" id="laborDemand-select" disabled required>
                                        <option value="{{ $labor_demand_detail->labor_demand->id }}">{{ $labor_demand_detail->labor_demand->code }}</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="hidden" name="labor_demand_detail_id" value="{{ $labor_demand_detail->id }}">
                                    <x-select label="rekrutment detail" id="laborDemandDetail-select" disabled required>
                                        <option>{{ $labor_demand_detail->position_name }}</option>
                                    </x-select>
                                </div>
                            </div>
                        @else
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="labor_demand_id" label="rekrutment" id="laborDemand-select" required>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="labor_demand_detail_id" label="rekrutment detail" id="laborDemandDetail-select" required></x-select>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row mt-20">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="name" label="nama" id="" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="email" label="email" id="" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="address" label="alamat ktp" id="" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="address_domicil" label="alamat domisili" id="" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="number" name="phone" label="nomor hp" id="" helpers="misal: 0812xxx" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date_of_birth" label="tanggal lahir" id="" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="place_of_birth" label="tempat lahir" id="" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <x-select name="gender" id="gender" label="gender" label="jenis kelamin" required>
                                <option value="">Pilih Item</option>
                                <option value="Laki-Laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </x-select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="marital_status" id="marital_status" label="status pernikahan" value="{{ $model->marital_status ?? '' }}" required>
                                    <option value="">Pilih Item</option>
                                    <option value="1">Sudah Menikah</option>
                                    <option value="0">Belum Menikah</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="identity_card_number" label="nomor identitas (KTP / SIM)" id="" required />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-30">
                        <h4>File Lampiran</h4>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="file_path[]" label="Formulir Lamaran" id="" helpers="Max 4 mb" required />
                                <input type="hidden" name="file_type[]" value="Formulir Lamaran">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="file_path[]" label="KTP" id="" helpers="Max 4 mb" required />
                                <input type="hidden" name="file_type[]" value="KTP">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="file_path[]" label="SIM" id="" helpers="Max 4 mb" />
                                <input type="hidden" name="file_type[]" value="SIM">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="file_path[]" label="Ijazah" id="" helpers="Max 4 mb" />
                                <input type="hidden" name="file_type[]" value="Ijazah">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="file_path[]" label="Sertifikasi pendukung" id="" helpers="Max 4 mb" />
                                <input type="hidden" name="file_type[]" value="Sertifikasi pendukung">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="file_path[]" label="Medical Check Up" id="" helpers="Max 4 mb" />
                                <input type="hidden" name="file_type[]" value="Medical Check Up">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-30">
                        <h4>Kontak darurat</h4>
                        <div id="row-emergency-contact"></div>
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-2">
                        <x-button color="secondary" label="Cancel" icon="x" fontawesome size="sm" />
                        <x-button color="primary" label="save" icon="save" fontawesome size="sm" />
                    </div>
                </x-slot>

            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')

    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>

    <script>
        let emergencyContactIndex = 0;

        $(document).ready(function() {
            checkClosingPeriod($('#date'))

            initSelect2Search('branch-select', "{{ route('admin.select.branch') }}", {
                id: "id",
                text: "name"
            });

            initSelectEmployee('#employee-select');

            initSelect2Search('laborDemand-select', "{{ route('admin.labor-application.get-labor-demand') }}", {
                id: "id",
                text: "code"
            });

            $('#laborDemand-select').change(function(e) {
                e.preventDefault();

                initSelect2Search('laborDemandDetail-select', `{{ route('admin.labor-application.get-labor-demand.detail') }}/${this.value}`, {
                    id: "id",
                    text: "position_name"
                });

            });

            const initializeEmergencyContact = () => {

                const deleteEmergencyContact = (row_index) => {
                    $(`#emergency-row-${row_index}`).remove();
                };

                const addEmergencyContact = (row_index) => {
                    emergencyContactIndex++;

                    let btn = '';

                    if (row_index == 0) {
                        btn = `<x-button color="primary" id="add-emergency-contact" icon="plus" fontawesome size="sm" />`;
                    } else {
                        btn = `<x-button color="danger" id="remove-emergency-contact-${row_index}" icon="minus" fontawesome size="sm" />`;
                    }

                    $('#row-emergency-contact').append(`
                        <div class="row" id="emergency-row-${row_index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_names[]" label="nama" id="" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_relationships[]" label="hubungan" id="" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_phones[]" label="nomor hp" id="" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_addresses[]" label="alamat" id="" required />
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-self-end">
                                <div class="form-group">
                                    ${btn}
                                </div>
                            </div>
                        </div>
                    `);

                    if (row_index == 0) {
                        $('#add-emergency-contact').click(function(e) {
                            e.preventDefault();
                            addEmergencyContact(emergencyContactIndex);
                        });
                    } else {
                        $(`#remove-emergency-contact-${row_index}`).click(function(e) {
                            e.preventDefault();
                            deleteEmergencyContact(row_index);
                        });
                    }
                };

                addEmergencyContact(emergencyContactIndex);
            };
            initializeEmergencyContact();
        });
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#rekrutment-sidebar');
        sidebarActive('#labor-application');
    </script>
@endsection
