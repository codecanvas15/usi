var csrf = $('input[name="_token"]').val();

const table = $("table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    ajax: {
        url: `${base_url}/asset-datatable`,
        type: "POST",
        data: {
            _token: csrf,
        },
    },
    columns: [{
            data: "DT_RowIndex",
            name: "DT_RowIndex",
            orderable: false,
            searchable: false,
        },
        {
            data: "code",
            name: "cashbooks.code",
        },
        {
            data: "asset_name",
        },
        {
            data: "purchase_date",
        },
        {
            data: "item_category_name",
            name: "item_categories.nama",
        },
        {
            data: "value",
        },
        {
            data: "outstanding_value",
            name: "value",
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

$("table").css("width", "100%");
