@extends('layouts.admin.layout.index')

@php
    $main = 'vendor';
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
                    <li class="breadcrumb-item active">
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
    @can("create $main")

        <form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
            @csrf
            @if ($model)
                @method('PUT')
            @endif

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Create Vendor</h3>
                </div>
                <div class="box-body">
                    @include('components.validate-error')
                    <div class="tab-content">
                        <div class="tab-pane active" id="general-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="nama" name="nama" label="nama" value="{{ $model->nama ?? '' }}" required autofucus />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="email" name="email" label="email" value="{{ $model->email ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="alamat" name="alamat" label="alamat" value="{{ $model->alamat ?? '' }}" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="npwp" name="npwp" label="npwp" value="{{ $model->npwp ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="business_phone" name="business_phone" label="phone bisnis" value="{{ $model->business_phone ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="mobile_phone" name="mobile_phone" label="Nomor HP" value="{{ $model->mobile_phone ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="whatsapp" name="whatsapp" label="Nomor WhatsApp" value="{{ $model->whatsapp ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="fax" name="fax" label="Fax" value="{{ $model->fax ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="website" name="website" label="website" value="{{ $model->website ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-select name="business_field_id" id="business_field_id" label="bidang usaha" value="" required>
                                            <option value="">Pilih Item</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-select name="type" id="">
                                            <option value="">Pilih Item</option>
                                            @foreach (customerTypes() as $item)
                                                <option value="{{ $item }}" {{ $model && $model->type == $item ? 'selected' : '' }}>{{ Str::headline($item) }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                            </div>

                            <div class="my-10 py-10 border-top border-bottom border-primary" id="bank-row">

                            </div>

                            @can('create vendor-coa')
                                <div class="row mt-30">
                                    @foreach (vendor_coa_types() as $item)
                                        @php
                                            $default_coa = \App\Models\DefaultCoa::where('type', 'vendor')->where('name', $item)->first();
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-select name="{{ Str::snake($item) }}" id="{{ Str::snake($item) }}" label="{{ Str::snake($item) }}">
                                                    {{-- @if ($default_coa)
                                                        <option value="{{ $default_coa->coa_id }}">{{ $default_coa->coa?->account_code }} - {{ $default_coa->coa?->name }}</option>
                                                    @endif --}}
                                                </x-select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endcan

                            <div class="row mt-30">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-select name="term_of_payment" label="term of payment" id="term-of-payment">
                                            <option value="" selected>- Pilih term of payment -</option>
                                            <option value="cash">Cash</option>
                                            <option value="by days">By Days</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div id="daysSection" class="col-md-4" style="display: none">
                                    <div class="form-group">
                                        <label for="topDays" class="mb-2">Days <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="top_days" id="topDays" value="0" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" class="commas-form" id="loss_tolerance" name="loss_tolerance" label="loss tolerance (%)" value="{{ $model->loss_tolerance ?? '' }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </div>
            </div>
        </form>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-vendor-sidebar');
        sidebarActive('#vendor-sidebar');
    </script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $(document).ready(function() {
            initSelect2Search('business_field_id', "{{ route('admin.select.business-field') }}", {
                id: "id",
                text: "name"
            });
        })
    </script>
    @can('create vendor-coa')
        <script>
            const initSelect2SearchCoa = (target, route, min_char = 3) => {
                let selected_item = [];

                $(`select[name="#${target}"]`)
                    .toArray()
                    .map(function() {
                        if ($(this).val() != null) {
                            selected_item.push($(this).val());
                        }
                    });

                let target_value = $(`#${target}`).val();

                var itemSelect = {
                    width: '100%',
                    placeholder: "Pilih Data",
                    minimumInputLength: min_char,
                    allowClear: true,
                    language: {
                        inputTooShort: () => {
                            return "Insert at least 3 characters";
                        },
                        noResults: () => {
                            return "Data can't be found";
                        },
                    },
                    ajax: {
                        url: route,
                        dataType: "json",
                        delay: 250,
                        type: "get",
                        data: (params) => {
                            let result = {};
                            result["search"] = params.term;
                            result["selected_item"] = selected_item;
                            result['page_limit'] = 10;
                            result['page'] = params.page;
                            result[target] = target_value;
                            return result;
                        },
                        processResults: (data, params) => {
                            params.page = params.page || 1;
                            let final_data = data.data.data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: `${data.account_code} - ${data.name} `,
                                };
                            });
                            return {
                                results: final_data,
                                pagination: {
                                    more: (params.page * 10) < data.data.total
                                }
                            };
                        },
                        cache: true,
                    },
                };

                $(`#${target}`).select2(itemSelect);
                return;
            };

            @foreach (vendor_coa_types() as $item)
                initSelect2SearchCoa('{{ Str::snake($item) }}', "{{ route('admin.select.coa') }}", 0);
            @endforeach

            $('#term-of-payment').change(function() {
                if ($(this).val() == 'by days') {
                    $('#daysSection').show();
                } else {
                    $('#daysSection').hide();
                }
            });

            const initializeVendorBanks = () => {
                let index_bank = 0;

                const addRow = (bank_index) => {
                    // if (index_bank <= 1) {}
                    index_bank++;
                    let btn = '';

                    if (bank_index == 0) {
                        btn = `<x-button color="primary" icon="plus" fontawesome size="sm" id="add-bank-btn"/>`;
                    } else {
                        btn = `<x-button color="danger" icon="trash" fontawesome size="sm" id="remove-bank-btn-${bank_index}"/>`;
                    }

                    let html = `<div class="row mb-5" id="bank-row-form-${bank_index}">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-input type="text" name="bank_name[]" label="nama bank" id="" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-input type="text" name="bank_account_number[]" label="nomor rekening" id="" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <x-input type="text" name="bank_behalf_of[]" label="atas nama" id="" required />
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex align-self-end">
                                                <div class="form-group">
                                                    ${btn}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;

                    $('#bank-row').append(html);

                    if (bank_index == 0) {

                        $('#add-bank-btn').click(function(e) {
                            e.preventDefault();
                            addRow();
                        });

                    } else {
                        // $('#add-bank-btn').prop('disabled', true);
                        $(`#remove-bank-btn-${bank_index}`).click(function(e) {
                            e.preventDefault();
                            removeRow(bank_index);
                        });
                    }
                };

                const removeRow = (bank_index) => {
                    $(`#bank-row-form-${bank_index}`).remove();
                    index_bank--;
                    $('#add-bank-btn').prop('disabled', false);
                };

                addRow(index_bank);
            };

            initializeVendorBanks();
        </script>
    @endcan
@endsection
