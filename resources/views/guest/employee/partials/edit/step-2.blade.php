@extends('guest.layout.app')

@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline("tambah pendidikan $title") . ' - ')

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
                        {{ Str::headline('tambah pendidikan ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('guest.employee.update.step2', ['employee_id' => $model->id]) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-card-data-table>
            <x-slot name="table_content">
                <div class="progress">
                    <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%"></div>
                </div>
            </x-slot>
        </x-card-data-table>
        <x-card-data-table title="{{ 'pendidikan formal ' . $title }}">
            <x-slot name="header_content">
                <x-button color="primary" icon="plus" fontawesome="" label="Tambah pendidikan" id="add-employee-education"></x-button>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="mt-20" id="formal-education-content">
                    @foreach ($model->employeeFormalEducations as $item)
                        <div class="row border-top border-primary pt-20" id="formal-education-{{ $loop->index }}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="education_level[]" :value="$item->level" label="jenjang" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="education_name[]" :value="$item->name" label="nama pendidikan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="education_city[]" :value="$item->city" label="kota" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="education_faculty[]" :value="$item->faculty" label="fakultas" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="education_major[]" :value="$item->major" label="jurusan" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="education_from[]" :value="$item->from" label="dari" class="datepicker-input" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="education_to[]" :value="$item->to" label="sampai" class="datepicker-input" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="education_gpa[]" :value="$item->gpa" label="ipk" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="education_graduate[]" :value="$item->graduate" label="tahun lulus" />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-employee-formal-education-{{ $loop->index }}"></x-button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-20">
                    <h5>Pendidikan Terakhir</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="reason_for_choosing_the_major" :value="$model->reason_for_choosing_the_major" label="alasan memilih jurusan" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="thesis_topic" :value="$model->thesis_topic" label="judul skripsi" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="reason_for_not_passing" :value="$model->reason_for_not_passing" label="alasan tidak lulus" />
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
        <x-card-data-table title="{{ 'pendidikan informal ' . $title }}">
            <x-slot name="header_content">
                <x-button color="primary" icon="plus" fontawesome="" label="Tambah pendidikan" id="add-employee-informal-education"></x-button>
            </x-slot>
            <x-slot name="table_content">
                <div class="mt-20" id="informal-education-content">
                    @foreach ($model->employeeINformalEducations as $item)
                        <div class="row pt-20" id="informal-education-item-{{ $loop->index }}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="informal_education_name[]" :value="$item->name" label="nama pendidikan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="informal_education_initiator[]" :value="$item->initiator" label="penyelenggara" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="informal_education_lama[]" :value="$item->lama" label="lama" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="informal_education_year[]" :value="$item->year" label="tahun" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="informal_education_financed_by[]" :value="$item->financed_by" label="dibiayai oleh" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-employee-informal-education-{{ $loop->index }}"></x-button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-slot>
        </x-card-data-table>

        <x-card-data-table title="{{ 'kemampuan bahasa ' . $title }}">
            <x-slot name="header_content">
                <x-button color="primary" icon="plus" fontawesome="" label="Tambah pendidikan" id="add-employee-language"></x-button>
            </x-slot>
            <x-slot name="table_content">
                <div class="mt-20" id="language-content">
                    @foreach ($model->employeeLanguages as $item)
                        <div class="row pt-20 border-top border-primary" id="language-content-item-{{ $loop->index }}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="language_language[]" :value="$item->language" label="bahasa" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="language_speak[]" :value="$item->speak" label="berbicara" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="language_listening[]" :value="$item->listening" label="mendengarkan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="language_write[]" :value="$item->write" label="menulis" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="language_read[]" :value="$item->read" label="membaca" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-employee-language-{{ $loop->index }}"></x-button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-slot>
        </x-card-data-table>

        <x-card-data-table title="{{ 'pendidikan khusus, keterampilan khusus ' . $title }}">
            <x-slot name="header_content">
                <x-button color="primary" icon="plus" fontawesome="" label="Tambah pendidikan" id="add-employee-special-education"></x-button>
            </x-slot>
            <x-slot name="table_content">
                <div class="mt-20" id="special-education-content">
                    @foreach ($model->employeeSpecialEducations as $item)
                        <div class="row border-top border-primary pt-20" id="special-education-content-{{ $loop->index }}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="special_education_name[]" :value="$item->name" label="nama pendidikan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="special_education_year[]" :value="$item->year" label="tahun" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-employee-special-education-{{ $loop->index }}"></x-button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-slot>
        </x-card-data-table>

        <x-card-data-table>
            <x-slot name="table_content">
                <div class="d-flex justify-content-end gap-3">
                    <x-button type="reset" color="secondary" label="lewati" link="{{ route('admin.employee.create.step3', ['employee_id' => $model->id]) }}" />
                    <x-button type="submit" color="primary" label="Save data" />
                </div>
            </x-slot>
        </x-card-data-table>
    </form>
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

            const handleFormalEducation = () => {
                let indexFormalEducation = "{{ $model->employeeFormalEducations->count() }}";

                const addEducation = (index) => {
                    let html = `
                    <div class="row border-top border-primary pt-20" id="formal-education-${index}">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="education_level[]" label="jenjang" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="education_name[]" label="nama pendidikan" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="education_city[]" label="kota" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="education_faculty[]" label="fakultas" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="education_major[]" label="jurusan" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="education_from[]" label="dari" class="datepicker-input" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="education_to[]" label="sampai" class="datepicker-input" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="education_gpa[]" label="ipk" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="education_graduate[]" label="tahun lulus" />
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-self-end">
                            <div class="form-group">
                                <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-employee-formal-education-${index}"></x-button>
                            </div>
                        </div>
                    </div>
                    `;

                    $('#formal-education-content').append(html);

                    initDatePicker();

                    $(`#delete-employee-formal-education-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteEducation(index);
                    });
                };

                const deleteEducation = (index) => {
                    $(`#formal-education-${index}`).remove();
                };

                $('#add-employee-education').click(function(e) {
                    e.preventDefault();
                    addEducation(indexFormalEducation);
                    indexFormalEducation++;
                });
            };

            const handleInformalEducation = () => {
                let informalEducationIndex = "{{ $model->employeeInformalEducations->count() }}";

                const addInformalEducation = (index) => {
                    let html = `
                        <div class="row pt-20" id="informal-education-item-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="informal_education_name[]" label="nama pendidikan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="informal_education_initiator[]" label="penyelenggara" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="informal_education_lama[]" label="lama" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="informal_education_year[]" label="tahun" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="informal_education_financed_by[]" label="dibiayai oleh" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-employee-informal-education-${index}"></x-button>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#informal-education-content').append(html);

                    $(`#delete-employee-informal-education-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteInformalEducation(index);
                    });
                };

                const deleteInformalEducation = (index) => {
                    $(`#informal-education-item-${index}`).remove();
                };

                $('#add-employee-informal-education').click(function(e) {
                    e.preventDefault();
                    addInformalEducation(informalEducationIndex);
                    informalEducationIndex++;
                });
            };

            const handleLanguage = () => {
                let employeeLanguageIndex = "{{ $model->employeeLanguages->count() }}";

                const addLanguage = (index) => {
                    let html = `
                        <div class="row pt-20 border-top border-primary" id="language-content-item-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="language_language[]" label="bahasa" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="language_speak[]" label="berbicara" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="language_listening[]" label="mendengarkan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="language_write[]" label="menulis" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="language_read[]" label="membaca" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-employee-language-${index}"></x-button>
                                </div>
                            </div>
                        </div>
                    `;

                    $(`#language-content`).append(html);

                    $(`#delete-employee-language-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteLanguage(index);
                    });
                };

                const deleteLanguage = (index) => {
                    $(`#language-content-item-${index}`).remove();
                };

                $('#add-employee-language').click(function(e) {
                    e.preventDefault();
                    addLanguage(employeeLanguageIndex);
                    employeeLanguageIndex++;
                });
            };

            const handleSpecialEducation = () => {
                let employeeSpecialEducationIndex = "{{ $model->employeeSpecialEducations->count() }}";

                const addSpecialEducation = (index) => {
                    let html = `
                        <div class="row border-top border-primary pt-20" id="special-education-content-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="special_education_name[]" label="nama pendidikan" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="special_education_year[]" label="tahun" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-employee-special-education-${index}"></x-button>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#special-education-content').append(html);

                    $(`#delete-employee-special-education-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteSpecialEducation(index);
                    });
                };

                const deleteSpecialEducation = (index) => {
                    $(`#special-education-content-${index}`).remove();
                };

                $('#add-employee-special-education').click(function(e) {
                    e.preventDefault();
                    addSpecialEducation(employeeSpecialEducationIndex);
                    employeeSpecialEducationIndex++;
                });
            };

            const init = () => {
                handleFormalEducation();
                handleInformalEducation();
                handleLanguage();
                handleSpecialEducation();
            };

            init();
        });
    </script>

    @foreach ($model->employeeFormalEducations as $item)
        <script>
            $(document).ready(function() {
                let index = "{{ $loop->index }}"
                $(`#delete-employee-formal-education-${index}`).click(function(e) {
                    e.preventDefault();
                    deleteEducation(index);
                });

                const deleteEducation = (index) => {
                    $(`#formal-education-${index}`).remove();
                };
            });
        </script>
    @endforeach

    @foreach ($model->employeeInformalEducations as $item)
        <script>
            $(document).ready(function() {
                let index = "{{ $loop->index }}";

                $(`#delete-employee-informal-education-${index}`).click(function(e) {
                    e.preventDefault();
                    deleteInformalEducation(index);
                });

                const deleteInformalEducation = (index) => {
                    $(`#informal-education-item-${index}`).remove();
                };
            });
        </script>
    @endforeach

    @foreach ($model->employeeLanguages as $item)
        <script>
            $(document).ready(function() {
                let index = "{{ $loop->index }}";

                $(`#delete-employee-language-${index}`).click(function(e) {
                    e.preventDefault();
                    deleteLanguage(index);
                });

                const deleteLanguage = (index) => {
                    $(`#language-content-item-${index}`).remove();
                };
            });
        </script>
    @endforeach

    @foreach ($model->employeeSpecialEducations as $item)
        <script>
            $(document).ready(function() {
                let index = "{{ $loop->index }}";

                $(`#delete-employee-special-education-${index}`).click(function(e) {
                    e.preventDefault();
                    deleteSpecialEducation(index);
                });

                const deleteSpecialEducation = (index) => {
                    $(`#special-education-content-${index}`).remove();
                };
            });
        </script>
    @endforeach
@endsection
