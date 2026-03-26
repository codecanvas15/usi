<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="nama" name="nama" value="{{ $model->nama ?? '' }}" required autofucus readonly />
            </div>
        </div>
        <div class="col-md-8">
            <div class="row">
                @if (count($model->item_type_coas) != 0)
                    @foreach ($model->item_type_coas as $item)
                        <div class="col-md-6">
                            <x-select name="coa_id[]" id="{{ Str::snake($item->type) }}" label="coa {{ $item->type }}" required>
                                @if ($item->coa)
                                    <option value="{{ $item->coa_id }}">{{ $item->coa->account_code }} - {{ $item->coa->name }}</option>
                                @endif
                            </x-select>
                            <input type="hidden" name="type[]" value="{{ $item->type }}">
                        </div>
                    @endforeach
                @else
                    @foreach (item_type_coas()[$model->nama] as $item)
                        <div class="col-md-6">
                            <x-select name="coa_id[]" id="{{ Str::snake($item) }}" label="coa {{ $item }}" required>

                            </x-select>
                            <input type="hidden" name="type[]" value="{{ $item }}">
                        </div>
                    @endforeach
                @endif
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
    <script>
        $(document).ready(function() {
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
                            result["page_limit"] = 10;
                            result["page"] = params.page;
                            result[target] = target_value;
                            return result;
                        },
                        processResults: (data, params) => {
                            params.page = params.page || 1;
                            let final_data = data.data.data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: `${data.name} - ${data.account_code}`,
                                };
                            });
                            return {
                                results: final_data,
                                pagination: {
                                    more: params.page * 10 < data.data.total,
                                }
                            };
                        },
                        cache: true,
                    },
                };

                $(`#${target}`).select2(itemSelect);
                return;
            };


            @if (count($model->item_type_coas) != 0)
                @foreach ($model->item_type_coas as $item)
                    initSelect2SearchCoa('{{ Str::snake($item->type) }}', "{{ route('admin.select.coa') }}", 0);
                @endforeach
            @else
                @foreach (item_type_coas()[$model->nama] as $item)
                    initSelect2SearchCoa('{{ Str::snake($item) }}', "{{ route('admin.select.coa') }}", 0);
                @endforeach
            @endif
        });
    </script>
@endpush
