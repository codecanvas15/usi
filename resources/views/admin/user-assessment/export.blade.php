<!DOCTYPE html>
<html>

<head>
    <title>Export Interview User {{ $model->kode }} - {{ getCompany()->name }}</title>
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
        <center><span class="bold text-center" style="justify-content:center;font-size:25px;margin-bottom:10px">Interview User</span></center><br>
        <hr style="border: 1px solid grey;">
    </div>

    @php
        $kbc_percentage_total = 0;
        $ksc_percentage_total = 0;
    @endphp

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
                    Job Position : {{ $model->candidate_data->laborDemandDetail->position->nama }}
                </td>
                <td style="width: 50%;font-size:14px;">
                    Department Name: {{ $model->candidate_data->laborDemandDetail->labor_demand->division->name }}
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
                    Hiring Manager: {{ ucwords(strtolower($model->hiring_manager)) }}
                </td>
            </tr>
        </table>
    </div>

    <h4 class="text-semi-bold mt-2">Key Behavioral Competencies</h4>
    <table class="table table-responsive table-stripe table-bordered border-secondary">
        <thead class="bg-dark text-white">
            <tr>
                <th class="col-1">#</th>
                <th class="col-4">Name</th>
                <th>Wts</th>
                <th>Rating 1-5 (5 Highest)</th>
                <th>Weighted Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($model->detail as $key => $detail)
                @if ($detail->type == 'kbc')
                    @php
                        $kbc_percentage_total += $detail->masterUserAssessment->weight * 100;
                    @endphp
                    <tr>
                        <td>{{ $key++ + 1 }}</td>
                        <td>{{ ucwords(strtolower($detail->masterUserAssessment->name)) }}</td>
                        <td>{{ $detail->masterUserAssessment->weight * 100 }}%</td>
                        <td>{{ formatRating($detail->rating) }}</td>
                        <td>{{ $detail->weight }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot class="bg-dark text-white">
            <tr>
                <td colspan="2" class="text-end fw-bold">Overall Behavioral Competency Rating</td>
                <td class="fw-bold">
                    <span>{{ $kbc_percentage_total }}%</span>
                </td>
                <td></td>
                <td class="fw-bold">
                    <div>
                        <span class="fw-bold">{{ $model->behavioral_rating }}</span>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>

    <h4 class="text-semi-bold mt-2">Key Skill Competencies</h4>
    <table class="table table-responsive table-stripe table-bordered border-secondary">
        <thead class="bg-dark text-white">
            <tr>
                <th class="col-1">#</th>
                <th class="col-4">Name</th>
                <th>Wts</th>
                <th>Rating 1-5 (5 Highest)</th>
                <th>Weighted Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($model->detail as $key => $detail)
                @if ($detail->type == 'ksc')
                    @php
                        $ksc_percentage_total += $detail->masterUserAssessment->weight * 100;
                    @endphp
                    <tr>
                        <td>{{ $key++ + 1 }}</td>
                        <td>{{ ucwords(strtolower($detail->masterUserAssessment->name)) }}</td>
                        <td>{{ $detail->masterUserAssessment->weight * 100 }}%</td>
                        <td>{{ formatRating($detail->rating) }}</td>
                        <td>{{ $detail->weight }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot class="bg-dark text-white">
            <tr>
                <td colspan="2" class="text-end fw-bold">Overall Skill Competency Rating</td>
                <td class="fw-bold">
                    <div>
                        <span>{{ $ksc_percentage_total }}%</span>
                    </div>
                </td>
                <td></td>
                <td>
                    <div>
                        <span class="fw-bold">{{ $model->skill_rating }}</span>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>

    <table class="table table-responsive table-stripe table-bordered border-secondary mt-3">
        <tr>
            <th>What Impressed You The Most</th>
            <th>What Impressed You The Least</th>
            <th>What Questions Or Reservations Do You Have?</th>
        </tr>
        <tr>
            <td>{{ $model->first_note ?? '-' }}</td>
            <td>{{ $model->second_note ?? '-' }}</td>
            <td>{{ $model->third_note ?? '-' }}</td>
        </tr>
        <tr>
            <th>Overall Rating</th>
            <th>Hiring Recommendation</th>
            <th rowspan="2"></th>
        </tr>
        <tr>
            <td style="color: #ed3338;" class="text-semi-bold">{{ $model->total_rating }}</td>
            <td>{{ formatRecommendStatus($model->recommend_status) }}</td>
        </tr>
    </table>
    <p class="mt-1 mb-0">Ratings:</p>
    <ul>
        <li>5. Excellent</li>
        <li>4. Good</li>
        <li>3. Fair</li>
        <li>2. Poor</li>
        <li>1. Unacceptable</li>
    </ul>

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
