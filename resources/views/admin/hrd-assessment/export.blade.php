<!DOCTYPE html>
<html>

<head>
    <title>Export Interview HRD {{ $model->kode }} - {{ getCompany()->name }}</title>
    <style>
        @font-face {
            font-family: 'montserrat';
            src: url('fonts/montserrat.ttf') format("truetype");
            font-weight: 400; // use the matching font-weight here ( 100, 200, 300, 400, etc).
            font-style: normal; // use the matching font-style here
        }

        @font-face {
            font-family: 'montserrat-bold';
            src: url('fonts/montserrat-bold.ttf') format("truetype");
            font-weight: 500; // use the matching font-weight here ( 100, 200, 300, 400, etc).
            font-style: normal; // use the matching font-style here
        }

        body {
            font-family: "montserrat";
            font-size: 12px
        }
    </style>

    {{-- <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/invoice-pdf.css"> --}}

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/pdf.css">
    {{-- <link rel="stylesheet" href="{{ public_path() }}/css/style.css"> --}}
    {{-- <link rel="stylesheet" href="{{ public_path() }}/css/skin_color.css"> --}}
    {{-- <link rel="stylesheet" href="{{ public_path() }}/css/custom.css"> --}}
    {{-- <link rel="stylesheet" href="{{ public_path() }}/css/vendors_css.css"> --}}
</head>

<body>
    <div class="row">
        <table>
            <tr>
                <td>
                    <h2 class="brand-head">{{ getCompany()->name }}</h2>
                    <p class="mb-0">{{ getCompany()->address }}</p>
                    <p class="mb-0">Telp. {{ getCompany()->phone }}</p>
                </td>
                <td style="width: 25%">
                    {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
                </td>
            </tr>
        </table>
    </div>

    <div>
        <center><span class="bold text-center" style="justify-content:center;font-size:25px;margin-bottom:10px">Interview HRD</span></center><br>
        <hr style="border: 1px solid grey;">
    </div>

    <div>
        <table class="w-100">
            <tr style="margin-bottom:10px">
                <td style="width: 50%;font-size:14px;">
                    Kode : {{ $model->reference }}
                </td>
                <td style="width: 50%;font-size:14px;">
                    Tanggal : {{ $model->assessment_date }}
                </td>
            </tr>
            <tr style="margin-bottom:10px">
                <td style="width: 50%;font-size:14px;">
                    Interviewer : {{ ucwords(strtolower($model->interviewer_data->name)) }}
                </td>
                <td style="width: 50%;font-size:14px;">
                    Kandidat : {{ ucwords(strtolower($model->candidate_data->name)) }}
                </td>
            </tr>
            <tr style="margin-bottom:10px">
                <td style="width: 50%;font-size:14px;">
                    Posisi : {{ $model->position_data->nama }}
                </td>
            </tr>
        </table>
    </div>

    <table class="table table-responsive table-stripe table-bordered border-secondary mt-2">
        <tr>
            <th>Assessment</th>
            <th>Rating</th>
            <th>Komentar</th>
        </tr>
        @foreach ($model->detail as $detail)
            <tr>
                <td><b>{{ ucwords(strtolower($detail->masterHrdAssessment->title)) }}</b> - {{ $detail->masterHrdAssessment->description }}</td>
                <td>{{ $detail->rating }}</td>
                <td>{{ $detail->notes ?? '-' }}</td>
            </tr>
        @endforeach
        <tr>
            <td><b>Kesan dan Rekomendasi Secara Keseluruhan</b> -<br>Ringkasan persepsi Anda tentang kekuatan/kelemahan kandidat.</td>
            @if ($model->assessment_status == 'y')
                <td class="fw-bold">Lanjut Tahap II</td>
            @elseif ($model->assessment_status == 'r')
                <td class="fw-bold">Lanjut Dengan Reservasi</td>
            @else
                <td class="fw-bold">Tidak Lanjut</td>
            @endif
            <td>{{ $model->notes ?? '-' }}</td>
        </tr>
    </table>

    <div id="footer">
        <div class="row">
            <table style="width: 100%;margin-top:20px;">
                <tr>
                    <td style="width: 65%; vertical-align: top">
                        <div>
                            <img src="data:image/png;base64, {{ $qr }}" width="140px">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
