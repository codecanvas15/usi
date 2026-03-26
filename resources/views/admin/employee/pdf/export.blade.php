<!DOCTYPE html>
<html>

<head>
    <title>Export Karyawan {{ $model->NIK }} | {{ Str::upper(config('app.name')) }}</title>
    <style type="text/css">
        body {
            font-size: 12px;
            color: #000;
        }

        table {
            border-spacing: 0px;
        }

        span {
            font-size: 12px;
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
            vertical-align: top
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table td {
            padding: 5px;
        }

        .table th {
            padding: 5px;
        }

        .table tr td,
        .table tr th {
            border: 1px solid black;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    <div class="container" style="color: black">
        <div class="row" style="max-width: 100%">
            <table style="width: 100%">
                <tr>
                    <td width="" style="text-align: left; padding-right: 10px">
                        <img src="{{ asset('/storage/' . getCompany()->logo) }}" alt="logo" width="122px">
                    </td>
                    <td width="65%" class="text-center">
                        <h1 class="font-medium-1 text-danger text-center mb-0">{{ Str::upper(getCompany()->name) }}</h1>
                        <h1 class="font-medium-1">DATA KARYAWAN</h1>
                    </td>
                    <td width="35%">
                        <div style="border: 1px solid black">
                            <img src="{{ asset('/storage/' . $model->file) }}" alt="" width="100px">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div><b>Harap ditulis dengan huruf cetak</b></div>
                        <table class="table" style="width: 100%; margin-top: 5px">
                            <tr>
                                <td width="33%">
                                    Lokasi : {{ $model->branch?->name }}
                                </td>
                                <td width="33%">
                                    Divisi : {{ $model->division?->name }}
                                </td>
                                <td width="33%">
                                    Tgl/thn Mulai Bekerja : {{ localDate($model->start_contract) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div>
                <h3 style="margin: 0px">I. PERSONAL DATA</h3>
                <table class="table" style="margin-top: 10px">
                    <tr>
                        <td width="25%">
                            Nama Lengkap
                        </td>
                        <td width="40%" colspan="2">
                            {{ $model->name }}
                        </td>
                        <td width="15%">
                            Agama
                        </td>
                        <td width="20%">
                            {{ $model->religion }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Tempat & Tanggal Lahir
                        </td>
                        <td colspan="2">
                            {{ $model->tempat_lahir }}, {{ localDate($model->tanggal_lahir) }}
                        </td>
                        <td>
                            Jenis Kelamin
                        </td>
                        <td>
                            {{ $model->jenis_kelamin }}
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="2">
                            Alamat Domisili
                        </td>
                        <td rowspan="2" colspan="2">
                            {{ $model->alamat_domisili }}
                        </td>
                        <td>
                            Tinggi
                        </td>
                        <td>
                            {{ $model->height }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Berat
                        </td>
                        <td>
                            {{ $model->weight }}
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="2">
                            Alamat KTP
                        </td>
                        <td colspan="2">
                            {{ $model->alamat }}
                        </td>
                        <td>
                            Gol Darah
                        </td>
                        <td>
                            {{ $model->blood_type }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Kode Pos
                        </td>
                        <td>
                            {{ $model->postal_code }}
                        </td>
                        <td>Hobi</td>
                        <td>{{ $model->hobby }}</td>
                    </tr>
                    <tr>
                        <td rowspan="2">
                            Alamat Tempat Tinggal Saat Ini (jika berbeda dengan alamat KTP)
                        </td>
                        <td colspan="2">
                            {{ $model->current_address }}
                        </td>
                        <td>
                            Telepon Rumah
                        </td>
                        <td>
                            {{ $model->house_phone }}
                        </td>
                    </tr>
                    <tr>
                        <td>Kode Pos</td>
                        <td>{{ $model->current_postal_code }}</td>
                        <td>
                            Handphone
                        </td>
                        <td>
                            {{ $model->nomor_telepone }}
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="2">
                            Alamat Tinggal Orangtua (jika berbeda dengan Anda)
                        </td>
                        <td colspan="2">
                            {{ $model->parents_address }}
                        </td>
                        <td>
                            Email
                        </td>
                        <td>
                            {{ $model->email }}
                        </td>
                    </tr>
                    <tr>
                        <td>Kode Pos</td>
                        <td>{{ $model->parents_postal_code }}</td>
                        <td>
                            Telepone Orangtua
                        </td>
                        <td>
                            {{ $model->parents_phone_number }}
                        </td>
                    </tr>
                </table>

                <table class="table" style="margin-top: 10px">
                    <tr>
                        <td>Status : {{ $model->non_taxable_income?->note ?? '-' }}</td>
                        <td>Tanggal Pernikahan : {{ $model->marriage_date ?? '-' }}</td>
                    </tr>
                </table>

                <table style="margin-top: 10px">
                    <tr>
                        <td width="60%" style="margin-right: 10px;">
                            <table class="table">
                                <tr>
                                    <th>Identitas</th>
                                    <th>Nomor Kartu</th>
                                    <th>Masa Berlaku</th>
                                </tr>

                                <tr>
                                    <td>NPWP</td>
                                    <td>{{ $model->npwp }}</td>
                                    <td></td>
                                </tr>
                                @forelse ($model->employeeDocument as $item)
                                    <tr>
                                        <td>{{ $item->document_name }}</td>
                                        <td>{{ $item->card_number }}</td>
                                        <td>{{ localDate($item->validity_period) }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </table>
                        </td>
                        <td width="30%" style="padding-left: 5px">
                            <div style="border: 1px solid black; padding: 2px">
                                <div style="padding: 0px">Kendaraan yang digunakan</div>
                                <div style="padding: 0px">Jenis / Merk / Tahun: <br>{{ $model->vehicle }} / {{ $model->vehicle_brand }} / {{ $model->vehicle_year }}</div>
                                <br>
                                <table>
                                    <tr>
                                        <td width="1%" style="vertical-align: bottom">
                                            <input type="radio" name="" id="" {{ is_null($model->vehicle_ownership) ? 'checked' : '' }}>
                                        </td>
                                        <td width="5%" style="vertical-align: bottom">
                                            Milik
                                        </td>
                                        <td width="1%" style="vertical-align: bottom">
                                            <input type="radio" name="" id="" {{ !is_null($model->vehicle_ownership) ? 'checked' : '' }}>
                                        </td>
                                        <td width="20%" style="vertical-align: bottom">
                                            Lain Lain
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="vertical-align: bottom">
                                            <table>
                                                <tr>
                                                    <td width="1%" style="vertical-align: bottom">
                                                        <input type="radio" name="" id="">
                                                    </td>
                                                    <td style="vertical-align: bottom">
                                                        Milik {{ is_null($model->vehicle_ownership) ? '........' : $model->vehicle_ownership }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>

                <h3 style="margin: 0px; margin-top: 10px">A. SUSUNAN KELUARGA</h3>

                <table class="table" style="margin-top: 10px">
                    <tr>
                        <th rowspan="2">Hubungan</th>
                        <th rowspan="2">Nama</th>
                        <th rowspan="2">L/P</th>
                        <th rowspan="2">Tgl. Lahir</th>
                        <th rowspan="2">Pendidikan</th>
                        <th colspan="2">Pendidikan Terakhir</th>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <th>Perusahaan</th>
                    </tr>

                    @forelse ($model->employeeFamilyTrees->groupBy('type') ?? [] as $key => $items)
                        <tr>
                            <th style="text-align: left" colspan="7">{{ $key == 'inti' ? 'KELUARGA INTI' : 'KELUARGA BESAR (orangtua, saudara kandung, termasuk diri Anda)' }}</th>
                        </tr>
                        @forelse ($items ?? [] as $item)
                            <tr>
                                <td>{{ Str::headline($item->relation) }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->gender == 'male' ? 'L' : 'P' }}</td>
                                <td>{{ localDate($item->birth_date) }}</td>
                                <td>{{ $item->education }}</td>
                                <td>{{ $item->last_position }}</td>
                                <td>{{ $item->last_company }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">-</td>
                            </tr>
                        @endforelse
                    @empty
                        <tr>
                            <td colspan="7" align="center">Tidak ada data!</td>
                        </tr>
                    @endforelse
                </table>

                <p style="page-break-before: always"></p>

                <table style=" margin-top: 10px">
                    <tr>
                        <td width="80%">
                            <h3 style="margin: 0px;">B. RUMAH YANG DITEMPATI</h3>
                        </td>
                        <td width="15%">
                            Beri tanda (x)
                        </td>
                    </tr>
                </table>

                <div style="border: 1px solid black; padding: 2px">
                    @php
                        $occupied_house = [
                            0 => [
                                'rumah_pribadi' => 'Rumah Pribadi',
                                'rumah_orangtua' => 'Rumah Orang Tua',
                                'kontrak' => 'Kontrak',
                            ],
                            1 => [
                                'sewa' => 'Sewa',
                                'indekos' => 'Indekos',
                            ],
                        ];
                    @endphp
                    <table>
                        @php
                            $isSelected = false;
                        @endphp
                        @foreach ($occupied_house as $key => $houses)
                            <tr>
                                @foreach ($houses as $key2 => $item)
                                    @php
                                        if ($key2 == $model->occupied_house) {
                                            $isSelected = true;
                                        }
                                    @endphp
                                    <td style="vertical-align: bottom" width="1%">
                                        <input type="radio" id="{{ $key . $key2 }}" {{ $key2 == $model->occupied_house ? 'checked' : '' }}>
                                    </td>
                                    <td style="vertical-align: bottom">{{ $item }}</td>

                                    @if ($key == 1 && $key2 == 'indekos')
                                        @if (!$isSelected)
                                            <td style="vertical-align: bottom">
                                                <input type="radio" id="{{ $key . 'lain' }}" checked>
                                            </td>
                                            <td style="vertical-align: bottom">{{ $model->occupied_house }}</td>
                                        @else
                                            <td style="vertical-align: bottom">
                                                <input type="radio" id="{{ $key . 'lain' }}">
                                            </td>
                                            <td style="vertical-align: bottom">Lain - lain</td>
                                        @endif
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                </div>

                <table style="margin-top: 10px">
                    <tr>
                        <td width="80%">
                            <h3 style="margin: 0px;">C. KONDISI & RIWAYAT KESEHATAN</h3>
                        </td>
                    </tr>
                </table>

                <div style="border: 1px solid black; padding: 2px">
                    <ol>
                        <li><b>Bagaimana kondisi kesehatan Saudara saat ini ?</b></li>
                        <div>{{ $model->employeeHealthHistory?->condition }}</div>
                        <li><b>Apakah Saudara pernah mengalami sakit keras atau kecelakaan berat ? (jika ya, jelaskan)</b></li>
                        <div>{{ $model->employeeHealthHistory?->description }}</div>
                        <li><b>Apakah ada efek samping yang dirasakan hingga saat ini ?</b></li>
                        <div>{{ $model->employeeHealthHistory?->description_2 }}</div>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div id="footer">
        <div class="row">
            <table>
                <tr>
                    <td style="width: 25%">
                        <div>
                            <img src="data:image/png;base64, {{ $qr }}" width="80px">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
