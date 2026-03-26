<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\SaleOrderGeneral\DeliveryOrderReportExport;
use App\Exports\Admin\SaleOrderGeneral\HistorySalesOrderExport;
use App\Exports\Admin\SaleOrderGeneral\SaleOrderGeneralDetailExport;
use App\Exports\Admin\SaleOrderGeneral\SaleOrderGeneralExport;
use App\Exports\Admin\SaleOrderGeneral\SaleOrderGeneralFakturPajakExport;
use App\Exports\Admin\SaleOrderGeneral\SaleOrderGeneralPerPeriodExport;
use App\Exports\Admin\SaleOrderGeneral\SaleOrderGeneralSummaryExport;
use App\Http\Controllers\Controller;
use App\Http\Traits\ResponseTrait;
use App\Models\DeliveryOrderGeneralDetail;
use App\Models\InvoiceGeneral;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SaleOrderGeneralReportController extends Controller
{
    use ResponseTrait;
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'sale-order-general-report';

    public function __construct() {}

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
            case "sale-order-general":
                $data = $this->saleOrderGeneral($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SaleOrderGeneralExport::class;
                break;
            case "laporan-history-sale-order":
                $data = $this->historySaleOrder($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = HistorySalesOrderExport::class;
                break;
            case "sale-order-general-faktur-pajak":
                $data = $this->saleOrderGeneralFakturPajak($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SaleOrderGeneralFakturPajakExport::class;
                break;
            case "summary-sale-order-general":
                $data = $this->saleOrderGeneralSummary($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SaleOrderGeneralSummaryExport::class;
                break;
            case "sale-order-general-detail":
                $data = $this->saleOrderGeneralDetail($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SaleOrderGeneralDetailExport::class;
                break;
            case "delivery-order-general":
                $data = $this->deliveryOrder($request);
                $orientation = 'landscape';
                $paper_size = 'a2';
                $excel_export = DeliveryOrderReportExport::class;
                break;
            case "per-periode-sale-order-general":

                $this->validate($request, [
                    'month' => 'required',
                ]);

                $data = $this->saleOrderGeneralPerPeriod($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SaleOrderGeneralPerPeriodExport::class;

                break;

            case "per-periode-sale-order-general-2":

                $this->validate($request, [
                    'month' => 'required',
                ]);

                $data = $this->saleOrderGeneralPerPeriod($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = SaleOrderGeneralPerPeriodExport::class;

                break;

            case "daily-sale-order-general-item-detail-customer":

                $data = $this->dialySaleOrderGeneralItemDetailCustomer($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderGeneral\DialySaleOrderGeneralItemDetailCustomerExport::class;
                break;

            case "monthly-sale-order-general-item-detail-customer":

                $this->validate($request, [
                    'month' => 'required',
                ]);

                $data = $this->monthlySaleOrderGeneralItemDetailCustomer($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderGeneral\MonthlySaleOrderGeneralItemDetailCustomerExport::class;
                break;

            case "sale-order-general-outstanding":

                $data = $this->saleOrderGeneralOutstandingReport($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderGeneral\SaleOrderGeneralOutstandingExport::class;
                break;

            case "stock-comparison-with-sale-order-general":

                $data = $this->stockComparisonWithSaleOrderGeneral($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderGeneral\StockComparisonWithSaleOrderGeneralExport::class;
                break;

            case "invoice-return-sale-order-general":

                $data = $this->invoiceReturnSaleOrderGeneral($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderGeneral\InvoiceReturnSaleOrderGeneralExport::class;
                break;

            case "invoice-return-sale-order-general-detail":

                $data = $this->saleOrderGeneralReturnDetail($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderGeneral\InvoiceReturnSaleOrderGeneralDetailExport::class;
                break;

            case "debt-due-sale-order-general":

                $data = $this->debtDueSaleOrderGeneral($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderGeneral\debtDueSaleOrderGeneralExport::class;
                break;

            case "receivable-aging":

                $data = $this->receivableAging($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderGeneral\debtDueSaleOrderGeneralExport::class;
                break;

            default:
                return redirect()->route("admin.sale-order-general.report")->with($this->ResponseMessageCRUD(false, "report", "selected report type was not found"));
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
            return redirect()->route("admin.sale-order-general.report")->with($this->ResponseMessageCRUD(false, "report", "selected export format was not found"));
        }
    }

    /**
     * General report for sale order general
     *
     * @param $request
     * @return array|mixed
     */
    public function saleOrderGeneral($request): array
    {
        // * GET DATA =======================================================
        $model = DB::table('invoice_generals')
            ->leftJoin('invoice_general_details', 'invoice_general_details.invoice_general_id', 'invoice_generals.id')
            ->leftJoin('sale_order_general_details as sog_detail', 'sog_detail.id', 'invoice_general_details.sale_order_general_detail_id')
            ->leftJoin('sale_order_generals as sog', 'sog.id', 'sog_detail.sale_order_general_id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_generals.customer_id')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_generals.branch_id')
            ->leftJoin('delivery_order_general_details', function ($j) {
                $j->on('delivery_order_general_details.id', 'invoice_general_details.delivery_order_general_detail_id');
            })
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', 'delivery_order_general_details.delivery_order_general_id')
            ->leftJoin('ware_houses', 'ware_houses.id', '=', 'delivery_order_generals.ware_house_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_general_details.item_id')
            ->leftJoin('units', 'units.id', '=', 'invoice_general_details.unit_id')
            ->whereNull('invoice_generals.deleted_at')
            ->whereNotIn('invoice_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->when($request['from_date'], function ($query) use ($request) {
                return $query->whereDate('invoice_generals.date', '>=', Carbon::parse($request['from_date']));
            })
            ->when($request['to_date'], function ($query) use ($request) {
                return $query->whereDate('invoice_generals.date', '<=', Carbon::parse($request['to_date']));
            })
            ->when(isset($request['customer_id']), function ($query) use ($request) {
                return $query->where('invoice_generals.customer_id', $request['customer_id']);
            })
            ->when(isset($request['item_id']), function ($query) use ($request) {
                return $query->where('invoice_general_details.item_id', $request['item_id']);
            })
            ->when(isset($request['warehouse_id']), function ($query) use ($request) {
                return $query->where('delivery_order_generals.ware_house_id', $request['warehouse_id']);
            })
            ->when(in_array($request['status'], payment_status()), function ($query) use ($request) {
                return $query->where('invoice_generals.payment_status', $request['status']);
            })
            // ->when(isset(get_current_branch()->is_primary) && isset($request['branch_id']), function ($query) use ($request) {
            //     return $query->where('invoice_generals.branch_id', $request['branch_id']);
            // })
            // ->when(!isset(get_current_branch()->is_primary), function ($query) {
            //     return $query->where('invoice_generals.branch_id', isset(get_current_branch()->id));
            // })
            ->groupBy('invoice_generals.id')
            ->selectRaw('
                invoice_generals.id,
                invoice_generals.date,
                invoice_generals.code,
                invoice_generals.sale_order_general_id,
                sog.kode as so_code,
                customers.nama as customer_name,
                branches.name as branch_name,
                invoice_generals.reference,
                invoice_generals.due_date,
                invoice_generals.status,
                invoice_generals.payment_status,
                invoice_generals.exchange_rate,
                delivery_order_generals.code as dog_code,
                delivery_order_generals.target_delivery
            ')
            ->get();

        $invoice_general_additionals = DB::table('invoice_general_additionals')
            ->join('invoice_generals', 'invoice_generals.id', '=', 'invoice_general_additionals.invoice_general_id')
            ->whereIn('invoice_generals.id', $model->pluck('id')->toArray())
            ->select('invoice_general_additionals.id', 'invoice_general_additionals.invoice_general_id', 'invoice_general_additionals.item_id', 'invoice_general_additionals.price', 'invoice_general_additionals.quantity', 'invoice_general_additionals.sub_total', 'invoice_general_additionals.total_tax', 'invoice_general_additionals.total')
            ->get();

        $invoice_general_details = DB::table('invoice_general_details')
            ->join('sale_order_generals as sog', 'sog.id', 'invoice_general_details.sale_order_general_id')
            ->whereIn('invoice_general_id', $model->pluck('id')->toArray())
            ->select('invoice_general_id', 'sog.kode as so_code', 'invoice_general_details.sale_order_general_id')
            ->groupBy('sog.kode')
            ->get();

        $model_detail_ids = $model->pluck('id')->toArray();
        $data_model_details = DB::table('invoice_general_details')
            ->whereIn('invoice_general_details.invoice_general_id', $model_detail_ids)
            ->get();


        // * END GET DATA =======================================================

        // * COMBINE DATA =======================================================
        $result = $model->map(function ($item) use ($data_model_details, $invoice_general_details, $invoice_general_additionals) {
            $details = $data_model_details->where('invoice_general_id', $item->id);
            $item->so_codes = $invoice_general_details->where('invoice_general_id', $item->id)->values() ?? [];

            $item->total_1 = $details->sum('total');
            $item->total_2 = $details->sum('total') * $item->exchange_rate;

            $additional_total = $invoice_general_additionals->where('invoice_general_id', $item->id)->sum('total');
            $additional_total_exchanged = $invoice_general_additionals->where('invoice_general_id', $item->id)->sum('total') * $item->exchange_rate;

            $item->total_1 += $additional_total;
            $item->total_2 += $additional_total_exchanged;

            return $item;
        });

        $modifiedData = [];
        $iteration = 1;
        foreach ($result->groupBy('code') as $parrentIndex => $parrent) {
            $modifiedData[$parrentIndex] = [];
            foreach ($parrent as $key => $value) {
                $indexBefore = $key - 1;
                if ($key == 0) {
                    $value->iteration = $iteration++;
                    array_push($modifiedData[$parrentIndex], $value);
                }
                if ($key > 0) {
                    if ($value->id != $result->groupBy('code')[$parrentIndex][$indexBefore]->id) {
                        $value->iteration = $iteration++;
                        array_push($modifiedData[$parrentIndex], $value);
                    }
                }
            }
            if (count($modifiedData[$parrentIndex]) > 0) {
                foreach ($modifiedData[$parrentIndex] as $index => $modify) {
                    $modify->delivery_orders = [];
                    foreach ($result->groupBy('code')[$parrentIndex] as $key => $value) {
                        if ($modify->id == $value->id) {
                            array_push($modify->delivery_orders, [
                                'code' => $value->dog_code,
                                'target_delivery' => $value->target_delivery
                            ]);
                        }
                    }
                }
            }
            $modifiedData[$parrentIndex] = collect($modifiedData[$parrentIndex]);
        }


        // * END COMBINE DATA =======================================================

        return [
            'data' => collect($modifiedData),
            'type' => "penjualan-general",
            "from_date" => $request['from_date'],
            "to_date" => $request['to_date'],
        ];
    }

    public function historySaleOrder($request): array
    {
        $query = DB::table('sale_order_general_details as sogd')
            ->leftJoin('sale_order_generals as sog', 'sog.id', '=', 'sogd.sale_order_general_id')
            ->leftJoin('delivery_order_general_details as dogd', 'dogd.sale_order_general_detail_id', '=', 'sogd.id')
            ->leftJoin('delivery_order_generals as dog', 'dog.id', '=', 'dogd.delivery_order_general_id')
            ->leftJoin('invoice_general_details as igd', 'igd.delivery_order_general_detail_id', '=', 'dogd.id')
            ->leftJoin('invoice_generals as ig', 'ig.id', '=', 'igd.invoice_general_id')
            ->leftJoin('items', 'items.id', '=', 'sogd.item_id')
            ->leftJoin('units', 'units.id', '=', 'sogd.unit_id')
            ->leftJoin('customers', 'customers.id', '=', 'sog.customer_id')
            ->leftJoin('branches', 'branches.id', '=', 'sog.branch_id')
            ->whereNull('sog.deleted_at')
            ->where('ig.status', '!=', 'void')
            ->when($request['from_date'], fn($query) => $query->whereDate('sog.tanggal', '>=', Carbon::parse($request['from_date'])))
            ->when($request['to_date'], fn($query) => $query->whereDate('sog.tanggal', '<=', Carbon::parse($request['to_date'])))
            ->when(isset($request['branch_id']), fn($query) => $query->where('sog.branch_id', $request['branch_id']))
            ->selectRaw('
            sog.tanggal as so_date,
            sog.kode as so_code,
            sog.id as sog_id,
            sog.no_po_external as external_po,
            items.kode as item_code,
            items.nama as keterangan,
            sogd.amount,
            units.name as unit_name,
            dog.code as do_code,
            dog.id as dog_id,
            dog.date as tgl_do,
            dogd.quantity as jumlah_dikirim,
            ig.code as invoice_code,
            ig.id as ig_id,
            sog.status,
            ig.payment_status,
            customers.nama as customer_name
        ')
            ->orderBy('sog.tanggal')
            ->get();

        $total_amount = $query->sum('amount');
        $total_dikirim = $query->sum('jumlah_dikirim');
        $total_sisa = $total_amount - $total_dikirim;

        return [
            'data' => $query,
            'type' => "laporan-history-sale-order",
            'from_date' => $request['from_date'],
            'to_date' => $request['to_date'],
            'totals' => [
                'amount' => $total_amount,
                'jumlah_dikirim' => $total_dikirim,
                'sisa_qty' => $total_sisa,
            ],
        ];
    }



    private function saleOrderGeneralFakturPajak($request): array
    {
        $model = DB::table('invoice_generals')
            ->leftJoin('invoice_general_details', 'invoice_general_details.invoice_general_id', '=', 'invoice_generals.id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_generals.customer_id')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_generals.branch_id')
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('invoice_generals.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('invoice_generals.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_generals.customer_id', $request->customer_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('invoice_generals.status', $request->status);
            })
            ->whereNull('invoice_generals.deleted_at')
            ->selectRaw('
                invoice_generals.id,
                invoice_generals.date,
                customers.nama as customer_name,
                invoice_generals.code,
                invoice_generals.status,
                invoice_generals.reference,
                invoice_general_details.sub_total,
                invoice_general_details.total_tax
            ')
            ->get();

        return [
            'data' => $model,
            'type' => "laporan-penjualan-faktur-pajak",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     * Generate report for summary sale order general
     *
     * @param $request
     * @return array|mixed
     */
    private function saleOrderGeneralSummary($request): array
    {
        // * GET DATA =======================================================
        $model = DB::table('invoice_generals')
            ->leftJoin('invoice_general_details', 'invoice_general_details.invoice_general_id', '=', 'invoice_generals.id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_generals.customer_id')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_generals.branch_id')
            ->leftJoin('delivery_order_general_details', 'invoice_general_details.delivery_order_general_detail_id', '=', 'delivery_order_general_details.id')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', '=', 'delivery_order_general_details.delivery_order_general_id')
            ->leftJoin('ware_houses', 'ware_houses.id', '=', 'delivery_order_generals.ware_house_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_general_details.item_id')
            ->leftJoin('units', 'units.id', '=', 'invoice_general_details.unit_id')
            ->whereNull('invoice_generals.deleted_at')
            ->whereNotIn('invoice_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('invoice_generals.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('invoice_generals.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_generals.customer_id', $request->customer_id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('invoice_general_details.item_id', $request->item_id);
            })
            ->when($request->warehouse_id, function ($query) use ($request) {
                return $query->where('delivery_order_generals.ware_house_id', $request->warehouse_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_generals.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_generals.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_generals.id')
            ->selectRaw('
                invoice_generals.id,
                invoice_generals.date,
                invoice_generals.code,
                customers.nama as customer_name,
                branches.name as branch_name,
                invoice_generals.reference,
                invoice_generals.due_date,
                invoice_generals.payment_status,
                invoice_generals.exchange_rate
            ')
            ->get();

        $model_detail_ids = $model->pluck('id')->toArray();
        $data_model_details = DB::table('invoice_general_details')
            ->whereIn('invoice_general_details.invoice_general_id', $model_detail_ids)
            ->get();

        // * END GET DATA =======================================================

        $result = $model->map(function ($item) use ($data_model_details) {
            $details = $data_model_details->where('invoice_general_id', $item->id);

            $item->sub_total = $details->sum('sub_total') * $item->exchange_rate;
            $item->total_tax = $details->sum('total_tax') * $item->exchange_rate;
            $item->total = $details->sum('total') * $item->exchange_rate;

            return $item;
        });

        return [
            'data' => $result,
            'type' => "ringkasan-penjualan-general",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     * Generate report for sale order detail
     *
     * @param $request
     * @return array|mixed
     */
    private function saleOrderGeneralDetail($request): array
    {
        $model = DB::table('invoice_generals')
            ->leftJoin('invoice_general_details', 'invoice_general_details.invoice_general_id', '=', 'invoice_generals.id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_generals.customer_id')
            ->leftJoin('branches', 'branches.id', '=', 'invoice_generals.branch_id')
            ->leftJoin('delivery_order_general_details', 'invoice_general_details.delivery_order_general_detail_id', '=', 'delivery_order_general_details.id')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', '=', 'delivery_order_general_details.delivery_order_general_id')
            ->leftJoin('ware_houses', 'ware_houses.id', '=', 'delivery_order_generals.ware_house_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_general_details.item_id')
            ->leftJoin('units', 'units.id', '=', 'invoice_general_details.unit_id')
            ->whereNull('invoice_generals.deleted_at')
            ->leftJoin('sale_order_generals as sog', 'sog.id', 'invoice_generals.sale_order_general_id')
            ->whereNotIn('invoice_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('invoice_generals.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('invoice_generals.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_generals.customer_id', $request->customer_id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('invoice_general_details.item_id', $request->item_id);
            })
            ->when($request->warehouse_id, function ($query) use ($request) {
                return $query->where('delivery_order_generals.ware_house_id', $request->warehouse_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_generals.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_generals.branch_id', get_current_branch()->id);
            })
            ->groupBy('invoice_generals.id')
            ->selectRaw('
                invoice_generals.id,
                invoice_generals.code,
                invoice_generals.date,
                invoice_generals.reference,
                invoice_generals.exchange_rate,
                invoice_generals.payment_status,
                customers.nama as customer_name,
                customers.alamat as customer_address,
                branches.name as branch_name,
                delivery_order_generals.target_delivery,
                sog.kode as so_code,
                sog.no_po_external as no_po_external
            ')
            ->get();

        $model_detail_ids = $model->pluck('id')->toArray();
        $data_model_details = DB::table('invoice_general_details')
            ->leftJoin('sale_order_generals as sog', 'sog.id', 'invoice_general_details.sale_order_general_id')
            ->leftJoin('delivery_order_general_details', 'invoice_general_details.delivery_order_general_detail_id', '=', 'delivery_order_general_details.id')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', '=', 'delivery_order_general_details.delivery_order_general_id')
            ->leftJoin('invoice_generals', 'invoice_generals.id', '=', 'invoice_general_details.invoice_general_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_general_details.item_id')
            ->leftJoin('units', 'units.id', '=', 'invoice_general_details.unit_id')
            ->whereIn('invoice_general_details.invoice_general_id', $model_detail_ids)
            ->selectRaw('
                invoice_general_details.id,
                invoice_general_details.invoice_general_id,
                invoice_general_details.item_id,

                items.kode as item_code,
                items.nama as item_name,

                units.name as unit_name,

                invoice_general_details.id,
                invoice_general_details.invoice_general_id,
                invoice_general_details.price as price,
                invoice_general_details.quantity,

                invoice_general_details.sub_total as detail_item_sub_total,
                invoice_general_details.total_tax as detail_item_total_tax,
                invoice_general_details.total as detail_item_total,

                delivery_order_generals.code as delivery_order_code,
                delivery_order_generals.target_delivery,
                sog.kode as so_code,
                sog.no_po_external as no_po_external,

                case
                    when invoice_generals.exchange_rate = 1
                        then invoice_general_details.total
                    else
                        invoice_general_details.total * invoice_generals.exchange_rate
                end as detail_item_total_final,

                case
                    when invoice_generals.exchange_rate = 1
                        then invoice_general_details.total_tax
                    else
                        invoice_general_details.total_tax * invoice_generals.exchange_rate
                end as detail_item_total_tax_final,

                case
                    when invoice_generals.exchange_rate = 1
                        then invoice_general_details.sub_total
                    else
                        invoice_general_details.sub_total * invoice_generals.exchange_rate
                end as detail_item_sub_total_final

            ')
            ->groupBy('invoice_general_details.id')
            ->get();

        $data_model_additionals = DB::table('invoice_general_additionals')
            ->whereIn('invoice_general_additionals.invoice_general_id', $model_detail_ids)
            ->leftJoin('items', 'items.id', '=', 'invoice_general_additionals.item_id')
            ->leftJoin('units', 'units.id', '=', 'invoice_general_additionals.unit_id')
            ->leftJoin('invoice_generals', 'invoice_generals.id', '=', 'invoice_general_additionals.invoice_general_id')
            ->selectRaw('
                invoice_general_additionals.id,
                invoice_general_additionals.invoice_general_id,
                invoice_general_additionals.item_id,

                items.kode as item_code,
                items.nama as item_name,
                units.name as unit_name,

                invoice_general_additionals.id,
                invoice_general_additionals.invoice_general_id,
                invoice_general_additionals.price as price,
                invoice_general_additionals.quantity as quantity,

                invoice_general_additionals.sub_total as detail_item_sub_total,
                invoice_general_additionals.total_tax as detail_item_total_tax,
                invoice_general_additionals.total as detail_item_total,

                case
                    when invoice_generals.exchange_rate = 1
                        then invoice_general_additionals.sub_total
                    else
                        invoice_general_additionals.sub_total * invoice_generals.exchange_rate
                end as detail_item_sub_total_final,

                case
                    when invoice_generals.exchange_rate = 1
                        then invoice_general_additionals.total_tax
                    else
                        invoice_general_additionals.total_tax * invoice_generals.exchange_rate
                end as detail_item_total_tax_final,

                case
                    when invoice_generals.exchange_rate = 1
                        then invoice_general_additionals.total
                    else
                        invoice_general_additionals.total * invoice_generals.exchange_rate
                end as detail_item_total_final
            ')
            ->get();

        $result = $model->map(function ($item) use ($data_model_details, $data_model_additionals) {
            $item->total_main = 0;
            $item->total_tax_main = 0;
            $item->sub_total_main = 0;

            $item->total_main_final = 0;
            $item->total_tax_main_final = 0;
            $item->sub_total_main_final = 0;

            $item->total_additional = 0;
            $item->total_tax_additional = 0;
            $item->sub_total_additional = 0;

            $item->total_additional_final = 0;
            $item->total_tax_additional_final = 0;
            $item->sub_total_additional_final = 0;

            $item->details = $data_model_details
                ->where('invoice_general_id', $item->id)
                ->map(function ($detail) use (&$item, $data_model_details) {
                    $item->total_main += $detail->detail_item_total;
                    $item->total_tax_main += $detail->detail_item_total_tax;
                    $item->sub_total_main += $detail->detail_item_sub_total;

                    $item->total_main_final += $detail->detail_item_total_final;
                    $item->total_tax_main_final += $detail->detail_item_total_tax_final;
                    $item->sub_total_main_final += $detail->detail_item_sub_total_final;

                    return $detail;
                });

            $item->additionals = $data_model_additionals
                ->where('invoice_general_id', $item->id)
                ->map(function ($additional) use (&$item, $data_model_additionals) {
                    $item->total_additional += $additional->detail_item_total;
                    $item->total_tax_additional += $additional->detail_item_total_tax;
                    $item->sub_total_additional += $additional->detail_item_sub_total;

                    $item->total_additional_final += $additional->detail_item_total_final;
                    $item->total_tax_additional_final += $additional->detail_item_total_tax_final;
                    $item->sub_total_additional_final += $additional->detail_item_sub_total_final;

                    return $additional;
                });

            return $item;
        });

        return [
            'data' => $result,
            'type' => "rincian-penjualan-general-per-customer",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     * Generate delivery order report
     *
     * @param $request
     * @return array
     */
    private function deliveryOrder($request): array
    {
        // ! PARENT DATA ---------------------------------------------------------------------------------------------------------------------
        $model = DB::table('delivery_order_generals')
            ->leftJoin('customers', 'customers.id', '=', 'delivery_order_generals.customer_id')
            ->leftJoin('branches', 'branches.id', '=', 'delivery_order_generals.branch_id')
            ->leftJoin('delivery_order_general_details', function ($delivery_order) {
                return $delivery_order
                    ->on('delivery_order_generals.id', '=', 'delivery_order_general_details.delivery_order_general_id');
            })
            ->leftJoin('sale_order_generals', function ($sale_order) {
                return $sale_order
                    ->on('delivery_order_generals.sale_order_general_id', '=', 'sale_order_generals.id');
            })
            ->leftJoin('ware_houses', function ($ware_house) {
                return $ware_house
                    ->on('delivery_order_generals.ware_house_id', '=', 'ware_houses.id');
            })
            ->whereNull('delivery_order_generals.deleted_at')
            ->whereNotIn('delivery_order_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('delivery_order_generals.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('delivery_order_generals.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('delivery_order_generals.customer_id', $request->customer_id);
            })
            ->when($request->warehouse_id, function ($query) use ($request) {
                return $query->where('delivery_order_generals.ware_house_id', $request->warehouse_id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query
                    ->where('delivery_order_general_details.item_id', $request->item_id);
            })
            ->when(in_array($request->status, delivery_order_general_status()), function ($query) use ($request) {
                return $query->where('delivery_order_generals.status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('delivery_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('delivery_order_generals.branch_id', get_current_branch()->id);
            })
            ->whereNull('delivery_order_generals.deleted_at')
            ->selectRaw('
                delivery_order_generals.branch_id,
                delivery_order_generals.id,
                delivery_order_generals.date,
                delivery_order_generals.code,
                delivery_order_generals.status,
                delivery_order_generals.description,

                customers.nama as customer_name,
                branches.name as branch_name,

                sale_order_generals.exchange_rate as exchange_rate,
                sale_order_generals.kode as sale_order_general_code,
                sale_order_generals.id as id_sale_order_general,

                ware_houses.nama as ware_house_name
            ')->distinct('delivery_order_generals.id');

        // ! PARENT DATA ---------------------------------------------------------------------------------------------------------------------

        // ! DELIVERY ORDER DETAIL ---------------------------------------------------------------------------------------------------------------------
        $delivery_order_general_details_model = clone $model;
        $delivery_order_general_detail_ids = $delivery_order_general_details_model->get()->pluck(['id']);
        $delivery_order_general_details_data = DB::table('delivery_order_general_details')
            ->whereIn('delivery_order_general_details.delivery_order_general_id', $delivery_order_general_detail_ids)
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('delivery_order_general_details.item_id', $request->item_id);
            })
            ->selectRaw('
                delivery_order_general_details.id,
                delivery_order_general_details.delivery_order_general_id,
                delivery_order_general_details.sale_order_general_detail_id,
                delivery_order_general_details.quantity
            ');
        // ! DELIVERY ORDER DETAIL ---------------------------------------------------------------------------------------------------------------------

        // ! SALE ORDER GENERAL DETAIL DATA ---------------------------------------------------------------------------------------------------------------------
        // * DETAIL DATA ==================================================================================================================================
        $sale_order_general_model_detail_model = clone $delivery_order_general_details_data;
        $sale_order_general_model_detail_ids = $sale_order_general_model_detail_model->get()->pluck(['sale_order_general_detail_id']);
        $sale_order_general_detail_data = DB::table('sale_order_general_details')
            ->whereIn('sale_order_general_details.id', $sale_order_general_model_detail_ids)
            ->selectRaw('
                sale_order_general_details.id,
                sale_order_general_details.sale_order_general_id,
                sale_order_general_details.price
            ');

        // * TAX DATA
        $sale_order_general_model_detail_tax_model = DB::table('sale_order_general_detail_taxes')
            ->whereIn('sale_order_general_detail_taxes.so_general_detail_id', $sale_order_general_model_detail_ids);
        $sale_order_general_model_detail_tax_ids = $sale_order_general_model_detail_tax_model->get()->pluck(['id']);
        $sale_order_general_tax_data = DB::table('sale_order_general_detail_taxes')
            ->whereIn('sale_order_general_detail_taxes.id', $sale_order_general_model_detail_tax_ids)
            ->selectRaw('
                sale_order_general_detail_taxes.id,
                sale_order_general_detail_taxes.so_general_detail_id as sale_order_general_detail_id,
                sale_order_general_detail_taxes.value
            ');
        // ! SALE ORDER GENERAL DETAIL DATA ---------------------------------------------------------------------------------------------------------------------

        $new_delivery_order_general_details_data = $delivery_order_general_details_data->get();
        $new_sale_order_general_detail_data = $sale_order_general_detail_data->get();
        $new_sale_order_general_tax_data = $sale_order_general_tax_data->get();

        $invoices = DB::table('invoice_general_details')
            ->join('invoice_generals', 'invoice_generals.id', '=', 'invoice_general_details.invoice_general_id')
            ->join('delivery_order_general_details', 'delivery_order_general_details.id', '=', 'invoice_general_details.delivery_order_general_detail_id')
            ->join('delivery_order_generals', 'delivery_order_generals.id', '=', 'delivery_order_general_details.delivery_order_general_id')
            ->whereIn('invoice_general_details.delivery_order_general_detail_id', $delivery_order_general_details_data->pluck('id')->toArray())
            ->whereNull('invoice_generals.deleted_at')
            ->select('delivery_order_generals.id', 'invoice_generals.id as invoice_general_id', 'invoice_generals.code')
            ->get();

        // ? COMBINE DATA ============================================================================================================
        $model = $model->get()->map(function ($model) use ($new_delivery_order_general_details_data, $new_sale_order_general_detail_data, $new_sale_order_general_tax_data, $invoices) {
            $exchange_rate = $model->exchange_rate;

            $sub_total = 0;
            $total = 0;
            $total_tax = 0;

            $sub_total_final = 0;
            $total_tax_final = 0;
            $total_final = 0;

            // * HAS MANY DELIVERY ORDER DETAILS
            foreach ($new_delivery_order_general_details_data->where('delivery_order_general_id', $model->id)->all() as $delivery_order_general_details_data) {
                // * BELONGS TO SALE ORDER DETAIL
                foreach ($new_sale_order_general_detail_data->where('id', $delivery_order_general_details_data->sale_order_general_detail_id)->all() as $sale_order_general_detail_data) {
                    $single_sub_total = $sale_order_general_detail_data->price * $delivery_order_general_details_data->quantity;
                    $single_sub_total_final = $sale_order_general_detail_data->price * $delivery_order_general_details_data->quantity * $exchange_rate;

                    // * CALCULATE TOTAL
                    $sub_total += $single_sub_total;
                    $sub_total_final += $single_sub_total_final;
                    $total += $sub_total;
                    $total_final += $sub_total_final;
                    // * HAS MANY SALE ORDER DETAIL TAXES

                    foreach ($new_sale_order_general_tax_data->where('sale_order_general_detail_id', $sale_order_general_detail_data->id)->all() as $key => $value) {
                        // * CALCULATE TOTAL TAX
                        $total_tax += $value->value * $single_sub_total;
                        $total_tax_final += $value->value * $single_sub_total_final;
                        $total += $value->value * $single_sub_total_final;
                        $total_final += $value->value * $single_sub_total_final;
                    }
                }
            }

            $model->sub_total = $sub_total;
            $model->total = $total;
            $model->total_tax = $total_tax;
            $model->sub_total_final = $sub_total_final;
            $model->total_tax_final = $total_tax_final;
            $model->total_final = $total_final;
            $model->invoices = $invoices->where('id', $model->id)
                ->pluck('code')
                ->unique()
                ->toArray();

            return $model;
        });
        // ? COMBINE DATA ============================================================================================================

        return [
            'data' => $model,
            'type' => "delivery-order-general",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     * Get sale order general report per period last year
     *
     * @param $request
     * @param $year
     * @return object|Collection
     */
    private function saleOrderGeneralPerPeriodLastYear($request, $year): object
    {
        $model = DB::table('invoice_general_details')
            ->leftJoin('invoice_generals', 'invoice_general_details.invoice_general_id', '=', 'invoice_generals.id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_generals.customer_id')
            ->leftJoin('delivery_order_general_details', 'invoice_general_details.delivery_order_general_detail_id', '=', 'delivery_order_general_details.id')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', '=', 'delivery_order_general_details.delivery_order_general_id')
            ->leftJoin('ware_houses', 'ware_houses.id', '=', 'delivery_order_generals.ware_house_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_general_details.item_id')
            ->whereNull('invoice_generals.deleted_at')
            ->whereNotIn('invoice_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->whereYear('invoice_generals.date', $year)
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_generals.customer_id', $request->customer_id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('invoice_general_details.item_id', $request->item_id);
            })
            ->when($request->warehouse_id, function ($query) use ($request) {
                return $query->where('delivery_order_generals.ware_house_id', $request->warehouse_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_generals.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_generals.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_general_details.id')
            ->selectRaw('

                invoice_general_details.id,
                invoice_generals.date,

                customers.id as customer_id,
                customers.nama as customer_name,
                customers.code as customer_code,

                items.kode as item_code,
                items.nama as item_name,

                invoice_general_details.item_id,
                invoice_general_details.quantity,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.price
                    else invoice_general_details.price * invoice_generals.exchange_rate
                end as price,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.sub_total
                    else invoice_general_details.sub_total * invoice_generals.exchange_rate
                end as sub_total,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.total_tax
                    else invoice_general_details.total_tax * invoice_generals.exchange_rate
                end as total_tax,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.total
                    else invoice_general_details.total * invoice_generals.exchange_rate
                end as total
            ')
            ->get();

        return $model;
    }

    /**
     * Get sale order general report per period selected month
     *
     * @param $request
     * @param $year
     * @param $month
     * @return object|Collection
     */
    private function saleOrderGeneralPerPeriodSelectedMonth($request, $year, $month): object
    {
        $model = DB::table('invoice_general_details')
            ->leftJoin('invoice_generals', 'invoice_general_details.invoice_general_id', '=', 'invoice_generals.id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_generals.customer_id')
            ->leftJoin('delivery_order_general_details', 'invoice_general_details.delivery_order_general_detail_id', '=', 'delivery_order_general_details.id')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', '=', 'delivery_order_general_details.delivery_order_general_id')
            ->leftJoin('ware_houses', 'ware_houses.id', '=', 'delivery_order_generals.ware_house_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_general_details.item_id')
            ->whereNull('invoice_generals.deleted_at')
            ->whereNotIn('invoice_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->whereMonth('invoice_generals.date', $month)
            ->whereYear('invoice_generals.date', $year)
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_generals.customer_id', $request->customer_id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('invoice_general_details.item_id', $request->item_id);
            })
            ->when($request->warehouse_id, function ($query) use ($request) {
                return $query->where('delivery_order_generals.ware_house_id', $request->warehouse_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_generals.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_generals.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_general_details.id')
            ->selectRaw('

                invoice_general_details.id,
                invoice_generals.date,

                customers.id as customer_id,
                customers.nama as customer_name,
                customers.code as customer_code,

                items.kode as item_code,
                items.nama as item_name,

                invoice_general_details.item_id,
                invoice_general_details.quantity,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.price
                    else invoice_general_details.price * invoice_generals.exchange_rate
                end as price,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.sub_total
                    else invoice_general_details.sub_total * invoice_generals.exchange_rate
                end as sub_total,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.total_tax
                    else invoice_general_details.total_tax * invoice_generals.exchange_rate
                end as total_tax,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.total
                    else invoice_general_details.total * invoice_generals.exchange_rate
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
    private function saleOrderGeneralPerPeriodJanuaryToSelectedMonthThisYear($request, $year, $month): object
    {
        $model = DB::table('invoice_general_details')
            ->leftJoin('invoice_generals', 'invoice_general_details.invoice_general_id', '=', 'invoice_generals.id')
            ->leftJoin('customers', 'customers.id', '=', 'invoice_generals.customer_id')
            ->leftJoin('delivery_order_general_details', 'invoice_general_details.delivery_order_general_detail_id', '=', 'delivery_order_general_details.id')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', '=', 'delivery_order_general_details.delivery_order_general_id')
            ->leftJoin('ware_houses', 'ware_houses.id', '=', 'delivery_order_generals.ware_house_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_general_details.item_id')
            ->whereNull('invoice_generals.deleted_at')
            ->whereNotIn('invoice_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->whereMonth('invoice_generals.date', '<=', $month)
            ->whereYear('invoice_generals.date', $year)
            ->whereMonth('invoice_generals.date', $month)
            ->whereYear('invoice_generals.date', $year)
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_generals.customer_id', $request->customer_id);
            })
            ->when($request->item_id, function ($query) use ($request) {
                return $query->where('invoice_general_details.item_id', $request->item_id);
            })
            ->when($request->warehouse_id, function ($query) use ($request) {
                return $query->where('delivery_order_generals.ware_house_id', $request->warehouse_id);
            })
            ->when(in_array($request->status, payment_status()), function ($query) use ($request) {
                return $query->where('invoice_generals.payment_status', $request->status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('invoice_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('invoice_generals.branch_id', get_current_branch()->id);
            })
            ->distinct('invoice_general_details.id')
            ->selectRaw('

                invoice_general_details.id,
                invoice_generals.date,

                customers.id as customer_id,
                customers.nama as customer_name,
                customers.code as customer_code,

                items.kode as item_code,
                items.nama as item_name,

                invoice_general_details.item_id,
                invoice_general_details.quantity,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.price
                    else invoice_general_details.price * invoice_generals.exchange_rate
                end as price,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.sub_total
                    else invoice_general_details.sub_total * invoice_generals.exchange_rate
                end as sub_total,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.total_tax
                    else invoice_general_details.total_tax * invoice_generals.exchange_rate
                end as total_tax,

                case when invoice_generals.exchange_rate = 1
                    then invoice_general_details.total
                    else invoice_general_details.total * invoice_generals.exchange_rate
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
    private function combineSaleOrderGeneralPerPeriod($previous_year_data, $selected_month_data, $january_to_selected_month_data): object|array
    {
        $customer_group = $selected_month_data->unique('customer_id');

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
            $customer_id = $item->customer_id;
            $customer_name = $item->customer_name;
            $customer_code = $item->customer_code;

            $_new_previous_year_data = $previous_year_data->where('customer_id', $customer_id);
            $_new_selected_month_data = $selected_month_data->where('customer_id', $customer_id);
            $_new_january_to_selected_month_data = $january_to_selected_month_data->where('customer_id', $customer_id);

            $item_group = $selected_month_data->unique('item_id');

            $item_group = $item_group->map(function ($item) use ($_new_previous_year_data, $_new_selected_month_data, $_new_january_to_selected_month_data, $customer_id, $customer_name, $customer_code, &$total) {
                $item_id = $item->item_id;
                $item_code = $item->item_code;
                $item_name = $item->item_name;
                $date = $item->date;
                $price = $item->price;

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
                $item->customer_id = $customer_id;
                $item->customer_name = $customer_name;
                $item->customer_code = $customer_code;
                $item->date = $date;
                $item->price = $price;
                $item->item_id = $item_id;
                $item->item_code = $item_code;
                $item->item_name = $item_name;
                $item->previous_year_quantity = $previous_year_quantity;
                $item->previous_year_sub_total = $previous_year_sub_total;
                $item->previous_year_total_tax = $previous_year_total_tax;
                $item->previous_year_total = $previous_year_total;
                $item->selected_month_quantity = $selected_month_quantity;
                $item->selected_month_sub_total = $selected_month_sub_total;
                $item->selected_month_total_tax = $selected_month_total_tax;
                $item->selected_month_total = $selected_month_total;
                $item->january_to_selected_month_quantity = $january_to_selected_month_quantity;
                $item->january_to_selected_month_sub_total = $january_to_selected_month_sub_total;
                $item->january_to_selected_month_total_tax = $january_to_selected_month_total_tax;
                $item->january_to_selected_month_total = $january_to_selected_month_total;

                $total->previous_year_quantity += $previous_year_quantity;
                $total->previous_year_sub_total += $previous_year_sub_total;
                $total->previous_year_total_tax += $previous_year_total_tax;
                $total->previous_year_total += $previous_year_total;
                $total->selected_month_quantity += $selected_month_quantity;
                $total->selected_month_sub_total += $selected_month_sub_total;
                $total->selected_month_total_tax += $selected_month_total_tax;
                $total->selected_month_total += $selected_month_total;
                $total->january_to_selected_month_quantity += $january_to_selected_month_quantity;
                $total->january_to_selected_month_sub_total += $january_to_selected_month_sub_total;
                $total->january_to_selected_month_total_tax += $january_to_selected_month_total_tax;
                $total->january_to_selected_month_total += $january_to_selected_month_total;

                return $item;
            });

            return collect([
                'customer_id' => $customer_id,
                'customer_name' => $customer_name,
                'customer_code' => $customer_code,
                'detail' => $item_group,
            ]);
        });

        return [
            'data' => $result,
            'total' => $total,
        ];
    }

    /**
     * Generate report sale order general per period
     *
     * @param $request
     * @return array
     */
    private function saleOrderGeneralPerPeriod($request): array
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
        $last_year_data = $this->saleOrderGeneralPerPeriodLastYear($request, $previous_year);
        $selected_month_data = $this->saleOrderGeneralPerPeriodSelectedMonth($request, $selected_year, $selected_month);
        $january_to_selected_month_data = $this->saleOrderGeneralPerPeriodJanuaryToSelectedMonthThisYear($request, $selected_year, $selected_month);
        // ! END GET DATA ============================================================================================================

        // return compact('last_year_data', 'selected_month_data', 'january_to_selected_month_data');

        // ! COMBINE DATA ============================================================================================================
        $model = $this->combineSaleOrderGeneralPerPeriod($last_year_data, $selected_month_data, $january_to_selected_month_data);
        // ! END COMBINE DATA ========================================================================================================

        return [
            'data' => $model['data'],
            'total' => $model['total'],
            'type' => 'per-periode-penjualan-general',
            'period' => $request->month,
        ];
    }

    /**
     *
     */
    private function dialySaleOrderGeneralItemDetailCustomer($request)
    {
        $deliveryOrders = DB::table('delivery_order_general_details')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', 'delivery_order_general_details.delivery_order_general_id')
            ->leftJoin('items', 'items.id', 'delivery_order_general_details.item_id')
            ->leftJoin('sale_order_general_details', 'sale_order_general_details.id', 'delivery_order_general_details.sale_order_general_detail_id')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'delivery_order_generals.sale_order_general_id')
            ->leftJoin('invoice_generals as ig', 'sale_order_generals.id', 'ig.sale_order_general_id')
            ->leftJoin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->when($request->ware_house_id, fn($q) => $q->where('delivery_order_generals.ware_house_id', $request->ware_house_id))
            ->when($request->item_id, fn($q) => $q->where('delivery_order_general_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_order_generals.customer_id', $request->customer_id))
            ->when($request->from_date, fn($q) => $q->whereDate('delivery_order_generals.date', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('delivery_order_generals.date', '<=', Carbon::parse($request->to_date)))
            ->whereNotIn('delivery_order_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_order_generals.branch_id', get_current_branch()->id);
            })
            ->whereNull('sale_order_generals.deleted_at')
            ->whereNull('delivery_order_generals.deleted_at')
            ->distinct('delivery_order_general_details.id')
            ->selectRaw('

                delivery_order_general_details.id as delivery_order_general_detail_id,

                delivery_order_generals.date as do_date,
                delivery_order_generals.code as do_code,

                ig.code as ig_code,

                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,

                delivery_order_general_details.quantity as quantity,
                delivery_order_general_details.quantity_received as quantity_received,

                sale_order_general_details.price as price,
                sale_order_generals.exchange_rate as exchange_rate
            ')
            ->get();

        $invoiceReturns = DB::table('invoice_return_details')
            ->leftJoin('invoice_returns', 'invoice_returns.id', 'invoice_return_details.invoice_return_id')
            ->leftJoin('items', 'items.id', 'invoice_return_details.item_id')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'invoice_returns.id')
            ->leftJoin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->where('invoice_returns.reference_model', \App\Models\DeliveryOrderGeneral::class)
            ->where('invoice_return_details.reference_model', \App\Models\DeliveryOrderGeneralDetail::class)
            ->whereNull('invoice_returns.deleted_at')
            ->where('invoice_returns.status', 'approve')
            ->where('invoice_returns.type', 'general')
            ->whereIn('invoice_return_details.reference_id', $deliveryOrders->pluck('delivery_order_general_detail_id')->toArray())
            ->selectRaw('
                invoice_return_details.reference_id as delivery_order_general_detail_id,

                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,

                invoice_return_details.return_qty as quantity,
                invoice_return_details.price as price,

                invoice_returns.exchange_rate as exchange_rate
            ')
            ->get();

        $invoiceReturnDeliveryOrderGeneralIds = $invoiceReturns->pluck('delivery_order_general_detail_id')->unique()->toArray();

        // * Process results
        $deliveryOrderCustomerIds = $deliveryOrders->pluck('customer_id')->unique();
        $results = $deliveryOrderCustomerIds->map(function ($customer) use ($deliveryOrders, $invoiceReturns, $invoiceReturnDeliveryOrderGeneralIds) {
            $itemIds = $deliveryOrders->where('customer_id', $customer)->pluck('item_id')->unique();

            return $itemIds->map(function ($item) use ($deliveryOrders, $invoiceReturns, $customer, $invoiceReturnDeliveryOrderGeneralIds) {
                $deliveryOrderFiltered = $deliveryOrders
                    ->where('customer_id', $customer)
                    ->where('item_id', $item);

                $quantity = 0;
                $sub_total = 0;
                $sub_total_idr = 0;

                $customer_name = $deliveryOrderFiltered->first()?->customer_name;
                $item_name = $deliveryOrderFiltered->first()?->item_name;
                $item_code = $deliveryOrderFiltered->first()?->item_code;
                $do_date = $deliveryOrderFiltered->first()?->do_date;
                $do_code = $deliveryOrderFiltered->first()?->do_code;
                $ig_code = $deliveryOrderFiltered->first()?->ig_code;

                $deliveryOrderFiltered->map(function ($data) use (&$quantity, &$sub_total, &$sub_total_idr, $invoiceReturns, $invoiceReturnDeliveryOrderGeneralIds) {
                    $price = $data->price;
                    $single_quantity = $data->quantity;

                    if (in_array($data->delivery_order_general_detail_id, $invoiceReturnDeliveryOrderGeneralIds)) {
                        $single_quantity -= $invoiceReturns->where('delivery_order_general_detail_id', $data->delivery_order_general_detail_id)->sum('return_qty');
                    }

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
                    'do_date' => $do_date,
                    'do_code' => $do_code,
                    'ig_code' => $ig_code,
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
            'type' => 'daily-sale-order-general-item-detail-customer',
            'title' => 'Laporan Harian Penjualan General Item Detail Customer',
        ];
    }

    /**
     */
    private function monthlySaleOrderGeneralItemDetailCustomerLastYear($request, $year)
    {
        $deliveryOrders = DB::table('delivery_order_general_details')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', 'delivery_order_general_details.delivery_order_general_id')
            ->leftJoin('items', 'items.id', 'delivery_order_general_details.item_id')
            ->leftJoin('sale_order_general_details', 'sale_order_general_details.id', 'delivery_order_general_details.sale_order_general_detail_id')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'delivery_order_generals.sale_order_general_id')
            ->leftJoin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->when($request->ware_house_id, fn($q) => $q->where('delivery_order_generals.ware_house_id', $request->ware_house_id))
            ->when($request->item_id, fn($q) => $q->where('delivery_order_general_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_order_generals.customer_id', $request->customer_id))
            ->whereNotIn('delivery_order_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_order_generals.branch_id', get_current_branch()->id);
            })
            ->whereYear('delivery_order_generals.date', $year)
            ->whereNull('sale_order_generals.deleted_at')
            ->whereNull('delivery_order_generals.deleted_at')
            ->distinct('delivery_order_general_details.id')
            ->selectRaw('
                delivery_order_general_details.id as delivery_order_general_detail_id,

                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,

                delivery_order_general_details.quantity as quantity,
                delivery_order_general_details.quantity_received as quantity_received,

                sale_order_general_details.price as price,
                sale_order_generals.exchange_rate as exchange_rate
            ')
            ->get();

        $invoiceReturns = DB::table('invoice_return_details')
            ->leftJoin('invoice_returns', 'invoice_returns.id', 'invoice_return_details.invoice_return_id')
            ->leftJoin('items', 'items.id', 'invoice_return_details.item_id')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'invoice_returns.id')
            ->leftJoin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->where('invoice_returns.reference_model', \App\Models\DeliveryOrderGeneral::class)
            ->where('invoice_return_details.reference_model', \App\Models\DeliveryOrderGeneralDetail::class)
            ->whereNull('invoice_returns.deleted_at')
            ->where('invoice_returns.status', 'approve')
            ->where('invoice_returns.type', 'general')
            ->whereIn('invoice_return_details.reference_id', $deliveryOrders->pluck('delivery_order_general_detail_id')->toArray())
            ->selectRaw('
                invoice_return_details.reference_id as delivery_order_general_detail_id,

                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,

                invoice_return_details.return_qty as quantity,
                invoice_return_details.price as price,

                invoice_returns.exchange_rate as exchange_rate
            ')
            ->get();

        return compact('deliveryOrders', 'invoiceReturns');
    }

    /**
     */
    private function monthlySaleOrderGeneralItemDetailCustomerSelectedMonth($request, $year, $month)
    {
        $deliveryOrders = DB::table('delivery_order_general_details')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', 'delivery_order_general_details.delivery_order_general_id')
            ->leftJoin('items', 'items.id', 'delivery_order_general_details.item_id')
            ->leftJoin('sale_order_general_details', 'sale_order_general_details.id', 'delivery_order_general_details.sale_order_general_detail_id')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'delivery_order_generals.sale_order_general_id')
            ->leftJoin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->when($request->ware_house_id, fn($q) => $q->where('delivery_order_generals.ware_house_id', $request->ware_house_id))
            ->when($request->item_id, fn($q) => $q->where('delivery_order_general_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_order_generals.customer_id', $request->customer_id))
            ->whereNotIn('delivery_order_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_order_generals.branch_id', get_current_branch()->id);
            })
            ->whereYear('delivery_order_generals.date', $year)
            ->whereMonth('delivery_order_generals.date', $month)
            ->whereNull('sale_order_generals.deleted_at')
            ->whereNull('delivery_order_generals.deleted_at')
            ->distinct('delivery_order_general_details.id')
            ->selectRaw('

                delivery_order_general_details.id as delivery_order_general_detail_id,

                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,

                delivery_order_general_details.quantity as quantity,
                delivery_order_general_details.quantity_received as quantity_received,

                sale_order_general_details.price as price,
                sale_order_generals.exchange_rate as exchange_rate
            ')
            ->get();

        $invoiceReturns = DB::table('invoice_return_details')
            ->leftJoin('invoice_returns', 'invoice_returns.id', 'invoice_return_details.invoice_return_id')
            ->leftJoin('items', 'items.id', 'invoice_return_details.item_id')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'invoice_returns.id')
            ->leftJoin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->where('invoice_returns.reference_model', \App\Models\DeliveryOrderGeneral::class)
            ->where('invoice_return_details.reference_model', \App\Models\DeliveryOrderGeneralDetail::class)
            ->whereNull('invoice_returns.deleted_at')
            ->where('invoice_returns.status', 'approve')
            ->where('invoice_returns.type', 'general')
            ->whereIn('invoice_return_details.reference_id', $deliveryOrders->pluck('delivery_order_general_detail_id')->toArray())
            ->selectRaw('
                invoice_return_details.reference_id as delivery_order_general_detail_id,

                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,

                invoice_return_details.return_qty as quantity,
                invoice_return_details.price as price,

                invoice_returns.exchange_rate as exchange_rate
            ')
            ->get();

        return compact('deliveryOrders', 'invoiceReturns');
    }

    /**
     */
    private function monthlySaleOrderGeneralItemDetailCustomerThisYear($request, $year, $month)
    {
        $deliveryOrders = DB::table('delivery_order_general_details')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', 'delivery_order_general_details.delivery_order_general_id')
            ->leftJoin('items', 'items.id', 'delivery_order_general_details.item_id')
            ->leftJoin('sale_order_general_details', 'sale_order_general_details.id', 'delivery_order_general_details.sale_order_general_detail_id')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'delivery_order_generals.sale_order_general_id')
            ->leftJoin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->when($request->ware_house_id, fn($q) => $q->where('delivery_order_generals.ware_house_id', $request->ware_house_id))
            ->when($request->item_id, fn($q) => $q->where('delivery_order_general_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_order_generals.customer_id', $request->customer_id))
            ->whereNotIn('delivery_order_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_order_generals.branch_id', get_current_branch()->id);
            })
            ->whereYear('delivery_order_generals.date', $year)
            ->whereMonth('delivery_order_generals.date', '<=', $month)
            ->whereNull('sale_order_generals.deleted_at')
            ->whereNull('delivery_order_generals.deleted_at')
            ->distinct('delivery_order_general_details.id')
            ->selectRaw('

                delivery_order_general_details.id as delivery_order_general_detail_id,

                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,

                delivery_order_general_details.quantity as quantity,
                delivery_order_general_details.quantity_received as quantity_received,

                sale_order_general_details.price as price,
                sale_order_generals.exchange_rate as exchange_rate
            ')
            ->get();

        $invoiceReturns = DB::table('invoice_return_details')
            ->leftJoin('invoice_returns', 'invoice_returns.id', 'invoice_return_details.invoice_return_id')
            ->leftJoin('items', 'items.id', 'invoice_return_details.item_id')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'invoice_returns.id')
            ->leftJoin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->where('invoice_returns.reference_model', \App\Models\DeliveryOrderGeneral::class)
            ->where('invoice_return_details.reference_model', \App\Models\DeliveryOrderGeneralDetail::class)
            ->whereNull('invoice_returns.deleted_at')
            ->where('invoice_returns.status', 'approve')
            ->where('invoice_returns.type', 'general')
            ->whereIn('invoice_return_details.reference_id', $deliveryOrders->pluck('delivery_order_general_detail_id')->toArray())
            ->selectRaw('
                invoice_return_details.reference_id as delivery_order_general_detail_id,

                customers.id as customer_id,
                customers.nama as customer_name,

                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,

                invoice_return_details.return_qty as quantity,
                invoice_return_details.price as price,

                invoice_returns.exchange_rate as exchange_rate
            ')
            ->get();

        return compact('deliveryOrders', 'invoiceReturns');
    }

    /**
     */
    private function processResulMmonthlySaleOrderGeneralItemDetailCustomer(array $data)
    {
        list(
            $previous_year_data,
            $selected_month_data,
            $selected_year_data
        ) = $data;

        // * explode the datas to specific variables
        $deliveryOrderPreviousYears = $previous_year_data['deliveryOrders'];
        $invoiceReturnPreviousYears = $previous_year_data['invoiceReturns'];
        $deliveryOrderSelectedMonths = $selected_month_data['deliveryOrders'];
        $invoiceReturnSelectedMonths = $selected_month_data['invoiceReturns'];
        $deliveryOrderThisYear = $selected_year_data['deliveryOrders'];
        $invoiceReturnThisYear = $selected_year_data['invoiceReturns'];

        $selectedMonthCustomerIds = $deliveryOrderSelectedMonths->pluck('customer_id')->unique();

        $results = $selectedMonthCustomerIds->map(function ($customer_id) use ($deliveryOrderPreviousYears, $invoiceReturnPreviousYears, $deliveryOrderSelectedMonths, $invoiceReturnSelectedMonths, $deliveryOrderThisYear, $invoiceReturnThisYear) {
            $itemIds = $deliveryOrderSelectedMonths->where('customer_id', $customer_id)->pluck('item_id')->unique();

            return $itemIds->map(function ($item_id) use ($deliveryOrderPreviousYears, $invoiceReturnPreviousYears, $deliveryOrderSelectedMonths, $invoiceReturnSelectedMonths, $deliveryOrderThisYear, $invoiceReturnThisYear, $customer_id) {
                $dataDeliveryOrderPreviousYears = $deliveryOrderPreviousYears->where('item_id', $item_id)->where('customer_id', $customer_id);
                $dataDeliveryOrderSelectedMonth = $deliveryOrderSelectedMonths->where('item_id', $item_id)->where('customer_id', $customer_id);
                $dataDeliveryOrderThisYear = $deliveryOrderThisYear->where('item_id', $item_id)->where('customer_id', $customer_id);

                $customer_name = $deliveryOrderSelectedMonths->where('item_id', $item_id)->where('customer_id', $customer_id)->first()?->customer_name;
                $item_name = $deliveryOrderSelectedMonths->where('item_id', $item_id)->where('customer_id', $customer_id)->first()?->item_name;
                $item_code = $deliveryOrderSelectedMonths->where('item_id', $item_id)->where('customer_id', $customer_id)->first()?->item_code;

                $quantity_last_year = 0;
                $sub_total_last_year = 0;

                $quantity_selected_month = 0;
                $sub_total_selected_month = 0;

                $quantity_this_year = 0;
                $sub_total_this_year = 0;

                $dataDeliveryOrderPreviousYears->map(function ($data) use (&$quantity_last_year, &$sub_total_last_year, $invoiceReturnPreviousYears) {
                    $price = $data->price;
                    $quantity = $data->quantity;

                    if (in_array($data->delivery_order_general_detail_id, $invoiceReturnPreviousYears->pluck('delivery_order_general_detail_id')->toArray())) {
                        $quantity -= $invoiceReturnPreviousYears->where('delivery_order_general_detail_id', $data->delivery_order_general_detail_id)->sum('return_qty');
                    }

                    $quantity_last_year += $quantity;
                    $sub_total_last_year += $price * $quantity;
                });

                $dataDeliveryOrderSelectedMonth->map(function ($data) use (&$quantity_selected_month, &$sub_total_selected_month, $invoiceReturnSelectedMonths) {
                    $price = $data->price;
                    $quantity = $data->quantity;

                    if (in_array($data->delivery_order_general_detail_id, $invoiceReturnSelectedMonths->pluck('delivery_order_general_detail_id')->toArray())) {
                        $quantity -= $invoiceReturnSelectedMonths->where('delivery_order_general_detail_id', $data->delivery_order_general_detail_id)->sum('return_qty');
                    }

                    $quantity_selected_month += $quantity;
                    $sub_total_selected_month += $price * $quantity;
                });

                $dataDeliveryOrderThisYear->map(function ($data) use (&$quantity_this_year, &$sub_total_this_year, $invoiceReturnThisYear) {
                    $price = $data->price;
                    $quantity = $data->quantity;

                    if (in_array($data->delivery_order_general_detail_id, $invoiceReturnThisYear->pluck('delivery_order_general_detail_id')->toArray())) {
                        $quantity -= $invoiceReturnThisYear->where('delivery_order_general_detail_id', $data->delivery_order_general_detail_id)->sum('return_qty');
                    }

                    $quantity_this_year += $quantity;
                    $sub_total_this_year += $price * $quantity;
                });

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
        $results->map(function ($results) use (&$final_results) {
            $results->map(function ($item) use (&$final_results) {
                $final_results[] = $item;
            });
        });

        return collect($final_results);
    }

    /**
     *
     */
    private function monthlySaleOrderGeneralItemDetailCustomer($request)
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
        $previous_year_data = $this->monthlySaleOrderGeneralItemDetailCustomerLastYear($request, $previous_year);
        $selected_month_data = $this->monthlySaleOrderGeneralItemDetailCustomerSelectedMonth($request, $selected_year, $selected_month);
        $selected_year_data = $this->monthlySaleOrderGeneralItemDetailCustomerThisYear($request, $selected_year, $selected_month);

        // ! PROCESS RESULTS
        $results = $this->processResulMmonthlySaleOrderGeneralItemDetailCustomer([
            $previous_year_data,
            $selected_month_data,
            $selected_year_data
        ]);

        return [
            'data' => $results,
            'title' => 'LAPORAN BULANAN RINCIAN ITEM PENJUALAN TRADING PER CUSTOMER',
            "periode" => $request->month,
            "type" => "daily-sale-order-general-item-detail-customer",
        ];
    }

    /**
     *
     */
    private function saleOrderGeneralOutstandingReport($request)
    {
        $models = DB::table('sale_order_general_details')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'sale_order_general_details.sale_order_general_id')
            ->leftJOin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->leftJoin('items', 'items.id', 'sale_order_general_details.item_id')
            ->when($request->item_id, fn($q) => $q->where('sale_order_general_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_order_generals.customer_id', $request->customer_id))
            ->whereNotIn('sale_order_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_order_generals.branch_id', get_current_branch()->id);
            })
            ->when($request->from_date, fn($q) => $q->whereDate('sale_order_generals.tanggal', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('sale_order_generals.tanggal', '<=', Carbon::parse($request->to_date)))
            ->whereNull('sale_order_generals.deleted_at')
            ->where('sale_order_general_details.amount', '>=', DB::raw('sale_order_general_details.sended'))
            ->distinct('delivery_order_general_details.id')
            ->selectRaw('
                sale_order_generals.kode,
                sale_order_generals.tanggal,
                sale_order_generals.no_po_external,
                customers.nama as customer_name,
                items.kode as item_code,
                items.nama as item_name,
                sale_order_general_details.amount,
                sale_order_general_details.price,
                sale_order_general_details.sended,

                (sale_order_general_details.amount - sale_order_general_details.sended) as outstanding,
                (sale_order_general_details.amount - sale_order_general_details.sended) * sale_order_general_details.price as outstanding_idr

            ')
            ->get();

        return [
            'data' => $models,
            'title' => 'LAPORAN OUTSTANDING SO GENERAL ',
            "type" => "sale-order-general-outstanding",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     *
     */
    private function stockComparisonWithSaleOrderGeneral($request)
    {
        $models = DB::table('sale_order_general_details')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'sale_order_general_details.sale_order_general_id')
            ->leftJOin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->leftJoin('items', 'items.id', 'sale_order_general_details.item_id')
            ->when($request->item_id, fn($q) => $q->where('sale_order_general_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_order_generals.customer_id', $request->customer_id))

            ->whereNotIn('sale_order_generals.status', ['pending', 'revert', 'reject', 'void'])
            ->when($request->from_date, fn($q) => $q->whereDate('sale_order_generals.tanggal', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('sale_order_generals.tanggal', '<=', Carbon::parse($request->to_date)))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_order_generals.branch_id', get_current_branch()->id);
            })
            ->whereNull('sale_order_generals.deleted_at')
            ->where('sale_order_general_details.amount', '>=', DB::raw('sale_order_general_details.sended'))
            ->distinct('delivery_order_general_details.id')
            ->selectRaw('
                sale_order_generals.kode,
                sale_order_generals.tanggal,
                customers.nama as customer_name,
                items.id as item_id,
                items.kode as item_code,
                items.nama as item_name,
                sale_order_general_details.amount,
                sale_order_general_details.sended,

                (sale_order_general_details.amount - sale_order_general_details.sended) as outstanding
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
            'title' => 'Laporan Perbandingan Stok dengan Penjualan General',
            "type" => "sale-order-general-outstanding",
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     *
     */
    private function invoiceReturnSaleOrderGeneral($request)
    {
        $models = DB::table('invoice_return_details')
            ->leftJoin('invoice_returns', 'invoice_returns.id', 'invoice_return_details.invoice_return_id')
            ->leftJoin('items', 'items.id', 'invoice_return_details.item_id')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', 'invoice_returns.reference_id')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'delivery_order_generals.sale_order_general_id')
            ->leftJoin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->when($request->ware_house_id, fn($q) => $q->where('delivery_order_generals.ware_house_id', $request->ware_house_id))
            ->when($request->item_id, fn($q) => $q->where('delivery_order_general_details.item_id', $request->item_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_order_generals.customer_id', $request->customer_id))

            ->whereNotIn('invoice_returns.status', ['pending', 'revert', 'reject', 'void'])
            ->when($request->from_date, fn($q) => $q->whereDate('invoice_returns.date', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('invoice_returns.date', '<=', Carbon::parse($request->to_date)))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_order_generals.branch_id', get_current_branch()->id);
            })
            ->whereNull('delivery_order_generals.deleted_at')
            ->whereNull('invoice_returns.deleted_at')
            ->whereNull('sale_order_generals.deleted_at')
            ->where('invoice_returns.reference_model', \APp\Models\DeliveryOrderGeneral::class)
            ->distinct('invoice_return_details.id')
            ->selectRaw('

                sale_order_generals.tanggal as date_sale_order_general,

                invoice_returns.date date_invoice_return,
                invoice_returns.tax_number,
                invoice_returns.code,

                customers.nama as customer_name,

                items.kode as item_code,
                items.nama as item_name,

                invoice_return_details.total as total,
                invoice_returns.exchange_rate,

                case
                    when invoice_returns.exchange_rate = 1
                        then invoice_returns.total
                    else
                        invoice_returns.total * invoice_returns.exchange_rate
                end as total_local,

                invoice_returns.status
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

    /**
     *
     */
    private function saleOrderGeneralReturnDetail($request)
    {
        $parents = DB::table('invoice_returns')
            ->leftJoin('delivery_order_generals', 'delivery_order_generals.id', 'invoice_returns.reference_id')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'delivery_order_generals.sale_order_general_id')
            ->leftJoin('customers', 'customers.id', 'sale_order_generals.customer_id')
            ->leftJoin('ware_houses', 'ware_houses.id', 'delivery_order_generals.ware_house_id')
            ->when($request->ware_house_id, fn($q) => $q->where('delivery_order_generals.ware_house_id', $request->ware_house_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_order_generals.customer_id', $request->customer_id))
            ->when($request->status, fn($q) => $q->where('invoice_returns.status', $request->status))
            ->when($request->from_date, fn($q) => $q->whereDate('invoice_returns.date', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('invoice_returns.date', '<=', Carbon::parse($request->to_date)))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_order_generals.branch_id', get_current_branch()->id);
            })
            ->whereNull('delivery_order_generals.deleted_at')
            ->whereNull('invoice_returns.deleted_at')
            ->whereNull('sale_order_generals.deleted_at')
            ->where('invoice_returns.reference_model', \APp\Models\DeliveryOrderGeneral::class)
            ->distinct('invoice_returns.id')
            ->selectRaw('
                invoice_returns.id,
                invoice_returns.code,
                invoice_returns.exchange_rate,
                invoice_returns.date,

                invoice_returns.hpp_total,

                case
                    when invoice_returns.exchange_rate = 1
                        then invoice_returns.hpp_total
                    else
                        invoice_returns.hpp_total * invoice_returns.exchange_rate
                end as hpp_total_local,

                invoice_returns.subtotal,

                case
                    when invoice_returns.exchange_rate = 1
                        then invoice_returns.subtotal
                    else
                        invoice_returns.subtotal * invoice_returns.exchange_rate
                end as subtotal_local,

                invoice_returns.tax_total,

                case
                    when invoice_returns.exchange_rate = 1
                        then invoice_returns.tax_total
                    else
                        invoice_returns.tax_total * invoice_returns.exchange_rate
                end as tax_total_local,

                invoice_returns.total,

                case
                    when invoice_returns.exchange_rate = 1
                        then invoice_returns.total
                    else
                        invoice_returns.total * invoice_returns.exchange_rate
                end as total_local,

                invoice_returns.tax_number,

                customers.nama as customer_name,
                ware_houses.nama as ware_house_name
            ')
            ->get();

        $details = DB::table('invoice_return_details')
            ->leftJoin('items', 'items.id', 'invoice_return_details.item_id')
            ->leftJoin('invoice_returns', 'invoice_returns.id', 'invoice_return_details.invoice_return_id')
            ->whereIn('invoice_return_details.invoice_return_id', $parents->pluck('id')->toArray())
            ->when($request->item_id, fn($q) => $q->where('invoice_return_details.item_id', $request->item_id))
            ->selectRaw('
                invoice_return_details.invoice_return_id,

                items.kode as item_code,
                items.nama as item_name,

                invoice_return_details.qty,
                invoice_return_details.return_qty,
                invoice_return_details.price,
                invoice_return_details.subtotal,

                case
                    when invoice_returns.exchange_rate = 1
                        then invoice_return_details.subtotal
                    else
                        invoice_return_details.subtotal * invoice_returns.exchange_rate
                end as subtotal_local,

                invoice_return_details.tax_amount,

                case
                    when invoice_returns.exchange_rate = 1
                        then invoice_return_details.tax_amount
                    else
                        invoice_return_details.tax_amount * invoice_returns.exchange_rate
                end as tax_amount_local,

                invoice_return_details.total,

                case
                    when invoice_returns.exchange_rate = 1
                        then invoice_return_details.total
                    else
                        invoice_return_details.total * invoice_returns.exchange_rate
                end as total_local
            ')
            ->get();

        $results = $parents->map(function ($parent) use ($details) {
            $parent->details = $details->where('invoice_return_id', $parent->id);
            return $parent;
        });

        return [
            'data' => $results,
            'title' => 'Laporan Ringkasan Retur Penjualan General Detail',
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    /**
     *
     */
    private function debtDueSaleOrderGeneral($request)
    {
        $invoices = DB::table('invoice_generals')
            ->leftJoin('sale_order_generals', 'sale_order_generals.id', 'invoice_generals.sale_order_general_id')
            ->leftJoin('customers', 'customers.id', 'invoice_generals.customer_id')
            ->leftJoin('branches', 'branches.id', 'invoice_generals.branch_id')
            ->join('invoice_parents', function ($q) {
                $q->on('invoice_generals.id', 'invoice_parents.reference_id')
                    ->where('invoice_parents.model_reference', \App\Models\InvoiceGeneral::class);
            })
            ->when($request->active, fn($q) => $q->where('invoice_parents.lock_status', 0))
            ->when($request->customer_id, fn($q) => $q->where('sale_order_generals.customer_id', $request->customer_id))
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('sale_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('sale_order_generals.branch_id', get_current_branch()->id);
            })
            ->whereNull('invoice_generals.deleted_at')
            ->whereNotIn('invoice_generals.status', ['pending', 'revert', 'reject', 'void'])
            // ->whereIn('invoice_generals.payment_status', ['unpaid', 'partial-paid'])
            ->whereDate('invoice_generals.due_date', '<=', now()->format('Y-m-d'))
            ->selectRaw('
                invoice_generals.id as id,
                invoice_generals.code,

                customers.nama as customer_name,
                branches.name as branch_name,

                invoice_generals.date,
                invoice_generals.due_date,
                invoice_generals.exchange_rate,

                invoice_generals.total,

                case
                    when invoice_generals.exchange_rate = 1
                        then invoice_generals.total
                    else
                        invoice_generals.total * invoice_generals.exchange_rate
                end as total_local
            ')
            ->get();

        $invoicePayments = DB::table('invoice_payments')
            ->where('invoice_payments.invoice_model', \App\Models\InvoiceGeneral::class)
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

            $invoice->paid = $payments->sum('amount_to_receive');
            $invoice->paid_local = $payments->sum('amount_to_receive_local');

            return $invoice;
        });

        return [
            'data' => $results,
            'title' => 'Laporan Piutang Jatuh Tempo Penjualan General',
            "from_date" => $request->from_date,
            "to_date" => $request->to_date,
        ];
    }

    private function receivableAging(Request $request)
    {
        $to_date = $request->to_date ? Carbon::parse($request->to_date) : Carbon::now();
        $invoice_parents = DB::table('invoice_parents')
            ->whereNotIn('invoice_parents.status', ['pending', 'revert', 'reject', 'void'])
            ->leftJoin('invoice_payments', function ($q) {
                $q->on('invoice_parents.model_reference', 'invoice_payments.invoice_model')
                    ->whereColumn('invoice_parents.reference_id', 'invoice_payments.invoice_id');
            })
            ->whereNull('invoice_parents.deleted_at')
            ->whereNull('invoice_payments.deleted_at')
            ->when($request->customer_id, fn($q) => $q->where('customer_id', $request->customer_id))
            ->when($to_date, function ($query) use ($to_date) {
                $query->whereDate('invoice_payments.date', '<=', Carbon::parse($to_date));
            })
            ->when($request->type, fn($q) => $q->where('invoice_parents.type', $request->type))
            ->when($request->active, fn($q) => $q->where('invoice_parents.lock_status', 0))
            ->selectRaw('
            invoice_parents.customer_id, 
            invoice_parents.exchange_rate,
            invoice_parents.due_date,
            invoice_parents.model_reference,
            invoice_parents.reference_id,
            COALESCE(SUM(invoice_payments.amount_to_receive * invoice_parents.exchange_rate), 0) as amount_to_receive,
            COALESCE(SUM(invoice_payments.receive_amount * invoice_parents.exchange_rate), 0) as receive_amount,
            COALESCE(SUM(invoice_payments.amount_to_receive * invoice_parents.exchange_rate), 0) - COALESCE(SUM(invoice_payments.receive_amount * invoice_parents.exchange_rate), 0) as balance
        ')
            ->groupBy('invoice_parents.id')
            // ->havingRaw('balance != 0')
            ->get()
            ->map(function ($invoice) use ($to_date) {
                $invoice->is_overdue = Carbon::parse($invoice->due_date)->lte(Carbon::parse($to_date));
                $invoice->diff_day = Carbon::parse($invoice->due_date)->diffInDays(Carbon::parse($to_date));
                return $invoice;
            });

        $invoice_return_generals = DB::table('invoice_returns')
            ->whereNull('invoice_returns.deleted_at')
            ->where('invoice_returns.status', 'approve')
            ->join('currencies', 'currencies.id', 'invoice_returns.currency_id')
            ->join('customers', 'customers.id', 'invoice_returns.customer_id')
            ->join('invoice_return_details', function ($join) {
                $join->on('invoice_return_details.invoice_return_id', 'invoice_returns.id')
                    ->whereNull('invoice_return_details.deleted_at');
            })
            ->leftJoin('delivery_order_general_details', function ($join) {
                $join->on('delivery_order_general_details.id', 'invoice_return_details.reference_id')
                    ->where('invoice_return_details.reference_model', DeliveryOrderGeneralDetail::class)
                    ->whereNull('delivery_order_general_details.deleted_at');
            })
            ->leftJoin('invoice_general_details', function ($join) {
                $join->on('invoice_general_details.delivery_order_general_detail_id', 'delivery_order_general_details.id');
            })
            ->leftJoin('invoice_generals', function ($join) {
                $join->on('invoice_generals.id', 'invoice_general_details.invoice_general_id')
                    ->where('invoice_generals.status', 'approve')
                    ->whereNull('invoice_generals.deleted_at');
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_returns.customer_id', $request->customer_id);
            })
            ->when($request->currency_id, function ($query)  use ($request) {
                $query->where('invoice_returns.currency_id', $request->currency_id);
            })
            ->selectRaw(
                'invoice_returns.*,
                customers.id as customer_id,
                customers.nama as customer_nama,
                customers.code as customer_code,
                currencies.nama as currency_name,
                invoice_generals.id as invoice_general_id'
            )
            ->whereDate('invoice_returns.date', '<=', Carbon::parse($to_date))
            ->groupBy('invoice_returns.id')
            ->get();

        $invoice_parents = $invoice_parents->map(function ($inv) use ($invoice_return_generals) {
            $total_return = 0;
            if ($inv->model_reference == InvoiceGeneral::class) {
                $total_return = $invoice_return_generals->where('invoice_general_id', $inv->reference_id)
                    ->where('customer_id', $inv->customer_id)
                    ->map(function ($return) {
                        return $return->total * $return->exchange_rate;
                    })->sum();
            }

            $inv->balance -= $total_return;
            return $inv;
        });

        $customers = DB::table('customers')
            ->whereIn('id', $invoice_parents->pluck('customer_id')->toArray())
            ->select('id', 'code', 'nama')
            ->get();

        $customers = $customers->map(function ($customer) use ($invoice_parents, $to_date, $invoice_return_generals) {
            $customer->total = $invoice_parents->where('customer_id', $customer->id)->sum('balance');

            $customer->not_overdue = $invoice_parents->where('customer_id', $customer->id)->filter(function ($q) use ($to_date) {
                return $q->is_overdue === false;
            })
                ->sum('balance');

            $customer->first_group =  $invoice_parents->where('customer_id', $customer->id)->filter(function ($q) use ($to_date) {
                return $q->is_overdue === true && $q->diff_day >= 0 && $q->diff_day <= 30;
            })
                ->sum('balance');

            $return_without_invoice = $invoice_return_generals->where('customer_id', $customer->id)
                ->filter(function ($q) use ($to_date) {
                    return $q->invoice_general_id === null;
                })
                ->map(function ($return) {
                    return $return->total * $return->exchange_rate;
                })->sum();

            $customer->first_group += $return_without_invoice;

            $customer->second_group =  $invoice_parents->where('customer_id', $customer->id)->filter(function ($q) use ($to_date) {
                return $q->is_overdue === true && $q->diff_day >= 31 && $q->diff_day <= 60;
            })
                ->sum('balance');

            $customer->third_group =  $invoice_parents->where('customer_id', $customer->id)->filter(function ($q) use ($to_date) {
                return $q->is_overdue === true && $q->diff_day >= 61 && $q->diff_day <= 90;
            })
                ->sum('balance');

            $customer->fourth_group =  $invoice_parents->where('customer_id', $customer->id)->filter(function ($q) use ($to_date) {
                return $q->is_overdue === true && $q->diff_day > 90;
            })
                ->sum('balance');

            return $customer;
        });

        return [
            'data' => $customers,
            'type' => 'Laporan Umur Piutang',
            'to_date' => $request->to_date,
        ];
    }
}
