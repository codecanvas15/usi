@if ($btn_config['detail']['display'])
    @can("view $main")
        <x-button color='primary' icon='eye' fontawesome size="sm" link='{{ route("admin.$main.show", $row) }}' />
    @endcan
@endif

@if ($btn_config['edit']['display'])
    @can("edit $main")
        <x-button color='warning' icon='edit' fontawesome size="sm" link='{{ route("admin.$main.edit", $row) }}' />
    @endcan
@endif

@if ($btn_config['delete']['display'])
    @can("delete $main")
        <x-button color='danger' icon='trash' fontawesome size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $row->id }}' />
        <div class="modal fade" id="delete-modal-{{ $row->id }}" tabindex="-1" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title">{{ Str::headline('Are you sure to do this action ?') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.item.price.destroy', ['id' => $row->item_id, 'price_id' => $row->id]) }}" method="post">
                        @csrf
                        @method('delete')

                        <div class="modal-body">
                            <p>You'll lose your data, this action can't be undone.</p>
                        </div>

                        <div class="modal-footer">
                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                            <x-button type="submit" color="danger" label="Delete" />
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endcan
@endif
