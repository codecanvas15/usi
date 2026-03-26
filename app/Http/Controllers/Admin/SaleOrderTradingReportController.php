<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SaleOrderTrading\DeliveryOrderTradingExport;
use App\Exports\SaleOrderTrading\PerPeriodSaleOrderTradingExport;
use App\Exports\SaleOrderTrading\SaleOrderTradingDetailExport;
use App\Exports\SaleOrderTrading\SaleOrderTradingExport;
use App\Exports\SaleOrderTrading\SaleOrderTradingFakturPajakExport;
use App\Exports\SaleOrderTrading\SummarySaleOrderTradingExport;
use App\Http\Controllers\Controller;
use App\Models\InvoiceTrading;
use App\Models\SoTrading;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class SaleOrderTradingReportController extends Controller
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
    protected string $view_folder = 'sale-order-trading-report';

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
            case "sale-order-trading":
                $data = $this->saleOrderTrading($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SaleOrderTradingExport::class;
                break;
            case "sale-order-trading-faktur-pajak":
                $data = $this->saleOrderTradingFakturPajak($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SaleOrderTradingFakturPajakExport::class;
                break;
            case "summary-sale-order-trading":
                $data = $this->saleOrderTradingSummary($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SummarySaleOrderTradingExport::class;
                break;
            case "sale-order-trading-detail":
                $data = $this->saleOrderTradingDetail($request);
                $orientation = 'landscape';
                $paper_size = 'a2';
                $excel_export = SaleOrderTradingDetailExport::class;
                break;
            case "delivery-order-trading":
                $data = $this->deliveryOrderTrading($request);
                $orientation = 'landscape';
                $paper_size = 'a1';
                $excel_export = DeliveryOrderTradingExport::class;
                break;
            case "per-periode-sale-order-trading":
                $this->validate($request, [
                    'month' => 'required',
                ]);

                $data = $this->perPeriodSaleOrderTrading($request);
                $orientation = 'landscape';
                $paper_size = 'a2';
                $excel_export = PerPeriodSaleOrderTradingExport::class;
                break;

            case "per-periode-sale-order-trading-2":
                $this->validate($request, [
                    'month' => 'required',
                ]);

                $data = $this->perPeriodSaleOrderTrading2($request);
                $orientation = 'landscape';
                $paper_size = 'a2';
                $excel_export = PerPeriodSaleOrderTradingExport::class;
                break;

            case "daily-sale-order-trading-item-detail-customer":

                $data = $this->dialySaleOrderTradingItemDetailCustomer($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderTrading\DialySaleOrderTradingItemDetailCustomerExport::class;
                break;

            case "monthly-sale-order-trading-item-detail-customer":

                $this->validate($request, [
                    'month' => 'required',
                ]);

                $data = $this->monthlySaleOrderTradingItemDetailCustomer($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderTrading\MonthlySaleOrderTradingItemDetailCustomerExport::class;
                break;

            case "sale-order-trading-outstanding":

                $data = $this->saleOrderTradingOutstandingReport($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderTrading\SaleOrderTradingOutstandingExport::class;
                break;

            case "stock-comparison-with-sale-order-trading":

                $data = $this->stockComparisonWithSaleOrderTrading($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderTrading\StockComparisonWithSaleOrderTradingExport::class;
                break;

            case "debt-due-sale-order-trading":

                $data = $this->debtDueSaleOrderTrading($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderTrading\debtDueSaleOrderTradingExport::class;
                break;
            case "compare-so-po":
                $data = $this->compareSOPO($request);

                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderTrading\CompareSoPo::class;
                break;
            case "losses-sales-order":
                $data = $this->lossesSalesOrderReport($request);

                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderTrading\LossesSalesOrderReportExport::class;
                break;
            case "trading-sales-detail-additional":
                $data = $this->tradingSalesDetailAdditional($request);

                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderTrading\TradingSalesDetailAdditionalExport::class;
                break;

            default:
                return redirect()->route("admin.$this->view_folder.report")->with($this->ResponseMessageCRUD(false, "report", "selected report type was not found"));
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
            return redirect()->route("admin.sale-order-trading-report.report")->with($this->ResponseMessageCRUD(false, "report", "selected export format was not found"));
        }
    }

    /**
     * Generate report for sale order trading
     *
     * @param $request
     * @return array
     */
    private function saleOrderTrading($request): array
    {
        // ! GET PARENT DATA
        $model = DB::table('invoice_tradings')
            ->leftJoin('sale_orders', 'sale_orders.id', '=', 'invoice_tradings.so_trading_id')
            ->leftJoin('invoice_trading_details as itd', 'invoice_tradings.id', 'itd.invoice_trading_id')
            ->leftJoin('delivery_orders as do', 'do.id', 'itd.delivery_order_id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_tradings.customer_id')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_tradings.branch_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_tradings.item_id')
            ->whereNull('invoice_tradings.deleted_at')
            ->whereNull('sale_orders.deleted_at')
            ->whereNull('do.deleted_at')
            ->whereNotIn('invoice_tradings.status', ['pending', 'revert', 'void', 'reject'])
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('invoice_tradings.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('invoice_tradings.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.customer_id', $request->customer_id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query
                    ->where('invoice_tradings.item_id', $request->item_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_tradings.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_tradings.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_tradings.id')
            ->selectRaw(
                'invoice_tradings.id,
                invoice_tradings.date,
                invoice_tradings.due_date,
                invoice_tradings.kode as code,
                invoice_tradings.reference as reference,
                invoice_tradings.nomor_po_external as po_external,
                invoice_tradings.payment_status,
                invoice_tradings.jumlah,
                invoice_tradings.harga,
                do.code as do_code,
                do.target_delivery as do_target_delivery,
                sale_orders.nomor_so as sale_order_code,
                customers.nama as customer_name,
                customers.npwp as customer_npwp,
                branches.name as branch_name,
                invoice_tradings.exchange_rate,
                invoice_tradings.total,
                invoice_tradings.subtotal,
                invoice_tradings.subtotal_after_tax,
                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.total
                    else invoice_tradings.total * invoice_tradings.exchange_rate
                end as total_final'
            )
            ->when(Schema::hasColumn('invoice_tradings', 'losses'), function ($query) {
                return $query->addSelect('invoice_tradings.losses');
            })
            ->orderBy('invoice_tradings.date', 'asc')
            ->get();

        $modifiedData = [];
        foreach ($model->groupBy('sale_order_code') as $parrentIndex => $parrent) {
            $modifiedData[$parrentIndex] = [];
            foreach ($model->groupBy('sale_order_code')[$parrentIndex] as $key => $value) {
                $indexBefore = $key - 1;
                if ($key == 0) {
                    array_push($modifiedData[$parrentIndex], $value);
                } else {
                    if ($model->groupBy('sale_order_code')[$parrentIndex][$key]->id != $model->groupBy('sale_order_code')[$parrentIndex][$indexBefore]->id) {
                        array_push($modifiedData[$parrentIndex], $value);
                    }
                }
            }

            if (count($modifiedData[$parrentIndex]) > 0) {
                foreach ($modifiedData[$parrentIndex] as $index => $modify) {
                    $modify->delivery_orders = [];
                    foreach ($model->groupBy('sale_order_code')[$parrentIndex] as $key => $value) {
                        if ($modify->id == $value->id) {
                            array_push($modify->delivery_orders, [
                                'code' => $value->do_code,
                                'target_delivery' => $value->do_target_delivery
                            ]);
                        }
                    }
                }
            }
            $modifiedData[$parrentIndex] = collect($modifiedData[$parrentIndex]);
        }

        return [
            'data' => collect($modifiedData),
            'type' => "penjualan-trading",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    private function saleOrderTradingFakturPajak($request): array
    {
        $model = DB::table('invoice_tradings')
            ->leftJoin('invoice_trading_details', 'invoice_trading_details.invoice_trading_id', '=', 'invoice_tradings.id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_tradings.customer_id')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_tradings.branch_id')
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('invoice_tradings.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('invoice_tradings.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.customer_id', $request->customer_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('invoice_tradings.status', $request->status);
            })
            ->whereNull('invoice_tradings.deleted_at')
            ->selectRaw('
                invoice_tradings.id,
                invoice_tradings.date,
                customers.nama as customer_name,
                invoice_tradings.kode,
                invoice_tradings.status,
                invoice_tradings.reference,
                invoice_tradings.subtotal,
                invoice_tradings.additional_tax_total
            ')
            ->groupBy('invoice_tradings.id')
            ->get();

        return [
            'data' => $model,
            'type' => "laporan-penjualan-faktur-pajak",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     * Generate summary sale order trading report
     *
     * @param $request
     * @return array
     */
    private function saleOrderTradingSummary($request): array
    {
        $model = DB::table('invoice_tradings')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_tradings.customer_id')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_tradings.branch_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_tradings.item_id')
            ->leftJoin('sale_orders', 'sale_orders.id', '=', 'invoice_tradings.so_trading_id')
            ->whereNull('invoice_tradings.deleted_at')
            ->whereNotIn('invoice_tradings.status', ['pending', 'revert', 'void', 'reject'])
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('invoice_tradings.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('invoice_tradings.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.customer_id', $request->customer_id);
            })
            // ->when($request->warehouse_id, function ($query) use ($request) {
            //     return $query
            //         ->where('delivery_order_tradings.ware_house_id', $request->warehouse_id);
            // })
            ->when($request->item_id, function ($query) use ($request) {
                return $query
                    ->where('invoice_tradings.item_id', $request->item_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('invoice_tradings.payment_status', $request->status);
            })
            // ->when($request->status, function ($query) use ($request) {
            //     return $query->where('invoice_tradings.payment_status', $request->status);
            // })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_tradings.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_tradings.id')
            ->selectRaw('
                invoice_tradings.id,
                invoice_tradings.date,
                invoice_tradings.due_date,
                invoice_tradings.kode as code,
                invoice_tradings.payment_status,
                sale_orders.nomor_so as sale_order_code,

                customers.nama as customer_name,
                branches.name as branch_name,

                invoice_tradings.exchange_rate,
                invoice_tradings.total,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.total
                    else invoice_tradings.total * invoice_tradings.exchange_rate
                end as total_final,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.additional_tax_total
                    else invoice_tradings.additional_tax_total * invoice_tradings.exchange_rate
                end as totalTaxAdditional,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal
                    else (invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal) * invoice_tradings.exchange_rate
                end as totalTaxMain,

                COALESCE(
                    case when invoice_tradings.exchange_rate = 1
                        then invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal
                        else (invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal) * invoice_tradings.exchange_rate
                    end
                    +
                    case when invoice_tradings.exchange_rate = 1
                        then invoice_tradings.additional_tax_total
                        else invoice_tradings.additional_tax_total * invoice_tradings.exchange_rate
                    end
                ) as taxTotal,

                COALESCE(
                    case when invoice_tradings.exchange_rate = 1
                        then invoice_tradings.total
                        else invoice_tradings.total * invoice_tradings.exchange_rate
                    end
                    -
                    case when invoice_tradings.exchange_rate = 1
                        then invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal
                        else (invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal) * invoice_tradings.exchange_rate
                    end
                    -
                    case when invoice_tradings.exchange_rate = 1
                        then invoice_tradings.additional_tax_total
                        else invoice_tradings.additional_tax_total * invoice_tradings.exchange_rate
                    end
                ) as cleanTotal
            ')->get();

        return [
            'data' => $model,
            'type' => "ringkasan-penjualan-trading",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     * Get parent detail sale order trading report
     *
     * @param $request
     * @return array
     */
    private function parentDataSaleOrderGeneralDetail($request): object
    {
        $model = DB::table('invoice_tradings')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_tradings.customer_id')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_tradings.branch_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_tradings.item_id')
            ->leftJoin('sale_orders', 'sale_orders.id', '=', 'invoice_tradings.so_trading_id')
            ->whereNull('invoice_tradings.deleted_at')
            ->whereNotIn('invoice_tradings.status', ['pending', 'revert', 'void', 'reject'])
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('invoice_tradings.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('invoice_tradings.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.customer_id', $request->customer_id);
            })
            // ->when($request->warehouse_id, function ($query) use ($request) {
            //     return $query
            //         ->where('delivery_order_tradings.ware_house_id', $request->warehouse_id);
            // })
            ->when($request->item_id, function ($query) use ($request) {
                return $query
                    ->where('invoice_tradings.item_id', $request->item_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_tradings.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_tradings.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_tradings.id')
            ->selectRaw('
                invoice_tradings.id,
                invoice_tradings.date,
                invoice_tradings.due_date,
                invoice_tradings.kode as code,
                invoice_tradings.payment_status,
                invoice_tradings.lost_tolerance,
                invoice_tradings.calculate_from,
                invoice_tradings.lost_tolerance_type,
                invoice_tradings.tolerance_amount,
                invoice_tradings.total_lost,
                invoice_tradings.total_jumlah_dikirim,
                invoice_tradings.jumlah,
                invoice_tradings.harga,
                invoice_tradings.exchange_rate,
                invoice_tradings.subtotal,
                (invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal) as total_tax,
                invoice_tradings.total,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.subtotal
                    else invoice_tradings.subtotal * invoice_tradings.exchange_rate
                end as subtotal_local,

                case when invoice_tradings.exchange_rate = 1
                    then (invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal)
                    else (invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal) * invoice_tradings.exchange_rate
                end as total_tax_local,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.total
                    else invoice_tradings.total * invoice_tradings.exchange_rate
                end as total_local,

                sale_orders.nomor_so as sale_order_code,
                customers.nama as customer_name,

                branches.name as branch_name,
                items.nama as item_name,
                items.kode as item_code
            ')
            ->get();

        return $model;
    }

    /**
     * Get sale order trading detail report details data
     *
     * @param $invoice_trading_ids
     * @return object
     */
    private function saleOrderTradingDetailData($invoice_trading_ids): object
    {
        $model = DB::table('invoice_trading_details')
            ->leftJoin('delivery_orders', 'delivery_orders.id', '=', 'invoice_trading_details.delivery_order_id')
            ->whereIn('invoice_trading_details.invoice_trading_id', $invoice_trading_ids)
            ->selectRaw('
                invoice_trading_details.id,
                invoice_trading_details.invoice_trading_id,
                invoice_trading_details.jumlah_dikirim,
                invoice_trading_details.jumlah_diterima,

                delivery_orders.code as delivery_order_code,
                delivery_orders.load_quantity_realization,
                delivery_orders.load_quantity,
                delivery_orders.unload_quantity,
                delivery_orders.unload_quantity_realization,
                delivery_orders.status,
                delivery_orders.target_delivery
            ')
            ->get();

        return $model;
    }

    /**
     * Get sale order trading detail taxes
     *
     * @param $invoice_trading_ids
     * @return object
     */
    private function saleOrderTradingDetailTaxes($invoice_trading_ids): object
    {
        $model = DB::table('invoice_trading_taxes')
            ->whereIn('invoice_trading_id', $invoice_trading_ids)
            ->selectRaw('
                invoice_trading_taxes.id,
                invoice_trading_taxes.invoice_trading_id,
                invoice_trading_taxes.value
            ')
            ->get();

        return $model;
    }

    /**
     * Get sale order trading detail additional item
     *
     * @param $invoice_trading_ids
     * @return object
     */
    private function saleOrderTradingDetailAdditionalItem($invoice_trading_ids): object
    {
        $model = DB::table('inv_trading_add_ons')
            ->leftJoin('items', 'items.id', '=', 'inv_trading_add_ons.item_id')
            ->whereIn('invoice_trading_id', $invoice_trading_ids)
            ->selectRaw('
                inv_trading_add_ons.id,
                inv_trading_add_ons.invoice_trading_id,
                items.nama as item_name,
                items.kode as item_code,
                quantity,
                price,
                sub_total,
                total
            ')
            ->get();

        return $model;
    }

    /**
     * Get sale order trading detail additional item tax
     *
     * @param $invoice_trading_additional_ids
     * @return object
     */
    private function saleOrderTradingDetailAdditionalItemTax($invoice_trading_additional_ids): object
    {
        $model = DB::table('inv_trading_add_on_taxes')
            ->whereIn('inv_trading_add_on_id', $invoice_trading_additional_ids)
            ->selectRaw('
                inv_trading_add_on_taxes.id,
                inv_trading_add_on_taxes.inv_trading_add_on_id,
                inv_trading_add_on_taxes.value
            ')
            ->get();

        return $model;
    }

    /**
     * Combine sale order trading detail data
     *
     * @param array $data
     * @return Collection|object|array|mixed
     */
    private function combineSaleOrderTradingDetailData(array $data): object
    {
        // * destructure data
        list(
            $parent,
            $details,
            $taxes,
            $additionalItems,
            $additionalItemTaxes
        ) = $data;

        // * combine data
        $results = $parent->map(function ($item) use ($details, $taxes, $additionalItems, $additionalItemTaxes) {
            // ? quantity variables
            $SALE_ORDER_PRICE = $item->harga;
            $EXCHANGE_RATE = $item->exchange_rate;

            $LOST_TOLERANCE = 0;

            $TOTAL_SENDED = $details->where('invoice_trading_id', $item->id)->sum('load_quantity_realization');
            $TOTAL_RECEIVED = $details->where('invoice_trading_id', $item->id)->sum('unload_quantity_realization');
            $TOTAL_LOST = $TOTAL_SENDED - $TOTAL_RECEIVED;

            $IS_USING_CALCULATE_QUANTITY_RECEIVED = false;

            // ? calculation value variables
            $main_sub_total = 0;
            $main_sub_total_final = 0;
            $main_total_tax = 0;
            $main_total_tax_final = 0;
            $main_total = 0;
            $main_total_final = 0;

            $additional_sub_total = 0;
            $additional_sub_total_final = 0;
            $additional_total_tax = 0;
            $additional_total_tax_final = 0;
            $additional_total = 0;
            $additional_total_final = 0;

            $total = 0;
            $total_final = 0;

            // losses percentage = lost tolerance / total sended
            // qty losses tolerance = losses percentage * total sended
            if ($item->lost_tolerance_type == 'percent') {
                $losses_percentage = $item->lost_tolerance / $TOTAL_SENDED;
                $qty_losses_tolerance = $losses_percentage * $TOTAL_SENDED;
            }

            // losses percentage = total sended
            if ($item->lost_tolerance_type == 'liter') {
                $losses_percentage = $TOTAL_LOST / 100;
                $qty_losses_tolerance = $item->lost_tolerance;
            }

            $item->losses_percentage = $losses_percentage;
            $item->qty_losses_tolerance = $qty_losses_tolerance;

            // ? results variables
            $details_results = [];

            // ! CALCULATE FROM SALES ====================================================================================
            if ($item->calculate_from == 'sales_order') {
                // * lost tolerance type == liter
                if ($item->lost_tolerance_type == 'liter') {
                    // if lost tolerance is more than liter
                    if ($TOTAL_LOST > $item->lost_tolerance) {
                        $IS_USING_CALCULATE_QUANTITY_RECEIVED = true;
                    }
                }

                // * lost tolerance type == percent
                if ($item->lost_tolerance_type == 'percent') {
                    $total_lost_as_percent = ($TOTAL_SENDED == 0) ? 0 : $TOTAL_LOST / $TOTAL_SENDED;

                    // if lost tolerance is more than percent
                    if ($total_lost_as_percent > $LOST_TOLERANCE) {
                        $IS_USING_CALCULATE_QUANTITY_RECEIVED = true;
                    }
                }

                // ? COMBINE DATA DETAILS =============================================================================================

                // * combine with details data
                foreach ($details as $detail) {
                    if ($detail->invoice_trading_id == $item->id) {
                        $single_data = $detail;
                        $detail->price = $SALE_ORDER_PRICE;
                        $detail->exchange_rate = $EXCHANGE_RATE;

                        if ($IS_USING_CALCULATE_QUANTITY_RECEIVED) {
                            $detail->sub_total = $single_data->unload_quantity_realization * $SALE_ORDER_PRICE;
                            $detail->sub_total_final = $single_data->unload_quantity_realization * $SALE_ORDER_PRICE * $EXCHANGE_RATE;
                        } else {
                            $detail->sub_total = $single_data->load_quantity_realization * $SALE_ORDER_PRICE;
                            $detail->sub_total_final = $single_data->load_quantity_realization * $SALE_ORDER_PRICE * $EXCHANGE_RATE;
                        }
                        $detail->total = $detail->sub_total;
                        $detail->total_final = $detail->sub_total_final;

                        $detail->total_tax = 0;
                        $detail->total_tax_final = 0;

                        // * combine with taxes data
                        foreach ($taxes as $tax) {
                            if ($tax->invoice_trading_id == $item->id) {
                                $detail->total_tax += $tax->value * $detail->sub_total;
                                $detail->total_tax_final += $tax->value * $detail->sub_total_final;
                            }
                        }

                        $detail->total += $detail->total_tax;
                        $detail->total_final += $detail->total_tax_final;

                        $details_results[] = $detail;

                        $main_sub_total += $detail->sub_total;
                        $main_sub_total_final += $detail->sub_total_final;
                        $main_total_tax += $detail->total_tax;
                        $main_total_tax_final += $detail->total_tax_final;
                        $main_total += $detail->total;
                        $main_total_final += $detail->total_final;

                        $total += $detail->total;
                        $total_final += $detail->total_final;
                    }
                }
            }

            // ! CALCULATE FROM DELIVERY =================================================================================
            if ($item->calculate_from == 'delivery_order') {
                // ? COMBINE DATA DETAILS =============================================================================================

                // * combine with details data
                foreach ($details as $detail) {
                    if ($detail->invoice_trading_id == $item->id) {
                        $single_sended = $detail->load_quantity_realization;
                        $single_received = $detail->unload_quantity_realization;
                        $single_lost = $single_sended - $single_received;

                        // * lost tolerance type == liter
                        if ($item->lost_tolerance_type == 'liter') {
                            // if lost tolerance is more than liter
                            if ($single_lost > $item->lost_tolerance) {
                                $IS_USING_CALCULATE_QUANTITY_RECEIVED = true;
                            }
                        }

                        // * lost tolerance type == percent
                        if ($item->lost_tolerance_type == 'percent') {
                            $total_lost_as_percent = $single_lost / $single_sended;

                            // if lost tolerance is more than percent
                            if ($total_lost_as_percent > $LOST_TOLERANCE) {
                                $IS_USING_CALCULATE_QUANTITY_RECEIVED = true;
                            }
                        }

                        // * calculate
                        $single_data = $detail;
                        $detail->price = $SALE_ORDER_PRICE;
                        $detail->exchange_rate = $EXCHANGE_RATE;
                        $detail->sub_total = $SALE_ORDER_PRICE * ($IS_USING_CALCULATE_QUANTITY_RECEIVED ? $single_data->unload_quantity_realization : $single_data->load_quantity_realization);
                        $detail->sub_total_final = $SALE_ORDER_PRICE * ($IS_USING_CALCULATE_QUANTITY_RECEIVED ? $single_data->unload_quantity_realization : $single_data->load_quantity_realization) * $EXCHANGE_RATE;
                        $detail->total = $detail->sub_total;
                        $detail->total_final = $detail->sub_total_final;

                        $detail->total_tax = 0;
                        $detail->total_tax_final = 0;
                        // * combine with taxes data
                        foreach ($taxes as $tax) {
                            if ($tax->invoice_trading_id == $item->id) {
                                $detail->total_tax += $tax->value * $detail->sub_total;
                                $detail->total_tax_final += $tax->value * $detail->sub_total_final * $EXCHANGE_RATE;
                            }
                        }

                        $detail->total += $detail->total_tax;
                        $detail->total_final += $detail->total_tax_final;

                        $details_results[] = $detail;

                        $main_sub_total += $detail->sub_total;
                        $main_sub_total_final += $detail->sub_total_final;
                        $main_total_tax += $detail->total_tax;
                        $main_total_tax_final += $detail->total_tax_final;
                        $main_total += $detail->total;
                        $main_total_final += $detail->total_final;

                        $total += $detail->total;
                        $total_final += $detail->total_final;
                    }
                }
            }

            // ? COMBINE DATA ADDITIONAL =============================================================================================

            // * combine with additional items data
            $additional_items_data = [];
            foreach ($additionalItems as $additionalItem) {
                if ($additionalItem->invoice_trading_id == $item->id) {
                    $single_additional = $additionalItem;
                    $additionalItem->price = $single_additional->price;
                    $additionalItem->exchange_rate = $EXCHANGE_RATE;
                    $additionalItem->sub_total = $single_additional->quantity * $single_additional->price;
                    $additionalItem->sub_total_final = $single_additional->quantity * $single_additional->price * $EXCHANGE_RATE;
                    $additionalItem->total = $additionalItem->sub_total;
                    $additionalItem->total_final = $additionalItem->sub_total_final;
                    $additionalItem->total_tax = 0;
                    $additionalItem->total_tax_final = 0;

                    // * combine with additional items taxes data
                    foreach ($additionalItemTaxes as $additionalItemTax) {
                        if ($additionalItemTax->inv_trading_add_on_id == $additionalItem->id) {
                            $additionalItem->total_tax = $additionalItemTax->value * $additionalItem->sub_total;
                            $additionalItem->total_tax_final = $additionalItemTax->value * $additionalItem->sub_total_final;
                        }
                    }

                    $additionalItem->total = $additionalItem->sub_total + $additionalItem->total_tax;
                    $additionalItem->total_final = $additionalItem->sub_total_final + $additionalItem->total_tax_final;

                    $additional_items_data[] = $additionalItem;

                    $additional_sub_total += $additionalItem->sub_total;
                    $additional_sub_total_final += $additionalItem->sub_total_final;
                    $additional_total_tax += $additionalItem->total_tax;
                    $additional_total_tax_final += $additionalItem->total_tax_final;
                    $additional_total += $additionalItem->total;
                    $additional_total_final += $additionalItem->total_final;

                    $total += $additionalItem->total;
                    $total_final += $additionalItem->total_final;
                }
            }

            // * combine all data
            $item->delivery_orders = $details_results;
            $item->additional_items = $additional_items_data;

            $item->main_sub_total = $main_sub_total;
            $item->main_sub_total_final = $main_sub_total_final;
            $item->main_total_tax = $main_total_tax;
            $item->main_total_tax_final = $main_total_tax_final;
            $item->main_total = $main_total;
            $item->main_total_final = $main_total_final;

            $item->additional_sub_total = $additional_sub_total;
            $item->additional_sub_total_final = $additional_sub_total_final;
            $item->additional_total_tax = $additional_total_tax;
            $item->additional_total_tax_final = $additional_total_tax_final;
            $item->additional_total = $additional_total;
            $item->additional_total_final = $additional_total_final;

            $item->total_all = $main_total + $additional_total;
            $item->sub_total_all = $main_sub_total + $additional_sub_total;
            $item->total_tax_all = $main_total_tax + $additional_total_tax;
            $item->total_final_all = $main_total_final + $additional_total_final;
            $item->sub_total_final_all = $main_sub_total_final + $additional_sub_total_final;
            $item->total_tax_final_all = $main_total_tax_final + $additional_total_tax_final;

            $item->total = $total;
            $item->total_final = $total_final;

            return $item;
        });


        return $results;
    }


    /**
     * Generate detail sale order trading report
     *
     * @param $request
     * @return array
     */
    private function saleOrderTradingDetail($request): array
    {
        $parent = $this->parentDataSaleOrderGeneralDetail($request);
        $details = $this->saleOrderTradingDetailData($parent->pluck('id'));
        $taxes = $this->saleOrderTradingDetailTaxes($parent->pluck('id'));
        $additionalItems = $this->saleOrderTradingDetailAdditionalItem($parent->pluck('id'));
        $additionalItemTaxes = $this->saleOrderTradingDetailAdditionalItemTax($additionalItems->pluck('id'));

        $result = $this->combineSaleOrderTradingDetailData([
            $parent,
            $details,
            $taxes,
            $additionalItems,
            $additionalItemTaxes
        ]);

        return [
            'data' => $result,
            'type' => "rincian-penjualan-trading-per-customer",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     * Generate delivery order trading report
     *
     * @param $request
     * @return array
     */
    private function deliveryOrderTrading($request)
    {
        $model = DB::table('delivery_orders')
            ->leftJoin('sale_orders', function ($query) {
                $query->on('sale_orders.id', '=', 'delivery_orders.so_trading_id')
                    ->whereNull('sale_orders.deleted_at');
            })
            ->leftJoin('sale_order_details', 'sale_orders.id', '=', 'sale_order_details.so_trading_id')
            ->leftJoin('customers', 'customers.id', '=', 'sale_orders.customer_id')
            ->leftJoin('branches', 'branches.id', '=', 'delivery_orders.branch_id')
            ->leftJoin('items', 'items.id', '=', 'sale_order_details.item_id')
            ->leftJoin('ware_houses', 'ware_houses.id', '=', 'delivery_orders.ware_house_id')
            ->leftJoin('invoice_trading_details', function ($query) {
                $query->on('invoice_trading_details.delivery_order_id', '=', 'delivery_orders.id')
                    ->leftJoin('invoice_tradings', 'invoice_tradings.id', '=', 'invoice_trading_details.invoice_trading_id')
                    ->whereNotIn('invoice_tradings.status', ['revert', 'pending', 'reject', 'void'])
                    ->whereNull('invoice_tradings.deleted_at');
            })
            ->whereNull('delivery_orders.deleted_at')
            ->whereNotIn('delivery_orders.status', ['revert', 'pending', 'reject', 'void'])
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('delivery_orders.target_delivery', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('delivery_orders.target_delivery', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('sale_orders.customer_id', $request->customer_id);
            })
            ->when($request->sale_order_id, function ($query) use ($request) {
                return $query->where('sale_orders.id', $request->sale_order_id);
            })
            ->when($request->warehouse_id, function ($query) use ($request) {
                return $query
                    ->where('delivery_orders.ware_house_id', $request->warehouse_id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query
                    ->where('sale_order_details.item_id', $request->item_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('delivery_orders.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('delivery_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('delivery_orders.branch_id', get_current_branch()->id);
            })
            ->where('delivery_orders.type', "delivery-order")
            ->selectRaw('
                delivery_orders.id,
                delivery_orders.code,
                delivery_orders.target_delivery,

                customers.nama as customer_name,
                customers.lost_tolerance_type,
                customers.lost_tolerance,

                branches.name as branch_name,

                sale_orders.id as sale_order_id,
                sale_orders.nomor_so as sale_order_code,

                ware_houses.nama as warehouse_name,

                delivery_orders.description,
                delivery_orders.load_quantity,
                delivery_orders.load_quantity_realization,
                delivery_orders.unload_quantity,
                delivery_orders.unload_quantity_realization,
                CASE 
                    WHEN customers.lost_tolerance_type = "percent" THEN
                        delivery_orders.load_quantity_realization * customers.lost_tolerance
                    ELSE
                        customers.lost_tolerance
                END as customer_tolerance,
               
                CASE 
                    WHEN invoice_tradings.lost_tolerance_type = "percent" THEN
                        delivery_orders.load_quantity_realization * invoice_tradings.lost_tolerance
                    ELSE
                        invoice_tradings.lost_tolerance
                END as invoice_tolerance,
                
                invoice_tradings.jumlah as quantity_invoice,

                sale_order_details.harga,
                sale_orders.exchange_rate,

                delivery_orders.status,

                invoice_tradings.calculate_from as invoice_calculate_from,
                invoice_tradings.lost_tolerance_type as invoice_lost_tolerance_type,
                invoice_tradings.lost_tolerance as inv_lost_tolerance,
                invoice_tradings.kode as invoice_trading_code,
                invoice_tradings.total as invoice_trading_total,
                invoice_tradings.subtotal_after_tax as invoice_trading_subtotal_after_tax,
                invoice_tradings.after_additional_tax as invoice_trading_additional_after_tax
            ')
            ->orderBy('delivery_orders.id')
            ->orderBy('delivery_orders.code')
            ->groupBy('delivery_orders.id')
            ->get();

        $sales_orders = SoTrading::with([
            'so_trading_detail.item',
            'sale_order_taxes.tax',
            'sale_order_additionals.item',
            'sale_order_additionals.sale_order_additional_taxes.tax',
        ])
            ->whereIn('id', $model->pluck('sale_order_id')->toArray())
            ->get();

        $model = $model->map(function ($item)  use ($sales_orders) {
            $sale_order = $sales_orders->where('id', $item->sale_order_id)->first();
            $losses_percentage = 0;
            if ($item->load_quantity_realization != 0 &&  $item->unload_quantity_realization != 0) {
                $losses_percentage = ($item->load_quantity_realization - $item->unload_quantity_realization) / $item->load_quantity_realization * 100;
            }
            $item->losses_percentage = $losses_percentage;
            $item->tolerance = $item->invoice_tolerance ?? $item->customer_tolerance;

            $LOST_TOLERANCE_TYPE = $item->invoice_lost_tolerance_type ?? $item->lost_tolerance_type;
            $LOST_TOLERANCE = $item->inv_lost_tolerance ?? $item->customer_tolerance;

            $TOTAL_SENDED = $item->load_quantity_realization;
            $TOTAL_RECEIVED = $item->unload_quantity_realization;
            $TOTAL_LOST = $TOTAL_SENDED - $TOTAL_RECEIVED;
            $QUANTITY_SALE_ORDER_FINAL = 0;
            $SUB_TOTAL = 0;
            $TAX_TOTAL = 0;

            $ADDITIONAL_SUB_TOTAL = 0;
            $ADDITIONAL_TAX_TOTAL = 0;

            $SALE_ORDER_PRICE = $item->harga;
            if ($LOST_TOLERANCE_TYPE == 'percent') {
                $QTY_TOLERANCE = ($TOTAL_SENDED * $LOST_TOLERANCE);

                if ($TOTAL_LOST > $QTY_TOLERANCE) {
                    $QUANTITY_SALE_ORDER_FINAL = ($TOTAL_RECEIVED + $QTY_TOLERANCE);
                    $SUB_TOTAL = ($TOTAL_RECEIVED + $QTY_TOLERANCE) * $SALE_ORDER_PRICE;
                } else {
                    $QUANTITY_SALE_ORDER_FINAL = $TOTAL_SENDED;
                    $SUB_TOTAL = $TOTAL_SENDED * $SALE_ORDER_PRICE;
                }
            } elseif ($LOST_TOLERANCE_TYPE == 'liter') {
                if ($TOTAL_LOST > $LOST_TOLERANCE) {
                    $SUB_TOTAL = $SALE_ORDER_PRICE * ($TOTAL_RECEIVED + $LOST_TOLERANCE);
                    $QUANTITY_SALE_ORDER_FINAL = $TOTAL_RECEIVED + $LOST_TOLERANCE;
                } else {
                    $SUB_TOTAL = $SALE_ORDER_PRICE * $TOTAL_SENDED;
                    $QUANTITY_SALE_ORDER_FINAL = $TOTAL_SENDED;
                }
            }

            foreach ($sale_order->sale_order_taxes as $tax_key => $tax) {
                $TAX_TOTAL += $tax->tax->value * $SUB_TOTAL;
            }

            foreach ($sale_order->sale_order_additionals as $additional_key => $additional) {
                $final_qty = $QUANTITY_SALE_ORDER_FINAL;
                $single_additional_sub_total = $final_qty * $additional->price;
                $single_additional_tax_total = 0;

                // * create tax additional
                foreach ($additional->sale_order_additional_taxes as $tax_key => $tax) {
                    $single_additional_tax_total += $single_additional_sub_total * $tax->value;
                }


                $ADDITIONAL_SUB_TOTAL += $single_additional_sub_total;
                $ADDITIONAL_TAX_TOTAL += $single_additional_tax_total;
            }

            $item->delivery_subtotal = $SUB_TOTAL;
            $item->delivery_tax_total = $TAX_TOTAL;
            $item->delivery_main_total = $SUB_TOTAL + $TAX_TOTAL;
            $item->delivery_additional_subtotal = $ADDITIONAL_SUB_TOTAL;
            $item->delivery_additional_tax_total = $ADDITIONAL_TAX_TOTAL;
            $item->delivery_additional_total = $ADDITIONAL_SUB_TOTAL + $ADDITIONAL_TAX_TOTAL;
            $item->delivery_grand_total = $SUB_TOTAL + $TAX_TOTAL + $ADDITIONAL_SUB_TOTAL + $ADDITIONAL_TAX_TOTAL;

            return $item;
        });

        return [
            'data' => $model->map(function ($item) {
                $item->target_delivery = $item->target_delivery ? date('d-m-Y', strtotime($item->target_delivery)) : null;

                $item->total = $item->harga * $item->load_quantity;
                $item->total_final = $item->total * $item->exchange_rate;
                $item->losses = $item->load_quantity_realization - $item->unload_quantity_realization;

                return $item;
            }),
            'type' => "delivery-order-trading",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     * Get per period sale order trading previous year data
     *
     * @param $request
     * @param $year
     * @return object
     */
    private function getPerPeriodSaleOrderTradingDataPreviousYear2($request, $year): object
    {
        $model = DB::table('invoice_tradings')
            ->leftJoin('inv_trading_add_ons as add_on', 'add_on.invoice_trading_id', 'invoice_tradings.id')
            ->leftJoin('items as add_on_item', 'add_on_item.id', 'add_on.item_id')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_tradings.branch_id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_tradings.customer_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_tradings.item_id')
            // ->leftJoin('ware_houses', 'ware_houses.id', '=', 'delivery_orders.ware_house_id')
            ->whereNull('invoice_tradings.deleted_at')
            ->whereYear('invoice_tradings.date', $year)
            ->whereNotIn('invoice_tradings.status', ['revert', 'pending', 'reject', 'void'])
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.customer_id', $request->customer_id);
            })
            // ->when($request->warehouse_id, function ($query) use ($request) {
            //     return $query
            //         ->where('delivery_order_tradings.ware_house_id', $request->warehouse_id);
            // })
            ->when($request->item_id, function ($query) use ($request) {
                return $query
                    ->where('invoice_tradings.item_id', $request->item_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_tradings.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_tradings.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_tradings.id')
            ->selectRaw('
                invoice_tradings.id,

                invoice_tradings.jumlah as quantity,
                invoice_tradings.date,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.harga
                    else invoice_tradings.harga * invoice_tradings.exchange_rate
                end as harga,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.subtotal
                    else invoice_tradings.subtotal * invoice_tradings.exchange_rate
                end as sub_total,

                invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal as total_tax,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.subtotal_after_tax
                    else invoice_tradings.subtotal_after_tax * invoice_tradings.exchange_rate
                end as subtotal_after_tax,

                customers.id as customer_id,
                customers.nama as customer_name,
                customers.code as customer_code,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,

                add_on.quantity as add_on_quantity,
                add_on.sub_total as add_on_subtotal,
                add_on.total as add_on_total,

                add_on_item.kode as add_on_item_code,
                add_on_item.nama as add_on_item_name
            ')
            ->get();

        $data_details = DB::table('invoice_trading_details')
            ->whereIn('invoice_trading_details.invoice_trading_id', $model->pluck('id')->toArray())
            ->selectRaw('
                invoice_trading_details.id,
                invoice_trading_details.invoice_trading_id,
                invoice_trading_details.jumlah_dikirim,
                invoice_trading_details.jumlah_diterima
            ')
            ->get();

        $results = $model->map(function ($item) use ($data_details) {
            $item->quantity_sended = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_dikirim');
            $item->quantity_received = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_diterima');

            return $item;
        });

        return $results;
    }

    /**
     * Get per period sale order trading selected month
     *
     * @param $request
     * @param $year
     * @param $Month
     * @return object
     */
    private function getPerPeriodSaleOrderTradingDataSelectedMonth2($request, $year, $Month): object
    {
        $model = DB::table('invoice_tradings')
            ->leftJoin('inv_trading_add_ons as add_on', 'add_on.invoice_trading_id', 'invoice_tradings.id')
            ->leftJoin('items as add_on_item', 'add_on_item.id', 'add_on.item_id')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_tradings.branch_id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_tradings.customer_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_tradings.item_id')
            // ->leftJoin('ware_houses', 'ware_houses.id', '=', 'invoice_tradings.ware_house_id')
            ->whereNull('invoice_tradings.deleted_at')
            ->whereMonth('invoice_tradings.date', $Month)
            ->whereYear('invoice_tradings.date', $year)
            ->whereNotIn('invoice_tradings.status', ['revert', 'pending', 'reject', 'void'])
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.customer_id', $request->customer_id);
            })
            // ->when($request->warehouse_id, function ($query) use ($request) {
            //     return $query
            //         ->where('delivery_order_tradings.ware_house_id', $request->warehouse_id);
            // })
            ->when($request->item_id, function ($query) use ($request) {
                return $query
                    ->where('invoice_tradings.item_id', $request->item_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_tradings.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_tradings.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_tradings.id')
            ->selectRaw('
                invoice_tradings.id,

                invoice_tradings.jumlah as quantity,
                invoice_tradings.date,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.harga
                    else invoice_tradings.harga * invoice_tradings.exchange_rate
                end as harga,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.subtotal
                    else invoice_tradings.subtotal * invoice_tradings.exchange_rate
                end as sub_total,

                invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal as total_tax,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.subtotal_after_tax
                    else invoice_tradings.subtotal_after_tax * invoice_tradings.exchange_rate
                end as subtotal_after_tax,

                customers.id as customer_id,
                customers.nama as customer_name,
                customers.code as customer_code,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,

                add_on.quantity as add_on_quantity,
                add_on.sub_total as add_on_subtotal,
                add_on.total as add_on_total,

                add_on_item.kode as add_on_item_code,
                add_on_item.nama as add_on_item_name
            ')
            ->get();

        $data_details = DB::table('invoice_trading_details')
            ->whereIn('invoice_trading_details.invoice_trading_id', $model->pluck('id')->toArray())
            ->selectRaw('
                invoice_trading_details.id,
                invoice_trading_details.invoice_trading_id,
                invoice_trading_details.jumlah_dikirim,
                invoice_trading_details.jumlah_diterima
            ')
            ->get();

        $results = $model->map(function ($item) use ($data_details) {
            $item->quantity_sended = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_dikirim');
            $item->quantity_received = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_diterima');

            return $item;
        });

        return $results;
    }

    /**
     * Get per period sale order trading January to selected month
     *
     * @param $request
     * @param $year
     * @param $Month
     * @return object
     */
    private function getPerPeriodSaleOrderTradingDataJanuaryToSelectedMonth2($request, $year, $Month): object
    {
        $model = DB::table('invoice_tradings')
            ->leftJoin('inv_trading_add_ons as add_on', 'add_on.invoice_trading_id', 'invoice_tradings.id')
            ->leftJoin('items as add_on_item', 'add_on_item.id', 'add_on.item_id')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_tradings.branch_id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_tradings.customer_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_tradings.item_id')
            // ->leftJoin('ware_houses', 'ware_houses.id', '=', 'invoice_tradings.ware_house_id')
            ->whereNull('invoice_tradings.deleted_at')
            ->whereMonth('invoice_tradings.date', '<=', $Month)
            ->whereYear('invoice_tradings.date', $year)
            ->whereNotIn('invoice_tradings.status', ['revert', 'pending', 'reject', 'void'])
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.customer_id', $request->customer_id);
            })
            // ->when($request->warehouse_id, function ($query) use ($request) {
            //     return $query
            //         ->where('delivery_order_tradings.ware_house_id', $request->warehouse_id);
            // })
            ->when($request->item_id, function ($query) use ($request) {
                return $query
                    ->where('invoice_tradings.item_id', $request->item_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_tradings.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_tradings.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_tradings.id')
            ->selectRaw('
                invoice_tradings.id,

                invoice_tradings.jumlah as quantity,
                invoice_tradings.date,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.harga
                    else invoice_tradings.harga * invoice_tradings.exchange_rate
                end as harga,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.subtotal
                    else invoice_tradings.subtotal * invoice_tradings.exchange_rate
                end as sub_total,

                invoice_tradings.subtotal_after_tax - invoice_tradings.subtotal as total_tax,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.subtotal_after_tax
                    else invoice_tradings.subtotal_after_tax * invoice_tradings.exchange_rate
                end as subtotal_after_tax,

                customers.id as customer_id,
                customers.nama as customer_name,
                customers.code as customer_code,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,

                add_on.quantity as add_on_quantity,
                add_on.sub_total as add_on_subtotal,
                add_on.total as add_on_total,

                add_on_item.kode as add_on_item_code,
                add_on_item.nama as add_on_item_name
            ')
            ->get();

        $data_details = DB::table('invoice_trading_details')
            ->whereIn('invoice_trading_details.invoice_trading_id', $model->pluck('id')->toArray())
            ->selectRaw('
                invoice_trading_details.id,
                invoice_trading_details.invoice_trading_id,
                invoice_trading_details.jumlah_dikirim,
                invoice_trading_details.jumlah_diterima
            ')
            ->get();

        $results = $model->map(function ($item) use ($data_details) {
            $item->quantity_sended = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_dikirim');
            $item->quantity_received = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_diterima');

            return $item;
        });

        return $results;
    }

    /**
     * Combine sale order trading per period
     *
     * @param array $data
     */
    private function combinePerPeriodSaleOrderTradingData2(array $data): object|array
    {
        list(
            $lastYearData,
            $selectedMonthData,
            $januaryToSelectedMonthData
        ) = $data;

        $grouped = $selectedMonthData
            ->unique('customer_id');

        $total = new \stdClass();
        $total->previous_year_quantity = 0;
        $total->previous_year_sub_total = 0;
        $total->previous_year_total_tax = 0;
        $total->previous_year_total = 0;
        $total->previous_year_add_on_sub_total = 0;
        $total->previous_year_add_on_total_tax = 0;
        $total->previous_year_add_on_total = 0;
        $total->selected_month_quantity = 0;
        $total->selected_month_sub_total = 0;
        $total->selected_month_total_tax = 0;
        $total->selected_month_total = 0;
        $total->selected_month_add_on_sub_total = 0;
        $total->selected_month_add_on_total_tax = 0;
        $total->selected_month_add_on_total = 0;
        $total->january_to_selected_month_quantity = 0;
        $total->january_to_selected_month_sub_total = 0;
        $total->january_to_selected_month_total_tax = 0;
        $total->january_to_selected_month_total = 0;
        $total->january_to_selected_month_add_on_sub_total = 0;
        $total->january_to_selected_month_add_on_total_tax = 0;
        $total->january_to_selected_month_add_on_total = 0;

        $result = $grouped->map(function ($item) use (
            $lastYearData,
            $selectedMonthData,
            $januaryToSelectedMonthData,
            &$total
        ) {
            $itemGroupLastYear = $lastYearData->where('customer_id', $item->customer_id)->groupBy('item_id');
            $itemGroupSelectedMonth = $selectedMonthData->where('customer_id', $item->customer_id)->groupBy('item_id');
            $itemGroupJanuaryToSelectedMonth = $januaryToSelectedMonthData->where('customer_id', $item->customer_id)->groupBy('item_id');

            $result = $itemGroupSelectedMonth->map(function ($item) use (
                $itemGroupLastYear,
                $itemGroupSelectedMonth,
                $itemGroupJanuaryToSelectedMonth,
                &$total
            ) {
                $itemGroupLastYear = $itemGroupLastYear->get($item->first()->item_id);
                $itemGroupSelectedMonth = $itemGroupSelectedMonth->get($item->first()->item_id);
                $itemGroupJanuaryToSelectedMonth = $itemGroupJanuaryToSelectedMonth->get($item->first()->item_id);

                $previous_year_quantity = $itemGroupLastYear ? $itemGroupLastYear->sum('quantity') : 0;
                $previous_year_sub_total = $itemGroupLastYear ? $itemGroupLastYear->sum('sub_total') : 0;
                $previous_year_total_tax = $itemGroupLastYear ? $itemGroupLastYear->sum('total_tax') : 0;
                $previous_year_total = $itemGroupLastYear ? $itemGroupLastYear->sum('subtotal_after_tax') : 0;

                $previous_year_add_on_sub_total = $itemGroupLastYear ? $itemGroupLastYear->sum('add_on_subtotal') : 0;
                $previous_year_add_on_total_tax = $itemGroupLastYear ? $itemGroupLastYear->sum('add_on_total') - $itemGroupLastYear->sum('add_on_subtotal') : 0;
                $previous_year_add_on_total = $itemGroupLastYear ? $itemGroupLastYear->sum('add_on_total') : 0;

                $selected_month_quantity = $itemGroupSelectedMonth ? $itemGroupSelectedMonth->sum('quantity') : 0;
                $selected_month_sub_total = $itemGroupSelectedMonth ? $itemGroupSelectedMonth->sum('sub_total') : 0;
                $selected_month_total_tax = $itemGroupSelectedMonth ? $itemGroupSelectedMonth->sum('total_tax') : 0;
                $selected_month_total = $itemGroupSelectedMonth ? $itemGroupSelectedMonth->sum('subtotal_after_tax') : 0;

                $selected_month_add_on_sub_total = $itemGroupSelectedMonth ? $itemGroupSelectedMonth->sum('add_on_subtotal') : 0;
                $selected_month_add_on_total_tax = $itemGroupSelectedMonth ? $itemGroupSelectedMonth->sum('add_on_total') - $itemGroupSelectedMonth->sum('add_on_subtotal') : 0;
                $selected_month_add_on_total = $itemGroupSelectedMonth ? $itemGroupSelectedMonth->sum('add_on_total') : 0;

                $january_to_selected_month_quantity = $itemGroupJanuaryToSelectedMonth ? $itemGroupJanuaryToSelectedMonth->sum('quantity') : 0;
                $january_to_selected_month_sub_total = $itemGroupJanuaryToSelectedMonth ? $itemGroupJanuaryToSelectedMonth->sum('sub_total') : 0;
                $january_to_selected_month_total_tax = $itemGroupJanuaryToSelectedMonth ? $itemGroupJanuaryToSelectedMonth->sum('total_tax') : 0;
                $january_to_selected_month_total = $itemGroupJanuaryToSelectedMonth ? $itemGroupJanuaryToSelectedMonth->sum('subtotal_after_tax') : 0;

                $january_to_selected_month_add_on_sub_total = $itemGroupJanuaryToSelectedMonth ? $itemGroupJanuaryToSelectedMonth->sum('add_on_subtotal') : 0;
                $january_to_selected_month_add_on_total_tax = $itemGroupJanuaryToSelectedMonth ? $itemGroupJanuaryToSelectedMonth->sum('add_on_total') - $itemGroupJanuaryToSelectedMonth->sum('add_on_subtotal') : 0;
                $january_to_selected_month_add_on_total = $itemGroupJanuaryToSelectedMonth ? $itemGroupJanuaryToSelectedMonth->sum('add_on_total') : 0;

                $result = new \stdClass();
                $result->customer_code = $item->first()->customer_code;
                $result->customer_name = $item->first()->customer_name;
                $result->item_name = $item->first()->item_name;
                $result->item_code = $item->first()->item_code;
                $result->add_on_item_code = $item->first()->add_on_item_code;
                $result->add_on_item_name = $item->first()->add_on_item_name;
                $result->date = $item->first()->date;
                $result->price = $item->first()->harga;
                $result->previous_year_quantity = $previous_year_quantity;
                $result->previous_year_sub_total = $previous_year_sub_total;
                $result->previous_year_total_tax = $previous_year_total_tax;
                $result->previous_year_total = $previous_year_total;
                $result->previous_year_add_on_sub_total = $previous_year_add_on_sub_total;
                $result->previous_year_add_on_total_tax = $previous_year_add_on_total_tax;
                $result->previous_year_add_on_total = $previous_year_add_on_total;
                $result->selected_month_quantity = $selected_month_quantity;
                $result->selected_month_sub_total = $selected_month_sub_total;
                $result->selected_month_total_tax = $selected_month_total_tax;
                $result->selected_month_total = $selected_month_total;
                $result->selected_month_add_on_sub_total = $selected_month_add_on_sub_total;
                $result->selected_month_add_on_total_tax = $selected_month_add_on_total_tax;
                $result->selected_month_add_on_total = $selected_month_add_on_total;
                $result->january_to_selected_month_quantity = $january_to_selected_month_quantity;
                $result->january_to_selected_month_sub_total = $january_to_selected_month_sub_total;
                $result->january_to_selected_month_total_tax = $january_to_selected_month_total_tax;
                $result->january_to_selected_month_total = $january_to_selected_month_total;
                $result->january_to_selected_month_add_on_sub_total = $january_to_selected_month_add_on_sub_total;
                $result->january_to_selected_month_add_on_total_tax = $january_to_selected_month_add_on_total_tax;
                $result->january_to_selected_month_add_on_total = $january_to_selected_month_add_on_total;

                $total->previous_year_quantity += $previous_year_quantity;
                $total->previous_year_sub_total += $previous_year_sub_total;
                $total->previous_year_total_tax += $previous_year_total_tax;
                $total->previous_year_total += $previous_year_total;
                $total->previous_year_add_on_sub_total += $previous_year_add_on_sub_total;
                $total->previous_year_add_on_total_tax += $previous_year_add_on_total_tax;
                $total->previous_year_add_on_total += $previous_year_add_on_total;
                $total->selected_month_quantity += $selected_month_quantity;
                $total->selected_month_sub_total += $selected_month_sub_total;
                $total->selected_month_total_tax += $selected_month_total_tax;
                $total->selected_month_total += $selected_month_total;
                $total->selected_month_add_on_sub_total += $selected_month_add_on_sub_total;
                $total->selected_month_add_on_total_tax += $selected_month_add_on_total_tax;
                $total->selected_month_add_on_total += $selected_month_add_on_total;
                $total->january_to_selected_month_quantity += $january_to_selected_month_quantity;
                $total->january_to_selected_month_sub_total += $january_to_selected_month_sub_total;
                $total->january_to_selected_month_total_tax += $january_to_selected_month_total_tax;
                $total->january_to_selected_month_total += $january_to_selected_month_total;
                $total->january_to_selected_month_add_on_sub_total += $january_to_selected_month_add_on_sub_total;
                $total->january_to_selected_month_add_on_total_tax += $january_to_selected_month_add_on_total_tax;
                $total->january_to_selected_month_add_on_total += $january_to_selected_month_add_on_total;

                return $result;
            })->flatten(1);

            return $result;
        })->flatten(1);

        return [
            'data' => $result,
            'total' => $total,
        ];
    }

    /**
     * Generate sale order trading report per period
     *
     * @param $request
     * @return array
     */
    private function perPeriodSaleOrderTrading2($request): array
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

        //  * SETTING UP AND GET DATA MONTH, PREVIOUS YEAR, THIS YEAR
        $previous_year = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->subYear()->format('Y');
        $selected_month = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->format('m');
        $selected_year = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->format('Y');

        //  ! GET DATA
        $lastYearData = $this->getPerPeriodSaleOrderTradingDataPreviousYear2($request, $previous_year);
        $selectedMonthData = $this->getPerPeriodSaleOrderTradingDataSelectedMonth2($request, $selected_year, $selected_month);
        $januaryToSelectedMonthData = $this->getPerPeriodSaleOrderTradingDataJanuaryToSelectedMonth2($request, $selected_year, $selected_month);

        // ! COMBINE DATA
        $result = $this->combinePerPeriodSaleOrderTradingData2([
            $lastYearData,
            $selectedMonthData,
            $januaryToSelectedMonthData
        ]);

        return [
            'data' => $result['data'],
            'total' => $result['total'],
            'type' => "per-periode-penjualan-trading",
            "period" => $request->month,
        ];
    }

    /**
     * Get per period sale order trading previous year data
     *
     * @param $request
     * @param $year
     * @return object
     */
    private function getPerPeriodSaleOrderTradingDataPreviousYear($request, $year): object
    {
        $model = DB::table('invoice_tradings')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_tradings.branch_id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_tradings.customer_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_tradings.item_id')
            // ->leftJoin('ware_houses', 'ware_houses.id', '=', 'delivery_orders.ware_house_id')
            ->whereNull('invoice_tradings.deleted_at')
            ->whereYear('invoice_tradings.date', $year)
            ->whereNotIn('invoice_tradings.status', ['revert', 'pending', 'reject', 'void'])
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.customer_id', $request->customer_id);
            })
            // ->when($request->warehouse_id, function ($query) use ($request) {
            //     return $query
            //         ->where('delivery_order_tradings.ware_house_id', $request->warehouse_id);
            // })
            ->when($request->item_id, function ($query) use ($request) {
                return $query
                    ->where('invoice_tradings.item_id', $request->item_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_tradings.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_tradings.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_tradings.id')
            ->selectRaw('
                invoice_tradings.id,

                invoice_tradings.jumlah as quantity,
                invoice_tradings.date,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.harga
                    else invoice_tradings.harga * invoice_tradings.exchange_rate
                end as harga,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.subtotal
                    else invoice_tradings.subtotal * invoice_tradings.exchange_rate
                end as value,

                customers.id as customer_id,
                customers.nama as customer_name,
                customers.code as customer_code,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code
            ')
            ->get();

        $data_details = DB::table('invoice_trading_details')
            ->whereIn('invoice_trading_details.invoice_trading_id', $model->pluck('id')->toArray())
            ->selectRaw('
                invoice_trading_details.id,
                invoice_trading_details.invoice_trading_id,
                invoice_trading_details.jumlah_dikirim,
                invoice_trading_details.jumlah_diterima
            ')
            ->get();

        $results = $model->map(function ($item) use ($data_details) {
            $item->quantity_sended = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_dikirim');
            $item->quantity_received = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_diterima');

            return $item;
        });

        return $results;
    }

    /**
     * Get per period sale order trading selected month
     *
     * @param $request
     * @param $year
     * @param $Month
     * @return object
     */
    private function getPerPeriodSaleOrderTradingDataSelectedMonth($request, $year, $Month): object
    {
        $model = DB::table('invoice_tradings')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_tradings.branch_id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_tradings.customer_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_tradings.item_id')
            // ->leftJoin('ware_houses', 'ware_houses.id', '=', 'invoice_tradings.ware_house_id')
            ->whereNull('invoice_tradings.deleted_at')
            ->whereMonth('invoice_tradings.date', $Month)
            ->whereYear('invoice_tradings.date', $year)
            ->whereNotIn('invoice_tradings.status', ['revert', 'pending', 'reject', 'void'])
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.customer_id', $request->customer_id);
            })
            // ->when($request->warehouse_id, function ($query) use ($request) {
            //     return $query
            //         ->where('delivery_order_tradings.ware_house_id', $request->warehouse_id);
            // })
            ->when($request->item_id, function ($query) use ($request) {
                return $query
                    ->where('invoice_tradings.item_id', $request->item_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_tradings.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_tradings.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_tradings.id')
            ->selectRaw('
                invoice_tradings.id,

                invoice_tradings.jumlah as quantity,
                invoice_tradings.date,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.harga
                    else invoice_tradings.harga * invoice_tradings.exchange_rate
                end as harga,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.subtotal
                    else invoice_tradings.subtotal * invoice_tradings.exchange_rate
                end as value,

                customers.id as customer_id,
                customers.nama as customer_name,
                customers.code as customer_code,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code
            ')
            ->get();

        $data_details = DB::table('invoice_trading_details')
            ->whereIn('invoice_trading_details.invoice_trading_id', $model->pluck('id')->toArray())
            ->selectRaw('
                invoice_trading_details.id,
                invoice_trading_details.invoice_trading_id,
                invoice_trading_details.jumlah_dikirim,
                invoice_trading_details.jumlah_diterima
            ')
            ->get();

        $results = $model->map(function ($item) use ($data_details) {
            $item->quantity_sended = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_dikirim');
            $item->quantity_received = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_diterima');

            return $item;
        });

        return $results;
    }

    /**
     * Get per period sale order trading January to selected month
     *
     * @param $request
     * @param $year
     * @param $Month
     * @return object
     */
    private function getPerPeriodSaleOrderTradingDataJanuaryToSelectedMonth($request, $year, $Month): object
    {
        $model = DB::table('invoice_tradings')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_tradings.branch_id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_tradings.customer_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_tradings.item_id')
            // ->leftJoin('ware_houses', 'ware_houses.id', '=', 'invoice_tradings.ware_house_id')
            ->whereNull('invoice_tradings.deleted_at')
            ->whereMonth('invoice_tradings.date', '<=', $Month)
            ->whereYear('invoice_tradings.date', $year)
            ->whereNotIn('invoice_tradings.status', ['revert', 'pending', 'reject', 'void'])
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.customer_id', $request->customer_id);
            })
            // ->when($request->warehouse_id, function ($query) use ($request) {
            //     return $query
            //         ->where('delivery_order_tradings.ware_house_id', $request->warehouse_id);
            // })
            ->when($request->item_id, function ($query) use ($request) {
                return $query
                    ->where('invoice_tradings.item_id', $request->item_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_tradings.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_tradings.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_tradings.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_tradings.id')
            ->selectRaw('
                invoice_tradings.id,

                invoice_tradings.jumlah as quantity,
                invoice_tradings.date,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.harga
                    else invoice_tradings.harga * invoice_tradings.exchange_rate
                end as harga,

                case when invoice_tradings.exchange_rate = 1
                    then invoice_tradings.subtotal
                    else invoice_tradings.subtotal * invoice_tradings.exchange_rate
                end as value,

                customers.id as customer_id,
                customers.nama as customer_name,
                customers.code as customer_code,

                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code
            ')
            ->get();

        $data_details = DB::table('invoice_trading_details')
            ->whereIn('invoice_trading_details.invoice_trading_id', $model->pluck('id')->toArray())
            ->selectRaw('
                invoice_trading_details.id,
                invoice_trading_details.invoice_trading_id,
                invoice_trading_details.jumlah_dikirim,
                invoice_trading_details.jumlah_diterima
            ')
            ->get();

        $results = $model->map(function ($item) use ($data_details) {
            $item->quantity_sended = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_dikirim');
            $item->quantity_received = $data_details->where('invoice_trading_id', $item->id)->sum('jumlah_diterima');

            return $item;
        });

        return $results;
    }

    /**
     * Combine sale order trading per period
     *
     * @param array $data
     */
    private function combinePerPeriodSaleOrderTradingData(array $data): object
    {
        list(
            $lastYearData,
            $selectedMonthData,
            $januaryToSelectedMonthData
        ) = $data;

        $grouped = $selectedMonthData
            ->unique('customer_id');

        $result = $grouped->map(function ($item) use (
            $lastYearData,
            $selectedMonthData,
            $januaryToSelectedMonthData
        ) {
            $itemGroupLastYear = $lastYearData->where('customer_id', $item->customer_id)->groupBy('item_id');
            $itemGroupSelectedMonth = $selectedMonthData->where('customer_id', $item->customer_id)->groupBy('item_id');
            $itemGroupJanuaryToSelectedMonth = $januaryToSelectedMonthData->where('customer_id', $item->customer_id)->groupBy('item_id');

            $result = $itemGroupSelectedMonth->map(function ($item) use (
                $itemGroupLastYear,
                $itemGroupSelectedMonth,
                $itemGroupJanuaryToSelectedMonth
            ) {
                $itemGroupLastYear = $itemGroupLastYear->get($item->first()->item_id);
                $itemGroupSelectedMonth = $itemGroupSelectedMonth->get($item->first()->item_id);
                $itemGroupJanuaryToSelectedMonth = $itemGroupJanuaryToSelectedMonth->get($item->first()->item_id);

                $result = new \stdClass();
                $result->customer_code = $item->first()->customer_code;
                $result->customer_name = $item->first()->customer_name;
                $result->item_name = $item->first()->item_name;
                $result->item_code = $item->first()->item_code;
                $result->date = $item->first()->date;
                $result->price = $item->first()->harga;
                $result->previous_year_quantity = $itemGroupLastYear ? $itemGroupLastYear->sum('quantity') : 0;
                $result->previous_year_quantity_sended = $itemGroupLastYear ? $itemGroupLastYear->sum('quantity_sended') : 0;
                $result->previous_year_quantity_received = $itemGroupLastYear ? $itemGroupLastYear->sum('quantity_received') : 0;
                $result->previous_year_value = $itemGroupLastYear ? $itemGroupLastYear->sum('value') : 0;
                $result->selected_month_quantity = $itemGroupSelectedMonth ? $itemGroupSelectedMonth->sum('quantity') : 0;
                $result->selected_month_quantity_sended = $itemGroupSelectedMonth ? $itemGroupSelectedMonth->sum('quantity_sended') : 0;
                $result->selected_month_quantity_received = $itemGroupSelectedMonth ? $itemGroupSelectedMonth->sum('quantity_received') : 0;
                $result->selected_month_value = $itemGroupSelectedMonth ? $itemGroupSelectedMonth->sum('value') : 0;
                $result->january_to_selected_month_quantity = $itemGroupJanuaryToSelectedMonth ? $itemGroupJanuaryToSelectedMonth->sum('quantity') : 0;
                $result->january_to_selected_month_quantity_sended = $itemGroupJanuaryToSelectedMonth ? $itemGroupJanuaryToSelectedMonth->sum('quantity_sended') : 0;
                $result->january_to_selected_month_quantity_received = $itemGroupJanuaryToSelectedMonth ? $itemGroupJanuaryToSelectedMonth->sum('quantity_received') : 0;
                $result->january_to_selected_month_value = $itemGroupJanuaryToSelectedMonth ? $itemGroupJanuaryToSelectedMonth->sum('value') : 0;

                return $result;
            })->flatten(1);

            return $result;
        })->flatten(1);

        return $result;
    }

    /**
     * Generate sale order trading report per period
     *
     * @param $request
     * @return array
     */
    private function perPeriodSaleOrderTrading($request): array
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

        //  * SETTING UP AND GET DATA MONTH, PREVIOUS YEAR, THIS YEAR
        $previous_year = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->subYear()->format('Y');
        $selected_month = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->format('m');
        $selected_year = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->format('Y');

        //  ! GET DATA
        $lastYearData = $this->getPerPeriodSaleOrderTradingDataPreviousYear($request, $previous_year);
        $selectedMonthData = $this->getPerPeriodSaleOrderTradingDataSelectedMonth($request, $selected_year, $selected_month);
        $januaryToSelectedMonthData = $this->getPerPeriodSaleOrderTradingDataJanuaryToSelectedMonth($request, $selected_year, $selected_month);

        // ! COMBINE DATA
        $result = $this->combinePerPeriodSaleOrderTradingData([
            $lastYearData,
            $selectedMonthData,
            $januaryToSelectedMonthData
        ]);

        return [
            'data' => $result,
            'type' => "per-periode-penjualan-trading",
            "period" => $request->month,
        ];
    }

    /**
     *
     */
    private function dialySaleOrderTradingItemDetailCustomer($request)
    {
        $models = DB::table('delivery_orders')
            ->leftJoin('sale_orders', 'sale_orders.id', 'delivery_orders.so_trading_id')
            ->leftJoin('invoice_tradings as it', 'it.so_trading_id', 'sale_orders.id')
            ->leftJoin('sale_order_details', 'sale_orders.id', 'sale_order_details.so_trading_id')
            ->leftJoin('items', 'items.id', 'sale_order_details.item_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->leftJoin('branches', 'branches.id', 'sale_orders.branch_id')
            ->when($request->ware_house_id, fn($q) => $q->where('delivery_orders.ware_house_id', $request->ware_house_id))
            ->when($request->item_id, fn($q) => $q->where('sale_order_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->when($request->from_date, fn($q) => $q->whereDate('delivery_orders.target_delivery', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('delivery_orders.target_delivery', '<=', Carbon::parse($request->to_date)))
            ->when($request->status, fn($q) => $q->where('delivery_orders.status', $request->status))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_orders.branch_id', get_current_branch()->id);
            })
            ->whereNull('sale_orders.deleted_at')
            ->whereNull('delivery_orders.deleted_at')
            ->distinct('delivery_orders.id')
            ->selectRaw('
                customers.id as customer_id,
                customers.nama as customer_name,
                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,
                it.kode as it_code,
                delivery_orders.target_delivery,
                delivery_orders.code,
                delivery_orders.load_quantity_realization load_quantity_realization,
                sale_order_details.harga as price,
                sale_orders.exchange_rate as exchange_rate
            ')
            ->get();

        $customerUniqueIds = $models->pluck('customer_id')->unique();

        $results = $customerUniqueIds->map(function ($customer_id) use ($models) {
            $modelFiltered = $models->where('customer_id', $customer_id);
            $resultItemIds = $modelFiltered->pluck('item_id')->unique();
            return $resultItemIds->map(function ($item_id) use ($modelFiltered) {
                $modelFiltered2 = $modelFiltered->where('item_id', $item_id);

                // $price;
                $quantity = 0;
                $sub_total = 0;
                $sub_total_idr = 0;

                $customer_name = $modelFiltered2->first()?->customer_name;
                $item_name = $modelFiltered2->first()?->item_name;
                $item_code = $modelFiltered2->first()?->item_code;
                $tanggal = $modelFiltered2->first()?->target_delivery;
                $do_code = $modelFiltered2->first()?->code;
                $it_code = $modelFiltered2->first()?->it_code;

                $modelFiltered2->map(function ($data) use (&$quantity, &$sub_total, &$sub_total_idr) {
                    $price = $data->price;
                    $single_quantity = $data->load_quantity_realization;

                    $quantity += $single_quantity;
                    $sub_total += $price * $single_quantity;
                    $sub_total_idr += $price * $single_quantity * $data->exchange_rate;
                });

                return [
                    'customer_name' => $customer_name,
                    'item_name' => $item_name,
                    'item_code' => $item_code,
                    'sub_total' => $sub_total,
                    'quantity' => $quantity,
                    'sub_total_idr' => $sub_total_idr,
                    'it_code' => $it_code,
                    'code' => $do_code,
                    'tanggal' => $tanggal,
                ];
            });
        });

        $final_results = [];
        $results->map(function ($result) use (&$final_results) {
            $result->map(function ($item) use (&$final_results) {
                $final_results[] = $item;
            });
        });

        return [
            'data' => collect($final_results),
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => 'daily-sale-order-trading-item-detail-customer',
            'title' => 'Laporan Harian Rincian Item Penjualan Trading Per Customer',
        ];
    }

    /**
     */
    private function monthlySaleOrderTradingItemDetailCustomerLastYear($request, $year)
    {
        return DB::table('delivery_orders')
            ->leftJoin('sale_orders', 'sale_orders.id', 'delivery_orders.so_trading_id')
            ->leftJoin('sale_order_details', 'sale_orders.id', 'sale_order_details.so_trading_id')
            ->leftJoin('items', 'items.id', 'sale_order_details.item_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->leftJoin('branches', 'branches.id', 'sale_orders.branch_id')
            ->when($request->ware_house_id, fn($q) => $q->where('delivery_orders.ware_house_id', $request->ware_house_id))
            ->when($request->item_id, fn($q) => $q->where('sale_order_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->whereIn('delivery_orders.status', ['approve', 'done'])
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_orders.branch_id', get_current_branch()->id);
            })
            ->whereNull('sale_orders.deleted_at')
            ->whereNull('delivery_orders.deleted_at')
            ->whereYear('delivery_orders.target_delivery', $year)
            ->distinct('delivery_orders.id')
            ->selectRaw('
                delivery_orders.id,
                customers.id as customer_id,
                customers.nama as customer_name,
                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,
                delivery_orders.load_quantity_realization load_quantity_realization,
                sale_order_details.harga as price,
                sale_orders.exchange_rate as exchange_rate
            ')
            ->get();
    }

    /**
     */
    private function monthlySaleOrderTradingItemDetailCustomerSelectedMonth($request, $year, $month)
    {
        return DB::table('delivery_orders')
            ->leftJoin('sale_orders', 'sale_orders.id', 'delivery_orders.so_trading_id')
            ->leftJoin('sale_order_details', 'sale_orders.id', 'sale_order_details.so_trading_id')
            ->leftJoin('items', 'items.id', 'sale_order_details.item_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->leftJoin('branches', 'branches.id', 'sale_orders.branch_id')
            ->when($request->ware_house_id, fn($q) => $q->where('delivery_orders.ware_house_id', $request->ware_house_id))
            ->when($request->item_id, fn($q) => $q->where('sale_order_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->whereIn('delivery_orders.status', ['approve', 'done'])
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_orders.branch_id', get_current_branch()->id);
            })
            ->whereNull('sale_orders.deleted_at')
            ->whereNull('delivery_orders.deleted_at')
            ->whereYear('delivery_orders.target_delivery', $year)
            ->whereMonth('delivery_orders.target_delivery', $month)
            ->distinct('delivery_orders.id')
            ->selectRaw('
                delivery_orders.id,
                customers.id as customer_id,
                customers.nama as customer_name,
                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,
                delivery_orders.load_quantity_realization load_quantity_realization,
                sale_order_details.harga as price,
                sale_orders.exchange_rate as exchange_rate
            ')
            ->get();
    }

    /**
     */
    private function monthlySaleOrderTradingItemDetailCustomerThisYear($request, $year, $month)
    {
        return DB::table('delivery_orders')
            ->leftJoin('sale_orders', 'sale_orders.id', 'delivery_orders.so_trading_id')
            ->leftJoin('sale_order_details', 'sale_orders.id', 'sale_order_details.so_trading_id')
            ->leftJoin('items', 'items.id', 'sale_order_details.item_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->leftJoin('branches', 'branches.id', 'sale_orders.branch_id')
            ->when($request->ware_house_id, fn($q) => $q->where('delivery_orders.ware_house_id', $request->ware_house_id))
            ->when($request->item_id, fn($q) => $q->where('sale_order_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->whereIn('delivery_orders.status', ['approve', 'done'])
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_orders.branch_id', get_current_branch()->id);
            })
            ->whereNull('sale_orders.deleted_at')
            ->whereNull('delivery_orders.deleted_at')
            ->whereYear('delivery_orders.target_delivery', $year)
            ->whereMonth('delivery_orders.target_delivery', '<=', $month)
            ->distinct('delivery_orders.id')
            ->selectRaw('
                delivery_orders.id,
                customers.id as customer_id,
                customers.nama as customer_name,
                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,
                delivery_orders.load_quantity_realization load_quantity_realization,
                sale_order_details.harga as price,
                sale_orders.exchange_rate as exchange_rate
            ')
            ->get();
    }

    /**
     */
    private function processResulMmonthlySaleOrderTradingItemDetailCustomer(array $data)
    {
        list(
            $previous_year_data,
            $selected_month_data,
            $selected_year_data
        ) = $data;

        $selectedMonthCustomerIds = $selected_month_data->pluck('customer_id')->unique();

        $results = $selectedMonthCustomerIds->map(function ($customer_id) use ($previous_year_data, $selected_month_data, $selected_year_data) {
            $itemIds = $selected_month_data->where('customer_id', $customer_id)->pluck('item_id')->unique();

            return $itemIds->map(function ($item_id) use ($previous_year_data, $selected_month_data, $selected_year_data, $customer_id) {
                $dataLastYear = $previous_year_data->where('customer_id', $customer_id)->where('item_id', $item_id);
                $dataSelectedMonth = $selected_month_data->where('customer_id', $customer_id)->where('item_id', $item_id);
                $dataThisYear = $selected_year_data->where('customer_id', $customer_id)->where('item_id', $item_id);

                $customer_name = $dataSelectedMonth->first()->customer_name;
                $item_name = $dataSelectedMonth->first()->item_name;
                $item_code = $dataSelectedMonth->first()->item_code;

                $quantity_last_year = $dataLastYear->sum('load_quantity_realization');
                $sub_total_last_year = $dataLastYear->map(function ($last_year) {
                    return $last_year->price * $last_year->load_quantity_realization;
                })->sum();

                $quantity_selected_month = $dataSelectedMonth->sum('load_quantity_realization');
                $sub_total_selected_month = $dataSelectedMonth->map(function ($last_year) {
                    return $last_year->price * $last_year->load_quantity_realization;
                })->sum();

                $quantity_this_year = $dataThisYear->sum('load_quantity_realization');
                $sub_total_this_year = $dataThisYear->map(function ($last_year) {
                    return $last_year->price * $last_year->load_quantity_realization;
                })->sum();

                return [
                    'customer_name' => $customer_name,
                    'item_name' => $item_name,
                    'item_code' => $item_code,
                    'quantity_last_year' => $quantity_last_year,
                    'sub_total_last_year' => $sub_total_last_year,
                    'quantity_selected_month' => $quantity_selected_month,
                    'sub_total_selected_month' => $sub_total_selected_month,
                    'quantity_this_year' => $quantity_this_year,
                    'sub_total_this_year' => $sub_total_this_year,
                ];
            });
        });

        $final_results = [];

        $results->map(function ($result) use (&$final_results) {
            $result->map(function ($item) use (&$final_results) {
                $final_results[] = $item;
            });
        });

        return collect($final_results)
            ->sortBy('customer_name');
    }

    /**
     */
    private function monthlySaleOrderTradingItemDetailCustomer($request)
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
        $previous_year_data = $this->monthlySaleOrderTradingItemDetailCustomerLastYear($request, $previous_year);
        $selected_month_data = $this->monthlySaleOrderTradingItemDetailCustomerSelectedMonth($request, $selected_year, $selected_month);

        $selected_year_data = $this->monthlySaleOrderTradingItemDetailCustomerThisYear($request, $selected_year, $selected_month);

        // ! PROCESS RESULTS
        $results = $this->processResulMmonthlySaleOrderTradingItemDetailCustomer([
            $previous_year_data,
            $selected_month_data,
            $selected_year_data
        ]);

        return [
            'data' => $results,
            'title' => 'LAPORAN BULANAN RINCIAN ITEM PENJUALAN TRADING PER CUSTOMER',
            "periode" => $request->month,
            "type" => "daily-sale-order-trading-item-detail-customer",
        ];
    }

    /**
     *
     */
    private function saleOrderTradingOutstandingReport($request)
    {
        $models = DB::table('sale_order_details')
            ->leftJoin('sale_orders', 'sale_orders.id', 'sale_order_details.so_trading_id')
            ->leftJoin('items', 'items.id', 'sale_order_details.item_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->when($request->item_id, fn($q) => $q->where('sale_order_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->when($request->status, fn($q) => $q->where('sale_orders.status', $request->status))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_orders.branch_id', get_current_branch()->id);
            })
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('sale_orders.tanggal', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('sale_orders.tanggal', '<=', Carbon::parse($request->to_date));
            })
            ->orderBy('sale_orders.tanggal', 'asc')
            ->whereNull('sale_orders.deleted_at')
            ->where('sale_order_details.jumlah', '>=', DB::raw('sale_order_details.sudah_dikirim'))
            ->distinct('sale_order_details.id')
            ->selectRaw('
                sale_orders.nomor_so as kode,
                sale_orders.tanggal,
                customers.nama as customer_name,
                items.kode as item_code,
                items.nama as item_name,
                sale_order_details.jumlah,
                sale_order_details.sudah_dikirim,

                (sale_order_details.jumlah - sale_order_details.sudah_dikirim) as outstanding
            ')
            ->get();

        return [
            'data' => $models,
            'type' => 'sale-order-trading-outstanding-report',
            'title' => 'Laporan Outstanding Penjualan Trading',
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
        ];
    }

    /**
     *
     */
    private function stockComparisonWithSaleOrderTrading($request)
    {
        $models = DB::table('sale_order_details')
            ->leftJoin('sale_orders', 'sale_orders.id', 'sale_order_details.so_trading_id')
            ->leftJoin('items', 'items.id', 'sale_order_details.item_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->when($request->item_id, fn($q) => $q->where('sale_order_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->when($request->status, fn($q) => $q->where('sale_orders.status', $request->status))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_orders.branch_id', get_current_branch()->id);
            })
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('sale_orders.tanggal', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('sale_orders.tanggal', '<=', Carbon::parse($request->to_date));
            })
            ->whereNull('sale_orders.deleted_at')
            ->where('sale_order_details.jumlah', '>=', DB::raw('sale_order_details.sudah_dikirim'))
            ->distinct('sale_order_details.id')
            ->selectRaw('
                sale_orders.nomor_so as kode,
                sale_orders.tanggal,
                customers.nama as customer_name,
                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,
                sale_order_details.jumlah,
                sale_order_details.sudah_dikirim,

                (sale_order_details.jumlah - sale_order_details.sudah_dikirim) as outstanding
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
            'type' => 'stock-comparison-with-sale-order-trading',
            'title' => 'Laporan Perbandingan Stok dengan Penjualan Trading',
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
        ];
    }

    /**
     *
     */
    private function debtDueSaleOrderTrading($request)
    {
        $invoices = DB::table('invoice_tradings')
            ->leftJoin('sale_orders', 'sale_orders.id', 'invoice_tradings.so_trading_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->leftJoin('branches', 'branches.id', 'sale_orders.branch_id')
            ->join('invoice_parents', function ($q) {
                $q->on('invoice_tradings.id', 'invoice_parents.reference_id')
                    ->where('invoice_parents.model_reference', \App\Models\InvoiceTrading::class);
            })
            ->when($request->active, fn($q) => $q->where('invoice_parents.lock_status', 0))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_orders.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_orders.branch_id', get_current_branch()->id);
            })
            ->whereNull('invoice_tradings.deleted_at')
            ->whereNotIn('invoice_tradings.status', ['reject', 'void'])
            // ->whereIn('invoice_tradings.payment_status', ['unpaid', 'partial-paid'])
            ->whereDate('invoice_tradings.due_date', '<=', now()->format('Y-m-d'))
            ->selectRaw('
                invoice_tradings.id as id,
                invoice_tradings.kode as code,

                customers.nama as customer_name,
                branches.name as branch_name,

                invoice_tradings.date,
                invoice_tradings.due_date,
                invoice_tradings.exchange_rate,
                invoice_tradings.payment_status,

                invoice_tradings.total,

                case
                    when invoice_tradings.exchange_rate = 1
                        then invoice_tradings.total
                    else
                        invoice_tradings.total * invoice_tradings.exchange_rate
                end as total_local
            ')
            ->get();

        $invoicePayments = DB::table('invoice_payments')
            ->where('invoice_payments.invoice_model', \App\Models\InvoiceTrading::class)
            ->whereIn('invoice_payments.reference_id', $invoices->pluck('id')->toArray())
            ->whereNull('invoice_payments.deleted_at')
            ->SelectRaw('
                invoice_payments.invoice_id,
                invoice_payments.exchange_rate,
                invoice_payments.amount_to_receive,
                invoice_payments.receive_amount,

                case
                    when invoice_payments.exchange_rate = 1
                        then invoice_payments.amount_to_receive
                    else
                        invoice_payments.amount_to_receive * invoice_payments.exchange_rate
                end as amount_to_receive_local,

                case
                    when invoice_payments.exchange_rate = 1
                        then invoice_payments.receive_amount
                    else
                        invoice_payments.receive_amount * invoice_payments.exchange_rate
                end as receive_amount_local
            ')
            ->get();

        $results = $invoices->map(function ($invoice) use ($invoicePayments) {
            $payments = $invoicePayments->where('invoice_id', $invoice->id);

            $invoice->overdue = Carbon::parse($invoice->due_date)->isPast() && $invoice->payment_status !== 'paid' ? Carbon::parse($invoice->due_date)->diffInDays(now()) : '';
            $invoice->paid = $payments->sum('receive_amount');
            $invoice->paid_local = $payments->sum('receive_amount_local');

            $invoice->outstanding = $invoice->total - $invoice->paid;
            $invoice->outstanding_local = $invoice->total_local - $invoice->paid_local;

            return $invoice;
        });

        return [
            'data' => $results,
            'title' => 'Laporan Piutang Jatuh Tempo Penjualan Trading',
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    public function compareSOPO($request)
    {
        try {
            $sale_orders = DB::table('sale_orders')
                ->join('sale_order_details', 'sale_order_details.so_trading_id', 'sale_orders.id')
                ->join('items', 'items.id', 'sale_order_details.item_id')
                ->join('customers', 'customers.id', 'sale_orders.customer_id')
                ->select(
                    'sale_orders.id',
                    'sale_orders.nomor_so',
                    'sale_orders.tanggal',
                    'customers.nama as customer_name',
                    'items.kode as item_code',
                    'items.nama as item_name'
                )
                ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
                ->when($request->from_date, fn($q) => $q->whereDate('sale_orders.tanggal', '>=', Carbon::parse(Carbon::parse($request->from_date))))
                ->when($request->to_date, fn($q) => $q->whereDate('sale_orders.tanggal', '<=', Carbon::parse($request->to_date)))
                ->when($request->branch_id, fn($q) => $q->where('sale_orders.branch_id', $request->branch_id))
                ->whereNull('sale_orders.deleted_at')
                ->get();

            $pairing_so_to_pos = DB::table('pairing_so_to_pos')
                ->join('sale_order_details', 'sale_order_details.id', 'pairing_so_to_pos.so_trading_detail_id')
                ->join('sale_orders', 'sale_orders.id', 'sale_order_details.so_trading_id')
                ->join('items', 'items.id', 'sale_order_details.item_id')
                ->join('customers', 'customers.id', 'sale_orders.customer_id')
                ->join('purchase_order_details', 'purchase_order_details.id', 'pairing_so_to_pos.po_trading_detail_id')
                ->join('purchase_orders', 'purchase_orders.id', 'purchase_order_details.po_trading_id')
                ->join('vendors', 'vendors.id', 'purchase_orders.vendor_id')
                ->whereIn('sale_orders.id', $sale_orders->pluck('id')->toArray())
                ->select(
                    'sale_orders.id as so_trading_id',
                    'sale_orders.nomor_so',
                    'sale_orders.tanggal',
                    'customers.nama as customer_name',
                    'items.kode as item_code',
                    'items.nama as item_name',
                    'sale_order_details.jumlah as so_qty',
                    'sale_order_details.harga as so_price',
                    'sale_orders.exchange_rate as so_exchange_rate',
                    'purchase_orders.id as po_trading_id',
                    'purchase_orders.nomor_po',
                    'purchase_orders.tanggal as po_date',
                    'vendors.nama as vendor_name',
                    'pairing_so_to_pos.alokasi as po_qty',
                    'purchase_order_details.harga as po_price',
                    'purchase_order_details.discount_per_liter',
                    'purchase_orders.exchange_rate as po_exchange_rate',
                )
                ->orderBy('sale_orders.tanggal', 'asc')
                ->orderBy('sale_orders.nomor_so', 'asc')
                ->get();

            $sale_order_taxes = DB::table('sale_order_taxes')
                ->whereIn('sale_order_taxes.so_trading_id', $pairing_so_to_pos->pluck('so_trading_id')->toArray())
                ->join('taxes', 'taxes.id', 'sale_order_taxes.tax_id')
                ->select(
                    'sale_order_taxes.so_trading_id',
                    'sale_order_taxes.total',
                )
                ->get();

            $purchase_order_taxes = DB::table('purchase_order_taxes')
                ->whereIn('purchase_order_taxes.po_trading_id', $pairing_so_to_pos->pluck('po_trading_id')->toArray())
                ->select(
                    'purchase_order_taxes.po_trading_id',
                    'purchase_order_taxes.value',
                )
                ->get();

            $pairing_so_to_pos = $pairing_so_to_pos->map(function ($pairing) use ($sale_order_taxes, $purchase_order_taxes) {
                $sale_order_tax = $sale_order_taxes->where('so_trading_id', $pairing->so_trading_id)->first();
                $pairing->so_subtotal = $pairing->so_qty * $pairing->so_price * $pairing->so_exchange_rate;
                $pairing->so_tax = $sale_order_tax ? $sale_order_tax->total * $pairing->so_exchange_rate : 0;
                $pairing->so_total = $pairing->so_subtotal + $pairing->so_tax;
                $pairing->po_price = ($pairing->po_price - $pairing->discount_per_liter) * $pairing->po_exchange_rate;
                $po_tax = 0;
                $po_subtotal = $pairing->po_qty * $pairing->po_price;
                $pairing->po_subtotal = $po_subtotal;
                $purchase_order_taxes->where('po_trading_id', $pairing->po_trading_id)->map(function ($item) use (&$po_tax, $pairing) {
                    $po_tax += $pairing->po_subtotal * $item->value;
                });
                $pairing->po_tax = $po_tax;
                $pairing->po_total = $pairing->po_subtotal + $pairing->po_tax;

                return $pairing;
            });

            $sale_orders->each(function ($sale_order) use ($pairing_so_to_pos) {
                $pairing_data_all = $pairing_so_to_pos->where('so_trading_id', $sale_order->id)->values()->all();
                $pairing_data = $pairing_so_to_pos->where('so_trading_id', $sale_order->id);
                $sale_order->pairings = $pairing_data_all;
                $sale_order->total_so_qty = $pairing_data->first()->so_qty ?? 0;
                $sale_order->total_so_subtotal = $pairing_data->first()->so_subtotal ?? 0;
                $sale_order->total_so_tax = $pairing_data->first()->so_tax ?? 0;
                $sale_order->total_so_total = $pairing_data->first()->so_total ?? 0;
                $sale_order->total_po_qty = $pairing_data->sum('po_qty');
                $sale_order->total_po_subtotal = $pairing_data->sum('po_subtotal');
                $sale_order->total_po_tax = $pairing_data->sum('po_tax');
                $sale_order->total_po_total = $pairing_data->sum('po_total');
            });

            return [
                'data' => $sale_orders,
                'type' => 'Laporan Perbandingan Penjualan Trading dengan Pembelian Trading',
                "from_date" => $request->from_date,
                "to_date" =>  $request->to_date,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Losses sales order report
     */
    private function lossesSalesOrderReport($request)
    {
        $models = DB::table('sale_orders')
            ->leftJoin('sale_order_details', 'sale_order_details.so_trading_id', 'sale_orders.id')
            ->leftJoin('items', 'items.id', 'sale_order_details.item_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->leftJoin('branches', 'branches.id', 'sale_orders.branch_id')
            // ->leftJoin('delivery_orders', 'delivery_orders.so_trading_id', 'sale_orders.id')
            ->when($request->item_id, fn($q) => $q->where('sale_order_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->when($request->status, fn($q) => $q->where('sale_orders.status', $request->status))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_orders.branch_id', $request->branch_id);
            })
            ->when($request->sh_number_id, fn($q) => $q->where('sales_orders.sh_number_id', $request->sh_number_id))
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_orders.branch_id', get_current_branch()->id);
            })
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('sale_orders.tanggal', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('sale_orders.tanggal', '<=', Carbon::parse($request->to_date));
            })
            ->whereNull('sale_orders.deleted_at')
            ->distinct('sale_orders.id')
            ->selectRaw('
                sale_orders.id as id,
                sale_orders.nomor_so as code,
                sale_orders.tanggal as date,
                sale_orders.status as status,

                branches.name as branch_name,

                customers.nama as customer_name,

                items.kode as item_code,
                items.nama as item_name,

                sale_order_details.harga * sale_orders.exchange_rate as price,
                sale_order_details.jumlah as quantity
            ')
            ->get();

        $deliveryOrders = DB::table('delivery_orders')
            ->whereIn('delivery_orders.so_trading_id', $models->pluck('id')->toArray())
            ->whereNull('delivery_orders.deleted_at')
            ->where('delivery_orders.status', 'done')
            ->where('delivery_orders.type', 'delivery-order')
            ->selectRaw('
                delivery_orders.so_trading_id,
                delivery_orders.load_quantity_realization as load_quantity_realization,
                delivery_orders.unload_quantity_realization as unload_quantity_realization,
                delivery_orders.load_quantity_realization - delivery_orders.unload_quantity_realization as losses_quantity
            ')
            ->get();

        $results = $models->map(function ($model) use ($deliveryOrders) {
            $deliveryOrder = $deliveryOrders->where('so_trading_id', $model->id);

            $model->load_quantity = $deliveryOrder->sum('load_quantity_realization');
            $model->unload_quantity = $deliveryOrder->sum('unload_quantity_realization');
            $model->unload_quantity_realization = $deliveryOrder->sum('unload_quantity_realization');
            $model->losses_quantity = $deliveryOrder->sum('losses_quantity');
            $model->losses_value = $model->losses_quantity * $model->price;

            return $model;
        });

        return [
            'data' => $results,
            'type' => 'losses-sales-order',
            'title' => 'Laporan Kerugian Penjualan Trading',
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
        ];
    }

    /**
     * Title: Penjualan Trading Detail & Additional
     * 
     */
    public function tradingSalesDetailAdditional($request)
    {
        $formated_from_date = Carbon::parse($request->from_date)->format('Y-m-d');
        $formated_to_date = Carbon::parse($request->to_date)->format('Y-m-d');

        $invoices = DB::table('invoice_tradings')
            ->join('sale_orders', 'sale_orders.id', 'invoice_tradings.so_trading_id')
            ->join('customers', 'customers.id', 'sale_orders.customer_id')
            ->leftJoin('items', 'items.id', 'invoice_tradings.item_id')
            ->leftJoin('branches', 'branches.id', 'invoice_tradings.branch_id')
            ->when($request->branch_id ?? false, function ($query, $branch) {
                return $query->where('invoice_tradings.branch_id', $branch);
            })
            ->where(function ($query) use ($formated_from_date, $formated_to_date) {
                $query->whereBetween('invoice_tradings.date', [$formated_from_date, $formated_to_date]);
            })
            ->when($request->customer_id ?? false, function ($query, $customer) {
                return $query->where('invoice_tradings.customer_id', $customer);
            })
            ->when($request->item_id ?? false, function ($query, $item) {
                return $query->where('invoice_tradings.item_id', $item);
            })
            ->when($request->status ?? false, function ($query, $status) {
                return $query->where('invoice_tradings.payment_status', $status);
            })
            ->whereIn('invoice_tradings.status', ['approve', 'done'])
            ->whereNull('invoice_tradings.deleted_at')
            ->selectRaw('
                invoice_tradings.id as id,
                invoice_tradings.kode as invoice_code,
                invoice_tradings.date as invoice_date,
                sale_orders.nomor_so as so_code,
                invoice_tradings.nomor_po_external as no_po_external,
                invoice_tradings.reference as tax_invoice,
                customers.nama as customer_name,
                branches.name as branch_name,
                invoice_tradings.due_date as due_date,
                items.kode as item_code,
                items.nama as item_name,
                invoice_tradings.exchange_rate as exchange_rate,
                invoice_tradings.subtotal as subtotal,
                invoice_tradings.subtotal_after_tax as total_foreign,
                invoice_tradings.total as total,
                invoice_tradings.jumlah as qty
            ')
            ->get();

        $get_invoice_ids = $invoices->pluck('id')->unique()->values()->toArray();

        $invoice_additionals = DB::table('inv_trading_add_ons as additional')
            ->whereIn('additional.invoice_trading_id', $get_invoice_ids)
            ->when($request->item_id ?? false, function ($query, $item) {
                return $query->where('additional.item_id', $item);
            })
            ->join('items', 'items.id', 'additional.item_id')
            ->selectRaw('
                additional.id as id,
                additional.invoice_trading_id as invoice_id,
                items.kode as item_code,
                items.nama as item_name,
                additional.sub_total as dpp,
                additional.total as total
            ')
            ->get();

        $get_additional_ids = $invoice_additionals->pluck('id')->unique()->values()->toArray();

        $invoice_taxes = DB::table('invoice_taxes')
            ->where('invoice_taxes.reference_model', InvoiceTrading::class)
            ->whereIn('invoice_taxes.reference_id', $get_invoice_ids)
            ->join('taxes', 'taxes.id', 'invoice_taxes.tax_id')
            ->selectRaw('
                invoice_taxes.id as id,
                invoice_taxes.reference_id as invoice_id,
                taxes.name as tax_name,
                taxes.type as tax_type,
                taxes.value as tax_value,
                invoice_taxes.amount as tax_total
            ')
            ->get();

        $invoice_additional_taxes = DB::table('inv_trading_add_on_taxes as additional_taxes')
            ->whereIn('additional_taxes.inv_trading_add_on_id', $get_additional_ids)
            ->join('taxes', 'taxes.id', 'additional_taxes.tax_id')
            ->selectRaw('
                additional_taxes.id as id,
                additional_taxes.inv_trading_add_on_id as additional_id,
                taxes.name as tax_name,
                taxes.type as tax_type,
                taxes.value as tax_value,
                additional_taxes.total as tax_total
            ')
            ->get();

        $results = $invoices->map(function ($invoice) use ($invoice_additionals, $invoice_taxes, $invoice_additional_taxes) {
            $invoice->products = [
                [
                    'item_code' => $invoice->item_code,
                    'item_name' => $invoice->item_name,
                    'dpp' => $invoice->subtotal,
                    'ppn' => $invoice_taxes->where('invoice_id', $invoice->id)->where('tax_type', 'ppn')->sum('tax_total'),
                    'other_taxes' => $invoice_taxes->where('invoice_id', $invoice->id)->where('tax_type', '!=', 'ppn')->sum('tax_total'),
                    'total_foreign' => $invoice->total_foreign,
                    'total' => $invoice->total_foreign * $invoice->exchange_rate,
                ]
            ];

            $item_additional = $invoice_additionals->where('invoice_id', $invoice->id);

            foreach ($item_additional ?? [] as $additional) {
                $invoice->products[] = [
                    'item_code' => $additional->item_code,
                    'item_name' => $additional->item_name,
                    'dpp' => $additional->dpp,
                    'ppn' => $invoice_additional_taxes->where('additional_id', $additional->id)->where('tax_type', 'ppn')->sum('tax_total'),
                    'other_taxes' => $invoice_additional_taxes->where('additional_id', $additional->id)->where('tax_type', '!=', 'ppn')->sum('tax_total'),
                    'total_foreign' => $additional->total,
                    'total' => $additional->total * $invoice->exchange_rate,
                ];
            }

            return $invoice;
        });

        return [
            'data' => $results,
            'type' => 'trading-sales-detail-additional',
            'title' => 'Laporan Penjualan Trading Detail & Additional',
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
        ];
    }
}
