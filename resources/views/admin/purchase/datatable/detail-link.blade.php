@php
$route = '';

if ($row->tipe == 'general') {
    $route = 'admin.purchase-order-general.show';
}

if ($row->tipe == 'trading') {
    $route = 'admin.purchase-order.show';
}

if ($row->tipe == 'transportir') {
    $route = 'admin.purchase-order-transport.show';
}

if ($row->tipe == 'jasa') {
    $route = 'admin.purchase-order-service.show';
}

@endphp

<a href="{{ route($route, $row->reference) }}" target="_blank" class="text-primary text-decoration-underline hover_text-dark">{{ $field }}</a>
