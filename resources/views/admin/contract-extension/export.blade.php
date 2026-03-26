<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contract Extension - {{ $model->code }}</title>
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

        .table tr th,
        .table tr td {
            padding: 4px 4px;
        }
    </style>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
</head>

<body style="font-size: 14px;">
    <div class="row">
        <table cellspacing="0" cellpadding="0" class="table" style="width: 100%">
            <tr>
                <td style="border: 0.5px solid black; width: 20%">
                    <img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" alt="" class="w-100">
                </td>
                <td style="border: 0.5px solid black; width: 80%">
                    <p class="text-center" style="font-size: 20px">Formulir Persetujuan Perpanjang/Tidak Perpanjang Kontrak</p>
                </td>
            </tr>
            <tr>
                <td style="border: 0.5px solid rgb(39, 38, 38); border-top: none;width: 20%;vertical-align: middle; text-align: center;">
                    From-HRD-05-09
                </td>
                <td style="border: 0.5px solid rgb(39, 38, 38); border-top: none;width: 20%;vertical-align: middle; text-align: center;">
                    No. Memo Intern : {{ $model->code }}/CE/{{ config('app.short_name') }}-HRD/{{ date('m', strtotime($model->created_at)) }}/{{ date('Y', strtotime($model->created_at)) }}
                </td>
            </tr>
            <tr style="border: 0.5px solid rgb(39, 38, 38); border-top: none;width: 20%;">
                <td colspan="2">
                    <table style="border: none">
                        <tr>
                            <td width="23%" style="border: none">Nama karyawan kontrak</td>
                            <td style="border: none; width: 1%">:</td>
                            <td style="border: none">{{ $model->employee->name }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border: 0.5px solid rgb(39, 38, 38); border-top: none;width: 20%; padding: 0; margin: 0">
                    <table>
                        <tr>
                            <td width="50%" style="border: none; border-right: 0.5px solid rgb(39, 38, 38)">
                                <table>
                                    <tr>
                                        <td width="10%" style="border: none">PT</td>
                                        <td width="10%" style="border: none">:</td>
                                        <td style="border: none">{{ getCompany()->name }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="50%" style="border: none">
                                <table>
                                    <tr>
                                        <td width="20%" style="border: none">Deb./Bag.</td>
                                        <td width="10%" style="border: none">:</td>
                                        <td style="border: none">{{ $model->division?->name }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border: 0.5px solid black; padding: 0; border-top: none">
                    <table cellspacing="0">
                        <tr>
                            <td style="border: none" rowspan="4">
                                Kontrak
                            </td>
                            <td style="border-left: 0.5px solid black"></td>
                            <td style="border-left: 0.5px solid black">Dari tanggal</td>
                            <td style="border-left: 0.5px solid black;">Sampai tanggal</td>
                        </tr>
                        <tr style="border-bottom: 0.5px solid black">
                            <td style="border:none;border-left: 0.5px solid black;">I</td>
                            <td style="border:none;border-left: 0.5px solid black;">{{ date('d-m-Y', strtotime($model->from_date)) }}</td>
                            <td style="border:none;border-left: 0.5px solid black;">{{ date('d-m-Y', strtotime($model->to_date)) }}</td>
                        </tr>
                        <tr style="border-bottom: 0.5px solid black">
                            <td style="border:none;border-left: 0.5px solid black;">II</td>
                            <td style="border:none;border-left: 0.5px solid black;"></td>
                            <td style="border:none;border-left: 0.5px solid black;"></td>
                        </tr>
                        <tr>
                            <td style="border:none;border-left: 0.5px solid black;">III</td>
                            <td style="border:none;border-left: 0.5px solid black;"></td>
                            <td style="border:none;border-left: 0.5px solid black;"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="border: 0.5px solid black">Setelah Memperhatikan : </td>
                <td style="padding: 0; border-bottom: none">
                    <table cellspacing="0">
                        <tr>
                            <td width="15%" style="border-right: 0.5px solid black"></td>
                            <td width="5%" style="border-right: 0.5px solid black">Baik</td>
                            <td width="5%" style="border-right: 0.5px solid black">Cukup</td>
                            <td width="7%" style="border-right: 0.5px solid black">Kurang Baik</td>
                            <td width="20%" style="border-right: 0.5px solid black">Keterangan</td>
                        </tr>
                        @foreach ($model->assesment as $assesment)
                            <tr>
                                <td style="border-right: 0.5px solid black">{{ Str::headline($assesment->type) }}</td>
                                <td style="border-right: 0.5px solid black;text-align: center">{{ $assesment->value == 'baik' ? 'v' : '' }}</td>
                                <td style="border-right: 0.5px solid black;text-align: center">{{ $assesment->value == 'cukup' ? 'v' : '' }}</td>
                                <td style="border-right: 0.5px solid black;text-align: center">{{ $assesment->value == 'kurang baik' ? 'v' : '' }}</td>
                                <td style="border-right: 0.5px solid black">{{ $assesment->note }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border: 0.5px solid black; border-top: none">
                    Mohon untuk : {!! $model->submission_status != 'perpanjang' ? '<s>diperpanjang</s>' : 'perpanjang' !!} / {!! $model->submission_status != 'tidak perpanjang' ? '<s>tidak diperpanjang</s>' : 'tidak diperpanjang' !!} kontrak ke : …………..
                </td>
            </tr>
        </table>
        <p>
            Demikian disampaikan untuk mendapatkan persetujuan Pimpinan Perusahaan.
        </p>
        <table>
            <tr>
                <td width="30%">
                    <p>Surabaya, {{ date('d-m-Y', strtotime($model->created_at)) }}</p>
                    <p>Diajukan Oleh,</p>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <p style="border-top: 1px solid black">{{ $model->user->name }}</p>
                </td>
                <td width="30%">

                </td>
                <td width="30%" style="vertical-align: bottom">
                    <p>Mengetahui,</p>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <p style="border-top: 1px solid black">HRD</p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <p>Menyetujui,</p>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <p style="border-top: 1px solid black">Direktur</p>
                </td>
                <td></td>
            </tr>
        </table>
        <p style="position: absolute; bottom: 0; left: 0">
            <span>*) Pilih Salah Satu</span>
            <br>
            <span>**) Isi dengan penguat diberi alasan-alasan diperpanjang/tidak diperpanjang.</span>
        </p>
    </div>
</body>

</html>
