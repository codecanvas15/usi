@extends('layouts.admin.layout.index')

@php
    $main = 'item';
@endphp

@section('title', Str::headline("Import $main") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Import ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <form action="{{ route('admin.item.import-store') }}" method="post" enctype="multipart/form-data">
            <x-card-data-table title="{{ 'Import ' . $main }}">
                <x-slot name="table_content">
                    @include('components.validate-error')
                    @csrf
                    <x-table theadColor='dark'>
                        <x-slot name="table_head">
                            <th>#</th>
                            <th>KODE</th>
                            <th>NAMA</th>
                            <th>TYPE</th>
                            <th>STATUS</th>
                            <th>UNIT</th>
                            <th>KATEGORI</th>
                            <th>ITEM TYPE</th>
                        </x-slot>
                        <x-slot name="table_body">
                            @foreach ($results ?? [] as $key => $item)
                                <tr class="{{ $item['is_exists'] ? 'bg-danger' : '' }}">
                                    <td>
                                        {{ $loop->iteration }}
                                        <input type="hidden" name="item_id[{{ $key }}]" value="{{ $item['id'] }}">
                                    </td>
                                    <td>
                                        <input type="text" name="code[]" class="form-control" value="{{ $item['kode'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="name[]" class="form-control" value="{{ $item['nama'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="type[]" class="form-control" value="{{ $item['type'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="status[]" class="form-control" value="{{ $item['status'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="unit_id[]" value="{{ $item['unit_id'] }}">
                                        <input type="text" name="unit[]" class="form-control" value="{{ $item['unit'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="item_category_id[]" value="{{ $item['item_category_id'] }}">
                                        <input type="text" name="item_category[]" class="form-control" value="{{ $item['item_category_data']->nama ?? $item['item_category'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="item_type_id[]" value="{{ $item['item_type_id'] }}">
                                        <input type="text" name="item_type[]" class="form-control" value="{{ $item['item_type_data']->nama ?? $item['item_type'] }}" readonly>
                                    </td>

                                </tr>
                            @endforeach
                        </x-slot>
                    </x-table>
                    {{-- @foreach ($results as $item)
                        <div class="row border-b border-primary">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="code[]" label="Kode" value="{{ $item['kode'] }}" required="required"></x-input>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="name[]" label="Nama" value="{!! $item['nama'] !!}" required="required"></x-input>
                                </div>
                            </div>

                            <div class="col-md-12">
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="item_type[]" id="itemType-select-{{ $loop->iteration }}" label="item type" required>
                                                @if (isset($item['item_type']))
                                                    <option value="{{ $item['item_type'] }}">{{ $item['item_type'] }}</option>
                                                @endif
                                            </x-select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-text-area name="description[]" label="deskripsi" required>{{ $item['deskripsi'] ?? '-' }}</x-text-area>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach --}}
                </x-slot>
                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@push('script')
    <script src="{{ asset('js/form/select2search.js') }}"></script>

    @foreach ($results as $item)
        <script>
            initSelect2Search("unit-select-{{ $loop->iteration }}", `{{ route('admin.select.unit') }}`, {
                id: "id",
                text: "name"
            });
            initSelect2Search("itemCategory-select-{{ $loop->iteration }}", `{{ route('admin.select.item-category') }}`, {
                id: "id",
                text: "nama"
            });
            initSelect2Search('itemType-select-{{ $loop->iteration }}', `{{ route('admin.select.item-type') }}`, {
                id: "nama",
                text: "nama",
            }, 0);
        </script>
    @endforeach

    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-item-sidebar');
        sidebarActive('#item');
    </script>
@endpush
