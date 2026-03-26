@php
    $main = 'admin.download-report';
    $title = 'download';
@endphp

@extends('layouts.admin.layout.index')

@section('title', Str::headline($title))

@section('css')
    <style>
        #check_all {
            opacity: 1;
            position: static;
        }
    </style>

@endsection

@section('breadcrumbs')
    <div class="card p-4">
        <div class="card-inner">
            <nav>
                <ul class="breadcrumb breadcrumb-arrow">
                    <li class="breadcrumb-item"><a href="#">{{ Str::headline($title) }}</a></li>
                </ul>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="card p-4">
        <div class="card-inner">
            <div class="row align-items-center justify-content-between">
                <div class="col">
                    <h5 class="card-title">{{ Str::headline($title) }}</h5>
                </div>
                <div class="col-auto">
                    <div class="text-end" id="bulk-action">
                        <button type="button" id="bulk-delete" class="btn btn-danger"><em class="icon ni ni-trash"></em><span>Hapus</span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card p-4">
        <div class="card-inner">
            <table class="nowrap table">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="check_all" name="check_all" value="1" class="download-check-all" />
                        </th>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Laporan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        var ids = [];
        var downloadsIds = [];

        const downloadCheck = (e) => {
            if (e.is(':checked')) {
                downloadsIds.push(e.val());
            } else {
                downloadsIds = downloadsIds.filter(elem => elem != e.val());
            }
            check_bulk_action();
        };

        const check_bulk_action = () => {
            $('#check_all').prop('checked', downloadsIds.length === ids.length);
            $('#bulk-action').toggle(downloadsIds.length > 0);
        };

        $(document).ready(() => {
            const table = $('table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: {
                    url: '{{ route("$main.index") }}',
                    data: {
                        _token: token,
                        selected_ids: () => downloadsIds
                    },
                    complete: (response) => {
                        ids = response.responseJSON.ids;
                    }
                },
                columns: [{
                        data: 'checkbox',
                        name: 'downloads.id',
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'id'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'type'
                    },
                    {
                        data: 'status'
                    }
                ]
            });

            $('#check_all').on('click', function() {
                downloadsIds = $(this).is(':checked') ? [...ids] : [];
                check_bulk_action();
                table.ajax.reload(null, false);
            });

            $('#bulk-delete').click(() => {
                if (confirm('Apakah anda yakin menghapus data ini?')) {
                    $.post("{{ route('admin.download-report.bulk-delete') }}", {
                            _token: token,
                            ids: downloadsIds
                        })
                        .done(response => {
                            alert(response.message);
                            downloadsIds = [];
                            $('#bulk-action').hide(200);
                            table.ajax.reload();
                        })
                        .fail(response => {
                            alert(`Server Error: ${response.responseJSON.message ?? "error"}`);
                        });
                }
            });
        });
    </script>
@endsection
