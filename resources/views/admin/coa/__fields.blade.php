<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-3" id="parent-select">
            <x-select name="branch_id" id="branch_id" label="branch">
                @if ($model)
                    <option value="{{ $model->branch_id }}">{{ $model->branch?->name }}</option>
                @endif
            </x-select>
        </div>
        @if (!$model)
            <div class="col-md-3">
                <x-input-checkbox label="sub_account" name="check" id="sub_account" />
            </div>
        @else
            <div class="col-md-3" style="display:  {{ $model->parent_id ? 'block' : 'none' }}" id="parent-select">
                <x-select name="parent_id" id="parent_id" label="parent coa">
                    <option value="{{ $model->parent_id }}">{{ $model->parent?->account_code }} - {{ $model->parent?->name }}</option>
                </x-select>
            </div>
        @endif
    </div>
    <div class="row mt-20">
        @if (!$model)
            <div class="col-md-3" style="display:  none" id="parent-select">

            </div>
        @endif
        <div class="col-md-3" id="account_code_col">
            <div class="form-group" id="account_code_form">
                <x-input type="text" id="account_code" name="account_code" label="nomor coa" value="{{ $model->account_code ?? '' }}" autofucus />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="text" id="name" name="name" label="nama" value="{{ $model->name ?? '' }}" required autofucus />
            </div>
        </div>
        <div class="col-md-3" id="account_type_col">
            <x-select name="account_type" id="account_type" label="tipe akun" required>
                <option value="">Pilih</option>
                @foreach (get_coa_types() as $coa_types)
                    @foreach ($coa_types as $item)
                        <option value="{{ $item }}" {{ $model && $model->account_type == $item ? 'selected' : '' }}>{{ Str::headline($item) }}</option>
                    @endforeach
                @endforeach
            </x-select>
        </div>
        <div class="col-md-3" id="account-category-col">
            <div class="form-group">
                <x-input type="text" name="account_category" id="account-category" value="{{ $model->account_category ?? null }}" readonly />
            </div>
        </div>

        <div class="col-md-3">
            <x-select name="currency_id" id="currency_id" label="currency" required>
                @if ($model)
                    @if ($model->currency)
                        <option value="{{ $model->currency->id }}">{{ $model->currency->kode . ' - ' . $model->currency->negara }}</option>
                    @endif
                @endif
            </x-select>
        </div>
    </div>
    <div class="box-footer">
        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary mr-2" label="cancel" link="{{ url()->previous() }}" />
            <x-button type="submit" color="primary" label="Save data" />
        </div>
    </div>
</form>

@push('script')
    @html_script('js/form/select2search.js')
    <script>
        $(document).ready(function() {
            let coa_types = {
                'activa': [
                    'Cash & Bank',
                    'Receivable',
                    'Inventory',
                    'Other Current Asset',
                    'Fixed Asset',
                    'Accumulated Depreciation',
                    'Other Asset',
                ],
                'pasiva': [
                    'Payable',
                    'Other Current Liability',
                    'Long Term Liability',
                ],
                'equity': [
                    'Equity',
                ],
                'revenue': [
                    'Revenue',
                    'Other Income',
                ],
                'expense': [
                    'Cost Of Good Sold',
                    'Expense',
                    'Other Expense',
                ],
            };

            const initCoa = (target, route) => {
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
                    minimumInputLength: 0,
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
                                    text: `${data.account_code} - ${data.name}`,
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


            const account_code_form = `<div class="form-group">
                                        <x-input type="text" id="account_code" name="account_code" label="nomor coa" value="{{ $model->account_code ?? '' }}" required autofucus />
                                    </div>`;
            const account_type_form = `<x-select name="account_type" id="account_type" label="tipe akun" required>
                                            <option value="">Pilih</option>
                                            @foreach (get_coa_types() as $coa_types)
                                                @foreach ($coa_types as $item)
                                                    <option value="{{ $item }}" {{ $model && $model->account_type == $item ? 'selected' : '' }}>{{ Str::headline($item) }}</option>
                                                @endforeach
                                            @endforeach
                                        </x-select>`;
            const account_category_select = `<div class="form-group">
                                                <x-input type="text" name="account_category" id="account-category" readonly />
                                            </div>`;

            const account_type_select = `<div class="form-group">
                                                <x-input type="text" name="account_type" id="account_type" readonly />
                                            </div>`;

            $('#account_type').change(function(e) {
                e.preventDefault();
                let account_type = $(this).val();
                Object.keys(coa_types).map((objkey, index) => {
                    coa_types[objkey].map((coa_type) => {
                        if (coa_type == account_type) {
                            $('#account-category').val(objkey);
                        }
                    })
                })
            });

            @if (!$model)
                $('#sub_account').click(function(e) {
                    if (this.checked) {
                        $('#account_code_col').hide();
                        $('#account_code_form').remove();

                        $('#account_type_col').show();
                        $('#account_type_col').html(account_type_select);

                        $('#account-category-col').show();
                        $('#account-category-col').html(account_category_select);

                        $('#parent-select').show();
                        $('#parent-select').append(`<x-select name="parent_id" id="parent_id" label="parent">
                                                </x-select>`);

                        initCoa('parent_id', `{{ route('admin.coa.coa-parent') }}`);
                        $('#parent_id').change(function(e) {

                            $.ajax({
                                type: "get",
                                url: '{{ route('admin.coa.detail') }}/' + this.value,
                                success: function({
                                    data
                                }) {
                                    $('#account-category').val(data.account_category);
                                    $('#account_type').val(data.account_type);
                                    // $(`option[value=${data.account_type}]`).prop('selected');
                                }
                            });
                        });
                    } else {
                        $('#account_code_col').show();
                        $('#account_code_col').html(account_code_form);

                        $('#account_type_col').show();
                        $('#account_type_col').html(account_type_form);

                        $('#account_type').change(function(e) {
                            e.preventDefault();
                            let account_type = $(this).val();
                            Object.keys(coa_types).map((objkey, index) => {
                                coa_types[objkey].map((coa_type) => {
                                    if (coa_type == account_type) {
                                        $('#account-category').val(objkey);
                                    }
                                })
                            })
                        });

                        $('#account-category-col').show();
                        $('#account-category-col').html(account_category_select);

                        $('#parent-select').hide();
                        $('#parent-select').html('');

                        $('.select2').select2();
                    }
                });
            @else
                initSelect2Search('parent_id', `{{ route('admin.coa.coa-parent') }}`, {
                    id: "id",
                    text: "name"
                });
                $('#parent_id').change(function(e) {

                    $.ajax({
                        type: "get",
                        url: '{{ route('admin.coa.detail') }}/' + this.value,
                        success: function({
                            data
                        }) {
                            $('#account_type').val(data.account_type);
                        }
                    });
                });
            @endif

        });

        initSelect2Search(`currency_id`, `{{ route('admin.select.currency') }}`, {
            id: "id",
            text: "kode,negara"
        });

        initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
            id: "id",
            text: "name"
        });
    </script>
@endpush
