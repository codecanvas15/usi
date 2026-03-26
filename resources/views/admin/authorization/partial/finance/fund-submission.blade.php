<x-table id="fund-submission-table">
    <x-slot name="table_head">
        <th>{{ Str::headline('#') }}</th>
        <th>{{ Str::headline('link') }}</th>
        <th>{{ Str::headline('Bayar ke') }}</th>
        <th>{{ Str::headline('tanggal') }}</th>
        <th>{{ Str::headline('Nilai') }}</th>
        <th>{{ Str::headline('judul') }}</th>
        <th>{{ Str::headline('subtXitle') }}</th>
        <th>{{ Str::headline('status') }}</th>
        <th>{{ Str::headline('dibuat pada') }}</th>
        <td></td>
    </x-slot>
    <x-slot name="table_body">

    </x-slot>
</x-table>

@push('script')
    <script>
        $(document).ready(function() {

            const initPurchaseGeneralTable = () => {
                const table = $('table#fund-submission-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route('admin.authorization.datatables') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            model: "\\App\\Models\\FundSubmission",
                        },
                    },
                    order: [
                        [8, 'desc']
                    ],

                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'link',
                            name: 'link'
                        },
                        {
                            data: 'to_name',
                            name: 'to_name'
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'total',
                            name: 'total'
                        },
                        {
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'subtitle',
                            name: 'subtitle'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                    ]
                });
                $('table').css('width', '100%');
            };

            $('#fund-submission-btn').click(function(e) {
                e.preventDefault();
                initPurchaseGeneralTable();
            });

        });
    </script>
@endpush
