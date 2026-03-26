@extends('layouts.admin.layout.index')

@php
    $main = 'pengeluaran dana';
    $folder = 'outgoing-payment';
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
                        {{ Str::headline($main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @php
        $permissions = ['outgoing-payment', 'cash-advance-payment', 'account-payable'];
        $tabs = ['general', 'deposite', 'account-payable'];
        $titles = ['General', 'Pembayaran Deposit', 'Pembayaran Supplier'];
        $active_tab = app()->request->get('tab');

        if (!$active_tab) {
            foreach ($permissions as $key => $permission) {
                if (
                    auth()
                        ->user()
                        ->can('view ' . $permission)
                ) {
                    $active_tab = $tabs[$key];
                    break;
                }
            }
        }
    @endphp
    <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
        @foreach ($tabs as $key => $tab)
            @can('view ' . $permissions[$key])
                <li class="nav-item">
                    <a class="nav-link rounded {{ $active_tab == $tab ? 'active' : '' }}" data-bs-toggle="tab" href="#{{ $tab }}" id="{{ $tab }}-btn" role="tab">
                        <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                        <span class="hidden-xs-down">{{ $titles[$key] }}</span>
                    </a>
                </li>
            @endcan
        @endforeach
    </ul>

    <div class="tab-content mt-30">
        @foreach ($tabs as $key => $tab)
            @can('view ' . $permissions[$key])
                @include('admin.outgoing-payment.__' . $tab . '_tab', [
                    'is_active' => $active_tab == $tab,
                    'title' => $titles[$key],
                    'permission' => $permissions[$key],
                    'tab' => $tab,
                ])
            @endcan
        @endforeach
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#outgoing-payment');
    </script>
    @foreach ($tabs as $key => $tab)
        @can('view ' . $permissions[$key])
            @include('admin.outgoing-payment.__' . $tab . '_script')
        @endcan
    @endforeach
@endsection
