@extends('layouts.admin.layout.index')

@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline("Detail $title") . ' - ')

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
                        {{ Str::headline('Detail ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table>
            <x-slot name="table_content">
                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link rounded active" data-bs-toggle="tab" href="#personal-data-tab" id="personal-data-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">{{ STr::headline('personal-data') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded" data-bs-toggle="tab" href="#pendidikan-tab" id="pendidikan-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">{{ STr::headline('pendidikan') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded" data-bs-toggle="tab" href="#penglaman-kerja-tab" id="penglaman-kerja-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">{{ STr::headline('pengalaman-kerja') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded" data-bs-toggle="tab" href="#minat-tab" id="minat-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">{{ STr::headline('minat') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded" data-bs-toggle="tab" href="#lain-lain-tab" id="lain-lain-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">{{ STr::headline('lain-lain') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded" data-bs-toggle="tab" href="#kelebihan-kekurangan-tab" id="kelebihan-kekurangan-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">{{ STr::headline('kelebihan-kekurangan') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded" data-bs-toggle="tab" href="#bank-tab" id="bank-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">{{ STr::headline('bank') }}</span>
                        </a>
                    </li>
                </ul>
            </x-slot>
        </x-card-data-table>

        <div class="tab-content mt-30">
            <div class="tab-pane active" id="personal-data-tab" role="tabpanel">
                @include('admin.employee.partials.show.personal')
            </div>
            <div class="tab-pane" id="pendidikan-tab" role="tabpanel">
                @include('admin.employee.partials.show.education')
            </div>
            <div class="tab-pane" id="penglaman-kerja-tab" role="tabpanel">
                @include('admin.employee.partials.show.work')
            </div>
            <div class="tab-pane" id="minat-tab" role="tabpanel">
                @include('admin.employee.partials.show.interest')
            </div>
            <div class="tab-pane" id="lain-lain-tab" role="tabpanel">
                @include('admin.employee.partials.show.other')
            </div>
            <div class="tab-pane" id="kelebihan-kekurangan-tab" role="tabpanel">
                @include('admin.employee.partials.show.stregth-weakneses')
            </div>
            <div class="tab-pane" id="bank-tab" role="tabpanel">
                @include('admin.employee.partials.show.bank')
            </div>
        </div>
    @endcan
@endsection

@section('js')
    {{-- <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script> --}}
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-employee-sidebar');
        sidebarActive('#employee-sidebar');
    </script>
@endsection
