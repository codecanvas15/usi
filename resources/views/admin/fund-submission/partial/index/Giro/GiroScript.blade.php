<script>
    $(document).ready(function() {
        const table = $('table#TableGiro').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '{{ route("admin.$main.index") }}',
                data: {
                    from_date: function() {
                        return $("#Girofrom_date").val();
                    },
                    to_date: function() {
                        return $("#Giroto_date").val();
                    },
                    branch_id: function() {
                        return $("#Girobranch-select").val();
                    },
                    is_used: function() {
                        return $("#Girois_used").val();
                    },
                    isGiro: true,
                    status: function() {
                        return $("#giro_status").val();
                    },
                },
            },
            columns: [{
                    data: 'created_at',
                    name: 'fund_submissions.created_at',
                    visible: false,
                    searchable: false
                },
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    "data": "date",
                    "name": "fund_submissions.date",
                },
                {
                    "data": "code",
                    "name": "fund_submissions.code",
                },
                {
                    "data": "to_name",
                    "name": "fund_submissions.to_name",
                },
                {
                    "data": "giro_number",
                    "name": "projects.giro_number",
                },
                {
                    "data": "giro_liquid_date",
                    "name": "fund_submissions.giro_liquid_date",
                },
                {
                    "data": "realization_date",
                    "name": "send_payments.realization_date",
                },
                {
                    "data": "total",
                    "name": "fund_submissions.total",
                },
                {
                    "data": "status",
                    "name": "fund_submissions.status",
                },
                {
                    "data": "action",
                    "orderable": false,
                    "searchable": false,
                }
            ]
        });

        $('table').css('width', '100%');

        initSelect2Search('Girobranch-select', '{{ route('admin.select.branch') }}', {
            'id': 'id',
            'text': 'name'
        });

        const download_recap = (format) => {
            const from_date = $("#Girofrom_date").val();
            const to_date = $("#Giroto_date").val();
            const branch_id = $("#Girobranch-select").val();
            const is_used = $("#Girois_used").val();
            const url = `{{ route('admin.fund-submission.download-recap') }}?from_date=${from_date}&to_date=${to_date}&branch_id=${branch_id}&is_used=${is_used}&format=${format}`;
            window.open(url, '_blank');
        };
    });
</script>
