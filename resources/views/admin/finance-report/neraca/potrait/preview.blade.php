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
                </div>
            </div>
            <div class="table-responsive mt-10">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="text-center">AKTIVA</th>
                        </tr>
                        <tr>
                            <th class="text-center">KODE REK.</th>
                            <th class="text-center">KETERANGAN</th>
                            <th class="text-center">BULAN INI</th>
                            <th class="text-center">BULAN LALU</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($aktiva as $key => $item)
                            <tr>
                                <td class="text-center">
                                    @if ($item['is_parent'] || $item['is_total'])
                                        <b>
                                    @endif
                                    {{ $item['code'] ?? '' }}
                                    @if ($item['is_parent'] || $item['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="{{ $item['is_total'] ? 'text-end' : '' }}">
                                    @for ($i = 0; $i < $item['indent']; $i++)
                                        &nbsp;
                                    @endfor
                                    @if ($item['is_parent'] || $item['is_total'])
                                        <b>
                                    @endif
                                    {{ $item['name'] ?? '' }}
                                    @if ($item['is_parent'] || $item['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($item['is_parent'] || $item['is_total'])
                                        <b>
                                    @endif
                                    @if ($item['balance'] != 0)
                                        {{ formatNumber($item['balance']) }}
                                    @endif
                                    @if ($item['total_balance'] != 0)
                                        {{ formatNumber($item['total_balance']) }}
                                    @endif
                                    @if ($item['is_parent'] || $item['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($item['is_parent'] || $item['is_total'])
                                        <b>
                                    @endif
                                    @if ($item['prev_balance'] != 0)
                                        {{ formatNumber($item['prev_balance']) }}
                                    @endif
                                    @if ($item['total_prev_balance'] != 0)
                                        {{ formatNumber($item['total_prev_balance']) }}
                                    @endif
                                    @if ($item['is_parent'] || $item['is_total'])
                                        </b>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td class="text-end">
                                <b>TOTAL AKTIVA</b>
                            </td>
                            <td class=" text-end">
                                <b>{{ formatNumber(array_sum(array_column($aktiva, 'balance'))) }}</b>
                            </td>
                            <td class=" text-end">
                                <b>{{ formatNumber(array_sum(array_column($aktiva, 'prev_balance'))) }}</b>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="text-center">PASIVA (KEWAJIBAN & EKUITAS)</th>
                        </tr>
                        <tr>
                            <th class="text-center">KODE REK.</th>
                            <th class="text-center">KETERANGAN</th>
                            <th class="text-center">BULAN INI</th>
                            <th class="text-center">BULAN LALU</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pasiva as $key => $item)
                            <tr>
                                <td class="text-center">
                                    @if ($item['is_parent'] || $item['is_total'])
                                        <b>
                                    @endif
                                    {{ $item['code'] ?? '' }}
                                    @if ($item['is_parent'] || $item['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="{{ $item['is_total'] ? 'text-end' : '' }}">
                                    @for ($i = 0; $i < $item['indent']; $i++)
                                        &nbsp;
                                    @endfor
                                    @if ($item['is_parent'] || $item['is_total'])
                                        <b>
                                    @endif
                                    {{ $item['name'] ?? '' }}
                                    @if ($item['is_parent'] || $item['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($item['is_parent'] || $item['is_total'])
                                        <b>
                                    @endif
                                    @if ($item['balance'] != 0)
                                        {{ formatNumber($item['balance']) }}
                                    @endif
                                    @if ($item['total_balance'] != 0)
                                        {{ formatNumber($item['total_balance']) }}
                                    @endif
                                    @if ($item['is_parent'] || $item['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($item['is_parent'] || $item['is_total'])
                                        <b>
                                    @endif
                                    @if ($item['prev_balance'] != 0)
                                        {{ formatNumber($item['prev_balance']) }}
                                    @endif
                                    @if ($item['total_prev_balance'] != 0)
                                        {{ formatNumber($item['total_prev_balance']) }}
                                    @endif
                                    @if ($item['is_parent'] || $item['is_total'])
                                        </b>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td class="text-end">
                                <b>TOTAL PASIVA</b>
                            </td>
                            <td class=" text-end">
                                <b>{{ formatNumber(array_sum(array_column($pasiva, 'balance'))) }}</b>
                            </td>
                            <td class=" text-end">
                                <b>{{ formatNumber(array_sum(array_column($pasiva, 'prev_balance'))) }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-end">
                                <b>BALANCE (AKTIVA - PASIVA)</b>
                            </td>
                            <td class=" text-end">
                                <b>{{ formatNumber(array_sum(array_column($aktiva, 'balance')) - array_sum(array_column($pasiva, 'balance'))) }}</b>
                            </td>
                            <td class=" text-end">
                                <b>{{ formatNumber(array_sum(array_column($aktiva, 'prev_balance')) - array_sum(array_column($pasiva, 'prev_balance'))) }}</b>
                            </td>
                        </tr>
                    </tfoot>
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
