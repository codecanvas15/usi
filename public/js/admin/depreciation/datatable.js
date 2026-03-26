var csrf = $('input[name="_token"]').val();

const table = $("table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    order: [[1, "desc"]],
    ajax: {
        url: `${base_url}/depreciation`,
        type: "GET",
        data: {
            _token: csrf,
            from_date: function () {
                return $("#from_date").val();
            },
            to_date: function () {
                return $("#to_date").val();
            },
            branch_id: function () {
                return $("#branch_id").val();
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
            data: "asset_name",
            name: "assets.asset_name",
        },
        {
            data: "date",
            name: "depreciations.date",
        },
        {
            data: "from_date",
            name: "depreciations.from_date",
        },
        {
            data: "to_date",
            name: "depreciations.to_date",
        },
        {
            data: "amount",
            name: "depreciations.amount",
        },
        {
            data: "note",
            name: "depreciations.note",
        },
    ],
});

$("table").css("width", "100%");
