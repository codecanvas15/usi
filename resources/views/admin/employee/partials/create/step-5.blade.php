@extends('layouts.admin.layout.index')

@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline("tambah lain lain $title") . ' - ')

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
                        {{ Str::headline('tambah lain lain ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ route('admin.employee.store.step5', ['employee_id' => $model->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="progress">
                        <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="62.5" aria-valuemin="0" aria-valuemax="100" style="width: 62.5%"></div>
                    </div>
                </x-slot>
            </x-card-data-table>
            <x-card-data-table title="{{ 'organisasi ' . $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome="" label="Tambah organisasi" id="add-employee-organization"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="mt-20" id="organization-content">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="{{ 'referensi ' . $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome="" label="Tambah referensi" id="add-employee-reference"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    <div class="mt-20" id="reference-content">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="{{ 'kenalan di dalam kantor ' . $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome="" label="Tambah kenalan di dalam kantor" id="add-employee-insider"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    <div class="mt-20" id="insider-content">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="{{ 'psikotest ' . $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome="" label="Tambah psikotest" id="add-employee-psikotest"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    <div class="mt-20" id="psikotest-content">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="{{ 'kontak darurat ' . $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome="" label="Tambah kontak darurat" id="add-employee-emergency-contact"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    <div class="mt-20" id="emergency-contact-content">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="lewati" link="{{ route('admin.employee.create.step6', ['employee_id' => $model->id]) }}" />
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

            const handleOrganization = () => {
                let employeeOrganizationIndex = 0;

                const addOrganization = (index) => {
                    let html = `
                        <div class="row border-top border-primary pt-20" id="organization-item-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="organization_name[]" label="nama" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="organization_place[]" label="tempat" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="organization_position[]" label="jabatan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="organization_from[]" label="dari" class="datepicker-input" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="organization_to[]" label="sampai" class="datepicker-input" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome="fas" id="delete-organization-${index}"></x-button>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#organization-content').append(html);
                    initDatePicker();

                    $(`#delete-organization-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteOrganization(index);
                    });
                };

                const deleteOrganization = (index) => {
                    $(`#organization-item-${index}`).remove();
                };

                $('#add-employee-organization').click(function(e) {
                    e.preventDefault();
                    addOrganization(employeeOrganizationIndex);
                    employeeOrganizationIndex++;
                });
            };

            const handleReference = () => {
                let indexReference = 0;

                const addReference = (index) => {
                    let html = `
                        <div class="row pt-20 border-top border-primary" id="reference-item-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="reference_name[]" label="nama" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="reference_address[]" label="alamat" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="reference_phone[]" label="telepon" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="reference_company[]" label="perusahaan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="reference_position[]" label="jabatan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="reference_relation[]" label="hubungan" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-center">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome="fas" id="delete-reference-${index}"></x-button>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#reference-content').append(html);

                    $(`#delete-reference-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteReference(index);
                    });
                };

                const deleteReference = (index) => {
                    $(`#reference-item-${index}`).remove();
                };

                $('#add-employee-reference').click(function(e) {
                    e.preventDefault();
                    addReference(indexReference);
                    indexReference++;
                });
            }

            const handleInsider = () => {
                let indexInsider = 0;

                const addInsider = (index) => {
                    let html = `
                        <div class="row border-top border-primary pt-20" id="insider-item-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="insider_name[]" label="nama" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="insider_position[]" label="jabatan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="insider_relation[]" label="hubungan" required />
                                </div>
                            </div>

                            <div class="col-md-3 d-flex align-self-center">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome="fas" id="delete-insider-${index}"></x-button>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#insider-content').append(html);

                    $(`#delete-insider-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteInsider(index);
                    });
                };

                const deleteInsider = (index) => {
                    $(`#insider-item-${index}`).remove();
                };

                $('#add-employee-insider').click(function(e) {
                    e.preventDefault();
                    addInsider(indexInsider);
                    indexInsider++;
                });
            };

            const handlePsikotest = () => {
                let indexPsikotest = 0;

                const addPsikotest = (index) => {
                    let html = `
                        <div class="row pt-20 border-top border-primary" id="psikotest-item-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="psikotest_place[]" label="tempat" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="psikotest_date[]" label="tanggal" class="datepicker-input" required />

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="psikotest_cause[]" label="alasan" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome="fas" id="delete-psikotest-${index}"></x-button>
                                </div>
                            </div>
                        </div>
                        `;

                    $('#psikotest-content').append(html);

                    initDatePicker();

                    $(`#delete-psikotest-${index}`).click(function(e) {
                        e.preventDefault();
                        deletePsikotest(index);
                    });
                };

                const deletePsikotest = (index) => {
                    $(`#psikotest-item-${index}`).remove();
                };

                $('#add-employee-psikotest').click(function(e) {
                    e.preventDefault();
                    addPsikotest(indexPsikotest);
                    indexPsikotest++;
                });
            };

            const handleEmergencyContact = () => {
                let emergencyContactIndex = 0;

                const addEmergencyContact = (index) => {
                    let html = `
                        <div class="row pt-20 border-top border-primary" id="emergency-contact-item-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="emergency_name[]" label="nama" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="emergency_relation[]" label="hubungan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="emergency_phone[]" label="telepon" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="emergency_address[]" label="alamat" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome="fas" id="delete-emergency-contact-${index}"></x-button>
                                </div>
                            </div>
                        </div>`;

                    $('#emergency-contact-content').append(html);

                    $(`#delete-emergency-contact-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteEmergencyContact(index);
                    });
                };

                const deleteEmergencyContact = (index) => {
                    $(`#emergency-contact-item-${index}`).remove();
                };

                $('#add-employee-emergency-contact').click(function(e) {
                    e.preventDefault();
                    addEmergencyContact(emergencyContactIndex);
                    emergencyContactIndex++;
                });
            };

            const init = () => {
                handleOrganization();
                handleReference();
                handleInsider();
                handlePsikotest();
                handleEmergencyContact();
            };

            init();
        });
    </script>
@endsection
