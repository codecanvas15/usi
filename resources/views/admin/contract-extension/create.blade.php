@extends('layouts.admin.layout.index')

@php
    $main = 'contract-extension';
@endphp

@section('title', Str::headline("Create $main") . ' - ')

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
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Create ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route("admin.$main.store") }}" method="post">
        @csrf
        <x-card-data-table title="{{ 'Create ' . $main }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 ">
                            <div class="form-group">
                                <x-select name="employee_id" label="employee" id="employee-select" required></x-select>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group">
                                <x-select name="division_id" label="divisi" id="division-select" required></x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <x-input class="datepicker-input" id="from_date" name="from_date" label="Dari Tanggal" required value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" />
                        </div>
                        <div class="col-md-3">
                            <x-input class="datepicker-input" id="to_date" name="to_date" label="Sampai Tanggal" required value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" />
                        </div>
                        <div class="col-md-12 row mt-2">
                            <x-table>
                                <x-slot name="table_body">
                                    @foreach ($assesment as $item)
                                        <tr>
                                            <td class="align-top"><b>{{ Str::headline($item) }}</b></td>
                                            <td class="align-top">
                                                <x-input-radio type="radio" name="{{ $item }}" value="baik" label="baik" id="{{ $item }}_baik" hideAsterix required />
                                                <input type="hidden" name="assesment[]" value="{{ Str::replace(' ', '_', $item) }}">
                                            </td>
                                            <td class="align-top">
                                                <x-input-radio type="radio" name="{{ $item }}" value="cukup" label="cukup" id="{{ $item }}_cukup" hideAsterix required />
                                            </td>
                                            <td class="align-top">
                                                <x-input-radio type="radio" name="{{ $item }}" value="kurang baik" label="kurang baik" id="{{ $item }}_kurang_baik" hideAsterix required />
                                            </td>
                                            <td class="align-top">
                                                <div class="form-group">
                                                    <label for="note">Note <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="note[]" id="note" rows="3" required></textarea>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-table>
                        </div>
                        <div class="col-md-3">
                            <x-input-radio type="radio" name="submission_status" value="perpanjang" label="Perpanjang" id="perpanjang" required />
                        </div>
                        <div class="col-md-3">
                            <x-input-radio type="radio" name="submission_status" value="tidak diperpanjang" label="Tidak diperpanjang" id="tidak_diperpanjang" required />
                        </div>

                        <div class="col-md-12 text-end">
                            <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-secondary">Cancel</a>
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        checkClosingPeriod($('#from_date'))


        initSelect2Search(`employee-select`, "{{ route('admin.select.employee') }}?employment_status_id=2", {
            id: "id",
            text: "name"
        })

        $('#employee-select').on('change', function() {
            let employee_id = $(this).val()
            // get detail employee
            $.ajax({
                url: `${base_url}/employee/` + employee_id,
                method: "GET",
                success: function(response) {
                    $('#division-select').append(`<option value="${response.data.division_id}" selected>${response.data.division.name}</option>`)
                }
            })
        })
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#contract-sidebar');
        sidebarActive('#contract-extension');
    </script>
@endsection
