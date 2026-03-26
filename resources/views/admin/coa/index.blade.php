@extends('layouts.admin.layout.index')

@php
    $main = 'coa';
@endphp

@section('title', Str::headline($main) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />

    <style>
        /* original idea http://www.bootply.com/phf8mnMtpe */
        .tree-coa {
            min-height: 20px;
            margin-bottom: 20px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05)
        }

        .tree-coa li {
            list-style-type: none;
            margin: 0;
            padding: 10px 5px 0 5px;
            position: relative
        }

        .tree-coa li::before,
        .tree-coa li::after {
            content: '';
            left: -20px;
            position: absolute;
            right: auto
        }

        .tree-coa li::before {
            border-left: 1px solid #999;
            bottom: 50px;
            height: 100%;
            top: 0;
            width: 1px
        }

        .tree-coa li::after {
            border-top: 1px solid #999;
            height: 20px;
            top: 30px;
            width: 25px
        }

        .tree-coa li span {
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            border: 1px solid #999;
            border-radius: 5px;
            display: block;
            padding: 3px 8px;
            text-decoration: none
        }

        .tree-coa li.parent_li>span {
            cursor: pointer
        }

        .tree-coa>ul>li::before,
        .tree-coa>ul>li::after {
            border: 0
        }

        .tree-coa li:last-child::before {
            height: 30px
        }

        .tree-coa li.parent_li>span:hover,
        .tree-coa li.parent_li>span:hover+ul li span {
            background: #eee;
            border: 1px solid #94a0b4;
            color: #000
        }
    </style>
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
    @can("view $main")
        <div class="row">
            <div class="col-md-8">
                <x-card-data-table title="{{ $main }}">
                    <x-slot name="header_content">
                        @can("create $main")
                            <div class="row justify-content-between mb-4">
                                <div class="col">
                                    <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />

                                    <x-button link='{{ route("admin.$main.export") }}' color="info" label="export coa" target="_blank" />
                                    <x-button link='{{ route("admin.$main.import") }}' color="info" icon="download" label="import" dataToggle="modal" dataTarget="#import-modal" />

                                    <x-modal title="import" id="import-modal" headerColor="info">
                                        <x-slot name="modal_body">
                                            <x-button link='{{ route("admin.$main.import-format") }}' color="info" icon="download" label="download format" />

                                            <div class="mt-30">
                                                <form action='{{ route("admin.$main.import") }}' method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-group">
                                                        <x-input type="file" label="file" name="file" required />
                                                    </div>
                                                    <x-button color="info" icon="download" label="import" />
                                                </form>
                                            </div>

                                        </x-slot>
                                    </x-modal>
                                    <x-button link='{{ route("admin.$main.coa-beginning.index") }}' color="info" icon="download" label="saldo awal" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <x-select id="bank_internal_status" label="Status Bank Internal">
                                        <option value="">Semua</option>
                                        <option value="done">Sudah ada</option>
                                        <option value="not">Belum ada</option>
                                    </x-select>
                                </div>
                            </div>
                        @endcan
                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <x-table>
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('account code') }}</th>
                                <th>{{ Str::headline('nama') }}</th>
                                <th>{{ Str::headline('tipe akun') }}</th>
                                <th>{{ Str::headline('kategori akun') }}</th>
                                <th>{{ Str::headline('bank internal') }}</th>
                                <th>{{ Str::headline('Created At') }}</th>
                                <th>{{ Str::headline('Last Modified At') }}</th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            </div>
            <div class="col-md-4">
                <x-card-data-table title='{{ "$main Tree" }}'>
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">

                        <div style="max-height: 900px; overflow: scroll" id="coa-tree">
                            {!! $tree !!}
                        </div>

                    </x-slot>
                </x-card-data-table>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    @can("view $main")
        <script>
            $(document).ready(() => {
                $('.tree-coa li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
                $('.tree-coa li.parent_li > span').parent('li.parent_li').find(' > ul > li').hide('fast');
                $('.tree-coa li.parent_li > span').on('click', function(e) {
                    var children = $(this).parent('li.parent_li').find(' > ul > li');

                    if (children.is(":visible")) {
                        children.hide('fast');
                        $(this).attr('title', 'Expand this branch').find(' > i').addClass('fa-plus-square').removeClass('fa-minus-square');
                    } else {
                        children.show('fast');
                        $(this).attr('title', 'Collapse this branch').find(' > i').addClass('fa-minus-square').removeClass('fa-plus-square');
                    }
                    e.stopPropagation();
                });

            });

            const table = $('table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.coa.index') }}",
                    type: "get",
                    data: {
                        _token: token,
                        filter_bank_internal: function() {
                            return $('#bank_internal_status').val();
                        }
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'account_code',
                        name: 'account_code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'account_type',
                        name: 'account_type'
                    },
                    {
                        data: 'account_category',
                        name: 'account_category'
                    },
                    {
                        data: 'bank_internal',
                        name: 'bank_internals.id'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#bank_internal_status').on('change', function() {
                table.ajax.reload();
            });
        </script>
        <script>
            sidebarMenuOpen('#master-sidebar');
            sidebarMenuOpen('#master-coa-sidebar');
            sidebarActive('#coa-sidebar');
        </script>
    @endcan
@endsection
