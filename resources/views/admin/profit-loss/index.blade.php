@extends('layouts.admin.layout.index')

@php
    $main = 'profit-loss';
@endphp

@section('title', Str::headline($main) . ' - ')

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
                        {{ Str::headline('report') }}
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline($main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ $main }}">
        <x-slot name="header_content">
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="from_date" label="from date" value="" id="service-from-date" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="to" label="to date" value="" id="service-to-date" required />
                    </div>
                </div>
                <div class="col-md-3 row align-self-end">
                    <div class="form-group">
                        <x-button type="button" color="primary" id="set-service-table" icon="search" fontawesome />
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <table role="table" class="table table-striped table-hover" style="border: 1pt solid rgba(0, 0, 0, 0.05);">
                <tbody role="rowgroup">
                    <tr>
                        <td colspan="100%"><b> Pendapatan </b></td>
                    </tr>
                    <tr style="border-top: 1pt solid rgb(0, 0, 0); border-bottom: 1pt solid rgb(0, 0, 0);">
                        <td style="text-indent: 2em;"> Total dari Pendapatan </td>
                        <td class="text-end"> 0,00 </td>
                        <td class=" padding-left-0"> </td>
                    </tr>
                    <tr>
                        <td colspan="100%"><b> Beban Pokok Pendapatan </b></td>
                    </tr>
                    <tr style="border-top: 1pt solid rgb(0, 0, 0); border-bottom: 1pt solid rgb(0, 0, 0);">
                        <td style="text-indent: 2em;"> Total dari Beban Pokok Pendapatan </td>
                        <td class="text-end"> 0,00 </td>
                        <td class=" padding-left-0"> </td>
                    </tr>
                    <tr>
                        <td><b>Laba Kotor</b></td>
                        <td class="font-weight-bold text-end"> 0,00 </td>
                        <td class=" padding-left-0"> </td>
                    </tr>
                    <tr>
                        <td colspan="100%"><b> Beban Operasional </b></td>
                    </tr>
                    <tr style="border-top: 1pt solid rgb(0, 0, 0); border-bottom: 1pt solid rgb(0, 0, 0);">
                        <td style="text-indent: 2em;"> Total dari Beban Operasional </td>
                        <td class="text-end"> 0,00 </td>
                        <td class=" padding-left-0"> </td>
                    </tr>
                    <tr>
                        <td><b>Laba Operasional</b></td>
                        <td class="font-weight-bold text-end"> 0,00 </td>
                        <td class=" padding-left-0"> </td>
                    </tr>
                    <tr>
                        <td colspan="100%"><b> Pendapatan (Beban Lain-lain) </b></td>
                    </tr>
                    <tr>
                        <td colspan="100%" class=" font-weight-bold index_child_label_1gjVD"> Pendapatan Lain-Lain
                        </td>
                    </tr>
                    <tr>
                        <td colspan="100%" class=" font-weight-bold index_child_label_1gjVD"> Beban Lain-Lain </td>
                    </tr>
                    <tr style="border-top: 1pt solid rgb(0, 0, 0); border-bottom: 1pt solid rgb(0, 0, 0);">
                        <td style="text-indent: 2em;"> Total dari Pendapatan (Beban Lain-lain) </td>
                        <td class="text-end"> 0,00 </td>
                        <td class=" padding-left-0"> </td>
                    </tr>
                    <tr>
                        <td><b>Laba (Rugi)</b></td>
                        <td class="font-weight-bold text-end"> 0,00 </td>
                        <td class=" padding-left-0"> </td>
                    </tr>
                </tbody>
            </table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        sidebarMenuOpen('#report-sidebar')
        sidebarActive('#profit-loss')
    </script>
@endsection
