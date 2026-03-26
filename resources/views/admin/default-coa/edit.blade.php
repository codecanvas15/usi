@extends('layouts.admin.layout.index')

@php
    $main = 'default-coa';
@endphp

@section('title', Str::headline($main) . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("edit $main") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('edit default-coa')
        <form action="{{ route('admin.default-coa.update') }}" method="post">
            <x-card-data-table title="{{ 'edit ' . $main }}">

                <x-slot name="header_content">
                    @include('components.validate-error')
                </x-slot>

                <x-slot name="table_content">
                    @csrf
                    @method('PUT')

                    @foreach ($data as $type => $defaul_coa_items)
                        <h4 class="fw-bold my-10">{{ Str::headline($type) }}</h4>
                        <div class="row">
                            @foreach ($defaul_coa_items as $item)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="hidden" name="type[]" value="{{ Str::headline($type) }}">
                                        <input type="hidden" name="name[]" value="{{ Str::headline($item->name) }}">
                                        <x-select name="coa_id[]" label="{{ $item->name }}" id="coa-select-{{ $type }}-{{ Str::snake($item->name, '-') }}" required>
                                            @if ($item->coa)
                                                <option value="{{ $item->coa_id }}" selected>{{ $item->coa?->account_code }} - {{ $item->coa?->name }}</option>
                                            @endif
                                        </x-select>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </x-slot>

                <x-slot name="footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    @can('edit default-coa')
        <script src="{{ asset('js/admin/select/coa.js') }}"></script>

        <script>
            $(document).ready(function() {
                @foreach ($data as $type => $defaul_coa_items)
                    @foreach ($defaul_coa_items as $item)
                        initCoaSelect('#coa-select-{{ $type }}-{{ Str::snake($item->name, '-') }}')
                    @endforeach
                @endforeach

                sidebarMenuOpen('#master-sidebar');
                sidebarMenuOpen('#master-coa-sidebar');
                sidebarActive('#default-coa-sidebar');
            });
        </script>
    @endcan
@endsection
