@extends('layouts.admin.layout.index')

@php
    $main = 'contract-extension';
@endphp

@section('title', Str::headline("Edit $main") . ' - ')

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
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route("admin.$main.update", ['contract_extension' => $model->id]) }}" method="post">
        @csrf
        @method('PUT')
        <x-card-data-table title="{{ 'Edit ' . $main }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 ">
                            <div class="form-group">
                                <x-select name="employee_id" label="employee" id="employee-select" required>
                                    <option value="{{ $model->employee_id }}">{{ $model->employee->name }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group">
                                <x-select name="division_id" label="divisi" id="division-select" required>
                                    <option value="{{ $model->division_id }}">{{ $model->division->name }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <x-input class="datepicker-input" id="from_date" name="from_date" label="Dari Tanggal" required value="{{ \Carbon\Carbon::parse($model->from_date)->format('d-m-Y') }}" />
                        </div>
                        <div class="col-md-3">
                            <x-input class="datepicker-input" id="to_date" name="to_date" label="Sampai Tanggal" required value="{{ \Carbon\Carbon::parse($model->to_date)->format('d-m-Y') }}" />
                        </div>
                        <div class="col-md-12 row mt-2">
                            <x-table>
                                <x-slot name="table_body">
                                    @foreach ($model->assesment as $item)
                                        <tr>
                                            <td class="align-top"><b>{{ Str::headline($item->type) }}</b></td>
                                            <td class="align-top">
                                                @if ($item->value == 'baik')
                                                    <x-input-radio type="radio" name="{{ $item->type }}" value="baik" label="baik" id="{{ $item->type }}_baik" checked hideAsterix required />
                                                @else
                                                    <x-input-radio type="radio" name="{{ $item->type }}" value="baik" label="baik" id="{{ $item->type }}_baik" hideAsterix required />
                                                @endif
                                                <input type="hidden" name="assesment[]" value="{{ Str::replace(' ', '_', $item->type) }}">
                                                <input type="hidden" name="assesment_id[]" value="{{ $item->id }}">
                                            </td>
                                            <td class="align-top">
                                                @if ($item->value == 'cukup')
                                                    <x-input-radio type="radio" name="{{ $item->type }}" value="cukup" label="cukup" id="{{ $item->type }}_cukup" checked hideAsterix required />
                                                @else
                                                    <x-input-radio type="radio" name="{{ $item->type }}" value="cukup" label="cukup" id="{{ $item->type }}_cukup" hideAsterix required />
                                                @endif
                                            </td>
                                            <td class="align-top">
                                                @if ($item->value == 'kurang baik')
                                                    <x-input-radio type="radio" name="{{ $item->type }}" value="kurang baik" label="kurang baik" id="{{ $item }}_kurang_baik" checked hideAsterix required />
                                                @else
                                                    <x-input-radio type="radio" name="{{ $item->type }}" value="kurang baik" label="kurang baik" id="{{ $item }}_kurang_baik" hideAsterix required />
                                                @endif
                                            </td>
                                            <td class="align-top">
                                                <label for="note">Note</label>
                                                <textarea class="form-control" name="note[]" id="note" rows="3" required>{!! $item->note !!}</textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-table>
                        </div>
                        <div class="col-md-3">
                            @if ($model->submission_status == 'perpanjang')
                                <x-input-radio type="radio" name="submission_status" value="perpanjang" label="Perpanjang" id="perpanjang" checked required />
                            @else
                                <x-input-radio type="radio" name="submission_status" value="perpanjang" label="Perpanjang" id="perpanjang" required />
                            @endif
                        </div>
                        <div class="col-md-3">
                            @if ($model->submission_status == 'tidak diperpanjang')
                                <x-input-radio type="radio" name="submission_status" value="tidak diperpanjang" label="Tidak diperpanjang" checked id="tidak_diperpanjang" required />
                            @else
                                <x-input-radio type="radio" name="submission_status" value="tidak diperpanjang" label="Tidak diperpanjang" id="tidak_diperpanjang" required />
                            @endif
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
        initSelect2Search(`division-select`, "{{ route('admin.select.division') }}", {
            id: "id",
            text: "name"
        })

        initSelect2Search(`employee-select`, "{{ route('admin.select.employee') }}?employment_status_id=2", {
            id: "id",
            text: "name"
        })
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#contract-sidebar');
        sidebarActive('#contract-extension');
    </script>
@endsection
