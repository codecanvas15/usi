@extends('layouts.admin.layout.index')

@php
    $main = 'master-loyalty';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

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
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection
@section('content')
    <x-card-data-table title="{{ 'detail ' . $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            <form class="form-data" id="form-data" action="{{ route('admin.' . $main . '.update', ['master_loyalty' => $data->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nilai Bawah</label>
                                <input type="text" class="form-control" id="nilai_bawah" name="nilai_bawah" placeholder="Masukkan Nama Bawah" required value="{{ $data->nilai_bawah }}">

                                @error('nilai_bawah')
                                    <small class="text-danger error_name">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nilai Atas</label>
                                <input type="text" class="form-control" id="nilai_atas" name="nilai_atas" placeholder="Masukkan Nilai Atas" required value="{{ $data->nilai_atas }}">
                                @error('nilai_atas')
                                    <small class="text-danger error_name">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Bonus</label>
                                <input type="text" class="form-control comma-on-blur" id="bonus" name="bonus" placeholder="Masukkan Bonus" required value="{{ $data->bonus }}">
                                @error('bonus')
                                    <small class="text-danger error_name">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="form-actions">
                <div class="text-right">
                    <button type="submit" onclick="$('#form-data').trigger('submit')" class="btn btn-primary btn-save" value="save">Simpan</button>
                    <button type="button" class="btn btn-danger" onclick="showDeleteConfirm({{ $data->id }})">Hapus</button>
                    <form id="delete-form{{ $data->id }}" action="{{ route('admin.' . $main . '.destroy', ['master_loyalty' => $data->id]) }}" method="POST" class="d-none form-data">
                        <input type="hidden" name="_method" value="DELETE">
                        @csrf
                    </form>
                    <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-warning">Batal</a>
                </div>
            </div>
        </x-slot>
    </x-card-data-table>

@endsection

@section('js')
    <script src="{{ asset('app-assets/js/select/branch.js') }}"></script>
    <script src="{{ asset('app-assets/js/master_loyalty.js') }}"></script>
    <script>
        $('.menu-master-loyalty').addClass('active');
    </script>
@endsection
