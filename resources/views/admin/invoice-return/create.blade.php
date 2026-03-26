@extends('layouts.admin.layout.index')

@php
    $main = 'invoice-return';
    $title = 'retur penjualan';
@endphp

@section('title', Str::headline("tambah $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        Retur/Adjustment
                    </li>
                    <li class="breadcrumb-item">
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
    <form action="{{ route("admin.$main.store") }}" method="post" id="form-data">
        @csrf
        <x-card-data-table title="{{ 'tambah ' . $title }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="{{ route('admin.' . $main . '.create') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="branch_id" id="branch_id" label="branch" required>
                                    <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="tanggal" name="date" id="date" class="datepicker-input" required value="{{ date('d-m-Y') }}" onchange="checkClosingPeriod($(this))" />
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="customer_id" id="customer_id" label="customer" required onchange="resetForm()">
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="reference_parent_id" id="reference_parent_id" label="DO" required onchange="getDODetail($(this))">
                                </x-select>
                                <input type="hidden" name="type" id="type">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="tanggal DO" name="delivery_date" id="delivery_date" class="datepicker-input" readonly />
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="currency_id" id="currency_id" label="currency" required>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="kurs" name="exchange_rate" id="exchange_rate" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="nomor faktur pajak" name="tax_number" id="tax_number" onblur="check_unique_tax_number('')" />
                                <small class="text-danger">* jika terdapat pajak nomor faktur wajib diisi!</small>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="project_id" id="project_id" label="project"></x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="note" name="referensi" id="referensi" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="ware_house_id" id="ware_house_id" label="gudang" required>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="bg-info">
                                        <tr>
                                            <th>{{ Str::headline('item') }}</th>
                                            <th>{{ Str::headline('unit') }}</th>
                                            <th>{{ Str::headline('qty DO') }}</th>
                                            <th class="text-end">{{ Str::headline('qty') }}</th>
                                            <th class="text-end"></span> {{ Str::headline('price') }} <span class="currency_kode"></th>
                                            <th class="text-end"></span> {{ Str::headline('subtotal') }} <span class="currency_kode"></th>
                                            <th class="text-end"></span> {{ Str::headline('pajak') }} <span class="currency_kode"></th>
                                            <th class="text-end"></span> {{ Str::headline('total') }} <span class="currency_kode"></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="return-data">

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4"></th>
                                            <th class="text-end">TOTAL</th>
                                            <th class="text-end" id="grand_subtotal_text"></th>
                                            <th class="text-end" id="grand_tax_amount_text"></th>
                                            <th class="text-end" id="grand_total_text"></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
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
    <script src="{{ asset('js/admin/invoice-return/transaction.js') }}?v=1"></script>

    <script>
        $('body').addClass('sidebar-collapse');
        checkClosingPeriod($('#date'));
        sidebarMenuOpen('#trading');
        sidebarActive('#invoice-return')

        $(document).ready(function() {
            initSelect2Search(`project_id`, `{{ route('admin.select.project') }}`, {
                id: "id",
                text: "code,name"
            }, 2, {
                branch_id: function() {
                    return $('#branch_id').val();
                }
            });

            initSelect2Search(`customer_id`, `{{ route('admin.select.customer') }}`, {
                id: "id",
                text: "nama"
            });

            initSelect2Search(`reference_parent_id`, `{{ route('admin.select.invoice-return-do-select') }}`, {
                id: "id",
                text: "code",
            }, 0, {
                branch_id: function() {
                    return $('#branch_id').val()
                },
                customer_id: function() {
                    return $('#customer_id').val()
                }
            });
        })
    </script>
    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif
@endsection
