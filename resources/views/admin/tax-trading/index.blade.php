@extends('layouts.admin.layout.index')

@php
    $main = 'tax-trading';
@endphp

@section('title', Str::headline($main) . ' - ')

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
                    <li class="breadcrumb-item">
                        {{ Str::headline($main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <div class="row">
            <div class="col-md-6">
                <x-card-data-table title="{{ $main }}">

                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nama') }}</label>
                                    <p>{{ $model->name }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nilai') }}</label>
                                    <p>{{ $model->value * 100 }} %</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('coa sale') }}</label>
                                    <p class="{{ $model->coa_sale?->deleted_at != null ? 'text-danger' : '' }}">{{ $model->coa_sale?->account_code }} - {{ $model->coa_sale?->name }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('coa purchase') }}</label>
                                    <p class="{{ $model->coa_purchase?->deleted_at != null ? 'text-danger' : '' }}">{{ $model->coa_purchase?->account_code }} - {{ $model->coa_purchase?->name }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('terakhir dirubah') }}</label>
                                    <p>{{ toDayDateTimeString($model->updated_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot name="footer">
                        @can("edit $main")
                            <div class="d-flex justify-content-end gap-3">
                                <x-button color="warning" icon="edit" fontawesome size="sm" id="edit-tax-trading" />
                            </div>
                        @endcan
                    </x-slot>

                </x-card-data-table>
            </div>

            @can("edit $main")
                <div class="col-md-6" id="tax-trading-edit-form" style="display:  none">
                    <form action="{{ route('admin.tax-trading.update', $model) }}" method="post">
                        <x-card-data-table title='{{ "Edit $main" }}'>

                            <x-slot name="table_content">
                                @csrf
                                @method('PUT')

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <x-input type="text" name="name" label="nama" value="{{ $model->name }}" id="" required />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <x-input type="text" name="value" label="nilai" value="{{ $model->value * 100 }}" id="" helpers="Percent" required />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <x-select name="coa_sale_id" label="coa sale" id="coa-select-sale" required>
                                            @if ($model->coa_sale_id)
                                                <option value="{{ $model->coa_sale_id }}" selected>{{ $model->coa_sale?->account_code }} - {{ $model->coa_sale?->name }}</option>
                                            @endif
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <x-select name="coa_purchase_id" label="coa purchase" id="coa-select-purchase" required>
                                            @if ($model->coa_purchase_id)
                                                <option value="{{ $model->coa_purchase_id }}" selected>{{ $model->coa_purchase?->account_code }} - {{ $model->coa_purchase?->name }}</option>
                                            @endif
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <x-select name="type" id="type" label="tipe" required>
                                            <option value="non_ppn" @if (($model->type ?? '') == 'non_ppn') selected @endif>Non PPN</option>
                                            <option value="ppn" @if (($model->type ?? '') == 'ppn') selected @endif>PPN</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="d-flex col-md-12 align-self-end">
                                    <div class="form-group">
                                        @if ($model && $model->is_show_percent)
                                            <x-input-checkbox name="is_show_percent" label="Tampilkan Persentase" value="{{ $model && $model->is_show_percent ? 1 : 0 }}" checked />
                                        @else
                                            <x-input-checkbox name="is_show_percent" label="Tampilkan Persentase" value="" />
                                        @endif
                                    </div>
                                </div>
                            </x-slot>

                            <x-slot name="footer">
                                <div class="d-flex justify-content-end gap-3">
                                    <x-button color="secondary" icon="x" fontawesome size="sm" id="cancel-edit-tax-trading" />
                                    <x-button type="submit" color="primary" icon="save" fontawesome size="sm" />
                                </div>
                            </x-slot>

                        </x-card-data-table>
                    </form>
                </div>
            @endcan
        </div>
    @endcan
@endsection

@section('js')
    @can("edit $main")
        <script src="{{ asset('js/admin/select/coa.js') }}"></script>
        <script>
            $('#edit-tax-trading').click(function(e) {
                e.preventDefault();

                $('#tax-trading-edit-form').fadeIn(500);

                initCoaSelect('#coa-select-sale')
                initCoaSelect('#coa-select-purchase')
            });

            $('#cancel-edit-tax-trading').click(function(e) {
                e.preventDefault();
                $('#tax-trading-edit-form').fadeOut(500);
            });
        </script>
    @endcan

    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-price-sidebar');
        sidebarActive('#tax-trading');
    </script>
@endsection
