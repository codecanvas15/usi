@extends('layouts.admin.layout.index')

@php
    $main = 'Import Beginning Balance Item';
@endphp

@section('title', Str::headline("$main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.item.index') }}">Coa</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("$main") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.item.beginning-balance.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <x-card-data-table :title="$main">
            <x-slot name="table_content">
                @include('components.validate-error')
                @foreach ($results as $item)
                    <div class="border-1 border-bottom border-primary pb-5">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input required="required" label="warehouse" value="{{ $item['ware_house_name'] }}" name="" />
                                    <input type="hidden" name="ware_house_id[]" value="{{ $item['ware_house_id'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="code[]" label="Kode" value="{{ $item['kode'] }}" required="required"></x-input>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Nama</label>
                                    <input class="form-control" name="name[]" id="name" value="{{ $item['nama'] }}" required="required">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="type[]" id="type" label="type" value="{{ $model->type ?? '' }}" required>
                                        <option value="">Pilih Item</option>
                                        <option value="general" {{ $item['type'] == 'general' ? 'selected' : '' }}>General</option>
                                        <option value="trading" {{ $item['type'] == 'trading' ? 'selected' : '' }}>Trading</option>
                                        <option value="service" {{ $item['type'] == 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="transport" {{ $item['type'] == 'transport' ? 'selected' : '' }}>Transport</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="status[]" id="status" label="Status" value="{{ $model->status ?? '' }}" required>
                                        @foreach (get_item_status() as $key => $status)
                                            <option value="{{ $key }}" {{ $item['status'] == $key ? 'selected' : '' }}>{{ Str::headline($status['label']) }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="unit_id[]" id="unit-select-{{ $loop->iteration }}" label="Unit" required>
                                        @if (is_null($item['unit_id']))
                                            <option value="{{ $item['unit'] }}">{{ $item['unit'] }}</option>
                                        @else
                                            <option value="{{ $item['unit_id'] }}">{{ $item['unit_data']->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="item_category_id[]" id="itemCategory-select-{{ $loop->iteration }}" label="Kategori Item" required>
                                        @if (is_null($item['item_category_id']))
                                            <option value="{{ $item['item_category'] }}">{{ $item['item_category'] }}</option>
                                        @else
                                            <option value="{{ $item['item_category_id'] }}">{{ $item['item_category_data']->nama }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-text-area name="description[]" label="deskripsi" required>{{ $item['deskripsi'] }}</x-text-area>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="quantity[]" value="{{ formatNumber($item['quantity']) }}" class="commas-form" label="Stock Awal" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="sell_price[]" value="{{ formatNumber($item['sell_price']) }}" class="commas-form" label="harga jual" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="buy_price[]" value="{{ formatNumber($item['buy_price']) }}" class="commas-form" label="harga beli" required />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </x-slot>
            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-3">
                    <x-button type="submit" color="primary" label="Save" icon="save" fontawesome id="btn-submit" />
                </div>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>

    @foreach ($results as $item)
        <script>
            initSelect2Search("unit-select-{{ $loop->iteration }}", `{{ route('admin.select.item-category') }}`, {
                id: "id",
                text: "nama"
            });
            initSelect2Search("itemCategory-select-{{ $loop->iteration }}", `{{ route('admin.select.unit') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endforeach

    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-item-sidebar');
        sidebarActive('#item');
    </script>
@endsection
