<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="nama" name="nama" label="nama" value="{{ $model->nama ?? '' }}" required readonly />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="alamat" name="alamat" label="alamat" value="{{ $model->alamat ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="npwp" class="npwp-form-input" name="npwp" label="npwp" value="{{ $model->npwp ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="mobile_phone" name="mobile_phone" label="mobile_phone" value="{{ $model->mobile_phone ?? '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="bussiness_phone" name="bussiness_phone" label="bussiness_phone" value="{{ $model->bussiness_phone ?? '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="whatsapp_number" name="whatsapp_number" label="whatsapp_number" value="{{ $model->whatsapp_number ?? '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="fax" name="fax" label="fax" value="{{ $model->fax ?? '' }}" />
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
                    <option value="{{ $item }}" {{ $model && $model->lost_tolerance_type == $item ? 'selected' : '' }}>{{ Str::headline($item) }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="col-md-4">
            <x-input type="text" class="commas-form" name="lost_tolerance" label="lost_tolerance" value="{{ $model->lost_tolerance_type == 'percent' ? $model->lost_tolerance * 100 : $model->lost_tolerance }}" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-select name="customer_bank_id[]" label="customer-bank" id="customer-bank-id" multiple>
                    @foreach ($model->customer_banks as $item)
                        <option value="{{ $item->bank_internal_id }}" selected>{{ $item->bank_internal?->nama_bank }} - {{ $item->bank_internal?->no_rekening }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>
    </div>

    <div class="row mt-30">
        <div class="col-md-4">
            <div class="form-group">
                <x-select name="term_of_payment" label="term of payment" id="term-of-payment">
                    <option value="" selected>- Pilih term of payment -</option>
                    <option value="cash" {{ $model->term_of_payment == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="by days" {{ $model->term_of_payment == 'by days' ? 'selected' : '' }}>By Days</option>
                </x-select>
            </div>
        </div>
        <div id="daysSection" class="col-md-4">
            <div class="form-group">
                <label for="topDays" class="mb-2">Days <span class="text-primary">*</span></label>
                <input type="number" class="form-control" name="top_days" id="topDays" value="{{ $model->top_days }}" />
            </div>
        </div>
    </div>

    <div class="box-footer">
        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
            <x-button type="submit" color="primary" label="Save data" />
        </div>
    </div>
</form>

@push('script')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $(document).ready(function() {
            const initSelect2SearchBankInternal = (target, route, selector, min_char = 0) => {
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
                                let return_text = "";
                                let split_text = selector.text.split(",");
                                return {
                                    id: data[selector.id],
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

            initSelect2SearchBankInternal('customer-bank-id', "{{ route('admin.select.bank-internal') }}", {
                id: "id",
                text: "nama_bank,no_rekening",
            });

            $('#term-of-payment').change(function() {
                if ($(this).val() == 'by days') {
                    $('#daysSection').show();
                } else {
                    $('#daysSection').hide();
                }
            });
        });
    </script>
@endpush
