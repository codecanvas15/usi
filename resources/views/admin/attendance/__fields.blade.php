<form action="{{ $model ? route("admin.$main.update", $model) : route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
    @csrf

    @if ($model)
        @method('PUT')
    @endif

    @if (get_current_branch()->is_primary)
        <div class="row">
            <div class="col-md-3">
                <x-select name="branch_id" label="branch" id="select-branch" required>
                    @if ($model and $model->branch)
                        <option value="{{ $model->branch_id }}">{{ $model->branch?->name }}</option>
                    @endif
                </x-select>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-3">
            <x-select name="employee_id" label="karyawan" id="select-employee" required>
                @if ($model and $model->employee)
                    <option value="{{ $model->employee_id }}">{{ $model->employee?->name }} - {{ $model->employee?->NIK }}</option>
                @endif
            </x-select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <x-input class="datepicker-input" label="tanggal" name="date" onchange="checkClosingPeriod($(this))" value="{{ $model ? localDate($model->date) : '' }}" id="date" required />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="time" label="masuk" name="in_time" value="{{ $model ? $model->in_time : '' }}" id="" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="time" label="keluar" name="out_time" value="{{ $model ? $model->out_time : '' }}" id="" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="time" label="pulang lebih cepat" name="go_home_early" value="{{ $model ? $model->go_home_early : '' }}" id="" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="time" label="telambat" name="late" value="{{ $model ? $model->late : '' }}" id="" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="time" label="lembur" name="overtime" value="{{ $model ? $model->overtime : '' }}" id="" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="time" label="jam kerja" name="work_hours" value="{{ $model ? $model->work_hours : '' }}" id="" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="time" label="jam hadir" name="attendance_hours" value="{{ $model ? $model->attendance_hours : '' }}" id="" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="text" label="deskripsi" name="description" value="{{ $model ? $model->description : '' }}" id="" />
            </div>
        </div>
    </div>

    <x-button color="secondary" label="Cancel" link="{{ url()->previous() }}" />
    <x-button type="submit" color="primary" label="Save" />

</form>

@push('script')
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>

    <script>
        $(document).ready(function() {
            // checkClosingPeriod($('date'))
            initBranchSelect('#select-branch');
            initSelectEmployee('#select-employee');
        });
    </script>
@endpush
