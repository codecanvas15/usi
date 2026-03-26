@if ($btn_config['detail']['display'])
    <x-button color='primary' icon='eye' fontawesome size="sm" link="{{ route('admin.' . $main . '.generate-single-show', ['id' => $row, 'model_id' => $transport, 'transport_id' => $model]) }}" />
@endif

@if ($btn_config['edit']['display'])
    <x-button color='warning' icon='edit' fontawesome size="sm" link="{{ route('admin.' . $main . '.generate-single-edit', ['id' => $row, 'model_id' => $transport, 'transport_id' => $model]) }}" />
@endif
