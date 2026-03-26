@extends('layouts.admin.layout.index')

@php
    $main = 'master-loyalty';
@endphp

@section('title', Str::headline($main) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
    <style>
        #DataTables_Table_0_wrapper .row:nth-child(1) {
            justify-content: start;
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
    <div class="content-body">
        <section id="table-panel">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <a href="{{ route('admin.' . $main . '.create') }}" class="btn btn-primary">Tambah Master Loyalty</a>
                            </div>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard">
                                <div id="result"></div>
                                <table class="table table-striped datatable-config" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th></th>
                                            <th>Cabang</th>
                                            <th>Deskripsi</th>
                                            <th>Bonus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        var table;

        function setTable() {

            table = $('.datatable-config').DataTable({
                "rowReorder": {
                    dataSrc: 'id'
                },
                "language": {
                    "emptyTable": "Tidak ada data"
                },
                "lengthMenu": [
                    [25, 50, 75, 100, -1],
                    [25, 50, 75, 100, 'All'],
                ],
                "destroy": true,
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "ordering": false,
                "columnDefs": [{
                        "targets": [0],
                        "visible": false,
                        "searchable": false
                    },
                    {
                        "targets": [2],
                        "sortable": false,
                        "searchable": false
                    }
                ],
                "ajax": {
                    "url": `${base_url}/master-loyalty/data`,
                    "dataType": "json",
                    "type": "post",
                    "data": {
                        _token: token,
                    },
                    error: function(err) {
                        console.log(err);
                    }
                },
                "columns": [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        "data": "new_tab"
                    },
                    {
                        "data": "branch"
                    },
                    {
                        data: null,
                        render: function(data) {
                            var html = [];
                            console.log(data)
                            html.push("Masa Kerja " + data.nilai_bawah + " - " + data.nilai_atas + " Tahun");

                            return html.join('<br>');
                        },
                        name: 'nama_pegawai'
                    },
                    {
                        "data": "bonus"
                    },
                ]
            });

            table.on('row-reorder', function(e, diff, edit) {
                for (var i = 0, ien = diff.length; i < ien; i++) {
                    $(diff[i].node).addClass("reordered");
                }
            });
        }

        $('.datatable-config tbody').on('click', 'tr', function(event) {
            var data = table.row(this).data();
            var url = `${base_url}/master-bonus/${data['id']}`;

            if ($(event.target).is('td:first-child') || $(event.target).is('a') || $(event.target).is('i')) {
                return null;
            } else {
                location.href = url;
            }
        });


        var count = 0;
        var table_detail = $('#table-detail');

        function addDetail() {
            var detail = `<tr id="row${count}" class="border-0">
                                <td class="border-0">
                                    <input type="text" class="form-control" name="bottom[]" id="bottom${count}" value="0">
                                </td>
                                <td class="border-0 text-right">
                                    <input type="text" class="form-control" name="top[]" id="top${count}" value="0">
                                </td>
                                <td class="border-0 text-right">
                                    <input type="text" class="form-control free-decimal" name="percentage[]" id="percentage${count}" value="0">
                                </td>
                                <td class="border-0">
                                    <button class="btn btn-danger" onclick="deleteRow(${count})">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </td>
                            </tr>`;

            table_detail.append(detail);


            $('#bottom' + count).mask("000.000.000.000", {
                reverse: true,
            });
            $('#top' + count).mask("000.000.000.000", {
                reverse: true,
            });

            count++;
        }

        function deleteRow(id) {
            $(`#row${id}`).remove();
        }

        function deleteSavedRow(id) {
            Swal.fire({
                title: "Apakah anda yakin?",
                text: "Anda tidak akan dapat mengembalikan ini!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#303179",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: base_url + `/master-loyalty/` + id,
                        method: "DELETE",
                        data: {
                            _token: token,
                        },
                        success: function(data) {
                            $(`#row-saved${id}`).remove();
                        }
                    })
                }
            });
        }


        $('.menu-master-loyalty').addClass('active');

        setTable();
    </script>
@endsection
