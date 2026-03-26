<script>
    $(document).ready(function() {
        const table = $('table#TableNonGiro').DataTable({
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
                        return $("#from_date").val();
                    },
                    to_date: function() {
                        return $("#to_date").val();
                    },
                    branch_id: function() {
                        return $("#branch-select").val();
                    },
                    is_used: function() {
                        return $("#is_used").val();
                    },
                    status: function() {
                        return $("#status").val();
                    }
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
                    "data": "item",
                    "name": "fund_submissions.item",
                    "className": "text-uppercase"
                },
                {
                    "data": "to_name",
                    "name": "fund_submissions.to_name",
                },
                {
                    "data": "project_name",
                    "name": "projects.name",
                },
                {
                    "data": "total",
                    "name": "fund_submissions.total",
                },
                {
                    "data": "reference",
                    "name": "fund_submissions.reference",
                },
                {
                    "data": "status",
                    "name": "fund_submissions.status",
                },
                {
                    "data": "is_used",
                    "name": "fund_submissions.is_used",
                },
                {
                    "data": "action",
                    "orderable": false,
                    "searchable": false,
                }
            ]
        });

        $('table').css('width', '100%');

        initSelect2Search('branch-select', '{{ route('admin.select.branch') }}', {
            'id': 'id',
            'text': 'name'
        });

        const download_recap = (format) => {
            const from_date = $("#from_date").val();
            const to_date = $("#to_date").val();
            const branch_id = $("#branch-select").val();
            const is_used = $("#is_used").val();
            const url = `{{ route('admin.fund-submission.download-recap') }}?from_date=${from_date}&to_date=${to_date}&branch_id=${branch_id}&is_used=${is_used}&format=${format}`;
            window.open(url, '_blank');
        };
    });
</script>
