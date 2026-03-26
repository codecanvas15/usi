<x-card-data-table title="Sales">

    <x-slot name="header_content">
        <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
            <li class="nav-item">
                <a class="nav-link rounded" data-bs-toggle="tab" href="#sales-general-tab" id="sales-general-btn" role="tab">
                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                    <span class="hidden-xs-down">General</span>
                    <span class="notification-bubble d-none" id="notification-counter-bubble-sale-trading"></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded" data-bs-toggle="tab" href="#sales-trading-tab" id="sales-trading-btn" role="tab">
                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                    <span class="hidden-xs-down">Trading</span>
                    <span class="notification-bubble d-none" id="notification-counter-bubble-sale-general"></span>
                </a>
            </li>
        </ul>
    </x-slot>

    <x-slot name="table_content">
        <div class="tab-content mt-30">

            <div class="tab-pane" id="sales-general-tab" role="tabpanel">
                @include('admin.authorization.partial.sales.sales-general')
            </div>
            <div class="tab-pane" id="sales-trading-tab" role="tabpanel">
                @include('admin.authorization.partial.sales.sales-trading')
            </div>
        </div>
    </x-slot>
</x-card-data-table>
