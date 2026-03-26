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
                            <th>KODE REK.</th>
                            <th>KETERANGAN</th>
                            <th>BULAN INI</th>
                            <th>BULAN LALU</th>
                            <th></th>
                            <th>KODE REK.</th>
                            <th>KETERANGAN</th>
                            <th>BULAN INI</th>
                            <th>BULAN LALU</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $loop_data = $aktiva;
                            $second_loop_data = $pasiva;
                        @endphp

                        @if (count($aktiva) > count($pasiva))
                            @foreach ($loop_data as $key => $item)
                                <tr>
                                    <td class="text-center">
                                        @if ($item['is_parent'] || $item['is_total'])
                                            <b>
                                        @endif
                                        {{ $item['code'] }}
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
                                        {{ $item['name'] }}
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
                                    <td></td>
                                    @if (isset($second_loop_data[$key]))
                                        <td class="text-center">
                                            @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                                <b>
                                            @endif
                                            {{ $second_loop_data[$key]['code'] }}
                                            @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                                </b>
                                            @endif
                                        </td>
                                        <td class="{{ $second_loop_data[$key]['is_total'] ? 'text-end' : '' }}">
                                            @for ($i = 0; $i < $second_loop_data[$key]['indent']; $i++)
                                                &nbsp;
                                            @endfor
                                            @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                                <b>
                                            @endif
                                            {{ $second_loop_data[$key]['name'] }}
                                            @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                                </b>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                                <b>
                                            @endif
                                            @if ($second_loop_data[$key]['balance'] != 0)
                                                {{ formatNumber($second_loop_data[$key]['balance']) }}
                                            @endif
                                            @if ($second_loop_data[$key]['total_balance'] != 0)
                                                {{ formatNumber($second_loop_data[$key]['total_balance']) }}
                                            @endif
                                            @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                                </b>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                                <b>
                                            @endif
                                            @if ($second_loop_data[$key]['prev_balance'] != 0)
                                                {{ formatNumber($second_loop_data[$key]['prev_balance']) }}
                                            @endif
                                            @if ($second_loop_data[$key]['total_prev_balance'] != 0)
                                                {{ formatNumber($second_loop_data[$key]['total_prev_balance']) }}
                                            @endif
                                            @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                                </b>
                                            @endif
                                        </td>
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            @foreach ($second_loop_data as $key => $item)
                                <tr>
                                    @if (isset($loop_data[$key]))
                                        <td class="text-center">
                                            @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                                <b>
                                            @endif
                                            {{ $loop_data[$key]['code'] }}
                                            @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                                </b>
                                            @endif
                                        </td>
                                        <td class="{{ $loop_data[$key]['is_total'] ? 'text-end' : '' }}">
                                            @for ($i = 0; $i < $loop_data[$key]['indent']; $i++)
                                                &nbsp;
                                            @endfor
                                            @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                                <b>
                                            @endif
                                            {{ $loop_data[$key]['name'] }}
                                            @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                                </b>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                                <b>
                                            @endif
                                            @if ($loop_data[$key]['balance'] != 0)
                                                {{ formatNumber($loop_data[$key]['balance']) }}
                                            @endif
                                            @if ($loop_data[$key]['total_balance'] != 0)
                                                {{ formatNumber($loop_data[$key]['total_balance']) }}
                                            @endif
                                            @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                                </b>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                                <b>
                                            @endif
                                            @if ($loop_data[$key]['prev_balance'] != 0)
                                                {{ formatNumber($loop_data[$key]['prev_balance']) }}
                                            @endif
                                            @if ($loop_data[$key]['total_prev_balance'] != 0)
                                                {{ formatNumber($loop_data[$key]['total_prev_balance']) }}
                                            @endif
                                            @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                                </b>
                                            @endif
                                        </td>
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @endif
                                    <td></td>
                                    <td class="text-center">
                                        @if ($item['is_parent'] || $item['is_total'])
                                            <b>
                                        @endif
                                        {{ $item['code'] }}
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
                                        {{ $item['name'] }}
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
                        @endif

                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td>
                                <b>TOTAL AKTIVA</b>
                            </td>
                            <td class=" text-end">
                                <b>{{ formatNumber(array_sum(array_column($loop_data, 'balance'))) }}</b>
                            </td>
                            <td class=" text-end">
                                <b>{{ formatNumber(array_sum(array_column($loop_data, 'prev_balance'))) }}</b>
                            </td>
                            <td></td>
                            <td></td>
                            <td>
                                <b>TOTAL PASIVA</b>
                            </td>
                            <td class=" text-end">
                                <b>{{ formatNumber(array_sum(array_column($second_loop_data, 'balance'))) }}</b>
                            </td>
                            <td class=" text-end">
                                <b>{{ formatNumber(array_sum(array_column($second_loop_data, 'prev_balance'))) }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <b>BALANCE (AKTIVA - PASIVA)</b>
                            </td>
                            <td align="right">
                                <b>{{ formatNumber(array_sum(array_column($loop_data, 'balance')) - array_sum(array_column($second_loop_data, 'balance'))) }}</b>
                            </td>
                            <td align="right">
                                <b>{{ formatNumber(array_sum(array_column($loop_data, 'prev_balance')) - array_sum(array_column($second_loop_data, 'prev_balance'))) }}</b>
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
