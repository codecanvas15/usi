<!DOCTYPE html>
<html>

<head>
    <title>Export Labor Demand {{ $model->kode }} - {{ getCompany()->name }}</title>
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
        <center><span class="bold text-center" style="justify-content:center;font-size:25px;margin-bottom:10px">PERMINTAAN TENAGA KERJA</span></center><br>
        <hr style="border: 1px solid grey;">
    </div>

    <div>
        <table class="w-100">
            <tr style="margin-bottom:10px">
                <td style="width: 60%;font-size:14px;">
                    Kode : {{ $model->code }}
                </td>
                <td style="width: 40%;font-size:14px;">
                    Branch : {{ $model->branch->name }}
                </td>
            </tr>
            <tr style="margin-bottom:10px">
                <td style="width: 60%;font-size:14px;">
                    Divisi : {{ $model->division->name }}
                </td>
                <td style="width: 40%;font-size:14px;">
                    Dibuat oleh : {{ $model->user?->name }} - {{ $model->user?->email }}
                </td>
            </tr>
            <tr style="margin-bottom:10px">
                <td style="width: 60%;font-size:14px;">
                    Lokasi : {{ $model->location }}
                </td>
            </tr>
        </table>
    </div>

    @foreach ($model->labor_demand_details as $labor_demand_detail)
        <table class="table table-responsive table-stripe table-bordered border-secondary mt-3">
            <tr>
                <th class="col-3">Posisi</th>
                <td>{{ $labor_demand_detail->position?->nama }}</td>
                <th class="col-3">Nama Posisi</th>
                <td>{{ $labor_demand_detail->position_name }}</td>
            </tr>
            <tr>
                <th class="col-3">Pendidikan</th>
                <td>{{ $labor_demand_detail->education->name }}</td>
                <th class="col-3">Jurusan</th>
                <td>{{ $labor_demand_detail->degree->name }}</td>
            </tr>
            <tr>
                <th class="col-3">Umur</th>
                <td>{{ $labor_demand_detail->age }} tahun</td>
                <th class="col-3">Jumlah</th>
                <td>{{ $labor_demand_detail->quantity }} orang</td>
            </tr>
            <tr>
                <th class="col-3">Jenis Kelamin</th>
                <td>{{ $labor_demand_detail->gender }}</td>
                <th class="col-3">Lama Pengalaman Kerja</th>
                <td>{{ $labor_demand_detail->long_work_experience }} tahun</td>
            </tr>
            <tr>
                <th class="col-3">Pengalaman Kerja</th>
                <td>{{ $labor_demand_detail->work_experience }}</td>
                <th class="col-3">Skill Pegawai</th>
                <td>{{ $labor_demand_detail->skills }}</td>
            </tr>
            <tr>
                <th class="col-3">Deskripsi Pekerjaan</th>
                <td>{{ $labor_demand_detail->job_description }}</td>
                <th class="col-3">Keterangan Tambahan</th>
                <td>{{ $labor_demand_detail->description }}</td>
            </tr>
        </table>
    @endforeach

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
