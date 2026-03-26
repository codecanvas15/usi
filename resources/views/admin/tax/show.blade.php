@extends('layouts.admin.layout.index')

@php
    $main = 'tax';
    $title = 'pajak';
@endphp

@section('title', Str::headline("Detail $title") . ' - ')

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
                        {{ Str::headline('Detail ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="{{ 'detail ' . $title }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('name') }}</label>
                            <p>
                                {{ $model->name }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('value') }}</label>
                            <p>
                                {{ $model->value }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('coa_sale') }}</label>
                            <p class="{{ $model->coa_sale_data->deleted_at != null ? 'text-danger' : '' }}">
                                {{ $model->coa_sale_data->name }} - {{ $model->coa_sale_data->account_code }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('coa purchase') }}</label>
                            <p class="{{ $model->coa_purchase_data->deleted_at != null ? 'text-danger' : '' }}">
                                {{ $model->coa_purchase_data->name }} - {{ $model->coa_purchase_data->account_code }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('Calculate After Discount') }}</label>
                            <p>
                                {{ $model->is_discount == '1' ? 'Yes' : 'No' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('created_at') }}</label>
                            <p>
                                {{ toDayDateTimeString($model->created_at) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('last medified') }}</label>
                            <p>
                                {{ toDayDateTimeString($model->updated_at) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('tipe') }}</label>
                            <p>
                                {{ Str::headline($model->type) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('Default') }}</label>
                            <p>
                                {{ $model->is_default == '1' ? 'Yes' : 'No' }}
                            </p>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-1">
                    <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                    @can("edit $main")
                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                    @endcan

                    @can("delete $main")
                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                    @endcan
                </div>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-price-sidebar');
        sidebarActive('#tax')
    </script>
@endsection
