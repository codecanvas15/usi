@extends('guest.layout.app')
@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline("$title") . ' - ')

@section('content')

    <form action="{{ route('guest.employee.find') }}" method="get" enctype="multipart/form-data">
        <x-card-data-table :title='"Temukan $title"'>
            <x-slot name="table_content">

                @include('components.validate-error')
                <div class="row border-primary border-bottom pb-10">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="nik" required="required" />
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-group">
                                        <x-button type="submit" color="info" icon="search" label="import" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection
