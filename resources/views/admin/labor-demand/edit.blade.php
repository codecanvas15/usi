@extends('layouts.admin.layout.index')

@php
    $main = 'labor-demand';
    $title = 'Permintaan Tenaga Kerja';
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
        <form action="{{ route("admin.$main.update", $model->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-card-data-table class="box" title="{{ Str::headline('edit ' . $title) }}">

                <x-slot name="table_content">
                    @include('components.validate-error')

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="branch_id" label="branch" id="branch-select" required>
                                    @if ($model)
                                        <option value="{{ $model->branch_id }}" selected>{{ $model->branch->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-10">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="division_id" label="divisi" id="division-select" required>
                                    @if ($model)
                                        <option value="{{ $model->division_id }}" selected>{{ $model->division->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="location" label="penempatan" value="{{ $model->location }}" id="" required />
                            </div>
                        </div>
                    </div>

                </x-slot>

            </x-card-data-table>

            <div id="other-job-display">

                @foreach ($model->labor_demand_details as $key => $labor_demand_detail)
                    <x-card-data-table title="" id="row-card-job-{{ $loop->index }}">
                        <x-slot name="table_content">
                            <div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="position_id[]" label="position" id="position-select-{{ $loop->index }}" required>
                                                <option value="{{ $labor_demand_detail->position_id }}" selected>{{ $labor_demand_detail->position->nama }}</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="position_name[]" value="{{ $labor_demand_detail->position_name }}" label="nama posisi" id="" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-20 border-top border-primary pt-20">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="education_id[]" label="Pendidikan" id="education-select-{{ $key }}" required>
                                                <option value="{{ $labor_demand_detail->education_id }}" selected>{{ $labor_demand_detail->education->name }}</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="degree_id[]" label="Jurusan" id="degree-select-{{ $key }}" required>
                                                <option value="{{ $labor_demand_detail->degree_id }}" selected>{{ $labor_demand_detail->degree->name }}</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="number" name="min_age[]" value="{{ $labor_demand_detail->min_age }}" label="usia minimal" id="" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="number" name="max_age[]" value="{{ $labor_demand_detail->max_age }}" label="usia maksimal" id="" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="quantity[]" value="{{ $labor_demand_detail->quantity }}" label="jumlah" id="" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="gender[]" label="jenis kelamin" id="gender-select" required>
                                                <option value="" selected>-Jenis Kelamin-</option>
                                                <option value="laki-Laki" {{ $labor_demand_detail->gender == 'laki-Laki' ? 'selected' : '' }}>laki-Laki</option>
                                                <option value="Perempuan" {{ $labor_demand_detail->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                <option value="Laki-Laki / Perempuan" {{ $labor_demand_detail->gender == 'Laki-Laki / Perempuan' ? 'selected' : '' }}>Laki-Laki / Perempuan</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="number" name="long_work_experience[]" value="{{ $labor_demand_detail->long_work_experience }}" label="lama pengalaman kerja" id="" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-20 border-top border-primary pt-20">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-text-area name="work_experience[]" label="pengalaman kerja" id="" cols="30" rows="10">
                                                {{ $labor_demand_detail->work_experience }}
                                            </x-text-area>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-text-area name="skills[]" label="skill pegawai" id="skill" cols="30" rows="10">
                                                {{ $labor_demand_detail->skills }}
                                            </x-text-area>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-text-area name="job_description[]" label="deskripsi pekerjaan" id="" cols="30" rows="10">
                                                {{ $labor_demand_detail->job_description }}
                                            </x-text-area>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-text-area name="description[]" label="keterangan tambahan" id="" cols="30" rows="10">
                                                {{ $labor_demand_detail->description }}
                                            </x-text-area>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-20">
                                    <x-button color="danger" id="delete-job-{{ $loop->index }}" icon="trash" fontawesome size="sm" />
                                </div>
                            </div>
                        </x-slot>
                    </x-card-data-table>
                @endforeach
            </div>

            <div class="box">
                <div class="box-body">
                    <div class="d-flex justify-content-end gap-3 mt-25">
                        <x-button color="info" label="Tambah Lainnya" icon="plus" fontawesome id="add-other-jobs" size="sm" />
                        <x-button type="submit" color="primary" label="Save data" icon="save" fontaweome size="sm" />
                    </div>
                </div>
            </div>
        </form>
    @endcan
@endsection

@section('js')

    @can("edit $main")
        <script src="{{ asset('js/form/select2search.js') }}"></script>

        <script>
            $(document).ready(function() {
                let JOB_INDEX = "{{ $model->labor_demand_details()->count() }}";

                const initializeOldData = () => {
                    @foreach ($model->labor_demand_details as $labor_demand_detail)

                        $(`#delete-job-{{ $loop->index }}`).click(function(e) {
                            e.preventDefault();
                            deleteJob("{{ $loop->index }}");
                        });
                        $(`.gender-select`).select2()
                        initSelect2Search(`position-select-{{ $loop->index }}`, "{{ route('admin.select.position') }}", {
                            id: "id",
                            text: "nama"
                        })
                    @endforeach
                };

                const initializeData = () => {

                    initializeOldData();

                    initSelect2Search(`branch-select`, "{{ route('admin.select.branch') }}", {
                        id: "id",
                        text: "name"
                    })

                    initSelect2Search(`division-select`, "{{ route('admin.select.division') }}", {
                        id: "id",
                        text: "name"
                    })

                    $('#add-other-jobs').click(function(e) {
                        e.preventDefault();
                        addOtherJobs(JOB_INDEX);
                    });
                };

                const addOtherJobs = (job_index) => {

                    let html = `
                    <x-card-data-table title="" id="row-card-job-${job_index}">
                        <x-slot name="table_content">
                            <div >

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="position_id[]" label="position" id="position-select-${job_index}" required></x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="position_name[]" label="nama posisi" id="" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-20 border-top border-primary pt-20">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="education_id[]" label="Pendidikan" id="education-select-${job_index}" required></x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="degree_id[]" label="Jurusan" id="degree-select-${job_index}" required></x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="number" name="min_age[]" label="usia minimal" id="" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="number" name="max_age[]" label="usia maksimal" id="" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="quantity[]" label="jumlah" id="" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="gender[]" label="jenis kelamin" id="gender-select" class="gender-select" required>
                                                <option value="" selected>-Jenis Kelamin-</option>
                                                <option value="laki-Laki">laki-Laki</option>
                                                <option value="Perempuan">Perempuan</option>
                                                <option value="Laki-Laki / Perempuan">Laki-Laki / Perempuan</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="number" name="long_work_experience[]" label="lama pengalaman kerja" id="" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-20 border-top border-primary pt-20">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-text-area name="work_experience[]" label="pengalaman kerja" id="" cols="30" rows="10"></x-text-area>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-text-area name="skills[]" label="skill pegawai" id="skill" cols="30" rows="10"></x-text-area>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-text-area name="job_description[]" label="deskripsi pekerjaan" id="" cols="30" rows="10"></x-text-area>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-text-area name="description[]" label="keterangan tambahan" id="" cols="30" rows="10"></x-text-area>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-20">
                                    <x-button color="danger" id="delete-job-${job_index}" icon="trash" fontawesome size="sm" />
                                </div>
                            </div>
                        </x-slot>
                    </x-card-data-table>
                `;

                    $('#other-job-display').append(html);

                    $(`#delete-job-${job_index}`).click(function(e) {
                        e.preventDefault();
                        deleteJob(job_index);
                    });

                    $(`.gender-select`).select2()
                    initSelect2Search(`position-select-${job_index}`, "{{ route('admin.select.position') }}", {
                        id: "id",
                        text: "nama"
                    })

                    initSelect2Search(`education-select-${job_index}`, "{{ route('admin.select.education') }}", {
                        id: "id",
                        text: "name"
                    })

                    initSelect2Search(`degree-select-${job_index}`, "{{ route('admin.select.degree') }}", {
                        id: "id",
                        text: "name"
                    })

                    JOB_INDEX++;
                };

                const deleteJob = (job_index) => {
                    $(`#row-card-job-${job_index}`).remove();
                };

                initializeData();
            });
        </script>

        @foreach ($model->labor_demand_details as $key => $labor_demand_detail)
            <script>
                initSelect2Search(`education-select-{{ $key }}`, "{{ route('admin.select.education') }}", {
                    id: "id",
                    text: "name"
                })

                initSelect2Search(`degree-select-{{ $key }}`, "{{ route('admin.select.degree') }}", {
                    id: "id",
                    text: "name"
                })
            </script>
        @endforeach
        <script>
            sidebarMenuOpen('#hrd');
            sidebarMenuOpen('#rekrutment-sidebar');
            sidebarActive('#labor-demand');
        </script>
    @endcan
@endsection
