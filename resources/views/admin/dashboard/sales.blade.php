<div class="row">
    <div class="col-xl-4">

        <div class="row">
            <div class="col-12">
                <div class="box scale">
                    <div class="box-body">
                        <div class="d-flex justify-content-between">
                            <h4 class="fw-500 text-danger underline">Sales Order</h4>
                            <div class="w-40 h-40 bg-danger rounded-circle text-center fs-20 l-h-40 float-end"><i class="fa-solid fa-share-from-square"></i></div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="col-lg-6">
                                <div class="text-center">
                                    <h2 class="my-0 fs-28 fw-600 text-danger" id="sale-order-count">0</h2>
                                    <p class="fs-12 m-0 text-secondary">This Month</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-center">
                                    <h2 class="my-0 fs-28 fw-600 text-danger" id="sale-order-waiting-count">0</h2>
                                    <p class="fs-12 m-0 text-secondary">Waiting Approval</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12">
                <div class="box scale">
                    <div class="box-body text-primary text-center">
                        <h2 class="my-0" id="customer-count-sales">0</h2>
                        <p class="mb-0 text-fade">Customer</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-12">
                <div class="box">
                    <div class="box-body" id="chart-target-sales">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">

        <div class="col-12">
            <div class="box">
                <div class="box-body" id="carts-monthly-sales-this-year">

                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-xl-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="table-responsive">
                                <x-table id="newest-sales-order-sales">
                                    <x-slot name="table_head">
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                    </x-slot>
                                </x-table>
                            </div>
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
            let sales_data_dasboard = [];

            const resetSaleDashboard = () => {
                $('#carts-monthly-sales-this-year').html('');
                $('#newest-sales-order-sales tbody').html('');
                $('#sale-order-count').html(0);
                $('#sale-order-waiting-count').html(0);
                $('#customer-count-sales').html(0);
            }

            const getDataSales = () => {
                $.ajax({
                    url: "{{ route('admin.index.get-data-dashboard-sales') }}",
                    success: function({
                        data
                    }) {
                        sales_data_dasboard = data;
                        loadSaleDashboard()
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

            const loadSaleDashboard = () => {

                let {
                    sales,
                    spending_sales,
                    customer,
                    sales_orders
                } = sales_data_dasboard;
                $('#sale-order-count').html(sales.this_month);
                $('#sale-order-waiting-count').html(sales.waiting_approval);
                $('#customer-count-sales').html(customer);

                let data_sales = spending_sales.map((data, index) => {
                    return {
                        x: index + 1,
                        y: data,
                    }
                });

                var options = {
                    chart: {
                        type: 'bar'
                    },
                    series: [{
                        data: data_sales
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
                    labels: ["Sale Stats This Year"]
                };

                var chart = new ApexCharts(document.querySelector("#carts-monthly-sales-this-year"), options);
                chart.render();

                let table_sales = sales_orders.map((sale, index) => {
                    return `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${sale.nomor_so}</td>
                            <td>${sale.customer.nama}</td>
                            <td>${sale.total}</td>
                            <td>${sale.status}</td>
                            <td>${sale.created_at}</td>
                        </tr>`;
                });

                $('#newest-sales-order-sales tbody').append(table_sales);
            }


            var options = {
                chart: {
                    height: 280,
                    type: "radialBar"
                },

                series: [20],

                plotOptions: {
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
                labels: ["Target Sale"]
            };

            var chart = new ApexCharts(document.querySelector("#chart-target-sales"), options);
            chart.render();

            getDataSales();

            $('#reload-data').click(function(e) {
                e.preventDefault();
                resetSaleDashboard();
                getDataSales();
            });
        });
    </script>
@endpush
