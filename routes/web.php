<?php

use App\Http\Controllers\Accounting\DashboardController;
use App\Http\Controllers\Admin\QuotationItemController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Test\TestComponentController;
use App\Jobs\TestJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('phpinfo', function () {
//     phpinfo();
// });

// ajax for rate limiter
Route::post('/rate-limiter/ajax', [\App\Http\Controllers\Admin\RateLimiterController::class, 'rateLimiterAjax'])->name('ajax.rate-limiter');

// application
Route::get('/application{token?}', [\App\Http\Controllers\Admin\ApplicationController::class, 'index'])->name('application');
Route::post('/application', [\App\Http\Controllers\Admin\ApplicationController::class, 'store'])->name('application.store');

Route::get('inject/update-journal-do-trading', [\App\Http\Controllers\Admin\TestController::class, 'update_journal_do_trading']);

Route::get('/', function () {
    return redirect()->route('admin.index');
})->middleware('auth');

Route::group([
    'as' => 'admin.',
    'namespace' => '\App\Http\Controllers\Admin',
], function () {

    // * select form =============================================================================================================================
    Route::group([
        'as' => 'select.',
        'prefix' => 'select',
    ], function () {
        Route::get('labor-application', [\App\Http\Controllers\Admin\LaborApplicationController::class, 'select'])->name('labor-application');
        Route::get('master-user-assessment', [\App\Http\Controllers\Admin\MasterUserAssessmentController::class, 'select'])->name('master-user-assessment');
        Route::get('master-hrd-assessment', [\App\Http\Controllers\Admin\MasterHrdAssessmentController::class, 'select'])->name('master-hrd-assessment');
        Route::get('master-gp-evaluation', [\App\Http\Controllers\Admin\MasterGpEvaluationController::class, 'select'])->name('master-gp-evaluation');
        Route::get('user-assessment/select-candidate', [\App\Http\Controllers\Admin\UserAssessmentController::class, 'selectCandidate'])->name('user-assessment.select-candidate');
        Route::get('hrd-assessment/select-candidate', [\App\Http\Controllers\Admin\HrdAssessmentController::class, 'selectCandidate'])->name('hrd-assessment.select-candidate');
        Route::get('specific-time-work-agreement/select-second-employee', [\App\Http\Controllers\Admin\SpecificTimeWorkAgreementController::class, 'selectSecondEmployee'])->name('specific-time-work-agreement.select-second-employee');
        Route::get('user', [\App\Http\Controllers\Admin\UserController::class, 'select'])->name('user');
        Route::get('customer', [\App\Http\Controllers\Admin\CustomerController::class, 'select'])->name('customer');
        Route::get('degree', [\App\Http\Controllers\Admin\DegreeController::class, 'select'])->name('degree');
        Route::get('education', [\App\Http\Controllers\Admin\EducationController::class, 'select'])->name('education');
        Route::get('employee', [\App\Http\Controllers\Admin\EmployeeController::class, 'select'])->name('employee');
        Route::get('tax', [\App\Http\Controllers\Admin\TaxController::class, 'select'])->name('tax');
        Route::post('tax-post', [\App\Http\Controllers\Admin\TaxController::class, 'select_post'])->name('tax-post');

        Route::get('quotation-add-on-type', [\App\Http\Controllers\Admin\QuotationAddOnTypeController::class, 'select'])->name('quotation-add-on-type');
        Route::get('sh-number', [\App\Http\Controllers\Admin\ShNumberController::class, 'select'])->name('sh-number');
        Route::get('sh-number/customer/{id?}', [\App\Http\Controllers\Admin\ShNumberController::class, 'select_by_customer'])->name('sh-number.customer');
        Route::get('item-type', [\App\Http\Controllers\Admin\ItemTypeController::class, 'select'])->name('item-type');
        Route::get('item-category', [\App\Http\Controllers\Admin\ItemCategoryController::class, 'select'])->name('item-category');
        Route::get('unit', [\App\Http\Controllers\Admin\UnitController::class, 'select'])->name('unit');
        Route::get('period/{year?}', [\App\Http\Controllers\Admin\PeriodController::class, 'select'])->name('period');
        Route::get('vehicle', [\App\Http\Controllers\Admin\VechicleFleetController::class, 'select'])->name('vehicle');
        Route::get('position', [\App\Http\Controllers\Admin\PositionController::class, 'select'])->name('position');
        Route::get('coa', [\App\Http\Controllers\Admin\CoaController::class, 'select'])->name('coa');
        Route::get('coa/{type?}', [\App\Http\Controllers\Admin\CoaController::class, 'select_with_type'])->name('coa.type');
        Route::get('coa-types', [\App\Http\Controllers\Admin\CoaController::class, 'select_coa_types'])->name('coa-types');
        Route::get('ware-house', [\App\Http\Controllers\Admin\WareHouseController::class, 'select'])->name('ware-house');
        Route::get('ware-house/{type?}', [\App\Http\Controllers\Admin\WareHouseController::class, 'select_by_type'])->name('ware-house.type');
        Route::get('role', [\App\Http\Controllers\Admin\RoleController::class, 'select'])->name('role');
        Route::get('vendor', [\App\Http\Controllers\Admin\VendorController::class, 'select'])->name('vendor');
        Route::get('currency', [\App\Http\Controllers\Admin\CurrencyController::class, 'select'])->name('currency');
        Route::get('business-field', [\App\Http\Controllers\Admin\BusinessFieldController::class, 'select'])->name('business-field');
        Route::get('branch', [\App\Http\Controllers\Admin\BranchController::class, 'select'])->name('branch');
        Route::get('division', [\App\Http\Controllers\Admin\DivisionController::class, 'select'])->name('division');
        Route::get('bank-internal', [\App\Http\Controllers\Admin\BankInternalController::class, 'select'])->name('bank-internal');
        Route::get('project', [\App\Http\Controllers\Admin\ProjectController::class, 'select'])->name('project');
        Route::get('employment-status', [\App\Http\Controllers\Admin\EmploymentStatusController::class, 'select'])->name('employment-status');
        Route::get('customer/sh-numbers/{id?}', [\App\Http\Controllers\Admin\CustomerController::class, 'select_customer_shs'])->name('customer.sh-numbers');
        Route::get('customer/customer-bank/{id?}', [\App\Http\Controllers\Admin\CustomerController::class, 'selectCustomerBank'])->name('customer.customer-banks');
        Route::get('employee-with-user', [\App\Http\Controllers\Admin\EmployeeController::class, 'selectWithUser'])->name('employee-with-user');
        Route::get('employee-with-id/{id?}', [\App\Http\Controllers\Admin\EmployeeController::class, 'selectWithID'])->name('employee-with-id');

        Route::get('fleet', [\App\Http\Controllers\Admin\FleetController::class, 'select'])->name('fleet');
        Route::get('fleet/{type?}', [\App\Http\Controllers\Admin\FleetController::class, 'select_by_type'])->name('fleet.type');

        Route::get('item', [\App\Http\Controllers\Admin\ItemController::class, 'select'])->name('item');
        Route::get('item/{type?}', [\App\Http\Controllers\Admin\ItemController::class, 'select_by_type'])->name('item.type');
        Route::get('item-general', [\App\Http\Controllers\Admin\ItemController::class, 'select_general'])->name('item-general');
        Route::get('item-trading', [\App\Http\Controllers\Admin\ItemController::class, 'select_trading'])->name('item-trading');

        Route::get('price/{id?}', [\App\Http\Controllers\Admin\PriceController::class, 'select'])->name('price');
        Route::get('price/harga-jual/{item?}/{customer?}/{date?}', [\App\Http\Controllers\Admin\PriceController::class, 'select_with_period_and_customer_and_search_harga_jual'])->name('select-with-period-and-customer-and-search-harga-jual');
        Route::get('price/harga-beli/{item?}/{customer?}/{date?}', [\App\Http\Controllers\Admin\PriceController::class, 'select_with_period_and_customer_and_search_harga_beli'])->name('select-with-period-and-customer-and-search-harga-beli');

        Route::get('price/harga-jual-sh/sh-number/{item?}/{sh_number?}/{date?}', [\App\Http\Controllers\Admin\PriceController::class, 'select_with_period_and_sh_number_and_search_harga_jual'])->name('select-with-period-and-sh-number-and-search-harga-jual');
        Route::get('price/harga-beli-sh/sh-number/{item?}/{sh_number?}/{date?}', [\App\Http\Controllers\Admin\PriceController::class, 'select_with_period_and_sh_number_and_search_harga_beli'])->name('select-with-period-and-sh-number-and-search-harga-beli');

        Route::get('sale-order', [\App\Http\Controllers\Admin\SoTradingController::class, 'select'])->name('sale-order');
        Route::get('sos-for-do', [\App\Http\Controllers\Admin\SoTradingController::class, 'select_for_delivery_order'])->name('sos-for-do');
        Route::get('sh-numbers-for-so/{id?}', [\App\Http\Controllers\Admin\SoTradingController::class, 'select_sh_number_from_so'])->name('sh-numbers-for-so');
        Route::get('po-trading-for-so/{id?}', [\App\Http\Controllers\Admin\SoTradingController::class, 'select_po_trading_from_so'])->name('po-trading-for-so');
        Route::get('so-jumlah/{id?}', [\App\Http\Controllers\Admin\SoTradingController::class, 'get_jumlah_so'])->name('so-jumlah');
        Route::get('so-sh-number/{id?}', [\App\Http\Controllers\Admin\SoTradingController::class, 'sh_number_so'])->name('so-sh-number');
        Route::get('sos-for-purchase-order', [\App\Http\Controllers\Admin\SoTradingController::class, 'select_for_purchase_order'])->name('sos-for-purchase-order');
        Route::get('purchase-request-order', [\App\Http\Controllers\Admin\PurchaseRequestReportController::class, 'select'])->name('purchase-request-order');

        Route::post('so-trading/{id}/bypass-pairing', [\App\Http\Controllers\Admin\SoTradingController::class, 'bypass_pairing'])->name('so-trading.bypass-pairing');
        Route::post('so-trading/{id}/cancel-bypass-pairing', [\App\Http\Controllers\Admin\SoTradingController::class, 'cancelPairing'])->name('so-trading.cancel-bypass-pairing');

        Route::get('purchase-request/{type?}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'select'])->name('purchase-request');
        Route::get('purchase-request-global', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'select_global'])->name('purchase-request-global');
        Route::get('purchase-order/lpbs', [\App\Http\Controllers\Admin\PoTradingController::class, 'select_for_lpb'])->name('purchase-order-lpbs');
        Route::get('purchase', [\App\Http\Controllers\Admin\PurchaseController::class, 'select'])->name('purchase');
        Route::get('purchase-order/for-transport', [\App\Http\Controllers\Admin\PoTradingController::class, 'select_for_transport'])->name('purchase-order-for-transport');

        Route::get('sales-order/delivery-complete', [\App\Http\Controllers\Admin\SoTradingController::class, 'select_sale_order_delivery_complete'])->name('select-sale-order-delivery-complete');
        Route::get('sales-order/delivery-complete/{id?}', [\App\Http\Controllers\Admin\SoTradingController::class, 'show_sale_order_delivery_complete'])->name('show-sale-order-delivery-complete');
        Route::get('sales-order/so-ware-house/{id?}', [\App\Http\Controllers\Admin\SoTradingController::class, 'so_ware_house'])->name('sale-order-ware-house');
        Route::get('sales-order/item-receiving-report/{id?}', [\App\Http\Controllers\Admin\SoTradingController::class, 'get_lpb'])->name('sales-order.item-receiving-reports');
        Route::get('customer-detail/{id?}', [\App\Http\Controllers\Admin\CustomerController::class, 'customer_detail'])->name('customer-detail');
        Route::get('vendor-detail/{id?}', [\App\Http\Controllers\Admin\VendorController::class, 'vendor_detail'])->name('vendor-detail');
        Route::get('fund-submission', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'select'])->name('fund-submission');
        Route::get('currency-with-condition', [\App\Http\Controllers\Admin\CurrencyController::class, 'select_with_condition'])->name('currency-with-condition');

        Route::get('fund-submission-select-purchase', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'select_purchase'])->name('fund-submission.select-purchase');
        Route::get('fund-submission-select-purchase-down-payment', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'select_purchase_down_payment'])->name('fund-submission.select-purchase-down-payment');

        Route::get('item-receiving-report', [\App\Http\Controllers\Admin\ItemReceivingReportController::class, 'select'])->name('item-receiving-report');

        Route::get('invoice-return-do-select', [\App\Http\Controllers\Admin\InvoiceReturnController::class, 'do_select'])->name('invoice-return-do-select');

        Route::get('asset', [\App\Http\Controllers\Admin\AssetController::class, 'select'])->name('asset');
        Route::get('payroll-period', [\App\Http\Controllers\Admin\PayrollPeriodController::class, 'select'])->name('payroll-period');
        Route::get('invoice', [\App\Http\Controllers\Admin\InvoiceController::class, 'select'])->name('invoice');
        Route::get('receive-payment', [\App\Http\Controllers\Admin\ReceivePaymentController::class, 'select'])->name('receive-payment');
        Route::get('send-payment', [\App\Http\Controllers\Admin\SendPaymentController::class, 'select'])->name('send-payment');

        Route::get('salary-item', [\App\Http\Controllers\Admin\SalaryItemController::class, 'select'])->name('salary-item');
        Route::get('non-taxable-income', [\App\Http\Controllers\Admin\NonTaxableIncomeController::class, 'select'])->name('non-taxable-income');

        Route::get('cash-advance-payment', [\App\Http\Controllers\Admin\CashAdvancePaymentController::class, 'select'])->name('cash-advance-payment');
        Route::get('cash-advance-receive', [\App\Http\Controllers\Admin\CashAdvanceReceiveController::class, 'select'])->name('cash-advance-receive');

        Route::get('purchase-request-trading', [\App\Http\Controllers\Admin\PurchaseRequestTradingController::class, 'select'])->name('purchase-request-trading');

        Route::get('potp/for-do', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'select_potp_for_do'])->name('potp.for-do');
        Route::get('potp/get-so/{potp}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'get_so'])->name('potp.get-so');
        Route::get('delivery-order/item-receiving-report-select', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'item_receiving_report_select'])->name('delivery-order.item-receiving-report-select');

        Route::get('master-letter', [\App\Http\Controllers\Admin\MasterLetterController::class, 'select'])->name('master-letter');
        Route::get('reset-leave', [\App\Http\Controllers\Admin\ResetLeaveController::class, 'select'])->name('reset-leave');

        Route::get('invoice-down-payment', [\App\Http\Controllers\Admin\InvoiceDownPaymentController::class, 'select'])->name('invoice-down-payment');
        Route::post('invoice-down-payment/sales-order', [\App\Http\Controllers\Admin\InvoiceDownPaymentController::class, 'select_sales_order'])->name('invoice-down-payment.sales-order');

        Route::get('supplier-invoice/cash-advance', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'select_cash_advance'])->name('supplier-invoice.cash-advance');

        Route::get('purchase-down-payment-select-purchase', [\App\Http\Controllers\Admin\PurchaseDownPaymentController::class, 'select_purchase'])->name('purchase-down-payment.select-purchase');
        Route::get('purchase-down-payment-purchase-detail/{id?}', [\App\Http\Controllers\Admin\PurchaseDownPaymentController::class, 'purchase_detail'])->name('purchase-down-payment.purchase-detail');
    });
    // * select form =============================================================================================================================
});

Route::group([
    'as' => 'admin.',
    'middleware' => ['auth'],
    'namespace' => '\App\Http\Controllers\Admin',
], function () {
    Route::resource('stock-adjustment', StockOpnameController::class);
    Route::get('download-report', [DownloadController::class, 'index'])->name('download-report.index');
    Route::get('download-get-file/{id}', [DownloadController::class, 'getFile'])->name('download-report.get-file');
    Route::post('download-report/bulk-delete', [DownloadController::class, 'bulkDelete'])->name('download-report.bulk-delete');

    Route::get('invoice-down-payment/export/{id}', [\App\Http\Controllers\Admin\InvoiceDownPaymentController::class, 'export'])->name('invoice-down-payment.export.id');
    Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('index');
    Route::get('/get-data-dashboard/main', [\App\Http\Controllers\Admin\AdminController::class, 'get_data_dashboard'])->name('index.get-data-dashboard');
    Route::get('/get-data-dashboard/purchase', [\App\Http\Controllers\Admin\AdminController::class, 'get_purchase_dashboard_data'])->name('index.get-data-dashboard-purchase');
    Route::get('/get-data-dashboard/sales', [\App\Http\Controllers\Admin\AdminController::class, 'get_sales_order_dashboard'])->name('index.get-data-dashboard-sales');
    Route::get('/get-data-dashboard/warehouse', [\App\Http\Controllers\Admin\AdminController::class, 'get_warehouse_dashboard'])->name('index.get-data-dashboard-warehouse');
    Route::get('/get-data-dashboard/finance', [\App\Http\Controllers\Admin\AdminController::class, 'financeDashboard'])->name('index.get-data-dashboard-finance');
    Route::get('/get-data-dashboard/accounting', [\App\Http\Controllers\Admin\AdminController::class, 'accountingDashboard'])->name('index.get-data-dashboard-accounting');
    Route::get('/get-data-dashboard/invoice', [\App\Http\Controllers\Admin\AdminController::class, 'getInvoiceMoreThenDueDate'])->name('index.get-data-dashboard-invoice');
    Route::get('/get-data-dashboard/hr-legal', [\App\Http\Controllers\Admin\AdminController::class, 'getHumanResourceLegalDocument'])->name('index.get-data-dashboard-hr-legal');

    Route::get('/get-data-dashboard/trading', [\App\Http\Controllers\Admin\AdminController::class, 'get_data_dashboard_trading'])->name('index.get-data-dashboard-trading');
    Route::get('/get-data-dashboard/hrd', [\App\Http\Controllers\Admin\AdminController::class, 'get_data_dashboard_hrd'])->name('index.get-data-dashboard-hrd');
    Route::get('/get-data-dashboard/finance-invoice-due', [\App\Http\Controllers\Admin\AdminController::class, 'get_data_dashboard_finance_invoice_due'])->name('index.get-data-dashboard-finance-invoice-due');
    Route::get('/get-data-dashboard/finance-supplier-invoice-due', [\App\Http\Controllers\Admin\AdminController::class, 'get_data_dashboard_finance_supplier_invoice_due'])->name('index.get-data-dashboard-finance-supplier-invoice-due');

    // Route::get('downloads', [\App\Http\Controllers\Admin\DownloadController::class, 'index'])->name('download.index');
    // Route::get('downloads/data', [\App\Http\Controllers\Admin\DownloadController::class, 'indexDataTable'])->name('download.data');
    // Route::get('downloads/{id}/download', [\App\Http\Controllers\Admin\DownloadController::class, 'download'])->name('download.download');
    // Route::post('downloads/delete', [\App\Http\Controllers\Admin\DownloadController::class, 'destroy'])->name('download.destroy');

    // * export import ================================================================================================================================
    Route::get('vendor/export', [\App\Http\Controllers\Admin\VendorController::class, 'export'])->name('vendor.export');
    Route::get('customer/export', [\App\Http\Controllers\Admin\CustomerController::class, 'export'])->name('customer.export');
    Route::get('user/export', [\App\Http\Controllers\Admin\UserController::class, 'export'])->name('user.export');
    Route::get('purchase/export', [\App\Http\Controllers\Admin\PurchaseController::class, 'export'])->name('purchase.export');
    Route::get('purchase-order-transport/export', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'export'])->name('purchase-order-transport.export');

    Route::post('vendor/import', [\App\Http\Controllers\Admin\VendorController::class, 'import'])->name('vendor.import');
    Route::get('vendor/import-format', [\App\Http\Controllers\Admin\VendorController::class, 'import_format'])->name('vendor.import-format');
    Route::post('customer/import', [\App\Http\Controllers\Admin\CustomerController::class, 'import'])->name('customer.import');
    Route::get('customer/import-format', [\App\Http\Controllers\Admin\CustomerController::class, 'import_format'])->name('customer.import-format');
    Route::post('user/import', [\App\Http\Controllers\Admin\UserController::class, 'import'])->name('user.import');
    Route::get('user/import-format', [\App\Http\Controllers\Admin\UserController::class, 'import_format'])->name('user.import-format');
    Route::post('user/store-token', [\App\Http\Controllers\Admin\UserController::class, 'store_token'])->name('user.store-token');

    Route::post('additional-item/{id}', [QuotationItemController::class, 'destroy'])->name('additional-item.destroy');
    // * export import ================================================================================================================================

    // * resource ================================================================================================================================
    Route::post('master-user-assessment-find-by-id', [\App\Http\Controllers\Admin\MasterUserAssessmentController::class, 'findById'])->name('master-user-assessment-find-by-id');
    Route::post('master-gp-evaluation-find-by-id', [\App\Http\Controllers\Admin\MasterGpEvaluationController::class, 'findById'])->name('master-gp-evaluation-find-by-id');

    Route::post('user-assessment-find-by-id', [\App\Http\Controllers\Admin\UserAssessmentController::class, 'findById'])->name('user-assessment-find-by-id');
    Route::post('user-assessment/{id}/update-status', [\App\Http\Controllers\Admin\UserAssessmentController::class, 'update_status'])->name('user-assessment.update_status');

    Route::post('master-hrd-assessment/get-data-by-id', [\App\Http\Controllers\Admin\MasterHrdAssessmentController::class, 'getDataById'])->name('master-hrd-assessment.get-data-by-id');

    Route::post('hrd-assessment/{id}/update-status', [\App\Http\Controllers\Admin\HrdAssessmentController::class, 'update_status'])->name('hrd-assessment.update_status');

    Route::post('labor-transfer-form/{id}/update-status', [\App\Http\Controllers\Admin\LaborTransferFormController::class, 'update_status'])->name('labor-transfer-form.update_status');

    Route::post('gp-evaluation/{id}/update-status', [\App\Http\Controllers\Admin\GpEvaluationController::class, 'update_status'])->name('gp-evaluation.update_status');

    Route::get('price/customers/{id?}', [\App\Http\Controllers\Admin\PriceController::class, 'data_price_customers'])->name('price.customers');
    Route::post('price/create/{id}', [\App\Http\Controllers\Admin\PriceController::class, 'add_price_customer'])->name('price.customers.create');
    Route::delete('price/delete/{id}', [\App\Http\Controllers\Admin\PriceController::class, 'destroy_price_customer'])->name('price.customers.destroy');

    Route::get('price/detail/{id?}', [\App\Http\Controllers\Admin\PriceController::class, 'get_detail_price'])->name('price.detail');
    Route::get('coa-bank-internal', [\App\Http\Controllers\Admin\BankInternalController::class, 'coaBankInternal'])->name('coa-bank-internal.dashboard');
    Route::get('bank-internal/{id}/detail', [\App\Http\Controllers\Admin\BankInternalController::class, 'detail'])->name('bank-internal.detail');
    Route::post('bank-internal/is-no-rek-exists', [\App\Http\Controllers\Admin\BankInternalController::class, 'isNoRekExists'])->name('bank-internal-is-no-rek-exists');
    Route::get('vehicle/detail/{id?}/', [\App\Http\Controllers\Admin\VechicleFleetController::class, 'detail'])->name('vehicle-fleet.detail');
    Route::get('tax/detail/{id?}', [\App\Http\Controllers\Admin\TaxController::class, 'detail'])->name('tax.detail');
    Route::post('tax/detail', [\App\Http\Controllers\Admin\TaxController::class, 'detail_post'])->name('tax.detail-post');
    Route::get('currency/detail/{id?}', [\App\Http\Controllers\Admin\CurrencyController::class, 'select_detail'])->name('currency.detail');
    Route::get('sh-number/detail/{id?}', [\App\Http\Controllers\Admin\ShNumberController::class, 'select_detail'])->name('sh-number.detail');
    Route::get('price/detail/{id?}', [\App\Http\Controllers\Admin\PriceController::class, 'detail'])->name('price.detail');
    Route::get('customer/detail/{id?}', [\App\Http\Controllers\Admin\CustomerController::class, 'detail'])->name('customer.detail');
    Route::get('customer-find-vendor/{id}', [\App\Http\Controllers\Admin\CustomerController::class, 'find_vendor'])->name('customer.find-vendor');
    Route::get('coa/detail/{id?}', [\App\Http\Controllers\Admin\CoaController::class, 'detail'])->name('coa.detail');
    Route::get('branch/detail/{id?}', [\App\Http\Controllers\Admin\BranchController::class, 'detail'])->name('branch.detail');
    Route::get('division/detail/{id?}', [\App\Http\Controllers\Admin\DivisionController::class, 'detail'])->name('division.detail');
    Route::get('position/detail/{id?}', [\App\Http\Controllers\Admin\PositionController::class, 'detail'])->name('position.detail');

    Route::get('quotation-add-on-type/detail/{id?}', [\App\Http\Controllers\Admin\QuotationAddOnTypeController::class, 'detail'])->name('quotation-add-on-type.detail');

    Route::post('customer/{id}/update-coa', [\App\Http\Controllers\Admin\CustomerController::class, 'update_customer_coa'])->name('customer.update-coa');
    Route::post('vendor/{id}/update-coa', [\App\Http\Controllers\Admin\VendorController::class, 'update_vendor_coa'])->name('vendor.update-coa');

    Route::post('journal/{id}/update-status', [\App\Http\Controllers\Admin\JournalController::class, 'update_status'])->name('journal.update-status');


    Route::get('vendor/alamat/{id}', [\App\Http\Controllers\Admin\VendorController::class, 'vendor_alamat'])->name('vendor.alamat');
    Route::get('vendor/{id}/users', [\App\Http\Controllers\Admin\VendorController::class, 'vendor_users'])->name('vendor.users');
    Route::post('vendor/{id}/users', [\App\Http\Controllers\Admin\VendorController::class, 'store_user'])->name('vendor.users.store');
    Route::get('vendor/{vendor_id}/users/{user_id}/edit', [\App\Http\Controllers\Admin\VendorController::class, 'edit_user'])->name('vendor.edit-user');
    Route::put('vendor/{vendor_id}/users/{user_id}/', [\App\Http\Controllers\Admin\VendorController::class, 'update_user'])->name('vendor.users.update-user');
    Route::post('vendor/coa-with-type', [\App\Http\Controllers\Admin\VendorController::class, 'vendor_coa_with_type'])->name('vendor.coa-with-type');
    Route::get('vendor-find-customer/{id}', [\App\Http\Controllers\Admin\VendorController::class, 'find_customer'])->name('vendor.find-customer');

    Route::post('vendor/{vendor_id}/vendor-bank', [\App\Http\Controllers\Admin\VendorController::class, 'create_vendor_bank'])->name('vendor.create-vendor-bank');
    Route::put('vendor/{vendor_id}/vendor-bank/{bank_id}', [\App\Http\Controllers\Admin\VendorController::class, 'update_vendor_bank'])->name('vendor.update-vendor-bank');
    Route::delete('vendor/{vendor_id}/vendor-bank/{bank_id}', [\App\Http\Controllers\Admin\VendorController::class, 'destroy_vendor_bank'])->name('vendor.destroy-vendor-bank');

    Route::get('fleet/get-data-by-type/{type?}', [\App\Http\Controllers\Admin\FleetController::class, 'get_data_by_type'])->name('fleet.get-data-by-type');

    Route::prefix('')->group(function () {
        Route::prefix('')->group(function () {
            Route::get('item/export-excelp', [\App\Http\Controllers\Admin\ItemController::class, 'export_item_excel'])->name('item.export-item-excel');
            Route::get('sales-order/{id}/delivery-order', [\App\Http\Controllers\Admin\SoTradingController::class, 'delivery_order'])->name('sales-order.delivery-order');
            Route::get('sales-order/{id}/delivery-order/done', [\App\Http\Controllers\Admin\SoTradingController::class, 'delivery_order_done'])->name('sales-order.delivery-order.done');
            Route::get('sales-order/{id}/adjust-pairing', [\App\Http\Controllers\Admin\SoTradingController::class, 'adjust_pairing'])->name('sales-order.adjust-pairing');
            Route::get('sales-order/coa/{id?}', [\App\Http\Controllers\Admin\SoTradingController::class, 'sale_order_coas'])->name('sales-order.coas');
            Route::put('sales-order/{id}/nomor-po-external', [\App\Http\Controllers\Admin\SoTradingController::class, 'update_po_external'])->name('sales-order.update-nomor-po-external');
            Route::get('sales-order/detail-for-invoice/{id?}', [\App\Http\Controllers\Admin\SoTradingController::class, 'get_detail_data_for_invoice'])->name('sales-order.get-detail-for-invoice');
            Route::post('sales-order/{id}/adjust-pairing', [\App\Http\Controllers\Admin\SoTradingController::class, 'store_adjust_pairing'])->name('sales-order.adjust-pairing.post');

            Route::get('sales-order/report', [\App\Http\Controllers\Admin\SaleOrderTradingReportController::class, 'index'])->name('sale-order-trading-report.report');
            Route::post('sales-order/report/{type}', [\App\Http\Controllers\Admin\SaleOrderTradingReportController::class, 'show'])->name('sale-order-trading-report.report.show');

            Route::get('sales-order/detail-for-delivery/{id?}', [\App\Http\Controllers\Admin\SoTradingController::class, 'detail_for_delivery_order'])->name('sales-order.detail-for-delivery');

            Route::get('sales-order/export', [\App\Http\Controllers\Admin\SoTradingController::class, 'export'])->name('sales-order.export');
            Route::get('sales-order/data', [\App\Http\Controllers\Admin\SoTradingController::class, 'data'])->name('sales-order.data');
            Route::get('sales-order/data-dont-have-invoice-yet', [\App\Http\Controllers\Admin\SoTradingController::class, 'getSaleOrderTradingInvoice'])->name('sales-order.get-sale-order-trading-invoice');
            Route::get('sales-order/{id}/detail-edit', [\App\Http\Controllers\Admin\SoTradingController::class, 'detail_edit'])->name('sales-order.detail-edit');
            Route::post('/sales-order/{id}/update-status', [\App\Http\Controllers\Admin\SoTradingController::class, 'update_status'])->name('sales-order.update_status');
            Route::resource('sales-order', SoTradingController::class);
            Route::get('sales-order/history/{id}', [\App\Http\Controllers\Admin\SoTradingController::class, 'history'])->name('sales-order.history');
        });

        Route::prefix('')->group(function () {
            Route::get('sales-order-general/export', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'export'])->name('sales-order-general.export');

            Route::get('sales-order-general/report', [\App\Http\Controllers\Admin\SaleOrderGeneralReportController::class, 'index'])->name('sale-order-general.report');
            Route::post('sales-order-general/report/{type}', [\App\Http\Controllers\Admin\SaleOrderGeneralReportController::class, 'show'])->name('sale-order-general.report.show');

            Route::get('sales-order-general/data', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'data'])->name('sale-order-general.data');
            Route::get('sales-order-general/data-dont-have-invoice-yet', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'getSaleOrderInvoice'])->name('sales-order-general.get-sale-order-invoice');
            Route::get('sales-order-general/item-stock/{item_id?}', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'get_item_stocks'])->name('sales-order-general.item-stock');
            Route::get('sales-order-general/{id}/detail-edit', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'detail_edit'])->name('sale-order-general.detail-edit');
            Route::post('sales-order-general/{id}/update-status', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'update_status'])->name('sales-order-general.update-status');
            Route::post('sales-order-general/get-by-customer', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'get_by_customer'])->name('sales-order-general.get-by-customer');
            Route::get('sales-order-general/select-for-delivery', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'select_for_delivery_order'])->name('sales-order-general.select-for-delivery-order');
            Route::get('sales-order-general/detail-for-delivery/{id?}', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'detail_for_delivery_order'])->name('sales-order-general.detail-for-delivery-order');
            Route::get('sales-order-general/select-for-invoice-general', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'select_for_invoice_general'])->name('sales-order-general.select-for-invoice-general');
            Route::get('sales-order-general/check-date/{id?}', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'check_date_so'])->name('sales-order-general.check-date');

            Route::resource('sales-order-general', SaleOrderGeneralController::class);

            Route::get('sales-order-general/history/{id}', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'history'])->name('sales-order-general.history');
        });

        Route::resource('sales', SalesController::class)->only(['index']);
    });

    Route::post('project/{id}/update-status', [\App\Http\Controllers\Admin\ProjectController::class, 'update_status'])->name('project.update-status');

    Route::prefix('')->group(function () {
        Route::get('supplier-invoice/get-vendor', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'getVendor'])->name('supplier-invoice.get-vendor');
        Route::get('supplier-invoice/get-currency', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'getCurrency'])->name('supplier-invoice.get-currency');
        Route::post('supplier-invoice/vendor/top', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'vendor_with_top'])->name('supplier-invoice.vendor-top');
        Route::post('supplier-invoice/get-lpb', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'getLpb'])->name('supplier-invoice.get-lpb');
        Route::get('supplier-invoice/{vendor_id}/get-po', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'getPo'])->name('supplier-invoice.get-po');
        Route::post('supplier-invoice/is-reference-exists', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'isReferenceExists'])->name('supplier-invoice.is-reference-exists');
        Route::post('supplier-invoice/is-tax-reference-exists', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'isTaxReferenceExists'])->name('supplier-invoice.is-tax-reference-exists');
        Route::post('supplier-invoice/{id}/update-status', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'update_status'])->name('supplier-invoice.update-status');
        Route::get('supplier-invoice/print-receipt', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'print_receipt'])->name('supplier-invoice.print-receipt');
        Route::get('supplier-invoice/history/{id}', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'history'])->name('supplier-invoice.history');
        Route::post('supplier-invoice/{id}/update-tax', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'update_tax'])->name('supplier-invoice.update-tax');
        Route::post('supplier-invoice/lock/{id}', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'lock'])->name('supplier-invoice.lock');

        Route::get('supplier-invoice-general/get-currency', [\App\Http\Controllers\Admin\SupplierInvoiceGeneralController::class, 'getCurrency'])->name('supplier-invoice-general.get-currency');
        Route::get('supplier-invoice-general/get-vendor', [\App\Http\Controllers\Admin\SupplierInvoiceGeneralController::class, 'getVendor'])->name('supplier-invoice-general.get-vendor');
        Route::post('supplier-invoice-general/vendor/top', [\App\Http\Controllers\Admin\SupplierInvoiceGeneralController::class, 'vendor_with_top'])->name('supplier-invoice-general.vendor-top');
        Route::post('supplier-invoice-general/coa/code-name', [\App\Http\Controllers\Admin\SupplierInvoiceGeneralController::class, 'coa_with_code_name'])->name('supplier-invoice-general.coa.code-name');
        Route::post('supplier-invoice-general/{id}/update-status', [\App\Http\Controllers\Admin\SupplierInvoiceGeneralController::class, 'update_status'])->name('supplier-invoice-general.update-status');
        Route::post('supplier-invoice-general/vendor-coa-id', [\App\Http\Controllers\Admin\SupplierInvoiceGeneralController::class, 'vendor_coa'])->name('supplier-invoice-general.vendor-coa-id');

        Route::resource('supplier-invoice', SupplierInvoiceController::class);
        Route::resource('supplier-invoice-general', SupplierInvoiceGeneralController::class);

        Route::post('supplier-invoice-payment-information', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'payment_information'])->name('supplier-invoice.payment-information');
    });

    // datatable
    Route::post('incoming-payment-datatable', [\App\Http\Controllers\Admin\IncomingPaymentController::class, 'datatable']);
    Route::post('/incoming-payment/{id}/update-status', [\App\Http\Controllers\Admin\IncomingPaymentController::class, 'update_status'])->name('incoming-payment.update-status');

    Route::post('outgoing-payment-datatable', [\App\Http\Controllers\Admin\OutgoingPaymentController::class, 'datatable']);
    Route::post('/outgoing-payment/{id}/update-status', [\App\Http\Controllers\Admin\OutgoingPaymentController::class, 'update_status'])->name('outgoing-payment.update-status');
    Route::post('/outgoing-payment/general-form', [\App\Http\Controllers\Admin\OutgoingPaymentController::class, 'general_form'])->name('outgoing-payment.general-form');
    Route::post('/outgoing-payment/fund-submission', [\App\Http\Controllers\Admin\OutgoingPaymentController::class, 'fund_submission'])->name('outgoing-payment.fund-submission');

    Route::get('journal/data', [\App\Http\Controllers\Admin\JournalController::class, 'data'])->name('journal.data');
    Route::post('journal/get-data', [\App\Http\Controllers\Admin\JournalController::class, 'get_data'])->name('journal.get-data');

    Route::post('permission-letter-employee/{id}/update-status', [\App\Http\Controllers\Admin\PermissionLetterEmployeeController::class, 'update_status'])->name('permission-letter-employee.update-status');

    Route::post('/cash-advance-payment/fund-submission', [\App\Http\Controllers\Admin\CashAdvancePaymentController::class, 'fund_submission'])->name('cash-advance-payment.fund-submission');
    Route::post('cash-advance-payment-datatable', [\App\Http\Controllers\Admin\CashAdvancePaymentController::class, 'datatable']);
    Route::post('/cash-advance-payment/{id}/update-status', [\App\Http\Controllers\Admin\CashAdvancePaymentController::class, 'update_status'])->name('cash-advance-payment.update-status');
    Route::post('/cash-advance-payment/{id}/update-tax', [\App\Http\Controllers\Admin\CashAdvancePaymentController::class, 'update_tax'])->name('cash-advance-payment.update-tax');
    Route::post('/cash-advance-payment/detail', [\App\Http\Controllers\Admin\CashAdvancePaymentController::class, 'detail'])->name('cash-advance-payment.detail');

    Route::post('cash-advance-receive-datatable', [\App\Http\Controllers\Admin\CashAdvanceReceiveController::class, 'datatable']);
    Route::post('/cash-advance-receive/{id}/update-status', [\App\Http\Controllers\Admin\CashAdvanceReceiveController::class, 'update_status'])->name('cash-advance-receive.update-status');
    Route::post('/cash-advance-receive/customer-coa/{id}', [\App\Http\Controllers\Admin\CashAdvanceReceiveController::class, 'customer_coa'])->name('cash-advance-receive.customer-coa');
    Route::post('/cash-advance-receive/{id}/update-tax', [\App\Http\Controllers\Admin\CashAdvanceReceiveController::class, 'update_tax'])->name('cash-advance-receive.update-tax');

    Route::get('user-activity', [\App\Http\Controllers\Admin\UserActivityController::class, 'index'])->name('user-activity.index');
    Route::get('user-activity/activity-log', [\App\Http\Controllers\Admin\UserActivityController::class, 'activity_logs'])->name('user-activity.activity-log');
    Route::get('user-activity/status-log', [\App\Http\Controllers\Admin\UserActivityController::class, 'status_logs'])->name('user-activity.status-log');
    Route::get('user-activity/activity-log/{id}', [\App\Http\Controllers\Admin\UserActivityController::class, 'show_activity'])->name('user-activity.activity-log.show');
    Route::get('user-activity/status-log/{id}', [\App\Http\Controllers\Admin\UserActivityController::class, 'show_status'])->name('user-activity.status-log.show');

    Route::get('position/{id}/employee', [\App\Http\Controllers\Admin\PositionController::class, 'getPositionWithEmployee'])->name('position.employee');
    Route::get('division/{id}/employee', [\App\Http\Controllers\Admin\DivisionController::class, 'getEmployeeDivision'])->name('division.employee');
    Route::get('business-field/{id}/vendor', [\App\Http\Controllers\Admin\BusinessFieldController::class, 'getBusinessFieldVendor'])->name('business-field.vendor');

    Route::post('labor-demand/{id}/update-status', [\App\Http\Controllers\Admin\LaborDemandController::class, 'update_status'])->name('labor-demand.update-status');
    Route::post('labor-demand/{id}/approve/{detail_id}', [\App\Http\Controllers\Admin\LaborDemandController::class, 'approve_labor_demand_detail'])->name('labor-demand.update-status.approve-labor-demand-detail');
    Route::post('labor-demand/{id}/reject/{detail_id}', [\App\Http\Controllers\Admin\LaborDemandController::class, 'reject_labor_demand_detail'])->name('labor-demand.update-status.reject-labor-demand-detail');
    Route::post('labor-demand/{id}/revert/{detail_id}', [\App\Http\Controllers\Admin\LaborDemandController::class, 'revert_labor_demand_detail'])->name('labor-demand.update-status.revert-labor-demand-detail');
    Route::post('labor-demand/{id}/close/{detail_id}', [\App\Http\Controllers\Admin\LaborDemandController::class, 'close_labor_demand_detail'])->name('labor-demand.update-status.close-labor-demand-detail');
    Route::get('labor-demand/download', [\App\Http\Controllers\Admin\LaborDemandController::class, 'download'])->name('labor-demand.download');
    Route::get('labor-demand/export', [\App\Http\Controllers\Admin\LaborDemandController::class, 'export'])->name('labor-demand.export');


    Route::get('labor-application/download', [\App\Http\Controllers\Admin\LaborApplicationController::class, 'download'])->name('labor-application.download');
    Route::get('labor-application/get-labor-demand', [\App\Http\Controllers\Admin\LaborDemandController::class, 'getLaborDemandForLaborApplication'])->name('labor-application.get-labor-demand');
    Route::get('labor-application/get-labor-demand/{id?}', [\App\Http\Controllers\Admin\LaborDemandController::class, 'getLaborDemandDetailForLaborApplication'])->name('labor-application.get-labor-demand.detail');
    Route::post("labor-application/{id}/update-status", [\App\Http\Controllers\Admin\LaborApplicationController::class, 'update_status'])->name("labor-application.update-status");
    Route::resource('labor-application', LaborApplicationController::class);

    Route::post('labor-application-find-by-id', [\App\Http\Controllers\Admin\LaborApplicationController::class, 'findById'])->name('labor-application-find-by-id');
    Route::get('labor-application/download', [\App\Http\Controllers\Admin\LaborApplicationController::class, 'download'])->name('labor-application.download');

    Route::get('labor-application/export', [\App\Http\Controllers\Admin\LaborApplicationController::class, 'export'])->name('labor-application.export');


    Route::get('offering-letter/export/{id}', [\App\Http\Controllers\Admin\OfferingLetterController::class, 'export'])->name('offering-letter.export');

    Route::get('user-assessment/export', [\App\Http\Controllers\Admin\UserAssessmentController::class, 'export'])->name('user-assessment.export');


    Route::get('hrd-assessment/download', [\App\Http\Controllers\Admin\HrdAssessmentController::class, 'download'])->name('hrd-assessment.download');
    Route::get('hrd-assessment/export', [\App\Http\Controllers\Admin\HrdAssessmentController::class, 'export'])->name('hrd-assessment.export');

    Route::get('master-letter-pdf/{id}', [\App\Http\Controllers\Admin\MasterLetterController::class, 'pdf_preview']);


    Route::prefix('')->group(function () {
        Route::get('employee/export', [\App\Http\Controllers\Admin\EmployeeController::class, 'export'])->name('employee.export');
        Route::post('employee/import', [\App\Http\Controllers\Admin\EmployeeController::class, 'import'])->name('employee.import');
        Route::get('employee/import-format', [\App\Http\Controllers\Admin\EmployeeController::class, 'import_format'])->name('employee.import-format');


        Route::get("employee/detail/{id?}", [\App\Http\Controllers\Admin\EmployeeController::class, 'detail'])->name('employee.detail');

        Route::get('employee/create/step/2/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'createStep2'])->name('employee.create.step2');
        Route::post('employee/create/step/2/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'storeStep2'])->name('employee.store.step2');

        Route::get('employee/create/step/3/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'createStep3'])->name('employee.create.step3');
        Route::post('employee/create/step/3/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'storeStep3'])->name('employee.store.step3');

        Route::get('employee/create/step/4/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'createStep4'])->name('employee.create.step4');
        Route::post('employee/create/step/4/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'storeStep4'])->name('employee.store.step4');

        Route::get('employee/create/step/5/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'createStep5'])->name('employee.create.step5');
        Route::post('employee/create/step/5/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'storeStep5'])->name('employee.store.step5');

        Route::get('employee/create/step/6/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'createStep6'])->name('employee.create.step6');
        Route::post('employee/create/step/6/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'storeStep6'])->name('employee.store.step6');

        Route::get('employee/create/step/7/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'createStep7'])->name('employee.create.step7');
        Route::post('employee/create/step/7/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'storeStep7'])->name('employee.store.step7');

        Route::get('employee/edit/step/2/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'editStep2'])->name('employee.edit.step2');
        Route::put('employee/edit/step/2/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'updateStep2'])->name('employee.update.step2');

        Route::get('employee/edit/step/3/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'editStep3'])->name('employee.edit.step3');
        Route::put('employee/edit/step/3/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'updateStep3'])->name('employee.update.step3');

        Route::get('employee/edit/step/4/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'editStep4'])->name('employee.edit.step4');
        Route::put('employee/edit/step/4/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'updateStep4'])->name('employee.update.step4');

        Route::get('employee/edit/step/5/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'editStep5'])->name('employee.edit.step5');
        Route::put('employee/edit/step/5/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'updateStep5'])->name('employee.update.step5');

        Route::get('employee/edit/step/6/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'editStep6'])->name('employee.edit.step6');
        Route::put('employee/edit/step/6/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'updateStep6'])->name('employee.update.step6');

        Route::get('employee/edit/step/7/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'editStep7'])->name('employee.edit.step7');
        Route::put('employee/edit/step/7/{employee_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'updateStep7'])->name('employee.update.step7');

        Route::resource('employee', EmployeeController::class);
    });

    Route::resource('tax-trading', TaxTradingController::class)->only(['index', 'update']);

    Route::prefix('')->group(function () {
        Route::get('purchase-request-data/{type?}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'data'])->name('purchase-request.data');
        Route::get('purchase-request/get-warehouse/{item_id?}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'getWarehouse'])->name('purchase-request.get-warehouse');
        Route::get('purchase-request/get-stock/{item_id?}/{warehouse_id?}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'getStock'])->name('purchase-request.get-stock');
        Route::get('purchase-request/detail/{id?}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'get_detail_purchase_request'])->name('purchase-request.detail');
        Route::get('purchase-request/get-warehouse/{item_id?}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'getWarehouse'])->name('purchase-request.get-warehouse');
        Route::get('purchase-request/get-stock/{item_id?}/{warehouse_id?}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'getStock'])->name('purchase-request.get-stock');
        Route::get('purchase-request/data-type/{type?}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'data_per_types'])->name('purchase-request.data-per-types');
        Route::post('purchase-request/select-for-stock-usage', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'select_purchase_request_stock_usage'])->name('purchase-request.select-for-stock-usage');
        Route::get('purchase-request/history/{id}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'history'])->name('purchase-request.history');

        Route::post('purchase-request/{id}/update-status', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'update_status'])->name('purchase-request.update-status');
        Route::post('purchase-request/{id}/reject-purchase-request-item/{purchase_request_detail_id}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'reject_purchase_request_detail'])->name('purchase-request.reject-purchase-request-item');
        Route::post('purchase-request/{id}/revert-purchase-request-item/{purchase_request_detail_id}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'revert_purchase_request_detail'])->name('purchase-request.revert-purchase-request-item');
        Route::post('purchase-request/{id}/approve-purchase-request-item/{purchase_request_detail_id}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'approve_purchase_request_detail'])->name('purchase-request.approve-purchase-request-item');
        Route::post('purchase-request/{id}/update-status', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'update_status'])->name('purchase-request.update-status');
        Route::post('purchase-request/{id}/reject-purchase-request-item/{purchase_request_detail_id}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'reject_purchase_request_detail'])->name('purchase-request.reject-purchase-request-item');
        Route::post('purchase-request/{id}/revert-purchase-request-item/{purchase_request_detail_id}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'revert_purchase_request_detail'])->name('purchase-request.revert-purchase-request-item');
        Route::post('purchase-request/{id}/approve-purchase-request-item/{purchase_request_detail_id}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'approve_purchase_request_detail'])->name('purchase-request.approve-purchase-request-item');
        Route::post('purchase-request/{id}/lock-stock', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'lock_stock'])->name('purchase-request.lock-stock');
        Route::delete('purchase-request/{id}/unlock-stock', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'unlock_stock'])->name('purchase-request.unlock-stock');

        Route::resource('purchase-request', PurchaseRequestController::class);

        Route::get('purchase-request-report', [\App\Http\Controllers\Admin\PurchaseRequestReportController::class, 'index'])->name('purchase-request-report.index');
        Route::post('purchase-request-report/{type}', [\App\Http\Controllers\Admin\PurchaseRequestReportController::class, 'show'])->name('purchase-request-report.show');
    });

    Route::prefix('')->group(function () {
        Route::resource('purchase-request-trading', PurchaseRequestTradingController::class);
        Route::post('purchase-request-trading/{id}/update-status', [\App\Http\Controllers\Admin\PurchaseRequestTradingController::class, 'update_status'])->name('purchase-request-trading.update-status');
    });

    Route::prefix('')->group(function () {
        Route::resource('purchase', PurchaseController::class)->only('index');

        Route::prefix('')->group(function () {
            Route::get('purchase-order-transport-lpbs', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'select_for_lpb'])->name('purchase-order-transport-lpbs');
            Route::get('purchase-order-transport/report', [\App\Http\Controllers\Admin\PurchaseOrderTransportReportController::class, 'index'])->name('purchase-order-transport.report');
            Route::post('purchase-order-transport/report/{type}', [\App\Http\Controllers\Admin\PurchaseOrderTransportReportController::class, 'show'])->name('purchase-order-transport.report.show');

            Route::get('purchase-order-transport/data', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'data'])->name('purchase-order-transport.data');
            Route::get('purchase-order-transport/coas/{id?}', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'purchase_coa'])->name('purchase-order-transport.coa');
            Route::get('purchase-order-transport/detail-lpb/{id?}', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'detail_lpb'])->name('purchase-order-transport.detail-lpb');
            Route::post('purchase-order-transport/{id}/update-status', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'update_status'])->name('purchase-order-transport.update-status');
            Route::get('purchase-order-transport/export', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'export'])->name('purchase-order-transport.export');
            Route::post('purchase-order-transport/check-stock', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'check_stock'])->name('purchase-order-transport.check-stock');
            Route::get('purchase-order-transport/select-delivery-order', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'deliveryOrderSelect'])->name('purchase-order-transport.select-delivery-order');
            Route::get('purchase-order-transport/select-delivery-order/{id?}', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'deliveryOrderCheckStock'])->name('purchase-order-transport.select-delivery-order.show');
            Route::get('purchase-order-transport/{id}/data-for-edit', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'getDataForEdit'])->name('purchase-order-transport.data-for-edit');
            Route::get('purchase-order-transport/{id}/delivery-orders', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'dataTableDeliveryOrders'])->name('purchase-order-transport.delivery-orders');

            Route::resource('purchase-order-transport', PurchaseTransportController::class);

            Route::get('purchase-order-transport/history/{id}', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'history'])->name('purchase-order-transport.history');
        });

        Route::prefix('')->group(function () {
            Route::get('purchase-order-report', [\App\Http\Controllers\Admin\PurchaseOrderTradingReportController::class, 'index'])->name('purchase-order-trading.report');
            Route::post('purchase-order-report/{type}', [\App\Http\Controllers\Admin\PurchaseOrderTradingReportController::class, 'show'])->name('purchase-order-trading.report.show');

            Route::get('purchase-order/data', [\App\Http\Controllers\Admin\PoTradingController::class, 'data'])->name('purchase-order.data');
            Route::get('purchase-order/{id}/detail-edit', [\App\Http\Controllers\Admin\PoTradingController::class, 'detail_edit'])->name('purchase-order.detail-edit');
            Route::get('purchase-order/detail/{id?}/', [\App\Http\Controllers\Admin\PoTradingController::class, 'detail'])->name('purchase-order.detail');
            Route::get('purchase-order/coa/{id?}', [\App\Http\Controllers\Admin\PoTradingController::class, 'po_coas'])->name('purchase-order.coa');
            Route::post('/purchase-order/{id}/update-status', [\App\Http\Controllers\Admin\PoTradingController::class, 'update_status'])->name('purchase-order.update_status');
            Route::put('/purchase-order/{id}/update-sale-confirmation', [\App\Http\Controllers\Admin\PoTradingController::class, 'update_sale_confirmation'])->name('purchase-order.update_sale_confirmation');
            Route::get('purchase-order/export', [\App\Http\Controllers\Admin\PoTradingController::class, 'export'])->name('purchase-order.export');
            Route::get('po-trading/sh-details/{id?}/', [\App\Http\Controllers\Admin\PoTradingController::class, 'po_sh'])->name('po-sh.details');

            Route::resource('purchase-order', PoTradingController::class);

            Route::get('purchase-order/history/{id}', [\App\Http\Controllers\Admin\PoTradingController::class, 'history'])->name('purchase-order.history');
        });

        Route::prefix('')->group(function () {
            Route::get('purchase-order-general/report', [\App\Http\Controllers\Admin\PurchaseOrderGeneralReportController::class, 'index'])->name('purchase-order-general.report');
            Route::post('purchase-order-general/report/{type}', [\App\Http\Controllers\Admin\PurchaseOrderGeneralReportController::class, 'show'])->name('purchase-order-general.report.show');

            Route::get("purchase-order-general/select-sales-order", [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'selectForPurchaseOrderGeneral'])->name("purchase-order-general.select-sales-order");
            Route::get("purchase-order-general/select-sales-order/{id?}", [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'getDetailForPurchaseOrderGeneral'])->name("purchase-order-general.select-sales-order.detail");
            Route::get("purchase-order-general/select-for-receiving", [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'select_api_for_item_receiving'])->name("purchase-order-general.select-for-receiving");
            Route::get("purchase-order-general/detail-for-receiving/{id?}", [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'detail_api_for_item_receiving'])->name("purchase-order-general.detail-for-receiving");

            Route::post('purchase-order-general-so-outstanding-data', [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'so_outstanding_data'])->name('purchase-order-general.so-outstanding-data');
            Route::post('purchase-order-general/get-selected-sale-order-general', [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'get_selected_sale_order_general'])->name('purchase-order-general.get-selected-sale-order-general');

            Route::post('purchase-order-general-pr-outstanding-data', [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'pr_outstanding_data'])->name('purchase-order-general.pr-outstanding-data');
            Route::post('purchase-order-general/get-selected-purchase-request', [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'get_selected_purchase_request'])->name('purchase-order-general.get-selected-purchase-request');

            Route::post('purchase-order-general/{id}/data-for-edit', [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'detail_api_for_edit'])->name('purchase-order-general.data-for-edit');

            Route::post('purchase-order-general/{id}/update-status', [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'update_status'])->name('purchase-order-general.update-status');

            Route::resource('purchase-order-general', PurchaseOrderGeneralController::class);

            Route::get('purchase-order-general/history/{id}', [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'history'])->name('purchase-order-general.history');
        });

        Route::prefix('')->group(function () {
            Route::get('purchase-order-service/report', [\App\Http\Controllers\Admin\PurchaseOrderServiceReportController::class, 'index'])->name('purchase-order-service.report');
            Route::post('purchase-order-service/report/{type}', [\App\Http\Controllers\Admin\PurchaseOrderServiceReportController::class, 'show'])->name('purchase-order-service.report.show');

            Route::get("purchase-order-service/select-for-receiving", [\App\Http\Controllers\Admin\PurchaseOrderServiceController::class, 'select_api_for_item_receiving'])->name("purchase-order-service.select-for-receiving");
            Route::get("purchase-order-service/detail-for-receiving/{id?}", [\App\Http\Controllers\Admin\PurchaseOrderServiceController::class, 'detail_api_for_item_receiving'])->name("purchase-order-service.detail-for-receiving");

            Route::post('purchase-order-service/{id}/data-for-edit', [\App\Http\Controllers\Admin\PurchaseOrderServiceController::class, 'detail_api_for_edit'])->name('purchase-order-service.data-for-edit');

            Route::post('purchase-order-service/{id}/update-status', [\App\Http\Controllers\Admin\PurchaseOrderServiceController::class, 'update_status'])->name('purchase-order-service.update-status');
            Route::post('purchase-order-service/{id}/approve/{purchase_order_service_detail_id}/{purchase_order_service_item_id}', [\App\Http\Controllers\Admin\PurchaseOrderServiceController::class, 'approve_detail_item_status'])->name('purchase-order-service.update-status.approve-purchase-order-service-detail-item');
            Route::post('purchase-order-service/{id}/reject/{purchase_order_service_detail_id}/{purchase_order_service_item_id}', [\App\Http\Controllers\Admin\PurchaseOrderServiceController::class, 'reject_detail_item_status'])->name('purchase-order-service.update-status.reject-purchase-order-service-detail-item');
            Route::post('purchase-order-service/{id}/revert/{purchase_order_service_detail_id}/{purchase_order_service_item_id}', [\App\Http\Controllers\Admin\PurchaseOrderServiceController::class, 'revert_detail_item_status'])->name('purchase-order-service.update-status.revert-purchase-order-service-detail-item');

            Route::resource('purchase-order-service', PurchaseOrderServiceController::class);

            Route::get('purchase-order-service/history/{id}', [\App\Http\Controllers\Admin\PurchaseOrderServiceController::class, 'history'])->name('purchase-order-service.history');
        });

        Route::resource('purchase-down-payment', PurchaseDownPaymentController::class);
        Route::post('purchase-down-payment/{id}/update-status', [\App\Http\Controllers\Admin\PurchaseDownPaymentController::class, 'update_status'])->name('purchase-down-payment.update-status');
        Route::get('purchase-down-payment/history/{id}', [\App\Http\Controllers\Admin\PurchaseDownPaymentController::class, 'history'])->name('purchase-down-payment.history');
    });

    Route::prefix('')->group(function () {
        Route::get('delivery', [\App\Http\Controllers\Admin\DeliveryController::class, 'index'])->name('delivery.index');

        Route::prefix('')->group(function () {
            Route::get('delivery-order/export', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'export'])->name('delivery-order.export');
            Route::get('delivery-order/data', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'data'])->name('delivery-order.delivery-orders');
            Route::get('delivery-order/list-received', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'list_delivery_order_where_status_received_is_true'])->name("delivery-order.list-received");

            Route::post('delivery-order/{id}/update-status', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'update_status'])->name('delivery-order.update_status');
            Route::post('delivery-order/{id}/set-done', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'set_delivery_done'])->name('delivery-order.set-done');

            Route::get('delivery-order/{id}/delivery-order', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'list_delivery_order'])->name('delivery-order.list-delivery-order');
            Route::get('delivery-order/{id}/quantity', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'data_delivery_by_quantity'])->name('delivery-order.by-quantity');
            Route::post('delivery-order/{id}/approve-request-print/all', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'approve_all_request_print'])->name('delivery-order.approve-print-request.all');
            Route::post('delivery-order/{id}/approve-submitted/all', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'approve_all_submitted'])->name('delivery-order.approve-submitted.all');
            Route::post('delivery-order/{id}/approve-submitted/{delivery_order_id}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'approve_status_detail'])->name('delivery-order.approve-status-detail');
            Route::post('delivery-order/{id}/reject-submitted/{delivery_order_id}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'reject_status_detail'])->name('delivery-order.reject-status-detail');
            Route::get('delivery-order/{sale_order_id}/delivery-order/{delivery_order_id}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'show_delivery_order'])->name('delivery-order.list-delivery-order.show');
            Route::get('delivery-order/{sale_order_id}/delivery-order/{delivery_order_id}/edit', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'edit_delivery_order'])->name('delivery-order.list-delivery-order.edit');
            Route::put('delivery-order/{sale_order_id}/delivery-order/{delivery_order_id}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'update_delivery_order'])->name('delivery-order.list-delivery-order.update');
            Route::post('delivery-order/{sale_order_id}/approve-request-print/{id}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'update_request_print'])->name('delivery-order.approve-print-request');
            Route::post('delivery-order/{sale_order_id}/delivery-order/{delivery_order_id}/check-stock-in-warehouse', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'checkWarehouseStock'])->name('delivery-order.check-stock');
            Route::get('delivery-order/check-stock-delivery/{delivery_order_id?}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'checkDeliveryStock'])->name('delivery-order.check-stock-delivery');

            Route::get('delivery-order/export', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'export'])->name('delivery-order.export');

            // Route::put('delivery-order/{id}/update-status', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'update_status'])->name('delivery-order.update_status');
            Route::post('delivery-order/{id}/set-done', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'set_delivery_done'])->name('delivery-order.set-done');

            Route::get('delivery-order/{id}/delivery-order', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'list_delivery_order'])->name('delivery-order.list-delivery-order');
            Route::get('delivery-order/{id}/quantity', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'data_delivery_by_quantity'])->name('delivery-order.by-quantity');
            Route::post('delivery-order/{id}/approve-request-print/all', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'approve_all_request_print'])->name('delivery-order.approve-print-request.all');
            Route::post('delivery-order/{id}/approve-submitted/all', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'approve_all_submitted'])->name('delivery-order.approve-submitted.all');
            Route::post('delivery-order/{id}/approve-submitted/{delivery_order_id}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'approve_status_detail'])->name('delivery-order.approve-status-detail');
            Route::post('delivery-order/{id}/reject-submitted/{delivery_order_id}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'reject_status_detail'])->name('delivery-order.reject-status-detail');
            Route::get('delivery-order/{sale_order_id}/delivery-order/{delivery_order_id}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'show_delivery_order'])->name('delivery-order.list-delivery-order.show');
            Route::get('delivery-order/{sale_order_id}/delivery-order/{delivery_order_id}/edit', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'edit_delivery_order'])->name('delivery-order.list-delivery-order.edit');
            Route::put('delivery-order/{sale_order_id}/delivery-order/{delivery_order_id}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'update_delivery_order'])->name('delivery-order.list-delivery-order.update');
            Route::post('delivery-order/{sale_order_id}/approve-request-print/{id}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'update_request_print'])->name('delivery-order.approve-print-request');
            Route::post('delivery-order/{sale_order_id}/delivery-order/{delivery_order_id}/check-stock-in-warehouse', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'checkWarehouseStock'])->name('delivery-order.check-stock');

            Route::resource('delivery-order', DeliveryOrderController::class);

            Route::get('delivery-order/history/{id}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'history'])->name('delivery-order.history');
        });

        Route::prefix('')->group(function () {
            Route::get('delivery-order-general/export', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'export'])->name('delivery-order-general.export');
            Route::get('delivery-order-general/check-stock/{warehouse_id?}/{id?}', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'getStockInAWarehouse'])->name('delivery-order-general.check-stock');
            Route::get('delivery-order-general/select-for-invoice-general', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'selectForInvoiceGeneral'])->name('delivery-order-general.select-for-invoice-general');
            Route::get('delivery-order-general/select-for-invoice-general/{id?}', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'detailForInvoiceGeneralDetail'])->name('delivery-order-general.detail-for-invoice-general-detail');

            Route::get('delivery-order-general/create/check-stock', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'getStockInAWarehouseWhileCreate'])->name('delivery-order-general.create.check-stock');

            Route::post('delivery-order-general/{id}/update-status', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'update_status'])->name('delivery-order-general.update-status');


            Route::get('delivery-order-general/export', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'export'])->name('delivery-order-general.export');
            Route::get('delivery-order-general/check-stock/{warehouse_id?}/{id?}', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'getStockInAWarehouse'])->name('delivery-order-general.check-stock');
            Route::post('delivery-order-general/get-by-customer-so', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'get_by_customer_so'])->name('delivery-order-general.get-by-customer-so');
            Route::get('delivery-order-general/select-for-invoice-general', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'selectForInvoiceGeneral'])->name('delivery-order-general.select-for-invoice-general');
            Route::get('delivery-order-general/select-for-invoice-general/{id?}', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'detailForInvoiceGeneralDetail'])->name('delivery-order-general.detail-for-invoice-general-detail');
            Route::post('delivery-order-general/{id}/update-status', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'update_status'])->name('delivery-order-general.update-status');
            Route::put('delivery-order-general/{id}/close', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'close'])->name('delivery-order-general.close');
            Route::resource('delivery-order-general', DeliveryOrderGeneralController::class);

            Route::get('delivery-order-general/history/{id}', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'history'])->name('delivery-order-general.history');
        });
    });

    Route::prefix('')->group(function () {
        Route::post('/invoice-trading/{id}/update-status', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'update_status'])->name('invoice-trading.update-status');
        Route::get('/invoice-trading/export', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'export'])->name('invoice-trading.export');
        Route::post('invoice-trading/lock/{id}', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'lock'])->name('invoice-trading.lock');

        Route::get('/invoice-trading-generate/{id}', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'generate'])->name('invoice-trading.generate');
        Route::post('/invoice-trading-new-store/{id}', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'store_generate'])->name('invoice-trading.generate.store');
        Route::get('invoice-trading/{id}/list-delivery-order', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'list_delivery_order'])->name('invoice-trading.list-delivery-order');

        Route::get('/invoice-general/export', [\App\Http\Controllers\Admin\InvoiceGeneralController::class, 'export'])->name('invoice-general.export');
        Route::post('invoice-general/get-quantity', [\App\Http\Controllers\Admin\InvoiceGeneralController::class, 'get_quantity'])->name('invoice-general.get-quantity');
        Route::post('invoice-general/{id}/update-status', [\App\Http\Controllers\Admin\InvoiceGeneralController::class, 'update_status'])->name('invoice-general.update-status');
        Route::post('invoice-general/lock/{id}', [\App\Http\Controllers\Admin\InvoiceGeneralController::class, 'lock'])->name('invoice-general.lock');

        Route::get('invoice-return/{id}/get-do-detail', [\App\Http\Controllers\Admin\InvoiceReturnController::class, 'get_do_detail'])->name('purchase-return.get-do-detail');
        Route::post('/invoice-return/{id}/update-status', [\App\Http\Controllers\Admin\InvoiceReturnController::class, 'update_status'])->name('invoice-return.update-status');
        //check unique tax number
        Route::post('/invoice-return/check-unique-tax-number', [\App\Http\Controllers\Admin\InvoiceReturnController::class, 'check_unique_tax_number'])->name('invoice-return.check-unique-tax-number');

        Route::resource('invoice', InvoiceController::class)->only('index');
        Route::resource('invoice-trading', InvoiceTradingController::class);
        Route::resource('invoice-general', InvoiceGeneralController::class);
        Route::post('invoice-payment-information', [\App\Http\Controllers\Admin\InvoiceController::class, 'payment_information'])->name('invoice.payment-information');

        Route::get('invoice-trading/history/{id}', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'history'])->name('invoice-trading.history');
        Route::get('invoice-general/history/{id}', [\App\Http\Controllers\Admin\InvoiceGeneralController::class, 'history'])->name('invoice-general.history');
        Route::post('/invoice-trading/{id}/update-reference', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'update_reference'])->name('invoice-trading.update-reference');
        Route::post('/invoice-general/{id}/update-reference', [\App\Http\Controllers\Admin\InvoiceGeneralController::class, 'update_reference'])->name('invoice-general.update-reference');

        Route::resource('invoice-down-payment', InvoiceDownPaymentController::class);
        Route::post('invoice-down-payment/{id}/update-status', [\App\Http\Controllers\Admin\InvoiceDownPaymentController::class, 'update_status'])->name('invoice-down-payment.update-status');
        Route::post('/invoice-down-payment/{id}/update-tax', [\App\Http\Controllers\Admin\InvoiceDownPaymentController::class, 'update_tax'])->name('invoice-down-payment.update-tax');
    });

    Route::prefix('')->group(function () {
        Route::get('coa-warning', [\App\Http\Controllers\Admin\CoaWarningController::class, 'index'])->name('coa-warning.index');
        Route::post('coa-warning', [\App\Http\Controllers\Admin\CoaWarningController::class, 'getListOfWarningCoa'])->name('coa-warning.get-list-of-data');

        Route::get('item-category-get-item-type-coa/{id}', [\App\Http\Controllers\Admin\ItemCategoryController::class, 'get_item_type_coa']);

        Route::get('coa/import', [\App\Http\Controllers\Admin\CoaController::class, 'import_format'])->name('coa.import-format');
        Route::post('coa/import', [\App\Http\Controllers\Admin\CoaController::class, 'import'])->name('coa.import');

        Route::get('coa/export', [\App\Http\Controllers\Admin\CoaController::class, 'export'])->name('coa.export');

        Route::get('coa/tree-view', [\App\Http\Controllers\Admin\CoaController::class, 'tree_view_api'])->name('coa.tree-view');
        Route::get('coa/coa-parent', [\App\Http\Controllers\Admin\CoaController::class, 'select_coa_parents'])->name('coa.coa-parent');

        Route::get('default-coa', [\App\Http\Controllers\Admin\DefaultCoaController::class, 'index'])->name('default-coa.index');
        Route::get('default-coa/edit', [\App\Http\Controllers\Admin\DefaultCoaController::class, 'edit'])->name('default-coa.edit');
        Route::put('default-coa', [\App\Http\Controllers\Admin\DefaultCoaController::class, 'update'])->name('default-coa.update');

        Route::get('coa/beginning-balance', [\App\Http\Controllers\Admin\CoaImportBeginningBalanceController::class, 'index'])->name('coa.coa-beginning.index');
        Route::get('coa/beginning-balance/import-format', [\App\Http\Controllers\Admin\CoaImportBeginningBalanceController::class, 'importFormat'])->name('coa.coa-beginning.import-format');
        Route::post('coa/beginning-balance/import', [\App\Http\Controllers\Admin\CoaImportBeginningBalanceController::class, 'import'])->name('coa.coa-beginning.import');
        Route::post('coa/beginning-balance/store', [\App\Http\Controllers\Admin\CoaImportBeginningBalanceController::class, 'store'])->name('coa.coa-beginning.store');


        Route::resource('coa', CoaController::class);
        Route::get('coa-select-for-bank-internal', [\App\Http\Controllers\Admin\CoaController::class, 'select_for_bank_internal'])->name('coa.select-for-bank-internal');
    });

    Route::prefix('')->group(function () {
        Route::post('asset-datatable', [\App\Http\Controllers\Admin\AssetController::class, 'datatable']);
        Route::get('asset/asset-document/{id?}', [\App\Http\Controllers\Admin\AssetController::class, 'asset_document'])->name('asset.asset-document');

        Route::get('asset/import', [\App\Http\Controllers\Admin\AssetController::class, 'import'])->name('asset.import');
        Route::get('asset/import-format', [\App\Http\Controllers\Admin\AssetController::class, 'importFormat'])->name('asset.import-forma');
        Route::post('asset/import', [\App\Http\Controllers\Admin\AssetController::class, 'processImport'])->name('asset.process-import');
        Route::post('asset/import/store', [\App\Http\Controllers\Admin\AssetController::class, 'storeImport'])->name('asset.store-import');

        Route::resource('asset', AssetController::class);
        Route::resource('asset-document', AssetDocumentController::class);
        Route::post('asset/update-status/{asset}', [\App\Http\Controllers\Admin\AssetController::class, 'update_status'])->name('asset.update-status');
        Route::post('asset-document/asset', [\App\Http\Controllers\Admin\AssetDocumentController::class, 'asset'])->name('asset-document.asset');

        Route::get('lease/import', [\App\Http\Controllers\Admin\LeaseController::class, 'import'])->name('lease.import');
        Route::get('lease/import-format', [\App\Http\Controllers\Admin\LeaseController::class, 'importFormat'])->name('lease.import-forma');
        Route::post('lease/import', [\App\Http\Controllers\Admin\LeaseController::class, 'processImport'])->name('lease.process-import');
        Route::post('lease/import/store', [\App\Http\Controllers\Admin\LeaseController::class, 'storeImport'])->name('lease.store-import');

        Route::resource('lease', LeaseController::class);
        Route::get('lease/lease-document/{id?}', [\App\Http\Controllers\Admin\LeaseController::class,    'lease_document'])->name('lease.lease-document');
        Route::post('lease-datatable', [\App\Http\Controllers\Admin\LeaseController::class, 'datatable']);
        Route::post('lease/update-status/{lease}', [\App\Http\Controllers\Admin\LeaseController::class, 'update_status'])->name('lease.update-status');

        Route::resource('lease-document', LeaseDocumentController::class);
        Route::post('lease-document/lease', [\App\Http\Controllers\Admin\LeaseDocumentController::class, 'lease'])->name('lease-document.lease');
    });


    Route::prefix('')->group(function () {

        Route::get('item/import', [\App\Http\Controllers\Admin\ItemController::class, 'viewImport'])->name('item.view-import');
        Route::get('item/import-format', [\App\Http\Controllers\Admin\ItemController::class, 'importFormat'])->name('item.import-format');
        Route::post('item/import/post', [\App\Http\Controllers\Admin\ItemController::class, 'import'])->name('item.import');
        Route::post('item/import/store', [\App\Http\Controllers\Admin\ItemController::class, 'importStore'])->name('item.import-store');

        Route::get('item/data-type/{type?}', [\App\Http\Controllers\Admin\ItemController::class, 'data_per_type'])->name('item.type');
        Route::get('item/price/latest/{id?}', [\App\Http\Controllers\Admin\ItemController::class, 'latest_price'])->name('item.price-latest');
        Route::get('item/unit/{id?}', [\App\Http\Controllers\Admin\ItemController::class, 'item_unit'])->name('item.item-unit');

        Route::get('item/{id}/price', [\App\Http\Controllers\Admin\ItemPriceController::class, 'index'])->name('item.price');
        Route::post('item/{id}/price', [\App\Http\Controllers\Admin\ItemPriceController::class, 'store'])->name('item.price.store');
        Route::delete('item/{id}/price/{price_id}', [\App\Http\Controllers\Admin\ItemPriceController::class, 'destroy'])->name('item.price.destroy');

        Route::get('item/select-for-purchase-request/{type?}', [\App\Http\Controllers\Admin\ItemController::class, 'select_for_purchase_request'])->name('item.select-for-purchase-request');

        Route::get('item/beginning-balance', [\App\Http\Controllers\Admin\BeginningBalanceItemController::class, 'index'])->name('item.beginning-balance.index');
        Route::post('item/beginning-balance/import-format', [\App\Http\Controllers\Admin\BeginningBalanceItemController::class, 'importFormat'])->name('item.beginning-balance.import-format');
        Route::post('item/beginning-balance/import', [\App\Http\Controllers\Admin\BeginningBalanceItemController::class, 'import'])->name('item.beginning-balance.import');
        Route::post('item/beginning-balance/store', [\App\Http\Controllers\Admin\BeginningBalanceItemController::class, 'store'])->name('item.beginning-balance.store');

        Route::resource('item', ItemController::class);
        Route::resource('item-type', ItemTypeController::class)->only('index', 'show', 'edit', 'update');
        Route::resource('item-category', ItemCategoryController::class);

        Route::post('item-category/import', [\App\Http\Controllers\Admin\ItemCategoryController::class, 'import'])->name('item-category.import');
    });

    Route::prefix('')->group(function () {
        Route::get('quotation/generate-code', [\App\Http\Controllers\Admin\QuotationController::class, 'generateCodeByDate'])->name('quotation.code');
    });


    Route::prefix('')->group(function () {
        Route::get('human-resource-report', [\App\Http\Controllers\Admin\HumanResourceReportController::class, 'index'])->name('human-resource-report.index');
        Route::post('human-resource-report', [\App\Http\Controllers\Admin\HumanResourceReportController::class, 'show'])->name('human-resource-report.show');
    });

    Route::get('user/get-employee', [\App\Http\Controllers\Admin\UserController::class, 'getEmployeeEmail'])->name('user.get-employee');

    Route::resources([
        'degree' => DegreeController::class,
        'education' => EducationController::class,
        'quotation-add-on-type' => QuotationAddOnTypeController::class,
        'customer' => CustomerController::class,
        'price' => PriceController::class,
        'quotation' => QuotationController::class,
        'user' => UserController::class,
        'tax' => TaxController::class,
        'employment-status' => EmploymentStatusController::class,
        'sh-number' => ShNumberController::class,
        'vendor' => VendorController::class,
        'vehicle-fleet' => VechicleFleetController::class,
        'garage' => GarageController::class,
        'unit' => UnitController::class,
        'position' => PositionController::class,
        'currency' => CurrencyController::class,
        'business-field' => BusinessFieldController::class,
        'ware-house' => WareHouseController::class,
        'payroll-period' => PayrollPeriodController::class,
        'payroll' => PayrollController::class,
        'labor-demand' => LaborDemandController::class,
        'labor-transfer-form' => LaborTransferFormController::class,
        'master-hrd-assessment' => MasterHrdAssessmentController::class,
        'master-user-assessment' => MasterUserAssessmentController::class,
        'master-gp-evaluation' => MasterGpEvaluationController::class,
        'hrd-assessment' => HrdAssessmentController::class,
        'user-assessment' => UserAssessmentController::class,
        'gp-evaluation' => GpEvaluationController::class,
        'role' => RoleController::class,
        'journal' => JournalController::class,
        'branch' => BranchController::class,
        'model' => ModelController::class,
        'bank-internal' => BankInternalController::class,
        'fleet' => FleetController::class,
        'division' => DivisionController::class,
        'offering-letter' => OfferingLetterController::class,
        'permission-letter-employee' => PermissionLetterEmployeeController::class,
        'project' => ProjectController::class,

        // FINANCE AND ACCOUNTING
        'fund-submission' => FundSubmissionController::class,

        // account receivable
        'receivables-payment' => ReceivablesPaymentController::class,

        // account payable
        'account-payable' => AccountPayableController::class,

        // incoming payment
        'incoming-payment' => IncomingPaymentController::class,

        // outgoing payment
        'outgoing-payment' => OutgoingPaymentController::class,

        // asset

        // purchase return
        'purchase-return' => PurchaseReturnController::class,

        'invoice-return' => InvoiceReturnController::class,

        // cash advance payment
        'cash-advance-payment' => CashAdvancePaymentController::class,
        'cash-advance-receive' => CashAdvanceReceiveController::class,

        // tax reconciliation
        'tax-reconciliation' => TaxReconciliationController::class,

        'receive-payment' => ReceivePaymentController::class,

        // closing period
        'closing-period' => ClosingPeriodController::class,

        // salary item
        'salary-item' => SalaryItemController::class,

        'income-tax' => IncomeTaxController::class,
        'non-taxable-income' => NonTaxableIncomeController::class,
        'setting' => SettingController::class,
        'master-letter' => MasterLetterController::class,
        'reset-leave' => ResetLeaveController::class,
    ]);

    Route::resource('company', CompanyController::class)->only(['index', 'update']);


    Route::prefix('')->group(function () {
        Route::delete('depreciation/destroy-range', [\App\Http\Controllers\Admin\DepreciationController::class, 'destroyRange'])->name('depreciation.destroyRange');
        Route::resource('depreciation', DepreciationController::class);

        Route::resource('disposition', DispositionController::class);

        Route::delete('amortization/destroy-range', [\App\Http\Controllers\Admin\AmortizationController::class, 'destroyRange'])->name('amortization.destroyRange');
        Route::resource('amortization', AmortizationController::class);
    });

    Route::get('select/asset-document-type', [\App\Http\Controllers\Admin\AssetDocumentTypeController::class, 'select'])->name('select.asset-document-type');
    Route::resource('asset-document-type', AssetDocumentTypeController::class)->except(['show']);

    Route::get('select/asset-category', [\App\Http\Controllers\Admin\AssetCategoryController::class, 'select'])->name('select.asset-category');
    Route::resource('asset-category', AssetCategoryController::class)->except(['show']);

    Route::resource('contract-extension', ContractExtensionController::class);

    Route::post('role/is-has-permission', [\App\Http\Controllers\Admin\RoleController::class, 'isHasPermission'])->name('role.is-has-permission');

    Route::get('fleet/detail/{id?}/', [\App\Http\Controllers\Admin\FleetController::class, 'detail'])->name('fleet.detail');

    Route::get('warehouse/get-stock-card', [\App\Http\Controllers\Admin\WareHouseController::class, 'getStockCard'])->name('warehouse.get-stock-card');

    Route::get('warehouse/{id?}', [\App\Http\Controllers\Admin\WareHouseController::class, 'detail'])->name('warehouse.detail');

    Route::get('inventory-report/', [\App\Http\Controllers\Admin\InventoryReportController::class, 'index'])->name('inventory-report.index');
    Route::post('inventory-report/{type}', [\App\Http\Controllers\Admin\InventoryReportController::class, 'show'])->name('inventory-report.show');

    Route::prefix('')->group(function () {
        Route::get('stock-card/{id}/{warehouse_id}', [\App\Http\Controllers\Admin\StockCardController::class, 'show'])->name('stock-card.show');
        Route::resource('stock-card', StockCardController::class)->except(['show']);

        Route::resource('stock-value', StockValueController::class);
        Route::resource('stock-mutation', StockMutationController::class);
        Route::post('stock-mutation-refresh', [\App\Http\Controllers\Admin\StockMutationController::class, 'refresh'])->name('stock-mutation.refresh');
        Route::get('stock-mutation-refersh-stock-log', [\App\Http\Controllers\Admin\StockMutationController::class, 'refresh_stock_log'])->name('stock-mutation.refresh-stock-log');
        Route::post('stock-mutation-weekly-refresh', [\App\Http\Controllers\Admin\StockMutationController::class, 'weekly_refresh'])->name('stock-mutation.weekly-refresh');
    });

    Route::prefix('')->group(function () {
        Route::post('stock-adjustment/price-select', [\App\Http\Controllers\Admin\StockOpnameController::class, 'priceSelect'])->name('stock-adjustment.price-select');
        Route::post('stock-adjustment/price-detail', [\App\Http\Controllers\Admin\StockOpnameController::class, 'priceDetail'])->name('stock-adjustment.price-detail');
        Route::post('stock-adjustment/{id}/update-status', [\App\Http\Controllers\Admin\StockOpnameController::class, 'update_status'])->name('stock-adjustment.update-status');
    });

    Route::prefix('')->group(function () {
        Route::get('stock-transfer/receiving', [\App\Http\Controllers\Admin\StockTransferController::class, 'receiving'])->name('stock-transfer.receiving');
        Route::get('stock-transfer/receiving/{id}', [\App\Http\Controllers\Admin\StockTransferController::class, 'showReceiving'])->name('stock-transfer.show.receiving');
        Route::post('stock-transfer/receiving/update/{id}', [\App\Http\Controllers\Admin\StockTransferController::class, 'updateReceiving'])->name('stock-transfer.update.receiving');

        Route::get('stock-transfer/check-stock', [\App\Http\Controllers\Admin\StockTransferController::class, 'checkStock'])->name('stock-transfer.check-stock');

        Route::post('stock-transfer/{id}/update-status', [\App\Http\Controllers\Admin\StockTransferController::class, 'update_status'])->name('stock-transfer.update-status');

        Route::post('stock-transfer-check/{id_from}/{id_item}', [\App\Http\Controllers\Admin\StockTransferController::class, 'checkItemStockTransfer'])->name('stock-transfer-check.item-stock-transfer');

        Route::resource('stock-transfer', StockTransferController::class);
    });

    Route::prefix('')->group(function () {
        Route::post('stock-usage/get-stock-left', [\App\Http\Controllers\Admin\StockUsageController::class, 'get_stock_left'])->name('stock-usage.get-stock-left');
        Route::post('stock-usage/get-data-for-division', [\App\Http\Controllers\Admin\StockUsageController::class, 'get_data_for_division'])->name('stock-usage.get-data-for-division');
        Route::post('stock-usage/{id}/update-status', [\App\Http\Controllers\Admin\StockUsageController::class, 'update_status'])->name('stock-usage.update-status');
        Route::post('stock-usage/{id}/upload', [\App\Http\Controllers\Admin\StockUsageController::class, 'upload'])->name('stock-usage.upload');
        Route::post('stock-usage/get-purchase-request-item', [\App\Http\Controllers\Admin\StockUsageController::class, 'get_purchase_request_item'])->name('stock-usage.get-purchase-request-item');
        Route::post('stock-usage/coa-expense', [\App\Http\Controllers\Admin\StockUsageController::class, 'coa_expense'])->name('stock-usage.coa-expense');

        Route::resource('stock-usage', StockUsageController::class);
    });

    Route::prefix('')->group(function () {
        Route::get('closing-delivery-order-ship/select-delivery-order', [\App\Http\Controllers\Admin\ClosingDeliveryOrderShipController::class, 'selectDeliveryOrder'])->name('closing-delivery-order-ship.select-delivery-order');
        Route::get('closing-delivery-order-ship/select-delivery-order/{closing_delivery_order_ship_id?}', [\App\Http\Controllers\Admin\ClosingDeliveryOrderShipController::class, 'detailDeliveryOrder'])->name('closing-delivery-order-ship.select-delivery-order.show');
        Route::post('closing-delivery-order-ship/{closing_delivery_order_ship_id}/void', [\App\Http\Controllers\Admin\ClosingDeliveryOrderShipController::class, 'void'])->name('closing-delivery-order-ship.void');
        Route::resource('closing-delivery-order-ship', ClosingDeliveryOrderShipController::class)->only(['index', 'create', 'store', 'show']);
    });

    Route::prefix('')->group(function () {

        Route::post('item-receiving-report-general/{id}/update-status', [\App\Http\Controllers\Admin\ItemReceivingReportGeneralController::class, 'updateStatus'])->name('item-receiving-report-general.update-status');
        Route::get('item-receiving-report-general/detail-for-edit-data/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportGeneralController::class, 'apiDetailForEditData'])->name('item-receiving-report-general.detail-for-edit-data');
        Route::resource('item-receiving-report-general', ItemReceivingReportGeneralController::class);
        Route::get('item-receiving-report-general/history/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportGeneralController::class, 'history'])->name('item-receiving-report-general.history');
        Route::get('item-receiving-report-general/{id}/get-payment-status-po-general', [\App\Http\Controllers\Admin\ItemReceivingReportGeneralController::class, 'getPaymentStatusPoGeneral'])->name('item-receiving-report-general.get-payment-status-po-general');
        Route::post('item-receiving-report-general/{id}/store-stock-usage', [\App\Http\Controllers\Admin\ItemReceivingReportGeneralController::class, 'storeStockUsage'])->name('item-receiving-report-general.store-stock-usage');


        Route::post('item-receiving-report-service/{id}/update-status', [\App\Http\Controllers\Admin\ItemReceivingReportServiceController::class, 'updateStatus'])->name('item-receiving-report-service.update-status');
        Route::get('item-receiving-report-service/detail-for-edit-data/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportServiceController::class, 'apiDetailForEditData'])->name('item-receiving-report-service.detail-for-edit-data');
        Route::resource('item-receiving-report-service', ItemReceivingReportServiceController::class);
        Route::get('item-receiving-report-service/history/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportServiceController::class, 'history'])->name('item-receiving-report-service.history');


        Route::post('item-receiving-report-trading/{id}/update-status', [\App\Http\Controllers\Admin\ItemReceivingReportTradingController::class, 'updateStatus'])->name('item-receiving-report-trading.update-status');
        Route::get('item-receiving-report-trading/detail-for-edit-data/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportTradingController::class, 'apiDetailForEditData'])->name('item-receiving-report-trading.detail-for-edit-data');
        Route::resource('item-receiving-report-trading', ItemReceivingReportTradingController::class);
        Route::get('item-receiving-report-trading/history/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportTradingController::class, 'history'])->name('item-receiving-report-trading.history');


        Route::post('item-receiving-report-transport/{id}/update-status', [\App\Http\Controllers\Admin\ItemReceivingReportTransportController::class, 'updateStatus'])->name('item-receiving-report-transport.update-status');
        Route::get('item-receiving-report-transport/detail-for-edit-data/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportTransportController::class, 'apiDetailForEditData'])->name('item-receiving-report-transport.detail-for-edit-data');
        Route::resource('item-receiving-report-transport', ItemReceivingReportTransportController::class);
        Route::get('item-receiving-report-transport/history/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportTransportController::class, 'history'])->name('item-receiving-report-transport.history');

        Route::resource('item-receiving-report', ItemReceivingReportController::class);
    });

    Route::resource('item-subtitute', ItemSubtituteController::class)->only('index', 'store', 'destroy');

    Route::post('period/generate', [\App\Http\Controllers\Admin\PeriodController::class, 'generate'])->name('period.generate');
    Route::resource('period', PeriodController::class)->only(['index']);
    Route::resource('quotation-add-on-value', QuotationAddOnValueController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('so-pairing/', [\App\Http\Controllers\Admin\PairingSoToPoController::class, 'so_not_pairing_completely'])->name('pairing.so_not_pairing_completely');
    Route::get('so-pairing/{id}/pairing', [\App\Http\Controllers\Admin\PairingSoToPoController::class, 'available_po_for_a_so'])->name('pairing.pairing');
    Route::get('so-pairing/{id}/select', [\App\Http\Controllers\Admin\PairingSoToPoController::class, 'select'])->name('pairing.select');
    Route::post('so-pairing/{id}/pairing', [\App\Http\Controllers\Admin\PairingSoToPoController::class, 'pairing'])->name('pairing.pairing-store');

    Route::get('po-pairing/', [\App\Http\Controllers\Admin\PairingPoToSoController::class, 'po_not_pairing_completely'])->name('pairing.po_not_pairing_completely');
    Route::get('po-pairing/{id}/pairing', [\App\Http\Controllers\Admin\PairingPoToSoController::class, 'available_so_for_a_po'])->name('pairing.po_pairing');
    Route::get('po-pairing/{id}/select', [\App\Http\Controllers\Admin\PairingPoToSoController::class, 'select'])->name('pairing.po_select');
    Route::post('po-pairing/{id}/pairing', [\App\Http\Controllers\Admin\PairingPoToSoController::class, 'pairing'])->name('pairing.po-pairing-store');

    Route::get('payroll-period-detail/{id}', [\App\Http\Controllers\Admin\PayrollPeriodController::class, 'detail']);
    Route::get('payroll-slip-gaji', [\App\Http\Controllers\Admin\PayrollController::class, 'exportPdf'])->name('payroll.export-slip-gaji');
    Route::post('payroll-check', [\App\Http\Controllers\Admin\PayrollController::class, 'checkPayroll'])->name('check-payroll');

    Route::post('payroll/get-fee', [\App\Http\Controllers\Admin\PayrollController::class, 'getFee']);
    Route::post('payroll/get-allowance', [\App\Http\Controllers\Admin\PayrollController::class, 'getAllowance']);
    Route::post('payroll/get-deduction', [\App\Http\Controllers\Admin\PayrollController::class, 'getDeduction']);
    Route::post('payroll/get-cuti', [\App\Http\Controllers\Admin\PayrollController::class, 'getCuti'])->name('payroll.get-cuti');
    Route::post('payroll/get-izin', [\App\Http\Controllers\Admin\PayrollController::class, 'getIzin'])->name('payroll.get-izin');
    Route::post('payroll/calculate-income-tax', [\App\Http\Controllers\Admin\PayrollController::class, 'calculateIncomeTax'])->name('payroll.calculate-income-tax');

    Route::prefix('')->group(function () {
        Route::get('attendance/employee', [\App\Http\Controllers\Admin\AttendanceController::class, 'employee'])->name('attendance.employee');
        Route::get('attendance/show-by-employee', [\App\Http\Controllers\Admin\AttendanceController::class, 'showByEmployee'])->name('attendance.show-by-employee');
        Route::get('attendance/import-format', [\App\Http\Controllers\Admin\AttendanceController::class, 'getImportFormat'])->name('attendance.import.format');
        Route::post('attendance/import', [\App\Http\Controllers\Admin\AttendanceController::class, 'importAttendance'])->name('attendance.import');
        Route::post('attendance/export', [\App\Http\Controllers\Admin\AttendanceController::class, 'exportAttendance'])->name('attendance.export');
        Route::post('attendance/bulk-delete', [\App\Http\Controllers\Admin\AttendanceController::class, 'bulkDelete'])->name('attendance.bulk-delete');

        Route::resource('attendance', AttendanceController::class);
        Route::post('attendance/data-employee', [\App\Http\Controllers\Admin\AttendanceController::class, 'data_employee'])->name('attendance.data-employee');
    });

    Route::prefix('')->group(function () {
        Route::post('specific-time-work-agreement/{id}/update-status', [\App\Http\Controllers\Admin\SpecificTimeWorkAgreementController::class, 'update_status'])->name('specific-time-work-agreement.update-status');
        Route::post('specific-time-work-agreement/generate', [\App\Http\Controllers\Admin\SpecificTimeWorkAgreementController::class, 'generate'])->name('specific-time-work-agreement.generate');

        Route::resource('specific-time-work-agreement', SpecificTimeWorkAgreementController::class);
    });

    Route::resource('recruitment', RecruitmentController::class);

    Route::get('/leave-pdf', 'LeaveController@exportPdf');
    Route::get('/leave-excel', 'LeaveController@exportExcel');

    Route::post('/leave-data', 'LeaveController@data')->name('leave.data');
    Route::post('/leave/remaining', 'LeaveController@dataLeave')->name('leave.remaining');
    Route::post('/update-status/{id}', 'LeaveController@update_status')->name('leave.update-status');
    Route::resource('leave', LeaveController::class);
    Route::post('/leave-file-change/{id}', 'LeaveController@changedFileAttachment')->name('leave.changed-file-attachment');
    Route::post('/leave-file-change/{id}/update-status', 'LeaveController@updateStatusChangedFile')->name('leave.changed-file-attachment.update-status');
    Route::get('/leave-file-change/{id}/check-status', 'LeaveController@checkHaveFileChangePending')->name('leave.changed-file-attachment.check-status');

    // MASS LEAVE
    Route::post('/mass-leave-data', 'MassLeaveController@data')->name('mass-leave.data');
    Route::resource('mass-leave', MassLeaveController::class);
    Route::post('/mass-leave/employee-data', [\App\Http\Controllers\Admin\MassLeaveController::class, 'employee_data'])->name('mass-leave.employee-data');

    Route::get('authorization/count-each', [\App\Http\Controllers\Admin\AuthorizationController::class, 'getCountEachAuthorizationModel'])->name('authorization.count-each');
    Route::post('authorization/datatables', [\App\Http\Controllers\Admin\AuthorizationController::class, 'datatables'])->name('authorization.datatables');
    Route::get('authorization/getCountTotalAuthorizationForSidebar', 'AuthorizationController@getCountTotalAuthorizationSidebar')->name('authorization.getCountTotalAuthorizationSidebar');

    Route::get('/authorization/notification', 'AuthorizationController@notification');
    Route::resource('authorization', 'AuthorizationController')->only('index');

    Route::post('authorization-request-revert-void/{id}', [\App\Http\Controllers\Admin\AuthorizationController::class, 'request_revert_void'])->name('authorization-request-revert-void');
    Route::post('authorization-response-revert-void/{id}', [\App\Http\Controllers\Admin\AuthorizationController::class, 'response_revert_void'])->name('authorization-response-revert-void');

    Route::post('master-loyalty/data', 'MasterLoyaltyController@data');
    Route::resource('master-loyalty', 'MasterLoyaltyController');

    Route::get('/fund-submission/check-date/{id}', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'checkdateFundSubmission'])->name('fund-submission.check-date');
    Route::post('/fund-submission/vendor-coa/{id}', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'vendor_coa'])->name('fund-submission.vendor-coa');
    Route::post('/fund-submission/{id}/update-status', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'update_status'])->name('fund-submission.update-status');
    Route::post('/fund-submission/supplier-invoice-select', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'supplier_invoice_select'])->name('receivables-payment.supplier-invoice-select');
    Route::post('/fund-submission/supplier-invoice-general-select', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'supplier_invoice_general_select'])->name('receivables-payment.supplier-invoice-general-select');
    Route::post('/fund-submission/purchase-return-select', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'purchase_return_select'])->name('fund-submission.purchase-return-select');
    Route::get('/fund-submission-invoice-return-select', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'invoice_return_select'])->name('incoming-payment.invoice-return-select');
    Route::get('/fund-submission-invoice-return/{id}', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'invoice_return_detail'])->name('incoming-payment.invoice-return-detail');
    Route::get('/fund-submission/export', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'export'])->name('fund-submission.export');
    Route::get('/fund-submission-download-recap', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'download_recap'])->name('fund-submission.download-recap');
    Route::get('fund-submission-show-purchase/{id?}', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'show_purchase'])->name('fund-submission.show-purchase');
    Route::get('fund-submission-show-purchase-down-payment/{id?}', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'show_purchase_down_payment'])->name('fund-submission.show-purchase-down-payment');
    Route::get('fund-submission/history/{id}', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'history'])->name('fund-submission.history');

    Route::get('/fund-submission-cash-advance-detail/{id}', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'cash_advance_detail'])->name('fund-submission.cash-advance-detail');

    Route::resource('fund-submission-lpb', FundSubmissionLpbController::class);

    // Route::get('/incoming-payment/export', [\App\Http\Controllers\Admin\IncomingPaymentController::class, 'export'])->name('incoming-payment.export');

    Route::get('/incoming-payment-purchase-return-select', [\App\Http\Controllers\Admin\IncomingPaymentController::class, 'purchase_return_select'])->name('incoming-payment.purchase-return-select');
    Route::get('/incoming-payment-purchase-return/{id}', [\App\Http\Controllers\Admin\IncomingPaymentController::class, 'purchase_return_detail'])->name('incoming-payment.purchase-return-detail');

    Route::get('/incoming-payment-cash-advance/{id}', [\App\Http\Controllers\Admin\IncomingPaymentController::class, 'cash_advance_detail'])->name('incoming-payment.cash-advance-detail');

    Route::post('/receivables-payment/invoice-select', [\App\Http\Controllers\Admin\ReceivablesPaymentController::class, 'invoice_select'])->name('receivables-payment.invoice-select');
    Route::post('/receivables-payment/{id}/update-status', [\App\Http\Controllers\Admin\ReceivablesPaymentController::class, 'update_status'])->name('receivables-payment.update-status');
    Route::get('/receivables-payment/export', [\App\Http\Controllers\Admin\ReceivablesPaymentController::class, 'export'])->name('receivables-payment.export');

    Route::post('/receivables-payment/invoice-return-select', [\App\Http\Controllers\Admin\ReceivablesPaymentController::class, 'invoice_return_select'])->name('receivables-payment.invoice-return-select');
    Route::get('receivables-payment/history/{id}', [\App\Http\Controllers\Admin\ReceivablesPaymentController::class, 'history'])->name('receivables-payment.history');


    Route::post('/account-payable/{id}/update-status', [\App\Http\Controllers\Admin\AccountPayableController::class, 'update_status'])->name('account-payable.update-status');
    Route::get('/account-payable/fund-submission/{id}', [\App\Http\Controllers\Admin\AccountPayableController::class, 'fund_submission'])->name('account-payable.fund-submission');
    Route::get('account-payable/history/{id}', [\App\Http\Controllers\Admin\AccountPayableController::class, 'history'])->name('account-payable.history');

    Route::prefix('')->group(function () {
        Route::prefix('')->group(function () {
            Route::post('cash-advance-return-customer/cash-advance-receive/{customer_id?}/{currency_id?}/{project_id?}', [\App\Http\Controllers\Admin\CashAdvancedReturnCustomerController::class, 'get_cash_advance_receives'])->name('cash-advance-return-customer.cash-advance-receives');
            Route::post('cash-advance-return-customer/unpaid-full-invoice/{customer_id?}/{currency_id?}/', [\App\Http\Controllers\Admin\CashAdvancedReturnCustomerController::class, 'get_unpaid_full_invoices'])->name('cash-advance-return-customer.unpaid-full-invoices');
            Route::get('cash-advance-return-customer/get-detail-for-edit', [\App\Http\Controllers\Admin\CashAdvancedReturnCustomerController::class, 'getDetailForEdit'])->name('cash-advance-return-customer.get-detail-for-edit');
            Route::post('cash-advance-return-customer/{id}/update-status', [\App\Http\Controllers\Admin\CashAdvancedReturnCustomerController::class, 'update_status'])->name('cash-advance-return-customer.update-status');

            Route::resource('cash-advance-return-customer', CashAdvancedReturnCustomerController::class);
        });

        Route::prefix('')->group(function () {
            Route::post('cash-advance-return-vendor/cash-advance-payment', [\App\Http\Controllers\Admin\CashAdvancedReturnVendorController::class, 'get_cash_advance_payments'])->name('cash-advance-return-vendor.cash-advance-payments');
            Route::post('cash-advance-return-vendor/unpaid-full-supplier-invoice', [\App\Http\Controllers\Admin\CashAdvancedReturnVendorController::class, 'get_unpaid_full_supplier_invoices'])->name('cash-advance-return-vendor.unpaid-full-supplier-invoices');
            Route::get('cash-advance-return-vendor/get-detail-for-edit', [\App\Http\Controllers\Admin\CashAdvancedReturnVendorController::class, 'getDetailForEdit'])->name('cash-advance-return-vendor.get-detail-for-edit');
            Route::post('cash-advance-return-vendor/{id}/update-status', [\App\Http\Controllers\Admin\CashAdvancedReturnVendorController::class, 'update_status'])->name('cash-advance-return-vendor.update-status');

            Route::resource('cash-advance-return-vendor', CashAdvancedReturnVendorController::class);
        });
    });

    Route::prefix('')->group(function () {
        Route::resource('legality-document', LegalityDocumentController::class);
    });

    Route::get('purchase-return/{id}/get-lpb-detail', [\App\Http\Controllers\Admin\PurchaseReturnController::class, 'get_lpb_detail'])->name('purchase-return.get-lpb-detail');
    Route::post('/purchase-return/{id}/update-status', [\App\Http\Controllers\Admin\PurchaseReturnController::class, 'update_status'])->name('purchase-return.update-status');
    //check unique tax number
    Route::post('/purchase-return/check-unique-tax-number', [\App\Http\Controllers\Admin\PurchaseReturnController::class, 'check_unique_tax_number'])->name('purchase-return.check-unique-tax-number');

    Route::post('cash-bond/{id}/update-status', [\App\Http\Controllers\Admin\CashBondController::class, 'update_status'])->name('cash-bond.update-status');
    Route::resource('cash-bond', CashBondController::class);
    Route::get('cash-bond-export/{id}', [\App\Http\Controllers\Admin\CashBondController::class, 'export'])->name('cash-bond.export');
    Route::get('cash-bond-return-export/{id}', [\App\Http\Controllers\Admin\CashBondReturnController::class, 'export'])->name('cash-bond-return.export');

    Route::post('cash-bond-return/get-data-for-create', [\App\Http\Controllers\Admin\CashBondReturnController::class, 'getCashBondForCreate'])->name('cash-bond-return.get-cash-bond-for-create');
    Route::post('cash-bond-return/{id}/update-status', [\App\Http\Controllers\Admin\CashBondReturnController::class, 'update_status'])->name('cash-bond-return.update-status');
    Route::resource('cash-bond-return', CashBondReturnController::class);

    Route::post('/disposition/{id}/update-status', [\App\Http\Controllers\Admin\DispositionController::class, 'update_status'])->name('disposition.update-status');

    Route::post('/tax-reconciliation/get-data', [\App\Http\Controllers\Admin\TaxReconciliationController::class, 'get_data'])->name('tax-reconciliation.get-data');
    Route::post('/tax-reconciliation/{id}/update-status', [\App\Http\Controllers\Admin\TaxReconciliationController::class, 'update_status'])->name('tax-reconciliation.update-status');
    Route::get('tax-reconciliation/export/{id}', [\App\Http\Controllers\Admin\TaxReconciliationController::class, 'export'])->name('tax-reconciliation.export');

    Route::post('contract-extension-find-by-id', [\App\Http\Controllers\Admin\ContractExtensionController::class, 'findById'])->name('contract-extension-find-by-id');
    Route::post('/contract-extension/{id}/update-status', [\App\Http\Controllers\Admin\ContractExtensionController::class, 'update_status'])->name('contract-extension.update-status');

    Route::post('receive-payment/{id}/update-status', [\App\Http\Controllers\Admin\ReceivePaymentController::class, 'update_status'])->name('receive-payment.update-status');
    Route::post('send-payment/{id}/update-status', [\App\Http\Controllers\Admin\SendPaymentController::class, 'update_status'])->name('send-payment.update-status');

    // * resource ================================================================================================================================

    Route::prefix('')->group(function () {
        Route::resource('finance-report', FinanceReportController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::resource('accounting-report', AccountingReportController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::resource('cashier-report', CashierReportController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::get('finance-report/{type}/report', [\App\Http\Controllers\Admin\FinanceReportController::class, 'report'])->name('finance-report.report');
        Route::get('sales-report-by-trading-period/{type}/report', [\App\Http\Controllers\Admin\FinanceReportController::class, 'report'])->name('sales-report-by-trading-period.report');

        Route::resource('purchase-order-report-all', PurchaseOrderReportController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::get('purchase-order-report-all/{type}/report', [\App\Http\Controllers\Admin\PurchaseOrderReportController::class, 'report'])->name('purchase-order-report-all.report');

        Route::resource('profit-loss-setting', ProfitLossSettingController::class);
        Route::post('profit-loss-setting/get-data', [\App\Http\Controllers\Admin\ProfitLossSettingController::class, 'get_data'])->name('profit-loss-setting.get-data');
        Route::post('profit-loss-setting/refresh', [\App\Http\Controllers\Admin\ProfitLossSettingController::class, 'refresh'])->name('profit-loss-setting.refresh');
        Route::post('profit-loss-setting/update-position', [\App\Http\Controllers\Admin\ProfitLossSettingController::class, 'update_position'])->name('profit-loss-setting.update-position');
        Route::post('profit-loss-setting/update-order', [\App\Http\Controllers\Admin\ProfitLossSettingController::class, 'update_order'])->name('profit-loss-setting.update-order');
    });


    Route::prefix('')->group(function () {
        Route::get('vendor-debt/create', [\App\Http\Controllers\Admin\VendorDebtController::class, 'create'])->name('vendor-debt.create');
        Route::get('vendor-debt/template', [\App\Http\Controllers\Admin\VendorDebtController::class, 'template'])->name('vendor-debt.template');
        Route::post('vendor-debt/preview', [\App\Http\Controllers\Admin\VendorDebtController::class, 'preview'])->name('vendor-debt.preview');
        Route::post('vendor-debt/import', [\App\Http\Controllers\Admin\VendorDebtController::class, 'import'])->name('vendor-debt.import');

        Route::get('customer-receivables/create', [\App\Http\Controllers\Admin\CustomerReceivablesController::class, 'create'])->name('customer-receivables.create');
        Route::get('customer-receivables/template', [\App\Http\Controllers\Admin\CustomerReceivablesController::class, 'template'])->name('customer-receivables.template');
        Route::post('customer-receivables/preview', [\App\Http\Controllers\Admin\CustomerReceivablesController::class, 'preview'])->name('customer-receivables.preview');
        Route::post('customer-receivables/import', [\App\Http\Controllers\Admin\CustomerReceivablesController::class, 'import'])->name('customer-receivables.import');
    });

    Route::post('check-bank-code', [\App\Http\Controllers\Admin\BankCodeMutationController::class, 'check_bank_code'])->name('check-bank-code');
    Route::post('closing-period/check', [\App\Http\Controllers\Admin\ClosingPeriodController::class, 'check'])->name('closing-period.check');
    Route::post('closing-period/{id}/update-status', [\App\Http\Controllers\Admin\ClosingPeriodController::class, 'update_status'])->name('closing-period.update-status');

    Route::post('check-can-print', [\App\Http\Controllers\Admin\DocumentPrintController::class, 'check_can_print'])->name('check-can-print');
    Route::post('request-print', [\App\Http\Controllers\Admin\DocumentPrintController::class, 'request_print'])->name('request-print');
    Route::post('authorize-request-print/{id}', [\App\Http\Controllers\Admin\DocumentPrintController::class, 'authorize_request_print'])->name('authorize-request-print');
    Route::post('get-print-request-approval', [\App\Http\Controllers\Admin\DocumentPrintController::class, 'get_print_request_approval'])->name('get-print-request-approval');

    Route::get('master-print-authorization', [\App\Http\Controllers\Admin\MasterPrintController::class, 'index'])->name('master-print-authorization.index');
    Route::post('master-print-authorization', [\App\Http\Controllers\Admin\MasterPrintController::class, 'store'])->name('master-print-authorization.store');
    Route::get('master-print-authorization/data', [\App\Http\Controllers\Admin\MasterPrintController::class, 'data'])->name('master-print-authorization.data');

    Route::resource('generate-journal', GenerateJournalController::class);

    Route::post('tax-reconciliation-regenerate', [\App\Http\Controllers\Admin\TaxReconciliationController::class, 'regenerate'])->name('tax-reconciliation.regenerate');
});


Route::group([
    'as' => 'transport.',
    'prefix' => 'transport',
    'middleware' => ['auth', 'vendor-user'],
    'namespace' => '\App\Http\Controllers\Transport',
], function () {
    Route::get('delivery-order/{id}/delivery-orders', [\App\Http\Controllers\Transport\DeliveryOrderController::class, 'delivery_orders'])->name('delivery-order.show.list');

    Route::post('delivery-order/{purchase_transport_id}/request-print-single/{delivery_order_id}', [\App\Http\Controllers\Transport\DeliveryOrderController::class, 'request_print'])->name('delivery-order.show.request.print');
    Route::put('delivery-order/{id}/request-print-all', [\App\Http\Controllers\Transport\DeliveryOrderController::class, 'request_print_all'])->name('delivery-order.show.request-print-all');
    Route::post('delivery-order/print/{purchase_transport_id}/{delivery_order_id}', [\App\Http\Controllers\Transport\DeliveryOrderController::class, 'print'])->name('delivery-order.show.print');

    Route::get('delivery-order/{purchase_transport_id}/{delivery_order_id}', [\App\Http\Controllers\Transport\DeliveryOrderController::class, 'show_single_delivery_order'])->name('delivery-order.show.show-single-delivery-order');

    Route::resource('delivery-order', DeliveryOrderController::class)->only(['index', 'show']);

    Route::get('delivery-order/{purchase_transport}/{id}/edit', [\App\Http\Controllers\Transport\DeliveryOrderController::class, 'edit_single_delivery_order'])->name('delivery-order.detail.edit');
    Route::put('delivery-order/{purchase_transport}/{id}/edit', [\App\Http\Controllers\Transport\DeliveryOrderController::class, 'update_single_delivery_order'])->name('delivery-order.detail.update');
});

Route::group([
    'as' => 'guest.',
    'namespace' => '\App\Http\Controllers\Guest',
    'prefix' => 'guest',
], function () {
    // !! LABOR APPLICATION
    Route::resource('labor-application', LaborApplicationController::class);
    Route::get('labor-application-success', [\App\Http\Controllers\Guest\LaborApplicationController::class, 'success'])->name('labor-application.success');

    // !! OFFERING LETTER
    Route::resource('offering-letter', OfferingLetterController::class)->except(['show']);
    Route::get('offering-letter-success', [\App\Http\Controllers\Guest\OfferingLetterController::class, 'success'])->name('offering-letter.success');
    Route::get('offering-letter/{id}', [\App\Http\Controllers\Guest\OfferingLetterController::class, 'show'])->name('offering-letter.show');
    Route::get('offering-letter-document/{id}', [\App\Http\Controllers\Guest\OfferingLetterController::class, 'document'])->name('offering-letter.document');

    Route::group([
        'as' => 'employee.',
        'prefix' => 'employee',
    ], function () {

        Route::get('', [\App\Http\Controllers\Guest\EmployeeController::class, 'index'])->name('index');
        Route::get('find', [\App\Http\Controllers\Guest\EmployeeController::class, 'find'])->name('find');

        Route::get('edit/step/1/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'editStep1'])->name('edit.step1');
        Route::put('edit/step/1/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'updateStep1'])->name('update.step1');

        Route::get('edit/step/2/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'editStep2'])->name('edit.step2');
        Route::put('edit/step/2/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'updateStep2'])->name('update.step2');

        Route::get('edit/step/3/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'editStep3'])->name('edit.step3');
        Route::put('edit/step/3/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'updateStep3'])->name('update.step3');

        Route::get('edit/step/4/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'editStep4'])->name('edit.step4');
        Route::put('edit/step/4/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'updateStep4'])->name('update.step4');

        Route::get('edit/step/5/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'editStep5'])->name('edit.step5');
        Route::put('edit/step/5/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'updateStep5'])->name('update.step5');

        Route::get('edit/step/6/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'editStep6'])->name('edit.step6');
        Route::put('edit/step/6/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'updateStep6'])->name('update.step6');

        Route::get('edit/step/7/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'editStep7'])->name('edit.step7');
        Route::put('edit/step/7/{employee_id}', [\App\Http\Controllers\Guest\EmployeeController::class, 'updateStep7'])->name('update.step7');
    });
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

if (config('app.debug')) {
    Route::get('test', [App\Http\Controllers\Admin\SupplierInvoiceController::class, 'generate_supplier_invoice_tax_summary']);
    Route::get('test-component', [TestComponentController::class, 'test_component']);
    Route::get('update-link', [App\Http\Controllers\Admin\TestController::class, 'update_link']);

    Route::get('test-flash', fn() => redirect('/test-component')->with([
        'success' => true,
        'message' => 'Success redirect',
    ]));

    Route::get('journal-update-ordering', [\App\Http\Controllers\Admin\JournalController::class, 'update_ordering']);
    Route::get('stock-update-ordering', [\App\Http\Controllers\Admin\JournalController::class, 'update_stock_mutation_order']);
    Route::get('test/menu', [App\Http\Controllers\Admin\TestController::class, 'menu']);
}

// NOTIFICATION
Route::get('notification-data', [App\Http\Controllers\Admin\NotificationController::class, 'data'])->name('notification.data');
Route::get('notification-counter', [App\Http\Controllers\Admin\NotificationController::class, 'counter'])->name('notification.counter');
Route::get('notification/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('notification.show');
Route::get('notification-clear', [App\Http\Controllers\Admin\NotificationController::class, 'clear'])->name('notification.clear');


// Testing Dashboard Accounting
Route::group([
    'as' => 'accounting.',
    'middleware' => ['auth'],
    'prefix' => 'accounting'
], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('index');
});


Route::get('test/notification/expired', [App\Http\Controllers\Admin\AssetController::class, 'setNotificationForReminderAssetDocument']);
Route::get('test/notification/expired/lease', [App\Http\Controllers\Admin\LeaseController::class, 'setNotificationForReminderLeaseDocument']);
Route::get('test/notification/expired/fleet', [App\Http\Controllers\Admin\FleetController::class, 'setNotificationForReminderFleetDocument']);
Route::get('test/notification/expired/legal-document', [App\Http\Controllers\Admin\LegalityDocumentController::class, 'pushNotificationForMostExpiredDate']);

// pdf export
Route::get('purchase-order-transport/export/{id?}', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'export'])->name('purchase-order-transport.export.id');
Route::get('sales-order/export/{id?}', [\App\Http\Controllers\Admin\SoTradingController::class, 'export'])->name('sales-order.export.id');
Route::get('sales-order-general/export/{id?}', [\App\Http\Controllers\Admin\SaleOrderGeneralController::class, 'export'])->name('sales-order-general.export.id');
Route::get('labor-demand/export/{id}', [\App\Http\Controllers\Admin\LaborDemandController::class, 'export'])->name('labor-demand.export.id');
Route::get('labor-application/export/{id}', [\App\Http\Controllers\Admin\LaborApplicationController::class, 'export'])->name('labor-application.export.id');
Route::get('user-assessment/export/{id}', [\App\Http\Controllers\Admin\UserAssessmentController::class, 'export'])->name('user-assessment.export.id');
Route::get('hrd-assessment/export/{id}', [\App\Http\Controllers\Admin\HrdAssessmentController::class, 'export'])->name('hrd-assessment.export.id');
Route::get('employee/export/{id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'export_pdf'])->name('employee.export.id');
Route::get('contract-extension/export/{id}', [\App\Http\Controllers\Admin\ContractExtensionController::class, 'export'])->name('contract-extension.export');
Route::get('labor-transfer-form/export/{id}', [\App\Http\Controllers\Admin\LaborTransferFormController::class, 'export'])->name('labor-transfer-form.export');
Route::get('item-receiving-report-general/export/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportGeneralController::class, 'exportPdf'])->name('item-receiving-report-general.export-pdf');
Route::get('item-receiving-report-service/export/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportServiceController::class, 'exportPdf'])->name('item-receiving-report-service.export-pdf');
Route::get('item-receiving-report-trading/export/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportTradingController::class, 'exportPdf'])->name('item-receiving-report-trading.export-pdf');
Route::get('item-receiving-report-transport/export/{id}', [\App\Http\Controllers\Admin\ItemReceivingReportTransportController::class, 'exportPdf'])->name('item-receiving-report-transport.export-pdf');
Route::get('specific-time-work-agreement/export/{id}', [\App\Http\Controllers\Admin\SpecificTimeWorkAgreementController::class, 'export'])->name('specific-time-work-agreement.export.id');
Route::get('leave-export/{id}', [\App\Http\Controllers\Admin\LeaveController::class, 'export'])->name('leave.export');
Route::get('incoming-payment/export/{id}', [\App\Http\Controllers\Admin\IncomingPaymentController::class, 'export'])->name('incoming-payment.export.id');
Route::get('receivables-payment/export/{id}', [\App\Http\Controllers\Admin\ReceivablesPaymentController::class, 'export'])->name('receivables-payment.export.id');
Route::get('fund-submission/export/{id}', [\App\Http\Controllers\Admin\FundSubmissionController::class, 'export'])->name('fund-submission.export.id');
Route::get('outgoing-payment/export/{id}', [\App\Http\Controllers\Admin\OutgoingPaymentController::class, 'export'])->name('outgoing-payment.export');
Route::get('permission-letter-employee-export/{id}', [\App\Http\Controllers\Admin\PermissionLetterEmployeeController::class, 'export'])->name('permission-letter-employee.export');
Route::get('purchase-request/export/{id?}', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'export'])->name('purchase-request.export.id');
Route::get('purchase-order-transport/export/{id?}', [\App\Http\Controllers\Admin\PurchaseTransportController::class, 'export'])->name('purchase-order-transport.export.id');
Route::get('purchase-order/export/{id?}', [\App\Http\Controllers\Admin\PoTradingController::class, 'export'])->name('purchase-order.export.id');
Route::get('delivery-order/export/{id?}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'export'])->name('delivery-order.export.id');
Route::get('delivery-order/export/{id?}', [\App\Http\Controllers\Admin\DeliveryOrderController::class, 'export'])->name('delivery-order.export.id');
Route::get('delivery-order-general/export/{id?}', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'export'])->name('delivery-order-general.export.id');
Route::get('delivery-order-general/export/{id?}', [\App\Http\Controllers\Admin\DeliveryOrderGeneralController::class, 'export'])->name('delivery-order-general.export.id');
Route::get('invoice-trading/export/{id?}', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'export'])->name('invoice-trading.export.id');
Route::get('invoice-trading/export-receipt/{id?}', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'export_receipt'])->name('invoice-trading.export-receipt.id');
Route::get('invoice-trading/export-tax/{id?}', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'exportTax'])->name('invoice-trading.export-tax.id');
Route::get('invoice-trading/export/{id?}/with-delivery-order', [\App\Http\Controllers\Admin\InvoiceTradingController::class, 'export_with_delivery_orders'])->name('invoice-trading.export.id.with-delivery-order');
Route::get('invoice-general/export/{id?}', [\App\Http\Controllers\Admin\InvoiceGeneralController::class, 'export'])->name('invoice-general.export.id');
Route::get('invoice-general/export-receipt/{id?}', [\App\Http\Controllers\Admin\InvoiceGeneralController::class, 'export_receipt'])->name('invoice-general.export-receipt.id');
Route::get('invoice-general/export-tax/{id?}', [\App\Http\Controllers\Admin\InvoiceGeneralController::class, 'exportTax'])->name('invoice-general.export-tax.id');
Route::get('invoice-general/export/{id?}/with-delivery-order', [\App\Http\Controllers\Admin\InvoiceGeneralController::class, 'export_with_delivery_orders'])->name('invoice-general.export.id.with-delivery-order');
Route::get('purchase-order-general/{id}/export', [\App\Http\Controllers\Admin\PurchaseOrderGeneralController::class, 'export_pdf'])->name('purchase-order-general.export-pdf');
Route::get('purchase-order-service/{id}/export', [\App\Http\Controllers\Admin\PurchaseOrderServiceController::class, 'export_pdf'])->name('purchase-order-service.export-pdf');
Route::get('stock-transfer/{id}/export', [\App\Http\Controllers\Admin\StockTransferController::class, 'export_pdf'])->name('stock-transfer.export-pdf');
Route::get('stock-usage/{id}/export', [\App\Http\Controllers\Admin\StockUsageController::class, 'export'])->name('stock-usage.export');
Route::get('cash-advance-return-customer/{id}/export', [\App\Http\Controllers\Admin\CashAdvancedReturnCustomerController::class, 'export'])->name('cash-advance-return-customer.export');
Route::get('cash-advance-return-vendor/{id}/export', [\App\Http\Controllers\Admin\CashAdvancedReturnVendorController::class, 'export'])->name('cash-advance-return-vendor.export');
Route::get('purchase-return/{id}/export', [\App\Http\Controllers\Admin\PurchaseReturnController::class, 'export_pdf'])->name('purchase-return.export');
Route::get('purchase-request-trading/{id}/export', [\App\Http\Controllers\Admin\PurchaseRequestTradingController::class, 'export'])->name('purchase-request-trading.export');
Route::get('disposition/export/{id?}', [\App\Http\Controllers\Admin\DispositionController::class, 'export'])->name('disposition.export.id');
Route::get('cash-advance-receive/export/{id?}', [\App\Http\Controllers\Admin\CashAdvanceReceiveController::class, 'export'])->name('cash-advance-receive.export.id');
Route::get('cash-advance-receive/export-proforma/{id?}', [\App\Http\Controllers\Admin\CashAdvanceReceiveController::class, 'export_proforma'])->name('cash-advance-receive.export-proforma.id');
Route::get('cash-advance-payment/export/{id?}', [\App\Http\Controllers\Admin\CashAdvancePaymentController::class, 'export'])->name('cash-advance-payment.export.id');
Route::get('cash-advance-payment/export-proforma/{id?}', [\App\Http\Controllers\Admin\CashAdvancePaymentController::class, 'export_proforma'])->name('cash-advance-payment.export-proforma.id');
Route::get('purchase-down-payment/export/{id?}', [\App\Http\Controllers\Admin\PurchaseDownPaymentController::class, 'export'])->name('purchase-down-payment.export.id');
Route::get('invoice-return/{id}/export', [\App\Http\Controllers\Admin\InvoiceReturnController::class, 'export_pdf'])->name('invoice-return.export');
