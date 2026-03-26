@extends('layouts.admin.layout.index')

@php
    $main = 'customer';
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
        <div class="box">
            <div class="box-body border-0">
                <ul class="nav nav-tabs customtab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link rounded active" data-bs-toggle="tab" href="#general-tab" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">General</span>
                        </a>
                    </li>
                    @can("view $main-coa")
                        <li class="nav-item">
                            <a class="nav-link rounded" data-bs-toggle="tab" href="#other-tab" id="tab-pairing-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Other</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
        </div>

        <div class="tab-content">
            <div class="tab-pane active" id="general-tab" role="tabpanel">
                <x-card-data-table title="{{ 'detail ' . $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <div class="row">
                            <div class="col-12 rounded rounded-lg mb-3 text-center p-2 bg-danger">
                                Customer information
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Nama') }}</label>
                                    <p>
                                        {{ $model->nama }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Alamat') }}</label>
                                    <p>
                                        {{ $model->alamat }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('email') }}</label>
                                    <p>
                                        {{ $model->email }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('bussiness_phone') }}</label>
                                    <p>
                                        {{ $model->bussiness_phone }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('mobile_phone') }}</label>
                                    <p>
                                        {{ $model->mobile_phone }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('whatsapp_number') }}</label>
                                    <p>
                                        {{ $model->whatsapp_number }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('fax') }}</label>
                                    <p>
                                        {{ $model->fax }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('website') }}</label>
                                    <p>
                                        {{ $model->website }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('lost_tolerance') }}</label>
                                    <p>
                                        {{ $model->lost_tolerance_type == 'percent' ? number_format($model->lost_tolerance * 100, 2) . '%' : "$model->lost_tolerance" }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('lost_tolerance_type') }}</label>
                                    <p>
                                        {{ $model->lost_tolerance_type }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('npwp') }}</label>
                                    <p>
                                        {{ $model->npwp }}
                                    </p>
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
                                    <label for="">{{ Str::headline('updated_at') }}</label>
                                    <p>
                                        {{ toDayDateTimeString($model->updated_at) }}
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-1">
                                <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />

                                <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                            </div>
                        </div>
                    </x-slot>

                </x-card-data-table>
            </div>

            @can("view $main-coa")
                <div class="tab-pane" id="other-tab" role="tabpanel">
                    <x-card-data-table title="customer coa">
                        @slot('table_content')
                            <x-table theadColor='danger' id="custmer-coas">
                                <x-slot name="table_head">
                                    <th>Customer Coa</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    @foreach ($model->customer_coas as $item)
                                        <tr>
                                            <td class="{{ $item->coa->deleted_at != null ? 'text-danger' : '' }}"><b>{{ Str::headline($item->tipe) }}</b> <br> {{ $item->coa->account_code }} - {{ $item->coa->name }}</td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-table>
                            <h5 class="text-primari">Note: Jika coa merah maka data coa sudah tidak ada di master </h5>
                            <div class="d-flex justify-content-end">
                                <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" id="edit-customer-coa" />
                            </div>

                            <div style="display: none;" id="form-edit-customer-coa">
                                <form action="{{ route("admin.$main.update-coa", $model) }}" method="post">
                                    @csrf
                                    <div class="row">
                                        @if ($model->customer_coas->count() > 0)
                                            @foreach ($model->customer_coas as $item)
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <x-select name="{{ Str::snake($item->tipe) }}" id="{{ Str::snake($item->tipe) }}" label="{{ Str::snake($item->tipe) }}" required>
                                                            <option value="{{ $item->coa_id }}">{{ $item->coa->account_code }} - {{ $item->coa->name }}</option>
                                                        </x-select>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            @foreach (customer_coa_types() as $item)
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <x-select name="{{ Str::snake($item) }}" id="{{ Str::snake($item) }}" label="{{ Str::snake($item) }}" required>
                                                            <option value=""></option>
                                                        </x-select>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                </form>
                            </div>
                        @endslot
                    </x-card-data-table>
                </div>
            @endcan
        </div>

    @endcan

    <x-card-data-table title="customer bank">
        <x-slot name="header_content">
            <x-table theadColor='danger'>
                <x-slot name="table_head">
                    <th>#</th>
                    <th>{{ Str::headline('Bank') }}</th>
                    <th>{{ Str::headline('Nomor Rekening') }}</th>
                    <th>{{ Str::headline('atas nama') }}</th>
                </x-slot>
                <x-slot name="table_body">
                    @foreach ($model->customer_banks as $item)
                        <tr>
                            <th>{{ $loop->index + 1 }}</th>
                            <td>{{ $item?->bank_internal?->nama_bank }}</td>
                            <td>{{ $item?->bank_internal?->no_rekening }}</td>
                            <td>{{ $item?->bank_internal?->on_behalf_of }}</td>
                        </tr>
                    @endforeach
                </x-slot>
            </x-table>
        </x-slot>
    </x-card-data-table>

    <x-card-data-table title="customer SH No.">
        <x-slot name="header_content">
            <x-button color="info" icon="plus" label="Create" id="toggle-sh-number" />

            <form action="{{ route('admin.sh-number.store') }}" method="post" enctype="multipart/form-data" id="sh-number-create" style="display: none;" class="mt-30">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $model->id }}">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" label="sh_number" name="sh_number" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" label="Nilai Sangu" name="allowance" class="allowance-input-create" />
                        </div>
                    </div>
                </div>

                {{-- supply_point_val --}}
                <h5 class="fw-bold">{{ Str::headline('supply point') }}</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" label="alamat" name="alamat[]" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" label="longitude" name="longitude[]" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" label="latitude" name="latitude[]" />
                        </div>
                    </div>
                    <input type="hidden" name="type[]" value="Supply Point">
                </div>

                {{-- drop_point_val --}}
                <h5 class="fw-bold">{{ Str::headline('drop point / ship to') }}</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" label="alamat" name="alamat[]" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" label="longitude" name="longitude[]" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" label="latitude" name="latitude[]" />
                        </div>
                    </div>
                    <input type="hidden" name="type[]" value="Drop Point">
                </div>

                <x-button type="button" color="secondary" id="cancel-create-sh-number" label="cancel" />
                <x-button type="submit" color="primary" label="Save data" />
            </form>
        </x-slot>
        <x-slot name="table_content">
            <x-table class='mt-4'>
                <x-slot name="table_head">
                    <th>#</th>
                    <th>{{ Str::headline('kode') }}</th>
                    <th>{{ Str::headline('supply point') }}</th>
                    <th>{{ Str::headline('drop point') }}</th>
                    <th>{{ Str::headline('Nilai Sangu') }}</th>
                    <th></th>
                </x-slot>
                <x-slot name="table_body">
                    @foreach ($model->sh_numbers as $sh_number)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $sh_number->kode }}</td>
                            @foreach ($sh_number->sh_number_details as $item)
                                @if ($item->type == 'Drop Point')
                                    <td>{{ $item->alamat }}</td>
                                @else
                                    <td>{{ $item->alamat }}</td>
                                @endif
                            @endforeach
                            <td>{{ number_format($sh_number->allowance ?? 0, 2, ',', '.') }}</td>
                            <td>
                                <x-button color="warning" fontawesome icon="edit" size="sm" link="{{ route('admin.sh-number.update', $sh_number) }}" dataToggle="modal" dataTarget="#update-modal-{{ $sh_number->id }}" />
                                <x-modal title="sh number" id="update-modal-{{ $sh_number->id }}" headerColor="warning">
                                    <x-slot name="modal_body">
                                        <form action="{{ route('admin.sh-number.store') }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="customer_id" value="{{ $model->id }}">

                                            <div class="form-group">
                                                <x-input type="text" label="sh_number" name="sh_number" value='{{ $sh_number->sh_number }}' required />
                                            </div>

                                            <div class="form-group">
                                                <x-input type="text" label="Nilai Sangu" name="allowance" value="{{ number_format($sh_number->allowance ?? 0, 2, ',', '.') }}" class="allowance-input-create" />
                                            </div>

                                            @foreach ($sh_number->sh_number_details as $item)
                                                <div>
                                                    <h5 class="fw-bold">{{ Str::headline($item->type) }}</h5>
                                                    <div class="form-group">
                                                        <x-input type="text" label="alamat" name="alamat[]" value='{{ $item->alamat }}' required />
                                                    </div>
                                                    <div class="form-group">
                                                        <x-input type="text" label="longitude" name="longitude[]" value='{{ $item->longitude }}' />
                                                    </div>
                                                    <div class="form-group">
                                                        <x-input type="text" label="latitude" name="latitude[]" value='{{ $item->latitude }}' />
                                                    </div>
                                                    <input type="hidden" name="type[]" value="{{ $item->type }}" />
                                                </div>
                                            @endforeach

                                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                            <x-button type="submit" color="primary" label="Save data" />
                                        </form>
                                    </x-slot>
                                </x-modal>

                                <x-button color="danger" fontawesome icon="trash" size="sm" dataToggle="modal" dataTarget="#delete-modal-sh-number-{{ $sh_number->id }}" />
                                <x-modal-delete id="delete-modal-sh-number-{{ $sh_number->id }}" url="{{ 'admin.sh-number.destroy' }}" dataId="{{ $sh_number->id }}" />
                            </td>
                        </tr>
                    @endforeach
                </x-slot>
            </x-table>
        </x-slot>
    </x-card-data-table>

    <div id="map"></div>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>

    <script>
        let map;
        let lat = document.getElementById('lat');
        let lng = document.getElementById('lng');

        function initMap() {
            // =========== core ===================
            const myLatlng = {
                lat: -8.739184,
                lng: 115.171127
            };
            map = new google.maps.Map(document.getElementById("map"), {
                center: myLatlng,
                zoom: 8,
                mapTypeId: "roadmap", //[roadmap, satellite, hybrid, terrain]
                gestureHandling: "auto", //[cooperative, auto]
                zoomControl: true,
                mapTypeControl: true,
                scaleControl: true,
                streetViewControl: true,
                rotateControl: true,
                fullscreenControl: true
            });
            var marker = new google.maps.Marker({
                position: myLatlng,
                map,
                title: "Hello World!",
            });

            map.addListener("click", (mapsMouseEvent) => {
                marker.setPosition(mapsMouseEvent.latLng)
                lat.value = mapsMouseEvent.latLng.lat();
                lng.value = mapsMouseEvent.latLng.lng();
            });
        }


        $('#edit-customer-coa').click(function(e) {
            e.preventDefault();
            $(this).hide();
            $('#custmer-coas').hide();
            $('#form-edit-customer-coa').show();
            @foreach (customer_coa_types() as $item)
                initCoaSelect('#{{ Str::snake($item) }}');
            @endforeach
        });

        $('#cancel-create-sh-number').click(function(e) {
            e.preventDefault();
            $('#sh-number-create').fadeOut();
            $('#toggle-sh-number').fadeIn();
        });

        $('#toggle-sh-number').click(function(e) {
            e.preventDefault();

            $('#sh-number-create').fadeIn();
            $('#toggle-sh-number').fadeOut();
        });
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#customer-sidebar')
    </script>
    <script>
            // Start of Selection
            $('.allowance-input-create').on('input', function () {
                let value = $(this).val();
                // Allow only numbers, dots, and commas
                value = value.replace(/[^0-9.,]/g, '');

                // Ensure only one comma for decimal
                const parts = value.split(',');
                if (parts.length > 2) {
                    value = parts[0] + ',' + parts.slice(1).join('');
                }

                $(this).val(value);
            });

            $('.allowance-input-create').on('blur', function () {
                let value = $(this).val().replace(/\./g, '').replace(',', '.'); // Convert to a number
                let floatValue = parseFloat(value);
                if (isNaN(floatValue)) {
                    floatValue = 0.00;
                } else {
                    floatValue = floatValue.toFixed(2);
                }
                $(this).val(new Intl.NumberFormat('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(floatValue));
            });

    </script>
@endsection
