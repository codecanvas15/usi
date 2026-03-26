@extends('guest.layout.app')
@php
    $main = 'labor-application';
    $title = 'lamaran pekerjaan';
@endphp

@section('title', Str::headline("tambah $title") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('content')
    <form action="{{ route('guest.labor-application.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <x-card-data-table :title='"tambah $title"'>
            <x-slot name="header_content">
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row border-primary border-bottom pb-10">
                    <input type="hidden" name="labor_demand_detail_id" value="{{ $labor_demand_detail->id }}">
                    <div class="col-md-3">
                        <span class="text-info">{{ Str::headline('kode') }}</span>
                        <h3>{{ $labor_demand_detail->labor_demand->code }}</h3>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-2">
                        <span class="text-info">{{ Str::headline('divisi') }}</span>
                        <h4>{{ $labor_demand_detail->labor_demand->division?->name }}</h4>
                    </div>
                    <div class="col-md-2">
                        <span class="text-info">{{ Str::headline('posisi yang dibutuhkan') }}</span>
                        <h4>{{ $labor_demand_detail->position_name }}</h4>
                    </div>
                    <div class="col-md-2">
                        <span class="text-info">{{ Str::headline('penempatan') }}</span>
                        <h4>{{ $labor_demand_detail->labor_demand->location }}</h4>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-2">
                        <span class="text-info">{{ Str::headline('maksimal umur') }}</span>
                        <h4>{{ $labor_demand_detail->age }} Tahun</h4>
                    </div>
                    <div class="col-md-2">
                        <span class="text-info">{{ Str::headline('jenis kelamin') }}</span>
                        <h4>{{ $labor_demand_detail->gender }}</h4>
                    </div>
                    <div class="col-md-2">
                        <span class="text-info">{{ Str::headline('minimal pendidikan') }}</span>
                        <h4>{{ $labor_demand_detail->education->name }}</h4>
                    </div>
                    <div class="col-md-2">
                        <span class="text-info">{{ Str::headline('lulusan') }}</span>
                        <h4 class="text-uppercase">{{ $labor_demand_detail->degree->name }}</h4>
                    </div>
                    <div class="col-md-2">
                        <span class="text-info">{{ Str::headline('minimal pengalaman kerja') }}</span>
                        <h4>{{ $labor_demand_detail->long_work_experience }} Tahun</h4>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-6">
                        <span class="text-info">{{ Str::headline('pengalaman kerja') }}</span>
                        <h5>{{ $labor_demand_detail->work_experience }}</h5>
                    </div>
                    <div class="col-md-6">
                        <span class="text-info">{{ Str::headline('skill yang dibutuhkan') }}</span>
                        <h5>{{ $labor_demand_detail->skills }}</h5>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-6">
                        <span class="text-info">{{ Str::headline('deskripsi pekerjaan') }}</span>
                        <h5>{{ $labor_demand_detail->job_description }}</h5>
                    </div>
                    <div class="col-md-6">
                        <span class="text-info">{{ Str::headline('keterangan lain') }}</span>
                        <h5>{{ $labor_demand_detail->description }}</h5>
                    </div>
                </div>
                <div class="row mt-20">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="name" label="nama" id="" required value="{{ @old('name') }}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="email" label="email" id="" required value="{{ @old('email') }}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="address" label="alamat ktp" id="" required value="{{ @old('address') }}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="address_domicil" label="alamat domisili" id="" required value="{{ @old('address_domicil') }}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="number" name="phone" label="nomor hp" id="" helpers="contoh: 0812xxx" required value="{{ @old('phone') }}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="date_of_birth" label="tanggal lahir" id="" required value="{{ localDate(@old('date_of_birth')) }}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="place_of_birth" label="tempat lahir" id="" required value="{{ @old('place_of_birth') }}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <x-select name="gender" id="gender" label="gender" label="jenis kelamin" required>
                            <option value="">Pilih Item</option>
                            <option value="Laki-Laki" {{ old('gender') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </x-select>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select name="marital_status" id="marital_status" label="status pernikahan" value="{{ $model->marital_status ?? '' }}" required>
                                <option value="">Pilih Item</option>
                                <option value="1" {{ old('maritial_status') == '1' ? 'selected' : '' }}>Sudah Menikah</option>
                                <option value="0" {{ old('maritial_status') == '2' ? 'selected' : '' }}>Belum Menikah</option>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="identity_card_number" label="nomor identitas (KTP / SIM)" id="" required value="{{ old('identity_card_number') }}" />
                        </div>
                    </div>
                </div>
                <div class="row mt-30">
                    <h4>File Lampiran</h4>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="file_path[]" label="Formulir Lamaran" id="" helpers="Max 4 mb" required />
                            <input type="hidden" name="file_type[]" value="Formulir Lamaran">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="file_path[]" label="KTP" id="" helpers="Max 4 mb" required />
                            <input type="hidden" name="file_type[]" value="KTP">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="file_path[]" label="SIM" id="" helpers="Max 4 mb" />
                            <input type="hidden" name="file_type[]" value="SIM">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="file_path[]" label="Ijazah" id="" helpers="Max 4 mb" />
                            <input type="hidden" name="file_type[]" value="Ijazah">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="file_path[]" label="Sertifikasi pendukung" id="" helpers="Max 4 mb" />
                            <input type="hidden" name="file_type[]" value="Sertifikasi pendukung">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="file_path[]" label="Medical Check Up" id="" helpers="Max 4 mb" />
                            <input type="hidden" name="file_type[]" value="Medical Check Up">
                        </div>
                    </div>
                </div>
                <div class="row mt-30">
                    <h4>Kontak darurat</h4>
                    <div id="row-emergency-contact"></div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-2">
                    <x-button color="primary" label="kirim lamaran" icon="save" fontawesome />
                </div>
            </x-slot>

        </x-card-data-table>
    </form>
@endsection

@section('js')

    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>

    <script>
        let emergencyContactIndex = 0;

        $(document).ready(function() {
            const initializeEmergencyContact = () => {

                const deleteEmergencyContact = (row_index) => {
                    $(`#emergency-row-${row_index}`).remove();
                };

                const addEmergencyContact = (row_index) => {
                    emergencyContactIndex++;

                    let btn = '';

                    if (row_index == 0) {
                        btn = `<x-button color="primary" id="add-emergency-contact" icon="plus" fontawesome size="sm" />`;
                    } else {
                        btn = `<x-button color="danger" id="remove-emergency-contact-${row_index}" icon="minus" fontawesome size="sm" />`;
                    }

                    $('#row-emergency-contact').append(`
                        <div class="row" id="emergency-row-${row_index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_names[]" label="nama" id="" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_relationships[]" label="hubungan" id="" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_phones[]" label="nomor hp" id="" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_addresses[]" label="alamat" id="" required />
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-self-end">
                                <div class="form-group">
                                    ${btn}
                                </div>
                            </div>
                        </div>
                    `);

                    if (row_index == 0) {
                        $('#add-emergency-contact').click(function(e) {
                            e.preventDefault();
                            addEmergencyContact(emergencyContactIndex);
                        });
                    } else {
                        $(`#remove-emergency-contact-${row_index}`).click(function(e) {
                            e.preventDefault();
                            deleteEmergencyContact(row_index);
                        });
                    }
                };

                addEmergencyContact(emergencyContactIndex);
            };
            initializeEmergencyContact();
        });
    </script>
@endsection
