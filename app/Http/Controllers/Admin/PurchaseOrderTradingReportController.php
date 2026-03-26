<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PurchaseOrderTrading\DetailPurchaseOrderTradingReport;
use App\Exports\PurchaseOrderTrading\DetailPurchaseOrderTradingReportWithReceiving;
use App\Exports\PurchaseOrderTrading\PurchaseOrderTradingOutstandingExport;
use App\Exports\PurchaseOrderTrading\PurchaseOrderTradingReport;
use App\Exports\PurchaseOrderTrading\StockComparisonPurchaseOrderTradingOutstandingExport;
use App\Exports\PurchaseOrderTrading\SummaryPurchaseOrderTradingReport;
use App\Http\Controllers\Controller;
use App\Models\PoTrading;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderTradingReportController extends Controller
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
    protected string $view_folder = 'purchase-order-trading-report';

    /**
     * where the route will be defined
     *
     * @var string
     */
    protected string $route = 'purchase-order-trading';

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
            case "purchase-order-trading":
                $data = $this->reportPurchaseOrderTrading($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = PurchaseOrderTradingReport::class;
                break;
            case "summary-purchase-order-trading":
                $data = $this->reportPurchaseOrderTradingSummary($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SummaryPurchaseOrderTradingReport::class;
                break;
            case "detail-purchase-order-trading":
                $this->validate($request, [
                    'month' => 'required'
                ]);

                $data = $this->reportPurchaseOrderTradingDetail($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = DetailPurchaseOrderTradingReport::class;
                break;
            case "purchase-order-trading-with-receiving":

                $data = $this->reportPurchaseOrderTradingDetailWithReceiving($request);

                $orientation = 'landscape';
                $paper_size = 'a2';
                $excel_export = DetailPurchaseOrderTradingReportWithReceiving::class;
                break;

            case "purchase-order-trading-outstanding":
                $data = $this->purchaseOrderTradingOutstanding($request);

                $orientation = 'landscape';
                $paper_size = 'a2';
                $excel_export = PurchaseOrderTradingOutstandingExport::class;
                break;

            case "stock-comparison-purchase-order-trading-outstanding":
                $data = $this->stockComparisonPurchaseOrderTradingOutstanding($request);

                $orientation = 'landscape';
                $paper_size = 'a2';
                $excel_export = StockComparisonPurchaseOrderTradingOutstandingExport::class;
                break;
            case  "summary-purchase-return-purchase-order-trading":
                $data = $this->summaryPurchaseReturnPurchaseOrderTrading($request);

                $orientation = 'landscape';
                $paper_size = 'a2';
                $excel_export = \App\Export\PurchaseOrderTrading\SummaryPurchaseReturnPurchaseOrderTradingExport::class;
                break;
            case "debt-due-purchase-order-trading":
                $data = $this->debtDuePurchaseOrderTrading($request);

                $orientation = 'landscape';
                $paper_size = 'a2';
                $excel_export = \App\Exports\PurchaseOrderTrading\DebtDuePurchaseOrderTradingExport::class;
                break;

            default:
                return redirect()->route("admin.purchase-order-trading.report")->with($this->ResponseMessageCRUD(false, "report", "selected report type was not found"));
        }

        // return $data;
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
            return redirect()->route("admin.$this->route.report")->with($this->ResponseMessageCRUD(false, "report", "selected export format was not found"));
        }
    }

    /**
     * Get data for purchase order trading report
     *
     * @param $request
     * @return array
     */
    private function reportPurchaseOrderTrading($request): array
    {
        $model = DB::table('purchase_orders')
            ->leftJoin('purchase_order_details', 'purchase_order_details.po_trading_id', '=', 'purchase_orders.id')
            ->leftJoin('customers', 'customers.id', '=', 'purchase_orders.customer_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'purchase_orders.vendor_id')
            ->leftJoin('sh_numbers', 'sh_numbers.id', '=', 'purchase_orders.sh_number_id')
            ->leftJoin('items', 'items.id', '=', 'purchase_order_details.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->whereNull('purchase_orders.deleted_at')
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('purchase_orders.tanggal', '>=', Carbon::parse(Carbon::parse($request->from_date)));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('purchase_orders.tanggal', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_details.item_id', $request->item_id);
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('purchase_orders.customer_id', $request->customer_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_orders.vendor_id', $request->vendor_id);
            })
            ->when($request->sh_number_id, function ($query) use ($request) {
                return $query->where('purchase_orders.sh_number_id', $request->sh_number_id);
            })
            ->when(in_array($request->status, status_purchase_orders()), function ($query) use ($request) {
                return $query->where('purchase_orders.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_orders.branch_id', get_current_branch()->id);
            })
            ->distinct('purchase_orders.id')
            ->selectRaw('
                purchase_orders.id,
                purchase_orders.tanggal as date,
                purchase_orders.nomor_po as code,
                purchase_orders.status,
                purchase_orders.sale_confirmation,
                purchase_orders.exchange_rate,

                customers.nama as customer_name,
                vendors.nama as vendor_name,
                sh_numbers.kode as sh_number_code,

                items.nama as item_name,
                items.kode as item_code,

                purchase_order_details.type as unit,
                purchase_order_details.jumlah as quantity,
                purchase_order_details.discount_per_liter as discount_per_liter,
                purchase_order_details.harga as price,

                purchase_orders.sub_total,
                purchase_orders.other_cost,
                purchase_orders.total,

                case when purchase_orders.exchange_rate = 1
                    then purchase_orders.sub_total
                    else purchase_orders.sub_total * purchase_orders.exchange_rate
                end as sub_total_idr,

                case when purchase_orders.exchange_rate = 1
                    then purchase_orders.total
                    else purchase_orders.total * purchase_orders.exchange_rate
                end as total_idr

            ')
            ->get();

        $purchase_order_taxes = DB::table('purchase_order_taxes')
            ->whereIn('po_trading_id', $model->pluck('id')->toArray())
            ->select(
                'purchase_order_taxes.po_trading_id',
                'purchase_order_taxes.total',
            )
            ->get();

        $model = $model->map(function ($item) use ($purchase_order_taxes) {
            $item->total_tax = $purchase_order_taxes->where('po_trading_id', $item->id)->sum('total');
            $item->total_tax_idr = $item->total_tax * $item->exchange_rate;

            return $item;
        });

        $total_all = new \stdClass();
        $total_all->quantity = $model->sum('quantity');
        $total_all->sub_total = $model->sum('sub_total');
        $total_all->sub_total_idr = $model->sum('sub_total_idr');
        $total_all->other_cost = $model->sum('other_cost');
        $total_all->total = $model->sum('total');
        $total_all->total_idr = $model->sum('total_idr');
        $total_all->total_tax = $model->sum('total_tax');

        return [
            'total_all' => $total_all,
            'data' => $model,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => 'pembelian-trading',
        ];
    }

    /**
     * Get data for purchase order trading summary report
     *
     * @param $request
     * @return array
     */
    private function reportPurchaseOrderTradingSummary($request): array
    {
        $model = DB::table('supplier_invoices')
            ->leftJoin('vendors', 'vendors.id', '=', 'supplier_invoices.vendor_id')
            ->leftJoin('branches', 'branches.id', '=', 'supplier_invoices.branch_id')
            ->leftJoin('supplier_invoice_details', 'supplier_invoice_details.supplier_invoice_id', '=', 'supplier_invoices.id')
            ->whereNull('supplier_invoices.deleted_at')
            ->where('supplier_invoice_details.reference_model', PoTrading::class)
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('supplier_invoices.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('supplier_invoices.date', '<=', Carbon::parse($request->to_date));
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('supplier_invoices.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('supplier_invoices.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('supplier_invoices.branch_id', get_current_branch()->id);
            })
            ->distinct('supplier_invoices.id')
            ->selectRaw('
                supplier_invoices.id,
                vendors.nama as vendor_name,
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

        $total_all = new \stdClass();
        $total_all->total = $model->sum('total');
        $total_all->total_idr = $model->sum('total_idr');

        return [
            'total_all' => $total_all,
            'data' => $model,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => 'ringkasan-pembelian-trading',
        ];
    }

    /**
     * Get data purchase order trading report previous year
     *
     * @param $request
     * @param $year
     * @return object
     */
    private function getReportPurchaseOrderTradingPreviousYear($request, $year): object
    {
        $model = DB::table('purchase_orders')
            ->leftJoin('purchase_order_details', 'purchase_order_details.po_trading_id', '=', 'purchase_orders.id')
            ->leftJoin('customers', 'customers.id', '=', 'purchase_orders.customer_id')
            ->leftJoin('sh_numbers', 'sh_numbers.id', '=', 'purchase_orders.sh_number_id')
            ->leftJoin('items', 'items.id', '=', 'purchase_order_details.item_id')
            ->whereNull('purchase_orders.deleted_at')
            ->whereYear('purchase_orders.tanggal', $year)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_details.item_id', $request->item_id);
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('purchase_orders.customer_id', $request->customer_id);
            })
            ->when($request->sh_number_id, function ($query) use ($request) {
                return $query->where('purchase_orders.sh_number_id', $request->sh_number_id);
            })
            ->when(in_array($request->status, status_purchase_orders()), function ($query) use ($request) {
                return $query->where('purchase_orders.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_orders.branch_id', get_current_branch()->id);
            })
            ->distinct('purchase_orders.id')
            ->selectRaw('
                purchase_orders.id,
                purchase_orders.exchange_rate,

                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,

                case when purchase_order_details.type = "Liter"
                    then purchase_order_details.jumlah
                    else purchase_order_details.jumlah * 1000
                end as quantity,

                purchase_orders.sub_total,

                case when purchase_orders.exchange_rate = 1
                    then purchase_orders.sub_total
                    else purchase_orders.sub_total * purchase_orders.exchange_rate
                end as sub_total_idr
            ')
            ->get();

        return $model;
    }

    /**
     * Get data purchase order trading report selected month
     *
     * @param $request
     * @param $year
     * @param $month
     * @return object
     */
    private function getReportPurchaseOrderTradingSelectedMonth($request, $year, $month): object
    {
        $model = DB::table('purchase_orders')
            ->leftJoin('purchase_order_details', 'purchase_order_details.po_trading_id', '=', 'purchase_orders.id')
            ->leftJoin('customers', 'customers.id', '=', 'purchase_orders.customer_id')
            ->leftJoin('sh_numbers', 'sh_numbers.id', '=', 'purchase_orders.sh_number_id')
            ->leftJoin('items', 'items.id', '=', 'purchase_order_details.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->whereNull('purchase_orders.deleted_at')
            ->whereYear('purchase_orders.tanggal', $year)
            ->whereMonth('purchase_orders.tanggal', $month)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_details.item_id', $request->item_id);
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('purchase_orders.customer_id', $request->customer_id);
            })
            ->when($request->sh_number_id, function ($query) use ($request) {
                return $query->where('purchase_orders.sh_number_id', $request->sh_number_id);
            })
            ->when(in_array($request->status, status_purchase_orders()), function ($query) use ($request) {
                return $query->where('purchase_orders.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_orders.branch_id', get_current_branch()->id);
            })
            ->distinct('purchase_orders.id')
            ->selectRaw('
                purchase_orders.id,
                purchase_orders.exchange_rate,

                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,

                case when purchase_order_details.type = "Liter"
                    then purchase_order_details.jumlah
                    else purchase_order_details.jumlah * 1000
                end as quantity,

                purchase_orders.sub_total,

                case when purchase_orders.exchange_rate = 1
                    then purchase_orders.sub_total
                    else purchase_orders.sub_total * purchase_orders.exchange_rate
                end as sub_total_idr
            ')
            ->get();

        return $model;
    }

    /**
     * Get data purchase order trading report january until selected month
     *
     * @param $request
     * @param $year
     * @param $month
     * @return object
     */
    private function getReportPurchaseOrderTradingJanuaryUntilSelectedMonth($request, $year, $month): object
    {
        $model = DB::table('purchase_orders')
            ->leftJoin('purchase_order_details', 'purchase_order_details.po_trading_id', '=', 'purchase_orders.id')
            ->leftJoin('customers', 'customers.id', '=', 'purchase_orders.customer_id')
            ->leftJoin('sh_numbers', 'sh_numbers.id', '=', 'purchase_orders.sh_number_id')
            ->leftJoin('items', 'items.id', '=', 'purchase_order_details.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->whereNull('purchase_orders.deleted_at')
            ->whereYear('purchase_orders.tanggal', $year)
            ->whereMonth('purchase_orders.tanggal', '<=', $month)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_details.item_id', $request->item_id);
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('purchase_orders.customer_id', $request->customer_id);
            })
            ->when($request->sh_number_id, function ($query) use ($request) {
                return $query->where('purchase_orders.sh_number_id', $request->sh_number_id);
            })
            ->when(in_array($request->status, status_purchase_orders()), function ($query) use ($request) {
                return $query->where('purchase_orders.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_orders.branch_id', get_current_branch()->id);
            })
            ->distinct('purchase_orders.id')
            ->selectRaw('
                purchase_orders.id,
                purchase_orders.exchange_rate,

                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,

                case when purchase_order_details.type = "Liter"
                    then purchase_order_details.jumlah
                    else purchase_order_details.jumlah * 1000
                end as quantity,

                purchase_orders.sub_total,

                case when purchase_orders.exchange_rate = 1
                    then purchase_orders.sub_total
                    else purchase_orders.sub_total * purchase_orders.exchange_rate
                end as sub_total_idr
            ')
            ->get();

        return $model;
    }

    /**
     * Combine data purchase order trading detail report
     *
     * @param array $data
     * @return array
     */
    private function combinePurchaseOrderTradingDetailReport(array $data): array
    {
        list($previous_year, $selected_month, $data_january_until_selected_month) = $data;

        // * group by customer and item
        $grouped = $selected_month->groupBy('customer_id')->map(function ($item) {
            $customer_name = '';
            $customer_id = '';

            // * get detail item
            $data = $item->map(function ($item_detail) use (&$customer_name, &$customer_id) {
                $customer_name = $item_detail->customer_name;
                $customer_id = $item_detail->customer_id;
                return $item_detail;
            })->groupBy('item_id');

            // * return data with grupp by item
            return collect([
                'customer_name' => $customer_name,
                'customer_id' => $customer_id,
                'detail' => $data
            ]);
        });

        // * set total all
        $total_all = new \stdClass();
        $total_all->january_until_selected_month_quantity = 0;
        $total_all->january_until_selected_month_sub_total_idr = 0;
        $total_all->selected_month_quantity = 0;
        $total_all->selected_month_sub_total_idr = 0;
        $total_all->previous_year_quantity = 0;
        $total_all->previous_year_sub_total_idr = 0;

        $results = $grouped->map(function ($customer_group) use (&$total_all, $previous_year, $selected_month, $data_january_until_selected_month) {
            $detail = collect($customer_group['detail'])->map(function ($item) use (&$total_all, $previous_year, $selected_month, $data_january_until_selected_month) {
                // * GET DATA SELECTED MONTH
                $selected_month_data = $selected_month->where('customer_id', $item->first()->customer_id)
                    ->where('item_id', $item->first()->item_id)
                    ->all();

                // * GET DATA PREVIOUS YEAR
                $previous_year_data = $previous_year->where('customer_id', $item->first()->customer_id)
                    ->where('item_id', $item->first()->item_id)
                    ->all();

                // * GET DATA JANUARY UNTIL SELECTED MONTH
                $january_until_selected_month = $data_january_until_selected_month->where('customer_id', $item->first()->customer_id)
                    ->where('item_id', $item->first()->item_id)
                    ->all();

                // * TOTAL ALL
                $total_all->january_until_selected_month_quantity += array_sum(array_column($january_until_selected_month, 'quantity'));
                $total_all->january_until_selected_month_sub_total_idr += array_sum(array_column($january_until_selected_month, 'sub_total_idr'));
                $total_all->selected_month_quantity += array_sum(array_column($selected_month_data, 'quantity'));
                $total_all->selected_month_sub_total_idr += array_sum(array_column($selected_month_data, 'sub_total_idr'));
                $total_all->previous_year_quantity += array_sum(array_column($previous_year_data, 'quantity'));
                $total_all->previous_year_sub_total_idr += array_sum(array_column($previous_year_data, 'sub_total_idr'));

                return [
                    'item_id' => $item->first()->item_id,
                    'item_code' => $item->first()->item_code,
                    'item_name' => $item->first()->item_name,
                    'january_until_selected_month_quantity' => array_sum(array_column($january_until_selected_month, 'quantity')),
                    'january_until_selected_month_sub_total_idr' => array_sum(array_column($january_until_selected_month, 'sub_total_idr')),
                    'selected_month_quantity' => array_sum(array_column($selected_month_data, 'quantity')),
                    'selected_month_sub_total_idr' => array_sum(array_column($selected_month_data, 'sub_total_idr')),
                    'previous_year_quantity' => array_sum(array_column($previous_year_data, 'quantity')),
                    'previous_year_sub_total_idr' => array_sum(array_column($previous_year_data, 'sub_total_idr')),
                ];
            });

            return [
                'customer_name' => $customer_group['customer_name'],
                'customer_id' => $customer_group['customer_id'],
                'detail' => $detail
            ];
        });

        return [
            'result' => $results,
            'total_all' => $total_all
        ];
    }

    /**
     * Get data for purchase order trading detail report
     *
     * @param $request
     * @return array
     */
    private function reportPurchaseOrderTradingDetail($request): array
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

        // ! GET DATA ============================================================================================================
        $data_previous_year = $this->getReportPurchaseOrderTradingPreviousYear($request, $previous_year);
        $data_selected_month = $this->getReportPurchaseOrderTradingSelectedMonth($request, $selected_year, $selected_month);
        $data_january_until_selected_month = $this->getReportPurchaseOrderTradingJanuaryUntilSelectedMonth($request, $selected_year, $selected_month);
        // ! END GET DATA ============================================================================================================

        // ! COMBINE DATA ============================================================================================================
        $data = $this->combinePurchaseOrderTradingDetailReport([
            $data_previous_year,
            $data_selected_month,
            $data_january_until_selected_month
        ]);

        return [
            'data' => $data['result'],
            'total_all' => $data['total_all'],
            'from_date' => Carbon::createFromFormat('m-Y', $request->month)->startOfMonth()->format('Y-m-d'),
            'to_date' => Carbon::createFromFormat('m-Y', $request->month)->endOfMonth()->format('Y-m-d'),
            'type' => 'detail-purchase-order-trading',
        ];
    }

    /**
     * Get data for purchase order trading detail report with receiving
     *
     * @param $request
     * @return array
     */
    private function reportPurchaseOrderTradingDetailWithReceiving($request): array
    {
        // ! PARENT DATA ==============================================================================================================
        $model = DB::table('item_receiving_reports')
            ->leftJoin('branches', 'branches.id', '=', 'item_receiving_reports.branch_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'item_receiving_reports.reference_id')
            ->leftJoin('customers', 'customers.id', '=', 'purchase_orders.customer_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'purchase_orders.vendor_id')
            ->leftJoin('sh_numbers', 'sh_numbers.id', '=', 'purchase_orders.sh_number_id')
            ->leftJoin('purchase_order_details', 'purchase_order_details.po_trading_id', '=', 'purchase_orders.id')
            ->leftJoin('item_receiving_po_tradings', 'item_receiving_po_tradings.item_receiving_report_id', '=', 'item_receiving_reports.id')
            ->leftJoin('item_receiving_po_trading_additionals', 'item_receiving_reports.id', 'item_receiving_po_trading_additionals.item_receiving_report_id')
            ->leftJoin('items', 'items.id', '=', 'item_receiving_po_tradings.item_id')
            ->where('tipe', 'trading')
            ->whereNUll('item_receiving_reports.deleted_at')
            ->where('item_receiving_reports.reference_model', PoTrading::class)
            ->whereNotIn('item_receiving_reports.status', ['pending', 'revert', 'void', 'reject'])
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('item_receiving_reports.date_receive', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('item_receiving_reports.date_receive', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('item_receiving_po_tradings.item_id', $request->item_id);
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('purchase_orders.customer_id', $request->customer_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_orders.vendor_id', $request->vendor_id);
            })
            ->when($request->sh_number_id, function ($query) use ($request) {
                return $query->where('purchase_orders.sh_number_id', $request->sh_number_id);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('item_receiving_reports.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('item_receiving_reports.branch_id', get_current_branch()->id);
            })
            ->distinct('item_receiving_reports.id')
            ->selectRaw('
                item_receiving_reports.id,
                item_receiving_reports.date_receive,
                purchase_orders.id as purchase_order_id,
                purchase_orders.nomor_po as purchase_order_code,
                customers.nama as customer_name,
                vendors.nama as vendor_name,
                sh_numbers.kode as sh_number_code,
                branches.name as branch_name,
                item_receiving_reports.kode as code,
                purchase_orders.exchange_rate,
                item_receiving_reports.status,
                items.nama as item_name,

                item_receiving_po_tradings.liter_15 as quantity,
                item_receiving_po_trading_additionals.total as total_additional,
                item_receiving_po_trading_additionals.total * purchase_orders.exchange_rate as total_additional_idr,
                purchase_order_details.harga as price,
                purchase_order_details.discount_per_liter as discount
            ')
            ->get();
        // ! END PARENT DATA ==============================================================================================================

        // ! PURCHASE ORDER TAX DATA ==============================================================================================================
        $purchase_order_ids = $model->pluck('purchase_order_id')->toArray();
        $purchase_order_tax_data = DB::table('purchase_order_taxes')
            ->whereIn('po_trading_id', $purchase_order_ids)
            ->leftJoin('taxes', 'taxes.id', '=', 'purchase_order_taxes.tax_id')
            ->leftJoin('taxes as tax_tradings', 'tax_tradings.id', '=', 'purchase_order_taxes.tax_trading_id')
            ->selectRaw('
                purchase_order_taxes.id,
                purchase_order_taxes.tax_id,
                purchase_order_taxes.tax_trading_id,
                purchase_order_taxes.po_trading_id,
                purchase_order_taxes.value,
                taxes.name as tax_name,
                taxes.category as tax_category,
                tax_tradings.name as tax_trading_name
            ')
            ->get();
        // ! END PURCHASE ORDER TAX DATA ==============================================================================================================

        $get_unique_tax_data = $purchase_order_tax_data->unique('tax_id')->pluck('tax_id')->toArray();
        $taxes = DB::table('taxes')->whereIn('id', $get_unique_tax_data)->get();

        $get_unique_tax_trading_data = $purchase_order_tax_data->unique('tax_trading_id')->pluck('tax_trading_id')->toArray();
        $tax_tradings = DB::table('taxes')->whereIn('id', $get_unique_tax_trading_data)->get();

        // ! CALCULATE ==============================================================================================================

        // * TOTAL ALL

        $total_all = new \stdClass();
        $total_all->sub_total = 0;
        $total_all->sub_total_idr = 0;
        $total_all->tax_total = 0;
        $total_all->tax_total_idr = 0;
        $total_all->total_additional = 0;
        $total_all->total_additional_idr = 0;
        $total_all->total = 0;
        $total_all->total_idr = 0;
        $total_all->quantity = 0;

        foreach ($taxes as $key => $tax) {
            $tax_name = strtolower($tax->category ?? $tax->name);
            $tax_name = str_replace(' ', '_', $tax_name);

            $total_all->$tax_name = 0;
        }

        foreach ($tax_tradings as $key => $tax_trading) {
            $tax_trading_name = strtolower($tax_trading->name);
            $tax_trading_name = str_replace(' ', '_', $tax_trading_name);

            $total_all->$tax_trading_name = 0;
        }


        $results = $model->map(function ($item) use ($purchase_order_tax_data, $total_all, $taxes) {
            $tax_data = $purchase_order_tax_data->where('po_trading_id', $item->purchase_order_id)->all();

            $sub_total = $item->quantity * ($item->price - $item->discount);
            $sub_total_idr = $sub_total * $item->exchange_rate;

            $total = $sub_total;
            $total_idr = $sub_total_idr;

            $tax_list = [];

            $tax_total = 0;
            $tax_total_idr = 0;
            foreach ($tax_data as $key => $tax) {
                $tax_name = strtolower($tax->tax_category ?? $tax->tax_name ?? $tax->tax_trading_name);
                $tax_name = str_replace(' ', '_', $tax_name);

                $item->$tax_name = 0;
            }

            foreach ($tax_data as $key => $tax) {
                if (is_null($tax->tax_id)) {
                    $calculate_tax = (($item->price - $item->discount) * $tax->value) * $item->quantity;
                    $tax_total += $calculate_tax;
                    $tax_total_idr += $calculate_tax * $item->exchange_rate;
                } else {
                    $calculate_tax = ($tax->value * $item->price) * $item->quantity;
                    $tax_total += $calculate_tax;
                    $tax_total_idr += ($calculate_tax) * $item->exchange_rate;
                }

                $tax_name = strtolower($tax->tax_category ?? $tax->tax_name ?? $tax->tax_trading_name);
                $tax_name = str_replace(' ', '_', $tax_name);
                $item->$tax_name += $calculate_tax;
                $total_all->$tax_name += $calculate_tax;
            }

            $total += $tax_total;
            $total_idr += $tax_total_idr;

            $item->tax_list = $tax_list;
            $item->sub_total = $sub_total;
            $item->sub_total_idr = $sub_total_idr;
            $item->tax_total = $tax_total;
            $item->tax_total_idr = $tax_total_idr;
            $item->total = $total;
            $item->total_idr = $total_idr;

            $total_all->quantity += $item->quantity;
            $total_all->sub_total += $sub_total;
            $total_all->sub_total_idr += $sub_total_idr;
            $total_all->tax_total += $tax_total;
            $total_all->tax_total_idr += $tax_total_idr;
            $total_all->total_additional = $item->total_additional;
            $total_all->total_additional_idr = $item->total_additional * $item->exchange_rate;
            $total_all->total += $total;
            $total_all->total_idr += $total_idr;

            return $item;
        });
        // ! END CALCULATE ==============================================================================================================

        $taxes_name = [];
        $unique_tax_names = $taxes->unique('category')->pluck('category')->toArray();
        foreach ($unique_tax_names as $key => $unique_tax_name) {
            array_push($taxes_name, strtolower(str_replace(' ', '_', $unique_tax_name)));
        }
        $unique_tax_trading_names = $tax_tradings->pluck('name')->toArray();
        foreach ($unique_tax_trading_names as $key => $unique_tax_trading_name) {
            array_push($taxes_name, strtolower(str_replace(' ', '_', $unique_tax_trading_name)));
        }

        return [
            'data' => $results,
            'total_all' => $total_all,
            'type' => 'detail-penerimaan-pembelian-trading',
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'taxes' => $taxes_name,
            'format' => $request->format,
        ];
    }
    /**
     * purchase-order-trading-outstanding
     */
    private function purchaseOrderTradingOutstanding($request)
    {
        $models = DB::table('purchase_orders')
            ->leftJoin('customers', 'customers.id', 'purchase_orders.customer_id')
            ->leftJoin('vendors', 'vendors.id', 'purchase_orders.vendor_id')
            ->leftJoin('sh_numbers', 'sh_numbers.id', 'purchase_orders.sh_number_id')
            ->leftJoin('purchase_order_details', 'purchase_order_details.po_trading_id', 'purchase_orders.id')
            ->leftJoin('items', 'items.id', 'purchase_order_details.item_id')
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('purchase_orders.tanggal', '>=', Carbon::parse(Carbon::parse($request->from_date)));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('purchase_orders.tanggal', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('purchase_orders.customer_id', $request->customer_id);
            })
            ->when($request->sh_number_id, function ($query) use ($request) {
                return $query->where('purchase_orders.sh_number_id', $request->sh_number_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_orders.vendor_id', $request->vendor_id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_details.item_id', $request->item_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_orders.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_orders.branch_id', get_current_branch()->id);
            })
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('purchase_orders.tanggal', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('purchase_orders.tanggal', '<=', Carbon::parse($request->to_date));
            })
            ->whereNull('purchase_orders.deleted_at')
            ->where('purchase_order_details.jumlah', '>', DB::raw('purchase_order_details.jumlah_lpbs'))
            ->selectRaw('
                purchase_orders.nomor_po as code,
                purchase_orders.tanggal as date,

                customers.nama as customer_name,
                vendors.nama as vendor_name,

                items.nama as item_name,
                items.kode as item_code,

                purchase_order_details.jumlah as quantity,
                purchase_order_details.jumlah_lpbs as quantity_outstanding,

                purchase_orders.status
            ')
            ->get();

        return [
            'title' => 'Laporan Pembelian Trading Outstanding',
            'data' => $models,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => 'purchase-order-trading-outstanding',
        ];
    }

    /**
     * stock-comparison-purchase-order-trading-outstanding
     */
    private function stockComparisonPurchaseOrderTradingOutstanding($request)
    {
        $items = DB::table('items')
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('id', $request->item_id);
            })
            ->where('type', 'trading')
            ->get();

        $stocksMutations = DB::table('stock_mutations')
            ->whereNull('stock_mutations.deleted_at')
            ->whereIn('stock_mutations.item_id', $items->pluck('id'))
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('stock_mutations.date', '<=', Carbon::parse($request->to_date));
            })
            ->get();

        $purchase_order_details = DB::table('purchase_order_details')
            ->join('purchase_orders', 'purchase_order_details.po_trading_id', 'purchase_orders.id')
            ->leftJoin('item_receiving_reports', function ($item_receiving_report_detail) use ($request) {
                $item_receiving_report_detail->on('item_receiving_reports.reference_id', '=', 'purchase_orders.id')
                    ->where('item_receiving_reports.reference_model', 'App\Models\PoTrading')
                    ->whereNotIn('item_receiving_reports.status', ['pending', 'revert', 'void', 'reject'])
                    ->whereNull('item_receiving_reports.deleted_at')
                    ->when($request->to_date, function ($query) use ($request) {
                        return $query->whereDate('item_receiving_reports.date_receive', '<=', Carbon::parse($request->to_date));
                    })
                    ->join('item_receiving_po_tradings', function ($item_receiving_po_trading) use ($request) {
                        $item_receiving_po_trading->on('item_receiving_po_tradings.item_receiving_report_id', '=', 'item_receiving_reports.id');
                    });
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_orders.vendor_id', $request->vendor_id);
            })
            ->whereNull('purchase_orders.deleted_at')
            ->whereIn('purchase_orders.status', ['approve', 'done', 'ready'])
            ->whereIn('purchase_order_details.item_id', $items->pluck('id'))
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('purchase_orders.tanggal', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('purchase_orders.tanggal', '<=', Carbon::parse($request->to_date));
            })
            ->selectRaw(
                'purchase_order_details.item_id, purchase_order_details.jumlah,
                purchase_orders.nomor_po, COALESCE(SUM(item_receiving_po_tradings.liter_obs),0) as total_received,
                purchase_order_details.jumlah - COALESCE(SUM(item_receiving_po_tradings.liter_obs),0) as total_outstanding'
            )
            ->groupBy('purchase_order_details.id')
            ->get();

        $items = $items->map(function ($item) use ($stocksMutations, $purchase_order_details) {
            $item->po_qty = $purchase_order_details->where('item_id', $item->id)->sum('jumlah');
            $item->realization_qty = $purchase_order_details->where('item_id', $item->id)->sum('total_received');
            $item->po_outstanding_qty = $item->po_qty - $item->realization_qty;
            $item->stock = $stocksMutations->where('item_id', $item->id)->sum('in') - $stocksMutations->where('item_id', $item->id)->sum('out');
            $item->total = $item->stock + $item->po_outstanding_qty;

            return $item;
        });

        return [
            'title' => 'Laporan Perbandingan Stock Pembelian Trading Outstanding',
            'data' => $items,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => 'stock-comparison-purchase-order-trading-outstanding',
        ];
    }

    /**
     * summary-purchase-return-purchase-order-trading
     */
    private function summaryPurchaseReturnPurchaseOrderTrading($request)
    {
        $models = DB::table('purchase_return_details')
            ->leftJoin('purchase_returns', 'purchase_returns.id', 'purchase_return_details.purchase_return_id')
            ->leftJoin('item_receiving_reports', 'item_receiving_reports.id', 'purchase_returns.item_receiving_report_id')
            ->leftJoin('vendors', 'vendors.id', 'purchase_returns.vendor_id')
            ->leftJoin('ware_houses', 'ware_houses.id', 'purchase_returns.ware_house_id')
            ->leftJoin('items', 'items.id', 'purchase_return_details.item_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', 'item_receiving_reports.reference_id')
            ->leftJoin('customers', 'customers.id', 'purchase_orders.customer_id')
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('purchase_orders.customer_id', $request->customer_id);
            })
            ->when($request->sh_number_id, function ($query) use ($request) {
                return $query->where('purchase_orders.sh_number_id', $request->sh_number_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_returns.vendor_id', $request->vendor_id);
            })
            ->when($request->ware_house_id, function ($query) use ($request) {
                return $query->where('purchase_returns.ware_house_id', $request->ware_house_id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_return_details.item_id', $request->item_id);
            })
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('purchase_returns.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('purchase_returns.date', '<=', Carbon::parse($request->to_date));
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_returns.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_returns.branch_id', get_current_branch()->id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_returns.status', $request->status);
            })
            ->whereNull('purchase_returns.deleted_at')
            ->where('item_receiving_reports.tipe', 'trading')
            ->selectRaw('
                purchase_returns.id,
                purchase_returns.tax_number,
                purchase_returns.date,
                purchase_returns.code,
                purchase_returns.status,
                purchase_returns.exchange_rate,

                item_receiving_reports.kode as code_item_receiving_report,

                vendors.nama as vendor_name,
                customers.nama as customer_name,
                ware_houses.nama as ware_house_name,

                items.nama as item_name,
                items.kode as item_code,

                purchase_return_details.subtotal,
                purchase_return_details.tax_amount,
                purchase_return_details.total,

                case when purchase_returns.exchange_rate = 1
                    then purchase_return_details.subtotal
                    else purchase_return_details.subtotal * purchase_returns.exchange_rate
                end as subtotal_local,

                case when purchase_returns.exchange_rate = 1
                    then purchase_return_details.tax_amount
                    else purchase_return_details.tax_amount * purchase_returns.exchange_rate
                end as tax_amount_local,

                case when purchase_returns.exchange_rate = 1
                    then purchase_return_details.total
                    else purchase_return_details.total * purchase_returns.exchange_rate
                end as total_local
            ')
            ->get();

        return [
            'title' => 'Laporan Ringkasan Retur Pembelian Trading',
            'data' => $models,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => 'summary-purchase-return-purchase-order-trading',
        ];
    }

    /**
     * debt-due-purchase-order-trading
     */
    private function debtDuePurchaseOrderTrading($request)
    {
        $supplierInvoiceDetails = DB::table('supplier_invoice_details')
            ->leftJoin('supplier_invoices', 'supplier_invoices.id', 'supplier_invoice_details.supplier_invoice_id')
            ->leftJoin('item_receiving_reports', 'item_receiving_reports.id', 'supplier_invoice_details.item_receiving_report_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', 'supplier_invoice_details.reference_id')
            ->leftJoin('customers', 'customers.id', 'purchase_orders.customer_id')
            ->leftJoin('vendors', 'vendors.id', 'purchase_orders.vendor_id')
            ->whereNull('supplier_invoices.deleted_at')
            ->whereNull('supplier_invoice_details.deleted_at')
            ->where('supplier_invoices.status', 'approve')
            ->where('supplier_invoice_details.reference_model', \App\Models\PoTrading::class)
            ->whereDate('supplier_invoices.top_due_date', '<=', Carbon::now())
            ->when($request->vendor_id, function ($q) use ($request) {
                return $q->where('purchase_orders.vendor_id', $request->vendor_id);
            })
            ->when($request->customer_id, function ($q) use ($request) {
                return $q->where('purchase_orders.customer_id', $request->customer_id);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($q) use ($request) {
                return $q->where('supplier_invoices.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($q) {
                return $q->where('supplier_invoices.branch_id', get_current_branch()->id);
            })
            ->when($request->from_date, function ($q) use ($request) {
                return $q->whereDate('supplier_invoices.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($q) use ($request) {
                return $q->whereDate('supplier_invoices.date', '<=', Carbon::parse($request->to_date));
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
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->where('supplier_invoice_payments.supplier_invoice_model', \App\Models\SupplierInvoice::class)
            ->whereIn('supplier_invoice_payments.supplier_invoice_id', $supplierInvoiceIds->toArray())
            ->selectRaw('
                supplier_invoice_payments.supplier_invoice_id,
                supplier_invoice_payments.exchange_rate,
                supplier_invoice_payments.pay_amount,
                supplier_invoice_payments.amount_to_pay
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

            $result->total = $supplierInvoicePayment->sum('amount_to_pay');
            $result->pay_amount = $supplierInvoicePayment->sum('pay_amount');
            $result->outstanding = $result->total - $result->pay_amount;

            return $result;
        });

        return [
            'title' => 'Laporan Hutang Jatuh Tempo Pembelian Trading',
            'type' => 'debt-due-purchase-order-trading',
            'data' => $results,
            'from_date' => $request->from_date ? Carbon::parse($request->from_date)->format('d/m/Y') : '',
            'to_date' => $request->to_date ? Carbon::parse($request->to_date)->format('d/m/Y') : '',
        ];
    }
}
