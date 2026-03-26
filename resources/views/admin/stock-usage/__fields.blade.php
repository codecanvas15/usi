<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <x-input class="datepicker-input" id="date" name="date" label="Tanggal" value="{{ localDate($now) }}" required autofucus onchange="checkClosingPeriod($(this))" />
            </div>
        </div>
        <div class="col-md-3">
            <x-select name="ware_house_id" id="ware_house_id" label="Gudang" onchange="setAllInput()" required></x-select>
        </div>
    </div>
    <div id="input">
        <div class="row" data-index="0">
            <div class="box-header with-border px-0 pb-1 mb-4" style="margin: 0px 10px;">
                <h4 class="box-title">Item 1</h4>
            </div>
            <div class="col-md-3">
                <x-select name="item_id[]" id="item_id_0" label="Item" onchange="getPo(0)" required></x-select>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="po_id" class="form-label">
                        PO
                        <span class="text-danger">*</span>
                    </label>
                    <select id="po_id_0" name="po_id[]" class="form-control select2" onchange="getLpb(0)" required disabled>
                    </select>
                    <input type="hidden" id="type_0">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="lpb_id" class="form-label">
                        LPB
                        <span class="text-danger">*</span>
                    </label>
                    <select id="lpb_id_0" name="lpb_id[]" class="form-control select2" onchange="getPriceDetail(0)" required disabled>
                    </select>
                    {{-- <input type="hidden" id="price_0" value="0"> --}}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="stock" class="form-label">
                        Stock
                    </label>
                    <input type="text" value="0" class="form-control" name="stock[]" id="stock_0" readonly>
                    <small class="text-primary" id="unit-name-0"></small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="qty" class="form-label">Jumlah Pemakaian</label>
                    <input type="text" value="0" class="form-control comma-on-blur" name="qty[]" id="qty_0" required onkeyup="validateQty(0)" onkeypress="return checkNumber(event)">
                </div>
            </div>
            <div class="col-md-3">
                <x-select id="fleet_type_0" name="fleet_type[]" label="Tipe Armada" onchange="getFleet(0)" required>
                    <option value="darat">Darat</option>
                    <option value="laut">Laut</option>
                </x-select>
            </div>
            <div class="col-md-3">
                <x-select name="fleet_id[]" id="fleet_id_0" label="Armada" required></x-select>
            </div>
            <div class="col-md-2 row align-items-end">
                <div class="form-group">
                    <x-button type="button" color="info" icon="plus" fontawesome size="sm" onclick="addInput()" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="note" class="form-label">Keperluan</label>
                <textarea type="text" rows="5" class="form-control" name="note" id="note" placeholder="Masukkan Keterangan" required></textarea>
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
    {{-- <script src="{{ asset('js/helpers/helpers.js') }}"></script> --}}
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        var price = $("#price");
        var value_input = $("#value");
        let count = 1;

        checkClosingPeriod($('#date'));

        const initSelectWareHouse2Search = (target, route, selector, min_char = 0) => {
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
                                id: data[selector.id],
                                text: data[selector.text],
                                type: data[selector.type],
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

        const initSelectItem2Search = (target, route, selector, min_char = 0) => {
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
                                id: data[selector.id],
                                text: data[selector.text],
                                type: data[selector.type],
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

        const initSelectFleet2Search = (target, route, selector, min_char = 0) => {
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
                                id: data[selector.id],
                                text: data[selector.text],
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

        initSelectWareHouse2Search('ware_house_id', `{{ route('admin.select.ware-house') }}`, {
            id: "id",
            text: "nama",
            type: "type",
        });

        function get_unit(item_id, index) {
            $.ajax({
                url: `{{ route('admin.item.item-unit') }}/${item_id}`,
                method: 'GET',
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    // console.log(response);
                    $(`#unit-name-${index}`).text(response.data.unit.name);
                    $(`#real-unit-name-${index}`).text(response.data.unit.name);
                }
            });
        }

        function addInput() {
            let input_element = `
                <div class="row mt-2" id="input_${count}" data-index="${count}">
                    <div class="box-header with-border px-0 pb-1 mb-4" style="margin: 0px 10px;">
                        <h4 class="box-title">Item ${count + 1}</h4>
                    </div>
                    <div class="col-md-3">
                        <x-select name="item_id[]" id="item_id_${count}" label="Item" onchange="getPo(${count})" required></x-select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="po_id" class="form-label">
                                PO
                                <span class="text-danger">*</span>
                            </label>
                            <select id="po_id_${count}" name="po_id[]" class="form-control select2" onchange="getLpb(${count})" required disabled>
                            </select>
                            <input type="hidden" id="type_${count}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="lpb_id" class="form-label">
                                LPB
                                <span class="text-danger">*</span>
                            </label>
                            <select id="lpb_id_${count}" name="lpb_id[]" class="form-control select2" onchange="getPriceDetail(${count})" required disabled>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="stock" class="form-label">
                                Stock
                            </label>
                            <input type="text" value="0" class="form-control" name="stock[]" id="stock_${count}" readonly>
                            <small class="text-primary" id="unit-name-${count}"></small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="qty" class="form-label">Jumlah Pemakaian</label>
                            <input type="text" value="0" class="form-control comma-on-blur" name="qty[]" id="qty_${count}" required onkeyup="validateQty(${count})" onkeypress="return checkNumber(event)">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <x-select id="fleet_type_${count}" name="fleet_type[]" label="Tipe Armada" onchange="getFleet(${count})" required>
                            <option value="darat">Darat</option>
                            <option value="laut">Laut</option>
                        </x-select>
                    </div>
                    <div class="col-md-3">
                        <x-select name="fleet_id[]" id="fleet_id_${count}" label="Armada" required></x-select>
                    </div>
                    <div class="col-md-2 row align-items-end">
                        <div class="form-group">
                            <x-button type="button" color="danger" icon="trash" fontawesome size="sm" onclick="deleteInput(${count})" />
                        </div>
                    </div>
                </div>
            `;

            $('#input').append(input_element);

            inititemSelect(`item_id_${count}`)

            initSelectFleet2Search('fleet_id_' + count, `{{ route('admin.select.fleet') }}`, {
                id: "id",
                text: "name"
            });

            $('#fleet_type_' + count).select2();

            count++;
        }

        function setAllInput() {
            data = $("#ware_house_id").select2('data')[0];

            $("#input .row").each(function() {
                let index = $(this).data("index");
                // getPrice(index);
                getItem(index);
                $('#item_id_' + index).val('').trigger('change');
                $('#po_id_' + index).val('').trigger('change');
                $('#lpb_id_' + index).val('').trigger('change');
                $('#po_id_' + index).attr('disabled', true);
                $('#lpb_id_' + index).attr('disabled', true);
                $('#stock_' + index).val('0');
                if ($('lpb_id_' + index).val() != null) {
                    getPriceDetail(index);
                }
            });
        }

        function deleteInput(i) {
            $(`#input_${i}`).remove();

            $("#input .box-title").each(function(i) {
                $(this).html("item " + (i + 1));
            });
        }

        function rp(angka) {
            var reverse = angka.toString().split('').reverse().join(''),
                ribuan = reverse.match(/\d{1,3}/g);
            ribuan = ribuan.join('.').split('').reverse().join('');
            return ribuan;
        }

        function getItem(index) {
            data = $("#ware_house_id").select2('data')[0];

            initSelectItem2Search('item_id_' + index, `{{ route('admin.select.item.type') }}/${data.type}`, {
                id: "id",
                text: "nama",
            })
        }

        function getFleet(index) {
            let fleet_type = $("#fleet_type_" + index).val();

            initSelectFleet2Search('fleet_id_' + index, `{{ route('admin.select.fleet.type') }}/${fleet_type}`, {
                id: "id",
                text: "name",
            })
        }

        getFleet(0);

        function getPo(index) {
            let item_id = $("#item_id_" + index).val();
            let ware_house_id = $("#ware_house_id").val();
            $('#stock_' + index).val("0");

            $.ajax({
                url: "{{ route('admin.stock-usage.get-po') }}",
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
                    let options = '<option value="">Pilih PO</option>';
                    $.map(res.data, function(obj) {
                        options += `<option value="${obj.id}" data-type="${
                            res.type
                        }">${obj.kode}</option>`;
                    });

                    $("#po_id_" + index).html(options).select2();
                    $('#type_' + index).val(res.type);
                    if ($("#po_id_" + index).prop("disabled")) {
                        $("#po_id_" + index).attr("disabled", false);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {},
            });

            get_unit(item_id, index);
        }

        function getLpb(index) {
            let po_id = $("#po_id_" + index).val();
            let type = $('#type_' + index).val();
            $('#stock_' + index).val("0");

            $.ajax({
                url: "{{ route('admin.stock-usage.get-lpb') }}",
                dataType: "json",
                delay: 250,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                },
                data: {
                    type: type,
                    po_id: po_id,
                },
                success: function(res) {
                    let options = '<option value="">Pilih LPB</option>';
                    $.map(res, function(obj) {
                        options += `<option value="${obj.id}">${obj.kode}</option>`;
                    });

                    $("#lpb_id_" + index).html(options).select2();
                    if ($("#po_id_" + index).val() != null && $("#lpb_id_" + index).prop("disabled")) {
                        $("#lpb_id_" + index).attr("disabled", false);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {},
            });
        }

        function getPrice(index) {
            item_id = $("#item_id_" + index).val();
            let ware_house_id = $("#ware_house_id").val();

            $.ajax({
                url: "{{ route('admin.stock-usage.price-select') }}",
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
                    let options = '<option value="">Pilih tanggal</option>';
                    $.map(res, function(obj) {
                        options += `<option value="${obj.price_id}" data-price="${
                            obj.price.harga_beli
                        }">${obj.date}</option>`;
                    });

                    $("#price_id_" + index).html(options).select2();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {},
            });
        }

        function validateQty(index) {
            var stock = $('#stock_' + index);
            var qty = $('#qty_' + index);
            var price = $('#price_' + index);
            var total = $('#total_' + index);

            if (replaceComma(qty.val()) > replaceComma(stock.val())) {
                alert('Jumlah retur tidak boleh lebih besar dari jumlah barang diterima!');

                qty.val(stock.val());
            }

            var total_x = parseFloat(replaceComma(qty.val())) * parseFloat(price.val());
            total.val(total_x).trigger('input');
        }

        function replaceComma(text) {
            if (text != 0) {
                return parseFloat(text.replace(/,/g, ""));
            } else {
                return 0;
            }
        }

        function getPriceDetail(index) {
            item_id = $("#item_id_" + index).val();
            let lpb_id = $("#lpb_id_" + index).val();

            $.ajax({
                url: "{{ route('admin.stock-usage.price-detail') }}",
                dataType: "json",
                delay: 250,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                },
                data: {
                    lpb_id: lpb_id,
                    item_id: item_id,
                },
                success: function(res) {
                    $("#stock_" + index).val(res);
                },
            });
        }

        function checkNumber(event) {
            var aCode = event.which ? event.which : event.keyCode;
            if (aCode > 31 && (aCode < 48 || aCode > 57)) return false;

            return true;
        }
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
