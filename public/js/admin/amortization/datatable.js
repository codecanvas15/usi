var csrf = $('input[name="_token"]').val();

const table = $("table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    order: [[1, "desc"]],
    ajax: {
        url: `${base_url}/amortization`,
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
            data: "lease_name",
            name: "leases.lease_name",
        },
        {
            data: "date",
            name: "amortizations.date",
        },
        {
            data: "from_date",
            name: "amortizations.from_date",
        },
        {
            data: "to_date",
            name: "amortizations.to_date",
        },
        {
            data: "amount",
            name: "amortizations.amount",
        },
        {
            data: "note",
            name: "amortizations.note",
        },
    ],
});

$("table").css("width", "100%");
