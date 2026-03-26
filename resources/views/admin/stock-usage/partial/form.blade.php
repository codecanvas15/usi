<script>
    let count = 0;
    const resetDataBefore = () => {
        $('#dataStockUsage-details').html('');
        $('#purchase-request-selectForm').val('').trigger('change');
    };

    const init = () => {
        initWarehouseSelect('#warehouse-selectForm')
        initSelectEmployee('#employee-selectForm')
        initProjectSelect('#project-selectForm', '#branch-selectForm');
    };

    $('#warehouse-selectForm').change(function(e) {
        e.preventDefault();
        resetDataBefore();
        if (this.value) {
            addData(count);

        }
    });

    $('#branch-selectForm').change(function(e) {
        e.preventDefault();

        $('#project-selectForm').val('').trigger('change');
    });

    $('#project-selectForm').change(function(e) {
        e.preventDefault();
        resetDataBefore();
        if (this.value) {
            addData(count);
        }
    });

    const addData = (cnt) => {
        let existing_count = $('select[name="item_id[]"').length;

        let row = `<div class="row" id="stockUsage-card-${cnt}">
                    <div class="col-md-3">
                        <div class="form-group">
                             <x-select name="item_id[]" id="item-Detail-inputForm-${cnt}" label="item" required autofocus>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                             <x-select name="coa_detail_id[]" id="coa-detail-id-${cnt}" label="COA" required autofocus>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input type="text" name="stock_left[]" id="stock-left-Detail-inputForm-${cnt}"  value="" required="required" label="sisa stock gudang" useCustomError="true" useCustomErrorColor="primary" readonly/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input type="text" name="quantity[]" id="quantity-Detail-inputForm-${cnt}" class="commas-form" value="0" required="required" label="jumlah" useCustomError="true" useCustomErrorColor="primary" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input type="text" name="necessity[]" id="necessity-Detail-inputForm-${cnt}" label="keperluan" required />
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-self-end">
                        <div class="form-group">
                            ${ existing_count == 0 ? `<x-button type="button" color="primary" id="add-data" icon="add" fontawesome size="sm" />` : `<x-button type="button" color="danger" id="btn-delete-item-${cnt}" icon="trash" fontawesome size="sm" />`}
                        </div>
                    </div>
                </div>`;

        $('#dataStockUsage-details').append(row);

        $(`#btn-delete-item-${cnt}`).click(function(e) {
            $(`#stockUsage-card-${cnt}`).remove();
        });

        initSelect2SearchPaginationData(`item-Detail-inputForm-${cnt}`, `{{ route('admin.select.item.type') }}/all?item_types=purchase item`, {
            id: 'id',
            text: 'kode,nama'
        })

        $(`#item-Detail-inputForm-${cnt}`).change(function(e) {
            e.preventDefault();
            getStockLeft(cnt);
        });

        $(`#quantity-Detail-inputForm-${cnt}`).keyup(debounce(function(e) {
            e.preventDefault();
            let quantity = thousandToFloat($(this).val());
            let stock_left = thousandToFloat($(`#stock-left-Detail-inputForm-${cnt}`).val());
            if (quantity > stock_left) {
                alert(`Stock tidak mencukupi, sisa stock ${stock_left}`);
                $(this).val(0);
            }
        }, 500));

        initSelect2SearchPagination(`coa-detail-id-${cnt}`, `{{ route('admin.select.coa') }}`, {
            id: "id",
            text: "account_code,name"
        }, 0, {
            account_type: ["Other Expense", "Expense"]
        });

        count += 1;

        if (existing_count == 0) {
            $('#add-data').click(function(e) {
                e.preventDefault();
                addData(count);
            });
        }
    };

    const getStockLeft = (cnt) => {
        let item_id = $(`#item-Detail-inputForm-${cnt}`).val();
        let warehouse_id = $('#warehouse-selectForm').val();
        $.ajax({
            url: "{{ route('admin.stock-usage.get-stock-left') }}",
            dataType: "json",
            delay: 250,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": token,
            },
            data: {
                item_id: item_id,
                ware_house_id: warehouse_id
            },
            success: function(res) {
                $(`#stock-left-Detail-inputForm-${cnt}`).val(res.data.stock_left);
                if (res.data.coa_expense) {
                    let option = `<option value="${res.data.coa_expense.id}" selected>${res.data.coa_expense.account_code} - ${res.data.coa_expense.name}</option>`;
                    $(`#coa-detail-id-${cnt}`).html(option).trigger('change');
                }
            },
        });
    };

    $('#purchase-request-selectForm').on('change', function(e) {
        e.preventDefault();
        $.ajax({
            url: `{{ route('admin.stock-usage.get-purchase-request-item') }}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                purchase_request_id: $(this).val()
            },
            success: function(res) {
                item_data = res.data;

                displayItem();
            }
        });
    });

    function displayItem() {
        $('#dataStockUsage-details').html('');
        $.each(item_data, function(index, item) {
            let existing_count = $('select[name="item_id[]"').length;
            existing_count += 1;

            let coa_expense = item.item_category.item_category_coas.filter((item) => item.type == 'Expense');
            coa_expense = coa_expense.length > 0 ? coa_expense[0] : null;

            let coa_html = '';
            if (coa_expense) {
                coa_html = `<option value="${coa_expense.coa.id}" selected>${coa_expense.coa.account_code} - ${coa_expense.coa.name}</option>`;
            }

            let row = `<div class="row item-pr" id="stockUsage-card-${existing_count}">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="item_id[]" id="item-Detail-inputForm-${existing_count}" label="item" required autofocus>
                                    <option value="${item.id}">${item.kode} - ${item.nama}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-select name="coa_detail_id[]" id="coa-detail-id-${existing_count}" label="COA" required autofocus>
                                    ${coa_html}
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="text" name="stock_left[]" id="stock-left-Detail-inputForm-${existing_count}"  value="" required="required" label="sisa stock gudang" useCustomError="true" useCustomErrorColor="primary" readonly/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="text" name="quantity[]" id="quantity-Detail-inputForm-${existing_count}" class="commas-form" value="0" required="required" label="jumlah" useCustomError="true" useCustomErrorColor="primary" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="text" name="necessity[]" id="necessity-Detail-inputForm-${existing_count}" label="keperluan" required />
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-self-end">
                            <div class="form-group">
                                ${ index == 0 ? `<x-button type="button" color="primary" id="add-data" icon="add" fontawesome size="sm" />` : `<x-button type="button" color="danger" id="btn-delete-item-${existing_count}" icon="trash" fontawesome size="sm" />`}
                            </div>
                        </div>
                    </div>`;

            $('#dataStockUsage-details').append(row);

            initSelect2SearchPagination(`coa-detail-id-${existing_count}`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, {
                account_type: ["Other Expense", "Expense"]
            });

            $(`#btn-delete-item-${existing_count}`).click(function(e) {
                $(`#stockUsage-card-${existing_count}`).remove();
            });

            $(`#quantity-Detail-inputForm-${existing_count}`).keyup(debounce(function(e) {
                e.preventDefault();
                let quantity = thousandToFloat($(this).val());
                let stock_left = thousandToFloat($(`#stock-left-Detail-inputForm-${existing_count}`).val());
                if (quantity > stock_left) {
                    alert(`Stock tidak mencukupi, sisa stock ${stock_left}`);
                    $(this).val(0);
                }
            }, 500));

            getStockLeft(existing_count);
            count += 1;

            $('#add-data').unbind('click');
            $('#add-data').click(function(e) {
                e.preventDefault();
                addData(count);
            });
        });
    }

    init();
</script>
