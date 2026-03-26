@extends('layouts.admin.layout.index')

@php
    $main = 'supplier-invoice';
    $title = 'Purchase Invoice (LPB)';
@endphp

@section('title', Str::headline($title) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
    <style>
        .form-group {
            margin-bottom: 0px !important;
        }
    </style>
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
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ $title }}">
        <x-slot name="header_content">
            <div class="row mb-4">
                @if (get_current_branch()->is_primary)
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-select id="branch-select" label="branch">

                            </x-select>
                        </div>
                    </div>
                @endif
                <div class="col-md-2">
                    <div class="form-group">
                        <x-select id="vendor-select" label="vendor">

                        </x-select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" id="from_date" name="from_date" label="tanggal awal" value="" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" id="to_date" name="to" label="tanggal akhir" value="" required />
                    </div>
                </div>
                <div class="col-md-3 col-md-6 col-xl-4 align-self-end">
                    <x-button type="button" color="info" icon="search" fontawesome onclick="table.ajax.reload()" />
                    <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                    <x-button color="dark" fontawesome icon="print" label="" id="button_print" class="d-none" onclick="printReceipt(event)" link="{{ url('/') }}" />
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <x-table>
                <x-slot name="table_head">
                    <th></th>
                    <th>#</th>
                    <th></th>
                    <th>{{ Str::headline('Kode') }}</th>
                    <th>{{ Str::headline('No Faktur') }}</th>
                    <th>{{ Str::headline('Vendor') }}</th>
                    <th>{{ Str::headline('Tgl. Dokumen') }}</th>
                    <th>{{ Str::headline('Tgl. Diterima') }}</th>
                    <th>{{ Str::headline('Jatuh Tempo') }}</th>
                    <th>{{ Str::headline('Total') }}</th>
                    <th>{{ Str::headline('Status Approval') }}</th>
                    <th>{{ Str::headline('Status Pembayaran') }}</th>
                    <th>{{ Str::headline('Aksi') }}</th>
                </x-slot>
                <x-slot name="table_body"></x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');
        initSelect2Search('branch-select', '{{ route('admin.select.branch') }}', {
            'id': 'id',
            'text': 'name'
        });

        initSelect2Search('vendor-select', '{{ route('admin.select.vendor') }}', {
            'id': 'id',
            'text': 'nama'
        });

        const table = $('table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            destroy: true,
            ajax: {
                url: '{{ route("admin.$main.index") }}',
                data: {
                    from_date: function() {
                        return $("#from_date").val();
                    },
                    to_date: function() {
                        return $("#to_date").val();
                    },
                    branch_id: function() {
                        return $("#branch-select").val();
                    },
                    vendor_id: function() {
                        return $("#vendor-select").val();
                    },
                },
            },
            order: [
                [0, 'desc']
            ],
            columns: [{
                    data: 'created_at',
                    name: 'created_at',
                    visible: false,
                    searchable: false,
                },
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    "data": "checkbox",
                    "name": "checkbox",
                    orderable: false,
                    searchable: false
                },
                {
                    "data": "code",
                },
                {
                    "data": "tax_reference",
                },
                {
                    "data": "vendor",
                    "name": "vendors.nama"
                },
                {
                    "data": "date",
                },
                {
                    "data": "accepted_doc_date",
                },
                {
                    "data": "top",
                },
                {
                    "data": "grand_total",
                },
                {
                    "data": "status",
                },
                {
                    "data": "payment_status",
                },
                {
                    "data": "action",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        table.on('draw', function() {
            $('input[type="checkbox"]').css('position', 'unset').css('left', '0').css('opacity', 1);
        });

        $('table').css('width', '100%');

        const checkSelectedSupplierInvoice = () => {
            let supplier_invoices_id = $('input[name="select_supplier_invoice_id[]"]:checked').length;

            if (supplier_invoices_id > 0) {
                $('#button_print').removeClass('d-none');
            } else {
                $('#button_print').addClass('d-none');
            }
        }

        const printReceipt = (event) => {
            event.preventDefault();

            var supplier_invoices_id = $('input[name="select_supplier_invoice_id[]"]');
            var selected_supplier_invoice_id = [];

            supplier_invoices_id.each(function(e) {
                if ($(this).is(":checked")) {
                    selected_supplier_invoice_id.push($(this).val());
                }
            });

            let url = "{{ route('admin.supplier-invoice.print-receipt') }}?vendor_id=" + $('#vendor-select').val() + "&selected_id=" + selected_supplier_invoice_id;
            $('#button_print').attr('href', url);
            show_print_out_modal(event, '&');
        }

        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#supplier-invoice-sidebar');
        sidebarActive('#supplier-invoice');
    </script>
@endsection
