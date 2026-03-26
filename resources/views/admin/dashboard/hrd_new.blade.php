<div class="row">
    <div class="col-md-12 mb-3">
        <h3>HRD Dashboard</h3>
    </div>
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between">
                    <h4 class="mt-0 fw-600 text-success" id="total_employees">0</h4>
                    <div class="w-30 h-30 bg-success rounded-circle text-center fs-16 l-h-30"><i class="fa fa-user"></i></div>
                </div>
                <p class="fs-14 mt-10">Total Pegawai</p>
            </div>
        </div>
    </div>
    <div class="col-md-12"></div>
    <div class="col-xl-4 col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Pegawai Cuti & Izin Hari Ini</h4>
            </div>
            <div class="box-body pt-0" style="min-height: 300px;max-height: 300px;overflow-y: scroll;">
                <div class="table-responsive">
                    <table width="100%" class="table">
                        <tbody id="leave_data"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Dokumen Akan Berakhir</h4>
            </div>
            <div class="box-body pt-0" style="min-height: 300px;max-height: 300px;overflow-y: scroll;">
                <div class="table-responsive">
                    <table width="100%" class="table">
                        <tbody id="document_data"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Kontrak Akan Berakhir</h4>
            </div>
            <div class="box-body pt-0 px-0" style="min-height: 300px;max-height: 300px;overflow-y: scroll; border-radius: 0 !important">
                <div class="table-responsive">
                    <table class="table mb-0 table-stripped table-danger">
                        <tbody id="contract_extension_data">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12"></div>
    <div class="col-xl-4">
        <div class="box">
            <div class="box-header">
                <h4 class="box-title">Pegawai Berdasarkan Jenis Kelamin</h4>
            </div>
            <div class="box-body">
                <div style="overflow-y: scroll">
                    <div id="gender-diversity" style="max-height: 500px;min-height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="box">
            <div class="box-header">
                <h4 class="box-title">Pegawai Berdasarkan Jabatan</h4>
            </div>
            <div class="box-body">
                <div style="overflow-y: scroll">
                    <div id="job-level" style="max-height: 500px;min-height: 500px;"></div>
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
                $.ajax({
                    url: "{{ route('admin.index.get-data-dashboard-hrd') }}",
                    success: function(data) {
                        $('#total_employees').html(numeral(data.total_employees).format('0,0'));
                        let leave = '';
                        $.each(data.leaves, function(index, value) {
                            leave += `<tr>
                                <td>
                                    <a href="${value.link}" target="_blank">${value.employee_name}</a><br>
                                </td>
                                <td>${value.cause}</td>
                                <td class="text-end">${value.badge}</td>
                                </tr>`;
                        });

                        $('#leave_data').html(leave);

                        let document = '';
                        $.each(data.documents, function(index, value) {
                            document += `<tr>
                                <td>
                                    <b>${value.name}</b> </br>
                                    <span class="text-${value.status} text-capitalize">${value.status_label}</span>
                                </td>
                                <td>
                                    <a href="${value.link}" target="_blank">
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>`;
                        });

                        $('#document_data').html(document);

                        let contract_extension = '';
                        $.each(data.contract_extensions, function(index, value) {
                            contract_extension += `<tr>
                                <td>
                                    <b>${value.employee_name}</b> </br>
                                    <small class="text-capitalize">${value.status}</small>
                                </td>
                                <td>
                                    <a href="${value.link}" target="_blank">
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>`;
                        });

                        $('#contract_extension_data').html(contract_extension);


                    },
                    error: function(error) {
                        console.log(error);
                    }
                });

                var options = {
                    series: {!! $countGender !!},
                    chart: {
                        type: 'donut',
                    },
                    labels: ["Laki-Laki", "Perempuan"],
                    legend: {
                        position: 'right', // set the legend position to bottom
                    },
                    responsive: [{
                        options: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                var chart = new ApexCharts(document.querySelector("#gender-diversity"), options);
                chart.render()

                var jobLevelOptions = {
                    series: {!! $countPosisi !!},
                    chart: {
                        type: 'pie',
                    },
                    legend: {
                        position: 'right', // set the legend position to bottom
                    },
                    labels: {!! $countPosisiName !!},
                    responsive: [{
                        options: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                var jobLevelChart = new ApexCharts(document.querySelector("#job-level"), jobLevelOptions);
                jobLevelChart.render()
            }

            getData();


            $('#reload-data').click(function(e) {
                e.preventDefault();
                getData();
            });
        });
    </script>
@endpush
