<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-3">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="col-lg-12">
                                <div class="text-center">
                                    <h2 class="my-0 fs-28 fw-600 text-primary" id="stockInCard">0</h2>
                                    <p class="fs-12 m-0 text-secondary">Stock Masuk</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="col-lg-12">
                                <div class="text-center">
                                    <h2 class="my-0 fs-28 fw-600 text-primary" id="stockOutCard">0</h2>
                                    <p class="fs-12 m-0 text-secondary">Stock Keluar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex justify-content-around">
                            <div class="text-center">
                                <h2 class="my-0" id="itemReceivingReportCard-count">0</h2>
                                <p class="mb-0 text-fade">Lpb bulan ini</p>
                            </div>
                            <div class="b-1"></div>
                            <div class="text-center">
                                <h2 class="my-0" id="itemReceivingReportCard-waitingApproval">0</h2>
                                <p class="mb-0 text-fade">Menunggu Persetujuan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex justify-content-around">
                            <div class="text-center">
                                <h2 class="my-0" id="stockCard-usage">0</h2>
                                <p class="mb-0 text-fade">Stock usage</p>
                            </div>
                            <div class="b-1"></div>
                            <div class="text-center">
                                <h2 class="my-0" id="stockCard-opname">0</h2>
                                <p class="mb-0 text-fade">Stock opname</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <x-table id="itemreceivingreportTable">
                    <x-slot name="table_head">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Tipe</th>
                            <th>Action</th>
                        </tr>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            let data_warehouse = [];

            const renderData = () => {
                let {
                    stockIn,
                    stockOut,
                    itemReceivingReportThisMonth,
                    itemReceivingReportThisMonthWaitingApproval,
                    itemReceivingReportWaitingApproval,
                    stockUsageThisMonth,
                    stockOpnameThisMonth
                } = data_warehouse;

                $('#stockInCard').html(numberWithCommas(stockIn));
                $('#stockOutCard').html(numberWithCommas(stockOut));
                $('#itemReceivingReportCard-count').html(numberWithCommas(itemReceivingReportThisMonth));
                $('#itemReceivingReportCard-waitingApproval').html(numberWithCommas(itemReceivingReportThisMonthWaitingApproval));
                $('#stockCard-usage').html(numberWithCommas(stockUsageThisMonth));
                $('#stockCard-opname').html(numberWithCommas(stockOpnameThisMonth));

                $('#itemreceivingreportTable tbody').html('');

                itemReceivingReportWaitingApproval.map((lpb, index) => {
                    let {
                        id,
                        kode,
                        tipe
                    } = lpb;

                    if (tipe == 'jasa') {
                        tipe = 'service';
                    }

                    let row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${kode}</td>
                            <td>${tipe}</td>
                            <td>
                                <a href="${base_url}/item-receiving-report-${tipe}/${id}" >Detail</a>
                            </td>
                        </tr>
                    `;

                    $('#itemreceivingreportTable tbody').append(row);
                });
            };

            const getData = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('admin.index.get-data-dashboard-warehouse') }}",
                    success: function({
                        data
                    }) {
                        data_warehouse = data;
                        renderData();
                    }
                });
            };


            getData();

            $('#reload-data').click(function(e) {
                e.preventDefault();
                getData()
            });
        });
    </script>
@endpush
