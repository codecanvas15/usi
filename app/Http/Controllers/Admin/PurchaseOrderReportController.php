<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanLPBExport;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Currency;
use App\Models\ItemReceivingReport;
use App\Models\Project;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceGeneral;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderReportController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'purchase-order-report';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.' . $this->view_folder . '.index');
    }

    public function report($type, Request $request)
    {
        $this->fill_empty_due_date();
        // GET DATA TO EXPORT
        $data = [];
        switch ($type) {
            // ! LAPORAN LPB
            case "laporan-lpb":
                $data = $this->laporan_lpb($type, $request);
                $excel_export = LaporanLPBExport::class;
                $paper_size = 'a3';
                $orientation = 'landscape';
                break;
            case "payable-aging":
                $data = $this->payableAging($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = \App\Exports\SaleOrderGeneral\debtDueSaleOrderGeneralExport::class;
                break;
            default:
                # code...
                break;
        }

        // ! PROCESS DATA WITH SELECTED FORMAT
        $view_file = 'admin.' . $this->view_folder . '.' . $type . '.';
        if ($request->folder) {
            $view_file .= $request->folder . '.';
        }
        $view_file .= $request->format;
        if ($request->format == "preview") {
            return view($view_file, $data);
        } elseif ($request->format == "pdf") {
            $pdf = Pdf::loadView($view_file, $data)
                ->setPaper($paper_size ?? 'a4', $orientation ?? 'potrait');
            return $pdf->stream($type . '.pdf');
        } else {
            return Excel::download(new $excel_export($view_file, $data), $type . '.xlsx');
        }
    }

    // ! LAPORAN LPB
    public function laporan_lpb($type, $request)
    {
        $item_receiving_reports = DB::table('item_receiving_reports')
            ->join('vendors', 'item_receiving_reports.vendor_id', '=', 'vendors.id')
            ->join('currencies', 'item_receiving_reports.currency_id', '=', 'currencies.id')
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('item_receiving_reports.date_receive', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('item_receiving_reports.date_receive', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('item_receiving_reports.vendor_id', $request->vendor_id);
            })
            ->when($request->currency_id, function ($query) use ($request) {
                return $query->where('item_receiving_reports.currency_id', $request->currency_id);
            })
            ->whereNotIn('item_receiving_reports.status', ['pending', 'revert', 'void', 'reject'])
            ->whereNull('item_receiving_reports.deleted_at')
            ->leftJoin('supplier_invoice_payments', function ($supplier_invoice_payment) use ($request) {
                $supplier_invoice_payment->on('supplier_invoice_payments.item_receiving_report_id', '=', 'item_receiving_reports.id')
                    ->whereNull('supplier_invoice_payments.deleted_at')
                    ->when($request->to_date, function ($query) use ($request) {
                        return $query->whereDate('supplier_invoice_payments.date', '<=', Carbon::parse($request->to_date));
                    });
            })
            ->orderBy('supplier_invoice_payments.date', 'asc')
            ->selectRaw(
                'vendors.nama as vendor_name,
                item_receiving_reports.id,
                item_receiving_reports.reference_model,
                item_receiving_reports.reference_id,
                item_receiving_reports.date_receive,
                item_receiving_reports.kode,
                item_receiving_reports.total,
                item_receiving_reports.exchange_rate,
                currencies.kode as currency_kode,
                supplier_invoice_payments.id as payment_id,
                supplier_invoice_payments.date as date_payment,
                supplier_invoice_payments.pay_amount as amount_payment,
                supplier_invoice_payments.created_at as created_at_payment,
                supplier_invoice_payments.model as payment_model,
                supplier_invoice_payments.reference_id as payment_model_id,
                supplier_invoice_payments.note,
                item_receiving_reports.total * COALESCE(item_receiving_reports.exchange_rate, 1) as total_rp
                ',
            )
            ->get();

        foreach ($item_receiving_reports as $key => $item_receiving_report) {
            $ap = \App\Models\AccountPayableDetail::find($item_receiving_report->payment_model_id);
            $bank_code = '';
            $bank = '';
            $po_code = '';
            $po_project = '';
            $po_project_link = '';
            $po = $item_receiving_report->reference_model::find($item_receiving_report->reference_id);
            $po_code = $po->kode ?? $po->code ??  $po->nomor_po ?? '';
            $po_project = Project::find($po->project_id)->name ?? '';
            $po_project_link = isset($po->project_id) ? route('admin.project.show', $po->project_id) : null;
            if ($ap) {
                $bank_code = $ap->account_payable->bank_code_mutation ?? '';
                $bank = $ap->account_payable->coa->name ?? '';
            }
            $item_receiving_report->bank_code = $bank_code;
            $item_receiving_report->bank = $bank;
            $item_receiving_report->po_code = $po_code;
            $item_receiving_report->po_project = $po_project;
            $item_receiving_report->po_project_link = $po_project_link;
            if ($item_receiving_report->id) {
                $item_receiving_report->outstanding = $item_receiving_report->total - DB::table('supplier_invoice_payments')
                    ->where('item_receiving_report_id', $item_receiving_report->id)
                    ->whereDate('date', '<=', Carbon::parse($request->to_date))
                    ->whereNull('deleted_at')
                    ->orderBy('date', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->sum('pay_amount');
            } else {
                $item_receiving_report->outstanding = 0;
            }
        }

        $return['data'] = $item_receiving_reports;
        $return['type'] = $type;
        $return['from_date'] = Carbon::parse($request->from_date);
        $return['to_date'] = Carbon::parse($request->to_date);
        $return['vendor'] = Vendor::find($request->vendor_id);
        $return['currency'] = Currency::find($request->currency_id);

        return $return;
    }

    private function  payableAging(Request $request)
    {
        $to_date = $request->to_date ? Carbon::parse($request->to_date) : Carbon::now();
        $item_receiving_reports = DB::table('item_receiving_reports')
            ->join('vendors', 'vendors.id', 'item_receiving_reports.vendor_id')
            ->whereNull('item_receiving_reports.deleted_at')
            ->whereDate('item_receiving_reports.date_receive', '<=', $to_date)
            ->whereIn('item_receiving_reports.status', ['approve', 'done', 'return-all'])
            ->leftJoin('supplier_invoice_payments', function ($query) use ($to_date) {
                $query->on('supplier_invoice_payments.item_receiving_report_id', 'item_receiving_reports.id')
                    ->whereNull('supplier_invoice_payments.deleted_at')
                    ->whereDate('supplier_invoice_payments.date', '<=', $to_date);
            })
            ->selectRaw(
                'item_receiving_reports.vendor_id,
                item_receiving_reports.id,
                item_receiving_reports.exchange_rate,
                item_receiving_reports.due_date,
                item_receiving_reports.total * item_receiving_reports.exchange_rate as amount_to_pay,
                COALESCE(SUM(supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate), 0) as pay_amount,
                (item_receiving_reports.total * item_receiving_reports.exchange_rate) - COALESCE(SUM(supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate), 0) as balance',
            )
            ->groupBy('item_receiving_reports.id')
            ->when($request->vendor_id, function ($query) use ($request) {
                $query->where('item_receiving_reports.vendor_id', $request->vendor_id);
            })
            // ->havingRaw('balance != 0')
            ->get();

        $purchase_returns = DB::table('purchase_returns')
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_returns.vendor_id', $request->vendor_id);
            })
            ->whereDate('purchase_returns.date', '<=', $to_date)
            ->where('purchase_returns.status', 'approve')
            ->whereNull('purchase_returns.deleted_at')
            ->select(
                'purchase_returns.*',
                'purchase_returns.total as balance'
            )
            ->get();

        $item_receiving_reports = $item_receiving_reports->map(function ($item_receiving_report) use ($purchase_returns) {
            $purchase_return = $purchase_returns->where('item_receiving_report_id', $item_receiving_report->id)
                ->sum(function ($pr) {
                    return $pr->total * $pr->exchange_rate;
                });
            $item_receiving_report->balance -= $purchase_return ? $purchase_return : 0;

            return $item_receiving_report;
        });

        $other_payments = DB::table('supplier_invoice_payments')
            ->join('supplier_invoices', function ($join) {
                $join->on('supplier_invoices.id', 'supplier_invoice_payments.supplier_invoice_id')
                    ->where('supplier_invoice_payments.supplier_invoice_model', SupplierInvoice::class);
            })
            ->join('vendors', 'supplier_invoices.vendor_id', 'vendors.id')
            ->whereNull('item_receiving_report_id')
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->whereDate('supplier_invoice_payments.date', '<=', $to_date)
            ->where('pay_amount', '!=', 0)
            ->when($request->vendor_id, function ($q) use ($request) {
                $q->where('vendors.id', $request->vendor_id);
            })
            ->selectRaw(
                'vendors.id as vendor_id,
                supplier_invoice_payments.exchange_rate,
                supplier_invoices.top_due_date as due_date,
                0 as amount_to_pay,
                supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate as pay_amount,
                supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate * -1 as balance'
            )
            ->get();

        $supplier_invoice_generals = DB::table('supplier_invoice_generals')
            ->join('vendors', 'vendors.id', 'supplier_invoice_generals.vendor_id')
            ->whereNull('supplier_invoice_generals.deleted_at')
            ->whereDate('supplier_invoice_generals.date', '<=', $to_date)
            ->whereIn('supplier_invoice_generals.status', ['approve'])
            ->leftJoin('supplier_invoice_payments', function ($query) use ($to_date) {
                $query->on('supplier_invoice_payments.supplier_invoice_id', 'supplier_invoice_generals.id')
                    ->where('supplier_invoice_payments.supplier_invoice_model', SupplierInvoiceGeneral::class)
                    ->whereNull('supplier_invoice_payments.deleted_at')
                    ->whereDate('supplier_invoice_payments.date', '<=', $to_date);
            })
            ->selectRaw(
                'supplier_invoice_generals.vendor_id,
                supplier_invoice_generals.id,
                supplier_invoice_generals.exchange_rate,
                supplier_invoice_generals.top_due_date as due_date,
                supplier_invoice_generals.debit * supplier_invoice_generals.exchange_rate as amount_to_pay,
                COALESCE(SUM(supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate), 0) as pay_amount,
                (supplier_invoice_generals.debit * supplier_invoice_generals.exchange_rate) - COALESCE(SUM(supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate), 0) as balance',
            )
            ->groupBy('supplier_invoice_generals.id')
            ->when($request->vendor_id, function ($query) use ($request) {
                $query->where('supplier_invoice_generals.vendor_id', $request->vendor_id);
            })
            // ->havingRaw('balance != 0')
            ->get();

        $merge_data = collect($item_receiving_reports)
            ->merge($other_payments)
            ->merge($supplier_invoice_generals)
            ->map(function ($q) {
                $is_overdue = Carbon::parse($q->due_date)->endOfDay()->lt(Carbon::now()->endOfDay());
                $diff_day = Carbon::parse($q->due_date)->endOfDay()->diffInDays(Carbon::now()->endOfDay());
                $q->is_overdue = $is_overdue;
                $q->diff_day = $diff_day;
                return $q;
            })
            ->sortBy('date');

        $vendors = DB::table('vendors')
            ->whereIn('id', $merge_data->pluck('vendor_id')->toArray())
            ->select('id', 'code', 'nama')
            ->orderBy('nama', 'asc')
            ->get();

        $vendors = $vendors->map(function ($vendor) use ($merge_data, $to_date) {
            $vendor->total = $merge_data->where('vendor_id', $vendor->id)->sum('balance');
            $vendor->not_overdue = $merge_data->where('vendor_id', $vendor->id)->filter(function ($q) use ($to_date) {
                return !$q->is_overdue;
            })
                ->sum('balance');

            $vendor->first_group =  $merge_data->where('vendor_id', $vendor->id)->filter(function ($q) use ($to_date) {
                return $q->diff_day >= 1 && $q->diff_day <= 30 && $q->is_overdue;
            })
                ->sum('balance');

            $vendor->second_group =  $merge_data->where('vendor_id', $vendor->id)->filter(function ($q) use ($to_date) {
                return $q->diff_day >= 31 && $q->diff_day <= 60 && $q->is_overdue;
            })
                ->sum('balance');

            $vendor->third_group =  $merge_data->where('vendor_id', $vendor->id)->filter(function ($q) use ($to_date) {
                return $q->diff_day >= 61 && $q->diff_day <= 90 && $q->is_overdue;
            })
                ->sum('balance');

            $vendor->fourth_group =  $merge_data->where('vendor_id', $vendor->id)->filter(function ($q) use ($to_date) {
                return $q->diff_day >= 91 && $q->is_overdue;
            })
                ->sum('balance');

            return $vendor;
        });

        return [
            'data' => $vendors,
            'type' => 'Laporan Umur Hutang',
            'to_date' => $request->to_date,
        ];
    }

    public function fill_empty_due_date()
    {
        $item_receiving_reports = ItemReceivingReport::whereNull('due_date')
            ->get();

        foreach ($item_receiving_reports as $item_receiving_report) {
            $due = $item_receiving_report->reference->top_day ?? $item_receiving_report->reference->term_of_payment_days ?? $item_receiving_report->reference->vendor->top_days ?? 30;

            DB::table('item_receiving_reports')
                ->where('id', $item_receiving_report->id)
                ->update([
                    'due_date' => Carbon::parse($item_receiving_report->date_receive)->addDays($due),
                ]);
        }

        return redirect()->back()->with('success', 'Due date berhasil diisi dengan nilai default 30 hari setelah tanggal transaksi.');
    }
}
