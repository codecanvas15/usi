<!DOCTYPE html>
<html>

<head>
    <title></title>
    <style type="text/css">
        body {
            /* font-size: 10px; */
            font-size: 0.6rem !important;
            color: #000;
        }

        table {
            border-spacing: 0px;
        }

        span {
            font-size: 10px;
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
                <h1 class="font-small-1 text-uppercase mb-0">{{ getCompany()->name }}</h1>
                <p class="font-xsmall-3 text-uppercase mt-0">{{ getCompany()->address }}</p>
            </td>
            <td style="width: 25%">
                {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
            </td>
        </tr>
        <tr>
            <td colspan="2" class="text-center">
                <h1 class="font-small-1 text-uppercase">SURAT IJIN<br>{{ $model->letter_type_alias }}</h1>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td class="font-xsmall-3 pb-1" colspan="5">
                Saya yang bertanda tangan dibawah ini :
            </td>
        </tr>
        <tr>
            <td class="font-xsmall-3" width="20%">
                Nama
            </td>
            <td class="font-xsmall-3" colspan="4">
                : {{ $model->employee->name }}
            </td>
        </tr>
        <tr>
            <td class="font-xsmall-3">
                NIK
            </td>
            <td class="font-xsmall-3" colspan="4">
                : {{ $model->employee->NIK }}
            </td>
        </tr>
        <tr>
            <td class="font-xsmall-3">
                Divisi
            </td>
            <td class="font-xsmall-3" colspan="4">
                : {{ $model->employee->division->name }}
            </td>
        </tr>
        <tr>
            <td class="font-xsmall-3">
                Jabatan
            </td>
            <td class="font-xsmall-3 text-uppercase" colspan="4">
                : {{ $model->employee->position->nama }}
            </td>
        </tr>
        <tr>
            <td colspan="5" class="py-1"></td>
        </tr>
        @php
            $permission_date = $model->letter_date_start;
            if ($model->letter_type == 'leave early') {
                $permission_date = $model->letter_date_end;
            }
        @endphp
        <tr>
            <td class="font-xsmall-3" colspan="2">
                Bermaksud melaporkan bahwa hari ini
            </td>
            <td class="font-xsmall-3 border-bottom">
                : {{ Carbon\Carbon::parse($permission_date)->translatedFormat('l') }}
            </td>
            <td class="font-xsmall-3 text-center">
                Tanggal
            </td>
            <td class="font-xsmall-3 border-bottom">
                : {{ Carbon\Carbon::parse($permission_date)->translatedFormat('d-m-Y') }}
            </td>
        </tr>
        <tr>
            <td class="font-xsmall-3 p-0" colspan="2">
                <table>
                    <tr>
                        <td class="p-0" width="10%">
                            <input type="checkbox" {{ $model->letter_type == 'came too late' ? 'checked' : '' }} style="height:12px; margin-top:-4px">
                        </td>
                        <td class="p-0">
                            <span>Datang Terlambat</span>
                        </td>
                        <td class="text-end font-xsmall-3">Jam &nbsp;&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td class="font-xsmall-3 border-bottom valign-middle" colspan="3">
                : {{ $model->letter_type == 'came too late' ? Carbon\Carbon::parse($model->letter_date_start)->translatedFormat('H:i') : '' }}
            </td>
        </tr>
        <tr>
            <td class="font-xsmall-3 p-0" colspan="2">
                <table>
                    <tr>
                        <td width="10%">
                        </td>
                        <td>
                            <span>Alasan</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="font-xsmall-3 border-bottom" colspan="3">
                : {{ $model->letter_type == 'came too late' ? $model->letter_reason : '' }}
            </td>
        </tr>
        <tr>
            <td class="font-xsmall-3 p-0" colspan="2">
                <table>
                    <tr>
                        <td class="p-0" width="10%">
                            <input type="checkbox" {{ $model->letter_type == 'leave during working hours' ? 'checked' : '' }} style="height:12px; margin-top:-4px">
                        </td>
                        <td class="p-0">
                            <span>Ijin Pada Jam Kerja</span>
                        </td>
                        <td class="text-end font-xsmall-3">Jam &nbsp;&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td class="font-xsmall-3 border-bottom valign-middle" colspan="3">
                : {{ $model->letter_type == 'leave during working hours' ? Carbon\Carbon::parse($model->letter_date_start)->translatedFormat('H:i') : '' }} {{ $model->letter_type == 'leave during working hours' ? '-' . Carbon\Carbon::parse($model->letter_date_end)->translatedFormat('H:i') : '' }}
            </td>
        </tr>
        <tr>
            <td class="font-xsmall-3 p-0" colspan="2">
                <table>
                    <tr>
                        <td width="10%">
                        </td>
                        <td>
                            <span>Alasan</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="font-xsmall-3 border-bottom" colspan="3">
                : {{ $model->letter_type == 'leave during working hours' ? $model->letter_reason : '' }}
            </td>
        </tr>
        <tr>
            <td class="font-xsmall-3 p-0" colspan="2">
                <table>
                    <tr>
                        <td class="p-0" width="10%">
                            <input type="checkbox" {{ $model->letter_type == 'leave early' ? 'checked' : '' }} style="height:12px; margin-top:-4px">
                        </td>
                        <td class="vertical-align-middle">
                            <span>Pulang Cepat</span>
                        </td>
                        <td class="text-end font-xsmall-3">Jam &nbsp;&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td class="font-xsmall-3 border-bottom valign-middle" colspan="3">
                : {{ $model->letter_type == 'leave early' ? Carbon\Carbon::parse($model->letter_date_end)->translatedFormat('H:i') : '' }}
            </td>
        </tr>
        <tr>
            <td class="font-xsmall-3 p-0" colspan="2">
                <table>
                    <tr>
                        <td width="10%">
                        </td>
                        <td>
                            <span>Alasan</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="font-xsmall-3 border-bottom" colspan="3">
                : {{ $model->letter_type == 'leave early' ? $model->letter_reason : '' }}
            </td>
        </tr>
        <tr>
            <td colspan="5" class="pt-2 font-xsmall-3">
                {{ $model->branch->name }}, {{ Carbon\Carbon::parse($permission_date)->translatedFormat('d F Y') }}
            </td>
        </tr>
    </table>

    <table class="table-bordered mt-2">
        <tr>
            <td class="font-xsmall-3 text-center" width="25%">
                Pemohon
            </td>
            <td class="font-xsmall-3 text-center" width="25%">
                HRD
            </td>
            <td class="font-xsmall-3 text-center" width="25%">
                Mengetahui
            </td>
            <td class="font-xsmall-3 text-center" width="25%">
                Menyetujui
            </td>
        </tr>
        <tr>
            <td class="font-xsmall-3">
                <br><br><br><br>
                <center>
                    <span>{{ $model->employee->name }}</span>
                </center>
            </td>
            <td class="font-xsmall-3">
                <br><br><br><br>
            </td>
            <td></td>
            <td></td>
        </tr>

    </table>
</body>

</html>
