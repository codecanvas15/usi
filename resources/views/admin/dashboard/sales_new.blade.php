<div class="row">
    <div class="col-md-12 mb-3">
        <h3>Sales Dashboard</h3>
    </div>
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-light">Trading</small>
                        <h4 class="mt-0 fw-600 text-success" id="current_month_sales_order_total">0</h4>
                        <hr>
                        <small class="text-light">General</small>
                        <h4 class="my-0 fw-600 text-success" id="current_month_sales_order_general_total">0</h4>
                    </div>
                    <div class="w-30 h-30 bg-success rounded-circle text-center fs-14 l-h-30"><small>Rp</small></div>
                </div>
                <p class="fs-14 mt-10">Sales Order Bulan Ini</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-light">Trading</small>
                        <h4 class="mt-0 fw-600 text-primary" id="current_month_invoice_trading_total">0</h4>
                        <hr>
                        <small class="text-light">General</small>
                        <h4 class="my-0 fw-600 text-primary" id="current_month_invoice_general_total">0</h4>
                    </div>
                    <div class="w-30 h-30 bg-primary rounded-circle text-center fs-14 l-h-30"><small>Rp</small></div>
                </div>
                <p class="fs-14 mt-10">Penjualan Bulan Ini</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-light">Trading</small>
                        <h4 class="mt-0 fw-600 text-warning" id="account_receivable_total">0</h4>
                        <hr>
                        <small class="text-light">General</small>
                        <h4 class="my-0 fw-600 text-warning" id="account_receivable_general_total">0</h4>
                    </div>
                    <div class="w-30 h-30 bg-warning rounded-circle text-center fs-14 l-h-30"><small>Rp</small></div>
                </div>
                <p class="fs-14 mt-10">Total Piutang</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-light">Trading</small>
                        <h4 class="mt-0 fw-600 text-danger" id="account_receivable_due_total">0</h4>
                        <hr>
                        <small class="text-light">General</small>
                        <h4 class="my-0 fw-600 text-danger" id="account_receivable_due_general_total">0</h4>
                    </div>
                    <div class="w-30 h-30 bg-danger rounded-circle text-center fs-14 l-h-30"><small>Rp</small></div>
                </div>
                <p class="fs-14 mt-10">Total Piutang Jatuh Tempo</p>
            </div>
        </div>
    </div>
</div>
<div class="row row-eq-height">
    <div class="col-xl-7 col-12">
        <div class="box">
            <div class="box-body" style="position: relative;">
                <div id="trading_sale_graph" style="min-height: 300px;">

                </div>
                <div class="resize-triggers">
                    <div class="expand-trigger">
                        <div style="width: 554px; height: 266px;"></div>
                    </div>
                    <div class="contract-trigger"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5 col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Sales Order Terbaru</h4>
            </div>
            <div class="box-body pt-0 " style="min-height: 300px; max-height: 300px; overflow-y: scroll">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody id="recent_sale_orders">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script src="{{ asset('js/numeral.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const getData = () => {
                $('#trading_sale_graph').html('');
                $.ajax({
                    url: "{{ route('admin.index.get-data-dashboard-trading') }}",
                    success: function(data) {
                        $('#current_month_sales_order_total').text(formatRupiahWithDecimal(data.current_month_sales_order_total));
                        $('#current_month_sales_order_general_total').text(formatRupiahWithDecimal(data.current_month_sales_order_general_total));
                        $('#current_month_invoice_trading_total').text(formatRupiahWithDecimal(data.current_month_invoice_trading_total));
                        $('#current_month_invoice_general_total').text(formatRupiahWithDecimal(data.current_month_invoice_general_total));
                        $('#account_receivable_total').text(formatRupiahWithDecimal(data.account_receivable_total));
                        $('#account_receivable_general_total').text(formatRupiahWithDecimal(data.account_receivable_general_total));
                        $('#account_receivable_due_total').text(formatRupiahWithDecimal(data.account_receivable_due_total));
                        $('#account_receivable_due_general_total').text(formatRupiahWithDecimal(data.account_receivable_due_general_total));

                        let recent_sale_order = '';
                        $.each(data.recent_sale_orders, function(index, value) {
                            let link = base_url + "/sales-order/" + value.id;
                            recent_sale_order += `<tr>
                                                    <td>
                                                        <a href="${link}" target="_blank">${value.nomor_so}</a><br>
                                                        ${value.customer_name}
                                                    </td>
                                                    <td>${value.badge}</td>
                                                </tr>`;
                        })

                        $('#recent_sale_orders').html(recent_sale_order);

                        var options = {
                            height: '300px',
                            series: [data.sale_graphs, data.sale_graph_generals],
                            chart: {
                                type: 'line',
                                dropShadow: {
                                    enabled: true,
                                    color: '#000',
                                    top: 18,
                                    left: 7,
                                    blur: 10,
                                    opacity: 0.2
                                },
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ["#6610f2", "#ec3136"],
                            dataLabels: {
                                enabled: true,
                                formatter: function(val, index) {
                                    return numeral(val).format('0 a');
                                }
                            },
                            stroke: {
                                curve: 'smooth'
                            },
                            title: {
                                text: 'Grafik Penjualan',
                                align: 'left'
                            },
                            grid: {
                                borderColor: '#e7e7e7',
                                row: {
                                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                                    opacity: 0.5
                                },
                            },
                            markers: {
                                size: 1
                            },
                            xaxis: {
                                categories: data.months,
                            },
                            yaxis: {
                                forceNiceScale: false,
                                min: 100000,
                                labels: {
                                    formatter: function(val, index) {
                                        return numeral(val).format('0 a');
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                horizontalAlign: 'right',
                                floating: true,
                                offsetY: -25,
                                offsetX: -5,
                            }

                        };

                        var chart = new ApexCharts(document.querySelector("#trading_sale_graph"), options);
                        chart.render();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

            getData();


            $('#reload-data').click(function(e) {
                e.preventDefault();
                getData();
            });
        });
    </script>
@endpush
