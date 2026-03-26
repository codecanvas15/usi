var csrf = $('input[name="_token"]').val();

const table = $("table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    order: [
        [1, 'desc']
    ],
    ajax: {
        url: `${base_url}/cash-advance-receive-datatable`,
        type: "POST",
        data: {
            _token: csrf,
            coa_id: function () {
                return $("#coa_id").val();
            },
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
            data: "code",
            name: "cash_advance_receives.code",
        },

        {
            data: "date",
            name: "cash_advance_receives.date",
        },
        {
            data: "customer_name",
            name: "customers.nama",
        },
        {
            data: "project_name",
            name: "projects.name",
        },
        {
            data: "reference",
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
