<div class="row">
    <div class="col-xl-2">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <h2 class="my-0" id="user-count">0</h2>
                <p class="mb-0 text-fade">User</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <h2 class="my-0" id="customer-count">0</h2>
                <p class="mb-0 text-fade">Customer</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <h2 class="my-0" id="item-count">0</h2>
                <p class="mb-0 text-fade">Item</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="box scale">
            <div class="box-body text-primary">
                <div class="d-flex justify-content-around">
                    <div class="text-center">
                        <h2 class="my-0" id="so-this-month-count">0</h2>
                        <p class="mb-0 text-fade">SO bulan ini</p>
                    </div>
                    <div class="b-1"></div>
                    <div class="text-center">
                        <h2 class="my-0" id="so-total">0</h2>
                        <p class="mb-0 text-fade">Total SO</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="box scale">
            <div class="box-body text-primary">
                <div class="d-flex justify-content-around">
                    <div class="text-center">
                        <h2 class="my-0" id="po-this-month-count">0</h2>
                        <p class="mb-0 text-fade">PO bulan ini</p>
                    </div>
                    <div class="b-1"></div>
                    <div class="text-center">
                        <h2 class="my-0" id="po-total">0</h2>
                        <p class="mb-0 text-fade">Total PO</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="box scale">
            <div class="box-body text-primary text-center">
                <h2 class="my-0">100.000K</h2>
                <p class="mb-0 text-fade">Target Penjualan</p>
            </div>
        </div>
    </div>
</div>

<div class="row flex-row-reverse">
    <div class="col-xl-4">
        <div class="col-12">
            <div class="box scale">
                <div class="box-body" id="chart2">

                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="row">
            <div class="col-xl-6">
                <div class="box ">
                    <div class="box-header with-border">
                        <h4 class="box-title">Newest Purchase Order</h4>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 dash-table" id="po-today">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Newest Sale Order</h4>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 dash-table" id="so-today">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body" id="monthly">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        $(document).ready(() => {

            const numberWithCommas = (x) => {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            const displaySaleOrder = ({
                latest_so
            }) => {
                let html = '';
                latest_so.map((so, key) => {
                    html += `
                        <tr>
                            <td><a href="{{ route('admin.sales-order.index') }}/${so.id}" class="text-primary">${so.nomor_so.substring(0,7) + "..."}</a></td>
                            <td><span class="text-muted text-nowrap">${so.created_at}</span> </td>
                            <td>${so.customer.nama.substring(0,7) + "..."}</td>
                            <td>${numberWithCommas(so.total)}</td>
                        </tr>
                    `;
                })
                $('#so-today').html(html);
            }

            const displayPurchaseOrder = ({
                latest_po
            }) => {
                let html = '';
                latest_po.map((po, key) => {
                    html += `
                        <tr>
                            <td><a href="{{ route('admin.purchase-order.index') }}/${po.id}" class="text-primary">${po.nomor_po.substring(0,7) + "..."}</a></td>
                            <td><span class="text-muted text-nowrap">${po.created_at}</span> </td>
                            <td>${po.customer.nama.substring(0,7) + "..."}</td>
                            <td>${numberWithCommas(po.sub_total_after_tax)}</td>
                        </tr>
                    `;
                });
                $('#po-today').html(html);
            }

            const displayCardData = ({
                user,
                customer,
                item,
                so_this_month_count,
                so_total,
                po_this_month_count,
                po_total
            }) => {
                $('#user-count').html(user);
                $('#customer-count').html(customer);
                $('#item-count').html(item);
                $('#so-this-month-count').html(so_this_month_count);
                $('#so-total').html(so_total);
                $('#po-this-month-count').html(po_this_month_count);
                $('#po-total').html(po_total);
                console.log(po_total)
            }

            const syncData = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('admin.index.get-data-dashboard') }}",
                    success: ({
                        data
                    }) => {
                        displayCardData(data);
                        displaySaleOrder(data);
                        displayPurchaseOrder(data);
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
        });
    </script>

    {{-- chart --}}
    <script>
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
            labels: ["Stock Limit"]
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

        var options = {
            chart: {
                height: 280,
                type: "radialBar"
            },

            series: [10],

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
            labels: ["Lost Precentage"]
        };

        var chart = new ApexCharts(document.querySelector("#chart2"), options);
        chart.render();

        var options = {
            chart: {
                type: 'bar'
            },
            series: [{
                data: [{
                        x: 'January',
                        y: Math.floor(Math.random() * 100),
                    },
                    {
                        x: 'February',
                        y: Math.floor(Math.random() * 100),
                    },
                    {
                        x: 'March',
                        y: Math.floor(Math.random() * 100),
                    },
                    {
                        x: 'Appril',
                        y: Math.floor(Math.random() * 100),
                    },
                    {
                        x: 'Mei',
                        y: Math.floor(Math.random() * 100),
                    },
                    {
                        x: 'June',
                        y: Math.floor(Math.random() * 100),
                    },
                    {
                        x: 'July',
                        y: Math.floor(Math.random() * 100),
                    },
                    {
                        x: 'August',
                        y: Math.floor(Math.random() * 100),
                    },
                    {
                        x: 'September',
                        y: Math.floor(Math.random() * 100),
                    },
                    {
                        x: 'October',
                        y: Math.floor(Math.random() * 100),
                    },
                    {
                        x: 'November',
                        y: Math.floor(Math.random() * 100),
                    },
                    {
                        x: 'December',
                        y: Math.floor(Math.random() * 100),
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
            labels: ["PO Sale Stats This Year"]
        };

        var chart = new ApexCharts(document.querySelector("#monthly"), options);
        chart.render();
    </script>
@endpush
