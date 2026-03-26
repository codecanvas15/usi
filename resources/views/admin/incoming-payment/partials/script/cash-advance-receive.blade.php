<script>
    var csrf = $('input[name="_token"]').val();

    const setTable = () => {
        initSelect2Search('branch-select-cash-advance-receive', '{{ route('admin.select.branch') }}', {
            'id': 'id',
            'text': 'name'
        });
        $("table#cash-advance-receive-table").DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            destroy: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: `${base_url}/cash-advance-receive-datatable`,
                type: "POST",
                data: {
                    _token: csrf,
                    coa_id: function() {
                        return $("#coa_id").val();
                    },
                    from_date: function() {
                        return $("#from_date-cash-advance-receive").val();
                    },
                    to_date: function() {
                        return $("#to_date-cash-advance-receive").val();
                    },
                    branch_id: $('#branch-select-cash-advance-receive').val()
                },
            },
            columns: [{
                    data: "created_at",
                    name: "cash_advance_receives.created_at",
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
                    data: "cash_advance_value",
                    name: "cash_advance_receive_details.credit",
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
    }

    $(document).ready(() => {
        setTable();
    });

    $('#set-cash-advance-receive-table').click(function(e) {
        e.preventDefault();
        setTable();
    });

    sidebarMenuOpen('#finance-main-sidebar');
    sidebarActive('#incoming-payment-sidebar');
    sidebarActive('#{{ $main }}');
</script>
