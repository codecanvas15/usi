<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PurchaseOrderGeneral\DetailPurchaseOrderGeneralReport;
use App\Exports\PurchaseOrderGeneral\PurchaseOrderServicePerPeriodReport;
use App\Exports\PurchaseOrderService\DetailPurchaseOrderServiceReport;
use App\Exports\PurchaseOrderService\PurchaseOrderServiceReport;
use App\Exports\PurchaseOrderService\PurchaseOrderServiceReportReceiving;
use App\Exports\PurchaseOrderService\SummaryPurchaseOrderServiceReport;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrderService;
use App\Models\PurchaseOrderServiceDetailItemTax;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use FontLib\TrueType\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderServiceReportController extends Controller
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
    protected string $view_folder = 'purchase-order-service-report';

    /**
     * where the route will be defined
     *
     * @var string
     */
    protected string $route = 'purchase-order-service';

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
            case "purchase-order-service":
                $data = $this->reportPurchaseOrderService($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = PurchaseOrderServiceReport::class;
                break;
            case "summary-purchase-order-service":
                $data = $this->reportSummaryPurchaseOrderService($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SummaryPurchaseOrderServiceReport::class;
                break;
            case "detail-purchase-order-service":
                $this->validate($request, [
                    'month' => 'required',
                ]);
                $data = $this->reportDetailPurchaseOrderService($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = DetailPurchaseOrderServiceReport::class;
                break;
            case "purchase-order-service-receiving":
                $data = $this->reportPurchaseOrderServiceReportReceiving($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = PurchaseOrderServiceReportReceiving::class;
                break;
            case "purchase-order-service-outstanding":

                $data = $this->purchaseOrderServiceOutstandingReport($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\PurchaseOrderService\PurchaseOrderServiceOutstandingExport::class;
                break;

            case "stock-comparison-with-purchase-order-service":

                $data = $this->stockComparisonWithPurchaseOrderService($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\PurchaseOrderService\StockComparisonWithPurchaseOrderServiceExport::class;
                break;

            case "return-purchase-order-service":

                $data = $this->ReturnPurchaseOrderService($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\PurchaseOrderService\ReturnPurchaseOrderServiceExport::class;
                break;
            case "debt-due-purchase-order-service":

                $data = $this->debtDuePurchaseOrderService($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\PurchaseOrderService\debtDuePurchaseOrderServiceExport::class;
                break;

            case "debt-due-purchase-order-service":

                $data = $this->debtDuePurchaseOrderService($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\PurchaseOrderService\debtDuePurchaseOrderServiceExport::class;
                break;

            case "per-periode-purchase-order-service":

                $this->validate($request, [
                    'month' => 'required',
                ]);

                $data = $this->purchaseOrderServicePerPeriod($request);
                $orientation = 'lanscape';
                $paper_size = 'a3';
                $excel_export = PurchaseOrderServicePerPeriodReport::class;
                break;


            case "detail-closed-purchase-order-service":
                $data = $this->reportClosedPurchaseOrderService($request);

                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = DetailPurchaseOrderGeneralReport::class;
                break;

            default:
                return redirect()->route("admin.$this->route.report")->with($this->ResponseJsonMessageCRUD(false, "report", "selected report type was not found"));
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
            return redirect()->route("admin.$this->route.report")->with($this->ResponseMessageCRUD(false, "report", "selected export format was not found"));
        }
    }

    private function debtDuePurchaseOrderService($request)
    {
        $invoices = DB::table('supplier_invoices as si')
            ->leftJoin('supplier_invoice_details as sid', 'si.id', 'sid.supplier_invoice_id')
            ->leftJoin('item_receiving_reports as lpb', 'lpb.id', 'sid.item_receiving_report_id')
            ->leftJoin('purchase_order_services as pos', 'pos.id', 'lpb.reference_id')
            ->leftJoin('purchase_order_service_details as posd', 'pos.id', 'posd.purchase_order_service_id')
            ->leftJoin('purchase_order_service_detail_items as posdi', 'posd.id', 'posdi.purchase_order_service_detail_id')
            ->leftJoin('items', 'items.id', 'posdi.item_id')
            ->leftJoin('vendors as v', 'v.id', 'pos.vendor_id')
            ->leftJoin('branches', 'branches.id', 'pos.branch_id')
            ->when($request->vendor_id, fn($q) => $q->where('pos.vendor_id', $request->vendor_id))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('pos.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('pos.branch_id', get_current_branch()->id);
            })
            ->where('lpb.tipe', 'jasa')
            ->whereNull('si.deleted_at')
            ->whereNotIn('si.status', ['reject', 'void'])
            ->selectRaw('
                si.id as id,
                si.code,

                v.nama as vendor_name,
                branches.name as branch_name,

                si.date,
                si.top_due_date as due_date,
                si.exchange_rate,
                si.grand_total as total,

                case
                    when si.exchange_rate = 1
                        then si.grand_total
                    else
                        si.grand_total * si.exchange_rate
                end as total_local
            ')
            ->get();


        $invoicePayments = DB::table('supplier_invoice_payments as invoice_payments')
            ->where('invoice_payments.supplier_invoice_model', \App\Models\SupplierInvoice::class)
            ->whereIn('invoice_payments.reference_id', $invoices->pluck('id')->toArray())
            ->whereNull('invoice_payments.deleted_at')
            ->SelectRaw('
                invoice_payments.supplier_invoice_id,
                invoice_payments.exchange_rate,
                invoice_payments.amount_to_pay,
                invoice_payments.pay_amount,

                case
                    when invoice_payments.exchange_rate = 1
                        then invoice_payments.amount_to_pay
                    else
                        invoice_payments.amount_to_pay * invoice_payments.exchange_rate
                end as amount_to_pay_local,

                case
                    when invoice_payments.exchange_rate = 1
                        then invoice_payments.pay_amount
                    else
                        invoice_payments.pay_amount * invoice_payments.exchange_rate
                end as pay_amount_local
            ')
            ->get();

        $results = $invoices->map(function ($invoice) use ($invoicePayments) {
            $payments = $invoicePayments->where('supplier_invoice_id', $invoice->id);

            $invoice->paid = $payments->sum('amount_to_pay');
            $invoice->paid_local = $payments->sum('amount_to_pay_local');

            return $invoice;
        });


        return [
            'data' => $results,
            'title' => 'Laporan Hutang Jatuh Tempo Pembelian Service',
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    private function ReturnPurchaseOrderService($request)
    {
        $models = DB::table('purchase_returns as pr')
            ->leftJoin('purchase_return_details as prd', 'pr.id', 'prd.purchase_return_id')
            ->leftJoin('items', 'items.id', 'prd.item_id')
            ->leftJoin('item_receiving_reports as lpb', 'lpb.id', 'pr.item_receiving_report_id')
            ->leftJoin('purchase_order_services as pos', 'pos.id', 'lpb.reference_id')
            ->leftJoin('purchase_order_service_details as posd', 'pos.id', 'posd.purchase_order_service_id')
            ->leftJoin('purchase_order_service_detail_items as posdi', 'posd.id', 'posdi.purchase_order_service_detail_id')
            ->leftJoin('vendors as v', 'v.id', 'pos.vendor_id')
            ->when($request->ware_house_id, fn($q) => $q->where('pr.ware_house_id', $request->ware_house_id))
            ->when($request->item_id, fn($q) => $q->where('posdi.item_id', $request->item_id))
            ->when($request->vendor_id, fn($q) => $q->where('pos.vendor_id', $request->vendor_id))
            ->when($request->status, fn($q) => $q->where('pr.status', $request->status))
            ->when($request->from_date, fn($q) => $q->whereDate('pr.date', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('pr.date', '<=', Carbon::parse($request->to_date)))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('pos.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('pos.branch_id', get_current_branch()->id);
            })
            ->where('lpb.tipe', 'service')
            ->whereNull('pos.deleted_at')
            ->whereNull('pr.deleted_at')
            ->distinct('prd.id')
            ->selectRaw('

                pos.date as date_purchase_order_service,

                pr.date date_invoice_return,
                pr.tax_number,
                pr.code,

                v.nama as vendor_name,

                items.kode as item_code,
                items.nama as item_name,

                prd.total as total,
                pr.exchange_rate,

                case
                    when pr.exchange_rate = 1
                        then pr.total
                    else
                        pr.total * pr.exchange_rate
                end as total_local,

                pr.status
            ')
            ->get();

        return [
            'data' => $models,
            'title' => 'Laporan Ringkasan Retur Penjualan service',
            "type" => "summary-sale-order-service-return",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    private function stockComparisonWithPurchaseOrderService($request)
    {
        $models = DB::table('purchase_order_service_details as posd')
            ->leftJoin('purchase_order_services as pos', 'pos.id', 'posd.purchase_order_service_id')
            ->leftJoin('purchase_order_service_detail_items as posdi', 'posd.id', 'posdi.purchase_order_service_detail_id')
            ->leftJoin('vendors as v', 'v.id', 'pos.vendor_id')
            ->leftJoin('items', 'items.id', 'posdi.item_id')
            ->when($request->item_id, fn($q) => $q->where('posdi.item_id', $request->item_id))
            ->when($request->vendor_id, fn($q) => $q->where('pos.vendor_id', $request->vendor_id))
            ->when($request->status, fn($q) => $q->where('pos.status', $request->status))
            ->when($request->from_date, fn($q) => $q->whereDate('pos.date', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('pos.date', '<=', Carbon::parse($request->to_date)))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('pos.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('pos.branch_id', get_current_branch()->id);
            })
            ->whereNull('pos.deleted_at')
            ->where('posdi.quantity', '>', DB::raw('posdi.quantity_received'))
            ->distinct('posd.id')
            ->selectRaw('
                pos.code,
                pos.date,
                v.nama as vendor_name,
                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,
                posdi.quantity,
                posdi.quantity_received,

                (posdi.quantity - posdi.quantity_received) as outstanding
            ')
            ->get();

        $itemIds = $models->pluck('item_id')->unique()->toArray();

        $stocks = DB::table('stock_mutations')
            ->leftJoin('items', 'items.id', 'stock_mutations.item_id')
            ->whereNull('stock_mutations.deleted_at')
            ->whereIn('stock_mutations.item_id', $itemIds)
            ->selectRaw('
                items.id as item_id,
                stock_mutations.in,
                stock_mutations.out
            ')
            ->get();

        $results = $models->map(function ($model) use ($stocks) {
            $in = $stocks->where('item_id', $model->item_id)->sum('in');
            $out = $stocks->where('item_id', $model->item_id)->sum('out');

            $model->stock = $in - $out;
            $model->gap = $model->stock - $model->outstanding;

            return $model;
        });

        return [
            'data' => $results,
            'title' => 'Laporan Perbandingan Stok dengan Pembelian Service',
            "type" => "stock-comparison-with-purchase-order-service-outstanding",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    private function purchaseOrderServiceOutstandingReport($request)
    {
        $models = DB::table('purchase_order_service_details as posd')
            ->leftJoin('purchase_order_services as pos', 'pos.id', 'posd.purchase_order_service_id')
            ->leftJoin('purchase_order_service_detail_items as posdi', 'posd.id', 'posdi.purchase_order_service_detail_id')
            ->leftJoin('vendors as v', 'v.id', 'pos.vendor_id')
            ->leftJoin('items', 'items.id', 'posdi.item_id')
            ->when($request->item_id, fn($q) => $q->where('posdi.item_id', $request->item_id))
            ->when($request->vendor_id, fn($q) => $q->where('pos.vendor_id', $request->vendor_id))
            ->when($request->status, fn($q) => $q->where('pos.status', $request->status))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('pos.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('pos.branch_id', get_current_branch()->id);
            })
            ->when($request->from_date, fn($q) => $q->whereDate('pos.date', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('pos.date', '<=', Carbon::parse($request->to_date)))
            ->whereNull('pos.deleted_at')
            ->where('posdi.quantity', '>', DB::raw('posdi.quantity_received'))
            ->distinct('posd.id')
            ->selectRaw('
                pos.code,
                pos.date,
                pos.status,
                v.nama as vendor_name,
                items.kode as item_code,
                items.nama as item_name,
                posdi.quantity,
                posdi.quantity_received,

                (posdi.quantity - posdi.quantity_received) as outstanding
            ')
            ->get();

        return [
            'data' => $models,
            'title' => 'Laporan Pembelian Service Outstanding',
            "type" => "purchase-order-service-outstanding",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     * Get purchase order service report per period last year
     *
     * @param $request
     * @param $year
     * @return object|Collection
     */
    private function purchaseOrderServicePerPeriodLastYear($request, $year): object
    {
        $model = DB::table('item_receiving_reports as lpb')
            ->leftJoin('item_receiving_report_details as lpbd', 'lpbd.item_receiving_report_id', '=', 'lpb.id')
            ->leftJoin('purchase_order_service_details as posd', 'lpb.reference_id', '=', 'posd.id')
            ->leftJoin('purchase_order_service_detail_items as posdi', 'posdi.purchase_order_service_detail_id', '=', 'posd.id')
            ->leftJoin('purchase_order_services as pos', 'pos.id', '=', 'posd.purchase_order_service_id')
            ->leftJoin('ware_houses as wh', 'wh.id', '=', 'lpb.ware_house_id')
            ->leftJoin('items', 'items.id', '=', 'lpbd.item_id')
            ->where('lpb.tipe', 'service')
            ->whereNull('lpb.deleted_at')
            ->whereYear('lpb.date_receive', $year)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('lpbd.item_id', $request->item_id);
            })
            ->when($request->warehouse_id, function ($query) use ($request) {
                return $query->where('lpb.ware_house_id', $request->warehouse_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('lpb.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('lpb.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('lpb.branch_id', get_current_branch()->id);
            })
            ->distinct('posd.id')
            ->selectRaw('

                posd.id,

                wh.id as ware_house_id,
                wh.nama as ware_house_name,

                items.kode as item_code,
                items.nama as item_name,

                posdi.item_id,
                posdi.quantity,

                case when lpb.exchange_rate = 1
                    then posdi.price
                    else posdi.price * lpb.exchange_rate
                end as price,

                case when lpb.exchange_rate = 1
                    then posdi.sub_total
                    else posdi.sub_total * lpb.exchange_rate
                end as sub_total,

                case when lpb.exchange_rate = 1
                    then posdi.tax_total
                    else posdi.tax_total * lpb.exchange_rate
                end as tax_total,

                case when lpb.exchange_rate = 1
                    then posdi.total
                    else posdi.total * lpb.exchange_rate
                end as total
            ')
            ->get();


        return $model;
    }

    /**
     * Get sale order service report per period january to selected month this year
     *
     * @param $request
     * @param $year
     * @param $month
     * @return object|Collection
     */
    private function purchaseOrderServicePerPeriodJanuaryToSelectedMonthThisYear($request, $year, $month): object
    {
        $model = DB::table('item_receiving_reports as lpb')
            ->leftJoin('item_receiving_report_details as lpbd', 'lpbd.item_receiving_report_id', '=', 'lpb.id')
            ->leftJoin('purchase_order_service_details as posd', 'lpb.reference_id', '=', 'posd.id')
            ->leftJoin('purchase_order_service_detail_items as posdi', 'posdi.purchase_order_service_detail_id', '=', 'posd.id')
            ->leftJoin('purchase_order_services as pos', 'pos.id', '=', 'posd.purchase_order_service_id')
            ->leftJoin('ware_houses as wh', 'wh.id', '=', 'lpb.ware_house_id')
            ->leftJoin('items', 'items.id', '=', 'lpbd.item_id')
            ->where('lpb.tipe', 'service')
            ->whereNull('lpb.deleted_at')
            ->whereMonth('lpb.date_receive', '<=', $month)
            ->whereYear('lpb.date_receive', $year)
            ->whereMonth('lpb.date_receive', $month)
            ->whereYear('lpb.date_receive', $year)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('lpbd.item_id', $request->item_id);
            })
            ->when($request->warehouse_id, function ($query) use ($request) {
                return $query->where('lpb.ware_house_id', $request->warehouse_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('lpb.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('lpb.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('lpb.branch_id', get_current_branch()->id);
            })
            ->distinct('posd.id')
            ->selectRaw('

            posd.id,

            wh.id as ware_house_id,
            wh.nama as ware_house_name,

            items.kode as item_code,
            items.nama as item_name,

            posdi.item_id,
            posdi.quantity,

            case when lpb.exchange_rate = 1
                then posdi.price
                else posdi.price * lpb.exchange_rate
            end as price,

            case when lpb.exchange_rate = 1
                then posdi.sub_total
                else posdi.sub_total * lpb.exchange_rate
            end as sub_total,

            case when lpb.exchange_rate = 1
                then posdi.tax_total
                else posdi.tax_total * lpb.exchange_rate
            end as tax_total,

            case when lpb.exchange_rate = 1
                then posdi.total
                else posdi.total * lpb.exchange_rate
            end as total
        ')
            ->get();

        return $model;
    }


    /**
     * Get purchase order service report per period selected month
     *
     * @param $request
     * @param $year
     * @param $month
     * @return object|Collection
     */
    private function purchaseOrderServicePerPeriodSelectedMonth($request, $year, $month): object
    {
        $model = DB::table('item_receiving_reports as lpb')
            ->leftJoin('item_receiving_report_details as lpbd', 'lpbd.item_receiving_report_id', '=', 'lpb.id')
            ->leftJoin('purchase_order_service_details as posd', 'lpb.reference_id', '=', 'posd.id')
            ->leftJoin('purchase_order_service_detail_items as posdi', 'posdi.purchase_order_service_detail_id', '=', 'posd.id')
            ->leftJoin('purchase_order_services as pos', 'pos.id', '=', 'posd.purchase_order_service_id')
            ->leftJoin('ware_houses as wh', 'wh.id', '=', 'lpb.ware_house_id')
            ->leftJoin('items', 'items.id', '=', 'lpbd.item_id')
            ->where('lpb.tipe', 'service')
            ->whereNull('lpb.deleted_at')
            ->whereMonth('lpb.date_receive', $month)
            ->whereYear('lpb.date_receive', $year)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('lpb.item_id', $request->item_id);
            })
            ->when($request->warehouse_id, function ($query) use ($request) {
                return $query->where('pos.ware_house_id', $request->warehouse_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('lpb.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('lpb.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('lpb.branch_id', get_current_branch()->id);
            })
            ->distinct('posd.id')
            ->selectRaw('

                posd.id,

                wh.id as ware_house_id,
                wh.nama as ware_house_name,

                items.kode as item_code,
                items.nama as item_name,

                posdi.item_id,
                posdi.quantity,

                case when lpb.exchange_rate = 1
                    then posdi.price
                    else posdi.price * lpb.exchange_rate
                end as price,

                case when lpb.exchange_rate = 1
                    then posdi.sub_total
                    else posdi.sub_total * lpb.exchange_rate
                end as sub_total,

                case when lpb.exchange_rate = 1
                    then posdi.tax_total
                    else posdi.tax_total * lpb.exchange_rate
                end as tax_total,

                case when lpb.exchange_rate = 1
                    then posdi.total
                    else posdi.total * lpb.exchange_rate
                end as total
            ')
            ->get();

        return $model;
    }

    /**
     * combine sale order service per period
     *
     * @param $previous_year_data
     * @param $selected_month_data
     * @param $january_to_selected_month_data
     * @return object|Collection|array
     */
    private function combinePurchaseOrderServicePerPeriod($previous_year_data, $selected_month_data, $january_to_selected_month_data): object|array
    {
        $customer_group = $selected_month_data->unique('where');

        $total = new \stdClass();
        $total->previous_year_quantity = 0;
        $total->previous_year_sub_total = 0;
        $total->previous_year_total_tax = 0;
        $total->previous_year_total = 0;
        $total->selected_month_quantity = 0;
        $total->selected_month_sub_total = 0;
        $total->selected_month_total_tax = 0;
        $total->selected_month_total = 0;
        $total->january_to_selected_month_quantity = 0;
        $total->january_to_selected_month_sub_total = 0;
        $total->january_to_selected_month_total_tax = 0;
        $total->january_to_selected_month_total = 0;

        $result = $customer_group->map(function ($item) use ($previous_year_data, $selected_month_data, $january_to_selected_month_data, &$total) {
            $ware_house_id = $item->ware_house_id;
            $ware_house_name = $item->ware_house_name;

            $_new_previous_year_data = $previous_year_data->where('ware_house_id', $ware_house_id);
            $_new_selected_month_data = $selected_month_data->where('ware_house_id', $ware_house_id);
            $_new_january_to_selected_month_data = $january_to_selected_month_data->where('ware_house_id', $ware_house_id);

            $item_group = $selected_month_data->unique('item_id');

            $item_group = $item_group->map(function ($item) use ($_new_previous_year_data, $_new_selected_month_data, $_new_january_to_selected_month_data, $ware_house_id, $ware_house_name, &$total) {
                $item_id = $item->item_id;
                $item_code = $item->item_code;
                $item_name = $item->item_name;

                $__new_previous_year_data = $_new_previous_year_data->where('item_id', $item_id);
                $__new_selected_month_data = $_new_selected_month_data->where('item_id', $item_id);
                $__new_january_to_selected_month_data = $_new_january_to_selected_month_data->where('item_id', $item_id);

                $previous_year_quantity = $__new_previous_year_data->sum('quantity');
                $previous_year_sub_total = $__new_previous_year_data->sum('sub_total');
                $previous_year_total_tax = $__new_previous_year_data->sum('total_tax');
                $previous_year_total = $__new_previous_year_data->sum('total');

                $selected_month_quantity = $__new_selected_month_data->sum('quantity');
                $selected_month_sub_total = $__new_selected_month_data->sum('sub_total');
                $selected_month_total_tax = $__new_selected_month_data->sum('total_tax');
                $selected_month_total = $__new_selected_month_data->sum('total');

                $january_to_selected_month_quantity = $__new_january_to_selected_month_data->sum('quantity');
                $january_to_selected_month_sub_total = $__new_january_to_selected_month_data->sum('sub_total');
                $january_to_selected_month_total_tax = $__new_january_to_selected_month_data->sum('total_tax');
                $january_to_selected_month_total = $__new_january_to_selected_month_data->sum('total');

                $item = new \stdClass();
                $item->ware_house_id =  $ware_house_id;
                $item->ware_house_name =  $ware_house_name;
                $item->item_id =  $item_id;
                $item->item_code =  $item_code;
                $item->item_name =  $item_name;
                $item->previous_year_quantity =  $previous_year_quantity;
                $item->previous_year_sub_total =  $previous_year_sub_total;
                $item->previous_year_total_tax =  $previous_year_total_tax;
                $item->previous_year_total =  $previous_year_total;
                $item->selected_month_quantity =  $selected_month_quantity;
                $item->selected_month_sub_total =  $selected_month_sub_total;
                $item->selected_month_total_tax =  $selected_month_total_tax;
                $item->selected_month_total =  $selected_month_total;
                $item->january_to_selected_month_quantity =  $january_to_selected_month_quantity;
                $item->january_to_selected_month_sub_total =  $january_to_selected_month_sub_total;
                $item->january_to_selected_month_total_tax =  $january_to_selected_month_total_tax;
                $item->january_to_selected_month_total =  $january_to_selected_month_total;

                $total->previous_year_quantity +=  $previous_year_quantity;
                $total->previous_year_sub_total +=  $previous_year_sub_total;
                $total->previous_year_total_tax +=  $previous_year_total_tax;
                $total->previous_year_total +=  $previous_year_total;
                $total->selected_month_quantity +=  $selected_month_quantity;
                $total->selected_month_sub_total +=  $selected_month_sub_total;
                $total->selected_month_total_tax +=  $selected_month_total_tax;
                $total->selected_month_total +=  $selected_month_total;
                $total->january_to_selected_month_quantity +=  $january_to_selected_month_quantity;
                $total->january_to_selected_month_sub_total +=  $january_to_selected_month_sub_total;
                $total->january_to_selected_month_total_tax +=  $january_to_selected_month_total_tax;
                $total->january_to_selected_month_total +=  $january_to_selected_month_total;

                return $item;
            });

            return collect([
                'ware_house_id' => $ware_house_id,
                'ware_house_name' => $ware_house_name,
                'detail' => $item_group,
            ]);
        });

        return [
            'data' => $result,
            'total' => $total,
        ];
    }


    /**
     * Generate report purchase order service per period
     *
     * @param $request
     * @return array
     */
    private function purchaseOrderServicePerPeriod($request): array
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
        $last_year_data = $this->purchaseOrderServicePerPeriodLastYear($request, $previous_year);
        $selected_month_data = $this->purchaseOrderServicePerPeriodSelectedMonth($request, $selected_year, $selected_month);
        $january_to_selected_month_data = $this->purchaseOrderServicePerPeriodJanuaryToSelectedMonthThisYear($request, $selected_year, $selected_month);
        // ! END GET DATA ============================================================================================================

        // return compact('last_year_data', 'selected_month_data', 'january_to_selected_month_data');

        // ! COMBINE DATA ============================================================================================================
        $model = $this->combinePurchaseOrderServicePerPeriod($last_year_data, $selected_month_data, $january_to_selected_month_data);
        // ! END COMBINE DATA ========================================================================================================

        return [
            'data' => $model['data'],
            'total' => $model['total'],
            'type' => 'per-periode-purchase-order-service',
            'period' => $request->month,
        ];
    }

    /**
     * Get data for purchase order service report
     *
     * @param $request
     * @return array
     */
    private function getReportPurchaseOrderService($request): array
    {
        $model = DB::table('purchase_order_services')
            ->leftJoin('vendors', 'purchase_order_services.vendor_id', '=', 'vendors.id')
            ->leftJoin('branches', 'purchase_order_services.branch_id', '=', 'branches.id')
            ->leftJoin('purchase_order_service_details', 'purchase_order_services.id', '=', 'purchase_order_service_details.purchase_order_service_id')
            ->leftJoin('purchase_order_service_detail_items', 'purchase_order_service_details.id', '=', 'purchase_order_service_detail_items.purchase_order_service_detail_id')
            ->whereNull('purchase_order_services.deleted_at')
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('purchase_order_services.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('purchase_order_services.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_service_detail_items.item_id', $request->item_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_services.vendor_id', $request->vendor_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_order_services.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_order_services.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_order_services.branch_id', get_current_branch()->id);
            })
            ->distinct('purchase_order_services.id')
            ->selectRaw('
                purchase_order_services.id,
                purchase_order_services.date,
                purchase_order_services.code,
                purchase_order_services.exchange_rate,
                vendors.nama as vendor_name
            ')
            ->get();

        $model_detail_ids = $model->pluck('id')->toArray();
        $model_detail_data = DB::table('purchase_order_service_details')
            ->whereIn('purchase_order_service_id', $model_detail_ids)
            ->where('type', 'main')
            ->selectRaw('
                purchase_order_service_details.id,
                purchase_order_service_details.purchase_order_service_id
            ')
            ->get();

        $model_details_item_ids = $model_detail_data->pluck('id')->toArray();
        $model_details_item_data = DB::table('purchase_order_service_detail_items')
            ->leftJoin('items', 'purchase_order_service_detail_items.item_id', '=', 'items.id')
            ->leftJoin('units', 'purchase_order_service_detail_items.unit_id', '=', 'units.id')
            ->leftJoin('purchase_order_service_details', 'purchase_order_service_detail_items.purchase_order_service_detail_id', '=', 'purchase_order_service_details.id')
            ->leftJoin('purchase_order_services', 'purchase_order_service_details.purchase_order_service_id', '=', 'purchase_order_services.id')
            ->whereIn('purchase_order_service_detail_id', $model_details_item_ids)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('item_id', $request->item_id);
            })
            ->selectRaw('
                purchase_order_service_detail_items.id,
                purchase_order_service_detail_items.purchase_order_service_detail_id,
                purchase_order_service_detail_items.quantity,
                purchase_order_service_detail_items.price,
                purchase_order_service_detail_items.sub_total,

                case when purchase_order_services.exchange_rate = 1
                    then purchase_order_service_detail_items.sub_total
                    else purchase_order_service_detail_items.sub_total * purchase_order_services.exchange_rate
                end as sub_total_idr,

                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name
            ')
            ->get();

        return [
            'model' => $model,
            'model_detail_data' => $model_detail_data,
            'model_details_item_data' => $model_details_item_data,
        ];
    }

    /**
     * combine data for purchase order service report
     *
     * @param array $data
     * @return array
     */
    public function combineReportPurchaseOrderService(array $data): array|Collection
    {
        list(
            'model' => $model,
            'model_detail_data' => $model_detail_data,
            'model_details_item_data' => $model_details_item_data,
        ) = $data;

        $total_all = new \stdClass();
        $total_all->total = 0;
        $total_all->total_idr = 0;

        $results = $model->map(function ($item) use ($model_detail_data, $model_details_item_data, $total_all) {
            $details = $model_detail_data->where('purchase_order_service_id', $item->id)->map(function ($item_detail) use ($model_details_item_data, $item, $total_all) {
                $data = $model_details_item_data->where('purchase_order_service_detail_id', $item_detail->id);
                $data->map(function ($item_data) use ($item, $total_all) {
                    $total_all->total += $item_data->sub_total;
                    $total_all->total_idr += $item_data->sub_total_idr;

                    $item_data->vendor_name = $item->vendor_name;
                    $item_data->exchange_rate = $item->exchange_rate;
                    $item_data->date = $item->date;
                    $item_data->code = $item->code;

                    return $item_data;
                });
                return $data;
            })->flatten(1);
            return $details;
        })->flatten(1);

        return [
            'total_all' => $total_all,
            'data' => $results,
        ];
    }

    /**
     * Generate purchase order service report
     *
     * @param $request
     * @return array
     */
    private function reportPurchaseOrderService($request): array|Collection
    {
        // * get data
        $data = $this->getReportPurchaseOrderService($request);

        // * combine data
        $results = $this->combineReportPurchaseOrderService($data);

        return [
            'total_all' => $results['total_all'],
            'data' => $results['data'],
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => 'purchase-order-service',
            'headline' => 'pembelian-service',
        ];
    }

    /**
     * Generate summary purchase order service report
     *
     * @param $request
     * @return array
     */
    private function reportSummaryPurchaseOrderService($request): array
    {
        $model = DB::table('supplier_invoices')
            ->leftJoin('branches', 'supplier_invoices.branch_id', '=', 'branches.id')
            ->leftJoin('vendors', 'supplier_invoices.vendor_id', '=', 'vendors.id')
            ->leftJoin('supplier_invoice_details', 'supplier_invoices.id', '=', 'supplier_invoice_details.supplier_invoice_id')
            ->whereNull('supplier_invoices.deleted_at')
            ->where('supplier_invoice_details.reference_model', PurchaseOrderService::class)
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
            'type' => 'summary-purchase-order-service',
            'headline' => 'ringkasan-pembelian-service',
        ];
    }

    /**
     * Generate purchase order service detail report previous year
     *
     * @param $request
     * @param $year
     * @return object
     */
    private function getReportDetailPurchaseOrderServicePreviousYear($request, $year): object
    {
        // * get detail items data
        $model_detail_items_data = DB::table('purchase_order_service_detail_items')
            ->leftJoin('items', 'purchase_order_service_detail_items.item_id', '=', 'items.id')
            ->leftJoin('units', 'purchase_order_service_detail_items.unit_id', '=', 'units.id')
            ->leftJoin('purchase_order_service_details', 'purchase_order_service_detail_items.purchase_order_service_detail_id', '=', 'purchase_order_service_details.id')
            ->leftJoin('purchase_order_services', 'purchase_order_service_details.purchase_order_service_id', '=', 'purchase_order_services.id')
            ->leftJoin('vendors', 'purchase_order_services.vendor_id', '=', 'vendors.id')
            ->whereNull('purchase_order_services.deleted_at')
            ->whereYear('purchase_order_services.date', $year)
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_services.vendor_id', $request->vendor_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_order_services.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_order_services.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_order_services.branch_id', get_current_branch()->id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_service_detail_items.item_id', $request->item_id);
            })
            ->selectRaw('
                vendors.id as vendor_id,
                vendors.nama as vendor_name,
                purchase_order_service_detail_items.id,
                purchase_order_service_detail_items.purchase_order_service_detail_id,
                purchase_order_service_detail_items.item_id,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                purchase_order_service_detail_items.quantity_received as quantity,

                case when purchase_order_services.exchange_rate = 1
                    then purchase_order_service_detail_items.price
                    else purchase_order_service_detail_items.price * purchase_order_services.exchange_rate
                end as price
            ')
            ->get();

        return $model_detail_items_data;
    }

    /**
     * Generate purchase order service detail report selected month
     *
     * @param $request
     * @param $year
     * @param $month
     * @return object
     */
    private function getReportDetailPurchaseOrderServiceSelectedMonth($request, $year, $month): object
    {
        // * get detail items data
        $model_detail_items_data = DB::table('purchase_order_service_detail_items')
            ->leftJoin('items', 'purchase_order_service_detail_items.item_id', '=', 'items.id')
            ->leftJoin('units', 'purchase_order_service_detail_items.unit_id', '=', 'units.id')
            ->leftJoin('purchase_order_service_details', 'purchase_order_service_detail_items.purchase_order_service_detail_id', '=', 'purchase_order_service_details.id')
            ->leftJoin('purchase_order_services', 'purchase_order_service_details.purchase_order_service_id', '=', 'purchase_order_services.id')
            ->leftJoin('vendors', 'purchase_order_services.vendor_id', '=', 'vendors.id')
            ->whereNull('purchase_order_services.deleted_at')
            ->whereMonth('purchase_order_services.date', $month)
            ->whereYear('purchase_order_services.date', $year)
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_services.vendor_id', $request->vendor_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_order_services.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_order_services.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_order_services.branch_id', get_current_branch()->id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_service_detail_items.item_id', $request->item_id);
            })
            ->selectRaw('
                vendors.id as vendor_id,
                vendors.nama as vendor_name,
                purchase_order_service_detail_items.id,
                purchase_order_service_detail_items.purchase_order_service_detail_id,
                purchase_order_service_detail_items.item_id,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                purchase_order_service_detail_items.quantity_received as quantity,
                case when purchase_order_services.exchange_rate = 1
                    then purchase_order_service_detail_items.price
                    else purchase_order_service_detail_items.price * purchase_order_services.exchange_rate
                end as price
            ')
            ->get();

        return $model_detail_items_data;
    }

    /**
     * Generate purchase order service detail report january until selected month
     *
     * @param $request
     * @param $year
     * @param $month
     * @return object
     */
    private function getReportDetailPurchaseOrderServiceJanuaryUntilSelectedMonth($request, $year, $month): object
    {
        // * get detail items data
        $model_detail_items_data = DB::table('purchase_order_service_detail_items')
            ->leftJoin('items', 'purchase_order_service_detail_items.item_id', '=', 'items.id')
            ->leftJoin('units', 'purchase_order_service_detail_items.unit_id', '=', 'units.id')
            ->leftJoin('purchase_order_service_details', 'purchase_order_service_detail_items.purchase_order_service_detail_id', '=', 'purchase_order_service_details.id')
            ->leftJoin('purchase_order_services', 'purchase_order_service_details.purchase_order_service_id', '=', 'purchase_order_services.id')
            ->leftJoin('vendors', 'purchase_order_services.vendor_id', '=', 'vendors.id')
            ->whereNull('purchase_order_services.deleted_at')
            ->whereMonth('purchase_order_services.date', '<=', $month)
            ->whereYear('purchase_order_services.date', $year)
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_services.vendor_id', $request->vendor_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_order_services.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_order_services.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_order_services.branch_id', get_current_branch()->id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_service_detail_items.item_id', $request->item_id);
            })
            ->selectRaw('
                vendors.id as vendor_id,
                vendors.nama as vendor_name,
                purchase_order_service_detail_items.id,
                purchase_order_service_detail_items.purchase_order_service_detail_id,
                purchase_order_service_detail_items.item_id,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                purchase_order_service_detail_items.quantity_received as quantity,
                case when purchase_order_services.exchange_rate = 1
                    then purchase_order_service_detail_items.price
                    else purchase_order_service_detail_items.price * purchase_order_services.exchange_rate
                end as price
            ')
            ->get();

        return $model_detail_items_data;
    }

    private function combineReportDetailPurchaseOrderService(array $data): array|Collection
    {
        list($previous_year_data, $selected_month_data, $january_until_selected_month_data) = $data;

        $grouped = $selected_month_data->groupBy('vendor_id')->map(function ($item) {
            return $item->groupBy('item_id');
        });

        $total_all = new \stdClass();
        $total_all->previous_year_quantity = 0;
        $total_all->previous_year_price = 0;
        $total_all->previous_year_sub_total = 0;
        $total_all->selected_month_quantity = 0;
        $total_all->selected_month_price = 0;
        $total_all->selected_month_sub_total = 0;
        $total_all->january_until_selected_month_quantity = 0;
        $total_all->january_until_selected_month_price = 0;
        $total_all->january_until_selected_month_sub_total = 0;

        $results = $grouped->map(function ($item, $vendor_key) use ($previous_year_data, $january_until_selected_month_data, $total_all) {
            $results = $item->map(function ($item, $item_key) use ($previous_year_data, $january_until_selected_month_data, $vendor_key, $total_all) {
                $data = new \stdClass();

                $data->vendor_id = $vendor_key;
                $data->vendor_name = $item[0]->vendor_name ?? "Undefined";
                $data->item_id = $item_key;
                $data->item_name = $item[0]->item_name ?? "Undefined";
                $data->item_code = $item[0]->item_code ?? "Undefined";

                $data->previous_year_quantity = $previous_year_data->where('vendor_id', $vendor_key)->where('item_id', $item_key)->sum('quantity');
                $data->previous_year_price = $previous_year_data->where('vendor_id', $vendor_key)->where('item_id', $item_key)->sum('price');
                $data->previous_year_sub_total = $data->previous_year_quantity * $data->previous_year_price;

                $data->selected_month_quantity = $item->sum('quantity');
                $data->selected_month_price = $item->sum('price');
                $data->selected_month_sub_total = $data->selected_month_quantity * $data->selected_month_price;

                $data->january_until_selected_month_quantity = $january_until_selected_month_data->where('vendor_id', $vendor_key)->where('item_id', $item_key)->sum('quantity');
                $data->january_until_selected_month_price = $january_until_selected_month_data->where('vendor_id', $vendor_key)->where('item_id', $item_key)->sum('price');
                $data->january_until_selected_month_sub_total = $data->january_until_selected_month_quantity * $data->january_until_selected_month_price;

                $total_all->previous_year_quantity += $data->previous_year_quantity;
                $total_all->previous_year_price += $data->previous_year_price;
                $total_all->previous_year_sub_total += $data->previous_year_sub_total;
                $total_all->selected_month_quantity += $data->selected_month_quantity;
                $total_all->selected_month_price += $data->selected_month_price;
                $total_all->selected_month_sub_total += $data->selected_month_sub_total;
                $total_all->january_until_selected_month_quantity += $data->january_until_selected_month_quantity;
                $total_all->january_until_selected_month_price += $data->january_until_selected_month_price;
                $total_all->january_until_selected_month_sub_total += $data->january_until_selected_month_sub_total;

                return $data;
            })->flatten();

            return $results;
        })->flatten();

        return [
            'results' => $results,
            'total_all' => $total_all
        ];
    }

    /**
     * Generate detail purchase order service report
     *
     * @param $request
     * @return array
     */
    private function reportDetailPurchaseOrderService($request): array|Collection
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
        $previous_year_data = $this->getReportDetailPurchaseOrderServicePreviousYear($request, $previous_year);
        $selected_month_data = $this->getReportDetailPurchaseOrderServiceSelectedMonth($request, $selected_year, $selected_month);
        $january_until_selected_month_data = $this->getReportDetailPurchaseOrderServiceJanuaryUntilSelectedMonth($request, $selected_year, $selected_month);

        // return compact('previous_year_data', 'selected_month_data', 'january_until_selected_month_data');

        // ! COMBINE AND GET RESULT
        $results = $this->combineReportDetailPurchaseOrderService([
            $previous_year_data,
            $selected_month_data,
            $january_until_selected_month_data
        ]);

        return [
            'data' => $results['results'],
            'total' => $results['total_all'],
            'period' => $request->month,
            'type' => 'detail-purchase-order-service'
        ];
    }

    /**
     * Get report detail purchase order service previous year
     *
     * @param $request
     * @return array
     */
    public function reportPurchaseOrderServiceReportReceiving($request): array
    {
        $model = DB::table('item_receiving_report_details')
            ->leftJoin('item_receiving_reports', 'item_receiving_reports.id', '=', 'item_receiving_report_details.item_receiving_report_id')
            ->leftJoin('items', 'items.id', '=', 'item_receiving_report_details.item_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'item_receiving_reports.vendor_id')
            ->leftJoin('branches', 'branches.id', '=', 'item_receiving_reports.branch_id')
            ->leftJoin('purchase_order_services', 'purchase_order_services.id', '=', 'item_receiving_reports.reference_id')
            ->leftJoin('purchase_order_service_detail_items', 'purchase_order_service_detail_items.id', '=', 'item_receiving_report_details.reference_id')
            ->leftJoin('units', 'units.id', '=', 'purchase_order_service_detail_items.unit_id')
            ->whereNull('item_receiving_reports.deleted_at')
            ->where('item_receiving_reports.tipe', 'jasa')
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('item_receiving_reports.date_receive', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('item_receiving_reports.date_receive', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_services.vendor_id', $request->vendor_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_order_services.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_order_services.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_order_services.branch_id', get_current_branch()->id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_service_detail_items.item_id', $request->item_id);
            })
            ->selectRaw('
                item_receiving_reports.kode as code,
                item_receiving_reports.date_receive as date,
                purchase_order_services.code as purchase_code,
                purchase_order_services.exchange_rate,
                vendors.nama as vendor_name,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                item_receiving_report_details.jumlah_diterima as quantity,
                purchase_order_service_detail_items.id as purchase_order_service_detail_item_id,
                purchase_order_service_detail_items.price,
                (item_receiving_report_details.jumlah_diterima * purchase_order_service_detail_items.price) as sub_total,
                (item_receiving_report_details.jumlah_diterima * purchase_order_service_detail_items.price * purchase_order_services.exchange_rate) as sub_total_idr

            ')
            ->get();

        $taxes = PurchaseOrderServiceDetailItemTax::with('tax')
            ->whereIn('purchase_order_service_detail_item_id', $model->pluck('purchase_order_service_detail_item_id'))
            ->get();

        $unique_taxes = $taxes->unique('tax_id');
        $model = $model->map(function ($item) use ($taxes) {
            $item->taxes = $taxes->where('purchase_order_service_detail_item_id', $item->purchase_order_service_detail_item_id)
                ->map(function ($tax) use ($item) {
                    $tax->tax_amount = $tax->value * $item->sub_total;

                    return $tax;
                });

            $item->total = $item->sub_total + $item->taxes->sum('tax_amount');
            $item->total_idr = $item->total * $item->exchange_rate;
            return $item;
        });

        $total_all = new \stdClass();
        $total_all->sub_total = $model->sum('sub_total');
        $total_all->sub_total_idr = $model->sum('sub_total_idr');
        $total_all->total = $model->sum('total');
        $total_all->total_idr = $model->sum('total_idr');
        $total_all->taxes = $unique_taxes;
        $total_all->taxes = $unique_taxes->map(function ($item) use ($model) {
            $item->total = $model->sum(function ($model) use ($item) {
                return $model->taxes->where('tax_id', $item->tax_id)->sum('tax_amount');
            });

            return $item;
        });

        return [
            'data' => $model,
            'total_all' => $total_all,
            'type' => 'purchase-order-service-receiving',
            'headline' => 'penerimaan-pembelian-service',
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'unique_taxes' => $unique_taxes,
        ];
    }

    private function reportClosedPurchaseOrderService($request)
    {
        $data = DB::table('purchase_order_service_detail_items')
            ->join('items', 'items.id', '=', 'purchase_order_service_detail_items.item_id')
            ->join('units', 'units.id', '=', 'purchase_order_service_detail_items.unit_id')
            ->join('purchase_order_service_details', 'purchase_order_service_details.id', '=', 'purchase_order_service_detail_items.purchase_order_service_detail_id')
            ->join('purchase_order_services', 'purchase_order_services.id', '=', 'purchase_order_service_details.purchase_order_service_id')
            ->join('branches', 'branches.id', '=', 'purchase_order_services.branch_id')
            ->join('vendors', 'vendors.id', '=', 'purchase_order_services.vendor_id')
            ->whereNull('purchase_order_services.deleted_at')
            ->whereIn('purchase_order_services.status', ['done', 'approve'])
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('purchase_order_services.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('purchase_order_services.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_service_detail_items.item_id', $request->item_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_services.vendor_id', $request->vendor_id);
            })
            ->orderBy('purchase_order_services.date', 'asc')
            ->select(
                'purchase_order_services.date',
                'purchase_order_services.id',
                'purchase_order_services.code',
                'purchase_order_services.payment_description',
                'purchase_order_services.total',
                'purchase_order_services.exchange_rate',
                'purchase_order_services.status',
                'branches.name as branch_name',
                'vendors.nama as vendor_name',
                'vendors.alamat as vendor_address',
                'vendors.code as vendor_id',
                'items.nama as item_name',
                'units.name as unit_name',
                'purchase_order_service_detail_items.price',
                'purchase_order_service_detail_items.quantity',
                'purchase_order_service_detail_items.total',
            )
            ->get();

        $purchase_order_services = $data->unique('id');

        $item_receiving_reports = DB::table('item_receiving_reports')
            ->whereNull('item_receiving_reports.deleted_at')
            ->whereIn('status', ['approve', 'done'])
            ->whereIn('reference_id', $purchase_order_services->pluck('id'))
            ->where('tipe', 'service')
            ->select('reference_id', 'date_receive')
            ->get();

        $purchase_order_services = $purchase_order_services->map(function ($item) use ($data, $item_receiving_reports) {
            $item->details = $data->where('id', $item->id)->values()->all();
            $item->date_receives = $item_receiving_reports->where('reference_id', $item->id)->pluck('date_receive')
                ->map(function ($item) {
                    return localDate($item);
                })->toArray();
            return $item;
        });

        return [
            'data' => $purchase_order_services,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => 'laporan detail order pembelian'
        ];
    }
}
