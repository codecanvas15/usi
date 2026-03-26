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
            <td class="px-0" width="60%">
                <h3 class="my-0">REKAP CUTI {{ $year }}</h3>
                <p class="mt-0">SKA GROUP INDONESIA</p>
                <p class="mt-0">{{ $branch->company ?? '' }}</p>
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
                <th class="text-center font-xsmall-3">No</th>
                <th class="text-center font-xsmall-3">Nama</th>
                <th class="text-center font-xsmall-3">Jumlah Cuti</th>
                <th class="text-center font-xsmall-3">Januari</th>
                <th class="text-center font-xsmall-3">Februari</th>
                <th class="text-center font-xsmall-3">Maret</th>
                <th class="text-center font-xsmall-3">April</th>
                <th class="text-center font-xsmall-3">Mei</th>
                <th class="text-center font-xsmall-3">Juni</th>
                <th class="text-center font-xsmall-3">Juli</th>
                <th class="text-center font-xsmall-3">Agustus</th>
                <th class="text-center font-xsmall-3">September</th>
                <th class="text-center font-xsmall-3">Oktober</th>
                <th class="text-center font-xsmall-3">November</th>
                <th class="text-center font-xsmall-3">Desember</th>
                <th class="text-center font-xsmall-3">Sisa Cuti</th>
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
