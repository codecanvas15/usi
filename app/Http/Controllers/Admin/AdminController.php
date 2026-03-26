<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\PoTrading;
use App\Models\SoTrading;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\InvoiceGeneral;
use App\Models\InvoicePayment;
use App\Models\InvoiceTrading;
use App\Models\Position;
use App\Models\PurchaseOrderGeneral;
use App\Models\PurchaseOrderService;
use App\Models\PurchaseRequest;
use App\Models\PurchaseTransport;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceGeneral;
use App\Models\SupplierInvoicePayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $countGender = json_encode([Employee::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where('jenis_kelamin', 'Laki-Laki')->count(), Employee::where('branch_id', get_current_branch_id())->where('jenis_kelamin', 'Perempuan')->count()]);
        $employementStatus = EmploymentStatus::all();
        $posisi = Position::all();

        $countEmpStats = [];
        foreach ($employementStatus as $value) {
            array_push($countEmpStats, ['name' => $value->name, 'data' => [Employee::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where('employment_status_id', $value->id)->count()]]);
        }
        $countEmpStats = json_encode($countEmpStats);

        $countPosisi = [];
        $countPosisiName = [];
        foreach ($posisi as $value) {
            array_push($countPosisi, Employee::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where('position_id', $value->id)->count());
            array_push($countPosisiName, $value->nama);
        }
        $countPosisi = json_encode($countPosisi);
        $countPosisiName = json_encode($countPosisiName);

        return view('admin.dashboard.index', compact('countGender', 'countEmpStats', 'countPosisi', 'countPosisiName'));
    }

    /**
     * get data main dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function get_data_dashboard()
    {
        $user = thousands_currency_format(User::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->get()->count());
        $customer = thousands_currency_format(Customer::all()->count());
        $item = thousands_currency_format(Item::all()->count());

        $so_this_month_count = thousands_currency_format(SoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get()->count());
        $so_total = thousands_currency_format(SoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->get()->count());
        $latest_so = SoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->with(['customer'])->orderByDesc('created_at')->limit(5)->get();

        $po_this_month_count = thousands_currency_format(PoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get()->count());
        $po_total = thousands_currency_format(PoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->get()->count());
        $latest_po = PoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->with(['customer'])->orderByDesc('created_at')->limit(5)->get();

        return $this->ResponseJsonData(compact('user', 'customer', 'item', 'so_this_month_count', 'so_total', 'latest_so', 'po_this_month_count', 'po_total', 'latest_po'));
    }

    /**
     * get_purchase_dashboard_data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_purchase_dashboard_data()
    {
        // purchase request
        $purchase_request = [
            'all_count' => PurchaseRequest::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->get()->count(),
            'this_month_count' => PurchaseRequest::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get()->count(),
            'waiting_approval' => PurchaseRequest::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where(function ($query) {
                $query->orWhere('status', 'pending');
                $query->orWhere('status', 'revert');
            })->get()->count(),
            'latest' => PurchaseRequest::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->orderByDesc('created_at')->limit(5)->get(),
        ];

        // purchase trading
        $purchase_trading = [
            'all_count' => PoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->get()->count(),
            'this_month_count' => PoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get()->count(),
            'waiting_approval' => PoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where(function ($query) {
                $query->orWhere('status', 'pending');
                $query->orWhere('status', 'revert');
            })->get()->count(),
            'latest' => PoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->orderByDesc('created_at')->limit(5)->get(),
        ];

        // purchase general
        $purchase_general = [
            'all_count' => PurchaseOrderGeneral::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->get()->count(),
            'this_month_count' => PurchaseOrderGeneral::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get()->count(),
            'waiting_approval' => PurchaseOrderGeneral::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where(function ($query) {
                $query->orWhere('status', 'pending');
                $query->orWhere('status', 'revert');
            })->get()->count(),
            'latest' => PurchaseOrderGeneral::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->orderByDesc('created_at')->limit(5)->get(),
        ];

        // purchase service
        $purchase_service = [
            'all_count' => PurchaseOrderService::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->get()->count(),
            'this_month_count' => PurchaseOrderService::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get()->count(),
            'waiting_approval' => PurchaseOrderService::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where(function ($query) {
                $query->orWhere('status', 'pending');
                $query->orWhere('status', 'revert');
            })->get()->count(),
            'latest' => PurchaseOrderService::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->orderByDesc('created_at')->limit(5)->get(),
        ];

        // purchase transport
        $purchase_transport = [
            'all_count' => PurchaseTransport::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->get()->count(),
            'this_month_count' => PurchaseTransport::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get()->count(),
            'waiting_approval' => PurchaseTransport::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where(function ($query) {
                $query->orWhere('status', 'pending');
                $query->orWhere('status', 'revert');
            })->get()->count(),
            'latest' => PurchaseTransport::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->orderByDesc('created_at')->limit(5)->get(),
        ];

        // spending purchase
        $spending_purchase_this_month = [
            'general' => PurchaseOrderGeneral::select(DB::raw('sum(case when exchange_rate = 1 then total else total * exchange_rate end) as final_total'))->when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->first()->final_total,
            'service' => PurchaseOrderService::select(DB::raw('sum(case when exchange_rate = 1 then total else total * exchange_rate end) as final_total'))->when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->first()->final_total,
            'trading' => PoTrading::select(DB::raw('sum(case when exchange_rate = 1 then total else total * exchange_rate end) as final_total'))->when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->first()->final_total,
            'transport' => PurchaseTransport::select(DB::raw('sum(case when exchange_rate = 1 then total else total * exchange_rate end) as final_total'))->when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->first()->final_total,
        ];

        // spending purchase
        $spending_purchases = [];
        for ($i = 1; $i <= 12; $i++) {
            $spending_purchases['general'][] = [
                'month' => $i,
                'data' => PurchaseOrderGeneral::select(DB::raw('sum(case when exchange_rate = 1 then total else total * exchange_rate end) as final_total'))
                    ->when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
                    ->whereYear('created_at', date('Y'))
                    ->whereMonth('created_at', $i)
                    ->groupBy(DB::raw('Month(created_at)'))
                    ->first()?->final_total ?? 0,

            ];
            $spending_purchases['service'][] = [
                'month' => $i,
                'data' => PurchaseOrderService::select(DB::raw('sum(case when exchange_rate = 1 then total else total * exchange_rate end) as final_total'))
                    ->when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
                    ->whereYear('created_at', date('Y'))
                    ->whereMonth('created_at', $i)
                    ->groupBy(DB::raw('Month(created_at)'))
                    ->first()?->final_total ?? 0,

            ];
            $spending_purchases['trading'][] = [
                'month' => $i,
                'data' => PoTrading::select(DB::raw('sum(case when exchange_rate = 1 then total else total * exchange_rate end) as final_total'))
                    ->when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
                    ->whereYear('created_at', date('Y'))
                    ->whereMonth('created_at', $i)
                    ->groupBy(DB::raw('Month(created_at)'))
                    ->first()?->final_total ?? 0,

            ];
            $spending_purchases['transport'][] = [
                'month' => $i,
                'data' => PurchaseTransport::select(DB::raw('sum(case when exchange_rate = 1 then total else total * exchange_rate end) as final_total'))
                    ->when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
                    ->whereYear('created_at', date('Y'))
                    ->whereMonth('created_at', $i)
                    ->groupBy(DB::raw('Month(created_at)'))
                    ->first()?->final_total ?? 0,

            ];
        }

        return $this->ResponseJsonData(compact('purchase_request', 'purchase_trading', 'purchase_general', 'purchase_service', 'purchase_transport', 'spending_purchase_this_month', 'spending_purchases'));
    }

    /**
     * get sales order dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function get_sales_order_dashboard()
    {
        $sales = [
            'this_month' => SoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get()->count(),
            'waiting_approval' => SoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where('status', 'pending')->orWhere('status', 'revert')->get()->count(),
        ];

        $spending_sales = [];
        for ($i = 1; $i <= 12; $i++) {
            $spending_sales[] = SoTrading::select(DB::raw('sum(case when exchange_rate = 1 then total else total * exchange_rate end) as final_total'))
                ->when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $i)
                ->groupBy(DB::raw('Month(created_at)'))
                ->first()?->final_total ?? 0;
        }

        $customer = Customer::all()->count();
        $sales_orders = SoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->orderByDesc('created_at')->with(['customer'])->limit(5)->get();

        return $this->ResponseJsonData(compact('sales', 'spending_sales', 'customer', 'sales_orders'));
    }

    /**
     * get_warehouse_dashboard
     *
     * @return mixed
     */
    public function get_warehouse_dashboard()
    {
        $stockThisMonth = \App\Models\StockMutation::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->get();

        // * stock in and stock out
        $stockIn = $stockThisMonth->sum('in');
        $stockOut = $stockThisMonth->sum('Out');

        // * Item receiving report
        $itemReceivingReportThisMonth = \App\Models\ItemReceivingReport::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->whereNotIn('status', ['reject', 'revert', 'pending'])
            ->count();

        $itemReceivingReportThisMonthWaitingApproval = \App\Models\ItemReceivingReport::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->whereIn('status', ['pending', 'revert'])
            ->count();

        // item receiving report waiting approval
        $itemReceivingReportWaitingApproval = \App\Models\ItemReceivingReport::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
            ->whereIn('status', ['pending', 'revert'])
            // ->whereMonth('created_at', date('m'))
            // ->whereYear('created_at', date('Y'))
            ->limit(5)
            ->get();

        // * stock usage
        $stockUsageThisMonth = \App\Models\StockUsage::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->whereNotIn('status', ['reject', 'revert', 'pending'])
            ->count();

        // * stock opname
        $stockOpnameThisMonth = \App\Models\StockOpname::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->whereNotIn('status', ['reject', 'revert', 'pending'])
            ->count();

        return $this->ResponseJsonData(compact('stockIn', 'stockOut', 'itemReceivingReportThisMonth', 'itemReceivingReportThisMonthWaitingApproval', 'itemReceivingReportWaitingApproval', 'stockUsageThisMonth', 'stockOpnameThisMonth'));
    }

    /**
     * Finance accounting dashboard data
     */
    public function financeDashboard()
    {
        # Dashboard Finance ==================================================================================================

        $limitSubmission = 5;
        if (\request()->get('submission')) {
            $limitSubmission = $limitSubmission + 5;
        }

        $limitSubmissionDisbursements = 5;
        if (\request()->get('submissionDisbursements')) {
            $limitSubmissionDisbursements = $limitSubmissionDisbursements + 5;
        }


        // Get Year and Month
        if (request()->get('date')) {
            $ym = request()->get('date');
        } else {
            $ym = date('Y-m');
        }

        // String params convert to timestamp
        $timestamp = strtotime($ym, '-01');

        // List pengajuan Pembayaran
        $fundSubmissions = \App\Models\FundSubmission::whereYear('date', date('Y', $timestamp))
            ->whereMonth('date', date('m', $timestamp))
            ->when(!get_current_branch(), fn($q) => $q->where('fund_submissions.branch_id', get_current_branch_id()))
            ->where('status', 'approve')
            ->where('is_used', false)
            ->limit($limitSubmission)
            ->orderByDesc('created_at')
            ->selectRaw('
                fund_submissions.id,
                fund_submissions.code,
                fund_submissions.date,
                fund_submissions.to_name,
                fund_submissions.item
            ')
            ->get();

        // List Pencairan
        $fundSubmissionDisbursements = \App\Models\FundSubmission::whereYear('date', date('Y', $timestamp))
            ->whereMonth('date', date('m', $timestamp))
            ->when(!get_current_branch(), fn($q) => $q->where('fund_submissions.branch_id', get_current_branch_id()))
            ->where('status', 'approve')
            ->where('is_used', true)
            ->limit($limitSubmissionDisbursements)
            ->orderByDesc('created_at')
            ->selectRaw('
                fund_submissions.id,
                fund_submissions.code,
                fund_submissions.date,
                fund_submissions.to_name,
                fund_submissions.item
            ')
            ->get();

        // Total Pembayaran Disetujui,
        $amountFundSubmissionApprove = \App\Models\FundSubmission::whereYear('date', date('Y', $timestamp))
            ->whereMonth('date', date('m', $timestamp))
            ->when(!get_current_branch(), fn($q) => $q->where('fund_submissions.branch_id', get_current_branch_id()))
            ->where('status', 'approve')
            ->count();

        // Total Pembayaran DiTolak,
        $amountFundSubmissionReject = \App\Models\FundSubmission::whereYear('date', date('Y', $timestamp))
            ->whereMonth('date', date('m', $timestamp))
            ->when(!get_current_branch(), fn($q) => $q->where('fund_submissions.branch_id', get_current_branch_id()))
            ->where('status', 'approve')
            ->count();

        // Total Pencairan (Rp .)
        $fundSubmissionsAmount = \App\Models\FundSubmission::whereYear('date', date('Y', $timestamp))
            ->whereMonth('date', date('m', $timestamp))
            ->when(!get_current_branch(), fn($q) => $q->where('fund_submissions.branch_id', get_current_branch_id()))
            ->where('status', 'approve')
            ->selectRaw('
                sum(total * exchange_rate) as total
            ')
            ->get()[0]?->total ?? 0;

        # End Dashboard Finance ==================================================================================================



        return $this->ResponseJsonData(compact('fundSubmissions', 'fundSubmissionDisbursements', 'amountFundSubmissionApprove', 'amountFundSubmissionReject', 'fundSubmissionsAmount',));
    }

    public function accountingDashboard()
    {
        # Dashboard Accounting ==================================================================================================
        // Get Year and Month
        if (request()->get('date')) {
            $ym = request()->get('date');
        } else {
            $ym = date('Y-m');
        }

        // String params convert to timestamp
        $timestamp = strtotime($ym, '-01');


        // Total Pendapatan (Rp . ),
        $amountIncome = 0;
        $incomeCoas = \App\Models\Coa::where('account_type', 'Cash & Bank')->get();
        $journalDetails = \App\Models\JournalDetail::leftJoin('journals', 'journals.id', '=', 'journal_details.journal_id')
            ->when(!get_current_branch(), fn($q) => $q->where('journals.branch_id', get_current_branch_id()))
            ->whereIn('coa_id', $incomeCoas->pluck('id'))
            ->whereYear('journals.date', date('Y', $timestamp))
            ->whereMonth('journals.date', date('m', $timestamp))
            ->select('journal_details.*')
            ->get();
        $amountIncome = $journalDetails->sum('debit_exchanged');

        // Total Piutang (Rp . ),
        $receivableAmount = 0;
        $invoicePayments = \App\Models\InvoicePayment::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m', $timestamp))
            ->whereYear('created_at', date('Y', $timestamp))
            ->get();
        $receivableAmount = $invoicePayments->sum('amount_to_receive') - $invoicePayments->sum('receive_amount');

        // Total Persediaan (Rp . ),
        $stockAmount = 0;
        $stockCoas =  \App\Models\Coa::where('account_type', 'Inventory')->get();
        $journalDetails = \App\Models\JournalDetail::leftJoin('journals', 'journals.id', '=', 'journal_details.journal_id')
            ->when(!get_current_branch(), fn($q) => $q->where('journals.branch_id', get_current_branch_id()))
            ->where('journals.status', 'approve')
            ->whereIn('coa_id', $stockCoas->pluck('id'))
            ->whereYear('journals.date', date('Y', $timestamp))
            ->whereMonth('journals.date', date('m', $timestamp))
            ->select('journal_details.*')
            ->get();

        $stockAmount = $journalDetails->sum('debit_exchanged') - $journalDetails->sum('credit_exchanged');

        // Total Pengeluaran (Rp . ),
        $amountExpense = 0;
        $expenseCoas = \App\Models\Coa::where('account_type', 'Cash & Bank')->get();
        $journalDetails = \App\Models\JournalDetail::leftJoin('journals', 'journals.id', '=', 'journal_details.journal_id')
            ->when(!get_current_branch(), fn($q) => $q->where('journals.branch_id', get_current_branch_id()))
            ->whereIn('coa_id', $expenseCoas->pluck('id'))
            ->where('journals.status', 'approve')
            ->whereYear('journals.date', date('Y', $timestamp))
            ->whereMonth('journals.date', date('m', $timestamp))
            ->select('journal_details.*')
            ->get();
        $amountExpense = $journalDetails->sum('credit_exchanged');

        $purchaseChartData = [];
        // purchase trading
        $purchase_trading = [
            'count_on_month' => PoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
                ->whereYear('tanggal', date('Y', $timestamp))
                ->whereMonth('tanggal', date('m', $timestamp))
                ->get()
                ->count(),
            'waiting_approval' => PoTrading::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where(function ($query) {
                $query->orWhere('status', 'pending');
                $query->orWhere('status', 'revert');
            })->whereYear('tanggal', date('Y', $timestamp))
                ->whereMonth('tanggal', date('m', $timestamp))
                ->get()
                ->count(),
        ];


        // purchase general
        $purchase_general = [
            'count_on_month' => PurchaseOrderGeneral::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
                ->whereYear('date', date('Y', $timestamp))
                ->whereMonth('date', date('m', $timestamp))
                ->get()
                ->count(),

            'waiting_approval' => PurchaseOrderGeneral::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where(function ($query) {
                $query->orWhere('status', 'pending');
                $query->orWhere('status', 'revert');
            })
                ->whereYear('date', date('Y', $timestamp))
                ->whereMonth('date', date('m', $timestamp))
                ->get()
                ->count(),
        ];

        // purchase service
        $purchase_service = [
            'count_on_month' => PurchaseOrderService::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
                ->whereYear('date', date('Y', $timestamp))
                ->whereMonth('date', date('m', $timestamp))
                ->get()
                ->count(),

            'waiting_approval' => PurchaseOrderService::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where(function ($query) {
                $query->orWhere('status', 'pending');
                $query->orWhere('status', 'revert');
            })
                ->whereYear('date', date('Y', $timestamp))
                ->whereMonth('date', date('m', $timestamp))
                ->get()
                ->count(),
        ];

        // purchase transport
        $purchase_transport = [
            'count_on_month' => PurchaseTransport::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))
                ->whereYear('created_at', date('Y', $timestamp))
                ->whereMonth('created_at', date('m', $timestamp))
                ->get()
                ->count(),

            'waiting_approval' => PurchaseTransport::when(!get_current_branch(), fn($q) => $q->where('branch_id', get_current_branch_id()))->where(function ($query) {
                $query->orWhere('status', 'pending');
                $query->orWhere('status', 'revert');
            })
                ->whereYear('created_at', date('Y', $timestamp))
                ->whereMonth('created_at', date('m', $timestamp))
                ->get()
                ->count(),
        ];

        $purchaseChartData['general'] = $purchase_general;
        $purchaseChartData['service'] = $purchase_service;
        $purchaseChartData['trading'] = $purchase_trading;
        $purchaseChartData['transport'] = $purchase_transport;

        // ?????????????????????????????????????????
        // -Grafik Pembelian
        // 	General, Service, trading, transport

        # / End Dashboard Accounting ==================================================================================================

        return $this->ResponseJsonData(compact('purchaseChartData', 'amountIncome', 'receivableAmount', 'stockAmount', 'amountExpense'));
    }

    public function getInvoiceMoreThenDueDate(Request $request)
    {
        $invoiceTrading = InvoiceTrading::whereDate('due_date', '<', \Carbon\Carbon::now())->limit($request->limit_trd ?? 5)->get();
        $invoiceGeneral = InvoiceGeneral::whereDate('due_date', '<', \Carbon\Carbon::now())->limit($request->limit_gnr ?? 5)->get();
        $supplierInvoice = SupplierInvoice::whereDate('top_due_date', '<', \Carbon\Carbon::now())->limit($request->limit_supp ?? 5)->get();
        $supplierInvoiceGeneral = SupplierInvoiceGeneral::whereDate('top_due_date', '<', \Carbon\Carbon::now())->limit($request->limit_supp_gnr ?? 5)->get();

        if ($supplierInvoice->count() > 0) {
            foreach ($supplierInvoice as $key => $supplier_inv) {
                $supplier_inv->children = $supplier_inv->supplier_invoice_payment()->toArray();
            }
        }

        if ($supplierInvoiceGeneral->count() > 0) {
            foreach ($supplierInvoiceGeneral as $key => $si_general) {
                $si_general->children = $si_general->supplier_invoice_payment()->toArray();
            }
        }

        return $this->ResponseJsonData([
            'customer' => [
                'invoice_trading' => $invoiceTrading,
                'invoice_general' => $invoiceGeneral,
            ],
            'supplier' => [
                'supplier_invoice' => $supplierInvoice,
                'supplier_invoice_general' => $supplierInvoiceGeneral,
            ]
        ]);
    }

    /**
     * Get human resource legal documents dashboard data for human resource dashboard.
     *
     * @return \Illuminate\Response\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     * @throws \Throwable
     * @throws \QueryException
     */
    public function getHumanResourceLegalDocument()
    {
        // * asset_documents
        // total dokumen / warning dokumen / dokumen expired
        $allAssetDocuments = \App\Models\AssetDocument::count();
        $warningAssetDocuments = \App\Models\AssetDocument::whereDate('end_date', '<', DB::raw('effective_date'))
            ->whereRaw('DATEDIFF(end_date, effective_date) > due_date')
            ->count();
        $expiredAssetDocuments = \App\Models\AssetDocument::whereDate('end_date', '<', \Carbon\Carbon::now())->count();

        // fleet_documents
        // total dokumen / warning dokumen / dokumen expired
        $allFleetDocuments = \App\Models\FleetDocument::count();
        $warningFleetDocuments = \App\Models\FleetDocument::whereDate('end_date', '<', DB::raw('effective_date'))
            ->whereRaw('DATEDIFF(end_date, effective_date) > due_date')
            ->count();
        $expiredFleetDocuments = \App\Models\FleetDocument::whereDate('end_date', '<', \Carbon\Carbon::now())->count();

        // * lease_documents
        // total dokumen / warning dokumen / dokumen expired
        $allLeaseDocuments = \App\Models\LeaseDocument::count();
        $warningLeaseDocuments = \App\Models\LeaseDocument::whereDate('end_date', '<', DB::raw('effective_date'))
            ->whereRaw('DATEDIFF(end_date, effective_date) > due_date')
            ->count();
        $expiredLeaseDocuments = \App\Models\LeaseDocument::whereDate('end_date', '<', \Carbon\Carbon::now())->count();

        // * legallity_document
        //      finance document
        //          total dokumen / warning dokumen / dokumen expired
        $allFinanceDocuments = \App\Models\LegalityDocument::where('type', 'finance')->count();
        $warningFinanceDocuments = \App\Models\LegalityDocument::where('type', 'finance')
            ->whereDate('end_date', '<', DB::raw('effective_date'))
            ->whereRaw('DATEDIFF(end_date, effective_date) > due_date')
            ->count();
        $expiredFinanceDocuments = \App\Models\LegalityDocument::where('type', 'finance')->whereDate('end_date', '<', \Carbon\Carbon::now())->count();

        //      company document
        //          total dokumen / warning dokumen / dokumen expired
        $allCompanyDocuments = \App\Models\LegalityDocument::where('type', 'company')->count();
        $warningCompanyDocuments = \App\Models\LegalityDocument::where('type', 'company')
            ->whereDate('end_date', '<', DB::raw('effective_date'))
            ->whereRaw('DATEDIFF(end_date, effective_date) > due_date')
            ->count();
        $expiredCompanyDocuments = \App\Models\LegalityDocument::where('type', 'company')->whereDate('end_date', '<', \Carbon\Carbon::now())->count();

        return $this->ResponseJsonData(compact(
            'allAssetDocuments',
            'warningAssetDocuments',
            'expiredAssetDocuments',
            'allFleetDocuments',
            'warningFleetDocuments',
            'expiredFleetDocuments',
            'allLeaseDocuments',
            'warningLeaseDocuments',
            'expiredLeaseDocuments',
            'allFinanceDocuments',
            'warningFinanceDocuments',
            'expiredFinanceDocuments',
            'allCompanyDocuments',
            'warningCompanyDocuments',
            'expiredCompanyDocuments'
        ));
    }

    public function get_data_dashboard_trading()
    {
        $current_month_sale_order = DB::table('sale_orders')
            ->whereNull('deleted_at')
            ->whereNotIn('status', ['reject', 'void', 'pending'])
            ->whereMonth('tanggal', Carbon::now())
            ->whereYear('tanggal', Carbon::now())
            ->sum('total');

        $current_month_sale_order_general = DB::table('sale_order_generals')
            ->whereNull('deleted_at')
            ->whereNotIn('status', ['reject', 'void', 'pending'])
            ->whereMonth('tanggal', Carbon::now())
            ->whereYear('tanggal', Carbon::now())
            ->sum('total');

        $current_month_invoice_trading = DB::table('invoice_tradings')
            ->whereNull('deleted_at')
            ->where('status', 'approve')
            ->whereMonth('date', Carbon::now())
            ->whereYear('date', Carbon::now())
            ->sum('total');

        $current_month_invoice_general = DB::table('invoice_generals')
            ->whereNull('deleted_at')
            ->where('status', 'approve')
            ->whereMonth('date', Carbon::now())
            ->whereYear('date', Carbon::now())
            ->sum('total');

        $account_receivables = DB::table('invoice_parents')
            ->whereNull('deleted_at')
            ->where('status', 'approve')
            ->where('type', 'trading')
            ->where('payment_status', '!=', 'paid')
            ->get();


        $payments = DB::table('invoice_payments')
            ->where('invoice_model', InvoiceTrading::class)
            ->whereIn('invoice_id', $account_receivables->pluck('reference_id')->toArray())
            ->whereNull('deleted_at')
            ->get();

        $outstanding_account_receivable = $account_receivables->sum('total') - $payments->sum('receive_amount');

        $account_receivable_due = $account_receivables->filter(function ($item) {
            return $item->due_date <= Carbon::now()->format('Y-m-d');
        });

        $payment_due = $payments->whereIn('invoice_id', $account_receivable_due->pluck('reference_id')->toArray());
        $outstanding_account_receivable_due = $account_receivable_due->sum('total') - $payment_due->sum('receive_amount');


        $account_receivable_generals = DB::table('invoice_parents')
            ->whereNull('deleted_at')
            ->where('status', 'approve')
            ->where('type', 'general')
            ->where('payment_status', '!=', 'paid')
            ->get();

        $payments_general = DB::table('invoice_payments')
            ->where('invoice_model', InvoiceGeneral::class)
            ->whereIn('invoice_id', $account_receivable_generals->pluck('reference_id')->toArray())
            ->whereNull('deleted_at')
            ->get();

        $outstanding_account_receivable_general = $account_receivable_generals->sum('total') - $payments_general->sum('receive_amount');

        $account_receivable_due_general = $account_receivable_generals->filter(function ($item) {
            return $item->due_date <= Carbon::now()->format('Y-m-d');
        });

        $payment_due_general = $payments_general->whereIn('invoice_id', $account_receivable_due->pluck('reference_id')->toArray());
        $outstanding_account_receivable_due_general = $account_receivable_due_general->sum('total') - $payment_due_general->sum('receive_amount');

        $recent_sale_orders = DB::table('sale_orders')
            ->join('customers', 'customers.id', '=', 'sale_orders.customer_id')
            ->whereNull('sale_orders.deleted_at')
            ->orderByDesc('sale_orders.tanggal')
            ->orderByDesc('sale_orders.id')
            ->limit(5)
            ->select('sale_orders.*', 'customers.nama as customer_name')
            ->get();

        $recent_sale_orders->map(function ($item) {
            $item->badge = '<div class="badge badge-lg badge-' . status_sale_orders()[$item->status]['color'] . '">
                                ' . status_sale_orders()[$item->status]['label'] . '
                            </div>';
            return $item;
        });

        $sale_graphs = DB::table('invoice_parents')
            ->whereNull('deleted_at')
            ->where('status', 'approve')
            ->where('type', 'trading')
            ->whereYear('date', Carbon::now())
            ->get();

        $sale_graph_generals = DB::table('invoice_parents')
            ->whereNull('deleted_at')
            ->where('status', 'approve')
            ->where('type', 'general')
            ->whereYear('date', Carbon::now())
            ->get();

        $data_graphs = [];
        $data_graph_generals = [];
        for ($i = 1; $i < 12; $i++) {
            $date = "1-$i-" . Carbon::now()->format('Y');

            $push_graph['name'] = Carbon::parse($date)->format('F');
            $push_graph['data'] = $sale_graphs->filter(function ($item) use ($date) {
                return Carbon::parse($item->date)->format('F-Y') == Carbon::parse($date)->format('F-Y');
            })->sum('total');

            array_push($data_graphs, $push_graph);

            $push_graph_general['name'] = Carbon::parse($date)->format('F');
            $push_graph_general['data'] = $sale_graph_generals->filter(function ($item) use ($date) {
                return Carbon::parse($item->date)->format('F-Y') == Carbon::parse($date)->format('F-Y');
            })->sum('total');

            array_push($data_graph_generals, $push_graph_general);
        }

        return response()->json(
            [
                'current_month_sales_order_total' => $current_month_sale_order,
                'current_month_sales_order_general_total' => $current_month_sale_order_general,
                'current_month_invoice_trading_total' => $current_month_invoice_trading,
                'current_month_invoice_general_total' => $current_month_invoice_general,
                'account_receivable_total' => $outstanding_account_receivable,
                'account_receivable_general_total' => $outstanding_account_receivable_general,
                'account_receivable_due_total' => $outstanding_account_receivable_due,
                'account_receivable_due_general_total' => $outstanding_account_receivable_due_general,
                'recent_sale_orders' => $recent_sale_orders,

                'sale_graphs' => ['name' => 'Trading', 'data' => collect($data_graphs)->pluck('data')->toArray()],
                'sale_graph_generals' => ['name' => 'General', 'data' => collect($data_graph_generals)->pluck('data')->toArray()],
                'months'  => collect($data_graphs)->pluck('name')->toArray(),
            ]
        );
    }

    public function get_data_dashboard_hrd()
    {
        $total_employees = DB::table('employees')
            ->whereNull('deleted_at')
            ->where('employee_status', '!=', 'non_aktif')
            ->count();
        $leaves = DB::table('leaves')
            ->join('employees', 'employees.id', '=', 'leaves.employee_id')
            ->whereNull('deleted_at')
            ->where('status', 'approve')
            ->where('leaves.from_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('leaves.to_date', '>=', Carbon::now()->format('Y-m-d'))
            ->select('leaves.*', 'employees.name as employee_name')
            ->get();

        $leaves = $leaves->map(function ($item) {
            if ($item->type == 'cuti') {
                $badge = '<div class="badge badge-pill badge-light">
                                Cuti
                            </div>';
            } else {
                $badge = '<div class="badge badge-pill badge-dark">
                                Izin
                            </div>';
            }

            $item->badge = $badge;
            $item->link = route('admin.leave.show', $item->id);
            return $item;
        });

        $legality_documents = DB::table('legality_documents')
            ->whereRaw('DATEDIFF(legality_documents.end_date, now()) <= legality_documents.due_date')
            ->whereNull('legality_documents.deleted_at')
            ->selectRaw(
                'legality_documents.*'
            )
            ->get();

        $legality_documents = $legality_documents->map(function ($item) {
            if (Carbon::now()->gt(Carbon::parse($item->end_date))) {
                $item->status = 'danger';
                $item->status_label = 'telah berakhir pada ' . Carbon::parse($item->end_date)->format('d/m/Y');
            } elseif (Carbon::now()->addDays($item->due_date)->gt(Carbon::parse($item->end_date))) {
                $item->status = 'warning';
                $item->status_label = 'akan berakhir pada ' . Carbon::parse($item->end_date)->format('d/m/Y');
            }
            $item->link = route('admin.legality-document.index');
            return $item;
        });

        $asset_documents = DB::table('asset_documents')
            ->join('assets', 'assets.id', '=', 'asset_documents.asset_id')
            ->whereRaw('DATEDIFF(asset_documents.end_date, now()) <= asset_documents.due_date')
            ->select('asset_documents.*', 'assets.asset_name')
            ->get();

        $asset_documents = $asset_documents->map(function ($item) {
            $item->name = $item->name . ' (' . $item->asset_name . ')';
            if (Carbon::now()->gt(Carbon::parse($item->end_date))) {
                $item->status = 'danger';
                $item->status_label = 'telah berakhir pada ' . Carbon::parse($item->end_date)->format('d/m/Y');
            } elseif (Carbon::now()->addDays($item->due_date)->gt(Carbon::parse($item->end_date))) {
                $item->status = 'warning';
                $item->status_label = 'akan berakhir pada ' . Carbon::parse($item->end_date)->format('d/m/Y');
            }
            $item->link = route('admin.legality-document.index');
            return $item;
        });

        $lease_documents = DB::table('lease_documents')
            ->join('leases', 'leases.id', '=', 'lease_documents.lease_id')
            ->whereRaw('DATEDIFF(lease_documents.end_date, now()) <= lease_documents.due_date')
            ->select('lease_documents.*', 'lease_name')
            ->get();

        $lease_documents = $lease_documents->map(function ($item) {
            $item->name = $item->name . ' (' . $item->lease_name . ')';
            if (Carbon::now()->gt(Carbon::parse($item->end_date))) {
                $item->status = 'danger';
                $item->status_label = 'telah berakhir pada ' . Carbon::parse($item->end_date)->format('d/m/Y');
            } elseif (Carbon::now()->addDays($item->due_date)->gt(Carbon::parse($item->end_date))) {
                $item->status = 'warning';
                $item->status_label = 'akan berakhir pada ' . Carbon::parse($item->end_date)->format('d/m/Y');
            }
            $item->link = route('admin.legality-document.index');
            return $item;
        });

        $documents = $legality_documents->merge($asset_documents)->merge($lease_documents);
        $documents = $documents->sortBy('end_date')->values()->all();

        $contract_extensions = DB::table('contract_extensions')
            ->join('employees', 'employees.id', '=', 'contract_extensions.employee_id')
            ->where('contract_extensions.status', 'approve')
            ->whereDate('contract_extensions.to_date', '>=', Carbon::now())
            ->whereRaw('DATEDIFF(contract_extensions.to_date, now()) <= 60')
            ->select('contract_extensions.*', 'employees.name as employee_name')
            ->get();

        $contract_extensions = $contract_extensions->map(function ($item) {
            $item->link = route('admin.contract-extension.show', $item->id);
            $item->status = 'akan berakhir pada ' . Carbon::parse($item->to_date)->format('d/m/Y');
            return $item;
        });


        return response()
            ->json(
                [
                    'total_employees' => $total_employees,
                    'leaves' => $leaves,
                    'documents' => $documents,
                    'contract_extensions' => $contract_extensions,
                ]
            );
    }

    public function get_data_dashboard_finance_invoice_due(Request $request)
    {
        $invoice_dues = DB::table('invoice_parents')
            ->join('customers', 'customers.id', '=', 'invoice_parents.customer_id')
            ->join('currencies', 'currencies.id', '=', 'invoice_parents.currency_id')
            ->where('payment_status', '!=', 'paid')
            ->whereNull('invoice_parents.deleted_at')
            ->whereNotIn('invoice_parents.status', ['pending', 'reject', 'void', 'revert'])
            ->select('invoice_parents.*', 'customers.nama as customer_name', 'currencies.simbol as currency_symbol')
            ->offset($request->offset ?? 0)
            ->when($request->date, function ($query) use ($request) {
                $query->whereDate('due_date', '<=', Carbon::parse($request->date));
            })
            ->limit(10)
            ->orderBy('invoice_parents.due_date', 'asc')
            ->get();

        $invoice_payments = InvoicePayment::whereIn('invoice_id', $invoice_dues->pluck('reference_id'))
            ->get();

        $invoice_dues = $invoice_dues->map(function ($item) use ($invoice_payments) {
            if ($item->type == 'trading') {
                $link = route('admin.invoice-trading.show', $item->reference_id);
            } else {
                $link = route('admin.invoice-general.show', $item->reference_id);
            }
            $item->link = $link;

            $payments = $invoice_payments->where('invoice_id', $item->reference_id)
                ->where('invoice_model', $item->model_reference)
                ->sum('receive_amount');
            $outstanding = $item->total - $payments;
            $item->outstanding = $item->currency_symbol . ' ' . floatDotFormat($outstanding);

            $item->total_exchanged = $item->total * $item->exchange_rate;
            $payment_exchanged = $invoice_payments->where('invoice_id', $item->reference_id)
                ->where('invoice_model', $item->model_reference)
                ->map(function ($item) {
                    $exchange_rate_gap = $item->reference_model_ref->exchange_rate_gap_idr ?? 0;
                    return $item->receive_amount * $item->exchange_rate - $exchange_rate_gap;
                })->sum();
            $item->outstanding_exchanged = $item->total_exchanged - $payment_exchanged;
            $item->outstanding_exchanged_formatted = get_local_currency()->simbol . ' ' . floatDotFormat($item->outstanding_exchanged);
            $item->is_exchange = $item->currency_id != get_local_currency()->id;

            $item->due_date = localDate($item->due_date);

            if ($item->type == 'trading') {
                $badge = '<div class="badge badge-sm badge-pill badge-light">
                                Inv Trading
                            </div>';
            } else {
                $badge = '<div class="badge badge-sm badge-pill badge-dark">
                                Inv General
                            </div>';
            }
            $item->badge = $badge;
            $payment_badge = '<div class="badge badge-lg badge-' . get_invoice_status()[$item->status]['color'] . '">
                                ' . get_invoice_status()[$item->status]['label'] . '
                            </div>';
            $item->payment_badge = $payment_badge;

            return $item;
        });

        return response()
            ->json(
                [
                    'invoice_dues' => $invoice_dues,
                ]
            );
    }

    public function get_data_dashboard_finance_supplier_invoice_due(Request $request)
    {
        $supplier_invoice_dues = DB::table('supplier_invoice_parents')
            ->join('vendors', 'vendors.id', '=', 'supplier_invoice_parents.vendor_id')
            ->join('currencies', 'currencies.id', '=', 'supplier_invoice_parents.currency_id')
            ->where('payment_status', '!=', 'paid')
            ->whereNull('supplier_invoice_parents.deleted_at')
            ->whereNotIn('supplier_invoice_parents.status', ['pending', 'reject', 'void', 'revert'])
            ->select('supplier_invoice_parents.*', 'vendors.nama as vendor_name', 'currencies.simbol as currency_symbol')
            ->offset($request->offset ?? 0)
            ->when($request->date, function ($query) use ($request) {
                $query->whereDate('due_date', '<=', Carbon::parse($request->date));
            })
            ->limit(10)
            ->orderBy('supplier_invoice_parents.due_date', 'asc')
            ->get();

        $supplier_invoice_payments = SupplierInvoicePayment::whereIn('supplier_invoice_id', $supplier_invoice_dues->pluck('reference_id'))
            ->get();

        $supplier_invoice_dues = $supplier_invoice_dues->map(function ($item) use ($supplier_invoice_payments) {
            if ($item->type == 'trading') {
                $link = route('admin.supplier-invoice.show', $item->reference_id);
            } else {
                $link = route('admin.supplier-invoice-general.show', $item->reference_id);
            }
            $item->link = $link;

            $payments = $supplier_invoice_payments->where('supplier_invoice_id', $item->reference_id)
                ->where('supplier_invoice_model', $item->model_reference)
                ->sum('pay_amount');
            $outstanding = $item->total - $payments;
            $item->outstanding = $item->currency_symbol . ' ' . floatDotFormat($outstanding);

            $item->total_exchanged = $item->total * $item->exchange_rate;
            $payment_exchanged = $supplier_invoice_payments->where('supplier_invoice_id', $item->reference_id)
                ->where('supplier_invoice_model', $item->model_reference)
                ->map(function ($item) {
                    $exchange_rate_gap = $item->reference_model_ref->exchange_rate_gap_idr ?? 0;
                    return $item->pay_amount * $item->exchange_rate - $exchange_rate_gap;
                })->sum();
            $item->outstanding_exchanged = $item->total_exchanged - $payment_exchanged;
            $item->outstanding_exchanged_formatted = get_local_currency()->simbol . ' ' . floatDotFormat($item->outstanding_exchanged);
            $item->is_exchange = $item->currency_id != get_local_currency()->id;

            $item->due_date = localDate($item->due_date);

            if ($item->type == 'trading') {
                $badge = '';
            } else {
                $badge = '<div class="badge badge-sm badge-pill badge-dark">
                               SI  General
                            </div>';
            }
            $item->badge = $badge;
            $payment_badge = '<div class="badge badge-sm badge-sm badge-pill badge-' . get_invoice_status()[$item->status]['color'] . '">
                                ' . get_invoice_status()[$item->status]['label'] . '
                            </div>';
            $item->payment_badge = $payment_badge;


            return $item;
        });

        return response()
            ->json(
                [
                    'supplier_invoice_dues' => $supplier_invoice_dues,
                ]
            );
    }
}
