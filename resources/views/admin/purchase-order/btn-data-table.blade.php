@if ($row->check_available_date)
    @if (in_array($row->status, ['pending', 'revert']))
    @can('edit purchase-order')
        <x-button link="{{ route('admin.purchase-order.edit', $row) }}" color="warning" size="sm" icon="pen" fontawesome />
    @endcan
    @can('delete purchase-order')
        <x-button color='danger' icon='trash' fontawesome size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $row->id }}' />
        <x-modal-delete id="delete-modal-{{ $row->id }}" url='{{ "admin.purchase-order.destroy" }}' dataId="{{ $row->id }}" />
    @endcan
    @endif
@endif