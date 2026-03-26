@extends('layouts.admin.layout.index')

@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline("tambah pengalaman kerja $title") . ' - ')

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
                        {{ Str::headline('tambah pengalaman kerja ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ route('admin.employee.update.step3', ['employee_id' => $model->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="progress">
                        <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="37.5" aria-valuemin="0" aria-valuemax="100" style="width: 37.5%"></div>
                    </div>
                </x-slot>
            </x-card-data-table>
            <x-card-data-table title="{{ 'pengalaman kerja ' . $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome="" label="Tambah pengalaman kerja" id="add-employee-work-experience"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="mt-20" id="work-experience-content">
                        @foreach ($model->employeeWorkExperiences as $item)
                            <div class="border-top border-primary pt-20" id="work-experience-item-{{ $loop->index }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="to[]" :value="$item->to" label="dari" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="from[]" :value="$item->from" label="sampai" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="name[]" :value="$item->name" label="nama perusahaan" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="phone[]" :value="$item->phone" label="telepon" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="employee_count[]" :value="$item->employee_count" label="jumlah karyawan" required />

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="type[]" :value="$item->type" label="jenis perusahaan" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="position[]" :value="$item->position" label="jabatan" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="beginning_position[]" :value="$item->beginning_position" label="posisi awal" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="end_position[]" :value="$item->end_position" label="posisi akhir" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="supervisor[]" :value="$item->supervisor" label="atasan" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-text-area name="reason_for_leaving[]" label="alasan berhenti" required>
                                                {{ $item->reason_for_leaving }}
                                            </x-text-area>
                                        </div>
                                    </div>

                                    <div class="col-md-3 d-flex align-self-end">
                                        <div class="form-group">
                                            <x-button color="danger" icon="trash" fontawesome="fas" label="Hapus pengalaman kerja" id="delete-employee-work-experience-{{ $loop->index }}"></x-button>
                                        </div>
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
                        <x-button type="reset" color="secondary" label="lewati" link="{{ route('admin.employee.create.step4', ['employee_id' => $model->id]) }}" />
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

            const handleEmployeeWorkExperience = () => {
                let indexWOrkExperience = "{{ $model->employeeWorkExperiences->count() }}";

                const addWotkExperience = (index) => {
                    let html = `
                        <div class="border-top border-primary pt-20" id="work-experience-item-${index}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="to[]" label="dari" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="from[]" label="sampai" required />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="name[]" label="nama perusahaan" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="phone[]" label="telepon" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="employee_count[]" label="jumlah karyawan" required />

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="type[]" label="jenis perusahaan" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="position[]" label="jabatan" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="beginning_position[]" label="posisi awal" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="end_position[]" label="posisi akhir" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="supervisor[]" label="atasan" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-text-area name="reason_for_leaving[]" label="alasan berhenti" required />
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex align-self-end">
                                    <div class="form-group">
                                        <x-button color="danger" icon="trash" fontawesome="fas" label="Hapus pengalaman kerja" id="delete-employee-work-experience-${index}"></x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#work-experience-content').append(html);

                    initDatePicker();

                    $(`#delete-employee-work-experience-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteWorkExperienve(index);
                    });
                };

                const deleteWorkExperienve = (index) => {
                    $(`#work-experience-item-${index}`).remove();
                };

                $('#add-employee-work-experience').click(function(e) {
                    e.preventDefault();
                    addWotkExperience(indexWOrkExperience);
                    indexWOrkExperience++;
                });
            };

            const init = () => {
                handleEmployeeWorkExperience();
            };

            init();
        });
    </script>

    @foreach ($model->employeeWorkExperiences as $item)
        <script>
            $(document).ready(function() {
                $(`#delete-employee-work-experience-{{ $loop->index }}`).click(function(e) {
                    e.preventDefault();
                    $(`#work-experience-item-{{ $loop->index }}`).remove();
                });
            });
        </script>
    @endforeach
@endsection
