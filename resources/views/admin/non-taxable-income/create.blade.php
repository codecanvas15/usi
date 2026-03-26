@extends('layouts.admin.layout.index')

@section('title', Str::headline("Tambah $title") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('content')
    @can("create $permission")
        <div class="row">
            <div class="col-md-6">
                <x-card-data-table title="Tambah {{ $title }}">
                    <x-slot name="table_content">
                        @include("$view.__fields")
                    </x-slot>
                </x-card-data-table>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    @can("create $permission")
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script>
            sidebarMenuOpen('#master-sidebar');
            sidebarActive('#master-salary-sidebar');
            sidebarActive('#non-taxable-income');
        </script>
    @endcan
@endsection
