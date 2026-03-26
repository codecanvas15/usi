@extends('layouts.admin.layout.index')

@php
    $main = 'price';
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
                    <li class="breadcrumb-item active">
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
    <x-card-data-table title="{{ 'edit ' . $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <form action="{{ route("admin.$main.update", $model) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-3">
                        <x-select label="tahun" id="tahun" name="tahun" required>
                            <option value="">Pilih data</option>
                            <option value="{{ $model->period->tahun }}" selected>{{ $model->period->tahun }}</option>
                            @foreach (range(Date('Y'), Date('Y', strtotime('+10 years'))) as $item)
                                @if ($item != $model->period->tahun)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endif
                            @endforeach
                        </x-select>
                    </div>
                    <div class="col-md-3">
                        <x-select label="period" id="period_id" name="period_id" required>
                            <option value="{{ $model->period_id }}" selected>{{ $model->period->value }}</option>
                        </x-select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="harga_beli" name="harga_beli" class="commas-form" value="{{ thousand_to_float_commas($model->harga_beli) ?? '' }}" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="harga_jual" name="harga_jual" class="commas-form" value="{{ thousand_to_float_commas($model->harga_jual) ?? '' }}" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <x-select label="item" id="item_id" name="item_id" required>
                            <option value="{{ $model->item_id }}" selected>{{ $model->item->nama }}</option>
                        </x-select>
                    </div>
                </div>

                <div>
                    <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                    <x-button type="submit" color="primary" label="Save data" />
                </div>
            </form>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#tahun').change(function(e) {
                if ($(this).val().length == 4) {
                    initSelect2Search(`period_id`, "{{ route('admin.select.period') }}/" + $(this).val(), {
                        id: "id",
                        text: "value"
                    });
                }
            });

            inititemSelect('item_id', 'trading');
        });
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-price-sidebar');
        sidebarActive('#price')
    </script>
@endsection
