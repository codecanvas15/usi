@extends('layouts.admin.layout.index')

@php
    $main = 'finance-report';
@endphp

@section('title', Str::headline($type) . ' - ')

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
                        {{ Str::headline($main) }}
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline($type) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="">
        <x-slot name="header_content">
        </x-slot>
        <x-slot name="table_content">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3 class="text-uppercase">laporan {{ Str::headline($type) }}</h3>
                    <h5 class="text-uppercase my-0">periode : {{ $period }}</h5>
                    @if ($branch)
                        <p class="font-small-2 text-uppercase my-0">Branch : {{ $branch->name }}</p>
                    @endif
                </div>
            </div>
            <div class="table-responsive mt-10">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">KODE REK.</th>
                            <th class="text-center">KETERANGAN</th>
                            <th class="text-center">BULAN INI</th>
                            <th class="text-center">S/D BULAN INI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_parent = 0;
                            $total_parent_prev = 0;
                        @endphp
                        @foreach ($data as $key => $item)
                            @foreach ($data[$key] as $key_subcategory => $subcategory)
                                @php
                                    $total_subcategory = 0;
                                    $total_subcategory_prev = 0;
                                @endphp
                                <tr>
                                    <td></td>
                                    <td><b>{{ Str::upper(Str::headline($key_subcategory)) }}</b></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @foreach ($item[$key_subcategory]['data'] as $detail)
                                    <tr>
                                        <td class="text-center">{{ Str::upper(Str::headline($detail['code'])) }}</td>
                                        <td>{{ Str::upper(Str::headline($detail['coa'])) }}</td>
                                        <td class="text-end">{{ formatNumber($detail['current_period']) }}</td>
                                        <td class="text-end">{{ formatNumber($detail['prev_period']) }}</td>
                                    </tr>
                                    @php
                                        if ($item[$key_subcategory]['type'] == 'plus') {
                                            $total_subcategory += $detail['current_period'];
                                            $total_subcategory_prev += $detail['prev_period'];
                                        } else {
                                            $total_subcategory -= $detail['current_period'];
                                            $total_subcategory_prev -= $detail['prev_period'];
                                        }
                                    @endphp
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td class="text-end"><b>TOTAL {{ Str::upper(Str::headline($key_subcategory)) }}</b></td>
                                    <td class="text-end"><b>{{ formatNumber($total_subcategory) }}</b></td>
                                    <td class="text-end"><b>{{ formatNumber($total_subcategory_prev) }}</b></td>
                                </tr>
                                @php
                                    $total_parent += $total_subcategory;
                                    $total_parent_prev += $total_subcategory_prev;
                                @endphp
                            @endforeach
                            <tr>
                                <td colspan="2"><b>{{ Str::upper(Str::headline($key)) }}</b></td>
                                <td class="text-end"><b>{{ formatNumber($total_parent) }}</b></td>
                                <td class="text-end"><b>{{ formatNumber($total_parent_prev) }}</b></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar')
        sidebarActive('#finance-report')
    </script>
@endsection
