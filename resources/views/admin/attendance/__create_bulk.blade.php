<form action="{{ $model ? route("admin.$main.update", $model) : route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
    @csrf

    @if ($model)
        @method('PUT')
    @endif

    <div class="row">
        @if (get_current_branch()->is_primary)
            <div class="col-md-3">
                <x-select name="branch_id" label="branch" id="select-branch" required onchange="$('#attendance-table').DataTable().ajax.reload()">
                    @if ($model and $model->branch)
                        <option value="{{ $model->branch_id }}">{{ $model->branch?->name }}</option>
                    @endif
                </x-select>
            </div>
        @else
            <input type="hidden" name="branch_id" value="{{ get_current_branch()->id }}">
        @endif
        <div class="col-md-3">
            <div class="form-group">
                <x-input class="datepicker-input" label="tanggal" name="date" value="{{ $model ? localDate($model->date) : '' }}" id="date" required />
            </div>
        </div>
        <input type="hidden" name="data" id="data">
    </div>

    <div class="row">
        <div class="col-md-12">
            <x-card-data-table title="Presensi">
                <x-slot name="table_content">
                    <x-table theadColor='' id="attendance-table">
                        <x-slot name="table_head">
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Lama Kerja</th>
                            <th>Lembur</th>
                            <th>Keterangan</th>
                        </x-slot>
                        <x-slot name="table_body">

                        </x-slot>
                    </x-table>
                </x-slot>
            </x-card-data-table>
        </div>
        <div class="col-md-12 text-end">
            <x-button color="secondary" label="Cancel" link="{{ url()->previous() }}" />
            <x-button type="submit" color="primary" label="Save" />
        </div>
    </div>

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

        var data_attendance = [];

        $('#attendance-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            destroy: true,
            ajax: {
                url: '{{ route('admin.attendance.data-employee') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    data_attendance: function() {
                        return JSON.stringify(data_attendance);
                    },
                    branch_id: function() {
                        return $('input[name="branch_id"]').val() ?? $('select[name="branch_id"]').val();
                    }
                },
                complete: function(res) {
                    $.each(res.responseJSON.data, function(index, value) {
                        data_attendance[value.id] = {
                            id: value.id,
                            in_time: value.in_time_val ?? '',
                            out_time: value.out_time_val ?? '',
                            attendance_hours: value.attendance_hours_val ?? '',
                            overtime: value.overtime_val ?? '',
                            description: value.description_val ?? ''
                        }
                    });

                    console.log(data_attendance);
                }
            },
            order: [
                [1, 'desc']
            ],
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'NIK',
                    name: 'NIK'
                },
                {
                    data: 'in_time',
                    searchable: false,
                    orderable: false,
                },
                {
                    data: 'out_time',
                    searchable: false,
                    orderable: false,
                },
                {
                    data: 'attendance_hours',
                    searchable: false,
                    orderable: false,
                },
                {
                    data: 'overtime',
                    searchable: false,
                    orderable: false,
                },
                {
                    data: 'description',
                    searchable: false,
                    orderable: false,
                },

            ]
        });

        function insert_presence(key, id) {
            data_attendance[id][key] = $(`#${key}_${id}`).val();

            console.log(data_attendance);
        }

        $('form').submit(function(e) {
            $('#data').val(JSON.stringify(data_attendance));
        })
    </script>
@endpush
