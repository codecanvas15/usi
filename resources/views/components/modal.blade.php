<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog" @if ($modalSize != '') style="max-width: {{ $modalSize }}px" @endif>
        <div class="modal-content rounded">
            <div class="modal-header bg-{{ $headerColor }}">
                <h5 class="modal-title">{{ Str::headline($title) }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $modal_body }}
            </div>
            @if ($modal_footer && $modal_footer != '')
                <div class="modal-footer">
                    {{ $modal_footer }}
                </div>
            @endif
        </div>
    </div>
</div>
