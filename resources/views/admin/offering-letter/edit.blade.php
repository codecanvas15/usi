@extends('layouts.admin.layout.index')

@php
    $main = 'offering-letter';
    $title = 'Letter of Intent';
@endphp

@section('title', Str::headline("Edit $title") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("edit $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <form action="{{ route("admin.$main.update", $model) }}" method="post">
            @csrf
            @method('put')
            <x-card-data-table :title="'Edit ' . $title">
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" id="reference" label="kode" name="reference" value="{{ generate_code(\App\Models\OfferingLetter::class, 'reference', 'created_at', 'HRD-OL', branch_sort: get_current_branch()->sort ?? null, date: \Carbon\Carbon::now()->format('d-m-Y')) }}" readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="hidden" name="labor_application_id" value="{{ $model->labor_application_id }}">
                                <x-input type="text" id="reference" label="kode" value="{{ $model->laborApplication->code . ' - ' . ucwords($model->laborApplication->name) }}" required readonly>
                                    </x-select>
                            </div>
                        </div>
                    </div>
                    <div class="row border-primary border-top pt-20 mt-10 mb-10">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="nama" id="employeeName" value="{{ $model->laborApplication->name }}" readonly></x-input>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="tempat / tgl. lahir" id="pob" value="{{ $model->laborApplication->place_of_birth }}" readonly></x-input>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="alamat" id="address" value="{{ $model->laborApplication->address }}" readonly></x-input>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="jabatan" id="position" value="{{ $model->laborApplication->laborDemandDetail->position_name }}" readonly></x-input>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="departemen" id="division" value="{{ $model->laborApplication->laborDemandDetail->labor_demand->division->name }}" readonly></x-input>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="tempat penerimaan" id="workPlacement" value="{{ $model->laborApplication->laborDemandDetail->labor_demand->location }}" readonly></x-input>
                            </div>
                        </div>
                    </div>
                    <div class="row border-primary border-top mt-10 pt-20">
                        <h5><b>Generate Content</b></h5>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" name="nik" label="nik" id="nik" value="{{ $model->nik }}" useCustomError required></x-input>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" class="commas-form" name="salary" label="gaji" id="salary" value="{{ $model->salary }}" useCustomError required></x-input>
                            </div>
                        </div>
                    </div>
                    <div class="row content-generated mt-20">
                        <div class="col">
                            <h5><b>Generated Content</b></h5>
                            <textarea name="offering_letter" id="content-generated-textarea" cols="30" rows="10">{{ $model->offering_letter }}</textarea>
                        </div>
                    </div>
                </x-slot>
                <x-slot name="footer">
                    <div class="text-end">
                        <x-button color="primary" label="Simpan" icon="save" fontawesome id="button-save" />
                    </div>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $(document).ready(function() {
            let previousUrl = "{{ URL::previous() }}";
            let employeeNIK = "";
            let editor = null;

            let formatDate = (value, isDay = false) => {
                const months = [
                    'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember'
                ];

                const days = [
                    'Minggu',
                    'Senin',
                    'Selasa',
                    'Rabu',
                    'Kamis',
                    'Jumat',
                    'Sabtu'
                ];

                let newDate = new Date(value);
                let unix_timestamp = newDate.setDate(newDate.getDate());

                newDate = new Date(unix_timestamp);

                let day = newDate.getDay();
                let date = newDate.getDate();
                let month = newDate.getMonth();
                const year = newDate.getFullYear();

                if (date < 10) {
                    date = '0' + date;
                }

                if (isDay == true) {
                    return `${days[day]}, ${date} ${months[month]} ${year}`;
                } else {
                    return `${date} ${months[month]} ${year}`;
                }
            }

            initCommasForm();

            const initSelect2LaborApplication = () => {
                let opts = {
                    placeholder: "Pilih Lamaran",
                    minimumInputLength: 0,
                    allowClear: false,
                    width: "100%",
                    language: {
                        noResults: () => {
                            return "Data can't be found";
                        },
                    },
                    ajax: {
                        url: `${base_url}/select/labor-application`,
                        dataType: "json",
                        delay: 250,
                        type: "get",
                        data: ({
                            term
                        }) => {
                            let result = {};
                            result["search"] = term;
                            return result;
                        },
                        processResults: ({
                            data
                        }) => {
                            let final_data = data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: `${data.code} - ${ucwords(data.name.toLowerCase())}`,
                                };
                            });
                            return {
                                results: final_data,
                            };
                        },
                        cache: true,
                    },
                };
                $('#laborApplication').select2(opts);
            }
            initSelect2LaborApplication();

            const setCKEditor = () => {
                let employeeName = $('#employeeName').val();
                let pob = $('#pob').val();
                let address = $('#address').val();
                let position = $('#position').val();
                let division = $('#division').val();
                let workPlacement = $('#workPlacement').val();

                let salary = $('#salary').val();
                let nik = $('#nik').val();

                if (editor == null) {
                    $('.ck.ck-reset.ck-editor.ck-rounded-corners').remove();

                    ClassicEditor
                        .create(document.querySelector('#content-generated-textarea'))
                        .then(newEditor => {
                            editor = newEditor;
                        })
                        .catch(error => {
                            console.error(error);
                        });

                    setTimeout(() => {
                        editor.setData(`
                            <p>
                                <span style="background-color: transparent; color: #000000;">
                                    Kepada YTH, &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                    ${formatDate(new Date())}
                                    <br>
                                    Bapak/Ibu Direksi <br>
                                    PT USI Petrotrans Energi <br>
                                    di ${workPlacement}
                                </span>
                            </p>
                            <h3 style="text-align: center">
                                <span style="background-color: transparent; color: #000000"><strong><u>Letter of Intent</u></strong></span>
                            </h3>
                            <p>&nbsp;</p>
                            <p style="text-align: justify; color: #000000;">
                                <span style="background-color: transparent; color: #000000">Saya yang bertanda-tangan di bawah ini :</span>
                            </p>
                            <p>&nbsp;</p>
                            <p style="text-align: justify">
                                <span style="background-color: transparent; color: #000000">Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ${employeeName}</span>
                                <br/>
                                <span style="background-color: transparent; color: #000000">Tempat Tanggal Lahir &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ${pob}</span>
                                <br/>
                                <span style="background-color: transparent; color: #000000">Alamat &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ${address}</span>
                                <br/>
                                <span style="background-color: transparent; color: #000000">NIK &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ${nik}</span>
                            </p>
                            <p>&nbsp;</p>
                            <p style="text-align: justify; color: #000000;">
                                <span style="background-color: transparent; color: #000000">
                                    sehubungan dengan tawaran kerja pada {{ getCompany()->name }}, sebagai berikut :
                                </span>
                            </p>
                            
                            <p>&nbsp;</p>
                            <p style="text-align: justify; color: #000000;">
                                <span style="background-color: transparent; color: #000000">Jabatan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ${position}</span>
                                <br/>
                                <span style="background-color: transparent; color: #000000">Gaji &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : Rp ${formatRupiahWithDecimal(salary) == '0' ? 0 : formatRupiahWithDecimal(salary)}</span>
                                <br/>
                            </p>

                            <p>&nbsp;</p>
                            <p style="text-align: justify">
                                <span style="background-color: transparent; color: #000000">
                                    maka saya berminat dan bersedia untuk bekerja pada perusahaan tersebut dan sanggup untuk ditempatkan dimanapun sesuai lokasi kegiatan usaha dari perusahaan.
                                </span>
                            </p>
                            <p>&nbsp;</p>
                            <p style="text-align: justify">
                                <span style="background-color: transparent; color: #000000">
                                    Demikian surat ini saya buat dengan sebenar-benarnya, atas perhatiannya dan kerjasamanya saya sampaikan terimakasih.
                                </span>
                            </p>
                            <p>&nbsp;</p>
                            <p style="text-align: justify">
                                <span style="background-color: transparent; color: #000000">
                                    Hormat Saya
                                </span>
                            </p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p style="text-align: justify">
                                <span style="background-color: transparent; color: #000000">
                                    ${employeeName == '' ? 'Nama Karyawan' : employeeName}
                                </span>
                            </p>
                        `);
                    }, 500);
                } else {
                    editor.setData(`
                            <p>
                                <span style="background-color: transparent; color: #000000;">
                                    Kepada YTH, &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                    ${formatDate(new Date())}
                                    <br>
                                    Bapak/Ibu Direksi <br>
                                    PT USI Petrotrans Energi <br>
                                    di ${workPlacement}
                                </span>
                            </p>
                            <h3 style="text-align: center">
                                <span style="background-color: transparent; color: #000000"><strong><u>Letter of Intent</u></strong></span>
                            </h3>
                            <p>&nbsp;</p>
                            <p style="text-align: justify; color: #000000;">
                                <span style="background-color: transparent; color: #000000">Saya yang bertanda-tangan di bawah ini :</span>
                            </p>
                            <p>&nbsp;</p>
                            <p style="text-align: justify">
                                <span style="background-color: transparent; color: #000000">Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ${employeeName}</span>
                                <br/>
                                <span style="background-color: transparent; color: #000000">Tempat Tanggal Lahir &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ${pob}</span>
                                <br/>
                                <span style="background-color: transparent; color: #000000">Alamat &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ${address}</span>
                                <br/>
                                <span style="background-color: transparent; color: #000000">NIK &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ${nik}</span>
                            </p>
                            <p>&nbsp;</p>
                            <p style="text-align: justify; color: #000000;">
                                <span style="background-color: transparent; color: #000000">
                                    sehubungan dengan tawaran kerja pada {{ getCompany()->name }}, sebagai berikut :
                                </span>
                            </p>
                            
                            <p>&nbsp;</p>
                            <p style="text-align: justify; color: #000000;">
                                <span style="background-color: transparent; color: #000000">Jabatan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ${position}</span>
                                <br/>
                                <span style="background-color: transparent; color: #000000">Gaji &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : Rp ${formatRupiahWithDecimal(salary) == '0' ? 0 : formatRupiahWithDecimal(salary)}</span>
                                <br/>
                            </p>

                            <p>&nbsp;</p>
                            <p style="text-align: justify">
                                <span style="background-color: transparent; color: #000000">
                                    maka saya berminat dan bersedia untuk bekerja pada perusahaan tersebut dan sanggup untuk ditempatkan dimanapun sesuai lokasi kegiatan usaha dari perusahaan.
                                </span>
                            </p>
                            <p>&nbsp;</p>
                            <p style="text-align: justify">
                                <span style="background-color: transparent; color: #000000">
                                    Demikian surat ini saya buat dengan sebenar-benarnya, atas perhatiannya dan kerjasamanya saya sampaikan terimakasih.
                                </span>
                            </p>
                            <p>&nbsp;</p>
                            <p style="text-align: justify">
                                <span style="background-color: transparent; color: #000000">
                                    Hormat Saya
                                </span>
                            </p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p style="text-align: justify">
                                <span style="background-color: transparent; color: #000000">
                                    ${employeeName == '' ? 'Nama Karyawan' : employeeName}
                                </span>
                            </p>
                        `);
                }
            }
            setCKEditor();

            $('#laborApplication').change(function() {
                let value = $(this).val();
                if (value) {
                    $(`#errorSelectLaborApplication`).removeClass('border border-danger');
                    $(`#errorMsgLaborApplication`).html('');

                    $.ajax({
                        type: "post",
                        url: `${base_url}/labor-application-find-by-id`,
                        dataType: 'json',
                        data: {
                            _token: token,
                            id: value,
                        },
                        success: function(data) {
                            employeeNIK = data.employee.NIK;

                            $('#employeeName').val(ucwords(data.name.toLowerCase()));
                            $('#pob').val(data.place_of_birth);
                            $('#address').val(data.address);
                            $('#position').val(data.labor_demand_detail.position.nama);
                            $('#division').val(data.labor_demand_detail.labor_demand.division.name);
                            $('#workPlacement').val(data.labor_demand_detail.labor_demand.location);

                            setCKEditor();
                        }
                    });
                } else {
                    $(`#errorSelectLaborApplication`).addClass('border border-danger');
                    $(`#errorMsgLaborApplication`).html('Lamaran wajib dipilih.');
                }
            })

            $('#workLocation').keyup(debounce(function() {
                setCKEditor();
                if ($(this).val() == '') {
                    $('#workLocation').addClass('is-invalid');
                    $('#error-message-for-workLocation').text('Lokasi kerja wajib diisi.');
                } else {
                    $('#workLocation').removeClass('is-invalid');
                    $('#error-message-for-workLocation').text('');
                }
            }, 500))

            $('#startWorkDate').change(debounce(function() {
                setCKEditor();
            }, 500))

            $('#employmentStatus').keyup(debounce(function() {
                setCKEditor();
                if ($(this).val() == '') {
                    $('#employmentStatus').addClass('is-invalid');
                    $('#error-message-for-employmentStatus').text('Status karyawan & masa kontrak wajib diisi.');
                } else {
                    $('#employmentStatus').removeClass('is-invalid');
                    $('#error-message-for-employmentStatus').text('');
                }
            }, 500))

            $('#compensation').keyup(debounce(function() {
                setCKEditor();
                if ($(this).val() == '') {
                    $('#compensation').addClass('is-invalid');
                    $('#error-message-for-compensation').text('Kompensasi wajib diisi.');
                } else {
                    $('#compensation').removeClass('is-invalid');
                    $('#error-message-for-compensation').text('');
                }
            }, 500))

            $('#salary').keyup(debounce(function() {
                setCKEditor();
                if ($(this).val() == '') {
                    $('#salary').addClass('is-invalid');
                    $('#error-message-for-salary').text('Gaji wajib diisi.');
                } else {
                    $('#salary').removeClass('is-invalid');
                    $('#error-message-for-salary').text('');
                }
            }, 500))
            $('#salary').trigger('blur');

            $('#nik').keyup(debounce(function() {
                setCKEditor();
                if ($(this).val() == '') {
                    $('#nik').addClass('is-invalid');
                    $('#error-message-for-nik').text('Gaji wajib diisi.');
                } else {
                    $('#nik').removeClass('is-invalid');
                    $('#error-message-for-nik').text('');
                }
            }, 500))
            $('#nik').trigger('blur');

            $('#allowanceSalary').keyup(debounce(function() {
                setCKEditor();
                if ($(this).val() == '') {
                    $('#allowanceSalary').addClass('is-invalid');
                    $('#error-message-for-allowanceSalary').text('Tunjangan wajib diisi.');
                } else {
                    $('#allowanceSalary').removeClass('is-invalid');
                    $('#error-message-for-allowanceSalary').text('');
                }
            }, 500))
            $('#allowanceSalary').trigger('blur');

            $('#leaveDay').keyup(debounce(function() {
                setCKEditor();
                if ($(this).val() == '') {
                    $('#leaveDay').addClass('is-invalid');
                    $('#error-message-for-leaveDay').text('Hari cuti wajib diisi.');
                } else {
                    $('#leaveDay').removeClass('is-invalid');
                    $('#error-message-for-leaveDay').text('');
                }
            }, 500))

            $('#holidayAllowance').keyup(debounce(function() {
                setCKEditor();
                if ($(this).val() == '') {
                    $('#holidayAllowance').addClass('is-invalid');
                    $('#error-message-for-holidayAllowance').text('THR wajib diisi.');
                } else {
                    $('#holidayAllowance').removeClass('is-invalid');
                    $('#error-message-for-holidayAllowance').text('');
                }
            }, 500))
            $('#holidayAllowance').trigger('blur');

            $('#mailTo').keyup(debounce(function() {
                setCKEditor();
                if ($(this).val() == '') {
                    $('#mailTo').addClass('is-invalid');
                    $('#error-message-for-mailTo').text('Email wajib diisi.');
                } else {
                    $('#mailTo').removeClass('is-invalid');
                    $('#error-message-for-mailTo').text('');
                }
            }, 500));

            $('#dueDate').change(function() {
                setCKEditor();
            })

            $('#generate-button').click(function() {
                setCKEditor();
            })

            $('#btnSave').click(function() {
                let workLocation = $('#workLocation').val();
                let employmentStatus = $('#employmentStatus').val();
                let compensation = $('#compensation').val();
                let salary = $('#salary').val();
                let allowanceSalary = $('#allowanceSalary').val();
                let leaveDay = $('#leaveDay').val();
                let holidayAllowance = $('#holidayAllowance').val();
                let mailto = $('#mailTo').val();

                if ($('#laborApplication').val() == '' || $('#laborApplication').val() == null || workLocation == '' || employmentStatus == '' || compensation == '' || salary == '' || allowanceSalary == '' || leaveDay == '' || holidayAllowance == '' || mailto == '') {
                    showAlert('', 'Masih ada error yang belum diperbaiki!', 'warning');
                } else {
                    $('#form').submit();
                }
            })
        });
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#rekrutment-sidebar');
        sidebarActive('#offering-letter')
    </script>
@endsection
