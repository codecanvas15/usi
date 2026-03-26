@canany(['view purchase-request', 'view purchase-request-service', 'view purchase-request-general', 'view purchase-request-transport'])
    <a href="{{ route("admin.$main.show", $row) }}" class="text-primary text-decoration-underline hover_text-dark">{{ $field }}</a>
@endcanany
