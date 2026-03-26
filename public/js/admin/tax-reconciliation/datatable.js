const table = $("table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    order: [[2, "desc"]],
    ajax: {
        url: `${base_url}/tax-reconciliation`,
        type: "get",
        data: {
            _token: token,
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
            data: "DT_RowIndex",
            name: "DT_RowIndex",
            orderable: false,
            searchable: false,
        },
        {
            data: "date",
            name: "tax_period",
        },

        {
            data: "code",
        },
        {
            data: "total_in",
            className: "text-end",
        },
        {
            data: "total_out",
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
