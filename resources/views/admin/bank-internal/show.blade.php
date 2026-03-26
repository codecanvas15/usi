@extends('layouts.admin.layout.index')

@php
    $main = 'bank-internal';
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
    @can("view $main")
        <x-card-data-table title="{{ 'detail ' . $main }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('kode document') }}</label>
                            <p>
                                {{ $model->code }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('nama_bank') }}</label>
                            <p>
                                {{ $model->nama_bank }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('cabang') }}</label>
                            <p>
                                {{ $model->branch_name }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('nomor_rekening') }}</label>
                            <p>
                                {{ $model->no_rekening }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('atas_nama') }}</label>
                            <p>
                                {{ $model->on_behalf_of }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('coa') }}</label>
                            <p>
                                {{ $model->coa?->account_code }} - {{ $model->coa?->name }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('logo') }}</label>
                            @if ($model->logo)
                                <br>
                                <img src="{{ asset('storage/' . $model->logo) }}" alt="" srcset="" class="w-100">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('created_at') }}</label>
                            <p>
                                {{ toDayDateTimeString($model->created_at) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('last modified') }}</label>
                            <p>
                                {{ toDayDateTimeString($model->updated_at) }}
                            </p>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-1">
                    <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                    @can("edit $main")
                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                    @endcan

                    @can("delete $main")
                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                    @endcan
                </div>
            </x-slot>

        </x-card-data-table>

        <x-card-data-table title="detail bank internal">
            <x-slot name="table_content">
                <x-table theadColor='danger'>
                    <x-slot name="table_head">
                        <th>#</th>
                        <th>{{ Str::headline('nama') }}</th>
                        <th>{{ Str::headline('deskripsi') }}</th>
                        <th>{{ Str::headline('batas credit') }}</th>
                        <th>{{ Str::headline('tanggal mulai') }}</th>
                        <th>{{ Str::headline('tanggal berakhir') }}</th>
                    </x-slot>
                    <x-slot name="table_body">
                        @foreach ($model->bank_internal_details as $item)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->credit_limit }}</td>
                                <td>{{ $item->start_date }}</td>
                                <td>{{ $item->end_date }}</td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-table>
            </x-slot>
        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#bank-internal-sidebar')
    </script>
@endsection
