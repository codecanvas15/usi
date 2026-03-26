<x-card-data-table title="Personal data">
    <x-slot name="table_content">

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>NIK</label>
                    <p class="text-uppercase">{{ $model->NIK }}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Nama</label>
                    <p class="text-uppercase">{{ $model->name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Gender / Jenis Kelamin</label>
                    <p class="text-uppercase">{{ $model->jenis_kelamin }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Tempat Lahir</label>
                    <p class="text-uppercase">{{ $model->tempat_lahir }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Tanggal Lahir</label>
                    <p class="text-uppercase">{{ localDate($model->tanggal_lahir) }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Agama</label>
                    <p class="text-uppercase">{{ $model->religion }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Status Pernikahan</label>
                    <p class="text-uppercase">{{ $model->non_taxable_income->note ?? 'Tidak Ada' }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Hobby</label>
                    <p class="text-uppercase">{{ $model->hobby }}</p>
                </div>
            </div>
            @foreach (['weight' => 'berat badan', 'height' => 'tinggi', 'blood_type' => 'golongan darah'] as $key => $item)
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">{{ $item }}</label>
                        <p class="text-uppercase">{{ $model->$key }}</p>
                    </div>
                </div>
            @endforeach
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Pendidikan Terakhir</label>
                    <p class="text-uppercase">{{ $model->education?->name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Gelar</label>
                    <p class="text-uppercase">{{ $model->degree?->name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Foto</label>
                    <p>
                        @if ($model->file)
                            {{-- <x-button color="info" link="{{ url('storage/' . $model->file) }}" size="sm" icon="file" fontawesome target="_blank" /> --}}
                            <img src="{{ asset('/storage/' . $model->file) }}" width="190px" alt="">
                        @else
                            <x-button badge color="danger" size="sm" icon="eye-slash" label="no file" fontawesome />
                        @endif
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Rumah yang ditempati</label>
                    <p class="text-uppercase">{{ Str::headline($model->occupied_house) }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Nomor KTP</label>
                    <p class="text-uppercase">{{ $model->no_ktp }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Alamat Ktp</label>
                    <p class="text-uppercase">{{ $model->alamat }} <b>Kode Pos : {{ $model->postal_code }} </b></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Alamat Domisili</label>
                    <p class="text-uppercase">{{ $model->alamat_domisili }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">NPWP</label>
                    <p class="text-uppercase">{{ $model->npwp }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Email</label>
                    <p class="text-uppercase">{{ $model->email }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Nomor Hp</label>
                    <p class="text-uppercase">{{ $model->nomor_telepone }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Telepon Rumah</label>
                    <p class="text-uppercase">{{ $model->house_phone }}</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Kendaraan (Jenis / Merk / Tahun)</label>
                    <p class="text-uppercase">{{ $model->vehicle ?? '-' }} / {{ $model->vehicle_brand ?? '-' }} / {{ $model->vehicle_year ?? '-' }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Kepemilikan Kendaraan</label>
                    <p class="text-uppercase">{{ $model->vehicle_ownership ?? 'Milik Sendiri' }}</p>
                </div>
            </div>

            <div class="col-md-12">
                <hr>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Cabang</label>
                    <p class="text-uppercase">{{ $model->branch?->name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Divisi</label>
                    <p class="text-uppercase">{{ $model->division?->name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Posisi</label>
                    <p class="text-uppercase">{{ $model->position?->nama }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Status Kepegawaian</label>
                    <p class="text-uppercase">{{ $model->employment_status?->name }}</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Status Staff</label>
                    <p class="text-uppercase">{{ $model->staff_type }}</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Status</label>
                    <p class="text-uppercase">{{ $model->employee_status }}</p>
                </div>
            </div>

            @php
                $dates = [
                    'join_date' => 'tanggal masuk',
                    'end_date' => 'tanggal selesai',
                    'start_contract' => 'mulai kontrak',
                    'end_contract' => 'selesai kontrak',
                ];
            @endphp
            @foreach ($dates as $key => $item)
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">{{ $item }}</label>
                        <p class="text-uppercase">{{ $model->$key ? localDate($model->$key) : '-' }}</p>
                    </div>
                </div>
            @endforeach

            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Jatah Cuti</label>
                    <p class="text-uppercase">{{ $model->leave }}</p>
                </div>
            </div>

            <div class="col-md-12">
                <hr>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Alamat Tinggal Orangtua</label>
                    <p class="text-uppercase">{{ $model->parents_address }} <b>Kode Pos : {{ $model->parents_postal_code }} </b></p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Nomor Hp Ortu</label>
                    <p class="text-uppercase">{{ $model->parents_phone_number }}</p>
                </div>
            </div>

        </div>
    </x-slot>
</x-card-data-table>

<x-card-data-table title="Dokument {{ $title }}">
    <x-slot name="table_content">
        <x-table>
            <x-slot name="table_head">
                <th>Nama</th>
                <th>Nomor Kartu</th>
                <th>Masa Berlaku</th>
                <th>File</th>
                <th>Action</th>
            </x-slot>
            <x-slot name="table_body">
                @foreach ($model->employeeDocument as $item)
                    <tr>
                        <td>{{ $item->document_name }}</td>
                        <td>{{ $item->card_number }}</td>
                        <td>{{ $item->validity_period ? localDate($model->validity_period) : '' }}</td>
                        <td>
                            @if ($item->document_file)
                                <x-button color="info" link="{{ url('storage/' . $item->document_file) }}" size="sm" icon="file" fontawesome target="_blank" />
                            @else
                                <x-button badge color="danger" size="sm" icon="eye-slash" label="no file" fontawesome />
                            @endif
                        </td>
                        <td></td>
                    </tr>
                @endforeach
            </x-slot>
        </x-table>
    </x-slot>
</x-card-data-table>

<x-card-data-table title="informasi keluarga {{ $title }}">
    <x-slot name="table_content">
        <x-table>
            <x-slot name="table_head">
                <th>Tipe Keluarga</th>
                <th>Hubungan</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Pendidikan</th>
                <th>Posisi Terakhir</th>
                <th>Perusahaan Terakhir</th>
                <th>Action</th>
            </x-slot>
            <x-slot name="table_body">
                @foreach ($model->employeeFamilyTrees as $item)
                    <tr>
                        <td>Keluarga {{ $item->type }}</td>
                        <td>{{ $item->relation }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->gender }}</td>
                        <td>{{ $item->birth_place }}</td>
                        <td>{{ $item->birth_date ? localDate($item->birth_date) : '-' }}</td>
                        <td>{{ $item->education }}</td>
                        <td>{{ $item->last_position }}</td>
                        <td>{{ $item->last_company }}</td>
                    </tr>
                @endforeach
            </x-slot>
        </x-table>
    </x-slot>
</x-card-data-table>

<x-card-data-table title="Informasi Kesehatan">
    <x-slot name="table_content">
        @php
            $healt = $model->employeeHealthHistory;
        @endphp
        <div class="row">
            <div class="col-md-3 form-group">
                <label for="">Bagaimana Kondisi Kesehatan Sekarang</label>
                <p class="text-uppercase">{{ $healt->condition ?? '=' }}</p>
            </div>
            <div class="col-md-3 form-group">
                <label for="">Apakah pernah mengalami sakit keras / kecelakaan berat</label>
                <p class="text-uppercase">{{ $healt->description ?? '=' }}</p>
            </div>
            <div class="col-md-3 form-group">
                <label for="">Apakah ada efek samping yang dirasakan sekarang</label>
                <p class="text-uppercase">{{ $healt->description_2 ?? '=' }}</p>
            </div>
        </div>
    </x-slot>
</x-card-data-table>

<x-card-data-table>
    <x-slot name="table_content">
        <div class="d-flex justify-content-end gap-3">
            <x-button color="warning" :link="route('admin.employee.edit', $model)" label="edit" />
        </div>
    </x-slot>
</x-card-data-table>
