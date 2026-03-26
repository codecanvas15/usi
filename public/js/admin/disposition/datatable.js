const table = $("table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    order: [
        [1, 'desc']
    ],
    ajax: {
        url: `${base_url}/disposition`,
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
    columns: [{
            data: "DT_RowIndex",
            name: "DT_RowIndex",
            orderable: false,
            searchable: false,
        },
        {
            data: "date",
        },

        {
            data: "asset_name",
        },
        {
            data: "last_book_value",
            className: "text-end",
        },
        {
            data: "selling_price",
            className: "text-end",
        },
        {
            data: "note",
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
