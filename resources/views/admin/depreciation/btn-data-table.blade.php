@can('create depreciation')
    <x-button color='danger' icon='trash' fontawesome size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $row->id }}' />
    <x-modal-delete id="delete-modal-{{ $row->id }}" url="{{ 'admin.depreciation.destroy' }}" dataId="{{ $row->id }}" />
@endcan
