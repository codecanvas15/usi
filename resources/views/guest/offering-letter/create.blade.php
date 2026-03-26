@extends('guest.layout.app')
@php
    $main = 'offering-letter';
    $title = 'penawaran pekerjaan';
@endphp

@section('title', Str::headline("$title") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('content')

    <x-card-data-table :title='"$title"'>
        <x-slot name="header_content">
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <div class="row pb-10">
                <div class="col-md-12">
                    <div class="alert alert-danger">Harap memberikan tanggapan paling lambat tanggal {{ localDate($offering_letter->due_date) }}</div>
                </div>
                <input type="hidden" name="offering_letter_id" value="{{ $offering_letter->id }}">
                <div class="col-md-3">
                    <span class="text-info">{{ Str::headline('kode') }}</span>
                    <h3>{{ $offering_letter->reference }}</h3>
                </div>
                <div class="col-md-12"></div>
                <div class="col-md-2">
                    <span class="text-info">{{ Str::headline('calon karyawan') }}</span>
                    <h5>{{ $offering_letter->laborApplication->name }}</h5>
                </div>
                <div class="col-md-2">
                    <span class="text-info">{{ Str::headline('posisi dilamar') }}</span>
                    <h5 class="text-capitalize">{{ $offering_letter->laborApplication->laborDemandDetail->position->nama }}</h5>
                </div>
                <div class="col-md-12"></div>
                <div class="col-md-2">
                    <span class="text-info">{{ Str::headline('penempatan kerja') }}</span>
                    <h5>{{ $offering_letter->work_location }}</h5>
                </div>
                <div class="col-md-2">
                    <span class="text-info">{{ Str::headline('tanggal mulai kerja') }}</span>
                    <h5>{{ localDate($offering_letter->start_work_date) }}</h5>
                </div>
                <div class="col-md-3">
                    <x-button target="_blank" link="{{ $document_link }}" color="primary" icon="file" label="lihat dokumen penawaran" />
                </div>

            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="justify-content-end gap-2">
                <x-button color="danger" id="reject-button" label="tolak penawaran" icon="times" fontawesome dataToggle="modal" dataTarget="#note-modal" class="my-1" />
                <x-button color="info" id="approve-button" label="terima penawaran" icon="save" fontawesome dataToggle="modal" dataTarget="#note-modal" class="my-1" />
            </div>
        </x-slot>

    </x-card-data-table>
    <x-modal title="" id="note-modal" headerColor="success">
        <x-slot name="modal_body">
            <form action="{{ route('guest.offering-letter.update', ['offering_letter' => $offering_letter->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="" class="form-label">Keterangan Menerima/Menolak</label>
                            <textarea name="reason" id="" cols="30" rows="10" class="form-control" required></textarea>
                            <input type="hidden" name="applicant_status">
                        </div>
                    </div>
                </div>
                <div class="mt-10 border-top pt-10">
                    <x-button type="button" color="secondary" dataDismiss="modal" label="tutup" icon="times" fontawesome />
                    <x-button type="submit" color="primary" label="submit" icon="save" fontawesome />
                </div>
            </form>
        </x-slot>
    </x-modal>
@endsection

@section('js')
    <script>
        $('#approve-button').click(function() {
            $('input[name="applicant_status"]').val('approve');
        });
        $('#reject-button').click(function() {
            $('input[name="applicant_status"]').val('reject');
        });
    </script>
@endsection
