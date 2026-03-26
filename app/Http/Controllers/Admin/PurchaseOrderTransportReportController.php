<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PurchaseOrderTransport\PurchaseOrderTransportReportDetailExport;
use App\Exports\PurchaseOrderTransport\PurchaseOrderTransportReportExport;
use App\Exports\PurchaseOrderTransport\PurchaseOrderTransportReportReceivingExport;
use App\Exports\PurchaseOrderTransport\SummaryPurchaseOrderTransportReportExport;
use App\Http\Controllers\Controller;
use App\Models\AccountPayable;
use App\Models\PurchaseTransport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderTransportReportController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'purchase-order-transport-report';

    /**
     * Display page from selecting report type or format
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("admin.$this->view_folder.index");
    }

    /**
     * Display report in some of type view
     *
     * @param string $type
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(string $type, Request $request)
    {
        $data = [];

        switch ($type) {
            case "purchase-order-transport":
                $data = $this->getPurchaseOrderTransportReport($request);
                $orientation = 'landscape';
                $paper_size = "a3";
                $excel_export = PurchaseOrderTransportReportExport::class;
                break;
            case "summary-purchase-order-transport":
                $data = $this->getSummaryPurchaseOrderTransport($request);
                $orientation = 'landscape';
                $paper_size = "a3";
                $excel_export = SummaryPurchaseOrderTransportReportExport::class;
                break;
            case "detail-purchase-order-transport":
                $this->validate($request, [
                    'month' => 'required'
                ]);
                $data = $this->getPurchaseOrderTransportReportDetail($request);
                $orientation = 'landscape';
                $paper_size = "a3";
                $excel_export = PurchaseOrderTransportReportDetailExport::class;
                break;
            case "purchase-order-transport-with-receiving":
                $data = $this->getPurchaseOrderTransportReceivingReport($request);
                $orientation = 'landscape';
                $paper_size = "a3";
                $excel_export = PurchaseOrderTransportReportReceivingExport::class;
                break;
            case "debt-due-purchase-order-transport":
                $data = $this->debtDuePurchaseOrderTransport($request);

                $orientation = 'landscape';
                $paper_size = 'a2';
                $excel_export = \App\Exports\PurchaseOrderTransport\DebtDuePurchaseOrderTransportExport::class;
                break;
            default:
                return redirect()->route("admin.purchase-order-transport.report")->with($this->ResponseMessageCRUD(false, "report", "selected report type was not found"));
        }

        $file_path = "admin.$this->view_folder.$type.$request->format";

        if ($request->format == 'preview') {
            return view($file_path, $data);
        } elseif ($request->format == 'pdf') {
            $pdf = Pdf::loadView($file_path, $data)
                ->setPaper($paper_size ?? 'a4', $orientation ?? 'potrait');

            return $pdf->stream($type . '.pdf');
        } elseif ($request->format == 'excel') {
            return Excel::download(new $excel_export($file_path, $data), $type . '.xlsx');
        } else {
            return redirect()->route("admin.purchase-order-transport.report")->with($this->ResponseMessageCRUD(false, "report", "selected export format was not found"));
        }
    }

    /**
     * Get purchase order transport report
     */
    private function getPurchaseOrderTransportReport($request)
    {
        $models = DB::table('purchase_transport_details')
            ->leftJoin('purchase_transports', 'purchase_transports.id', 'purchase_transport_details.purchase_transport_id')
            ->leftJoin('items', 'items.id', 'purchase_transports.item_id')
            ->leftJoin('currencies', 'currencies.id', 'purchase_transports.currency_id')
            ->leftJoin('sale_orders', 'sale_orders.id', 'purchase_transports.so_trading_id')
            ->leftJoin('branches', 'branches.id', 'purchase_transports.branch_id')
            ->leftJoin('vendors', 'vendors.id', 'purchase_transports.vendor_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->whereNull('purchase_transports.deleted_at')
            ->when($request->vendor_id, fn($q) => $q->where('purchase_transports.vendor_id', $request->vendor_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->when($request->item_id, fn($q) => $q->where('purchase_transports.item_id', $request->item_id))
            ->when($request->status, fn($q) => $q->where('purchase_transports.status', $request->status))
            ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('purchase_transports.branch_id', $request->branch_id))
            ->when(!get_current_branch()->is_primary, fn($q) => $q->where('purchase_transports.branch_id', $request->branch_id))
            ->when($request->from_date, fn($q) => $q->whereDate('purchase_transports.target_delivery', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('purchase_transports.target_delivery', '<=', Carbon::parse($request->to_date)))
            ->selectRaw('
                purchase_transports.id as purchase_transport_id,
                purchase_transports.target_delivery as purchase_transport_target_delivery,
                purchase_transports.exchange_rate as purchase_transport_exchange_rate,
                purchase_transports.kode as purchase_transport_code,
                purchase_transports.status as purchase_transport_status,
                purchase_transports.type as purchase_transport_type,
                purchase_transports.harga as purchase_transport_price,
                purchase_transports.sub_total as purchase_transport_sub_total,

                case when purchase_transports.exchange_rate = 1
                    then purchase_transports.sub_total
                    else purchase_transports.sub_total * purchase_transports.exchange_rate
                end as purchase_transport_sub_total_local,

                sale_orders.id as sale_orders_id,
                sale_orders.nomor_so as sale_orders_code,

                customers.nama as customer_name,
                vendors.nama as vendor_name,

                items.nama as item_name,
                items.kode as item_code,

                branches.name as branch_name,

                currencies.nama as currency_name,

                purchase_transport_details.purchase_transport_id as detail_purchase_transport_id,
                purchase_transport_details.jumlah_do as delivery_order_amount,
                purchase_transport_details.jumlah as delivery_order_quantity,
                purchase_transport_details.jumlah_do * purchase_transport_details.jumlah as total_jumlah_do
            ')
            ->get();

        // * process response
        $purchaseTransportIds = $models->unique('purchase_transport_id')->pluck('purchase_transport_id');

        $results = $purchaseTransportIds->map(function ($transportId) use ($models) {
            $result = $models->where('purchase_transport_id', $transportId)->first();

            $result->delivery_order_amount_sum = $models->where('purchase_transport_id', $transportId)->sum('delivery_order_amount');
            $result->delivery_order_quantity_sum = $models->where('purchase_transport_id', $transportId)->sum('total_jumlah_do');

            return $result;
        })->flatten(1);


        return [
            "title" => 'Laporan Pembelian Transport',
            "type" => $request->type,
            "data" => $results,
            'from_date' => localDate($request->from_date),
            'to_date' => localDate($request->to_date),
        ];
    }

    /**
     * Get summary purchase order transport report
     */
    private function getSummaryPurchaseOrderTransport($request)
    {
        $models = DB::table('supplier_invoices')
            ->leftJoin('branches', 'supplier_invoices.branch_id', 'branches.id')
            ->leftJoin('vendors', 'supplier_invoices.vendor_id', 'vendors.id')
            ->leftJoin('supplier_invoice_details', 'supplier_invoice_details.supplier_invoice_id', 'supplier_invoices.id')
            ->leftJoin('purchase_transports', 'purchase_transports.id', 'supplier_invoice_details.reference_id')
            ->leftJoin('sale_orders', 'sale_orders.id', 'purchase_transports.so_trading_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->whereNull('supplier_invoices.deleted_at')
            ->where('supplier_invoice_details.reference_model', PurchaseTransport::class)
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('supplier_invoices.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('supplier_invoices.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('supplier_invoices.vendor_id', $request->vendor_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('supplier_invoices.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('supplier_invoices.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('supplier_invoices.branch_id', get_current_branch_id());
            })
            ->distinct('supplier_invoices.id')
            ->selectRaw('
                supplier_invoices.id,

                vendors.nama as vendor_name,
                customers.nama as customer_name,
                branches.name as branch_name,

                supplier_invoices.exchange_rate,
                supplier_invoices.code,
                supplier_invoices.reference,
                supplier_invoices.tax_reference,
                supplier_invoices.date,
                supplier_invoices.top_due_date,
                supplier_invoices.grand_total as total,

                case when supplier_invoices.exchange_rate = 1
                    then supplier_invoices.grand_total
                    else supplier_invoices.grand_total * supplier_invoices.exchange_rate
                end as total_idr,

                supplier_invoices.payment_status
            ')
            ->get();

        return [
            'data' => $models,
            'from_date' => localDate($request->from_date),
            'to_date' => localDate($request->to_date),
            'type' => 'summary-purchase-order-transport',
            'title' => 'Ringkasan Pembelian transport',
        ];
    }

    /**
     * getReportDetailPurchaseOrderTransportPreviousYear
     */
    private function getReportDetailPurchaseOrderTransportPreviousYear($request, $previous_year)
    {
        $models = DB::table('purchase_transport_details')
            ->leftJoin('purchase_transports', 'purchase_transport_details.purchase_transport_id', 'purchase_transports.id')
            ->leftJoin('items', 'purchase_transports.item_id', 'items.id')
            ->leftJoin('branches', 'branches.id', 'purchase_transports.branch_id')
            ->leftJoin('vendors', 'purchase_transports.vendor_id', 'vendors.id')
            ->leftJoin('sale_orders', 'sale_orders.id', 'purchase_transports.so_trading_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->whereNull('purchase_transports.deleted_at')
            ->whereyear('purchase_transports.target_delivery', $previous_year)
            ->when($request->vendor_id, fn($q) => $q->where('purchase_transports.vendor_id', $request->vendor_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->when($request->item_id, fn($q) => $q->where('purchase_transports.item_id', $request->item_id))
            ->when($request->status, fn($q) => $q->where('purchase_transports.status', $request->status))
            ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('purchase_transports.branch_id', $request->branch_id))
            ->when(!get_current_branch()->is_primary, fn($q) => $q->where('purchase_transports.branch_id', $request->branch_id))
            ->selectRaw('
                purchase_transports.id as purchase_transport_id,
                purchase_transports.target_delivery as purchase_transport_target_delivery,
                purchase_transports.exchange_rate as purchase_transport_exchange_rate,
                purchase_transports.kode as purchase_transport_code,
                purchase_transports.status as purchase_transport_status,
                purchase_transports.type as purchase_transport_type,
                purchase_transports.harga as purchase_transport_price,
                purchase_transports.sub_total as purchase_transport_sub_total,

                case when purchase_transports.exchange_rate = 1
                    then purchase_transports.sub_total
                    else purchase_transports.sub_total * purchase_transports.exchange_rate
                end as purchase_transport_sub_total_local,

                vendors.id as vendor_id,
                vendors.nama as vendor_name,
                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,

                branches.name as branch_name,

                purchase_transport_details.purchase_transport_id as detail_purchase_transport_id,
                purchase_transport_details.jumlah_do as delivery_order_amount,
                purchase_transport_details.jumlah as delivery_order_quantity
            ')
            ->get();

        return $models;
    }

    /**
     * GetReportDetailPurchaseOrderTransportSelectedMonth
     */
    private function getReportDetailPurchaseOrderTransportSelectedMonth($request, $selected_year, $selected_month)
    {
        $models = DB::table('purchase_transport_details')
            ->leftJoin('purchase_transports', 'purchase_transport_details.purchase_transport_id', 'purchase_transports.id')
            ->leftJoin('items', 'purchase_transports.item_id', 'items.id')
            ->leftJoin('branches', 'branches.id', 'purchase_transports.branch_id')
            ->leftJoin('vendors', 'purchase_transports.vendor_id', 'vendors.id')
            ->leftJoin('sale_orders', 'sale_orders.id', 'purchase_transports.so_trading_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->whereNull('purchase_transports.deleted_at')
            ->whereMonth('purchase_transports.target_delivery', $selected_month)
            ->whereyear('purchase_transports.target_delivery', $selected_year)
            ->when($request->vendor_id, fn($q) => $q->where('purchase_transports.vendor_id', $request->vendor_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->when($request->item_id, fn($q) => $q->where('purchase_transports.item_id', $request->item_id))
            ->when($request->status, fn($q) => $q->where('purchase_transports.status', $request->status))
            ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('purchase_transports.branch_id', $request->branch_id))
            ->when(!get_current_branch()->is_primary, fn($q) => $q->where('purchase_transports.branch_id', $request->branch_id))
            ->selectRaw('
                purchase_transports.id as purchase_transport_id,
                purchase_transports.target_delivery as purchase_transport_target_delivery,
                purchase_transports.exchange_rate as purchase_transport_exchange_rate,
                purchase_transports.kode as purchase_transport_code,
                purchase_transports.status as purchase_transport_status,
                purchase_transports.type as purchase_transport_type,
                purchase_transports.harga as purchase_transport_price,
                purchase_transports.sub_total as purchase_transport_sub_total,

                case when purchase_transports.exchange_rate = 1
                    then purchase_transports.sub_total
                    else purchase_transports.sub_total * purchase_transports.exchange_rate
                end as purchase_transport_sub_total_local,

                vendors.id as vendor_id,
                vendors.nama as vendor_name,
                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,

                branches.name as branch_name,

                purchase_transport_details.purchase_transport_id as detail_purchase_transport_id,
                purchase_transport_details.jumlah_do as delivery_order_amount,
                purchase_transport_details.jumlah as delivery_order_quantity
            ')
            ->get();

        return $models;
    }

    /**
     * getReportDetailPurchaseOrderTransportJanuaryUntilSelectedMonth
     */
    private function getReportDetailPurchaseOrderTransportJanuaryUntilSelectedMonth($request, $selected_year, $selected_month)
    {
        $models = DB::table('purchase_transport_details')
            ->leftJoin('purchase_transports', 'purchase_transport_details.purchase_transport_id', 'purchase_transports.id')
            ->leftJoin('items', 'purchase_transports.item_id', 'items.id')
            ->leftJoin('branches', 'branches.id', 'purchase_transports.branch_id')
            ->leftJoin('vendors', 'purchase_transports.vendor_id', 'vendors.id')
            ->leftJoin('sale_orders', 'sale_orders.id', 'purchase_transports.so_trading_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->whereNull('purchase_transports.deleted_at')
            ->whereMonth('purchase_transports.target_delivery', '<=', $selected_month)
            ->whereyear('purchase_transports.target_delivery', $selected_year)
            ->when($request->vendor_id, fn($q) => $q->where('purchase_transports.vendor_id', $request->vendor_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->when($request->item_id, fn($q) => $q->where('purchase_transports.item_id', $request->item_id))
            ->when($request->status, fn($q) => $q->where('purchase_transports.status', $request->status))
            ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('purchase_transports.branch_id', $request->branch_id))
            ->when(!get_current_branch()->is_primary, fn($q) => $q->where('purchase_transports.branch_id', $request->branch_id))
            ->selectRaw('
                purchase_transports.id as purchase_transport_id,
                purchase_transports.target_delivery as purchase_transport_target_delivery,
                purchase_transports.exchange_rate as purchase_transport_exchange_rate,
                purchase_transports.kode as purchase_transport_code,
                purchase_transports.status as purchase_transport_status,
                purchase_transports.type as purchase_transport_type,
                purchase_transports.harga as purchase_transport_price,
                purchase_transports.sub_total as purchase_transport_sub_total,

                case when purchase_transports.exchange_rate = 1
                    then purchase_transports.sub_total
                    else purchase_transports.sub_total * purchase_transports.exchange_rate
                end as purchase_transport_sub_total_local,

                vendors.id as vendor_id,
                vendors.nama as vendor_name,
                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,

                branches.name as branch_name,

                purchase_transport_details.purchase_transport_id as detail_purchase_transport_id,
                purchase_transport_details.jumlah_do as delivery_order_amount,
                purchase_transport_details.jumlah as delivery_order_quantity
            ')
            ->get();

        return $models;
    }

    /**
     * Combine purchase order transport report detail
     */
    private function combinePurchaseTransportDetailReport(array $data)
    {
        list(
            $previous_year_data,
            $selected_month_data,
            $january_until_selected_month_data
        ) = $data;

        $grouped = $selected_month_data->groupBy('vendor_id')->map(function ($vendor) {
            return $vendor->groupBy('customer_id')->map(function ($customer) {
                return $customer->groupBy('item_id');
            });
        });

        $results = $grouped->map(function ($vendor, $vendorKey) use ($previous_year_data, $selected_month_data, $january_until_selected_month_data) {
            $results = $vendor->map(function ($customer, $customerKey) use ($vendorKey, $previous_year_data, $selected_month_data, $january_until_selected_month_data) {
                return $customer->map(function ($item, $itemKey) use ($vendorKey, $customerKey, $previous_year_data, $selected_month_data, $january_until_selected_month_data) {
                    $data = new \stdClass();

                    $data->vendor_id = $vendorKey;
                    $data->vendor_name = $item[0]->vendor_name ?? "Undefined";
                    $data->customer_name = $item[0]->customer_name ?? "Undefined";
                    $data->item_id = $itemKey;
                    $data->item_name = $item[0]->item_name ?? "Undefined";
                    $data->item_code = $item[0]->item_code ?? "Undefined";

                    $data->previous_year_amount_delivery = $previous_year_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('delivery_order_amount');
                    $data->previous_year_quantity = $previous_year_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('delivery_order_quantity');
                    $data->previous_year_sub_total = $previous_year_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('purchase_transport_sub_total');
                    $data->previous_year_sub_total_local = $previous_year_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('purchase_transport_sub_total_local');

                    $data->selected_month_amount_delivery = $selected_month_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('delivery_order_amount');
                    $data->selected_month_quantity = $selected_month_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('delivery_order_quantity');
                    $data->selected_month_sub_total = $selected_month_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('purchase_transport_sub_total');
                    $data->selected_month_sub_total_local = $selected_month_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('purchase_transport_sub_total_local');

                    $data->january_until_selected_month_amount_delivery = $january_until_selected_month_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('delivery_order_amount');
                    $data->january_until_selected_month_quantity = $january_until_selected_month_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('delivery_order_quantity');
                    $data->january_until_selected_month_sub_total = $january_until_selected_month_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('purchase_transport_sub_total');
                    $data->january_until_selected_month_sub_total_local = $january_until_selected_month_data->where('customer_id', $customerKey)->where('vendor_id', $vendorKey)->where('item_id', $itemKey)->sum('purchase_transport_sub_total_local');

                    return $data;
                })->flatten();
            })->flatten();

            return $results;
        })->flatten();

        return $results;
    }

    /**
     * get purchase order transport report detail
     */
    private function getPurchaseOrderTransportReportDetail($request)
    {
        /**
         * --------------------------------------------------------------
         * NOTE
         * --------------------------------------------------------------
         *
         * FIRST COLUMN IS THE ALL DATA (PREVIOUS YEAR)
         * SECOND COLUMN IS THE ALL DATA SELECTED MONTH (THIS YEAR)
         * THIRD COLUMN IS THE ALL DATA JANUARY => SELECTED MONTH (THIS YEAR)
         */

        // * SETTING UP AND GET DATA MONTH, PREVIOUS YEAR, THIS YEAR
        $previous_year = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->subYear()->format('Y');
        $selected_month = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->format('m');
        $selected_year = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->format('Y');

        // ! GET DATA
        $previous_year_data = $this->getReportDetailPurchaseOrderTransportPreviousYear($request, $previous_year);
        $selected_month_data = $this->getReportDetailPurchaseOrderTransportSelectedMonth($request, $selected_year, $selected_month);
        $january_until_selected_month_data = $this->getReportDetailPurchaseOrderTransportJanuaryUntilSelectedMonth($request, $selected_year, $selected_month);

        $results = $this->combinePurchaseTransportDetailReport([
            $previous_year_data,
            $selected_month_data,
            $january_until_selected_month_data,
        ]);

        return [
            'data' => $results,
            'type' => $request->type,
            'title' => "Laporan Detail Purchase Transport",
            'month' => $request->month
        ];
    }

    /**
     * getPurchaseOrderTransportReceivingReport
     */
    private function getPurchaseOrderTransportReceivingReport($request)
    {
        // * GET ITEM RECEIVING REPORT
        $models = DB::table('item_receiving_report_purchase_transports')
            ->leftJoin('item_receiving_reports', 'item_receiving_reports.id', 'item_receiving_report_purchase_transports.item_receiving_report_id')
            ->leftJoin('purchase_transports', 'purchase_transports.id', 'item_receiving_reports.reference_id')
            ->leftJoin('vendors', 'purchase_transports.vendor_id', 'vendors.id')
            ->leftJoin('items', 'purchase_transports.item_id', 'items.id')
            ->leftJoin('branches', 'item_receiving_reports.branch_id', 'branches.id')
            ->leftJoin('sale_orders', 'sale_orders.id', 'purchase_transports.so_trading_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->whereNull('item_receiving_reports.deleted_at')
            ->when($request->from_date, fn($q) => $q->whereDate('item_receiving_reports.date_receive', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('item_receiving_reports.date_receive', '<=', Carbon::parse($request->to_date)))
            ->when($request->status, fn($q) => $q->where('item_receiving_reports.status', $request->status))
            ->when($request->vendor_id, fn($q) => $q->where('item_receiving_reports.vendor_id', $request->vendor_id))
            ->when($request->item_id, fn($q) => $q->where('item_receiving_report_purchase_transports.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->qhere('item_receiving_reports.branch_id', $request->branch_id))
            ->when(!get_current_branch()->is_primary, fn($q) => $q->where('item_receiving_repos.branch_id', get_current_branch()->id))
            ->selectRaw('
                item_receiving_reports.kode as code,
                item_receiving_reports.date_receive as date,
                item_receiving_reports.reference_id as reference_id,

                purchase_transports.kode as purchase_code,
                purchase_transports.exchange_rate as exchange_rate,
                purchase_transports.harga as price,
                item_receiving_reports.status as status,

                vendors.nama as vendor_name,
                customers.nama as customer_name,

                items.nama as item_name,
                items.kode as item_code,

                item_receiving_report_purchase_transports.sended,
                item_receiving_report_purchase_transports.received,
                item_receiving_report_purchase_transports.sub_total,

                case when purchase_transports.exchange_rate = 1
                    then item_receiving_report_purchase_transports.sub_total
                    else item_receiving_report_purchase_transports.sub_total * purchase_transports.exchange_rate
                end as sub_total_local
            ')
            ->get();

        $modelIds = $models->pluck('reference_id')->toArray();

        // * GET TAX DATA
        $purchaseTransportTaxes = DB::table('purchase_transport_taxes')
            ->leftJoin('taxes', 'purchase_transport_taxes.tax_id', 'taxes.id')
            ->whereIn('purchase_transport_taxes.purchase_transport_id', $modelIds)
            ->selectRaw('
                purchase_transport_taxes.purchase_transport_id,
                taxes.id as tax_id,
                taxes.name as tax_name,
                purchase_transport_taxes.value
            ')
            ->get();

        $taxNames = $purchaseTransportTaxes->pluck('tax_name')->unique()->toArray();
        $taxIds = $purchaseTransportTaxes->pluck('tax_id')->unique();
        $taxNameValues = $purchaseTransportTaxes->pluck('tax_name')->unique();

        // * PROCESS RESULT
        $results = $models->map(function ($item) use ($purchaseTransportTaxes, &$taxNameValues) {
            $total_tax = 0;
            $total_tax_local = 0;
            $item->taxes = $purchaseTransportTaxes->where('purchase_transport_id', $item->reference_id)->map(function ($tax) use ($item, &$total_tax, &$total_tax_local, &$taxNameValues) {
                $total_tax += $tax->value * $item->sub_total;
                $total_tax_local += $tax->value * $item->sub_total * $item->exchange_rate;

                $tax->total = $tax->value * $item->sub_total;
                $tax->total_local = $tax->value * $item->sub_total * $item->exchange_rate;

                if ($taxNameValues->where('tax_name', $tax->tax_name)->first()) {
                    $taxNameValues->where('tax_name', $tax->tax_name)->first()->total = $tax->total;
                    $taxNameValues->where('tax_name', $tax->tax_name)->first()->total_local = $tax->total_local;
                }

                return $tax;
            });

            $item->total = $item->sub_total + $total_tax;
            $item->total_local = $item->sub_total_local + $total_tax_local;

            return $item;
        });

        return [
            'data' => $results,
            'taxNames' => $taxNames,
            'taxNameValues' => $taxNameValues,
            'taxIds' => $taxIds,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => $request->type,
            'title' => "Laporan Penerimaan Pembelian Transport"
        ];
    }

    /**
     * debt-due-purchase-order-Transport
     */
    private function debtDuePurchaseOrderTransport($request)
    {
        $supplierInvoiceDetails = DB::table('supplier_invoice_details')
            ->leftJoin('supplier_invoices', 'supplier_invoices.id', 'supplier_invoice_details.supplier_invoice_id')
            ->leftJoin('item_receiving_reports', 'item_receiving_reports.id', 'supplier_invoice_details.item_receiving_report_id')
            ->leftJoin('purchase_transports', 'purchase_transports.id', 'supplier_invoice_details.reference_id')
            ->leftJoin('sale_orders', 'sale_orders.id', 'purchase_transports.so_trading_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->leftJoin('vendors', 'vendors.id', 'purchase_transports.vendor_id')
            ->whereNull('supplier_invoices.deleted_at')
            ->whereNull('supplier_invoice_details.deleted_at')
            ->where('supplier_invoices.status', 'approve')
            ->where('supplier_invoice_details.reference_model', \App\Models\PurchaseTransport::class)
            ->where('supplier_invoices.date', '<=', DB::raw('supplier_invoices.top_due_date'))
            ->when($request->vendor_id, function ($q) use ($request) {
                return $q->where('purchase_transports.vendor_id', $request->vendor_id);
            })
            ->when($request->customer_id, function ($q) use ($request) {
                return $q->where('sale_orders.customer_id', $request->customer_id);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($q) use ($request) {
                return $q->where('supplier_invoices.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($q) {
                return $q->where('supplier_invoices.branch_id', get_current_branch()->id);
            })
            ->selectRaw('
                supplier_invoices.id as supplier_invoice_id,
                supplier_invoice_details.id as supplier_invoice_detail_id,
                supplier_invoices.date as supplier_invoice_date,
                supplier_invoices.top_due_date as supplier_invoice_top_due_date,
                supplier_invoices.code as supplier_invoice_code,
                supplier_invoices.exchange_rate as supplier_invoice_exchange_rate,

                customers.nama as customer_name,
                vendors.nama as vendor_name,

                supplier_invoice_details.total
            ')
            ->get();

        $supplierInvoiceIds = $supplierInvoiceDetails->pluck('supplier_invoice_id')->unique();

        $supplierInvoicePayments = DB::table('supplier_invoice_payments')
            ->whereNUll('supplier_invoice_payments.deleted_at')
            ->where('supplier_invoice_payments.supplier_invoice_model', \App\Models\SupplierInvoice::class)
            ->where('supplier_invoice_payments.supplier_invoice_id', $supplierInvoiceIds->toArray())
            ->selectRaw('
                supplier_invoice_payments.supplier_invoice_id,
                supplier_invoice_payments.exchange_rate,
                supplier_invoice_payments.pay_amount
            ')
            ->get();


        $results = $supplierInvoiceIds->map(function ($supplierInvoiceId) use ($supplierInvoiceDetails, $supplierInvoicePayments) {
            $supplierInvoiceDetail = $supplierInvoiceDetails->where('supplier_invoice_id', $supplierInvoiceId);
            $supplierInvoicePayment = $supplierInvoicePayments->where('supplier_invoice_id', $supplierInvoiceId);

            /**
             * results
             */
            $result = new \stdClass();

            $result->customer = $supplierInvoiceDetail->first()->customer_name;
            $result->vendor = $supplierInvoiceDetail->first()->vendor_name;
            $result->code = $supplierInvoiceDetail->first()->supplier_invoice_code;
            $result->date = $supplierInvoiceDetail->first()->supplier_invoice_date;
            $result->top_due_date = $supplierInvoiceDetail->first()->supplier_invoice_top_due_date;
            $result->exchange_rate = $supplierInvoiceDetail->first()->supplier_invoice_exchange_rate;

            $result->total = $supplierInvoicePayment->sum('total');
            $result->pay_amount = $supplierInvoicePayment->sum('pay_amount');
            $result->outstanding = $result->total - $result->pay_amount;

            return $result;
        });

        return [
            'title' => 'Laporan Hutang Jatuh Tempo Pembelian Transport',
            'type' => 'debt-due-purchase-order-transport',
            'data' => $results,
        ];
    }

    public function history($id, Request $request)
    {
        try {
            $purchase_transports = DB::table('purchase_transports')
                ->where('id', $id)
                ->select(
                    'purchase_transports.id',
                    'purchase_transports.kode as code',
                    'purchase_transports.target_delivery as date',
                    'purchase_transports.status',
                )
                ->get();

            $purchase_transports = $purchase_transports->map(function ($item) {
                $item->link = route('admin.purchase-order-transport.show', $item->id);
                $item->menu = 'purchase order transport';
                return $item;
            });

            $item_receiving_reports = DB::table('item_receiving_reports')
                ->where('reference_model', PurchaseTransport::class)
                ->whereIn('reference_id', $purchase_transports->pluck('id')->toArray())
                ->whereNull('item_receiving_reports.deleted_at')
                ->whereNotIn('item_receiving_reports.status', ['pending', 'revert', 'void', 'reject'])
                ->select(
                    'item_receiving_reports.id',
                    'item_receiving_reports.kode as code',
                    'item_receiving_reports.date_receive as date',
                    'item_receiving_reports.reference_id',
                    'item_receiving_reports.tipe'
                )
                ->get();

            $item_receiving_reports = $item_receiving_reports->map(function ($item) {
                if ($item->tipe == 'jasa') {
                    $item_type = 'item-receiving-report-service';
                } elseif ($item->tipe == 'general') {
                    $item_type = 'item-receiving-report-general';
                } elseif ($item->tipe == 'trading') {
                    $item_type = 'item-receiving-report-trading';
                } elseif ($item->tipe == 'transport') {
                    $item_type = 'item-receiving-report-transport';
                }

                $item->link = route('admin.' . $item_type . '.show', $item->id);
                $item->menu = 'penerimaan barang ' . $item->tipe;
                return $item;
            });

            $purchase_returns = DB::table('purchase_returns')
                ->whereIn('item_receiving_report_id', $item_receiving_reports->pluck('id')->toArray())
                ->whereIn('status', ['approve', 'done'])
                ->whereNull('purchase_returns.deleted_at')
                ->select(
                    'id',
                    'code',
                    'date',
                    'item_receiving_report_id'
                )
                ->get();

            $purchase_returns = $purchase_returns->map(function ($item) {
                $item->link = route('admin.purchase-return.show', $item->id);
                $item->menu = 'retur pembelian';
                return $item;
            });

            $supplier_invoices = DB::table('supplier_invoice_details')
                ->join('supplier_invoices', 'supplier_invoices.id', '=', 'supplier_invoice_details.supplier_invoice_id')
                ->join('supplier_invoice_parents', function ($j) {
                    $j->on('supplier_invoice_parents.reference_id', '=', 'supplier_invoices.id')
                        ->where('supplier_invoice_parents.model_reference', 'App\Models\SupplierInvoice');
                })
                ->whereNull('supplier_invoices.deleted_at')
                ->whereNull('supplier_invoice_details.deleted_at')
                ->whereIn('supplier_invoices.status', ['approve'])
                ->whereIn('supplier_invoice_details.item_receiving_report_id', $item_receiving_reports->pluck('id')->toArray())
                ->select(
                    'supplier_invoices.id',
                    'supplier_invoices.code',
                    'supplier_invoices.accepted_doc_date as date',
                    'supplier_invoice_details.item_receiving_report_id',
                    'supplier_invoice_parents.id as supplier_invoice_parent_id'
                )
                ->get();

            $supplier_invoices = $supplier_invoices->map(function ($item) {
                $item->link = route('admin.supplier-invoice.show', $item->id);
                $item->menu = 'purchase invoice';
                return $item;
            });

            $fund_submissions = DB::table('fund_submission_supplier_details')
                ->join('fund_submissions', 'fund_submissions.id', '=', 'fund_submission_supplier_details.fund_submission_id')
                ->whereNull('fund_submissions.deleted_at')
                ->whereNull('fund_submission_supplier_details.deleted_at')
                ->whereIn('fund_submissions.status', ['approve'])
                ->whereIn('supplier_invoice_parent_id', $supplier_invoices->pluck('supplier_invoice_parent_id')->toArray())
                ->select(
                    'fund_submissions.id',
                    'fund_submissions.code',
                    'fund_submissions.date',
                    'fund_submission_supplier_details.supplier_invoice_parent_id'
                )
                ->get();

            $fund_submissions = $fund_submissions->map(function ($item) {
                $item->link = route('admin.fund-submission.show', $item->id);
                $item->menu = 'pengajuan dana';
                return $item;
            });

            $account_payables = DB::table('account_payable_details')
                ->join('account_payables', 'account_payables.id', '=', 'account_payable_details.account_payable_id')
                ->leftJoin('bank_code_mutations', function ($j) {
                    $j->on('account_payables.id', '=', 'bank_code_mutations.ref_id')
                        ->where('bank_code_mutations.ref_model', AccountPayable::class);
                })
                ->whereNull('account_payable_details.deleted_at')
                ->whereNull('account_payables.deleted_at')
                ->whereIn('account_payables.status', ['approve'])
                ->whereIn('account_payable_details.supplier_invoice_parent_id', $supplier_invoices->pluck('supplier_invoice_parent_id')->toArray())
                ->select(
                    'account_payables.id',
                    'account_payables.code',
                    'bank_code_mutations.code as bank_code_mutation_code',
                    'account_payables.date',
                    'account_payable_details.supplier_invoice_parent_id'
                )
                ->get()
                ->map(function ($item) {
                    $item->code = $item->bank_code_mutation_code ?? $item->code;
                    return $item;
                });

            $account_payables = $account_payables->map(function ($item) {
                $item->link = route('admin.account-payable.show', $item->id);
                $item->menu = 'pelunasan hutang';
                return $item;
            });

            $histories = $purchase_transports->unique('id')
                ->merge($item_receiving_reports->unique('id'))
                ->merge($supplier_invoices->unique('id'))
                ->merge($fund_submissions->unique('id'))
                ->merge($account_payables->unique('id'))
                ->merge($purchase_returns->unique('id'));
            return response()->json([
                'success' => true,
                'data' => $histories
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
