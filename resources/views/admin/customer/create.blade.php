@extends('layouts.admin.layout.index')

@php
    $main = 'customer';
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
    <form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
        <x-card-data-table title="{{ 'create ' . $main }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                @csrf
                @if ($model)
                    @method('PUT')
                @endif
                @can("create $main")
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="nama" name="nama" label="nama" value="{{ $model->nama ?? '' }}" required autofucus />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="alamat" name="alamat" label="alamat" value="{{ $model->alamat ?? '' }}" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="npwp" class="npwp-form-input" name="npwp" label="npwp" value="{{ $model->npwp ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="number" id="mobile_phone" name="mobile_phone" label="mobile_phone" value="{{ $model->mobile_phone ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="number" id="bussiness_phone" name="bussiness_phone" label="bussiness_phone" value="{{ $model->bussiness_phone ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="number" id="whatsapp_number" name="whatsapp_number" label="whatsapp_number" value="{{ $model->whatsapp_number ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="number" id="fax" name="fax" label="fax" value="{{ $model->fax ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="email" id="email" name="email" label="email" value="{{ $model->email ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="url" id="website" name="website" label="website" value="{{ $model->website ?? '' }}" />
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

                    <div class="row mt-30">
                        <div class="col-md-4">
                            <x-select name="lost_tolerance_type" id="">
                                <option value="">Pilih Item</option>
                                @foreach (lost_tolerance_types() as $item)
                                    <option value="{{ $item }}">{{ Str::headline($item) }}</option>
                                @endforeach
                            </x-select>
                        </div>
                        <div class="col-md-4">
                            <x-input type="text" class="commas-form" name="lost_tolerance" label="lost_tolerance" />
                        </div>
                    </div>
                @endcan
                @can("create $main-coa")
                    <div class="row mt-30">
                        @foreach (customer_coa_types() as $item)
                            @php
                                $default_coa = \App\Models\DefaultCoa::where('type', 'customer')
                                    ->where('name', $item)
                                    ->first();
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

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select name="customer_bank_id[]" label="customer-bank" id="customer-bank-id" multiple>

                            </x-select>
                        </div>
                    </div>
                </div>

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
                            <label for="topDays" class="mb-2">Days <span class="text-primary">*</span></label>
                            <input type="number" class="form-control" name="top_days" id="topDays" />
                        </div>
                    </div>
                </div>

            </x-slot>
            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-3">
                    <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                    @canany("create $main", "create $main-coa")
                        <x-button type="submit" color="primary" label="Save data" />
                    @endcanany
                </div>
            </x-slot>

        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#customer-sidebar')
        sidebarActive('#customer')
        $(document).ready(function() {
            @can("create $main-coa")
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

                @foreach (customer_coa_types() as $item)
                    initSelect2SearchCoa('{{ Str::snake($item) }}', "{{ route('admin.select.coa') }}", 0);
                @endforeach
            @endcan

            initNpwpInputForm();

            const initSelect2SearchBankInternal = (target, route, min_char = 0) => {
                let selected_item = [];

                $(`select[id="#${target}"]`)
                    .toArray()
                    .map(function() {
                        if ($(this).val() != null) {
                            selected_item.push($(this).val());
                        }
                    });

                let target_value = $(`#${target}`).val();

                var itemSelect = {
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
                        data: ({
                            term
                        }) => {
                            let result = {};
                            result["search"] = term;
                            result["selected_item"] = selected_item;
                            result[target] = target_value;
                            return result;
                        },
                        processResults: ({
                            data
                        }) => {
                            let final_data = data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: `${data.nama_bank} - ${data.no_rekening}`,
                                };
                            });
                            return {
                                results: final_data,
                            };
                        },
                        cache: true,
                    },
                };

                $(`#${target}`).select2(itemSelect);
                return;
            };
            initSelect2SearchBankInternal('customer-bank-id', "{{ route('admin.select.bank-internal') }}");

            $('#term-of-payment').change(function() {
                if ($(this).val() == 'by days') {
                    $('#daysSection').show();
                } else {
                    $('#daysSection').hide();
                }
            });
        });
    </script>
@endsection
