@extends('layouts.admin.layout.index')

@php
    $main = 'item-receiving-report';
    $title = 'Laporan Penerimaan barang';
@endphp

@section('title', Str::headline("Tambah $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Tambah ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('')
        <form action="" method="post">
            @csrf
        </form>

    @endcan
@endcan

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        $(document).ready(function() {
            //
        });
    </script>

    <script>
        sidebarMenuOpen('#purchase-menu')
        sidebarActive('#item-receiving-report');
    </script>
@endsection
