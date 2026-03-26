@if ($type == 'checkbox')
    <input type="checkbox" id="print_check_{{ $data->id }}" style="position: unset; left: 0; opacity: 1" class="form-check-input print_check" value="{{ $data->id }}" {{ $data->can_print ? 'checked' : '' }}>
@else
    {!! $data->can_print ? '<i class="fa fa-check fs-3 text-primary check_master_print"></i>' : '' !!}
@endif