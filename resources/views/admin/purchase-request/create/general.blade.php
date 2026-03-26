<x-card-data-table title="{{ 'tambah ' . $main . ' General' }}">
    <x-slot name="header_content">
    </x-slot>
    <x-slot name="table_content">
        @include('components.validate-error')
        @csrf

        <input type="hidden" name="type" value="general">

        <div class="row mt-20">
            <div class="col-md-4">
                <div class="form-group">
                    <x-input class="datepicker-input" id="tanggal" name="tanggal" value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" autofucus required />
                    <span class="text-primary">Default Today</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <x-select name="project_id" id="project-select" label="Project">

                    </x-select>
                </div>
            </div>
        </div>

        <div class="row">
            @php
                $last_purchase_request = \App\Models\PurchaseRequest::where('branch_id', get_current_branch_id())
                    // ->where('type', $model->type)
                    ->whereMonth('created_at', date('m'))
                    ->orderBy('id', 'desc')
                    ->first();

                if ($last_purchase_request) {
                    $kode = generate_code_purchase_request($last_purchase_request->kode);
                } else {
                    $kode = generate_code_purchase_request('0000/0000/00/0000');
                }
            @endphp
            <div class="col-md-4">
                <div class="form-group">
                    <x-input type="text" name="kode" label="kode" value="{{ $kode }}" readonly required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <x-select name="division_id" label="divisi" id="division_id" required>
                        <option value="">Pilih Divisi</option>
                    </x-select>
                </div>
            </div>
        </div>

        <hr class="my-20">

        <div class="mb-10">
            <h5 class="fw-bold">{{ Str::headline($main) }} Item</h5>

            <div id="item-form">

            </div>

            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-2"></div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
                <div class="col-md-1 row align-self-center">
                    <x-button type="" color="info" icon="plus" fontawesome size="sm" id="add-item" />
                </div>
            </div>

            <div class="row mt-30">
                <div class="col-md-6">
                    <x-text-area name="keterangan" label="keterangan" id="keterangan" cols="30" rows="10" required></x-text-area>
                </div>
            </div>
        </div>
        <div class="box-footer" id="position_btn_submit">
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
            checkClosingPeriod($('#tanggal'))
            // root var ============================================================================================================
            let item_count = 0;
            // root var ============================================================================================================

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

                initSelect2SearchPaginationData(`item_id${index}`, `{{ route('admin.select.item.type') }}/general`, {
                    id: 'id',
                    text: 'nama,kode'
                })

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

                if (index == 0) {
                    $('#add-item').click(function(e) {
                        e.preventDefault();
                        addItem(item_count)
                    });
                }

                initCommasForm()
                $(`#delete-item-${index}`).click(function(e) {
                    e.preventDefault();
                    deleteItem(index);
                });

                item_count++;
            };


            addItem(item_count);
            // purchase requst items ============================================================================================================

            initSelect2SearchPaginationData(`division_id`, `{{ route('admin.select.division') }}`, {
                id: "id",
                text: "name"
            });
        });
    </script>
@endpush
