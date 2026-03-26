const table = $("table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    order: [[0, "desc"]],
    ajax: {
        url: `${base_url}/purchase-return`,
        type: "get",
        data: {
            _token: token,
            vendor_id: function () {
                return $("#vendor_id").val();
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
            name: "purchase_returns.created_at",
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
            name: "purchase_returns.code",
        },
        {
            data: "date",
            name: "purchase_returns.date",
        },
        {
            data: "vendor_nama",
            name: "vendors.nama",
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
