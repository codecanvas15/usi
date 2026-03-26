<x-card-data-table title="Purchase">
    <x-slot name="header_content">
        <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
            <li class="nav-item">
                <a class="nav-link rounded" data-bs-toggle="tab" href="#purchase-general-tab" id="purchase-general-btn" role="tab">
                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                    <span class="hidden-xs-down">General</span>
                    <span class="notification-bubble d-none" id="notification-counter-bubble-purchase-general"></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded" data-bs-toggle="tab" href="#purchase-service-tab" id="purchase-service-btn" role="tab">
                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                    <span class="hidden-xs-down">Service</span>
                    <span class="notification-bubble d-none" id="notification-counter-bubble-purchase-service"></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded" data-bs-toggle="tab" href="#purchase-transport-tab" id="purchase-transport-btn" role="tab">
                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                    <span class="hidden-xs-down">Transport</span>
                    <span class="notification-bubble d-none" id="notification-counter-bubble-purchase-transport"></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded" data-bs-toggle="tab" href="#purchase-trading-tab" id="purchase-trading-btn" role="tab">
                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                    <span class="hidden-xs-down">Trading</span>
                    <span class="notification-bubble d-none" id="notification-counter-bubble-purchase-trading"></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded" data-bs-toggle="tab" href="#purchase-request-tab" id="purchase-request-btn" role="tab">
                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                    <span class="hidden-xs-down">Request</span>
                    <span class="notification-bubble d-none" id="notification-counter-bubble-purchase-request"></span>
                </a>
            </li>
        </ul>
    </x-slot>
    <x-slot name="table_content">
        <div class="tab-content mt-30">
            <div class="tab-pane" id="purchase-general-tab" role="tabpanel">
                @include('admin.authorization.partial.purchase.purchase-general')
            </div>
            <div class="tab-pane" id="purchase-service-tab" role="tabpanel">
                @include('admin.authorization.partial.purchase.purchase-service')
            </div>
            <div class="tab-pane" id="purchase-transport-tab" role="tabpanel">
                @include('admin.authorization.partial.purchase.purchase-transport')
            </div>
            <div class="tab-pane" id="purchase-trading-tab" role="tabpanel">
                @include('admin.authorization.partial.purchase.purchase-trading')
            </div>
            <div class="tab-pane" id="purchase-request-tab" role="tabpanel">
                @include('admin.authorization.partial.purchase.purchase-request')
            </div>
        </div>
    </x-slot>
</x-card-data-table>
