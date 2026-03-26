<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Letter of Intent - {{ Str::upper(config('app.name', 'Unitetd Shipping Indonesia')) }}</title>
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
</head>

<body style="font-size: 12px">
    <img src="{{ public_path() }}/images/header-offering-letter.png" style="position: fixed; top: 0;" alt="">
    <footer style="position: fixed; bottom: 0; left: 50%; transform: translate(-50%, 0); font-size: 10pt; white-space: nowrap;">
        <p style="text-align: center; color: blue; border: 1px solid blue; padding: 10px">
            Jl. Gondosuli No. 8 Surabaya – 60272, Jawa Timur – Indonesia <br>
            +62 (31) 547 1841 <br>
            www.ptusi.co.id <br>
        </p>
    </footer>

    <div style="margin: 0 100px; margin-top: 6rem;font-size: 11pt;text-align: justify">
        <div style="font-size: 14pt; text-align:center"><b>Letter of Intent</b></div>
        <div style="font-size: 10pt; text-align:center">No. : {{ addThreeZeroOnFront($model->id) }}/OL-HRD/{{ date('m', strtotime($model->created_at)) }}/{{ date('Y', strtotime($model->created_at)) }}</div>
        <br>
        <p>
            Mewakili PT {{ getCompany()->name }}, saya dengan senang hati menawarkan Anda untuk bergabung karena Kami sangat terkesan dengan pengalaman dan keterampilan yang Anda miliki selama proses rekrutmen. Dengan apa yang Anda punya, kami yakin Anda bisa berkontribusi dan menjadi aset bernilai bagi perusahaan, bila Anda setuju dengan tawaran seperti kondisi dibawah ini :
        </p>
        <table cellpadding="5">
            <tr>
                <td>Nama</td>
                <td width="10" style="text-align: center">:</td>
                <td>{{ $model->laborApplication?->name }}</td>
            </tr>
            <tr>
                <td>Tempat Tanggal, Lahir</td>
                <td style="text-align: center">:</td>
                <td>{{ $model->laborApplication?->place_of_birth }}, {{ date('d-m-Y', strtotime($model->laborApplication?->date_of_birth)) }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td style="text-align: center">:</td>
                <td>{{ $model->laborApplication?->address }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td style="text-align: center">:</td>
                <td>{{ $model->laborApplication?->laborDemandDetail?->position?->nama }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td style="text-align: center">:</td>
                <td>{{ $model->laborApplication?->laborDemandDetail?->labor_demand?->division?->name }}</td>
            </tr>
            <tr>
                <td>Tempat penerimaan</td>
                <td style="text-align: center">:</td>
                <td>{{ $model->laborApplication?->laborDemandDetail?->labor_demand?->location }}</td>
            </tr>
            <tr>
                <td>Lokasi Kerja</td>
                <td style="text-align: center">:</td>
                <td>{{ $model->work_location }}</td>
            </tr>
            <tr>
                <td>Tanggal Efektif Bekerja</td>
                <td style="text-align: center">:</td>
                <td>{{ $model->start_work_date }}</td>
            </tr>
            <tr>
                <td>Status Karyawan & Masa kontrak</td>
                <td style="text-align: center">:</td>
                <td>{{ $model->employment_status }}</td>
            </tr>
            <tr>
                <td>Kompensasi</td>
                <td style="text-align: center">:</td>
                <td>{{ $model->compensation }}</td>
            </tr>
            <tr>
                <td>Gaji</td>
                <td style="text-align: center">:</td>
                <td>Rp. {{ formatNumber($model->salary) }}</td>
            </tr>
            <tr>
                <td>Tunjangan</td>
                <td style="text-align: center">:</td>
                <td>Rp. {{ formatNumber($model->allowance_salary) }}</td>
            </tr>
            <tr>
                <td>Hari cuti</td>
                <td style="text-align: center">:</td>
                <td>{{ formatNumber($model->leave_day) }}</td>
            </tr>
            <tr>
                <td>THR</td>
                <td style="text-align: center">:</td>
                <td>Rp. {{ formatNumber($model->holiday_allowance) }}</td>
            </tr>
        </table>
        <p>
            Harap ditinjau kembali dokumen yang terlampir dengan surat ini, jika Anda memilih menerima tawaran pekerjaan ini, silahkan tanda tangan ditempat yang sudah ditentukan.
        </p>
        <p>
            Scan kembali dokumen ini, dan kirim kembali softcopy melalui email : <u>{{ $model->to_email }}</u>
            sesegera mungkin terakhir kami harus terima pada tanggal : <u>{{ $model->due_date }}</u>
        </p>
        <p>
            Kami akan menghubungi Anda setelah kami terima, jangan ragu untuk menghubungi saya jika Anda masih memiliki pertanyaan apapun terkait dengan dokumen kontrak kerja terlampir.
        </p>

    </div>

    <P style="page-break-before: always" />

    <div style="margin: 0 100px; margin-top: 6rem;font-size: 11pt;text-align: justify">
        <table style="width: 100%">
            <tr>
                <td style="width: 30%">
                    <p>Surabaya, {{ date('d-m-Y', strtotime($model->created_at)) }}</p>
                    <p>Menyetujui,</p>
                    <br><br><br>
                    <p style="border-top: 1px solid black">Hrd</p>
                </td>
                <td style="40%"></td>
                <td style="width: 30%; vertical-align: bottom;">
                    <p>...........................................,</p>
                    <br><br><br>
                    <p style="border-top: 1px solid black">{{ $model->laborApplication?->name }}-{{ $model->laborApplication?->employee?->NIK }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
