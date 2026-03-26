@extends('layouts.admin.layout.index')

@php
    $main = 'vendor-debt';
    $menu = 'saldo awal hutang';
@endphp

@section('title', Str::headline("import $menu") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.vendor.index') }}">{{ Str::headline('vendor') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('import ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form id="form-data" action="{{ route('admin.' . $main . '.preview') }}" method="post" enctype="multipart/form-data">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">{{ Str::headline('import ' . $menu) }}</h3>
            </div>
            <div class="box-body">
                @include('components.validate-error')
                @csrf
                <div class="row d-flex align-items-end">
                    <div class="col-md-12 mb-3">
                        <a href="{{ route('admin.' . $main . '.template') }}" target="_blank">
                            Download Format Import
                        </a>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" id="file" name="file" label="pilih file" required />
                        </div>
                    </div>
                    <div class="col mb-3">
                        <button class="btn btn-primary">Upload File</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="form-data" action="{{ route('admin.' . $main . '.import') }}" method="post">
        @csrf
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="nama_array" value="{{ $nama_array ?? '' }}">
                        <input type="hidden" name="alamat_array" value="{{ $alamat_array ?? '' }}">
                        <input type="hidden" name="npwp_array" value="{{ $npwp_array ?? '' }}">
                        <input type="hidden" name="email_array" value="{{ $email_array ?? '' }}">
                        <input type="hidden" name="mobile_phone_array" value="{{ $mobile_phone_array ?? '' }}">
                        <input type="hidden" name="business_phone_array" value="{{ $business_phone_array ?? '' }}">
                        <input type="hidden" name="whatsapp_array" value="{{ $whatsapp_array ?? '' }}">
                        <input type="hidden" name="fax_array" value="{{ $fax_array ?? '' }}">
                        <input type="hidden" name="term_of_payment_array" value="{{ $term_of_payment_array ?? '' }}">
                        <input type="hidden" name="top_days_array" value="{{ $top_days_array ?? '' }}">
                        <input type="hidden" name="account_payable_coa_array" value="{{ $account_payable_coa_array ?? '' }}">
                        <input type="hidden" name="purchase_discounts_coa_array" value="{{ $purchase_discounts_coa_array ?? '' }}">
                        <input type="hidden" name="vendor_deposite_coa_array" value="{{ $vendor_deposite_coa_array ?? '' }}">
                        <input type="hidden" name="invoice_code_array" value="{{ $invoice_code_array ?? '' }}">
                        <input type="hidden" name="currency_array" value="{{ $currency_array ?? '' }}">
                        <input type="hidden" name="exchange_rate_array" value="{{ $exchange_rate_array ?? '' }}">
                        <input type="hidden" name="accepted_doc_date_array" value="{{ $accepted_doc_date_array ?? '' }}">
                        <input type="hidden" name="date_array" value="{{ $date_array ?? '' }}">
                        <input type="hidden" name="due_date_array" value="{{ $due_date_array ?? '' }}">
                        <input type="hidden" name="tax_reference_array" value="{{ $tax_reference_array ?? '' }}">
                        <input type="hidden" name="invoice_amount_array" value="{{ $invoice_amount_array ?? '' }}">
                        <x-table>
                            <x-slot name="table_head">
                                <th>{{ Str::headline('nama') }}</th>
                                <th>{{ Str::headline('alamat') }}</th>
                                <th>{{ Str::headline('npwp') }}</th>
                                <th>{{ Str::headline('email') }}</th>
                                <th>{{ Str::headline('mobile_phone') }}</th>
                                <th>{{ Str::headline('business_phone') }}</th>
                                <th>{{ Str::headline('whatsapp') }}</th>
                                <th>{{ Str::headline('fax') }}</th>
                                <th>{{ Str::headline('term_of_payment') }}</th>
                                <th>{{ Str::headline('top_days') }}</th>
                                <th>{{ Str::headline('Account Payable COA') }}</th>
                                <th>{{ Str::headline('Purchase Discounts COA') }}</th>
                                <th>{{ Str::headline('Vendor Deposite COA') }}</th>
                                <th>{{ Str::headline('invoice_code') }}</th>
                                <th>{{ Str::headline('currency') }}</th>
                                <th>{{ Str::headline('exchange_rate') }}</th>
                                <th>{{ Str::headline('accepted_doc_date') }}</th>
                                <th>{{ Str::headline('date') }}</th>
                                <th>{{ Str::headline('due_date') }}</th>
                                <th>{{ Str::headline('tax_reference') }}</th>
                                <th>{{ Str::headline('invoice_amount') }}</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @forelse ($data as $d)
                                    <tr>
                                        <td>
                                            {{ $d['nama'] }}
                                            @if ($d['is_vendor_exists'])
                                                <span class="text-success">Terdaftar</span>
                                            @else
                                                <span class="text-danger">Tidak Terdaftar</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $d['alamat'] }}
                                        </td>
                                        <td>
                                            {{ $d['npwp'] }}
                                        </td>
                                        <td>
                                            {{ $d['email'] }}
                                        </td>
                                        <td>
                                            {{ $d['mobile_phone'] }}
                                        </td>
                                        <td>
                                            {{ $d['business_phone'] }}
                                        </td>
                                        <td>
                                            {{ $d['whatsapp'] }}
                                        </td>
                                        <td>
                                            {{ $d['fax'] }}
                                        </td>
                                        <td>
                                            {{ $d['term_of_payment'] }}
                                        </td>
                                        <td>
                                            {{ $d['top_days'] }}
                                        </td>
                                        <td>
                                            {{ $d['account_payable_coa'] }}
                                        </td>
                                        <td>
                                            {{ $d['purchase_discounts_coa'] }}
                                        </td>
                                        <td>
                                            {{ $d['vendor_deposite_coa'] }}
                                        </td>
                                        <td>
                                            {{ $d['invoice_code'] }}
                                        </td>
                                        <td>
                                            {{ $d['currency'] }}
                                        </td>
                                        <td>
                                            {{ $d['exchange_rate'] }}
                                        </td>
                                        <td>
                                            {{ $d['accepted_doc_date'] }}
                                        </td>
                                        <td>
                                            {{ $d['date'] }}
                                        </td>
                                        <td>
                                            {{ $d['due_date'] }}
                                        </td>
                                        <td>
                                            {{ $d['tax_reference'] }}
                                        </td>
                                        <td>
                                            <input type="text" class="form-control commas-form text-end" name="invoice_amount[]" value="{{ formatNumber($d['invoice_amount']) }}" readonly>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="20" class="text-center">
                                            Tidak ada data
                                        </td>
                                    </tr>
                                @endforelse
                            </x-slot>
                        </x-table>
                    </div>
                </div>
            </div>
            <div class="box-footer text-end">
                <a href="{{ route('admin.vendor.index') }}" class="btn btn-secondary">Cancel</a>
                <x-button type="submit" color="primary" label="Save data" />
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#master-vendor-sidebar');
        sidebarActive('#vendor-sidebar');
    </script>
@endsection
