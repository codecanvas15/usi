<div class="row">
    <div class="row justify-content-end mb-4">
        <div class="col-xl-3 d-flex gap-items-2">
            <input type="month" class="form-control" name="date_id" id="filter_month_finance">
            <x-button color="info" icon="search" id="filter_submit_finance" fontawesome></x-button>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-6">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">List Pengajuan Pembayaran</h4>
                </div>
                <div class="box-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 dash-table" id="po-today">
                            <thead>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pengajuan</th>
                            </thead>
                                <tbody id="lpp">
    
                                </tbody>
                            </table>
                            <a href="#" class="text-primary p-4" id="showMoreFundSubmissions">Show more...</a>
                        </div>
                    </div>
                </div>
            </div>
        <div class="col-xl-6">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">List Pencairan</h4>
                </div>
                <div class="box-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 dash-table">
                            <thead>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Tanggal</th>
                            </thead>
                                <tbody id="fund_submissions_disbursements">
                                </tbody>
                            </table>
                            <a href="#" class="text-primary p-4" id="showMoreFundSubmissionDisbursements">Show more...</a>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    
    <div class="row">
        <div class="col-xl-4">
            <div class="box scale">
                <div class="box-body text-primary text-center">
                    <p class="mb-0 text-fade">Total Pembayaran Disetujui</p>
                    <h2 class="my-0" id="total_pembayaran_disetujui">0</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="box scale">
                <div class="box-body text-primary text-center">
                    <p class="mb-0 text-fade">Total Pembayaran Ditolak</p>
                    <h2 class="my-0" id="total_pembayaran_ditolak">0</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="box scale">
                <div class="box-body text-primary text-center">
                    <p class="mb-0 text-fade">Total Pencairan</p>
                    <h3 class="my-0" id="total_pencairan">Rp 0,00</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        $(document).ready(() => {
            $('#showMoreFundSubmissionDisbursements').hide()
            $('#showMoreFundSubmissions').hide()

            //
            // const numberWithCommas = (x) => {
            //     return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            // }

            $('#showMoreFundSubmissions').click(function (e) {
                e.preventDefault()
                syncData($('#filter_month'), 5, 0)
            })

            $('#showMoreFundSubmissionDisbursements').click(function (e) {
                e.preventDefault()
                syncData($('#filter_month'), 0, 5)
            })

            const displayLPPembayaran = (fundSubmissions) => {
                let html = '';
                if (fundSubmissions.length <= 5){
                    $('#showMoreFundSubmissions').hide()
                } else {
                    $('#showMoreFundSubmissions').show()

                }
                fundSubmissions.map((lpp, key) => {
                    html += `
                        <tr>
                            <td><a href="{{ route('admin.fund-submission.index') }}/${lpp?.id}" class="text-primary">${lpp.code}</a></td>
                            <td><span class="text-muted text-nowrap">${dateFormat(lpp?.date)}</span> </td>
                            <td>${lpp?.item}</td>
                        </tr>
                    `;
                })
                $('#lpp').html(html);
            }

            const displayPencairan = (fundSubmissionDisbursements) => {
                let html = '';
                if (fundSubmissionDisbursements.length <= 5){
                    $('#showMoreFundSubmissionDisbursements').hide()
                } else {
                    $('#showMoreFundSubmissionDisbursements').show()
                }
                fundSubmissionDisbursements.map((fsb, key) => {
                    html += `
                        <tr>
                            <td>${key + 1}</td>
                            <td><a href="{{ route('admin.fund-submission.index') }}/${fsb.id}" class="text-primary">${fsb.code}</a> </td>
                            <td>${dateFormat(fsb.date)}</td>
                        </tr>
                    `;
                });
                $('#fund_submissions_disbursements').html(html);
            }

            const displayCardData = (
                amountFundSubmissionApprove,
                amountFundSubmissionReject,
                fundSubmissionsAmount
            ) => {
                $('#total_pembayaran_disetujui').html(amountFundSubmissionApprove);
                $('#total_pembayaran_ditolak').html(amountFundSubmissionReject);
                $('#total_pencairan').html(currencyFormatID(fundSubmissionsAmount));
            }

            $('#filter_submit_finance').click(function(e) {
                e.preventDefault()
                syncData($('#filter_month_finance').val())
            })

            const syncData = (date = '', submissions = 0, submissionDisbursements = 0) => {
                $.ajax({
                    type: "get",
                    url: `{{ route('admin.index.get-data-dashboard-finance') }}?date=${date}&submissions=${submissions}&submission_disbursements=${submissionDisbursements}`,
                    success: ({
                        data
                    }) => {
                        let {
                            fundSubmissionDisbursements,
                            fundSubmissions,
                            amountFundSubmissionApprove,
                            amountFundSubmissionReject,
                            fundSubmissionsAmount
                        } = data
                        displayCardData(amountFundSubmissionApprove, amountFundSubmissionReject, fundSubmissionsAmount);
                        displayLPPembayaran(fundSubmissions);
                        displayPencairan(fundSubmissionDisbursements);
                    },
                    error: (error) => {
                        console.log(error)
                    }
                });
            }

            const currencyFormatID = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: "currency",
                    currency: "IDR"
                }).format(number)
            }

            const dateFormat = (date) => {
                let d = new Date(date)
                return d.toLocaleDateString('id-ID', {
                    weekday: 'long'
                }) + ', ' + d.getDate() + ' ' + d.toLocaleDateString('id-ID', {
                    month: 'long'
                }) + ' ' + d.getFullYear()
            }

            $('#reload-data').click(function(e) {
                e.preventDefault();
                syncData();
            });

            syncData()
        });
    </script>
@endpush
