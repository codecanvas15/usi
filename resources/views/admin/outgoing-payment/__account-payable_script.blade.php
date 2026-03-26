<script>
    initSelect2Search('vendor_id', `{{ route('admin.select.vendor') }}`, {
        id: "id",
        text: "nama"
    });

    $("#payable-table").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [
            [0, 'desc']
        ],
        ajax: {
            url: `${base_url}/account-payable`,
            type: "get",
            data: {
                _token: token,
                vendor_id: function() {
                    return $("#vendor_id").val();
                },
                from_date: function() {
                    return $("#from_date_payable").val();
                },
                to_date: function() {
                    return $("#to_date_payable").val();
                },
            },
        },
        columns: [{
                data: "created_at",
                name: "account_payables.created_at",
                searchable: false,
                visible: false,
            },
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            {
                data: "code",
                name: "bank_code_mutations.code"
            },
            {
                data: "date",
            },

            {
                data: "vendor_nama",
                name: "vendors.nama",
            },
            {
                data: "currency_nama",
                name: "currencies.nama",
            },
            {
                data: "total",
                className: "text-end",
            },
            {
                data: "status",
            },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
    });

    $("#payable-table").css("width", "100%");
</script>
