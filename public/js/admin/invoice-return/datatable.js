const table = $("table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    order: [
        [0, 'desc']
    ],
    ajax: {
        url: `${base_url}/invoice-return`,
        type: "get",
        data: {
            _token: token,
            customer_id: function () {
                return $("#customer_id").val();
            },
            from_date: function () {
                return $("#from_date").val();
            },
            to_date: function () {
                return $("#to_date").val();
            },
        },
    },
    columns: [
        {
            data: "created_at",
            name: "invoice_returns.created_at",
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
            name: "invoice_returns.code",
        },
        {
            data: "date",
            name: "invoice_returns.date",
        },
        {
            data: "customer_nama",
            name: "customers.nama",
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
        {
            data: "export",
            name: "export",
            orderable: false,
            searchable: false,
        },
    ],
});

$("table").css("width", "100%");
