@extends('layouts.admin.layout.index')


@php
    $main = 'master-print-authorization';
    $title = 'master print authorization';
@endphp

@section('title', Str::headline($title) . ' - ')

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
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ $title }}">
        <x-slot name="header_content">
            <div class="d-flex justify-content-end mb-3">
                <div>
                    <button class="btn btn-warning" data-type="edit" onclick="handleClickEdit(event)">
                        <i class="fa fa-edit"></i>
                    </button>
                </div>
                <div id="wrapper_submit" style="display: none">
                    <button class="btn btn-primary" onclick="handleSubmit(event)">
                        Submit
                    </button>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <x-table id="table-master-print">
                <x-slot name="table_head">
                    <th>{{ Str::upper('#') }}</th>
                    <th>{{ Str::upper('Group') }}</th>
                    <th>{{ Str::upper('Label') }}</th>
                    <th>{{ Str::upper('Authorize') }}</th>
                </x-slot>
                <x-slot name="table_body">

                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection


@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#master-print');

        $(document).ready(function() {
            window.table_master_print = $('table#table-master-print').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route("admin.$main.data") }}',
                    data: {
                        type: function() {
                            return window.type_ ?? 'not_checkbox'
                        }
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'group',
                        name: 'group'
                    },
                    {
                        data: 'label',
                        name: 'label'
                    },
                    {
                        data: 'can_print',
                        name: 'can_print'
                    }
                ]
            });

            $('#table-master-print').on('click', '.btn-edit', function() {
                const data = window.table_master_print.row($(this).parents('tr')).data();
                console.log(data);
            });
        });


        function handleClickEdit(event) {
            const self = $(event.currentTarget);
            const currentType = self.data('type');

            if (currentType == 'edit') {
                self.data('type', 'cancel');
                self.html('<i class="fa fa-times"></i>');
                $('#wrapper_submit').show();

                window.type_ = 'checkbox';
            } else if (currentType === 'cancel') {
                self.data('type', 'edit');
                self.html('<i class="fa fa-edit"></i>');
                $('#wrapper_submit').hide();

                window.type_ = 'not_checkbox';
            }

            window.table_master_print.ajax.reload();
        }

        function handleSubmit(event) {
            const isPrint = Array.from($('.print_check').map((i, el) => {
                return {
                    id: $(el).val(),
                    can_print: $(el).is(':checked')
                }
            }).get());

            $.ajax({
                url: `{{ route('admin.master-print-authorization.store') }}`,
                method: 'POST',
                data: {
                    can_print: isPrint,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    window.type_ = 'not_checkbox';
                    window.table_master_print.ajax.reload();

                    $('#wrapper_submit').hide();
                    $('.btn-warning').data('type', 'edit');
                    $('.btn-warning').html('<i class="fa fa-edit"></i>');
                }
            })
        }
    </script>
@endsection
