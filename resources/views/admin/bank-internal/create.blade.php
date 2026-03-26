@extends('layouts.admin.layout.index')

@php
    $main = 'bank-internal';
@endphp

@section('title', Str::headline("Create $main") . ' - ')

@section('css')
    <style>
        /* original idea http://www.bootply.com/phf8mnMtpe */
        .tree-coa {
            min-height: 20px;
            margin-bottom: 20px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05)
        }

        .tree-coa li {
            list-style-type: none;
            margin: 0;
            padding: 10px 5px 0 5px;
            position: relative
        }

        .tree-coa li::before,
        .tree-coa li::after {
            content: '';
            left: -20px;
            position: absolute;
            right: auto
        }

        .tree-coa li::before {
            border-left: 1px solid #999;
            bottom: 50px;
            height: 100%;
            top: 0;
            width: 1px
        }

        .tree-coa li::after {
            border-top: 1px solid #999;
            height: 20px;
            top: 30px;
            width: 25px
        }

        .tree-coa li span {
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            border: 1px solid #999;
            border-radius: 5px;
            display: block;
            padding: 3px 8px;
            text-decoration: none
        }

        .tree-coa li.parent_li>span {
            cursor: pointer
        }

        .tree-coa>ul>li::before,
        .tree-coa>ul>li::after {
            border: 0
        }

        .tree-coa li:last-child::before {
            height: 30px
        }

        .tree-coa li.parent_li>span:hover,
        .tree-coa li.parent_li>span:hover+ul li span {
            background: #eee;
            border: 1px solid #94a0b4;
            color: #000
        }
    </style>
@endsection

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
                        {{ Str::headline('Create ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action='{{ route("admin.$main.store") }}' method='post' id="form-create-bank-internal" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">{{ Str::headline('create ' . $main) }}</h3>
                        </div>
                        <div class="box-body">
                            @include('components.validate-error')
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="code" label="kode document" id="" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="type" id="type" label="jenis">
                                            <option value="kas">Kas</option>
                                            <option value="bank">Bank</option>
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
                                        <x-select name="coa_id" label="bank internal coa" id="select-coa">
                                            @if ($coa)
                                                <option value="{{ $coa->id }}" selected>{{ $coa->account_code }} - {{ $coa->name }}</option>
                                            @endif
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input name="logo" label="Logo" type="file"></x-input>
                                    </div>
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
                </div>

                <div class="col-md-4">
                    <x-card-data-table title='coa tree'>
                        <x-slot name="header_content">

                        </x-slot>
                        <x-slot name="table_content">

                            <div style="max-height: 900px; overflow-y: scroll" id="coa-tree">
                                {!! $coa_tree !!}
                            </div>

                        </x-slot>
                    </x-card-data-table>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-30 pb-20">
                <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                <x-button id="btnSave" type="submit" color="primary" label="Save data" />
            </div>
        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#bank-internal-sidebar');
    </script>
    <script>
        $(document).ready(() => {
            $('.tree-coa li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');

            $('.tree-coa li.parent_li > span').parent('li.parent_li').find(' > ul > li').hide('fast');

            $('.tree-coa li.parent_li > span').on('click', function(e) {
                var children = $(this).parent('li.parent_li').find(' > ul > li');

                if (children.is(":visible")) {
                    children.hide('fast');
                    $(this).attr('title', 'Expand this branch').find(' > i').addClass('fa-plus-square').removeClass('fa-minus-square');
                } else {
                    children.show('fast');
                    $(this).attr('title', 'Collapse this branch').find(' > i').addClass('fa-minus-square').removeClass('fa-plus-square');
                }
                e.stopPropagation();
            });
        })
    </script>
    <script>
        initSelect2Search('select-coa', "{{ route('admin.coa.select-for-bank-internal') }}", {
            id: "id",
            text: "name",
        }, 0, {
            'account_type': 'Cash & Bank'
        });

        var index = 0;

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
                        $('#no_rekening').addClass('is-invalid');
                        $('#error-message-for-no_rekening').text('No rekening sudah ada!');
                    } else {
                        $('#no_rekening').removeClass('is-invalid');
                        $('#error-message-for-no_rekening').text(null);
                    }
                },
            });
        }, 500));

        $('#form-create-bank-internal').submit(function() {

            if ($('#no_rekening').hasClass('is-invalid')) {
                $('#form-create-bank-internal').unbind('submit');
                showAlert('', 'Masih ada error yang belum diperbaiki!', 'warning');
            } else {
                $('#form-create-bank-internal').unbind('submit').submit();
            }
        });

        function addDetail() {
            index++;
            const html = `<div id="detail${index}" class="box">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <x-input type="text" label="text" label="Nama/Nomor Dokumen" placeholder="Nama/Nomor Dokumen" name="detail_name[]" autofocus required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <x-input type="text" class="commas-form" label="text" label="Batas Kredit" placeholder="Batas Kredit" name="detail_credit_limit[]" autofocus required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" label="Masa Berlaku Dari" name="detail_start_date[]" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" label="Masa Berlaku Sampai" name="detail_end_date[]" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label for="">Deskripsi</label>
                                                <textarea class="form-control mt-1" rows="3" placeholder="Deskripsi" name="detail_description[]" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <x-button type="submit" color="danger" label="Hapus Detail" onclick="deleteDetail(${index})" />
                                    </div>
                                </div>
                            </div>`;
            $('#details').append(html);
            document.getElementById(`detail${index}`).scrollIntoView();

            initDatePicker();
        }


        addDetail();

        function deleteDetail(index) {
            $(`#detail${index}`).remove();
        }
    </script>
@endsection
