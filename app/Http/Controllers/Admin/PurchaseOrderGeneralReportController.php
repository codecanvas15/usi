<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PurchaseOrderGeneral\DetailPurchaseOrderGeneralReport;
use App\Exports\PurchaseOrderGeneral\PerPeriodPurchaseOrderGeneralReport;
use App\Exports\PurchaseOrderGeneral\PurchaseOrderGeneralPerPeriodReport;
use App\Exports\PurchaseOrderGeneral\PurchaseOrderGeneralReport;
use App\Exports\PurchaseOrderGeneral\PurchaseOrderGeneralReportReceiving;
use App\Exports\PurchaseOrderGeneral\SummaryPurchaseOrderGeneralReport;
use App\Http\Controllers\Controller;
use App\Models\LpbTaxSummary;
use App\Models\PurchaseOrderGeneral;
use App\Models\PurchaseOrderGeneralDetailItemTax;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderGeneralReportController extends Controller
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
    protected string $view_folder = 'purchase-order-general-report';

    /**
     * where the route will be defined
     *
     * @var string
     */
    protected string $route = 'purchase-order-general';

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
            case "purchase-order-general":
                $data = $this->reportPurchaseOrderGeneral($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = PurchaseOrderGeneralReport::class;
                break;
            case "summary-purchase-order-general":
                $data = $this->reportSummaryPurchaseOrderGeneral($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SummaryPurchaseOrderGeneralReport::class;
                break;
            case "detail-purchase-order-general":
                $this->validate($request, [
                    'month' => 'required',
                ]);
                $data = $this->reportDetailPurchaseOrderGeneral($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = DetailPurchaseOrderGeneralReport::class;
                break;
            case "purchase-order-general-receiving":
                $data = $this->reportPurchaseOrderGeneralReportReceiving($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = PurchaseOrderGeneralReportReceiving::class;
                break;
            case "per-periode-purchase-order-general":

                $this->validate($request, [
                    'month' => 'required',
                ]);

                $data = $this->purchaseOrderGeneralPerPeriod($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = PurchaseOrderGeneralPerPeriodReport::class;
                break;

            case "purchase-order-general-outstanding":

                $data = $this->purchaseOrderGeneralOutstandingReport($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\PurchaseOrderGeneral\PurchaseOrderGeneralOutstandingExport::class;
                break;

            case "stock-comparison-with-purchase-order-general":

                $data = $this->stockComparisonWithPurchaseOrderGeneral($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\PurchaseOrderGeneral\StockComparisonWithPurchaseOrderGeneralExport::class;
                break;

            case "return-purchase-order-general":

                $data = $this->ReturnPurchaseOrderGeneral($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\PurchaseOrderGeneral\ReturnPurchaseOrderGeneralExport::class;
                break;

            case "debt-due-purchase-order-general":

                $data = $this->debtDuePurchaseOrderGeneral($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\PurchaseOrderGeneral\debtDuePurchaseOrderGeneralExport::class;
                break;

            case "detail-closed-purchase-order-general":
                $data = $this->reportClosedPurchaseOrderGeneral($request);

                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = DetailPurchaseOrderGeneralReport::class;
                break;

            default:
                return redirect()->route("admin.$this->route.report")->with($this->ResponseMessageCRUD(false, "report", "selected report type was not found"));
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


    private function debtDuePurchaseOrderGeneral($request)
    {
        $invoices = DB::table('supplier_invoices as si')
            ->leftJoin('supplier_invoice_details as sid', 'si.id', 'sid.supplier_invoice_id')
            ->leftJoin('item_receiving_reports as lpb', 'lpb.id', 'sid.item_receiving_report_id')
            ->leftJoin('purchase_order_generals as pog', 'pog.id', 'lpb.reference_id')
            ->leftJoin('purchase_order_general_details as pogd', 'pog.id', 'pogd.purchase_order_general_id')
            ->leftJoin('purchase_order_general_detail_items as pogdi', 'pogd.id', 'pogdi.purchase_order_general_detail_id')
            ->leftJoin('items', 'items.id', 'pogdi.item_id')
            ->leftJoin('vendors as v', 'v.id', 'pog.vendor_id')
            ->leftJoin('branches', 'branches.id', 'pog.branch_id')
            ->when($request->vendor_id, fn($q) => $q->where('pog.vendor_id', $request->vendor_id))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('pog.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('pog.branch_id', get_current_branch()->id);
            })
            ->where('lpb.tipe', '=', 'general')
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
            ->where('invoice_payments.supplier_invoice_model', \App\Models\SupplierInvoiceGeneral::class)
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
            'title' => 'Laporan Hutang Jatuh Tempo Pembelian General',
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    private function ReturnPurchaseOrderGeneral($request)
    {
        $models = DB::table('purchase_returns as pr')
            ->leftJoin('purchase_return_details as prd', 'pr.id', 'prd.purchase_return_id')
            ->leftJoin('items', 'items.id', 'prd.item_id')
            ->leftJoin('item_receiving_reports as lpb', 'lpb.id', 'pr.item_receiving_report_id')
            ->leftJoin('purchase_order_generals as pog', 'pog.id', 'lpb.reference_id')
            ->leftJoin('purchase_order_general_details as pogd', 'pog.id', 'pogd.purchase_order_general_id')
            ->leftJoin('purchase_order_general_detail_items as pogdi', 'pogd.id', 'pogdi.purchase_order_general_detail_id')
            ->leftJoin('vendors as v', 'v.id', 'pog.vendor_id')
            ->when($request->ware_house_id, fn($q) => $q->where('pr.ware_house_id', $request->ware_house_id))
            ->when($request->item_id, fn($q) => $q->where('pogdi.item_id', $request->item_id))
            ->when($request->vendor_id, fn($q) => $q->where('pog.vendor_id', $request->customer_id))
            ->when($request->status, fn($q) => $q->where('pr.status', $request->status))
            ->when($request->from_date, fn($q) => $q->whereDate('pr.date', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('pr.date', '<=', Carbon::parse($request->to_date)))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('pog.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('pog.branch_id', get_current_branch()->id);
            })
            ->where('lpb.tipe', 'general')
            ->whereNull('pog.deleted_at')
            ->whereNull('pr.deleted_at')
            ->whereNull('pog.deleted_at')
            ->distinct('prd.id')
            ->selectRaw('

                pog.date as date_purchase_order_general,

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
            'title' => 'Laporan Ringkasan Retur Penjualan General',
            "type" => "summary-sale-order-general-return",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    private function stockComparisonWithPurchaseOrderGeneral($request)
    {
        $models = DB::table('purchase_order_general_details as pogd')
            ->leftJoin('purchase_order_generals as pog', 'pog.id', 'pogd.purchase_order_general_id')
            ->leftJoin('purchase_order_general_detail_items as pogdi', 'pogd.id', 'pogdi.purchase_order_general_detail_id')
            ->leftJOin('vendors as v', 'v.id', 'pog.vendor_id')
            ->leftJoin('items', 'items.id', 'pogdi.item_id')
            ->when($request->item_id, fn($q) => $q->where('pogdi.item_id', $request->item_id))
            ->when($request->vendor_id, fn($q) => $q->where('pog.vendor_id', $request->vendor_id))
            ->when($request->status, fn($q) => $q->where('pog.status', $request->status))
            ->when($request->from_date, fn($q) => $q->whereDate('pog.date', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('pog.date', '<=', Carbon::parse($request->to_date)))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('pog.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('pog.branch_id', get_current_branch()->id);
            })
            ->whereNull('pog.deleted_at')
            ->where('pogdi.quantity', '>', DB::raw('pogdi.quantity_received'))
            ->distinct('pogd.id')
            ->selectRaw('
                pog.code,
                pog.date,
                v.nama as vendor_name,
                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,
                pogdi.quantity,
                pogdi.quantity_received,

                (pogdi.quantity - pogdi.quantity_received) as outstanding
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
            'title' => 'Laporan Perbandingan Stok dengan Pembelian General',
            "type" => "stock-comparison-with-purchase-order-general-outstanding",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    private function purchaseOrderGeneralOutstandingReport($request)
    {
        $models = DB::table('purchase_order_general_details as pogd')
            ->leftJoin('purchase_order_generals as pog', 'pog.id', 'pogd.purchase_order_general_id')
            ->leftJoin('purchase_order_general_detail_items as pogdi', 'pogd.id', 'pogdi.purchase_order_general_detail_id')
            ->leftJoin('vendors as v', 'v.id', 'pog.vendor_id')
            ->leftJoin('items', 'items.id', 'pogdi.item_id')
            ->when($request->item_id, fn($q) => $q->where('pogdi.item_id', $request->item_id))
            ->when($request->vendor_id, fn($q) => $q->where('pog.vendor_id', $request->vendor_id))
            ->when($request->status, fn($q) => $q->where('pog.status', $request->status))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('pog.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('pog.branch_id', get_current_branch()->id);
            })
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('pog.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('pog.date', '<=', Carbon::parse($request->to_date));
            })
            ->whereNull('pog.deleted_at')
            ->where('pogdi.quantity', '>', DB::raw('pogdi.quantity_received'))
            ->distinct('pogd.id')
            ->selectRaw('
                pog.code,
                pog.date,
                pog.status,
                v.nama as vendor_name,
                items.kode as item_code,
                items.nama as item_name,
                pogdi.quantity,
                pogdi.quantity_received,

                (pogdi.quantity - pogdi.quantity_received) as outstanding
            ')
            ->get();

        return [
            'data' => $models,
            'title' => 'Laporan Pembelian General Outstanding',
            "type" => "purchase-order-general-outstanding",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     * Get purchase order general report per period last year
     *
     * @param $request
     * @param $year
     * @return object|Collection
     */
    private function purchaseOrderGeneralPerPeriodLastYear($request, $year): object
    {
        $model = DB::table('item_receiving_reports as lpb')
            ->leftJoin('item_receiving_report_details as lpbd', 'lpbd.item_receiving_report_id', '=', 'lpb.id')
            ->join('purchase_order_general_detail_items as pogdi', 'pogdi.id', '=', 'lpbd.reference_id')
            ->join('purchase_order_general_details as pogd', 'pogdi.purchase_order_general_detail_id', '=', 'pogd.id')
            ->join('purchase_order_generals as pog', 'pog.id', '=', 'pogd.purchase_order_general_id')
            ->join('vendors as v', 'v.id', 'pog.vendor_id')
            ->join('items', 'items.id', '=', 'lpbd.item_id')
            ->where('lpb.tipe', 'general')
            ->whereNull('lpb.deleted_at')
            ->whereYear('lpb.date_receive', $year)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('lpbd.item_id', $request->item_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('pog.vendor_id', $request->vendor_id);
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
            ->distinct('pogd.id')
            ->selectRaw('

                pogd.id,
                pog.code,

                v.id as vendor_id,
                v.nama as vendor_name,
                v.code as vendor_code,

                items.kode as item_code,
                items.nama as item_name,

                pogdi.item_id,
                pogdi.quantity,

                case when lpb.exchange_rate = 1
                    then pogdi.price
                    else pogdi.price * lpb.exchange_rate
                end as price,

                case when lpb.exchange_rate = 1
                    then pogdi.sub_total
                    else pogdi.sub_total * lpb.exchange_rate
                end as sub_total,

                case when lpb.exchange_rate = 1
                    then pogdi.tax_total
                    else pogdi.tax_total * lpb.exchange_rate
                end as tax_total,

                case when lpb.exchange_rate = 1
                    then pogdi.total
                    else pogdi.total * lpb.exchange_rate
                end as total
            ')
            ->get();


        return $model;
    }

    /**
     * Get sale order general report per period january to selected month this year
     *
     * @param $request
     * @param $year
     * @param $month
     * @return object|Collection
     */
    private function purchaseOrderGeneralPerPeriodJanuaryToSelectedMonthThisYear($request, $year, $month): object
    {
        $model = DB::table('item_receiving_reports as lpb')
            ->leftJoin('item_receiving_report_details as lpbd', 'lpbd.item_receiving_report_id', '=', 'lpb.id')
            ->join('purchase_order_general_detail_items as pogdi', 'pogdi.id', '=', 'lpbd.reference_id')
            ->join('purchase_order_general_details as pogd', 'pogdi.purchase_order_general_detail_id', '=', 'pogd.id')
            ->join('purchase_order_generals as pog', 'pog.id', '=', 'pogd.purchase_order_general_id')
            ->join('vendors as v', 'v.id', 'pog.vendor_id')
            ->join('items', 'items.id', '=', 'lpbd.item_id')
            ->where('lpb.tipe', 'general')
            ->whereNull('lpb.deleted_at')
            ->whereMonth('lpb.date_receive', '<=', $month)
            ->whereYear('lpb.date_receive', $year)
            ->whereMonth('lpb.date_receive', $month)
            ->whereYear('lpb.date_receive', $year)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('lpbd.item_id', $request->item_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('lpb.vendor_id', $request->vendor_id);
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
            ->distinct('pogd.id')
            ->selectRaw('

            pogd.id,
            pog.code,

            v.id as vendor_id,
            v.nama as vendor_name,
            v.code as vendor_code,

            items.kode as item_code,
            items.nama as item_name,

            pogdi.item_id,
            pogdi.quantity,

            case when lpb.exchange_rate = 1
                then pogdi.price
                else pogdi.price * lpb.exchange_rate
            end as price,

            case when lpb.exchange_rate = 1
                then pogdi.sub_total
                else pogdi.sub_total * lpb.exchange_rate
            end as sub_total,

            case when lpb.exchange_rate = 1
                then pogdi.tax_total
                else pogdi.tax_total * lpb.exchange_rate
            end as tax_total,

            case when lpb.exchange_rate = 1
                then pogdi.total
                else pogdi.total * lpb.exchange_rate
            end as total
        ')
            ->get();

        return $model;
    }


    /**
     * Get purchase order general report per period selected month
     *
     * @param $request
     * @param $year
     * @param $month
     * @return object|Collection
     */
    private function purchaseOrderGeneralPerPeriodSelectedMonth($request, $year, $month): object
    {
        $model = DB::table('item_receiving_reports as lpb')
            ->leftJoin('item_receiving_report_details as lpbd', 'lpbd.item_receiving_report_id', '=', 'lpb.id')
            ->join('purchase_order_general_detail_items as pogdi', 'pogdi.id', '=', 'lpbd.reference_id')
            ->join('purchase_order_general_details as pogd', 'pogdi.purchase_order_general_detail_id', '=', 'pogd.id')
            ->join('purchase_order_generals as pog', 'pog.id', '=', 'pogd.purchase_order_general_id')
            ->join('vendors as v', 'v.id', 'pog.vendor_id')
            ->join('items', 'items.id', '=', 'lpbd.item_id')
            ->where('lpb.tipe', 'general')
            ->whereNull('lpb.deleted_at')
            ->whereMonth('lpb.date_receive', $month)
            ->whereYear('lpb.date_receive', $year)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('lpb.item_id', $request->item_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('pog.vendor_id', $request->vendor_id);
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
            ->distinct('pogd.id')
            ->selectRaw('

                pogd.id,
                pog.code,

                v.id as vendor_id,
                v.nama as vendor_name,
                v.code as vendor_code,

                items.kode as item_code,
                items.nama as item_name,

                pogdi.item_id,
                pogdi.quantity,

                case when lpb.exchange_rate = 1
                    then pogdi.price
                    else pogdi.price * lpb.exchange_rate
                end as price,

                case when lpb.exchange_rate = 1
                    then pogdi.sub_total
                    else pogdi.sub_total * lpb.exchange_rate
                end as sub_total,

                case when lpb.exchange_rate = 1
                    then pogdi.tax_total
                    else pogdi.tax_total * lpb.exchange_rate
                end as tax_total,

                case when lpb.exchange_rate = 1
                    then pogdi.total
                    else pogdi.total * lpb.exchange_rate
                end as total
            ')
            ->get();

        return $model;
    }

    /**
     * combine sale order general per period
     *
     * @param $previous_year_data
     * @param $selected_month_data
     * @param $january_to_selected_month_data
     * @return object|Collection|array
     */
    private function combinePurchaseOrderGeneralPerPeriod($previous_year_data, $selected_month_data, $january_to_selected_month_data): object|array

    {
        $all_data = $previous_year_data->merge($selected_month_data)->merge($january_to_selected_month_data);
        $customer_group = $all_data->unique('vendor_id');

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

        $result = $customer_group->map(function ($item) use ($previous_year_data, $selected_month_data, $january_to_selected_month_data, &$total, $all_data) {
            $vendor_id = $item->vendor_id;
            $vendor_name = $item->vendor_name;
            $vendor_code = $item->vendor_code;

            $_new_previous_year_data = $previous_year_data->where('vendor_id', $vendor_id);
            $_new_selected_month_data = $selected_month_data->where('vendor_id', $vendor_id);
            $_new_january_to_selected_month_data = $january_to_selected_month_data->where('vendor_id', $vendor_id);

            $item_group = $all_data->unique('item_id');

            $item_group = $item_group->map(function ($item) use ($_new_previous_year_data, $_new_selected_month_data, $_new_january_to_selected_month_data, $vendor_id, $vendor_name, $vendor_code, &$total) {
                $item_id = $item->item_id;
                $item_code = $item->item_code;
                $item_name = $item->item_name;
                $pog_code = $item->code;

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
                $item->pog_code = $pog_code;
                $item->vendor_id =  $vendor_id;
                $item->vendor_code =  $vendor_code;
                $item->vendor_name =  $vendor_name;
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
            })
                ->filter(function ($item) {
                    return $item->previous_year_quantity > 0 || $item->selected_month_quantity > 0 || $item->january_to_selected_month_quantity > 0;
                });

            return collect([
                'vendor_id' => $vendor_id,
                'vendor_name' => $vendor_name,
                'detail' => $item_group,
            ]);
        });

        return [
            'data' => $result,
            'total' => $total,
        ];
    }


    /**
     * Generate report purchase order general per period
     *
     * @param $request
     * @return array
     */
    private function purchaseOrderGeneralPerPeriod($request)
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
        $last_year_data = $this->purchaseOrderGeneralPerPeriodLastYear($request, $previous_year);
        $selected_month_data = $this->purchaseOrderGeneralPerPeriodSelectedMonth($request, $selected_year, $selected_month);
        $january_to_selected_month_data = $this->purchaseOrderGeneralPerPeriodJanuaryToSelectedMonthThisYear($request, $selected_year, $selected_month);
        // ! END GET DATA ============================================================================================================

        // return compact('last_year_data', 'selected_month_data', 'january_to_selected_month_data');

        // ! COMBINE DATA ============================================================================================================
        $model = $this->combinePurchaseOrderGeneralPerPeriod($last_year_data, $selected_month_data, $january_to_selected_month_data);

        // ! END COMBINE DATA ========================================================================================================

        return [
            'data' => $model['data'],
            'total' => $model['total'],
            'type' => 'per-periode-purchase-order-general',
            'period' => $request->month,
        ];
    }


    /**
     * Get data for purchase order general report
     *
     * @param $request
     * @return array
     */
    private function getReportPurchaseOrderGeneral($request): array
    {
        $model = DB::table('purchase_order_generals')
            ->leftJoin('vendors', 'purchase_order_generals.vendor_id', '=', 'vendors.id')
            ->leftJoin('branches', 'purchase_order_generals.branch_id', '=', 'branches.id')
            ->leftJoin('purchase_order_general_details', 'purchase_order_generals.id', '=', 'purchase_order_general_details.purchase_order_general_id')
            ->leftJoin('purchase_order_general_detail_items', 'purchase_order_general_details.id', '=', 'purchase_order_general_detail_items.purchase_order_general_detail_id')
            ->whereNull('purchase_order_generals.deleted_at')
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('purchase_order_generals.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('purchase_order_generals.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_general_detail_items.item_id', $request->item_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_generals.vendor_id', $request->vendor_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_order_generals.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_order_generals.branch_id', get_current_branch()->id);
            })
            ->distinct('purchase_order_generals.id')
            ->selectRaw('
                purchase_order_generals.id,
                purchase_order_generals.date,
                purchase_order_generals.code,
                purchase_order_generals.exchange_rate,
                vendors.nama as vendor_name
            ')
            ->get();

        $model_detail_ids = $model->pluck('id')->toArray();
        $model_detail_data = DB::table('purchase_order_general_details')
            ->whereIn('purchase_order_general_id', $model_detail_ids)
            ->where('type', 'main')
            ->selectRaw('
                purchase_order_general_details.id,
                purchase_order_general_details.purchase_order_general_id
            ')
            ->get();

        $model_details_item_ids = $model_detail_data->pluck('id')->toArray();
        $model_details_item_data = DB::table('purchase_order_general_detail_items')
            ->leftJoin('items', 'purchase_order_general_detail_items.item_id', '=', 'items.id')
            ->leftJoin('units', 'purchase_order_general_detail_items.unit_id', '=', 'units.id')
            ->leftJoin('purchase_order_general_details', 'purchase_order_general_detail_items.purchase_order_general_detail_id', '=', 'purchase_order_general_details.id')
            ->leftJoin('purchase_order_generals', 'purchase_order_general_details.purchase_order_general_id', '=', 'purchase_order_generals.id')
            ->whereIn('purchase_order_general_detail_id', $model_details_item_ids)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('item_id', $request->item_id);
            })
            ->selectRaw('
                purchase_order_general_detail_items.id,
                purchase_order_general_detail_items.purchase_order_general_detail_id,
                purchase_order_general_detail_items.quantity,
                purchase_order_general_detail_items.price,
                purchase_order_general_detail_items.sub_total,

                case when purchase_order_generals.exchange_rate = 1
                    then purchase_order_general_detail_items.sub_total
                    else purchase_order_general_detail_items.sub_total * purchase_order_generals.exchange_rate
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
     * combine data for purchase order general report
     *
     * @param array $data
     * @return array
     */
    public function combineReportPurchaseOrderGeneral(array $data): array|Collection
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
            $details = $model_detail_data->where('purchase_order_general_id', $item->id)->map(function ($item_detail) use ($model_details_item_data, $item, $total_all) {
                $data = $model_details_item_data->where('purchase_order_general_detail_id', $item_detail->id);
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
     * Generate purchase order general report
     *
     * @param $request
     * @return array
     */
    private function reportPurchaseOrderGeneral($request): array|Collection
    {
        // * get data
        $data = $this->getReportPurchaseOrderGeneral($request);

        // * combine data
        $results = $this->combineReportPurchaseOrderGeneral($data);

        return [
            'total_all' => $results['total_all'],
            'data' => $results['data'],
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => 'purchase-order-general',
            'headline' => 'pembelian-general'
        ];
    }

    /**
     * Generate summary purchase order general report
     *
     * @param $request
     * @return array
     */
    private function reportSummaryPurchaseOrderGeneral($request): array
    {
        $model = DB::table('supplier_invoices')
            ->leftJoin('branches', 'supplier_invoices.branch_id', '=', 'branches.id')
            ->leftJoin('vendors', 'supplier_invoices.vendor_id', '=', 'vendors.id')
            ->leftJoin('supplier_invoice_details', 'supplier_invoices.id', '=', 'supplier_invoice_details.supplier_invoice_id')
            ->whereNull('supplier_invoices.deleted_at')
            ->where('supplier_invoice_details.reference_model', PurchaseOrderGeneral::class)
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
            'type' => 'summary-purchase-order-general',
            'headline' => 'ringkasan-pembelian-general',
        ];
    }

    /**
     * Generate purchase order general detail report previous year
     *
     * @param $request
     * @param $year
     * @return object
     */
    private function getReportDetailPurchaseOrderGeneralPreviousYear($request, $year): object
    {
        // * get detail items data
        $model_detail_items_data = DB::table('purchase_order_general_detail_items')
            ->leftJoin('items', 'purchase_order_general_detail_items.item_id', '=', 'items.id')
            ->leftJoin('units', 'purchase_order_general_detail_items.unit_id', '=', 'units.id')
            ->leftJoin('purchase_order_general_details', 'purchase_order_general_detail_items.purchase_order_general_detail_id', '=', 'purchase_order_general_details.id')
            ->leftJoin('purchase_order_generals', 'purchase_order_general_details.purchase_order_general_id', '=', 'purchase_order_generals.id')
            ->leftJoin('vendors', 'purchase_order_generals.vendor_id', '=', 'vendors.id')
            ->whereNull('purchase_order_generals.deleted_at')
            ->whereYear('purchase_order_generals.date', $year)
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_generals.vendor_id', $request->vendor_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_order_generals.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_order_generals.branch_id', get_current_branch()->id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_general_detail_items.item_id', $request->item_id);
            })
            ->selectRaw('
                vendors.id as vendor_id,
                vendors.nama as vendor_name,
                purchase_order_general_detail_items.id,
                purchase_order_general_detail_items.purchase_order_general_detail_id,
                purchase_order_general_detail_items.item_id,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                purchase_order_general_detail_items.quantity_received as quantity,

                case when purchase_order_generals.exchange_rate = 1
                    then purchase_order_general_detail_items.price
                    else purchase_order_general_detail_items.price * purchase_order_generals.exchange_rate
                end as price
            ')
            ->get();

        return $model_detail_items_data;
    }

    /**
     * Generate purchase order general detail report selected month
     *
     * @param $request
     * @param $year
     * @param $month
     * @return object
     */
    private function getReportDetailPurchaseOrderGeneralSelectedMonth($request, $year, $month): object
    {
        // * get detail items data
        $model_detail_items_data = DB::table('purchase_order_general_detail_items')
            ->leftJoin('items', 'purchase_order_general_detail_items.item_id', '=', 'items.id')
            ->leftJoin('units', 'purchase_order_general_detail_items.unit_id', '=', 'units.id')
            ->leftJoin('purchase_order_general_details', 'purchase_order_general_detail_items.purchase_order_general_detail_id', '=', 'purchase_order_general_details.id')
            ->leftJoin('purchase_order_generals', 'purchase_order_general_details.purchase_order_general_id', '=', 'purchase_order_generals.id')
            ->leftJoin('vendors', 'purchase_order_generals.vendor_id', '=', 'vendors.id')
            ->whereNull('purchase_order_generals.deleted_at')
            ->whereMonth('purchase_order_generals.date', $month)
            ->whereYear('purchase_order_generals.date', $year)
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_generals.vendor_id', $request->vendor_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_order_generals.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_order_generals.branch_id', get_current_branch()->id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_general_detail_items.item_id', $request->item_id);
            })
            ->selectRaw('
                vendors.id as vendor_id,
                vendors.nama as vendor_name,
                purchase_order_general_detail_items.id,
                purchase_order_general_detail_items.purchase_order_general_detail_id,
                purchase_order_general_detail_items.item_id,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                purchase_order_general_detail_items.quantity_received as quantity,
                case when purchase_order_generals.exchange_rate = 1
                    then purchase_order_general_detail_items.price
                    else purchase_order_general_detail_items.price * purchase_order_generals.exchange_rate
                end as price
            ')
            ->get();

        return $model_detail_items_data;
    }

    /**
     * Generate purchase order general detail report january until selected month
     *
     * @param $request
     * @param $year
     * @param $month
     * @return object
     */
    private function getReportDetailPurchaseOrderGeneralJanuaryUntilSelectedMonth($request, $year, $month): object
    {
        // * get detail items data
        $model_detail_items_data = DB::table('purchase_order_general_detail_items')
            ->leftJoin('items', 'purchase_order_general_detail_items.item_id', '=', 'items.id')
            ->leftJoin('units', 'purchase_order_general_detail_items.unit_id', '=', 'units.id')
            ->leftJoin('purchase_order_general_details', 'purchase_order_general_detail_items.purchase_order_general_detail_id', '=', 'purchase_order_general_details.id')
            ->leftJoin('purchase_order_generals', 'purchase_order_general_details.purchase_order_general_id', '=', 'purchase_order_generals.id')
            ->leftJoin('vendors', 'purchase_order_generals.vendor_id', '=', 'vendors.id')
            ->whereNull('purchase_order_generals.deleted_at')
            ->whereMonth('purchase_order_generals.date', '<=', $month)
            ->whereYear('purchase_order_generals.date', $year)
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_generals.vendor_id', $request->vendor_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_order_generals.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_order_generals.branch_id', get_current_branch()->id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_general_detail_items.item_id', $request->item_id);
            })
            ->selectRaw('
                vendors.id as vendor_id,
                vendors.nama as vendor_name,
                purchase_order_general_detail_items.id,
                purchase_order_general_detail_items.purchase_order_general_detail_id,
                purchase_order_general_detail_items.item_id,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                purchase_order_general_detail_items.quantity_received as quantity,
                case when purchase_order_generals.exchange_rate = 1
                    then purchase_order_general_detail_items.price
                    else purchase_order_general_detail_items.price * purchase_order_generals.exchange_rate
                end as price
            ')
            ->get();

        return $model_detail_items_data;
    }

    private function combineReportDetailPurchaseOrderGeneral(array $data): array|Collection
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
     * Generate detail purchase order general report
     *
     * @param $request
     * @return array
     */
    private function reportDetailPurchaseOrderGeneral($request): array|Collection
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
        $previous_year_data = $this->getReportDetailPurchaseOrderGeneralPreviousYear($request, $previous_year);
        $selected_month_data = $this->getReportDetailPurchaseOrderGeneralSelectedMonth($request, $selected_year, $selected_month);
        $january_until_selected_month_data = $this->getReportDetailPurchaseOrderGeneralJanuaryUntilSelectedMonth($request, $selected_year, $selected_month);

        // return compact('previous_year_data', 'selected_month_data', 'january_until_selected_month_data');

        // ! COMBINE AND GET RESULT
        $results = $this->combineReportDetailPurchaseOrderGeneral([
            $previous_year_data,
            $selected_month_data,
            $january_until_selected_month_data
        ]);

        return [
            'data' => $results['results'],
            'total' => $results['total_all'],
            'period' => $request->month,
            'type' => 'detail-purchase-order-general'
        ];
    }

    /**
     * Get report detail purchase order general previous year
     *
     * @param $request
     * @return array
     */
    public function reportPurchaseOrderGeneralReportReceiving($request): array
    {
        $model = DB::table('item_receiving_report_details')
            ->leftJoin('item_receiving_reports', 'item_receiving_reports.id', '=', 'item_receiving_report_details.item_receiving_report_id')
            ->leftJoin('items', 'items.id', '=', 'item_receiving_report_details.item_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'item_receiving_reports.vendor_id')
            ->leftJoin('branches', 'branches.id', '=', 'item_receiving_reports.branch_id')
            ->leftJoin('purchase_order_generals', 'purchase_order_generals.id', '=', 'item_receiving_reports.reference_id')
            ->leftJoin('purchase_order_general_detail_items', 'purchase_order_general_detail_items.id', '=', 'item_receiving_report_details.reference_id')
            ->leftJoin('units', 'units.id', '=', 'purchase_order_general_detail_items.unit_id')
            ->whereNull('item_receiving_reports.deleted_at')
            ->where('item_receiving_reports.tipe', 'general')
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('item_receiving_reports.date_receive', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('item_receiving_reports.date_receive', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_generals.vendor_id', $request->vendor_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('purchase_order_generals.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('purchase_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('purchase_order_generals.branch_id', get_current_branch()->id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_general_detail_items.item_id', $request->item_id);
            })
            ->selectRaw('
                item_receiving_reports.id,
                item_receiving_reports.kode as code,
                item_receiving_reports.date_receive as date,
                purchase_order_generals.code as purchase_code,
                purchase_order_generals.exchange_rate,
                vendors.nama as vendor_name,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                item_receiving_report_details.jumlah_diterima as quantity,
                purchase_order_general_detail_items.id as purchase_order_general_detail_item_id,
                purchase_order_general_detail_items.price,
                (item_receiving_report_details.jumlah_diterima * purchase_order_general_detail_items.price) as sub_total,
                (item_receiving_report_details.jumlah_diterima * purchase_order_general_detail_items.price * purchase_order_generals.exchange_rate) as sub_total_idr
            ')
            ->get();

        $taxes = PurchaseOrderGeneralDetailItemTax::with('tax')
            ->whereIn('purchase_order_general_detail_item_id', $model->pluck('purchase_order_general_detail_item_id'))
            ->get();

        $unique_taxes = $taxes->unique('tax_id');
        $model = $model->map(function ($item) use ($taxes) {
            $item->taxes = $taxes->where('purchase_order_general_detail_item_id', $item->purchase_order_general_detail_item_id)
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

        // dd($total_all);  

        return [
            'data' => $model,
            'total_all' => $total_all,
            'type' => 'purchase-order-general-receiving',
            'headline' => 'penerimaan-pembelian-general',
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'unique_taxes' => $unique_taxes,
        ];
    }

    private function reportClosedPurchaseOrderGeneral($request)
    {
        $data = DB::table('purchase_order_general_detail_items')
            ->join('items', 'items.id', '=', 'purchase_order_general_detail_items.item_id')
            ->join('units', 'units.id', '=', 'purchase_order_general_detail_items.unit_id')
            ->join('purchase_order_general_details', 'purchase_order_general_details.id', '=', 'purchase_order_general_detail_items.purchase_order_general_detail_id')
            ->join('purchase_order_generals', 'purchase_order_generals.id', '=', 'purchase_order_general_details.purchase_order_general_id')
            ->join('branches', 'branches.id', '=', 'purchase_order_generals.branch_id')
            ->join('vendors', 'vendors.id', '=', 'purchase_order_generals.vendor_id')
            ->whereNull('purchase_order_generals.deleted_at')
            ->whereIn('purchase_order_generals.status', ['done', 'approve'])
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('purchase_order_generals.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('purchase_order_generals.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('purchase_order_general_detail_items.item_id', $request->item_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_order_generals.vendor_id', $request->vendor_id);
            })
            ->orderBy('purchase_order_generals.date', 'asc')
            ->select(
                'purchase_order_generals.date',
                'purchase_order_generals.id',
                'purchase_order_generals.code',
                'purchase_order_generals.payment_description',
                'purchase_order_generals.total',
                'purchase_order_generals.exchange_rate',
                'purchase_order_generals.status',
                'branches.name as branch_name',
                'vendors.nama as vendor_name',
                'vendors.alamat as vendor_address',
                'vendors.code as vendor_id',
                'items.nama as item_name',
                'units.name as unit_name',
                'purchase_order_general_detail_items.price',
                'purchase_order_general_detail_items.quantity',
                'purchase_order_general_detail_items.total',
            )
            ->get();

        $purchase_order_generals = $data->unique('id');

        $item_receiving_reports = DB::table('item_receiving_reports')
            ->whereNull('item_receiving_reports.deleted_at')
            ->whereIn('status', ['approve', 'done'])
            ->whereIn('reference_id', $purchase_order_generals->pluck('id'))
            ->where('tipe', 'general')
            ->select('reference_id', 'date_receive')
            ->get();

        $purchase_order_generals = $purchase_order_generals->map(function ($item) use ($data, $item_receiving_reports) {
            $item->details = $data->where('id', $item->id)->values()->all();
            $item->date_receives = $item_receiving_reports->where('reference_id', $item->id)->pluck('date_receive')
                ->map(function ($item) {
                    return localDate($item);
                })->toArray();
            $item->total_all = $data->where('id', $item->id)
                ->sum('total');

            return $item;
        });

        return [
            'data' => $purchase_order_generals,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => 'laporan detail order pembelian'
        ];
    }
}
