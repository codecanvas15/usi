<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="nama" name="name" label="name" value="{{ $model->name ?? '' }}" required autofucus />
            </div>
        </div>
        <div class="col-md-12 my-20">
            <div class="row border-bottom">
                @foreach (get_permissions_description() as $key => $item)
                    <div class="col-md-4 mt-10">
                        <h4 class="fw-bold mb-0">{{ Str::headline($key) }}.</h4>
                        <p class="text-secondary">{{ Str::title($item) }}</p>
                    </div>
                @endforeach
            </div>

            <div class="row border-bottom my-20">
                @foreach (get_permission_notes() as $key => $item)
                    <div class="col-md-4 mt-10">
                        <h4 class="fw-bold text-primary mb-0">{{ $key }}</h4>
                        <p class="text-secondary">{{ $item }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @php
            $master = master_permissions;
            $penjualan = penjualan_permissions;
            $pembelian = pembelian_permissions;
            $gudang = gudang_permissions;
            $hrd = hrd_permissions;
            $akuntansi = akuntansi_permissions;
            $report = report_permissions;
        @endphp
        <div id="accordion">
            <div class="card">
                <div class="card-header" id="headingMaster">
                    <button class="text-dark accordion-button collapsed btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMaster" aria-expanded="false" aria-controls="collapseMaster">
                        <b>Master</b>
                    </button>
                </div>
                @foreach ($permissions->sortKeys() as $permission_key => $permission)
                    @if (in_array($permission_key, $master))
                        <div id="collapseMaster" class="px-3 collapse" aria-labelledby="headingMaster" data-parent="#accordion">
                            <div class="card-body py-0" id="childMaster">
                                <div class="card ">
                                    <div class="card-header bg-light">
                                        <button type="button" class="accordion-button text-dark collapsed btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $permission_key }}" aria-expanded="false" aria-controls="collapseMaster">
                                            {{ ucwords(str_replace('-', ' ', $permission_key)) }} &nbsp;<small> ({{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }})</small>
                                        </button>
                                    </div>
                                    <div class="card-body bg-light collapse" data-parent="#childMaster" id="collapse-{{ $permission_key }}">
                                        <input id="{{ $permission_key }}-check" type="checkbox" class="{{ $permission_key }} chk-col-info" name="permission_name">
                                        <label for="{{ $permission_key }}-check" class="form-label text-info">{{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }} <span class="text-primary">*</span></label>
                                        <div class="row mt-10">
                                            @foreach ($permission as $key => $data)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input id="permission-{{ $data->id }}-{{ $permission_key }}" name="permission[]" value="{{ $data->name }}" type="checkbox" class="check-{{ $permission_key }} chk-col-info" name="permission[]" @if ($model && $model->hasPermissionTo($data->name)) checked @endif>
                                                        <label for="permission-{{ $data->id }}-{{ $permission_key }}">{{ ucwords(getAkaPermission($data->name)) }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="card">
                <div class="card-header" id="headingPenjualan">
                    <button class="text-dark accordion-button collapsed btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePenjualan" aria-expanded="false" aria-controls="collapsePenjualan">
                        <b>Penjualan</b>
                    </button>
                </div>
                @foreach ($permissions->sortKeys() as $permission_key => $permission)
                    @if (in_array($permission_key, $penjualan))
                        <div id="collapsePenjualan" class="px-3 collapse" aria-labelledby="headingPenjualan" data-parent="#accordion">
                            <div class="card-body py-0" id="childPenjualan">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <button type="button" class="accordion-button text-dark collapsed btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $permission_key }}" aria-expanded="false" aria-controls="collapsePenjualan">
                                            {{ ucwords(str_replace('-', ' ', $permission_key)) }} &nbsp;<small> ({{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }})</small>
                                        </button>
                                    </div>
                                    <div class="card-body bg-light collapse" data-parent="#childPenjualan" id="collapse-{{ $permission_key }}">
                                        <input id="{{ $permission_key }}-check" type="checkbox" class="{{ $permission_key }} chk-col-info" name="permission_name">
                                        <label for="{{ $permission_key }}-check" class="form-label text-info">{{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }} <span class="text-primary">*</span></label>
                                        <div class="row mt-10">
                                            @foreach ($permission as $key => $data)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input id="permission-{{ $data->id }}-{{ $permission_key }}" name="permission[]" value="{{ $data->name }}" type="checkbox" class="check-{{ $permission_key }} chk-col-info" name="permission[]" @if ($model && $model->hasPermissionTo($data->name)) checked @endif>
                                                        <label for="permission-{{ $data->id }}-{{ $permission_key }}">{{ ucwords(getAkaPermission($data->name)) }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="card">
                <div class="card-header" id="headingPembelian">
                    <button class="text-dark accordion-button collapsed btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePembelian" aria-expanded="false" aria-controls="collapsePembelian">
                        <b>Pembelian</b>
                    </button>
                </div>
                @foreach ($permissions->sortKeys() as $permission_key => $permission)
                    @if (in_array($permission_key, $pembelian))
                        <div id="collapsePembelian" class="px-3 collapse" aria-labelledby="headingPembelian" data-parent="#accordion">
                            <div class="card-body py-0" id="childPembelian">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <button type="button" class="accordion-button text-dark collapsed btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $permission_key }}" aria-expanded="false" aria-controls="collapsePembelian">
                                            {{ ucwords(str_replace('-', ' ', $permission_key)) }} &nbsp;<small> ({{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }})</small>
                                        </button>
                                    </div>
                                    <div class="card-body bg-light collapse" data-parent="#childPembelian" id="collapse-{{ $permission_key }}">
                                        <input id="{{ $permission_key }}-check" type="checkbox" class="{{ $permission_key }} chk-col-info" name="permission_name">
                                        <label for="{{ $permission_key }}-check" class="form-label text-info">{{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }} <span class="text-primary">*</span></label>
                                        <div class="row mt-10">
                                            @foreach ($permission as $key => $data)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input id="permission-{{ $data->id }}-{{ $permission_key }}" name="permission[]" value="{{ $data->name }}" type="checkbox" class="check-{{ $permission_key }} chk-col-info" name="permission[]" @if ($model && $model->hasPermissionTo($data->name)) checked @endif>
                                                        <label for="permission-{{ $data->id }}-{{ $permission_key }}">{{ ucwords(getAkaPermission($data->name)) }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="card">
                <div class="card-header" id="headingGudang">
                    <button class="text-dark accordion-button collapsed btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGudang" aria-expanded="false" aria-controls="collapseGudang">
                        <b>Gudang</b>
                    </button>
                </div>
                @foreach ($permissions->sortKeys() as $permission_key => $permission)
                    @if (in_array($permission_key, $gudang))
                        <div id="collapseGudang" class="px-3 collapse" aria-labelledby="headingGudang" data-parent="#accordion">
                            <div class="card-body py-0" id="childGudang">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <button type="button" class="accordion-button text-dark collapsed btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $permission_key }}" aria-expanded="false" aria-controls="collapseGudang">
                                            {{ ucwords(str_replace('-', ' ', $permission_key)) }} &nbsp;<small> ({{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }})</small>
                                        </button>
                                    </div>
                                    <div class="card-body bg-light collapse" data-parent="#childGudang" id="collapse-{{ $permission_key }}">
                                        <input id="{{ $permission_key }}-check" type="checkbox" class="{{ $permission_key }} chk-col-info" name="permission_name">
                                        <label for="{{ $permission_key }}-check" class="form-label text-info">{{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }} <span class="text-primary">*</span></label>
                                        <div class="row mt-10">
                                            @foreach ($permission as $key => $data)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input id="permission-{{ $data->id }}-{{ $permission_key }}" name="permission[]" value="{{ $data->name }}" type="checkbox" class="check-{{ $permission_key }} chk-col-info" name="permission[]" @if ($model && $model->hasPermissionTo($data->name)) checked @endif>
                                                        <label for="permission-{{ $data->id }}-{{ $permission_key }}">{{ ucwords(getAkaPermission($data->name)) }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="card">
                <div class="card-header" id="headingHrd">
                    <button class="text-dark accordion-button collapsed btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHrd" aria-expanded="false" aria-controls="collapseHrd">
                        <b>Hrd</b>
                    </button>
                </div>
                @foreach ($permissions->sortKeys() as $permission_key => $permission)
                    @if (in_array($permission_key, $hrd))
                        <div id="collapseHrd" class="px-3 collapse" aria-labelledby="headingHrd" data-parent="#accordion">
                            <div class="card-body py-0" id="childHrd">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <button type="button" class="accordion-button text-dark collapsed btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $permission_key }}" aria-expanded="false" aria-controls="collapseHrd">
                                            {{ ucwords(str_replace('-', ' ', $permission_key)) }} &nbsp;<small> ({{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }})</small>
                                        </button>
                                    </div>
                                    <div class="card-body bg-light collapse" data-parent="#childHrd" id="collapse-{{ $permission_key }}">
                                        <input id="{{ $permission_key }}-check" type="checkbox" class="{{ $permission_key }} chk-col-info" name="permission_name">
                                        <label for="{{ $permission_key }}-check" class="form-label text-info">{{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }} <span class="text-primary">*</span></label>
                                        <div class="row mt-10">
                                            @foreach ($permission as $key => $data)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input id="permission-{{ $data->id }}-{{ $permission_key }}" name="permission[]" value="{{ $data->name }}" type="checkbox" class="check-{{ $permission_key }} chk-col-info" name="permission[]" @if ($model && $model->hasPermissionTo($data->name)) checked @endif>
                                                        <label for="permission-{{ $data->id }}-{{ $permission_key }}">{{ ucwords(getAkaPermission($data->name)) }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="card">
                <div class="card-header" id="headingAkuntansi">
                    <button class="text-dark accordion-button collapsed btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAkuntansi" aria-expanded="false" aria-controls="collapseAkuntansi">
                        <b>Akuntansi</b>
                    </button>
                </div>
                @foreach ($permissions->sortKeys() as $permission_key => $permission)
                    @if (in_array($permission_key, $akuntansi))
                        <div id="collapseAkuntansi" class="px-3 collapse" aria-labelledby="headingAkuntansi" data-parent="#accordion">
                            <div class="card-body py-0" id="childAkuntansi">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <button type="button" class="accordion-button text-dark collapsed btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $permission_key }}" aria-expanded="false" aria-controls="collapseAkuntansi">
                                            {{ ucwords(str_replace('-', ' ', $permission_key)) }} &nbsp;<small> ({{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }})</small>
                                        </button>
                                    </div>
                                    <div class="card-body bg-light collapse" data-parent="#childAkuntansi" id="collapse-{{ $permission_key }}">
                                        <input id="{{ $permission_key }}-check" type="checkbox" class="{{ $permission_key }} chk-col-info" name="permission_name">
                                        <label for="{{ $permission_key }}-check" class="form-label text-info">{{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }} <span class="text-primary">*</span></label>
                                        <div class="row mt-10">
                                            @foreach ($permission as $key => $data)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input id="permission-{{ $data->id }}-{{ $permission_key }}" name="permission[]" value="{{ $data->name }}" type="checkbox" class="check-{{ $permission_key }} chk-col-info" name="permission[]" @if ($model && $model->hasPermissionTo($data->name)) checked @endif>
                                                        <label for="permission-{{ $data->id }}-{{ $permission_key }}">{{ ucwords(getAkaPermission($data->name)) }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="card">
                <div class="card-header" id="headingOther">
                    <button class="text-dark accordion-button collapsed btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOther" aria-expanded="false" aria-controls="collapseOther">
                        <b>Lain - Lain</b>
                    </button>
                </div>
                @foreach ($permissions->sortKeys() as $permission_key => $permission)
                    @if (!in_array($permission_key, $master) && !in_array($permission_key, $penjualan) && !in_array($permission_key, $pembelian) && !in_array($permission_key, $gudang) && !in_array($permission_key, $hrd) && !in_array($permission_key, $akuntansi) && !in_array($permission_key, $report))
                        <div id="collapseOther" class="px-3 collapse" aria-labelledby="headingOther" data-parent="#accordion">
                            <div class="card-body py-0" id="childOther">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <button type="button" class="accordion-button text-dark collapsed btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $permission_key }}" aria-expanded="false" aria-controls="collapseOther">
                                            {{ ucwords(str_replace('-', ' ', $permission_key)) }} &nbsp;<small> ({{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }})</small>
                                        </button>
                                    </div>
                                    <div class="card-body bg-light collapse" data-parent="#childOther" id="collapse-{{ $permission_key }}">
                                        <input id="{{ $permission_key }}-check" type="checkbox" class="{{ $permission_key }} chk-col-info" name="permission_name">
                                        <label for="{{ $permission_key }}-check" class="form-label">{{ ucwords(str_replace('-', ' ', AKA_PERMISSIONS[$permission_key])) }} <span class="text-primary">*</span></label>
                                        <div class="row mt-10">
                                            @foreach ($permission as $key => $data)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input id="permission-{{ $data->id }}-{{ $permission_key }}" name="permission[]" value="{{ $data->name }}" type="checkbox" class="check-{{ $permission_key }} chk-col-info" name="permission[]" @if ($model && $model->hasPermissionTo($data->name)) checked @endif>
                                                        <label for="permission-{{ $data->id }}-{{ $permission_key }}">{{ ucwords(getAkaPermission($data->name)) }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="box-footer">
        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
            <x-button type="submit" color="primary" label="Save data" />
        </div>
    </div>
</form>

@push('script')
    <script>
        $(document).ready(function() {
            $('input[name="permission_name"]').on('click', function() {
                var permission_name = $(this).attr('class');
                $('.check-' + permission_name.split(' ')[0]).prop('checked', this.checked);
            });
        });
    </script>
@endpush
