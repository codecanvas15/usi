@extends('layouts.admin.layout.index')

@php
    $main = 'permission-letter-employee';
    $title = 'surat izin pegawai';
@endphp

@section('title', Str::headline("Create $title") . ' - ')

@section('css')
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.3.0/mdb.min.css" rel="stylesheet" /> --}}
@endsection

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
        <x-card-data-table title="{{ 'tambah ' . $title }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input-radio label="Izin datang terlambat" required class="radioType" name="type" value="came too late" id="late" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input-radio required class="radioType" name="type" label="Izin pulang lebih awal" value="leave early" id="early" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input-radio required class="radioType" name="type" label="Izin keluar kantor pada jam kerja" value="leave during working hours" id="outside" />
                            </div>
                        </div>
                    </div>

                    <div class="row mt-20">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="employee_id" label="pegawai" id="employee-select" required>
                                    @if (Auth::user()->employee)
                                        <option value="{{ Auth::user()->employee->id }}" selected>{{ Auth::user()->employee->name }} - {{ Auth::user()->employee->NIK }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-20" id="main-row">

                    </div>

                    <div class="row mt-20">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="letter_reason" label="alasan" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="letter_note" label="catatan" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="file" name="file" label="file" />
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-end gap-3">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>
                </form>

            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        let dateNow = "{!! \Carbon\Carbon::now()->format('d-m-Y') !!}";

        $(document).ready(function() {

            const late = `
                <div class="col-md-4">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="letter_date" label="tanggal datang" id="" required />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <x-input type="time" name="letter_date_start" label="jam datang" id="" required />
                    </div>
                </div>
            `;
            const leave = `
                <div class="col-md-4">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="letter_date" label="tanggal" id="" required />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <x-input type="time" name="letter_date_start" label="dari jam" id="" required />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <x-input type="time" name="letter_date_end" label="sampai jam" id="" required />
                    </div>
                </div>
            `;
            const home = `
                <div class="col-md-4">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="letter_date" label="tanggal" id="" required />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <x-input type="time" name="letter_date_end" label="jam_pulang" id="" required />
                    </div>
                </div>
            `;

            const come = `
                <div class="col-md-4">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="letter_date" label="dari tanggal" id="letter-date" required />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <x-input type="time" name="letter_date_end" label="sampai tanggal" id="letter-date-end" required />
                    </div>
                </div>
            `;

            $('#main-row').hide();
            $('.radioType').change(function(e) {
                e.preventDefault();
                if (this.value) {
                    $('#main-row').show();

                    if (this.value == 'came too late') {
                        $('#main-row').html(late);
                    } else if (this.value == 'leave during working hours') {
                        $('#main-row').html(leave);
                    } else if (this.value == 'leave early') {
                        $('#main-row').html(home);
                    }
                } else {
                    $('#main-row').hide();
                    $('#main-row').html('');
                }

                initDatePicker();
            });

        });
    </script>

    @if (get_current_branch()->is_primary && Auth::user()->can('create employee'))
        <script>
            initSelect2Search('employee-select', "{{ route('admin.select.employee') }}", {
                id: "id",
                text: "name"
            });
        </script>
    @endif

    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarMenuOpen('#permission-letter-employee');
    </script>
@endsection
