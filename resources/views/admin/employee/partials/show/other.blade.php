<x-card-data-table title="Organisasi">
    <x-slot name="table_content">
        @foreach ($model->employeeOrganizations as $item)
            <div class="row border-top border-primary mt-10 pt-10">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Nama</label>
                        <p>{{ $item->name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Tempat</label>
                        <p>{{ $item->place }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Posisi</label>
                        <p>{{ $item->posisi }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Dari</label>
                        <p>{{ $item->from ? localDate($item->from) : '-' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Sampai</label>
                        <p>{{ $item->to ? localDate($item->to) : '-' }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </x-slot>
</x-card-data-table>

<x-card-data-table title="Referensi">
    <x-slot name="table_content">
        @foreach ($model->employeeReferences as $item)
            <div class="row border-top border-primary mt-10 pt-10">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Nama</label>
                        <p>{{ $item->name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Alamat</label>
                        <p>{{ $item->address }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Nomor Hp</label>
                        <p>{{ $item->phone }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Perusahaan</label>
                        <p>{{ $item->company }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Jabatan</label>
                        <p>{{ $item->position }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Hubungan</label>
                        <p>{{ $item->relation }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </x-slot>
</x-card-data-table>

<x-card-data-table title="Kenalan di dalam kantor">
    <x-slot name="table_content">
        @foreach ($model->employeeInsiders as $item)
            <div class="row border-top border-primary mt-10 pt-10">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Nama</label>
                        <p>{{ $item->name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Posisi</label>
                        <p>{{ $item->position }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Hubungan</label>
                        <p>{{ $item->relation }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </x-slot>
</x-card-data-table>

<x-card-data-table title="Psikotest">
    <x-slot name="table_content">
        @foreach ($model->employeePsikotests as $item)
            <div class="row border-top border-primary mt-10 pt-10">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Tempat</label>
                        <p>{{ $item->place }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Tanggal</label>
                        <p>{{ $item->date ? localDate($item->date) : '-' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Alasan</label>
                        <p>{{ $item->cause }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </x-slot>
</x-card-data-table>

<x-card-data-table title="kontak darurat">
    <x-slot name="table_content">
        @foreach ($model->employee_emergency_contacts as $item)
            <div class="row border-top border-primary pt-10 mt-10">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Nama</label>
                        <p>{{ $item->nama }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Hubungan</label>
                        <p>{{ $item->hubungan }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Nomor Hp</label>
                        <p>{{ $item->nomor_telepon }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Alamat</label>
                        <p>{{ $item->alamat }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </x-slot>
</x-card-data-table>

<x-card-data-table>
    <x-slot name="table_content">
        <div class="d-flex justify-content-end gap-3">
            <x-button color="warning" :link="route('admin.employee.edit.step5', [
                'employee_id' => $model->id,
            ])" label="edit" />
        </div>
    </x-slot>
</x-card-data-table>
