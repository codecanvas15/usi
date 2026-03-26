<x-table id="purchase-request-table">
    <x-slot name="table_head">
        <th>{{ Str::headline('#') }}</th>
        <th>{{ Str::headline('link') }}</th>
        <th>{{ Str::headline('tanggal') }}</th>
        <th>{{ Str::headline('judul') }}</th>
        <th>{{ Str::headline('subtitle') }}</th>
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
                const table = $('table#purchase-request-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route('admin.authorization.datatables') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            model: "\\App\\Models\\PurchaseRequest",
                        },
                    },
                    order: [
                        [5, 'desc']
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
                            data: 'tanggal',
                            name: 'tanggal'
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

            $('#purchase-request-btn').click(function(e) {
                e.preventDefault();
                initPurchaseGeneralTable();
            });

        });
    </script>
@endpush
