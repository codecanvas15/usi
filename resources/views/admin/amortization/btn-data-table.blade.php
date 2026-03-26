@can('create amortization')
    <x-button color='danger' icon='trash' fontawesome size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $row->id }}' />
    <x-modal-delete id="delete-modal-{{ $row->id }}" url="{{ 'admin.amortization.destroy' }}" dataId="{{ $row->id }}" />
@endcan
