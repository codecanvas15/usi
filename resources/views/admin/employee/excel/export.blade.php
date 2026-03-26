<table>
    <tr>
        <td colspan="5">
            <p><b>{{ getCompany()->name }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }}</b></p>
        </td>
        <td colspan="3">
        </td>
    </tr>
    <tr>
        <td colspan="8" align="center">
            <p><b>{{ $title }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="8" align="center">
            <p><b>TANGGAL : {{ $date }}</b></p>
        </td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">NIK</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Email</th>
            <th class="text-center">Alamat KTP</th>
            <th class="text-center">Alamat Domisili</th>
            <th class="text-center">No. HP</th>
            <th class="text-center">Tempat Lahir</th>
            <th class="text-center">Tgl. Lahir</th>
            <th class="text-center">No. HP Orang Tua</th>
            <th class="text-center">NPWP</th>
            <th class="text-center">Status Pernikahan</th>
            <th class="text-center">Tgl. Pernikahan</th>
            <th class="text-center">Tgl. Masuk</th>
            <th class="text-center">Tgl. Selesai</th>
            <th class="text-center">Mulai Kontrak</th>
            <th class="text-center">Selesai Kontrak</th>
            <th class="text-center">Berat Badan</th>
            <th class="text-center">Tinggi</th>
            <th class="text-center">Gol. Darah</th>
            <th class="text-center">Jatah Cuti</th>
            <th class="text-center">Hobi</th>
            <th class="text-center">Kendaraan</th>
            <th class="text-center">Tipe Staff</th>
            <th class="text-center">Agama</th>
            <th class="text-center">Gender</th>
            <th class="text-center">Status</th>
            <th class="text-center">Jabatan</th>
            <th class="text-center">Cabang</th>
            <th class="text-center">Divisi</th>
            <th class="text-center">Status Kepegawaian</th>
            <th class="text-center">Pendidikan Terakhir</th>
            <th class="text-center">Gelar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($employees as $employee)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $employee->NIK }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->alamat }}</td>
                <td>{{ $employee->alamat_domisili }}</td>
                <td>{{ $employee->nomor_telepone }}</td>
                <td>{{ $employee->tempat_lahir }}</td>
                <td>{{ $employee->tanggal_lahir }}</td>
                <td>{{ $employee->parents_phone_number }}</td>
                <td>{{ $employee->npwp }}</td>
                <td>{{ $employee->non_taxable_income->name . ' (' . $employee->non_taxable_income->note . ')' }}</td>
                <td>{{ $employee->marriage_date }}</td>
                <td>{{ \Carbon\Carbon::parse($employee->start_date)->translatedFormat('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($employee->end_date)->translatedFormat('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($employee->start_contract)->translatedFormat('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($employee->end_contract)->translatedFormat('d/m/Y') }}</td>
                <td>{{ $employee->weight }}</td>
                <td>{{ $employee->height }}</td>
                <td>{{ $employee->blood_type }}</td>
                <td>{{ $employee->leave }}</td>
                <td>{{ $employee->hobby }}</td>
                <td>{{ $employee->vehicle }}</td>
                <td>{{ $employee->staff_type }}</td>
                <td>{{ $employee->religion }}</td>
                <td>{{ $employee->jenis_kelamin }}</td>
                <td>{{ $employee->employee_status }}</td>
                <td>{{ $employee->position->name }}</td>
                <td>{{ $employee->branch->name }}</td>
                <td>{{ $employee->division->name }}</td>
                <td>{{ $employee->employment_status->name }}</td>
                <td>{{ $employee->education?->name ?? '-' }}</td>
                <td>{{ $employee->degree?->name ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>