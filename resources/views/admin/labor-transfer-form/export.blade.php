<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Labor Transfer Form - {{ Str::upper(config('app.name', 'Unitetd Shipping Indonesia')) }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 14px;
            color: #000;
        }

        @page {
            margin: 28px;
        }

        #footer {
            position: fixed;
            left: 0px;
            bottom: 0;
            right: 0px;
        }

        .table-padding tr td {
            padding: 0px 5px;
        }

        .table tr th,
        .table tr td {
            padding: 4px 4px;
        }
    </style>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
</head>

<body style="font-size: 12pt;">
    <div class="row">
        <table class="table-padding" style="width: 100%;" cellspacing="0">
            <tr style="border: 2px solid black">
                <td width="25%" style="border-right: 2px solid black; border: 2px solid black" class="p-0 text-center">
                    <img src="{{ public_path('images/icon.png') }}" alt="">
                </td>
                <td width="75%">
                    <p style="font-size: 24pt; text-align: center;">Formulir Pemindahan Tenaga Kerja</p>
                </td>
            </tr>
            <tr style="border: 1px solid black; border-top: none">
                <td colspan="2" style="padding: 0;">
                    <table class="m-0" cellspacing="0">
                        <tr>
                            <td width="20%" style="border-right: 1px solid black; padding-left: 5px">
                                Form-HRD-05-08
                            </td>
                            <td class="text-center" style="padding: 5px 0">
                                No. Memo Intern :
                                <span>{{ addThreeZeroOnFront($model->id) }}</span>/<span>{{ config('app.short_name') }}</span>/<span>{{ date('m', strtotime($model->created_at)) }}</span>/<span>{{ date('Y', strtotime($model->created_at)) }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="border: 1px solid black; border-top: none">
                <td style="padding: 10px 5px" colspan="2">
                    Nama Karyawan : {{ $model->employee?->name }}
                </td>
            </tr>
            <tr style="border: 1px solid black; border-top: none">
                <td colspan="2" class="p-0">
                    <table cellspacing="0">
                        <tr style="border-bottom: 1px solid black">
                            <td width="50%" height="30" class="border-end" style="vertical-align: top">
                                Dari
                            </td>
                            <td width="50%" style="vertical-align: top">
                                Dipindah ke
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid black">
                            <td class="border-end">
                                <table cellspacing="0">
                                    <tr>
                                        <td width="30%" style="padding: 5px 5px" class="border-end">PT</td>
                                        <td>{{ $model->from_company }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table cellspacing="0">
                                    <tr>
                                        <td width="30%" style="padding: 5px 5px" class="border-end">PT</td>
                                        <td>{{ $model->to_company }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid black">
                            <td class="border-end">
                                <table cellspacing="0">
                                    <tr>
                                        <td width="30%" style="padding: 5px 5px" class="border-end">Cabang</td>
                                        <td>{{ $model->from_branch_data?->name }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table cellspacing="0">
                                    <tr>
                                        <td width="30%" style="padding: 5px 5px" class="border-end">Cabang</td>
                                        <td>{{ $model->to_branch_data?->name }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="border-end">
                                <table cellspacing="0">
                                    <tr>
                                        <td width="30%" style="padding: 5px 5px" class="border-end">Dep./Bagian</td>
                                        <td>{{ $model->from_division_data?->name }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table cellspacing="0">
                                    <tr>
                                        <td width="30%" style="padding: 5px 5px" class="border-end">Dep./Bagian</td>
                                        <td>{{ $model->to_division_data?->name }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="border: 1px solid black">
                <td colspan="2" style="height: 150px; vertical-align: top">
                    Alasan Pemindahan : <br>
                    <span style="font-size: 10pt">
                        {{ $model->reason }}
                    </span>
                </td>
            </tr>
        </table>

        <p>Demikian disampaikan untuk mendapatkan persetujuan Pimpinan Perusahaan.</p>

        <table>
            <tr>
                <td width="30%">
                    <span>Surabaya, {{ date('d-m-Y', strtotime($model->created_at)) }}</span><br>
                    Diajukan oleh,
                    <br><br><br><br>
                    <p style="border-top: 1px solid black">{{ $model->submitted_by_data?->name }}</p>
                </td>
                <td width="40%"></td>
                <td width="30%" style="vertical-align: bottom">
                    <p>Mengetahui,</p>
                    <br><br><br>
                    <p style="border-top: 1px solid black">HRD</p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="vertical-align: bottom; text-align: center">
                    <p>Mengetahui,</p>
                    <br><br><br>
                    <p style="border-top: 1px solid black">Direktur</p>
                </td>
                <td></td>
            </tr>
        </table>
    </div>
</body>

</html>
