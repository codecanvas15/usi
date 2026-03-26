@extends('layouts.admin.layout.index')

@php
    $main = 'fleet';
@endphp

@section('title', Str::headline("Create $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline('armada') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Create ' . 'armada') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
            <x-card-data-table title="{{ 'create armada' }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')

                    @csrf
                    @if ($model)
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-3">
                            <x-select name="type" id="select-type" required autofocus>
                                <option value="">Pilih Item</option>
                                <option value="darat">Darat</option>
                                <option value="laut">Laut</option>
                            </x-select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="name" id="" label="Nama Armada" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="merk" id="" label="Merk" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="quantity" id="" label="Kapasitas" helpers="" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="year" name="year" id="" label="tahun_pembuatan" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="project_id" id="" label="Project">
                                    <option value="">Pilih Project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-10" id="darat">

                    </div>

                    <div class="row mt-10" id="laut">

                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-end gap-3">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>
                </x-slot>

            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')

    <script>
        $(document).ready(function() {
            $('#select-type').change(function(e) {
                e.preventDefault();

                if (this.value) {
                    if (this.value == 'darat') {
                        $('#darat').html(`
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="vehicle_type" value="" id="" label="tipe kendaraan" required />
                                </div>
                            </div>
                        `);

                        $('.select2').select2();
                        $('#laut').html('');
                    } else if (this.value == 'laut') {
                        $('#laut').html(`
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="nomor_lambung" id="" label="nomor_lambung" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="number" name="panjang" id="" label="panjang" helpers="Meter" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="number" name="lebar" id="" label="lebar" helpers="Meter" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="gt" id="" label="gt" required />
                            </div>
                        </div>`);

                        $('#darat').html('');
                    } else {
                        $('#darat').html('');
                        $('#laut').html('');
                    }
                } else {
                    $('#darat').html('');
                    $('#laut').html('');
                }
            });
        });
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-garage-sidebar');
        sidebarActive('#fleet')
    </script>
@endsection
