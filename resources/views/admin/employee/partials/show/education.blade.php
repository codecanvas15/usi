<x-card-data-table title="Pendidikan Formal {{ $title }}">
    <x-slot name="table_content">
        @foreach ($model->employeeFormalEducations as $item)
            <div class="row border-top border-primary mt-10 pt-20">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Jenjang</label>
                        <p>{{ $item->level }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Nama Pendidikan</label>
                        <p>{{ $item->name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">KOta</label>
                        <p>{{ $model->city }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Fakultas</label>
                        <p>{{ $item->faculty }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Jurusan</label>
                        <p>{{ $item->major }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Dari</label>
                        <p>{{ localDate($item->from) }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Sampai</label>
                        <p>{{ $item->to ? localDate($item->to) : '-' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">IPK</label>
                        <p>{{ $item->gpa }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Tahun Lulus</label>
                        <p>{{ $item->graduate }}</p>
                    </div>
                </div>
            </div>
        @endforeach

        <hr class="border-top border-primary">

        <h5>Alasan Pendidikan Terkahir</h5>
        <div class="row mt-10 pt-10">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Alasan Memilih Jurusan</label>
                    <p>{{ $model->reason_for_choosing_the_major }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Topik Tesis</label>
                    <p>{{ $model->theses_topic }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Alasan Tidak lulus</label>
                    <p>{{ $model->reason_for_not_passing }}</p>
                </div>
            </div>
        </div>
    </x-slot>
</x-card-data-table>

<x-card-data-table title="Pendidikan Non Formal {{ $title }}">
    <x-slot name="table_content">
        @foreach ($model->employeeInformalEducations as $item)
            <div class="row border-top border-primary py-10 mt-10">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Nama Pendidikan</label>
                        <p>{{ $item->name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Penyelenggara</label>
                        <p>{{ $item->initiator }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Lama</label>
                        <p>{{ $item->lama }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Tahun</label>
                        <p>{{ $item->year }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">DIbiayai Oleg</label>
                        <p>{{ $item->financed_by }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </x-slot>
</x-card-data-table>

<x-card-data-table title="Kemampuan Bahasa">
    <x-slot name="table_content">
        @foreach ($model->employeeLanguages as $item)
            <div class="row border-top border-primary mt-10 pt-10">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Bahaasa</label>
                        <p>{{ $item->language }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Bebicara</label>
                        <p>{{ $item->speak }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Mendengan</label>
                        <p>{{ $item->listening }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Menulis</label>
                        <p>{{ $item->wrire }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Membaca</label>
                        <p>{{ $item->read }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </x-slot>
</x-card-data-table>

<x-card-data-table title="Pendidikan Khusus">
    <x-slot name="table_content">
        @foreach ($model->employeeSpecialEducations as $item)
            <div class="row border-top border-primary mt-10 pt-10">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Nama Pendidikan</label>
                        <p>{{ $item->name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Tahun</label>
                        <p>{{ $item->year }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </x-slot>
</x-card-data-table>

<x-card-data-table>
    <x-slot name="table_content">
        <div class="d-flex justify-content-end gap-3">
            <x-button color="warning" :link="route('admin.employee.edit.step2', [
                'employee_id' => $model->id,
            ])" label="edit" />
        </div>
    </x-slot>
</x-card-data-table>
