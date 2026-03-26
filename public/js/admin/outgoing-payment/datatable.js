var csrf = $('input[name="_token"]').val();

const table = $("#general-table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    order: [
        [0, 'desc']
    ],
    ajax: {
        url: `${base_url}/outgoing-payment-datatable`,
        type: "POST",
        data: {
            _token: csrf,
            from_date: function () {
                return $("#from_date").val();
            },
            to_date: function () {
                return $("#to_date").val();
            },
            branch_id: function () {
                return $("#branch-select").val();
            },
        },
    },
    columns: [
        {
            data: "created_at",
            name: "outgoing_payments.created_at",
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
            name: "bank_code_mutations.code",
        },
        {
            data: "date",
            name: "outgoing_payments.date",
        },
        {
            data: "to_name",
            name: "outgoing_payments.to_name",
        },
        {
            data: "total",
            name: "outgoing_payments.total",
            className: "text-end",
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
        }
    ],
});

$("#general-table").css("width", "100%");
