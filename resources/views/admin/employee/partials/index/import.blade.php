<x-button link='{{ route("admin.$main.import") }}' color="info" icon="download" label="import" dataToggle="modal" dataTarget="#import-modal" />

<x-modal title="import data" id="import-modal" headerColor="info">
    <x-slot name="modal_body">
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="" class="d-block mb-1">1. Download Format</label>
                <x-button link='{{ route("admin.$main.import-format") }}' color="info" icon="download" label="import format" />
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="" class="d-block mb-1">2. Lihat Panduan</label>
                <x-button color="primary" label="lihat panduan" dataToggle="modal" dataTarget="#panduan-modal" />
                <x-modal title="Panduan mengisi format import excel" id="panduan-modal" headerColor="info" modalSize="800">
                    <x-slot name="modal_body">
                        <p>Berikut ini adalah panduan mengisi kolom excel, harap diperhatikan dengan benar agar tidak terjadi error pada saat import data.</p>
                        <ul>
                            <li>NIK : Wajib diisi</li>
                            <li>Nama : Wajib diisi</li>
                            <li>Email : Opsional</li>
                            <li>Nomor Telepon : Wajib diisi</li>
                            <li>Alamat : Wajib diisi</li>
                            <li>Alamat Domisili : Opsional</li>
                            <li>Tempat Lahir : Wajib diisi</li>
                            <li>Tanggal Lahir : Wajib diisi, format tanggal (dd/mm/yyyy)</li>
                            <li>Jenis Kelamin : Wajib diisi, format (Laki-Laki / Perempuan)</li>
                            <li>Status Pernikahan : Wajib diisi, format (sudah / belum)</li>
                            <li>Nomor KTP : Wajib diisi</li>
                            <li>NPWP : Opsional</li>
                            <li>Tanggal Masuk : Opsional, format tanggal (dd/mm/yyyy)</li>
                            <li>Tanggal Keluar : Opsional, format tanggal (dd/mm/yyyy)</li>
                            <li>Nomor BPJS : Opsional</li>
                            <li>BPJS Dues : Opsional</li>
                            <li>Jatah Cuti : Opsional</li>
                            <li>Cabang : Opsional, format jika diisi (name dari master branch, contoh: Surabaya)</li>
                            <li>Divisi : Opsional</li>
                            <li>Posisi : Opsional</li>
                            <li>Status Kepegawaian : Opsional, format jika diisi (aktif, non_aktif, calon_karyawan)</li>
                            <li>Pendidikan : Opsional, format jika diisi (nama dari master pendidikan, contoh: SMA)</li>
                            <li>Jurusan : Opsional, format jika diisi (nama dari master jurusan, contoh: Akuntansi)</li>
                            <li>Employee Status : Opsional, format jika diisi (nama dari master status karyawan/pegawai, contoh: Tetap)</li>
                            <li>Start Contract : Opsional, format tanggal (dd/mm/yyyy)</li>
                            <li>End Contract : Opsional, format tanggal (dd/mm/yyyy)</li>
                            <li>Deposit Asset Employee : Opsional</li>
                            <li>Deposit Asset Company : Opsional</li>
                            <li>Exit Interview : Opsional</li>
                        </ul>
                    </x-slot>
                </x-modal>
            </div>
        </div>
        <div class="mt-10">
            <form action='{{ route("admin.$main.import") }}' method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <x-input type="file" label="3. import" name="file" required />
                </div>
                <x-button color="info" icon="download" label="import" />
            </form>
        </div>

    </x-slot>
</x-modal>
