<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Cuti {{ $year }}</title>
    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/app-assets/css/pdf.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif
        }

        .mt-2 {
            margin-top: 2rem;
        }

        .bg-primary {
            background-color: #303179 !important;
        }

        .text-white {
            color: #fff !important;
        }
    </style>
</head>

<body>
    <table width="100%">
        <tr>
            <td class="px-0" colspan="12">
                <h3>Rekap Cuti {{ $year }}</h3>
                <p>SKA GROUP INDONESIA</p>
                <p>{{ $branch->company ?? '' }}</p>
                <p>{{ app('request')->input('from_date') }} - {{ app('request')->input('to_date') }}
                </p>
            </td>
            <td class="px-0" width="40%" align="right" style="vertical-align: top">
                <img onerror="imgError(this);" src="{{ public_path() }}/app-assets/images/logo/logo-text.png"
                    width="200">
            </td>
        </tr>
    </table>
    <table class="table table-bordered" width="100%">
        <thead>
            <tr>
                <th align="center">No</th>
                <th align="center" width="200px">Nama Pegawai</th>
                <th align="center">Jumlah Cuti</th>
                <th align="center">Januari</th>
                <th align="center">Februari</th>
                <th align="center">Maret</th>
                <th align="center">April</th>
                <th align="center">Mei</th>
                <th align="center">Juni</th>
                <th align="center">Juli</th>
                <th align="center">Agustus</th>
                <th align="center">September</th>
                <th align="center">Oktober</th>
                <th align="center">November</th>
                <th align="center">Desember</th>
                <th align="center">Sisa Cuti</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $key => $user)
                <tr>
                    <td class="font-xsmall-3">{{ $loop->iteration }}</td>
                    <td class="font-xsmall-3">{{ $user->name }}</td>
                    <td class="font-xsmall-3">{{ $user->leave ?? 0 }}</td>
                    @foreach ($leaves[$key] as $leave)
                        <td class="font-xsmall-3">{{ $leave }}</td>
                    @endforeach
                    <td class="font-xsmall-3">{{ $user->leave_rest }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>
</body>

</html>
