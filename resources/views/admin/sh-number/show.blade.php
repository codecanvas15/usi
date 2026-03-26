@extends('layouts.admin.layout.index')

@php
    $main = 'sh-number';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

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
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'detail ' . $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <x-table theadColor="danger">
                <x-slot name="table_head">
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                </x-slot>
                <x-slot name="table_body">
                    <tr>
                        <th>{{ Str::headline('kode') }}</th>
                        <td>{{ $model->kode }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('customer') }}</th>
                        <td>{{ $model->customer->nama }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('created_at') }}</th>
                        <td>{{ toDayDateTimeString($model->created_at) }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('last modified at') }}</th>
                        <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                    </tr>
                </x-slot>
            </x-table>
            <div class="row mt-30">
                @foreach ($model->sh_number_details as $item)
                    <div class="col-lg-6">
                        <x-table>
                            <x-slot name="table_head">
                                <tr>
                                    <th>{{ $item->type }}</th>
                                    <th></th>
                                </tr>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <th>{{ Str::headline('alamat') }}</th>
                                    <td>{{ $item->alamat }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('longitude') }}</th>
                                    <td>{{ $item->longitude }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('latitude') }}</th>
                                    <td>{{ $item->latitude }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('created_at') }}</th>
                                    <td>{{ toDayDateTimeString($model->created_at) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('last modified at') }}</th>
                                    <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </div>
                @endforeach
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="d-flex justify-content-end gap-2">
                <x-button color='secondary' class="w-auto" label='Back' link='{{ route("admin.$main.index") }}' />
                <x-button color="warning" label='edit' link='{{ route('admin.sh-number.update', $model) }}' dataToggle="modal" dataTarget="#update-modal-{{ $model->id }}" />
                <x-modal title="edit sh no." id="update-modal-{{ $model->id }}" headerColor="warning">
                    <x-slot name="modal_body">
                        <form action="{{ route('admin.sh-number.update', $model) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $model->id }}">

                            @foreach ($model->sh_number_details as $item)
                                <div>
                                    <h5 class="fw-bold">{{ Str::headline('supply point') }}</h5>
                                    <div class="form-group">
                                        <x-input type="text" label="alamat" name="alamat[]" value='{{ $item->alamat }}' required />
                                    </div>
                                    <div class="form-group">
                                        <x-input type="text" label="longitude" name="longitude[]" value='{{ $item->longitude }}' required />
                                    </div>
                                    <div class="form-group">
                                        <x-input type="text" label="latitude" name="latitude[]" value='{{ $item->latitude }}' required />
                                    </div>
                                    <input type="hidden" name="type[]" value="{{ $item->type }}" />
                                </div>
                            @endforeach

                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </form>
                    </x-slot>
                </x-modal>
                <x-button color='danger' class="w-auto" label='Delete' dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />

                <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
            </div>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')

@endsection
