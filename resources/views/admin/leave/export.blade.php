<!DOCTYPE html>
<html>

<head>
    <title></title>
    <style type="text/css">
        body {
            font-size: 14px;
            color: #000;
        }

        table {
            border-spacing: 0px;
        }

        span {
            font-size: 12px;
        }

        #footer {
            position: fixed;
            left: 0px;
            bottom: 0;
            right: 0px;
        }

        #footer .page:after {
            content: counter(page, upper-roman);
        }

        td {
            vertical-align: top;
        }

        .border-bottom {
            border-bottom: 1px solid #000;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    <table style="width: 100%">
        <tr>
            <td style="width: 65%; vertical-align: top">
                <h1 class="font-medium-1 text-uppercase mb-0">{{ getCompany()->name }}</h1>
                <p class="font-small-2 text-uppercase mt-0">{{ getCompany()->address }}</p>
            </td>
            <td style="width: 25%">
                {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
            </td>
        </tr>
        <tr>
            <td colspan="2" class="text-center">
                <h1 class="font-medium-1 text-capitalize mb-0">Permohonan izin tidak masuk kerja / cuti</h1>
                <p class="font-small-2 my-0"><i>Request For Time Out/Leave</i></p>
            </td>
        </tr>
    </table>

    <table class="mt-2">
        <tr>
            <td>
                <table>
                    <tr>
                        <td class="font-small-2">
                            Nama
                            <br>
                            <i class="font-small-1">Name</i>
                        </td>
                        <td class="font-small-2 border-bottom valign-bottom">
                            : {{ $model->employee->name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="font-small-2">
                            Nomor Karyawan
                            <br>
                            <i class="font-small-1">Employee No.</i>
                        </td>
                        <td class="font-small-2 border-bottom valign-bottom">
                            : {{ $model->employee->NIK }}
                        </td>
                    </tr>
                    <tr>
                        <td class="font-small-2">
                            Department
                            <br>
                            <i class="font-small-1">Department</i>
                        </td>
                        <td class="font-small-2 border-bottom valign-bottom">
                            : {{ $model->employee->division->name }}
                        </td>
                    </tr>
                </table>
            </td>
            <td width="25%"></td>
            <td>
                <table>
                    <tr>
                        <td class="font-small-2">
                            Tanggal
                            <br>
                            <i class="font-small-1">Date</i>
                        </td>
                        <td class="font-small-2 border-bottom valign-bottom">
                            : {{ localDate($model->date ?? $model->created_at) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="font-small-2">
                            Cabang
                            <br>
                            <i class="font-small-1">Branch</i>
                        </td>
                        <td class="font-small-2 border-bottom valign-bottom">
                            : {{ $model->branch->name }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr>
    <table>
        <tbody>
            <tr>
                <td class="border-bottom">
                    <h1 class="font-medium-1 text-capitalize mb-0">alasan/keperluan cuti</h1>
                    <i class="font-small-1">Reason for Leave</i>
                    <p class="font-small-2">{{ $model->cause }}</p>
                </td>
                <td>
                    <table>
                        <tbody>
                            <tr>
                                <td width="10%"><input type="checkbox" {{ $model->necessary == 'vacation' ? 'checked' : '' }} style="height:16px; margin-top:0px"></td>
                                <td class="font-small-2">
                                    Liburan
                                    <br>
                                    <i class="font-small-1">Vacation</i>
                                </td>
                            </tr>
                            <tr>
                                <td width="10%"><input type="checkbox" {{ $model->necessary == 'illnes' ? 'checked' : '' }} style="height:16px; margin-top:0px"></td>
                                <td class="font-small-2">
                                    Sakit
                                    <br>
                                    <i class="font-small-1">Illnes</i>
                                </td>
                            </tr>
                            <tr>
                                <td width="10%"><input type="checkbox" {{ $model->necessary == 'maternity' ? 'checked' : '' }} style="height:16px; margin-top:0px"></td>
                                <td class="font-small-2">
                                    Melahirkan
                                    <br>
                                    <i class="font-small-1">Maternity </i>
                                </td>
                            </tr>
                            <tr>
                                <td width="10%"><input type="checkbox" {{ $model->necessary == 'others' ? 'checked' : '' }} style="height:16px; margin-top:0px"></td>
                                <td class="font-small-2">
                                    Lain-lain
                                    <br>
                                    <i class="font-small-1">Others </i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>
                            <td width="10%"><input type="checkbox" {{ $model->type == 'cuti' ? 'checked' : '' }} style="height:16px; margin-top:0px"></td>
                            <td class="font-small-2">
                                Cuti
                                <br>
                                <i class="font-small-1">Unpaid Leave</i>
                            </td>
                            <td width="10%"><input type="checkbox" {{ $model->type == 'izin' ? 'checked' : '' }} style="height:16px; margin-top:0px"></td>
                            <td class="font-small-2">
                                Izin
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h1 class="font-medium-1 text-capitalize mb-0">penjelasan</h1>
                                <i class="font-small-1">Explain</i>
                                <p class="font-small-2">{{ $model->note }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <hr>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <h1 class="font-medium-1 text-capitalize mb-0">Masa tidak masuk kerja :</h1>
                    <i class="font-small-1">Period off time off (check in corresponding date)</i>
                </td>
            </tr>
            <tr>
                <td>
                    <table>
                        <tbody>
                            <tr>
                                <td class="font-small-2">
                                    <b>Bulan</b>
                                    <br>
                                    <i class="font-small-1">Month</i>
                                </td>
                                <td class="border-bottom valign-bottom">: {{ Carbon\Carbon::parse($model->start_date)->translatedFormat('F') }}</td>
                            </tr>
                            <tr>
                                <td class="font-small-2">
                                    <b>Tanggal</b>
                                    <br>
                                    <i class="font-small-1">Date</i>
                                </td>
                                <td class="border-bottom valign-bottom">: {{ Carbon\Carbon::parse($model->from_date)->translatedFormat('d/m/Y') }} - {{ Carbon\Carbon::parse($model->to_date)->translatedFormat('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="font-small-2">
                                    <b>Jumlah Cuti</b>
                                    <br>
                                    <i class="font-small-1">Val Leave Days</i>
                                </td>
                                <td class="border-bottom valign-bottom">: {{ $model->day }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td>

                </td>
                <td>
                    <table>
                        <tr>
                            <td colspan="2">
                                <h1 class="font-medium-1 text-capitalize mb-0">alamat selama cuti</h1>
                                <i class="font-small-1">Contact Addres During Leave</i>
                                <p class="font-small-2">{{ $model->address }} <i class="font-small-1">Phone: </i> {{ $model->phone_number }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <hr>
    <table>
        <tbody>
            <tr>
                <td>
                    <h1 class="font-medium-1 text-capitalize mb-0">Status cuti sampai saat ini :</h1>
                    <i class="font-small-1">Leave status to date</i>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table-bordered mt-2">
        <tbody>
            <tr>
                <td class="font-small-2" width="20%">
                    <b>(a) <br> Hak Cuti Tahun Ini</b>
                    <br>
                    <i class="font-small-1">Entitlement this year</i>
                </td>
                <td class="font-small-2" width="20%">
                    <b>(b) <br> Cuti Yang Telah Di ambil Tahun Ini </b>
                    <br>
                    <i class="font-small-1">Leave taken this year</i>
                </td>
                <td class="font-small-2" width="20%">
                    <b>(c) <br> Sisa</b>
                    <br>
                    <i class="font-small-1">Leave balance</i>
                </td>
                <td class="font-small-2" width="20%">
                    <b>(d) <br> Permohonan Cuti Bulan Ini</b>
                    <br>
                    <i class="font-small-1">Leave Request This Month</i>
                </td>
                <td class="font-small-2" width="20%">
                    <b>(e) <br> Jumlah Cuti Yang Tersisa </b>
                    <br>
                    <i class="font-small-1">Balance After This Request</i>
                </td>
            </tr>
            <tr>
                <td class="fonts-small-1 text-center">(a)</td>
                <td class="fonts-small-1 text-center">(b)</td>
                <td class="fonts-small-1 text-center">(c)=(a)-(b)</td>
                <td class="fonts-small-1 text-center">(d)</td>
                <td class="fonts-small-1 text-center">(e)=(c)-(d)</td>
            </tr>
            <tr>
                <td class="fonts-small-1 text-center">
                    {{ $model->employee->leave }}
                </td>
                <td class="fonts-small-1 text-center">
                    {{ $leave_taken }}
                </td>
                <td class="fonts-small-1 text-center">
                    {{ $model->employee->leave - $leave_taken }}
                </td>
                <td class="fonts-small-1 text-center">
                    {{ $model->day }}
                </td>
                <td class="fonts-small-1 text-center">
                    {{ $last_leave_remaining }}
                </td>
            </tr>
        </tbody>

    </table>
    <table class="table-bordered mt-2">
        <tr>
            <td class="font-small-2 text-center" width="25%">
                Diajukan Oleh,
            </td>
            <td class="font-small-2 text-center" width="25%">
                Mengetahui,
            </td>
            <td class="font-small-2 text-center" width="25%">
                Mengetahui,
            </td>
            <td class="font-small-2 text-center" width="25%">
                Menyetujui,
            </td>
        </tr>
        <tr>
            <td class="font-small-2">
                <br><br><br><br>
                <center>
                    <span>{{ $model->employee->name }}</span>
                </center>
            </td>
            <td class="font-small-2">
                <br><br><br><br>
            </td>
            <td></td>
            <td></td>
        </tr>

    </table>
</body>

</html>
