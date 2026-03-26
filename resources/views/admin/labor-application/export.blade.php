<!DOCTYPE html>
<html>

<head>
    <title>Labor Application {{ $model->kode }}</title>
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
    <link rel="stylesheet" type="text/css" href="{{ public_path('/css/pdf.css') }}">
</head>

<body>
    <div class="row" style="border: 1px solid black; padding: 0">
        <table>
            <tr>
                <td width="100" style="vertical-align: top">
                    {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
                </td>
                <td width="200" style="vertical-align: top">
                    <table class="table-bordered text-center" style="margin-top: -4px">
                        <tr>
                            <td colspan="2" style="border-top: none">DIISI OLEH STAFF HRD</td>
                        </tr>
                        <tr>
                            <td>No. Memo Intern</td>
                            <td>Kabag yang merekrut</td>
                        </tr>
                        <tr>
                            <td class="p-2"></td>
                            <td class="p-2"></td>
                        </tr>
                    </table>
                </td>
                <td width="100" rowspan="2" class="py-1">
                    <center>
                        <div style="width: 1.41in; height: 1.85in; border: 1px solid black; border-radius: 5px;">
                            <div class="mt-5">Pas photo baru</div>
                        </div>
                    </center>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: bottom; text-align: left;">
                    <div style="border: 1px solid black; padding: 8px;margin-bottom: -3px; margin-left: -3px; width: 50%">
                        Form-HRD-05-02
                    </div>
                </td>
                <td style="vertical-align: top">
                    <p style="font-size: 20px" class="text-center">FORMULIR LAMARAN</p>
                    <p class="text-center">Tanggal .........................................................</p>
                    <ul style="list-style: none">
                        <li># Formulir ini bersifat pribadi dan rahasia.</li>
                        <li># Formulir diisi dengan jujur, lengkap dan dengan tulisan
                            Sendiri.
                        </li>
                    </ul>
                </td>
            </tr>
        </table>
    </div>

    <div class="row mt-1">
        <table class="table-bordered">
            <tr>
                <td width="200">Lamaran Untuk Posisi</td>
                <td></td>
            </tr>
            <tr>
                <td>Sumber Referensi Lamaran</td>
                <td>
                    <table style="border-collapse: collapse" class="text-center">
                        <tr>
                            <td style="border: none">
                                <input type="checkbox" name="iklan" id="iklan">
                            </td>
                            <td style="border: none">
                                <input type="checkbox" name="teman" id="teman">
                            </td>
                            <td style="border: none">
                                <input type="checkbox" name="keluarga" id="keluarga">
                            </td>
                            <td style="border: none">
                                <input type="checkbox" name="datang-sendiri" id="datang-sendiri">
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none">Iklan</td>
                            <td style="border: none">Teman</td>
                            <td style="border: none">Keluarga</td>
                            <td style="border: none">Datang Sendiri</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center" style="padding: 8px">DATA PRIBADI</td>
            </tr>
            <tr>
                <td>Nama lengkap</td>
                <td>{{ $model->name }}</td>
            </tr>
            <tr>
                <td>Alamat sekarang</td>
                <td>{{ $model->address_domicil }}</td>
            </tr>
            <tr>
                <td>No. telepun & No. HP</td>
                <td>{{ $model->phone }}</td>
            </tr>
            <tr>
                <td>Alamat tetap</td>
                <td>{{ $model->address }}</td>
            </tr>
            <tr>
                <td>No. telepun</td>
                <td>{{ $model->phone }}</td>
            </tr>
            <tr>
                <td>Tempat & Tgl. Lahir / Umur</td>
                <td>{{ $model->place_of_birth }} / {{ localDate($model->date_of_birth) }}</td>
            </tr>
            <tr>
                <td>Agama</td>
                <td></td>
            </tr>
            <tr>
                <td>No. KTP / SIM</td>
                <td>{{ $model->identity_card_number }}</td>
            </tr>
            <tr>
                <td>SIM apa yang anda miliki ?</td>
                <td>
                    <table style="border-collapse: collapse" class="text-center">
                        <tr>
                            <td style="border: none">
                                <input type="checkbox" name="C" id="C">
                            </td>
                            <td style="border: none">
                                <input type="checkbox" name="A" id="A">
                            </td>
                            <td style="border: none">
                                <input type="checkbox" name="B1" id="B1">
                            </td>
                            <td style="border: none">
                                <input type="checkbox" name="B2" id="B2">
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none">C</td>
                            <td style="border: none">A</td>
                            <td style="border: none">B1</td>
                            <td style="border: none">B2</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>Warga negara & Suku bangsa</td>
                <td></td>
            </tr>
            <tr>
                <td>Status perkawinan & Jumlah anak</td>
                <td>{{ $model->marial_status == 1 ? 'Sudah menikah' : 'Belum menikah' }}</td>
            </tr>
            <tr>
                <td>Siapa yang harus dihubungi jika ada kecelakaan atau
                    sakit ? (Nama, Alamat & Tlp.)
                </td>
                <td style="padding: 0">
                    <table class="table-noborder">
                        <tr class="border-bottom">
                            <td style="border-right: 1px solid black">Nama</td>
                            <td style="border-right: 1px solid black">Alamat</td>
                            <td>Telepon</td>
                        </tr>
                        @foreach ($model->laborApplicationEmergencyContacts as $item)
                            <tr class="{{ count($model->laborApplicationEmergencyContacts) - ($item->itteration + 1) > 1 ? 'border-bottom' : '' }}">
                                <td style="border-right: 1px solid black">{{ $item->name }}</td>
                                <td style="border-right: 1px solid black">{{ $item->address }}</td>
                                <td>{{ $item->phone }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center" style="padding: 8px">DATA KELUARGA (Buat lampiran bilamana tidak cukup)</td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 0">
                    <table class="table-bordered text-center">
                        <tr>
                            <td width="70"></td>
                            <td width="150">Nama</td>
                            <td width="30">L/P</td>
                            <td width="30">Umur</td>
                            <td width="70">Pendidikan <br> Terkhir</td>
                            <td width="100">Pekerjaan & Tempat kerja</td>
                        </tr>
                        <tr>
                            <td class="text-left">Ayah</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="text-left">
                            <td>Ibu</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">Saudara 1</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">2</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">3</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">4</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">5</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">6</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-left">Suami/Istri*)</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">Anak 1</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">2</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">3</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">4</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">5</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-end">6</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
        <div style="color: rgb(181, 181, 181); position: absolute; bottom: 0">*) Coret yang tidak perlu</div>
    </div>

    <P style="page-break-before: always">

    <div class="row">
        <table class="table-bordered">
            <tr>
                <td width="250">Siapakah yang menjadi tanggungan Anda ?</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">DATA KESEHATAN</td>
            </tr>
            <tr>
                <td>Tinggi, Berat badan & Golongan darah</td>
                <td style="padding: 0">
                    <table style="border: none;border-collapse: collapse;">
                        <tr>
                            <td style="border: none" class="py-1">
                                <span style="padding: 7px 20px;border: 1px solid black"></span><span class="ml-1">Cm</span>
                            </td>
                            <td style="border: none">
                                <span style="padding: 7px 20px; border: 1px solid black"></span><span class="ml-1">Kg</span>
                            </td>
                            <td style="border: none">A</td>
                            <td style="border: none">B</td>
                            <td style="border: none">AB</td>
                            <td style="border: none">O</td>

                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>Apakah Anda berkaca mata ? Beri keternagan !</td>
                <td></td>
            </tr>
            <tr>
                <td>Apakah pernah mengalami kecelakaan ? Apakah ada akibatnya ?</td>
                <td></td>
            </tr>
            <tr>
                <td>Apakah memiliki penyakit dan masalah kesehatan yang serius (termasuk alergi berat)</td>
                <td></td>
            </tr>
            <tr>
                <td>Apakah Anda merokok atau minum-minuman keras ? Sampai sejauh mana ?</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">DATA PENDIDIKAN ( Lampirkan Ijasah & Daftar Nilai yang disahkan )</td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">Pendidikan Formal</td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 0">
                    <table class="table-bordered text-center">
                        <tr>
                            <td width="70"></td>
                            <td>Nama Lembaga</td>
                            <td>Jurusan</td>
                            <td>Th. Masuk - <br>Th Lulus</td>
                            <td>Nilai</td>
                        </tr>
                        <tr>
                            <td>Univ/Pasca*)</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Univ/Akademi*)</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>SMA/STM/SMEA*)</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Lulus / Tdk Lulus</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>SMP</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">Pendidikan Non-Formal</td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 0">
                    <table class="table-bordered text-center">
                        <tr>
                            <td>Kursus</td>
                            <td>Penyelenggara</td>
                            <td>Lama Kursus</td>
                            <td>Seminar / Training</td>
                            <td>Penyelenggara</td>
                            <td>Tgl/bl/th</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0px" style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                            <td style="padding: 10px 0px"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>Terangkan Prestasi Akademis Anda dengan singkat</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px 0px">

                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px 0px">

                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">
                    DATA KETERAMPILAN
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 0">
                    <table class="table-bordered">
                        <tr>
                            <td rowspan="6" width="150">Kemampuan komputer yang dimiliki</td>
                            <td width="170"></td>
                            <td width="20" style="text-align: center">SB</td>
                            <td width="20" style="text-align: center">B</td>
                            <td width="20" style="text-align: center">C</td>
                            <td width="20" style="text-align: center">K</td>
                        </tr>
                        <tr>
                            <td>a.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>b.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>c.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>d.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>e.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td rowspan="5">Keahlian kantor yang dimiliki</td>
                            <td>a.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>b.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>c.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>d.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 0;">
                    <table class="table-bordered text-center">
                        <tr>
                            <td rowspan="2" width="20">No.</td>
                            <td rowspan="2" width="250">Kemampuan Bahasa Asing</td>
                            <td colspan="3" width="120">Lisan</td>
                            <td colspan="3" width="120">Tulisan</td>
                        </tr>
                        <tr>
                            <td>B</td>
                            <td>C</td>
                            <td>K</td>
                            <td>B</td>
                            <td>C</td>
                            <td>K</td>
                        </tr>
                        <tr>
                            <td>1.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>2.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>3.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>4.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>5.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0px"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0px"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div style="color: rgb(181, 181, 181);">*) Coret yang tidak perlu</div>
    </div>

    <P style="page-break-before: always">

    <div class="row">
        <table class="table-bordered">
            <tr>
                <td colspan="5" class="text-center">RIWAYAT PEKERJAAN ( Dimulai dari pekerjaan terakhir )</td>
            </tr>
            <tr>
                <td colspan="2">
                    Nama <br> Perusahaan
                </td>
                <td width="150">

                </td>
                <td width="100">
                    Alamat Perusahaan
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td colspan="2">
                    Bidang Usaha
                </td>
                <td>

                </td>
                <td>
                    Nama Atasan
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td colspan="2">
                    Jabatan
                </td>
                <td>

                </td>
                <td>
                    Alamat & No. Telp
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td width="50" rowspan="2">
                    Masa Kerja
                </td>
                <td width="50">
                    Dari
                </td>
                <td>
                    Th .......................................
                </td>
                <td>
                    Gaji awal
                </td>
                <td>
                    Rp .......................................
                </td>
            </tr>
            <tr>
                <td>
                    Sampai
                </td>
                <td>
                    Th .......................................
                </td>
                <td>
                    Gaji Akhir
                </td>
                <td>
                    Rp .......................................
                </td>
            </tr>
            <tr>
                <td colspan="2">Alasan Berhenti</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="5" style="padding: 8px"></td>
            </tr>
            <tr>
                <td colspan="2">
                    Nama <br> Perusahaan
                </td>
                <td width="150">

                </td>
                <td width="100">
                    Alamat Perusahaan
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td colspan="2">
                    Bidang Usaha
                </td>
                <td>

                </td>
                <td>
                    Nama Atasan
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td colspan="2">
                    Jabatan
                </td>
                <td>

                </td>
                <td>
                    Alamat & No. Telp
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td width="50" rowspan="2">
                    Masa Kerja
                </td>
                <td width="50">
                    Dari
                </td>
                <td>
                    Th .......................................
                </td>
                <td>
                    Gaji awal
                </td>
                <td>
                    Rp .......................................
                </td>
            </tr>
            <tr>
                <td>
                    Sampai
                </td>
                <td>
                    Th .......................................
                </td>
                <td>
                    Gaji Akhir
                </td>
                <td>
                    Rp .......................................
                </td>
            </tr>
            <tr>
                <td colspan="2">Alasan Berhenti</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="5" style="padding: 8px"></td>
            </tr>
            <tr>
                <td colspan="2">
                    Nama <br> Perusahaan
                </td>
                <td width="150">

                </td>
                <td width="100">
                    Alamat Perusahaan
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td colspan="2">
                    Bidang Usaha
                </td>
                <td>

                </td>
                <td>
                    Nama Atasan
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td colspan="2">
                    Jabatan
                </td>
                <td>

                </td>
                <td>
                    Alamat & No. Telp
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td width="50" rowspan="2">
                    Masa Kerja
                </td>
                <td width="50">
                    Dari
                </td>
                <td>
                    Th .......................................
                </td>
                <td>
                    Gaji awal
                </td>
                <td>
                    Rp .......................................
                </td>
            </tr>
            <tr>
                <td>
                    Sampai
                </td>
                <td>
                    Th .......................................
                </td>
                <td>
                    Gaji Akhir
                </td>
                <td>
                    Rp .......................................
                </td>
            </tr>
            <tr>
                <td colspan="2">Alasan Berhenti</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="5" style="padding: 8px"></td>
            </tr>
            <tr>
                <td colspan="5">LAIN - LAIN</td>
            </tr>
            <tr>
                <td colspan="3">Olah raga dan hobby (sejauh mana)</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="5" style="padding: 8px"></td>
            </tr>
            <tr>
                <td colspan="3">Apa rencana Anda unutk masa depan jangka panjang <br>(5 thn, 10 thn), jelaskan secara singkat !</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="5" style="padding: 8px"></td>
            </tr>
            <tr>
                <td colspan="5">
                    Pengalaman Organisasi
                </td>
            </tr>
            <tr>
                <td colspan="5" style="padding: 0">
                    <table class="table-bordered text-center">
                        <tr>
                            <td width="10">No.</td>
                            <td width="150">Nama Organisasi</td>
                            <td width="120">Jabatan</td>
                            <td>Tahun</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                            <td style="padding: 8px"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">Apakah Anda masih terikat kontrak dengan perusahaan tempat Anda bekerja sekarang ?</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="3">Apakah pengalaman terbaik dalam karir Anda ?</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="3">Apakah pengalaman terburuk dalam karir Anda ?</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="3">Pernakah Anda melamar di perusahaan ini sebelumnya ?</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="3">Pernahkan Anda mengikuti psikotest ?</td>
                <td colspan="2">
                    <table class="table-noborder" style="width: 100%">
                        <tr>
                            <td width="50">[&nbsp;&nbsp;&nbsp;] Ya</td>
                            <td width="50"></td>
                            <td style="text-align: right">[&nbsp;&nbsp;&nbsp;] Tidak, bila ya : </td>
                            <td width="50"></td>
                        </tr>
                        <tr>
                            <td>Di : </td>
                            <td></td>
                            <td rowspan="2" style="text-align: right">Tujuan : </td>
                            <td rowspan="2"></td>
                        </tr>
                        <tr>
                            <td>Tahun : </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Di : </td>
                            <td></td>
                            <td rowspan="2" style="text-align: right">Tujuan : </td>
                            <td rowspan="2"></td>
                        </tr>
                        <tr>
                            <td>Tahun : </td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    Apakah Anda mempunyai hubungan keluarga / teman dengan seorang staff atau pegawai dari perusahaan ini ?<br>Bila ya, sebutkan !
                </td>
                <td colspan="2">

                </td>
            </tr>

        </table>
        <div style="color: rgb(181, 181, 181)" class="mt-2">*) Coret yang tidak perlu</div>
    </div>

    <P style="page-break-before: always" />

    <div class="row">
        <table class="table-bordered">
            <tr>
                <td width="250">
                    Bersediakah Anda bekerja lembur bilamana diperlukan ? (alasannya)
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Bersediakah Anda ditempatkan di bagian lain bilamana diperlukan ? (alasannya)
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Bersediakah Anda ditempatkan di luar daerah atau keluar kota bilamana diperlukan ? (alasannya)
                </td>
                <td></td>
            </tr>
            <tr>
                <td style="padding: 8px">

                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Apakah yang diharapkan bila Anda bergabung dengan perusahaan ini ? Mengapa ?
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Sebutkan secara berurutan apa yang Anda prioritaskan : suasana kerja, gaji, kedudukan.
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Jenis pekerjaan apa yang sebenarnya Anda senangi kantor/administrasi, lapangan, dinas luar kota, dll ?
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Apakah Anda mempunyai sumber penghasilan yang lain? Beri keterangan
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Bersediakah Anda bekerja lembur bilamana diperlukan ?<br>(alasannya)
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Apakah Anda memiliki sendiri : rumah, mobil, sepeda motor dll.
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Referensi dari siapa yang dapat diperoleh untuk kepentingan Saudara ? (Nama, Alamat, No. telp)
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Berapa gaji yang Anda inginkan ?
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Kapan Anda bersedia mulai bekerja ?
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Apakah ada hal-hal lain yang ingin Anda sampaikan ?
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">
                    Kami yang bertanda tangan di bawah ini menyatakan bahwa keterangan yang kami berikan diatas adalah benar dan lengkap. Mulai Saya bekerja di perusahaan ini, Saya bersedia diambil tindakan sesuai dengan hukum yang berlaku ataupun sesuai dengan peraturan perusahaan bilamana keterangan diatas tidak benar.
                </td>
            </tr>
            <tr>
                <td>
                    <br><br><br>
                    <p>Tanggal .........................................................................</p>
                </td>
                <td class="text-center">
                    <br><br><br>
                    <p>(________________________________________________)</p>
                    <p>Nama Lengkap</p>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 14px; font-weight: bold;" class="text-center">
                    HASIL WAWANCARA
                </td>
            </tr>
            <tr>
                <td>
                    <p style="font-weight: bold;">CATATAN : </p>
                    <br><br><br>
                </td>
                <td>
                    <p style="font-weight: bold;">CATATAN : </p>
                    <br><br><br>
                </td>
            </tr>
            <tr>
                <td>PEWAWANCARA I</td>
                <td>PEWAWANCARA II</td>
            </tr>
            <tr>
                <td>
                    <table class="table-noborder text-center">
                        <tr class="border-bottom">
                            <td style="border-right: 1px solid black">Nama</td>
                            <td style="border-right: 1px solid black">Tanggal</td>
                            <td>Tanda Tangan</td>
                        </tr>
                        <tr>
                            <td style="border-right: 1px solid black" class="py-3"></td>
                            <td style="border-right: 1px solid black"></td>
                            <td></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="table-noborder text-center">
                        <tr class="border-bottom">
                            <td style="border-right: 1px solid black">Nama</td>
                            <td style="border-right: 1px solid black">Tanggal</td>
                            <td>Tanda Tangan</td>
                        </tr>
                        <tr>
                            <td style="border-right: 1px solid black" class="py-3"></td>
                            <td style="border-right: 1px solid black"></td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="table-noborder">
            <tr>
                <td width="200">Nilai Candidate HRD - Assessment</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Nilai Candidate User - Assessment</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Total</td>
                <td>:</td>
                <td></td>
            </tr>
        </table>
        <table class="table-noborder mt-2">
            <tr>
                <td>Konklusi</td>
                <td>:</td>
                <td><strong>Diterima / Tidak Diterima *</strong></td>
            </tr>
        </table>
        <div style="color: rgb(181, 181, 181); " class="mt-2">*) Coret yang tidak perlu</div>
    </div>

    <P style="page-break-before: always" />

    <div class="row">
        <table class="table-noborder text-center">
            <tr>
                <td style="vertical-align: bottom" class="text-left">
                    <p>Surabaya, ................................... 20..............</p>
                    <p>Yang mewawancarai,</p>
                    <br><br><br>
                    <p>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</p>
                </td>
                <td style="vertical-align: bottom">
                    <p>Disetujui,</p>
                    <br><br><br>
                    <p>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</p>
                </td>
            </tr>
        </table>

        <table class="table-noborder mt-2">
            <tr>
                <td width="150"><strong>Bukti Panggilan</strong></td>
                <td>:</td>
                <td>(melalui telepon/WA)</td>
            </tr>
            <tr>
                <td>Hari Tanggal</td>
                <td>:</td>
                <td>......................................................</td>
            </tr>
            <tr>
                <td>Jam</td>
                <td>:</td>
                <td>......................................................</td>
            </tr>
            <tr>
                <td>No. Telepon</td>
                <td>:</td>
                <td>......................................................</td>
            </tr>
            <tr>
                <td>Penerima</td>
                <td>:</td>
                <td>......................................................</td>
            </tr>
            <tr>
                <td>Penelepon</td>
                <td>:</td>
                <td>......................................................</td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td>......................................................</td>
            </tr>
        </table>
        <div style="color: rgb(181, 181, 181); " class="mt-2">*) Coret yang tidak perlu</div>

    </div>
</body>

</html>
