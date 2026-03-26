<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">{{ Str::headline($title) }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route($url, $dataId)}}" method="post">
                @csrf
                @method("delete")

                <div class="modal-body">
                    <p>{{ $text }}</p>
                </div>

                <div class="modal-footer">
                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel"/>
                    <x-button type="submit" color="danger" label="Delete"/>
                </div>

            </form>
        </div>
    </div>
</div>