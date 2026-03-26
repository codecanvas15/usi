    @php
        $can_fill_hpp = auth()->user()->can('edit-hpp stock-adjustment') ? '' : 'readonly';
    @endphp

    <form id="form-data" action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
        @csrf
        @if ($model)
            @method('PUT')
        @endif
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <x-input class="datepicker-input" id="date" name="date" label="Tanggal" value="{{ $model ? localDate($model->date) : localDate($now) }}" onchange="checkClosingPeriod($(this))" required autofucus />
                </div>
            </div>
            <div class="col-md-3">
                <x-select name="ware_house_id" id="ware_house_id" label="Gudang" onchange="setAllInput()" required>
                    @if ($model && $model->ware_house_id)
                        <option value="{{ $model->ware_house_id }}" selected>{{ $model->warehouse->nama }}</option>
                    @endif
                </x-select>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <x-select name="coa_id" label="coa" id="coa-selectForm" required>
                        @if ($model && $model->coa_id)
                            <option value="{{ $model->coa_id }}" selected>{{ $model->coa->account_code }} - {{ $model->coa->name }}</option>
                        @endif
                    </x-select>
                </div>
            </div>
            <div class="col-md-12 my-20">
                <div id="input">
                    @if (!$model)
                        <div class="row" data-index="0">
                            <div class="col-md-2">
                                <x-select name="item_id[]" id="item_id_0" label="Item" value="{{ $model ? $model->item_id : '' }}" onchange="getPrice(0)" required></x-select>
                                <input type="hidden" name="item_value[]" id="item_value_0" value="">
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="stock_0" class="form-label">
                                        Stock
                                    </label>
                                    <input type="text" value="0" class="form-control" name="stock[]" id="stock_0" readonly>
                                    <small class="text-primary" id="unit-name-0"></small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="real_stock_0" class="form-label">Stock Fisik</label>
                                    <input type="text" value="0" class="form-control commas-form" name="real_stock[]" id="real_stock_0" onkeyup="getDifference(0)" onkeypress="return checkNumber(event)">
                                    <small class="text-primary" id="real-unit-name-0"></small>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="difference_0" class="form-label">Selisih</label>
                                    <input type="text" value="" class="form-control" name="difference[]" id="difference_0" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="price_unit_0" class="form-label">HPP</label>
                                    <input type="text" value="" class="form-control commas-form" name="price_unit[]" id="price_unit_0" onkeyup="getDifference(0)" {{ $can_fill_hpp }}>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="value_0" class="form-label">Nilai Selisih Stock</label>
                                    <input type="text" value="" class="form-control" name="value[]" id="value_0" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="note_0" class="form-label">Keterangan</label>
                                    <input type="text" value="" class="form-control" name="note[]" id="note_0">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mt-4">
                                    <x-button type="button" color="info" icon="plus" fontawesome size="sm" onclick="addInput()" />
                                </div>
                            </div>
                        </div>
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
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script>
            let count = 1;

            inititemSelect('item_id_0', 'all', 'purchase item');
            initSelect2Search('ware_house_id', `{{ route('admin.select.ware-house') }}`, {
                id: "id",
                text: "nama"
            });

            initSelect2SearchPagination(`coa-selectForm`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, {});

            checkClosingPeriod($('#date'))

            if (@json($model) != []) {
                getDetails()
            }

            function get_unit(item_id, index) {
                $.ajax({
                    url: `{{ route('admin.item.item-unit') }}/${item_id}`,
                    method: 'GET',
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        $(`#unit-name-${index}`).text(response.data.unit.name);
                        $(`#real-unit-name-${index}`).text(response.data.unit.name);
                    }
                });
            }

            function getDetails() {
                let details = @json($model->details ?? []);
                details.forEach(function(v, i) {
                    let add_detail = `<div class="col-md-1">
                        <div class="form-group mt-4">
                            <x-button type="button" color="info" icon="plus" fontawesome size="sm" onclick="addInput()" />
                        </div>
                    </div>`;

                    let delete_detail = `<div class="col-md-1">
                        <div class="form-group mt-4">
                            <x-button type="button" color="danger" icon="trash" fontawesome size="sm" onclick="deleteInput(${count})" />
                        </div>
                    </div>`;

                    let input_element = `
                        <div class="row mt-2" id="input_${count}" data-index="${count}">
                            <div class="col-md-2">
                                <x-select name="item_id[]" id="item_id_${count}" label="Item" value="{{ $model->item_id ?? '' }}" onchange="getPrice(${count})" required></x-select>
                                <input type="hidden" name="item_value[]" id="item_value_${count}" value="${v.value}">
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="stock_${count}" class="form-label">
                                        Stock
                                    </label>
                                    <input type="text" value="${formatRupiahWithDecimal(v.stock)}" class="form-control commas-form" name="stock[]" id="stock_${count}" readonly>
                                    <small class="text-primary" id="unit-name-${count}"></small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="real_stock_${count}" class="form-label">Stock Fisik</label>
                                    <input type="text" value="${formatRupiahWithDecimal(v.real_stock)}" class="form-control commas-form" name="real_stock[]" id="real_stock_${count}" onkeyup="getDifference(${count})" onkeypress="return checkNumber(event)">
                                    <small class="text-primary" id="real-unit-name-${count}"></small>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="difference_${count}" class="form-label">Selisih</label>
                                    <input type="text" value="${formatRupiahWithDecimal(v.difference)}" class="form-control commas-form" name="difference[]" id="difference_${count}" readonly>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label for="price_unit_${count}" class="form-label">HPP</label>
                                    <input type="text" value="${formatRupiahWithDecimal(v.price_unit)}" class="form-control commas-form" name="price_unit[]" id="price_unit_${count}" onkeyup="getDifference(${count})" {{ $can_fill_hpp }}>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="value_${count}" class="form-label">Nilai Selisih Stock</label>
                                    <input type="text" value="${formatRupiahWithDecimal(v.value)}" class="form-control commas-form" name="value[]" id="value_${count}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="note_${count}" class="form-label">Keterangan</label>
                                    <input type="text" value="${v.note ?? ''}" class="form-control" name="note[]" id="note_${count}">
                                </div>
                            </div>
                            ${i == 0 ? add_detail : delete_detail}
                        </div>
                    `;

                    $('#input').append(input_element);

                    initCommasForm();

                    inititemSelect('item_id_' + count, 'all', 'purchase item')
                    let opt = new Option(`${v.item.kode} - ${v.item.nama}`, v.item_id, true, true);
                    $(`#item_id_${count}`).append(opt);

                    count++;
                });
            }

            function addInput() {
                let input_element = `
                    <div class="row mt-2" id="input_${count}" data-index="${count}">
                        <div class="col-md-2">
                            <x-select name="item_id[]" id="item_id_${count}" label="Item" value="{{ $model->item_id ?? '' }}" onchange="getPrice(${count})" required></x-select>
                            <input type="hidden" name="item_value[]" id="item_value_${count}" value="">
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="stock_${count}" class="form-label">
                                    Stock
                                </label>
                                <input type="text" value="0" class="form-control commas-form" name="stock[]" id="stock_${count}" readonly>
                                <small class="text-primary" id="unit-name-${count}"></small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="real_stock_${count}" class="form-label">Stock Fisik</label>
                                <input type="text" value="0" class="form-control commas-form" name="real_stock[]" id="real_stock_${count}" onkeyup="getDifference(${count})" onkeypress="return checkNumber(event)">
                                <small class="text-primary" id="real-unit-name-${count}"></small>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="difference_${count}" class="form-label">Selisih</label>
                                <input type="text" value="" class="form-control" name="difference[]" id="difference_${count}" readonly>
                            </div>
                        </div>
                         <div class="col-md-2">
                            <div class="form-group">
                                <label for="price_unit_${count}" class="form-label">HPP</label>
                                <input type="text" value="" class="form-control commas-form" name="price_unit[]" id="price_unit_${count}" onkeyup="getDifference(${count})" {{ $can_fill_hpp }}>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="value_${count}" class="form-label">Nilai Selisih Stock</label>
                                <input type="text" value="" class="form-control" name="value[]" id="value_${count}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="note_${count}" class="form-label">Keterangan</label>
                                <input type="text" value="" class="form-control" name="note[]" id="note_${count}">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mt-4">
                                <x-button type="button" color="danger" icon="trash" fontawesome size="sm" onclick="deleteInput(${count})" />
                            </div>
                        </div>
                    </div>
                `;

                $('#input').append(input_element);

                initCommasForm();

                inititemSelect('item_id_' + count, 'all', 'purchase item')

                count++;
            }

            function rp(angka) {
                var reverse = angka.toString().split('').reverse().join(''),
                    ribuan = reverse.match(/\d{1,3}/g);
                ribuan = ribuan.join('.').split('').reverse().join('');
                return ribuan;
            }

            function deleteInput(i) {
                $(`#input_${i}`).remove();
            }

            function getPrice(index) {
                item_id = $("#item_id_" + index).val();
                let ware_house_id = $("#ware_house_id").val();

                if (!item_id || !ware_house_id) {
                    $("#stock_" + index).val(0);
                    return;

                }

                $.ajax({
                    url: "{{ route('admin.stock-adjustment.price-select') }}",
                    dataType: "json",
                    delay: 250,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                    data: {
                        item_id: item_id,
                        ware_house_id: ware_house_id,
                    },
                    success: function(res) {
                        $('#item_value_' + index).val(res.item_value);
                        $('#price_unit_' + index).val(formatRupiahWithDecimal(res.item_value));
                        $('#stock_' + index).val(formatRupiahWithDecimal(res.stock));
                    },
                });

                get_unit(item_id, index);
            }

            function setAllInput() {
                $("#input .row").each(function() {
                    let index = $(this).data("index");
                    getPrice(index);
                    getPriceDetail(index);
                });
            }

            function getDifference(index) {
                let stock = $(`#stock_${index}`).val();
                stock = thousandToFloat(stock);
                let real_stock = $(`#real_stock_${index}`).val();
                real_stock = thousandToFloat(real_stock);
                let price = $(`#price_unit_${index}`).val();
                price = thousandToFloat(price);
                if (real_stock == "") {
                    real_stock = 0;
                }
                let count_diff = (stock - real_stock) * -1;
                let value_from_diff = price * count_diff;

                $("#value_" + index).val(numberWithCommas(value_from_diff.toFixed(2)));

                $(`#difference_${index}`).val(numberWithCommas(count_diff.toFixed(2))).trigger("input");
            }

            function getPriceDetail(index) {
                let item_id = $("#item_id_" + index).val();
                let price_id = $("#price_id_" + index).val();
                let price = $("#price_" + index)
                price.val($("#price_id_" + index).find("option:selected").data("price"));
                let ware_house_id = $("#ware_house_id").val();

                if (!item_id || !ware_house_id) {
                    $("#stock_" + index).val(0);
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.stock-adjustment.price-detail') }}",
                    dataType: "json",
                    delay: 250,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                    data: {
                        id: item_id,
                        price_id: price_id,
                        ware_house_id: ware_house_id,
                    },
                    success: function(res) {
                        $("#stock_" + index).val(formatRupiahWithDecimal(res.main_stock));
                    },
                });
            }

            function checkNumber(event) {
                var aCode = event.which ? event.which : event.keyCode;
                if (aCode > 31 && (aCode < 48 || aCode > 57)) return false;

                return true;
            }

            $('#form-data').submit(function(e) {
                if ($('select[name="item_id[]"]').length == 0) {
                    e.preventDefault();
                    showAlert('', 'Item tidak boleh kosong', 'warning');

                    $('input[type="submit"]').attr('disabled', false);
                    $('button[type="submit"]').attr('disabled', false);
                }
            });

            $('body').addClass('sidebar-collapse');
        </script>
        <script>
            $(document).ready(function() {
                $('input[name="permission_name"]').on('click', function() {
                    var permission_name = $(this).attr('class');
                    $('.check-' + permission_name.split(' ')[0]).prop('checked', this.checked);
                });
            });
        </script>
    @endpush
