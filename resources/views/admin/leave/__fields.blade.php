<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
    @csrf
    @if ($model)
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <x-select name="branch_id" label="branch" id="branch-select" required>
                    @if ($model)
                        <option value="{{ $model->branch_id }}" selected>{{ $model->branch->name }}</option>
                    @else
                        <option value="{{ get_current_branch()->id }}">{{ get_current_branch()->name }}</option>
                    @endif
                </x-select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input class="datepicker-input" name="date" value="{{ $model ? $model?->date : null ?? \Carbon\Carbon::now()->format('d-m-Y') }}" label="tanggal" id="date" required onblur="checkDate()" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Cuti/izin</label>
                <br>
                <x-input-radio name="type" label="cuti" id="cuti_value" value="cuti" required />
                <x-input-radio name="type" label="izin" id="izin_value" value="izin" required />
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                <label for="">Cuti/Tidak Masuk Untuk</label>
                <br>
                <x-input-radio name="necessary" label="liburan" id="vacation" value="vacation" required />
                <x-input-radio name="necessary" label="sakit" id="illnes" value="illnes" required />
                <x-input-radio name="necessary" label="melahirkan" id="maternity" value="maternity" required />
                <x-input-radio name="necessary" label="lain lain" id="others" value="others" required />
            </div>
        </div>

        <div class="col-md-12"></div>
        <div class="col-md-3">
            <div class="form-group">
                <x-select name="employee_id" label="pegawai" id="employee-select" required>
                    @if ($model)
                        <option value="{{ $model->employee_id }}" selected>{{ $model->employee?->name }} - {{ $model->employee?->NIK }}</option>
                    @else
                        @if (Auth::user()->employee)
                            <option value="{{ Auth::user()->employee->id }}" selected>{{ Auth::user()->employee->name }} - {{ Auth::user()->employee->NIK }}</option>
                        @endif
                    @endif
                </x-select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="text" name="leave_remaining" label="sisa jatah cuti" id="leave-remaining" readonly required value="{{ $model ? $model->leave_remaining : '' }}" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input class="datepicker-input" name="from_date" value="{{ $model ? $model?->from_date : '' ?? \Carbon\Carbon::now()->format('d-m-Y') }}" label="dari tanggal" id="from_date" required onchange="checkDate();" autocomplete="off" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input class="datepicker-input" name="to_date" value="{{ $model ? $model?->to_date : '' ?? \Carbon\Carbon::now()->format('d-m-Y') }}" label="sampai tanggal" id="to_date" required onchange="checkDate();" autocomplete="off" />
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <x-text-area name="cause" label="alasan/keperluan cuti" id="cause" cols="30" rows="10" required>
                    {!! $model ? $model->cause : '' !!}
                </x-text-area>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <x-text-area name="note" label="keterangan" id="" cols="30" rows="10" required>
                    {!! $model ? $model->note : '' !!}
                </x-text-area>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="text" name="phone_number" value="{{ $model ? $model?->phone_number : '' }}" label="Nomor Hp selama cuti" id="" required />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <x-text-area name="address" label="alamat selama cuti" id="" cols="30" rows="10" required>
                    {!! $model ? $model->note : '' !!}
                </x-text-area>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <x-input type="file" name="attachment" label="upload file" id="attachment" onchange="handleChangeAttachment(event)" />
                <div id="preview-attachment">
                    @if ($model && $model->attachment)
                        <embed width="150" src="{{ asset('storage/' . $model->attachment) }}"></embed>
                    @endif
                </div>
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
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        $(document).ready(function() {

            $('#employee-select').change(function(e) {
                e.preventDefault();
                get_leave_remaining();

            });
            $('#employee-select').change();

        });

        function get_leave_remaining() {
            if ($('input[name="type"]:checked').val() == 'cuti') {
                $.ajax({
                    type: "post",
                    url: "{{ route('admin.leave.remaining') }}",
                    data: {
                        employee_id: function() {
                            return $('#employee-select').val();
                        },
                        from_date: function() {
                            return $('#from_date').val()
                        },
                        to_date: function() {
                            return $('#to_date').val()
                        },
                        _token: token,
                    },
                    success: function({
                        data
                    }) {
                        $('#leave-remaining').val(data);
                    },
                    error: function(xhr, status, error) {
                        $('#from_date').val('');
                        $('#to_date').val('');
                        Swal.fire('', xhr.responseJSON.message, 'error');
                    }
                });
            }
        }

        function checkDate() {
            if ($('#from_date').val() != '' && $('#to_date').val() != '') {
                get_leave_remaining()
                const parsedDate1 = new Date(parseDate($('#from_date').val()));
                const parsedDate2 = new Date(parseDate($('#to_date').val()));
                let monthIsCorrect = parsedDate1.getMonth() == parsedDate2.getMonth();

                if (!monthIsCorrect) {
                    alert('Pilih tanggal di bulan yang sama!');
                    $('#to_date').val('');
                }
            }
        }

        function handleChangeAttachment(e) {
            const file = e.target.files[0]
            const reader = new FileReader()
            reader.readAsDataURL(file)
            reader.onload = function() {
                $('#preview-attachment').html(`
                     <embed width="150" src="${reader.result}"></embed>
                  `)
            }
        }
    </script>

    @if (get_current_branch()->is_primary && Auth::user()->can('create employee'))
        <script>
            initSelectEmployee('#employee-select')
            initBranchSelect('#branch-select')
        </script>
    @endif

    @if ($model)
        <script>
            $(document).ready(function() {
                $('input[name="type"][value="{{ $model->type }}"]').prop('checked', true)
                $('input[name="necessary"][value="{{ $model->necessary }}"]').prop('checked', true)
            })
        </script>
    @endif
@endpush
