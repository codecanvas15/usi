<div class="row">
    <div class="col-xl-3">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <h2 class="my-0">
                    <span class="mb-20">{{ get_local_currency_symbol() }}</span>
                    <span id="spending-this-month-general">0</span>
                </h2>
                <p class="mb-0 text-fade">Spending This Month Po General</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <h2 class="my-0">
                    <span class="mb-20">{{ get_local_currency_symbol() }}</span>
                    <span id="spending-this-month-service">0</span>
                </h2>
                <p class="mb-0 text-fade">Spending This Month Po Service</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <h2 class="my-0">
                    <span class="mb-20">{{ get_local_currency_symbol() }}</span>
                    <span id="spending-this-month-trading">0</span>
                </h2>
                <p class="mb-0 text-fade">Spending This Month Po Trading</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <h2 class="my-0">
                    <span class="mb-20">{{ get_local_currency_symbol() }}</span>
                    <span id="spending-this-month-transport">0</span>
                </h2>
                <p class="mb-0 text-fade">Spending This Month Po Transport</p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="row">

            <div class="col-xl-12">
                <div class="box">
                    <div class="box-body">
                        <div id="graph-chart">

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="box scale">
                    <div class="box-body">
                        <div class="d-flex justify-content-between">
                            <h4 class="fw-500 text-primary underline">Purchase Request</h4>
                            <div class="w-40 h-40 bg-primary rounded-circle text-center fs-20 l-h-40 float-end"><i class="fa-solid fa-cart-shopping"></i></div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="col-lg-6">
                                <div class="text-center">
                                    <h2 class="my-0 fs-28 fw-600 text-primary" id="purchase-request-count">0</h2>
                                    <p class="fs-12 m-0 text-secondary">This Month</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-center">
                                    <h2 class="my-0 fs-28 fw-600 text-primary" id="purchase-request-waiting-count">0</h2>
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
                            <h4 class="fw-500 text-success underline">PO Trading</h4>
                            <div class="w-40 h-40 bg-success rounded-circle text-center fs-20 l-h-40 float-end"><i class="fa-sharp fa-solid fa-chart-simple"></i></div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="col-lg-6">
                                <div class="text-center">
                                    <h2 class="my-0 fs-28 fw-600 text-success" id="purchase-trading-count">0</h2>
                                    <p class="fs-12 m-0 text-secondary">This Month</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-center">
                                    <h2 class="my-0 fs-28 fw-600 text-success" id="purchase-trading-waiting-count">0</h2>
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
                                    <h2 class="my-0 fs-28 fw-600 text-info" id="purchase-service-count">0</h2>
                                    <p class="fs-12 m-0 text-secondary">This Month</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-center">
                                    <h2 class="my-0 fs-28 fw-600 text-info" id="purchase-service-waiting-count">0</h2>
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
                                    <h2 class="my-0 fs-28 fw-600 text-dark" id="purchase-general-count">0</h2>
                                    <p class="fs-12 m-0 text-secondary">This Month</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-center">
                                    <h2 class="my-0 fs-28 fw-600 text-dark" id="purchase-general-waiting-count">0</h2>
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
                                    <h2 class="my-0 fs-28 fw-600 text-warning" id="purchase-transport-count">0</h2>
                                    <p class="fs-12 m-0 text-secondary">This Month</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-center">
                                    <h2 class="my-0 fs-28 fw-600 text-warning" id="purchase-transport-waiting-count">0</h2>
                                    <p class="fs-12 m-0 text-secondary">Waiting Approval</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="box">
                    <div class="box-header">
                        <h5 class="mb-0 text-fade fw-bold">Purchase Request</h5>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table table-responsive dash-table">
                                <tbody id="table-purchase-request">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="box">
                    <div class="box-header">
                        <h5 class="mb-0 text-fade fw-bold">PO Trading</h5>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table table-responsive dash-table">
                                <tbody id="table-purchase-trading">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="box">
                    <div class="box-header">
                        <h5 class="mb-0 text-fade fw-bold">PO Service</h5>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table table-responsive dash-table">
                                <tbody id="table-purchase-service">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="box">
                    <div class="box-header">
                        <h5 class="mb-0 text-fade fw-bold">PO General</h5>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table table-responsive dash-table">
                                <tbody id="table-purchase-general">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="box">
                    <div class="box-header">
                        <h5 class="mb-0 text-fade fw-bold">PO Transport</h5>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table table-responsive dash-table">
                                <tbody id="table-purchase-transport">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            let data_dashboard = [];

            const resetDataDashboard = () => {
                // top card
                $('#spending-this-month-general').text(0);
                $('#spending-this-month-service').text(0);
                $('#spending-this-month-trading').text(0);
                $('#spending-this-month-transport').text(0);

                // card
                $('#purchase-request-count').text(0);
                $('#purchase-request-waiting-count').text(0);

                $('#purchase-trading-count').text(0);
                $('#purchase-trading-waiting-count').text(0);

                $('#purchase-service-count').text(0);
                $('#purchase-service-waiting-count').text(0);
                $('#purchase-general-count').text(0);
                $('#purchase-general-waiting-count').text(0);

                $('#purchase-transport-count').text(0);
                $('#purchase-transport-waiting-count').text(0);

                // table
                $('#table-purchase-request').html('');
                $('#table-purchase-trading').html('');
                $('#table-purchase-service').html('');
                $('#table-purchase-general').html('');
                $('#table-purchase-transport').html('');

                // card
                $('#graph-chart').html('');
            }

            const renderDataDashboard = () => {
                // card top
                $('#spending-this-month-general').text(KFormatter(data_dashboard.spending_purchase_this_month?.general ?? 0));
                $('#spending-this-month-service').text(KFormatter(data_dashboard.spending_purchase_this_month?.service ?? 0));
                $('#spending-this-month-trading').text(KFormatter(data_dashboard.spending_purchase_this_month?.trading ?? 0));
                $('#spending-this-month-transport').text(KFormatter(data_dashboard.spending_purchase_this_month?.transport ?? 0));

                // card
                $('#purchase-request-count').text(data_dashboard.purchase_request.this_month_count);
                $('#purchase-request-waiting-count').text(data_dashboard.purchase_request.waiting_approval);

                $('#purchase-trading-count').text(data_dashboard.purchase_trading.this_month_count);
                $('#purchase-trading-waiting-count').text(data_dashboard.purchase_trading.waiting_approval);

                $('#purchase-service-count').text(data_dashboard.purchase_service.this_month_count);
                $('#purchase-service-waiting-count').text(data_dashboard.purchase_service.waiting_approval);

                $('#purchase-general-count').text(data_dashboard.purchase_general.this_month_count);
                $('#purchase-general-waiting-count').text(data_dashboard.purchase_general.waiting_approval);

                $('#purchase-transport-count').text(data_dashboard.purchase_transport.this_month_count);
                $('#purchase-transport-waiting-count').text(data_dashboard.purchase_transport.waiting_approval);

                // table
                data_dashboard.purchase_request.latest.map((data, index) => {
                    let table_purchase_request = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <a href="{{ route('admin.purchase-request.index') }}/${data.id}" class="text-primary">${data.kode}</a>
                            </td>
                            <td>${data.tanggal}</td>
                            <td>${data.type}</td>
                            <td>${data.status}</td>
                        </tr>
                    `;

                    $('#table-purchase-request').append(table_purchase_request);
                });

                data_dashboard.purchase_trading.latest.map((data, index) => {
                    let table_purchase_trading = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <a href="{{ route('admin.purchase-order.index') }}/${data.id}" class="text-primary">${data.nomor_po}</a>
                            </td>
                            <td>${data.tanggal}</td>
                            <td>${data.status}</td>
                        </tr>
                    `;

                    $('#table-purchase-trading').append(table_purchase_trading);
                });

                data_dashboard.purchase_service.latest.map((data, index) => {
                    let table_purchase_service = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <a href="{{ route('admin.purchase-order-service.index') }}/${data.id}" class="text-primary">${data.code}</a>
                            </td>
                            <td>${data.date}</td>
                            <td>${data.status}</td>
                        </tr>
                    `;

                    $('#table-purchase-service').append(table_purchase_service);
                });

                data_dashboard.purchase_general.latest.map((data, index) => {
                    let table_purchase_general = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <a href="{{ route('admin.purchase-order-general.index') }}/${data.id}" class="text-primary">${data.code}</a>
                            </td>
                            <td>${data.date}</td>
                            <td>${data.status}</td>
                        </tr>
                    `;

                    $('#table-purchase-general').append(table_purchase_general);
                });

                data_dashboard.purchase_transport.latest.map((data, index) => {
                    let table_purchase_transport = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <a href="{{ route('admin.purchase-order-transport.index') }}/${data.id}" class="text-primary">${data.kode}</a>
                            </td>
                            <td>${data.status}</td>
                        </tr>
                    `;

                    $('#table-purchase-transport').append(table_purchase_transport);
                });

                let {
                    general,
                    service,
                    trading,
                    transport
                } = data_dashboard.spending_purchases;
                var options = {
                    chart: {
                        height: 350,
                        type: "line",
                        stacked: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    // colors: ["#FF1654", "#247BA0"],
                    series: [{
                            name: 'General',
                            data: general.map((spending, index) => {
                                return spending.data;
                            }),
                        },
                        {
                            name: 'Service',
                            data: service.map((spending, index) => {
                                return spending.data;
                            }),
                        },
                        {
                            name: 'Trading',
                            data: trading.map((spending, index) => {
                                return spending.data;
                            }),
                        },
                        {
                            name: 'Transport',
                            data: transport.map((spending, index) => {
                                return spending.data;
                            }),
                        }
                        // {
                        //     name: "Series A",
                        //     data: [1.4, 2, 2.5, 1.5, 2.5, 2.8, 3.8, 4.6]
                        // },
                    ],
                    stroke: {
                        width: [4, 4]
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: "20%"
                        }
                    },
                    // xaxis: {
                    //     categories: [
                    //         'Januari',
                    //         'Februari',
                    //         'Maret',
                    //         'April',
                    //         'Mei',
                    //         'Juni',
                    //         'Jull',
                    //         'Agustus',
                    //         'September',
                    //         'Oktober',
                    //         'November',
                    //         'Desember',
                    //     ]
                    // },
                    yaxis: [
                        [
                            'Januari',
                            'Februari',
                            'Maret',
                            'April',
                            'Mei',
                            'Juni',
                            'Jull',
                            'Agustus',
                            'September',
                            'Oktober',
                            'November',
                            'Desember',
                        ].map((month, index) => {

                            return {
                                axisTicks: {
                                    show: true
                                },
                                axisBorder: {
                                    show: true,
                                    //     color: "#FF1654"
                                },
                                // labels: {
                                //     style: {
                                //         colors: "#FF1654"
                                //     }
                                // },
                                title: {
                                    text: month,
                                    style: {
                                        // color: "#FF1654"
                                    }
                                }
                            }

                        })
                    ],
                    tooltip: {
                        shared: false,
                        intersect: true,
                        x: {
                            show: false
                        }
                    },
                    markers: {
                        size: 1,
                    },
                    legend: {
                        horizontalAlign: "left",
                        offsetX: 40
                    }
                };

                var chart = new ApexCharts(document.querySelector("#graph-chart"), options);
                chart.render();
            }

            const getDataDatsboard = () => {
                resetDataDashboard();

                $.ajax({
                    url: "{{ route('admin.index.get-data-dashboard-purchase') }}",
                    success: function({
                        data
                    }) {
                        data_dashboard = data;
                        renderDataDashboard();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            };

            getDataDatsboard();

            $('#reload-data').click(function(e) {
                e.preventDefault();
                getDataDatsboard()
            });
        });
    </script>
@endpush
