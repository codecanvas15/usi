<x-card-data-table title="Finance">
    <x-slot name="header_content">
        <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
            <li class="nav-item">
                <a class="nav-link rounded" data-bs-toggle="tab" href="#fund-submission-tab" id="fund-submission-btn" role="tab">
                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                    <span class="hidden-xs-down">PDK</span>
                    <span class="notification-bubble d-none" id="notification-counter-bubble-finance-car"></span>
                </a>
            </li>
        </ul>
    </x-slot>
    <x-slot name="table_content">
        <div class="tab-content mt-30">
            <div class="tab-pane" id="fund-submission-tab" role="tabpanel">
                @include('admin.authorization.partial.finance.fund-submission')
            </div>
        </div>
    </x-slot>
</x-card-data-table>
