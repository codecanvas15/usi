<script>
    var csrf = $('input[name="_token"]').val();

    const setDepositeTable = () => {
        initSelect2Search('branch-select-deposite', '{{ route('admin.select.branch') }}', {
            'id': 'id',
            'text': 'name'
        });

        $("#deposite-table").DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            destroy: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: `${base_url}/cash-advance-payment-datatable`,
                type: "POST",
                data: {
                    _token: csrf,
                    coa_id: function() {
                        return $("#coa_id").val();
                    },
                    from_date: function() {
                        return $("#from_date_deposite").val();
                    },
                    to_date: function() {
                        return $("#to_date_deposite").val();
                    },
                    branch_id: $('#branch-select-deposite').val()
                },
            },
            columns: [{
                    data: "created_at",
                    name: "cash_advance_payments.created_at",
                    visible: false,
                    searchable: false,
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
                    name: "cash_advance_payments.date",
                },
                {
                    data: "to_model",
                },
                {
                    data: "to_name",
                },
                {
                    data: "credit",
                    name: "cash_advance_payment_details.credit",
                    className: "text-end",
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

        $("#deposite-table").css("width", "100%");
    }

    $(document).ready(() => {
        setDepositeTable();
    });

    $('#set-deposite-table').click(function(e) {
        e.preventDefault();
        setDepositeTable();
    });
</script>
