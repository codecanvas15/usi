@extends('layouts.admin.layout.index')

@php
    $main = 'permission-letter-employee';
    $title = 'surat izin pegawai';
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
        <x-card-data-table title="{{ 'edit ' . $title }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="{{ route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        @if ($model->letter_type == 'came too late')
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input-radio label="Izin datang terlambat" required class="radioType" name="type" value="came too late" id="late" />
                                </div>
                            </div>
                        @endif
                        @if ($model->letter_type == 'leave early')
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input-radio required class="radioType" name="type" label="Izin pulang lebih awal" value="leave early" id="early" />
                                </div>
                            </div>
                        @endif
                        @if ($model->letter_type == 'leave during working hours')
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input-radio required class="radioType" name="type" label="Izin keluar kantor pada jam kerja" value="leave during working hours" id="outside" />
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row mt-20">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input value="{{ $model->employee?->name }}" readonly />
                                <input type="hidden" name="employee_id" value="{{ $model->employee_id }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row mt-20" id="main-row">
                        <!-- Late -->
                        @if ($model->letter_type == 'came too late')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="letter_date" value="{{ date('d-m-Y', strtotime($model->letter_date_start)) }}" label="tanggal datang" id="" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="time" name="letter_date_start" value="{{ date('H:i', strtotime($model->letter_date_start)) }}" label="jam datang" id="" required />
                                </div>
                            </div>
                        @endif

                        <!-- Early Home -->
                        @if ($model->letter_type == 'leave early')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="letter_date" value="{{ date('d-m-Y', strtotime($model->letter_date_end)) }}" label="tanggal" id="" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="time" name="letter_date_end" value="{{ date('H:i', strtotime($model->letter_date_end)) }}" label="jam_pulang" id="" required />
                                </div>
                            </div>
                        @endif

                        @if ($model->letter_type == 'leave during working hours')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="letter_date" value="{{ date('d-m-Y', strtotime($model->letter_date_end)) }}" label="tanggal" id="" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="time" name="letter_date_start" value="{{ date('H:i', strtotime($model->letter_date_start)) }}" label="dari jam" id="" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="time" name="letter_date_end" value="{{ date('H:i', strtotime($model->letter_date_end)) }}" label="sampai jam" id="" required />
                                </div>
                            </div>
                        @endif

                        @if ($model->letter_type == 'not work')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="letter_date" value="{{ date('d-m-Y', strtotime($model->letter_date_start)) }}" label="tanggal" id="" required />
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row mt-20">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="letter_reason" value="{{ $model->letter_reason }}" label="alasan" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="letter_note" value="{{ $model->letter_note }}" label="catatan" />
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
        $(document).ready(function() {
            var letter_type = "{{ $model->letter_type }}";
            console.log(letter_type)
            if (letter_type == 'leave early') {
                $('#early').attr('checked', true);
            } else if (letter_type == 'came too late') {
                $('#late').attr('checked', true);
            } else if (letter_type == 'not work') {
                $('#alpha').attr('checked', true);
            } else if (letter_type == 'leave during working hours') {
                $('#outside').attr('checked', true);
            }

            $('#tes').val(new Date());

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
                        <x-input class="datepicker-input" name="letter_date_end" label="sampai tanggal" id="letter-date-end" required />
                    </div>
                </div>
            `;

            // $('#main-row').hide();
            $('.radioType').change(function(e) {
                e.preventDefault();
                console.log(this.value)
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

            initSelect2Search('employee-select', "{{ route('admin.select.employee') }}", {
                id: "id",
                text: "name"
            });
        });
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarMenuOpen('#permission-letter-employee');
    </script>
@endsection
