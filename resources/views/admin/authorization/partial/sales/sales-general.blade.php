<x-table id="sales-general-table">
    <x-slot name="table_head">
        <th>{{ Str::headline('#') }}</th>
        <th>{{ Str::headline('link') }}</th>
        <th>{{ Str::headline('vendor') }}</th>
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
                const table = $('table#sales-general-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route('admin.authorization.datatables') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            model: "\\App\\Models\\SaleOrderGeneral",
                        },
                    },
                    order: [
                        [7, 'desc']
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
                            data: 'customer_name',
                            name: 'customer_name'
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

            $('#sales-general-btn').click(function(e) {
                e.preventDefault();
                initPurchaseGeneralTable();
            });

        });
    </script>
@endpush
