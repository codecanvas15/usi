<x-card-data-table title="Pengalaman kerja">
    <x-slot name="table_content">
        @foreach ($model->employeeWorkExperiences as $item)
            <div class="border-top border-primary pt-10 mt-10">
                <div class="row">
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

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Nama Perusahaan</label>
                            <p>{{ $item->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Telepon</label>
                            <p>{{ $item->phone }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Jumlah Karyawan</label>
                            <p>{{ $item->employee_count }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Jenis Perusahaan</label>
                            <p>{{ $item->type }}</p>
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
                            <label for="">Posisi Awal</label>
                            <p>{{ $item->beginning_position }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Posisi Akhir</label>
                            <p>{{ $item->end_position }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Atasan Lansung</label>
                            <p>{{ $item->supervisor }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Alasan Keluar</label>
                            <p>{{ $item->reason_for_leaving }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </x-slot>
</x-card-data-table>

<x-card-data-table>
    <x-slot name="table_content">
        <div class="d-flex justify-content-end gap-3">
            <x-button color="warning" :link="route('admin.employee.edit.step3', [
                'employee_id' => $model->id,
            ])" label="edit" />
        </div>
    </x-slot>
</x-card-data-table>
