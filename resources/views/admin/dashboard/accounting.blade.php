<div class="row justify-content-end mb-4">
    <div class="col-xl-3 d-flex gap-items-2">
        <input type="month" class="form-control" name="date_id" id="filter_month_accouting">
        <x-button color="info" icon="search" id="filter_submit_accounting" fontawesome></x-button>
    </div>
</div>

<div class="row">
    <div class="col-xl-3">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <p class="mb-0 text-fade">Total Pendapatan</p>
                <h4 class="my-0" id="total_pendapatan">Rp. 0.00</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <p class="mb-0 text-fade">Total Piutang</p>
                <h4 class="my-0" id="total_piutang">Rp. 0.00</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <p class="mb-0 text-fade">Total Persediaan</p>
                <h4 class="my-0" id="total_persediaan">0</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <p class="mb-0 text-fade">Total Pengeluaran</p>
                <h4 class="my-0" id="total_pengeluaran">Rp. 0.00</h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="box">
                    <h4 class="box-body m-0">Grafik Pembelian</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body" id="chartPurchase">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4">
        <div class="box scale">
            <div class="box-body">
                <div class="d-flex justify-content-between">
                    <h4 class="fw-500 text-success underline">PO Trading</h4>
                    <div class="w-40 h-40 bg-success rounded-circle text-center fs-20 l-h-40 float-end"><i class="fa-sharp fa-solid fa-chart-simple"></i></div>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="col-lg-6">
                        <div class="text-center">
                            <h2 class="my-0 fs-28 fw-600 text-success" id="accounting-purchase-trading-count">0</h2>
                            <p class="fs-12 m-0 text-secondary" id="this_month">This Month</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-center">
                            <h2 class="my-0 fs-28 fw-600 text-success" id="accounting-purchase-trading-waiting-count">0</h2>
                            <p class="fs-12 m-0 text-secondary">Waiting Approval</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="box scale">
            <div class="box-body">
                <div class="d-flex justify-content-between">
                    <h4 class="fw-500 text-info underline">PO Service</h4>
                    <div class="w-40 h-40 bg-info rounded-circle text-center fs-20 l-h-40 float-end"><i class="fa-solid fa-broom"></i></div>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="col-lg-6">
                        <div class="text-center">
                            <h2 class="my-0 fs-28 fw-600 text-info" id="accounting-purchase-service-count">0</h2>
                            <p class="fs-12 m-0 text-secondary" id="this_month2">This Month</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-center">
                            <h2 class="my-0 fs-28 fw-600 text-info" id="accounting-purchase-service-waiting-count">0</h2>
                            <p class="fs-12 m-0 text-secondary">Waiting Approval</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="box scale">
            <div class="box-body">
                <div class="d-flex justify-content-between">
                    <h4 class="fw-500 text-dark underline">PO General</h4>
                    <div class="w-40 h-40 bg-dark rounded-circle text-center fs-20 l-h-40 float-end"><i class="fa-solid fa-dolly"></i></div>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="col-lg-6">
                        <div class="text-center">
                            <h2 class="my-0 fs-28 fw-600 text-dark" id="accounting-purchase-general-count">0</h2>
                            <p class="fs-12 m-0 text-secondary" id="this_month3">This Month</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-center">
                            <h2 class="my-0 fs-28 fw-600 text-dark" id="accounting-purchase-general-waiting-count">0</h2>
                            <p class="fs-12 m-0 text-secondary">Waiting Approval</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="box scale">
            <div class="box-body">
                <div class="d-flex justify-content-between">
                    <h4 class="fw-500 text-warning underline">PO Transport</h4>
                    <div class="w-40 h-40 bg-warning rounded-circle text-center fs-20 l-h-40 float-end"><i class="fa-solid fa-truck-fast"></i></div>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="col-lg-6">
                        <div class="text-center">
                            <h2 class="my-0 fs-28 fw-600 text-warning" id="accounting-purchase-transport-count">0</h2>
                            <p class="fs-12 m-0 text-secondary" id="this_month4">This Month</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-center">
                            <h2 class="my-0 fs-28 fw-600 text-warning" id="accounting-purchase-transport-waiting-count">0</h2>
                            <p class="fs-12 m-0 text-secondary">Waiting Approval</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@push('script')
    <script>
        $(document).ready(() => {

            let data_dashboard = []
            const renderDataCard = () => {
                // card
                $('#accounting-purchase-trading-count').text(data_dashboard.trading.count_on_month);
                $('#accounting-purchase-trading-waiting-count').text(data_dashboard.trading.waiting_approval);

                $('#accounting-purchase-service-count').text(data_dashboard.service.count_on_month);
                $('#accounting-purchase-service-waiting-count').text(data_dashboard.service.waiting_approval);

                $('#accounting-purchase-general-count').text(data_dashboard.general.count_on_month);
                $('#accounting-purchase-general-waiting-count').text(data_dashboard.general.waiting_approval);

                $('#accounting-purchase-transport-count').text(data_dashboard.transport.count_on_month);
                $('#accounting-purchase-transport-waiting-count').text(data_dashboard.transport.waiting_approval);
            }

            const resetDataCard = () => {
                // card
                $('#purchase-request-count').text(0);
                $('#purchase-request-waiting-count').text(0);

                $('#accounting-purchase-trading-count').text(0);
                $('#accounting-purchase-trading-waiting-count').text(0);

                $('#accounting-purchase-service-count').text(0);
                $('#accounting-purchase-service-waiting-count').text(0);

                $('#accounting-purchase-general-count').text(0);
                $('#accounting-purchase-general-waiting-count').text(0);

                $('#accounting-purchase-transport-count').text(0);
                $('#accounting-purchase-transport-waiting-count').text(0);
            }


            const numberWithCommas = (x) => {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            const displayCardData = ({
                amountIncome,
                receivableAmount,
                stockAmount,
                amountExpense
            }) => {
                $('#total_pendapatan').html(currencyFormatID(amountIncome));
                $('#total_piutang').html(currencyFormatID(receivableAmount));
                $('#total_persediaan').html(currencyFormatID(stockAmount));
                $('#total_pengeluaran').html(currencyFormatID(amountExpense));
            }

            $('#filter_submit_accounting').click(function() {
                syncData($('#filter_month_accouting').val())
                $('#this_month').html($('#filter_month_accouting').val() ? new Date($('#filter_month_accouting').val()).toLocaleDateString('en-EN', {
                    month: 'long'
                }) : 'This Month')
                $('#this_month2').html($('#filter_month_accouting').val() ? new Date($('#filter_month_accouting').val()).toLocaleDateString('en-EN', {
                    month: 'long'
                }) : 'This Month')
                $('#this_month3').html($('#filter_month_accouting').val() ? new Date($('#filter_month_accouting').val()).toLocaleDateString('en-EN', {
                    month: 'long'
                }) : 'This Month')
                $('#this_month4').html($('#filter_month_accouting').val() ? new Date($('#filter_month_accouting').val()).toLocaleDateString('en-EN', {
                    month: 'long'
                }) : 'This Month')
            })

            const syncData = (date = '') => {
                resetDataCard()
                $.ajax({
                    type: "get",
                    url: `{{ route('admin.index.get-data-dashboard-accounting') }}?date=${date}`,
                    success: async({
                        data
                    }) => {
                        $('#chartPurchase').html('')
                        displayCardData(data);
                        data_dashboard = data?.purchaseChartData
                        renderDataCard()

                        // Response {general : ..., service: ..., transport: ..., trading}
                        let {
                            purchaseChartData
                        } = data;

                        // Chart Data
                        var options = {
                            chart: {
                                type: 'bar'
                            },
                            series: [{
                                data: [{
                                        x: 'General',
                                        y: purchaseChartData?.general?.count_on_month,
                                    },
                                    {
                                        x: 'Service',
                                        y: purchaseChartData?.service?.count_on_month,
                                    },
                                    {
                                        x: 'Trading',
                                        y: purchaseChartData?.trading?.count_on_month,
                                    },
                                    {
                                        x: 'Transport',
                                        y: purchaseChartData?.transport?.count_on_month,
                                    },
                                ]
                            }],

                            plotOptions: {
                                bar: {
                                    distributed: true
                                },
                                radialBar: {
                                    hollow: {
                                        margin: 15,
                                        size: "70%"
                                    },

                                    dataLabels: {
                                        showOn: "always",
                                        name: {
                                            offsetY: -10,
                                            show: true,
                                            color: "#888",
                                            fontSize: "13px"
                                        },
                                        value: {
                                            color: "#111",
                                            fontSize: "30px",
                                            show: true
                                        }
                                    }
                                }
                            },

                            stroke: {
                                lineCap: "round",
                            },
                            labels: ["General", "Service", "Trading", "Transport"]
                        };

                        var chart = new ApexCharts(document.querySelector("#chartPurchase"), options);
                        chart.render()
                    },
                    error: (error) => {
                        console.log(error)
                    }
                });
            }

            $('#reload-data').click(function(e) {
                e.preventDefault();
                syncData();
            });

            syncData()

            const currencyFormatID = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: "currency",
                    currency: "IDR"
                }).format(number)
            }
        });
    </script>

@endpush
