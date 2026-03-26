<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-select name="user_type" id="user-type" required autofocus>
                    <option value="" selected>Pilih tipe</option>
                    <option value="employee">Pegawai</option>
                    <option value="non-employee">Bukan Pegawai</option>
                    <option value="vendor">Vendor</option>
                </x-select>
            </div>
        </div>
    </div>

    <div class="row mt-20" id="user-base">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="username" name="username" id="" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="name" name="name" id="" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="email" label="email" name="email" id="" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="password" label="password" name="password" id="" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="password" label="confirm_password" name="confirm_password" id="" required />
            </div>
        </div>
    </div>

    <div class="row mt-20" id="vendor-select">

    </div>

    <div class="mt-20" id="employee-form">

    </div>

    <div class="row mt-20" id="employee-and-user-extra-form">

    </div>

    <div class="box-footer">
        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
            <x-button type="submit" color="primary" label="Save data" />
        </div>
    </div>

</form>

@push('script')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#vendor-select').hide();

            $('#employee-form').hide();
            $('#select-employee').hide();
            $('#create-employee').hide();

            $('#employee-and-user-extra-form').hide();

            const userEmployeeExtraForm = () => {
                let html = `<div class="col-md-4">
                                <div class="form-group">
                                    <x-select  id="branch_id" name="branch_id" label="branch">
                                        @if ($model)
                                            <option value="{{ $model->branch_id }}">{{ $model->branch?->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select id="division_id" name="division_id" label="division">
                                        @if ($model)
                                            <option value="{{ $model->division_id }}">{{ $model->division?->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select id="project_id" name="project_id" label="project">
                                        @if ($model)
                                            <option value="{{ $model->project_id }}">{{ $model->project?->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select label="role" name="role[]" id="role-select" required multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>`;

                $('#employee-and-user-extra-form').html('');
                $('#employee-and-user-extra-form').show();

                $('#employee-and-user-extra-form').append(html);

                $('#role-select').select2();

                initSelect2Search('branch_id', "{{ route('admin.select.branch') }}", {
                    id: "id",
                    text: "name"
                });

                initSelect2Search('division_id', "{{ route('admin.select.division') }}", {
                    id: "id",
                    text: "name"
                });

                initSelect2Search('project_id', "{{ route('admin.select.project') }}", {
                    id: "id",
                    text: "name"
                });
            }

            const vendorFormShow = () => {
                $('#vendor-select').show();

                $('#employee-form').html('');
                $('#employee-and-user-extra-form').html('');
                $('#employee-form').hide();

                $('#select-employee').hide();
                $('#create-employee').hide();
                $('#employee-and-user-extra-form').hide();

                $('#vendor-select').show();
                $('#vendor-select').html(`<div class="col-md-4">
                                                <x-select name="vendor_id" id="vendor-id" label="vendor" required>

                                                </x-select>
                                            </div>`);

                initSelect2Search('vendor-id', "{{ route('admin.select.vendor') }}", {
                    id: "id",
                    text: "nama"
                });
            }

            const selectEmployeeForm = () => {
                $('#employee-form').html(`<div class="row mt-10" id="select-employee">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <x-select name="employee_id" label="employee" id="employee-select" required>
                                                            <option value="">select employee</option>
                                                        </x-select>
                                                    </div>
                                                </div>
                                            </div>`);

                initSelect2Search('employee-select', "{{ route('admin.select.employee') }}", {
                    id: "id",
                    text: "name"
                });

                $('#employee-select').change(function(e) {
                    e.preventDefault();
                    if (this.value) {
                        $.ajax({
                            url: "{{ route('admin.user.get-employee') }}",
                            data: {
                                employee_id: this.value
                            },
                            success: function({
                                data
                            }) {
                                if (!data || $('input[name="email"]').val()) return;

                                $('input[name="email"]').val(data);
                            }
                        });
                    } else {
                        $('input[name="email"]').val('');
                    }
                });

                userEmployeeExtraForm()
                $('#create-employee').html('');
            }


            const employeeFormShow = () => {
                $('#vendor-select').hide();
                $('#employee-form').show();
                selectEmployeeForm();
            }

            const nonEmployeeFormShow = () => {
                $('#vendor-select').hide();
                $('#employee-form').hide();
                nonEmployeeForm();
            }

            const nonEmployeeForm = () => {
                let html = `
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select id="gender" name="gender" label="jenis kelamin" required>
                                        <option value="Laki-laki">Laki - laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="no. telepon" name="phone" id="" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="instansi" name="agency" id="" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="alamat instansi" name="address" id="" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="nomor KTP" name="identity_number" id="" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="jabatan" name="role_name" id="" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select label="role" name="role[]" id="role-select" required multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>`;

                $('#employee-and-user-extra-form').html('');
                $('#employee-and-user-extra-form').show();

                $('#employee-and-user-extra-form').append(html);

                $('#role-select').select2()
            }

            const userFormShow = () => {
                $('#user-form').show();

                $('#employee-form').hide();
                $('#vendfor-select').hide();
                $('#employee-form').html('');
                $('#vendor-select').html('');

                userEmployeeExtraForm();
            }


            $('#user-type').change(function(e) {
                e.preventDefault();

                if (this.value) {

                    if (this.value == 'user') {
                        userFormShow();
                    } else if (this.value == 'employee') {
                        employeeFormShow();
                    } else if (this.value == 'vendor') {
                        vendorFormShow();
                    } else if (this.value == 'non-employee') {
                        nonEmployeeFormShow();
                    } else {
                        alert('error. invalid type');
                    }

                } else {
                    $('#vendor-select').html('');
                    $('#select-employee').html('');
                    $('#create-employee').html('');
                    $('#employee-form').html('');
                    $('#employee-and-user-extra-form').html('');

                    $('#vendor-select').hide();
                    $('#select-employee').hide();
                    $('#create-employee').hide();
                    $('#employee-form').hide();
                    $('#employee-and-user-extra-form').hide();
                }
            });

        });
    </script>
@endpush
