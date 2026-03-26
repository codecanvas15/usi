@extends('layouts.admin.layout.index')

@php
    $main = 'sh-number';
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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Create ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'create ' . $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            @include("admin.$main.__fields")
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')

@endsection
