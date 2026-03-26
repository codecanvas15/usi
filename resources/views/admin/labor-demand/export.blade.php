<!DOCTYPE html>
<html>

<head>
    <title>Export Labor Demand {{ $model->code }} - {{ getCompany()->name }}</title>
    <style>
        body {
            font-family: "montserrat";
            font-size: 11pt
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ public_path('/css/pdf.css') }}">
</head>

<body>
    <div>
        <table style="border-collapse: collapse">
            <tr>
                <td style="width: 30%; border: 3px solid black">
                    <center><img src="{{ public_path('/images/icon.png') }}" style="width: 140px"></center>
                </td>
                <td style="border: 3px solid black; border-left: none">
                    <p style="font-size: 24px; text-align: center;">Formulir Permintaan Tenaga Kerja</p>
                </td>
            </tr>
        </table>
    </div>
    <div style="border: 1px solid gray; border-top: 0px;">
        <table style="border-collapse: collapse; padding: 1px">
            <tr>
                <td style="border: 1px solid black; border-top: none; border-left: none;width: 20%; padding: 8px">
                    Form-HRD-05-05
                </td>
                <td class="text-end" style="border-bottom: 1px solid black; text-align: center;">
                    <p style="text-align: center">No. Memo Intern : {{ $model->code }}/LB/{{ config('app.short_name') }}-HRD/{{ date('m', strtotime($model->created_at)) }}/{{ date('Y', strtotime($model->created_at)) }}</p>
                </td>
            </tr>
        </table>
        <table cellspacing="0" style="border-collapse: collapse">
            <tr>
                <td width="70%" style="border: 1px solid black; border-left:none; border-top: none;padding: 8px; padding-top: 0.6rem">
                    Cab./Bag. Yang Membutuhkan : {{ $model->branch->name }}
                </td>
                <td style="border-bottom: 1px solid black ;padding: 8px">
                    Untuk : {{ $model->division?->name }}
                </td>
            </tr>
        </table>
        @foreach ($model->labor_demand_details as $labor_demand_detail)
            <table style="border-collapse: collapse">
                <tr class="border-bottom">
                    <td colspan="4" class="text-center w-100 py-1">
                        Kriteria Umum Tenaga Kerja yang dibutuhkan :
                    </td>
                </tr>
                <tr class="border-bottom">
                    <td width="40" class="border-end py-1" style="padding-left: 8px">
                        Pendidikan
                    </td>
                    <td width="80" style="padding-left: 8px">
                        {{ $labor_demand_detail->education->name }}
                    </td>
                    <td width="40" style="border: none;padding-left: 8px">
                        Jurusan :
                    </td>
                    <td width="60" style="padding-left: 8px">
                        {{ $labor_demand_detail->degree->name }}
                    </td>
                </tr>
                <tr>
                    <td class="border-end" style="border-bottom: 1px solid black;padding-left: 8px">
                        Umur
                    </td>
                    <td class="border-end" style=" border-bottom: 1px solid black;padding-left: 8px">
                        {{ $labor_demand_detail->age }} tahun
                    </td>
                    <td class="border-end" style=" border-bottom: 1px solid black;padding-left: 8px">
                        Jenis Kelamin :
                    </td>
                    <td width="100" class="border-bottom" style="padding: 0; margin: 0">
                        <table class="text-center" style="border-collapse: collapse;">
                            <tr class="border-bottom">
                                <td style="border: none; border-right: 1px solid black">Laki-Laki*</td>
                                <td style="border: none;">Perempuan*</td>
                            </tr>
                            <tr style="border-bottom: none">
                                <td style="border: none; border-right: 1px solid black">{{ $labor_demand_detail->gender === 'laki-Laki' ? 'v' : '' }}</td>
                                <td style="border: none;">{{ $labor_demand_detail->gender !== 'laki-Laki' ? 'v' : '' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 0; margin: 0;width:100%">
                        <table style="border-collapse: collapse;">
                            <tr>
                                <td style="border: none;  border-right: 1px solid black; padding-left: 8px" width="150">Pengalaman Kerja</td>
                                <td style="border: none;" style="padding: 0; margin: 0">
                                    <table style="border-collapse:collapse;" class="text-center">
                                        <tr class="border-bottom">
                                            <td width="30" style="border-left:none;border-right: 1px solid black;border-top:none">Ya*</td>
                                            <td width="30" style="border-top:none;border-right: 1px solid black">Tidak*</td>
                                            <td width="100" style="border: none; border-right: 1px solid black">Bidang</td>
                                            <td width="100" style="border: none">Berapa Tahun</td>
                                        </tr>
                                        <tr>
                                            <td style="border-left: none;border-bottom:none" class="border-end">{{ is_null($labor_demand_detail->work_experience) ? '' : 'v' }}</td>
                                            <td style="border-bottom:none;border-right: 1px solid black">{{ is_null($labor_demand_detail->work_experience) ? 'v' : '' }}</td>
                                            <td style="border: none;  border-right: 1px solid black">{{ $labor_demand_detail->work_experience }}</td>
                                            <td style="border: none">{{ $labor_demand_detail->long_work_experience }} tahun</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        @endforeach
    </div>

    <div class=" mt-2">
        <p>Demikian permintaan tenaga kerja ini diajukan untuk mendapatkan persetujuan Pimpinan Perusahaan.</p>
    </div>

    <div>
        <table class="mt-1">
            <tr>
                <td width="170" style="vertical-align: bottom">
                    <p>Surabaya, {{ date('m-d-Y', strtotime($model->created_at)) }}</p>
                    <p>Yang membutuhkan,</p>
                    <br><br><br>
                    <p style="border-top: 1px solid black; width: 100%">Ka.Cab/Kabag/DPA</p>
                </td>
                <td width="170"></td>
                <td width="170" style="vertical-align: bottom">
                    <p>Mengetahui,</p>
                    <br><br><br>
                    <p style="border-top: 1px solid black; width: 100%">HRD</p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td colspan="">
                    <p>Menyetujui,</p>
                    <br><br><br>
                    <p style="border-top: 1px solid black; width: 100%" class="border-top border-dark w-75">Direktur</p>
                </td>
                <td></td>
            </tr>
        </table>
    </div>

    <p style="color: rgb(169, 168, 168); position:absolute; bottom:0;">*) pilih salah satu</p>
</body>

</html>
