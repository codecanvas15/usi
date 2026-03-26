@extends('guest.layout.app')

@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline("tambah bank $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('tambah bank ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('guest.employee.update.step7', ['employee_id' => $model->id]) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-card-data-table>
            <x-slot name="table_content">
                <div class="progress">
                    <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="87.5" aria-valuemin="0" aria-valuemax="100" style="width: 87.5%"></div>
                </div>
            </x-slot>
        </x-card-data-table>
        <x-card-data-table title="{{ 'bank ' . $title }}">
            <x-slot name="header_content">
                <x-button color="primary" icon="plus" fontawesome="" label="Tambah bank" id="add-bank"></x-button>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="mt-20" id="bank-content">
                    @foreach ($model->employee_banks as $item)
                        <div class="row pt-20 border-top border-primary" id="bank-item-{{ $loop->index }}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="bank_name[]" :value="$item->bank_name" label="nama bank" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="bank_behalf_of[]" :value="$item->behalf_of" label="atas nama" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="bank_account_number[]" :value="$item->account_number" label="nomor rekening" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" icon="trash" fontawesome="fas" label="Hapus" id="remove-bank-{{ $loop->index }}"></x-button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-slot>
        </x-card-data-table>

        <x-card-data-table>
            <x-slot name="table_content">
                <div class="d-flex justify-content-end gap-3">
                    <x-button type="reset" color="secondary" label="lewati" link="{{ route('admin.employee.show', $model->id) }}" />
                    <x-button type="submit" color="primary" label="Save data" />
                </div>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-employee-sidebar');
        sidebarActive('#employee-sidebar');
        $('body').addClass('sidebar-collapse');
    </script>

    <script>
        $(document).ready(function() {
            const handleBank = () => {
                let bankIndex = "{{ $model->employee_banks->count() }}";

                const addBank = (index) => {
                    let html = `
                        <div class="row pt-20 border-top border-primary" id="bank-item-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="bank_name[]" label="nama bank" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="bank_behalf_of[]" label="atas nama" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="bank_account_number[]" label="nomor rekening" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" icon="trash" fontawesome="fas" label="Hapus" id="remove-bank-${index}"></x-button>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#bank-content').append(html);

                    $(`#remove-bank-${index}`).click(function(e) {
                        e.preventDefault();
                        removeBank(index);
                    });
                };

                const removeBank = (index) => {
                    $(`#bank-item-${index}`).remove();
                };

                $('#add-bank').click(function(e) {
                    e.preventDefault();
                    addBank(bankIndex);
                    bankIndex++;
                });
            };

            const init = () => {
                handleBank();
            };

            init();
        });
    </script>

    @foreach ($model->employee_banks as $item)
        <script>
            $(document).ready(function() {
                $(`#remove-bank-{{ $loop->index }}`).click(function(e) {
                    e.preventDefault();
                    $(`#bank-item-{{ $loop->index }}`).remove();
                });
            });
        </script>
    @endforeach
@endsection
