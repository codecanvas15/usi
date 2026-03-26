<x-card-data-table title="{{ 'edit ' . $main }} service">
    <x-slot name="header_content">

    </x-slot>
    <x-slot name="table_content">
        @include('components.validate-error')
        @csrf
        @method('PUT')

        <div class="row mt-20">
            <div class="col-md-4">
                <div class="form-group">
                    <x-input class="datepicker-input" id="tanggal" name="tanggal" value="{{ localDate($model->tanggal) ?? \Carbon\Carbon::today()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" autofucus required />
                    <span class="text-primary">Default Today</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <x-select name="project_id" id="project-select" label="Project">
                        @if ($model->project)
                            <option value="{{ $model->project_id }}" selected>{{ $model->project->name }}</option>
                        @endif
                    </x-select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <x-input type="text" name="kode" label="kode" value="{{ $model->kode }}" id="" required readonly />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <x-select name="division_id" label="divisi" id="division_id" required>
                        @if ($model->division)
                            <option value="{{ $model->division_id }}">{{ $model->division->name }}</option>
                        @endif
                    </x-select>
                </div>
            </div>
        </div>

        <hr class="my-20">

        <div class="mb-10">
            <h5 class="fw-bold">{{ Str::headline($main) }} Item</h5>

            <div id="item-form">
                @foreach ($model->purchase_request_details as $key => $item)
                    <div id="item-form-{{ $loop->index + 1 }}">
                        <input type="hidden" name="purchase_request_detail_id[{{ $key }}]" value="{{ $item->id }}">
                        <div class="row mt-10">
                            <div class="col-md-3">
                                <div class="form-group">
                                    @if (is_null($item->item_id))
                                        <x-input type="text" id="item_id{{ $loop->index }}" value="{{ $item->item }}" name="item[{{ $key }}]" label="item" required />
                                    @else
                                        <x-select name="item_id[{{ $key }}]" id="item_id{{ $loop->index }}" label="Item" required>
                                            <option value="{{ $item->item_id }}" selected>{{ $item->item_data?->kode }} - {{ $item->item_data?->nama }}</option>
                                        </x-select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2">
                                @if (is_null($item->item_id))
                                    <x-select name="unit_id[{{ $key }}]" id="unit_id{{ $loop->index }}" label="Unit" required>
                                        <option value="{{ $item->unit_id }}" selected>{{ $item->unit->name }}</option>
                                    </x-select>
                                @else
                                    <x-select name="unit_id[{{ $key }}]" id="unit_id{{ $loop->index }}" label="Unit" required disabled>
                                        <option value="{{ $item->unit_id }}" selected>{{ $item->unit->name }}</option>
                                    </x-select>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" class="commas-form" id="jumlah" value="{{ (int) $item->jumlah }}" name="jumlah[{{ $key }}]" label="jumlah" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="file" class="commas-form" id="file" name="file[{{ $key }}]" label="file" />
                                </div>
                            </div>
                            <div class="col-md-1 row align-self-center">
                                <x-button type="" class="d-block" color="danger" icon="trash" fontawesome size="sm" id="delete-item-{{ $loop->index + 1 }}" />
                            </div>
                        </div>

                        <div class="row mt-10">
                            <div class="col-md-6">
                                <x-text-area name="keterangan_item[{{ $key }}]" label="keterangan" id="keterangan" cols="30" rows="10">{!! $item->keterangan !!}</x-text-area>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group"></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group"></div>
                </div>
                <div class="col-md-3">
                    <div class="form-group"></div>
                </div>
                <div class="col-md-3">
                    <div class="form-group"></div>
                </div>
                <div class="col-md-1 row align-self-center">
                    <x-button type="" color="info" icon="plus" fontawesome size="sm" id="add-item" />
                </div>
            </div>

            <div class="row mt-30">
                <div class="col-md-6">
                    <x-text-area name="keterangan" label="keterangan" id="keterangan" cols="30" rows="10" required>{!! $model->keterangan !!}</x-text-area>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <div class="float-end">
                <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                <x-button type="submit" color="primary" label="Save data" />
            </div>
        </div>

    </x-slot>

</x-card-data-table>

@push('script')
    <script>
        $(document).ready(function() {
            let item_count = '{{ $model->purchase_request_details->count() + 1 }}';
            initProjectSelect('#project-select')

            let purchase_details_delete_btns = [];
            @foreach ($model->purchase_request_details as $item)
                purchase_details_delete_btns.push($(`#delete-item-{{ $loop->index + 1 }}`));

                initSelect2Search(`unit_id{{ $loop->index }}`, `{{ route('admin.select.unit') }}`, {
                    id: "id",
                    text: "name"
                });

                inititemSelectForPurchasseRequest(`item_id{{ $loop->index }}`, 'service')
                $(`#item_id{{ $loop->index }}`).change(function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.item.item-unit') }}/${$(this).val()}`,
                        success: function({
                            data
                        }) {
                            let {
                                unit
                            } = data;

                            $(`#unit_id${index}`).val(unit.name);
                        }
                    });
                });
            @endforeach

            purchase_details_delete_btns.map((btn, index) => {
                btn.click(function() {
                    $(`#item-form-${btn.attr('id').split('-')[2]}`).remove();
                });
            });

            // purchase requst items ============================================================================================================
            const deleteItem = (index) => {
                $(`#item-form-${index}`).remove();
            };

            const addItem = (index) => {

                // with btn delete ot not
                let with_btn = ``;
                with_btn = `<x-button type="" color="danger" icon="trash" fontawesome size="sm" id="delete-item-${index}" />`;


                let html = `
                        <div class="mt-10" id="item-form-${index}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="item_id[${index}]" id="item_id${index}" label="Item" required >

                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <x-input type="text" id="unit_id${index}" name="jumlah[${index}]" label="unit" required disabled/>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" class="commas-form" id="jumlah" name="jumlah[${index}]" label="jumlah" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="file" id="file" name="file[${index}]" label="file" required />
                                    </div>
                                </div>
                                <div class="col-md-1 row align-self-center">
                                    ${with_btn}
                                </div>
                            </div>
                            <div class="row mt-10">
                                <div class="col-md-6">
                                    <x-text-area name="keterangan_item[${index}]" label="deskripsi barang" id="keterangan"></x-text-area>
                                </div>
                            </div>
                        </div>`;

                $('#item-form').append(html);

                inititemSelectForPurchasseRequest(`item_id${index}`, 'service')
                $(`#item_id${index}`).change(function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.item.item-unit') }}/${$(this).val()}`,
                        success: function({
                            data
                        }) {
                            let {
                                unit
                            } = data;

                            $(`#unit_id${index}`).val(unit.name);
                        }
                    });
                });

                initCommasForm()
                $(`#delete-item-${index}`).click(function(e) {
                    e.preventDefault();
                    deleteItem(index);
                });

                item_count++;
            };

            $('#add-item').click(function(e) {
                e.preventDefault();
                addItem(item_count)
            });

            // purchase requst items ============================================================================================================

            initSelect2Search(`division_id`, `{{ route('admin.select.division') }}`, {
                id: "id",
                text: "name"
            });

            initCommasForm()

        });
    </script>
@endpush
