@extends('layouts.admin.layout.index')

@php
    $main = 'bank-internal';
@endphp

@section('title', Str::headline("Edit $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form id="form" action='{{ route("admin.$main.update", $model) }}' method='post' enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ Str::headline('edit ' . $main) }}</h3>
                </div>
                <div class="box-body">
                    @include('components.validate-error')
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" name="code" label="kode document" value="{{ $model->code }}" id="" required autofocus />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="type" id="type" label="jenis">
                                    <option value="kas" {{ $model->type == 'kas' ? 'selected' : '' }}>Kas</option>
                                    <option value="bank" {{ $model->type === 'bank' ? 'selected' : '' }}>Bank</option>
                                </x-select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" name="nama_bank" label="nama bank" id="" value="{{ $model->nama_bank ?? '' }}" required autofocus />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" name="branch_name" label="cabang" id="" value="{{ $model->branch_name ?? '' }}" autofocus />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="on_behalf_of" name="on_behalf_of" label="atas nama" value="{{ $model->on_behalf_of ?? '' }}" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="no_rekening" name="no_rekening" label="nomor rekening" value="{{ $model->no_rekening ?? '' }}" useCustomError required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="coa_id" label="bank_ internal coa" id="select-coa">
                                    @if ($model->coa_id)
                                        <option value="{{ $model->coa_id }}" selected>{{ $model->coa->account_code }} - {{ $model->coa->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input name="logo" label="Logo" type="file"></x-input>
                            </div>
                            @if ($model->logo)
                                <img src="{{ asset('storage/' . $model->logo) }}" alt="" srcset="">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="box-title">Detail</h3>
                        </div>
                        <div class="col-auto">
                            <x-button type="button" color="primary" label="Tambah Detail" onclick="addDetail();" />
                        </div>
                    </div>
                </div>
            </div>

            <div id="details"></div>

            <div class="d-flex justify-content-end gap-3 mt-30">
                <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                <x-button id="btnSave" type="button" color="primary" label="Save data" />
            </div>
        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        initSelect2Search('select-coa', "{{ route('admin.coa.select-for-bank-internal') }}", {
            id: "id",
            text: "name",
        }, 0, {
            'account_type': 'Cash & Bank'
        });

        var index = 0;
        var no_rekening = @json($model->no_rekening);

        $('#no_rekening').keyup(debounce(function() {
            $.ajax({
                url: `${base_url}/bank-internal/is-no-rek-exists`,
                method: 'POST',
                dataType: 'JSON',
                data: {
                    _token: token,
                    no_rekening: $(this).val()
                },
                success: function(data) {
                    if (data.is_exists == true) {
                        if (no_rekening !== $('#no_rekening').val()) {
                            $('#no_rekening').addClass('is-invalid');
                            $('#error-message-for-no_rekening').text('No rekening sudah ada!');
                        }
                    } else {
                        $('#no_rekening').removeClass('is-invalid');
                        $('#error-message-for-no_rekening').text(null);
                    }
                },
            });
        }, 500));

        $('#btnSave').click(function() {
            if ($('#no_rekening').hasClass('is-invalid')) {
                showAlert('', 'Masih ada error yang belum diperbaiki!', 'warning');
            } else {
                $('#form').submit();
            }
        });

        function getDetail() {
            $.ajax({
                url: "/bank-internal/" + "{{ $model->id }}" + "/detail",
                method: "GET",
                success: function(data) {
                    console.log(data);
                    if (data.data.length > 0) {
                        data.data.forEach(function(detail) {
                            index++;
                            const html = `<div id="detail${index}" class="box">
                                            <div class="box-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="text" label="text" label="Nama/Nomor Dokumen" placeholder="Nama/Nomor Dokumen" name="detail_name[]" value="${detail.name}" autofocus />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="text" class="commas-form" label="text" label="Batas Kredit" placeholder="Batas Kredit" name="detail_credit_limit[]" value="${detail.credit_limit}" autofocus />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" label="Masa Berlaku Dari" name="detail_start_date[]" value="${localDate(detail.start_date)}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" label="Masa Berlaku Sampai" name="detail_end_date[]" value="${localDate(detail.end_date)}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mt-2">
                                                        <div class="form-group">
                                                            <label for="">Deskripsi</label>
                                                            <textarea class="form-control mt-1" rows="3" placeholder="Deskripsi" name="detail_description[]">${detail.description}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <x-button type="submit" color="danger" label="Hapus Detail" onclick="deleteDetail(${index})" />
                                                </div>
                                            </div>
                                        </div>`;
                            $('#details').append(html);

                            initDatePicker();
                        });
                    }
                }
            });
        }
        getDetail();

        function addDetail() {
            index++;
            const html = `<div id="detail${index}" class="box">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-input type="text" label="text" label="Nama/Nomor Dokumen" placeholder="Nama/Nomor Dokumen" name="detail_name[]" autofocus />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-input type="text" class="commas-form" label="text" label="Batas Kredit" placeholder="Batas Kredit" name="detail_credit_limit[]" autofocus />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" label="Masa Berlaku Dari" name="detail_start_date[]" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" label="Masa Berlaku Sampai" name="detail_end_date[]" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <div class="form-group">
                                            <label for="">Deskripsi</label>
                                            <textarea class="form-control mt-1" rows="3" placeholder="Deskripsi" name="detail_description[]"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <x-button type="submit" color="danger" label="Hapus Detail" onclick="deleteDetail(${index})" />
                                </div>
                            </div>
                        </div>`;
            $('#details').append(html);
            initDatePicker();
            document.getElementById(`detail${index}`).scrollIntoView();
        }

        function deleteDetail(index) {
            $(`#detail${index}`).remove();
        }
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#bank-internal-sidebar');
    </script>
@endsection
