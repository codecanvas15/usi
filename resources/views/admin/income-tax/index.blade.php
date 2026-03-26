@extends('layouts.admin.layout.index')

@section('title', Str::headline($title) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('content')
    @can("view $permission")
        <x-card-data-table :title="$title">
            <x-slot name="table_content">
                @can("create $permission")
                    <x-button color="info" icon="plus" label="Create" link='{{ route("$routeName.create") }}' class="mb-3" />
                @endcan

                <x-table>
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('range') }}</th>
                        <th>{{ Str::headline('persentase') }}</th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            </x-slot>
        </x-card-data-table>

        <x-card-data-table title="setting">
            <x-slot name="table_content">
                <form action='{{ route('admin.setting.store') }}' method="post">
                    @csrf
                    <div class="row">
                        @foreach ($settings as $setting)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" name="type[]" value="{{ $setting->type }}">
                                    <input type="hidden" name="name[]" value="{{ $setting->name }}">
                                    <input type="hidden" name="is_numeric[]" value="{{ is_numeric($setting->value) }}">
                                    <x-input name="value[]" placeholder="{{ $setting->name }}" label="{{ $setting->name }}" :value="is_numeric($setting->value) ? formatNumber($setting->value) : $setting->value" required class="{{ is_numeric($setting->value) ? 'commas-form' : '' }}" />
                                </div>
                            </div>
                        @endforeach
                        <div class="col-md-12 text-end">
                            @can("update $permission")
                                <x-button type="submit" color="primary" icon="save" label="Simpan" />
                            @endcan
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-card-data-table>
    @endcan
@endsection

@section('js')
    @can("view $permission")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script>
            $(document).ready(() => {
                const table = $('table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route("$routeName.index") }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'range',
                            name: 'min'
                        },
                        {
                            data: 'percentage',
                            name: 'percentage'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                $('table').css('width', '100%');
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#master-salary-sidebar');
        sidebarActive('#income-tax');
    </script>
@endsection
