<?php

namespace App\Http\Controllers\Admin;

use stdClass;
use Carbon\Carbon;
use App\Models\Coa;
use App\Models\Vendor;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Download;
use App\Jobs\DownloadQueue;
use App\Models\Amortization;
use App\Models\Depreciation;
use Illuminate\Http\Request;
use App\Exports\NeracaExport;
use App\Models\AccountPayable;
use App\Exports\CashBondExport;
use App\Models\SupplierInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BukuBesarExport;
use App\Models\ProfitLossDetail;
use App\Exports\KartuHutangExport;
use App\Exports\NeracaSaldoExport;
use App\Http\Traits\ResponseTrait;
use App\Models\CashAdvancePayment;
use App\Models\CashAdvanceReceive;
use App\Models\InvoiceDownPayment;
use App\Models\ReceivablesPayment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\ItemReceivingReport;
use App\Exports\HarianKasBankExport;
use App\Http\Controllers\Controller;
use App\Models\AccountPayableDetail;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SupplierInvoiceGeneral;
use App\Models\SupplierInvoicePayment;
use App\Exports\HarianKasBankDetailExport;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Exports\LaporanPerPeriodePenjualanExport;
use App\Exports\FinanceReport\SummaryHutangDagang;
use App\Exports\DocumentHistoryPurchaseInvoiceExport;
use App\Exports\FinanceReport\SaleJournalReportExport;
use App\Exports\FinanceReport\DebtCardReportSaleOrderTrading;
use App\Models\DeliveryOrderGeneral;
use App\Models\DeliveryOrderGeneralDetail;
use App\Models\InvoiceReturn;
use App\Models\SaleOrderGeneral;
use App\Models\SoTrading;

class FinanceReportController extends Controller
{
    use ActivityStatusLogHelper, ResponseTrait;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'finance-report';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    public function __construct() {}

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
        // GET DATA TO EXPORT
        $data = [];
        switch ($type) {
            case 'harian-kas-bank':
                $data = $this->harian_kas_bank($type, $request);
                $excel_export = HarianKasBankExport::class;
                break;
            // ! HARIAN KAS BANK DETAIL
            case 'harian-kas-bank-detail':
                $data = $this->harian_kas_bank_detail($type, $request);
                $excel_export = HarianKasBankDetailExport::class;
                $orientation = 'landscape';
                $paper_size = 'a3';
                break;
            // ! PELUNASAN PIUTANG DETAIL
            case 'pelunasan-piutang-detail':
                $data = $this->pelunasan_piutang_detail($type, $request);
                $excel_export = HarianKasBankDetailExport::class;
                $orientation = 'landscape';
                $paper_size = 'a4';
                break;
            // ! PELUNASAN HUTANG DETAIL
            case 'pelunasan-hutang-detail':
                $data = $this->pelunasan_hutang_detail($type, $request);
                $excel_export = HarianKasBankDetailExport::class;
                $orientation = 'landscape';
                $paper_size = 'a4';
                break;
            // ! NERACA
            case 'neraca':
                $get_data = app('App\Http\Controllers\Admin\FinanceReportNeracaController')->get_data($request);
                $data['aktiva'] = $this->neraca($get_data['aktiva']);
                $data['pasiva'] = $this->neraca($get_data['kewajiban_dan_ekuitas']);

                $data['type'] = $type;
                $data['period'] = Carbon::parse('01-' . $request->period)->translatedFormat('F Y');

                $excel_export = NeracaExport::class;
                $orientation = $request->folder;
                $paper_size = 'a4';
                break;
            // ! NERACA SALDO
            case 'neraca-saldo':
                $get_data = app('App\Http\Controllers\Admin\FinanceReportNeracaSaldoController')->get_data($request);
                $data['neraca'] = $this->neraca_saldo($get_data['account']);
                $data['type'] = $type;
                $data['period'] = $request->period;

                $excel_export = NeracaSaldoExport::class;
                $orientation = 'landscape';
                $paper_size = 'a4';
                break;
            // ! LABA RUGI
            case 'laba-rugi':
                $data = app('App\Http\Controllers\Admin\FinanceReportProfitLossController')->get_data($type, $request);
                $excel_export = HarianKasBankDetailExport::class;
                $paper_size = 'a4';
                break;
            // ! TRANSAKSI JURNAL
            case 'transaksi-jurnal':
                if ($request->format == "preview") {
                    $data = app('\App\Http\Controllers\Admin\FinanceReportController')->transaksi_jurnal($type, $request);
                }
                $paper_size = 'a3';
                $orientation = 'landscape';
                break;
            // ! JOURNAL PURCHASING
            case 'document-history-purchase-invoice':
                $data = $this->documentHistoryPurchaseInvoice($type, $request);
                $excel_export = DocumentHistoryPurchaseInvoiceExport::class;
                $paper_size = 'a3';
                $orientation = 'landscape';
                break;
            case "purchasing-journal-report":
                if ($request->format == "preview") {
                    $data = $this->reportJournalPurchasing($request);
                }
                $paper_size = 'a1';
                $orientation = 'landscape';
                break;
            case "sale-journal-report":
                if ($request->format == "preview") {
                    $data = $this->reportJournalSale($request);
                }
                $excel_export = SaleJournalReportExport::class;
                $paper_size = 'a1';
                $orientation = 'landscape';
                break;
            // ! KARTU HUTANG
            case "sisa-hutang":
                $data = $this->sisa_hutang($type, $request);
                $excel_export = HarianKasBankDetailExport::class;
                $paper_size = 'a2';
                $orientation = 'landscape';
                break;
            // ! SUMMARY HUTANG DAGANG
            case "summary-hutang-dagang":
                $data = $this->summary_hutang_dagang($type, $request);
                $excel_export = SummaryHutangDagang::class;
                $paper_size = 'a4';
                $orientation = 'landscape';
                break;
            // ! SUMMARY UANG MUKA PEMBELIAN
            case "summary-uang-muka-pembelian":
                $data = $this->summary_uang_muka_pembelian($type, $request);

                $excel_export = HarianKasBankDetailExport::class;
                $paper_size = 'a4';
                $orientation = 'landscape';
                break;
            // ! KARTU HUTANG
            case "kartu-hutang":
                $data = $this->kartu_hutang($type, $request);

                $excel_export = KartuHutangExport::class;
                $paper_size = 'a3';
                $orientation = 'landscape';
                break;
            // ! BUKU BESAR
            case "buku-besar":
                if ($request->format == "preview") {
                    $data = $this->buku_besar($type, $request);
                }
                $excel_export = BukuBesarExport::class;
                $paper_size = 'a3';
                $orientation = 'landscape';
                break;
            // ! CASHBOND
            case "cash-bond":
                $data = $this->cashBond($request);
                $excel_export = CashBondExport::class;
                $paper_size = 'a1';
                $orientation = 'landscape';
                break;
            case "debt-card-trading":
                $data = $this->debtCardReportSaleOrderTrading($request);
                $orientation = 'landscape';
                $paper_size = 'a2';
                $excel_export = DebtCardReportSaleOrderTrading::class;
                break;
            // ! SUMMARY PIUTANG DAGANG
            case "summary-piutang-dagang":
                $data = $this->summary_piutang_dagang($type, $request);
                $excel_export = SummaryHutangDagang::class;
                $paper_size = 'a4';
                $orientation = 'landscape';
                break;
            case "daftar-aktifa-tetap":
                $data = $this->exportAsset($request);
                $excel_export = \App\Exports\AssetExport::class;
                $paper_size = 'a3';
                $orientation = 'landscape';
                $data['type'] = $type;
                break;
            case "biaya-dibayar-dimuka":
                $data = $this->exportBDM($request);
                $excel_export = \App\Exports\LeaseExport::class;
                $paper_size = 'a3';
                $orientation = 'landscape';
                $data['type'] = $type;
                break;
            case "sisa-piutang":
                $data = $this->sisa_piutang($type, $request);
                $excel_export = HarianKasBankDetailExport::class;
                $paper_size = 'a2';
                $orientation = 'landscape';
                break;
            case "sisa-piutang-per-customer":
                $data = $this->sisa_piutang_per_customer($type, $request);
                $excel_export = HarianKasBankDetailExport::class;
                $paper_size = 'a3';
                $orientation = 'landscape';
                break;
            case "sisa-hutang-per-vendor":
                $data = $this->sisa_hutang_per_vendor($type, $request);
                $excel_export = HarianKasBankDetailExport::class;
                $paper_size = 'a3';
                $orientation = 'landscape';
                break;
            // ! SUMMARY UANG MUKA PENJUALAN
            case "summary-uang-muka-penjualan":
                $data = $this->summary_uang_muka_penjualan($type, $request);

                $excel_export = HarianKasBankDetailExport::class;
                $paper_size = 'a4';
                $orientation = 'landscape';
                break;
            case 'profit-loss-multiperiod':
                if ($request->format == "preview") {
                    $data = app('App\Http\Controllers\Admin\FinanceReportProfitLossController')->get_data($type, $request, true, $request->year);
                }

                $excel_export = HarianKasBankDetailExport::class;
                $paper_size = 'a3';
                $orientation = 'landscape';
                break;
            // ! NERACA MULTIPERIOD
            case 'sales-report-by-trading-period':
                $data = app('App\Http\Controllers\Admin\SalesReportByTradingPeriodController')->get_data($type, $request);

                $excel_export = LaporanPerPeriodePenjualanExport::class;
                $paper_size = 'a3';
                $orientation = 'landscape';
                break;
            case 'neraca-multiperiod':
                $get_data = app('App\Http\Controllers\Admin\FinanceReportNeracaMultiperiodController')->get_data($request);

                $data['aktiva'] = $this->neraca_multiperiod($get_data['aktiva']);
                $data['pasiva'] = $this->neraca_multiperiod($get_data['kewajiban_dan_ekuitas']);
                $data['type'] = $type;
                $data['period'] = $request->period;

                $excel_export = NeracaExport::class;
                $orientation = 'landscape';
                $paper_size = 'a4';
                break;
            default:
                # code...
                break;
        }

        // ! PROCESS DATA WITH SELECTED FORMAT
        $view_file = 'admin.' . $this->view_folder . '.' . $type . '.';
        $file_path = $view_file . $request->format;

        if ($request->folder) {
            $view_file .= $request->folder . '.';
        }
        $view_file .= $request->format;

        if ($request->format == "preview") {
            return view($view_file, $data);
        } else {
            if (in_array($type, ['transaksi-jurnal', 'purchasing-journal-report', 'sale-journal-report', 'buku-besar', 'profit-loss-multiperiod'])) {
                try {
                    $download = Download::create([
                        'user_id' => auth()->user()->id,
                        'path' => '',
                        'status' => 'pending',
                        'type' => $type,
                    ]);

                    $request_params = $request->all();
                    $request_params['type'] = $type;
                    $request_params['from_date'] = $request->from_date;
                    $request_params['to_date'] = $request->to_date;

                    DownloadQueue::dispatch($request_params, $file_path, $paper_size, $orientation, $download->id);

                    return redirect()->route('admin.download-report.index');
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return $this->AjaxResponse(
                        success: true,
                        custom_message: '',
                        exception_message: 'Gagal mengunduh laporan. ' . $th->getMessage()
                    );
                }
            } else {
                if ($request->format == "pdf") {
                    $pdf = Pdf::loadView($view_file, $data)
                        ->setPaper($paper_size ?? 'a4', $orientation ?? 'portrait');
                    return $pdf->stream($type . '.pdf');
                } else {
                    return Excel::download(new $excel_export($view_file, $data), $type . '.xlsx');
                }
            }
        }
    }

    // ! GET HARIAN KAS BANK DATA
    public function harian_kas_bank($type, $request)
    {
        try {
            $data = Coa::where(function ($query) {
                $query->where('account_type', 'Cash & Bank')
                    ->orWhereHas('bank_internal');
            })
                ->where('is_parent', 0)
                ->whereNull('coas.deleted_at')
                ->groupBy('coas.id')
                ->selectRaw('coas.*')
                ->when($request->coa_id, function ($q) use ($request) {
                    $q->where('coas.id', $request->coa_id);
                });

            $data = $data->get();

            $journal_data = DB::table('journal_details')
                ->join('coas', 'journal_details.coa_id', 'coas.id')
                ->join('journals', 'journals.id', 'journal_details.journal_id')
                ->where('journals.status', 'approve')
                ->whereNull('journals.deleted_at')
                ->whereIn('journal_details.coa_id', $data->pluck('id'))
                ->whereDate('journals.date', '<=', Carbon::parse($request->to_date))
                ->orderBy('journal_details.ordering')
                ->select(
                    'journal_details.*',
                    'journals.date',
                )
                ->get();

            $data->each(function ($d) use ($journal_data, $request) {
                // current month
                $debit = $journal_data->where('coa_id', $d->id)
                    ->where('date', '>=', Carbon::parse($request->from_date)->format('Y-m-d'))
                    ->where('date', '<=', Carbon::parse($request->to_date)->format('Y-m-d'))
                    ->sum('debit_exchanged');

                $credit = $journal_data->where('coa_id', $d->id)
                    ->where('date', '>=', Carbon::parse($request->from_date)->format('Y-m-d'))
                    ->where('date', '<=', Carbon::parse($request->to_date)->format('Y-m-d'))
                    ->sum('credit_exchanged');

                $d->mutation_debit = $debit;
                $d->mutation_credit = $credit;

                $debit_before = $journal_data->where('coa_id', $d->id)
                    ->where('date', '<', Carbon::parse($request->from_date)->format('Y-m-d'))
                    ->sum('debit_exchanged');

                $credit_before = $journal_data->where('coa_id', $d->id)
                    ->where('date', '<', Carbon::parse($request->from_date)->format('Y-m-d'))
                    ->sum('credit_exchanged');

                $balance_amount_before = $debit_before - $credit_before;
                $d->balance_amount_before = $balance_amount_before;
                $d->balance_final = $balance_amount_before + $debit - $credit;
            });

            $return['data'] = $data;
            $return['type'] = $type;
            $return['coa'] = Coa::find($request->coa_id);
            $return['from_date'] = Carbon::parse($request->from_date);
            $return['to_date'] = Carbon::parse($request->to_date);

            return $return;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // ! GET HARIAN KAS BANK DETAIL DATA
    public function harian_kas_bank_detail($type, $request)
    {
        try {
            $banks = Coa::where(function ($query) {
                $query->where('account_type', 'Cash & Bank')
                    ->orWhereHas('bank_internal');
            })
                ->orderBy('name')
                ->where('is_parent', 0);

            if ($request->coa_id) {
                $banks->where('id', $request->coa_id);
            }

            $banks = $banks
                ->get();

            $beginning_balance = DB::table('journal_details')
                ->join('coas', 'journal_details.coa_id', 'coas.id')
                ->join('journals', 'journals.id', 'journal_details.journal_id')
                ->where('journals.status', 'approve')
                ->whereNull('journals.deleted_at')
                ->whereIn('journal_details.coa_id', $banks->pluck('id'))
                ->whereDate('journals.date', '<', Carbon::parse($request->from_date))
                ->orderBy('journal_details.ordering')
                ->select(
                    'journal_details.*',
                )
                ->get();

            $balance_before = DB::table('journal_details')
                ->join('coas', 'journal_details.coa_id', 'coas.id')
                ->join('journals', 'journals.id', 'journal_details.journal_id')
                ->where('journals.status', 'approve')
                ->whereNull('journals.deleted_at')
                ->whereIn('journal_details.coa_id', $banks->pluck('id'))
                ->whereDate('journals.date', '<=', Carbon::parse($request->to_date))
                ->orderBy('journal_details.ordering')
                ->select(
                    'journal_details.*',
                )
                ->get();

            $vendors = DB::table('vendors')
                ->select('id', 'nama')
                ->get();

            $customers = DB::table('customers')
                ->select('id', 'nama')
                ->get();

            $banks->each(function ($b) use ($request, $balance_before, $beginning_balance, $vendors, $customers) {
                $get_transaction = DB::table('journal_details')
                    ->join('coas', 'journal_details.coa_id', 'coas.id')
                    ->join('journals', 'journals.id', 'journal_details.journal_id')
                    ->leftJoin('send_payments', 'send_payments.id', 'journals.send_payment_id')
                    ->leftJoin('receive_payments', 'receive_payments.id', 'journals.receive_payment_id')
                    ->where('journals.status', 'approve')
                    ->join('currencies', 'currencies.id', 'journal_details.currency_id')
                    ->where('coas.id', $b->id)
                    ->where('coas.is_parent', 0)
                    ->whereNull('coas.deleted_at')
                    ->whereNull('journals.deleted_at')
                    ->leftJoin('journal_details as opponent', function ($opponent) {
                        $opponent->on('opponent.journal_id', 'journal_details.journal_id')
                            ->where(function ($o) {
                                $o->whereColumn('opponent.debit', 'journal_details.credit')
                                    ->whereColumn('opponent.credit', 'journal_details.debit');
                            });
                    })
                    ->leftJoin('coas as coa_opponent', 'coa_opponent.id', 'opponent.coa_id')
                    ->groupBy('journal_details.id')
                    ->orderBy('journal_details.ordering')
                    ->selectRaw('journal_details.*,
                    journals.vendor_id,
                    journals.customer_id,
                    journal_details.exchange_rate,
                    journals.date,
                    journals.reference,
                    journals.document_reference,
                    journals.created_at,
                    journals.bank_code_mutation,
                    send_payments.cheque_no as giro_out,
                    receive_payments.cheque_no as giro_in,
                    coas.account_code,
                    coas.name,
                    coa_opponent.account_code as opponent_account_code,
                    coa_opponent.name as opponent_name,
                    currencies.nama,
                    currencies.simbol,
                    currencies.kode
                    ')
                    ->when($request->from_date, function ($q) use ($request) {
                        $q->whereDate('journals.date', '>=', Carbon::parse($request->from_date));
                    })
                    ->when($request->to_date, function ($q) use ($request) {
                        $q->whereDate('journals.date', '<=', Carbon::parse($request->to_date));
                    })
                    ->when($request->coa_id, function ($q) use ($request) {
                        $q->where('coas.id', $request->coa_id);
                    })
                    ->get();

                $get_transaction = $get_transaction->each(function ($g) use ($balance_before, $vendors, $customers) {
                    $debit_before = $balance_before->where('ordering', '<', $g->ordering)->where('coa_id', $g->coa_id)->sum('debit_exchanged');
                    $credit_before = $balance_before->where('ordering', '<', $g->ordering)->where('coa_id', $g->coa_id)->sum('credit_exchanged');
                    $balance_coa_before = $debit_before - $credit_before;
                    $balance_coa_after = $balance_coa_before + $g->debit_exchanged - $g->credit_exchanged;

                    $foreign_debit_before = $balance_before->where('ordering', '<', $g->ordering)->where('coa_id', $g->coa_id)->sum('debit');
                    $foreign_credit_before = $balance_before->where('ordering', '<', $g->ordering)->where('coa_id', $g->coa_id)->sum('credit');
                    $foreign_balance_coa_before = $foreign_debit_before - $foreign_credit_before;
                    $foreign_balance_coa_after = $foreign_balance_coa_before + $g->debit - $g->credit;

                    $g->balance_before = $balance_coa_before;
                    $g->balance_after = $balance_coa_after;
                    $g->foreign_balance_before = $foreign_balance_coa_before;
                    $g->foreign_balance_after = $foreign_balance_coa_after;
                    $g->document_reference =  json_decode($g->document_reference);
                    $g->vendor_customer = $g->vendor_id ? $vendors->where('id', $g->vendor_id)->first() : $customers->where('id', $g->customer_id)->first();

                    return $g;
                });

                $beginning_balance_coa = $beginning_balance->where('coa_id', $b->id)->sum('debit_exchanged') - $beginning_balance->where('coa_id', $b->id)->sum('credit_exchanged');
                $final_balance = $beginning_balance_coa + $get_transaction->sum('debit_exchanged') - $get_transaction->sum('credit_exchanged');

                $foreign_beginning_balance_coa = $beginning_balance->where('coa_id', $b->id)->sum('debit') - $beginning_balance->where('coa_id', $b->id)->sum('credit');
                $foreign_final_balance = $foreign_beginning_balance_coa + $get_transaction->sum('debit') - $get_transaction->sum('credit');

                $b->beginning_balance = $beginning_balance_coa;
                $b->foreign_beginning_balance = $foreign_beginning_balance_coa;
                $b->transactions = $get_transaction;
                $b->final_balance = $final_balance;
                $b->foreign_final_balance = $foreign_final_balance;
            });


            $return['data'] = $banks;
            $return['type'] = $type;
            $return['coa'] = Coa::find($request->coa_id);
            $return['from_date'] = Carbon::parse($request->from_date);
            $return['to_date'] = Carbon::parse($request->to_date);

            return $return;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // ! GET PELUNASAN PIUTANG DETAIL DATA
    public function pelunasan_piutang_detail($type, $request)
    {
        try {
            $data = DB::table('receivables_payments')
                ->where('receivables_payments.status', 'approve')
                ->whereNull('receivables_payments.deleted_at')
                ->join('coas', 'coas.id', 'receivables_payments.coa_id')
                ->join('currencies', 'currencies.id', 'receivables_payments.currency_id')
                ->join('customers', 'customers.id', 'receivables_payments.customer_id')
                ->leftJoin('receivables_payment_details', 'receivables_payment_details.receivables_payment_id', 'receivables_payments.id')
                ->whereNull('receivables_payment_details.deleted_at')
                ->join('invoice_parents', 'invoice_parents.id', 'receivables_payment_details.invoice_parent_id')
                ->leftJoin('bank_code_mutations', function ($join) {
                    $join->on('bank_code_mutations.ref_id', '=', 'receivables_payments.id')
                        ->where('ref_model', ReceivablesPayment::class);
                })
                ->orderBy('receivables_payments.date')
                ->orderBy('receivables_payments.code')
                ->when($request->from_date, function ($q) use ($request) {
                    $q->whereDate('receivables_payments.date', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($q) use ($request) {
                    $q->whereDate('receivables_payments.date', '<=', Carbon::parse($request->to_date));
                })
                ->when($request->coa_id, function ($q) use ($request) {
                    $q->where('receivables_payments.coa_id', $request->coa_id);
                })

                ->when($request->customer_id, function ($q) use ($request) {
                    $q->where('receivables_payments.customer_id', $request->customer_id);
                })

                ->when($request->status, function ($q) use ($request) {
                    $q->where('invoice_parents.status', $request->status);
                })
                ->when($request->payment_status, function ($q) use ($request) {
                    $q->where('invoice_parents.payment_status', $request->payment_status);
                })
                ->when($request->invoice_parent_id, function ($q) use ($request) {
                    $q->where('invoice_parents.id', $request->invoice_parent_id);
                })
                ->when($request->active, function ($q) {
                    return $q->where('invoice_parents.lock_status', 0);
                })
                ->selectRaw(
                    'receivables_payments.date,
                receivables_payments.code,
                invoice_parents.code as invoice_code,
                invoice_parents.model_reference as model_reference,
                invoice_parents.reference_id as reference_id,
                receivables_payments.exchange_rate,
                customers.nama,
                coas.account_code as coa_account_code,
                coas.name as coa_name,
                coas.id as coa_id,
                receivables_payment_details.receivables_payment_id as receivables_payment_id,
                receivables_payment_details.note,
                receivables_payment_details.receive_amount,
                receivables_payment_details.receive_amount * receivables_payments.exchange_rate as receive_amount_local,
                currencies.kode as currency_kode,
                currencies.simbol as currency_simbol,
                bank_code_mutations.code as bank_code_mutation',
                )
                ->get();

            $return['data'] = $data;
            $return['type'] = $type;
            $return['coa'] = Coa::find($request->coa_id);
            $return['from_date'] = Carbon::parse($request->from_date);
            $return['to_date'] = Carbon::parse($request->to_date);

            return $return;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function pelunasan_hutang_detail($type, $request)
    {
        try {
            $data = DB::table('account_payables')
                ->where('account_payables.status', 'approve')
                ->whereNull('account_payables.deleted_at')
                ->join('coas', 'coas.id', 'account_payables.coa_id')
                ->join('currencies', 'currencies.id', 'account_payables.currency_id')
                ->join('vendors', 'vendors.id', 'account_payables.vendor_id')
                ->leftJoin('account_payable_details', 'account_payable_details.account_payable_id', 'account_payables.id')
                ->whereNull('account_payable_details.deleted_at')
                ->join('supplier_invoice_parents', 'supplier_invoice_parents.id', 'account_payable_details.supplier_invoice_parent_id')
                ->leftJoin('bank_code_mutations', function ($join) {
                    $join->on('bank_code_mutations.ref_id', '=', 'account_payables.id')
                        ->where('ref_model', AccountPayable::class);
                })
                ->orderBy('account_payables.date')
                ->orderBy('account_payables.code')
                ->when($request->from_date, function ($q) use ($request) {
                    $q->whereDate('account_payables.date', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($q) use ($request) {
                    $q->whereDate('account_payables.date', '<=', Carbon::parse($request->to_date));
                })
                ->when($request->coa_id, function ($q) use ($request) {
                    $q->where('account_payables.coa_id', $request->coa_id);
                })

                ->when($request->vendor_id, function ($q) use ($request) {
                    $q->where('account_payables.vendor_id', $request->vendor_id);
                })

                ->when($request->status, function ($q) use ($request) {
                    $q->where('supplier_invoice_parents.status', $request->status);
                })
                ->when($request->payment_status, function ($q) use ($request) {
                    $q->where('supplier_invoice_parents.payment_status', $request->payment_status);
                })
                ->when($request->invoice_parent_id, function ($q) use ($request) {
                    $q->where('supplier_invoice_parents.id', $request->invoice_parent_id);
                })
                ->when($request->active, function ($q) {
                    return $q->where('supplier_invoice_parents.lock_status', 0);
                })
                ->selectRaw(
                    '
                    account_payables.date,
                    account_payables.code,
                    supplier_invoice_parents.code as invoice_code,
                    supplier_invoice_parents.model_reference as model_reference,
                    supplier_invoice_parents.reference_id as reference_id,
                    account_payables.exchange_rate,
                    vendors.nama,
                    
                    coas.account_code as coa_account_code,
                    coas.name as coa_name,
                    account_payable_details.note,
                    account_payable_details.amount,
                    account_payable_details.amount * account_payables.exchange_rate as amount_local,
                    currencies.kode as currency_kode,
                    currencies.simbol as currency_simbol,
                    bank_code_mutations.code as bank_code_mutation,
                    bank_code_mutations.ref_model as bank_code_mutation_ref_model,
                    bank_code_mutations.ref_id as bank_code_mutation_ref_id
                    ',
                )
                ->get();

            $return['data'] = $data;
            $return['type'] = $type;
            $return['coa'] = Coa::find($request->coa_id);
            $return['from_date'] = Carbon::parse($request->from_date);
            $return['to_date'] = Carbon::parse($request->to_date);

            return $return;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // ! GET TRANSAKSI JURNAL DATA
    public function transaksi_jurnal($type, $request): array
    {
        $journals = DB::table('journal_details as jd')
            ->join('coas', 'coas.id', 'jd.coa_id')
            ->join('journals as j', 'j.id', 'jd.journal_id')
            ->join('currencies as c', 'c.id', 'jd.currency_id')
            ->whereNull('j.deleted_at')
            ->where('j.status', 'approve')
            ->whereDate('j.date', '>=', Carbon::parse($request['from_date']))
            ->whereDate('j.date', '<=', Carbon::parse($request['to_date']))
            ->orderBy('j.date')
            ->orderBy('jd.ordering')
            ->groupBy('jd.id')
            ->selectRaw(
                'jd.*,
                j.remark as journal_remark,
                j.reference_number,
                j.id as j_id,
                j.journal_type,
                j.reference,
                j.document_reference,
                j.date as journal_date,
                jd.exchange_rate as journal_exchange_rate,
                j.created_at as journal_created_at,
                coas.account_code as coa_code,
                coas.name as coa_name,
                coas.normal_balance,
                c.kode as currency_code,
                c.nama as currency_name,
                c.simbol as currency_symbol'
            );

        $journals = $journals->get();
        $journals->each(function ($journal) {
            $journal->document_reference = json_decode($journal->document_reference ?? null);
            $journal->reference = json_decode($journal->reference ?? null);
        });

        $return['data'] = $journals;
        $return['type'] = $type;
        $return['from_date'] = Carbon::parse($request['from_date']);
        $return['to_date'] = Carbon::parse($request['to_date']);

        return $return;
    }

    public function documentHistoryPurchaseInvoice($type, $request)
    {
        $from_date = Carbon::parse($request->from_date);
        $to_date = Carbon::parse($request->to_date);

        $purchases = DB::table('purchases')
            ->whereNull('purchases.deleted_at')
            ->whereNotIn('purchases.status', ['pending', 'revert', 'void', 'reject'])
            ->when($request->from_date, function ($q) use ($from_date) {
                $q->whereDate('purchases.tanggal', '>=', $from_date);
            })
            ->when($request->to_date, function ($q) use ($to_date) {
                $q->whereDate('purchases.tanggal', '<=', $to_date);
            })
            ->join('vendors', function ($q) {
                $q->on('vendors.id', 'purchases.vendor_id');
            })
            ->leftJoin('item_receiving_reports', function ($q) {
                $q->on('item_receiving_reports.purchase_id', 'purchases.id')
                    ->whereIn('item_receiving_reports.status', ['approve', 'done', 'return-all']);
            })
            ->leftJoin('supplier_invoice_details', function ($q) {
                $q->on('supplier_invoice_details.item_receiving_report_id', 'item_receiving_reports.id');
            })
            ->leftJoin('supplier_invoices', function ($q) {
                $q->on('supplier_invoices.id', 'supplier_invoice_details.supplier_invoice_id')
                    ->whereNotIn('supplier_invoices.status', ['pending', 'revert', 'void', 'reject']);
            })
            ->leftJoin('supplier_invoice_parents', function ($q) {
                $q->on('supplier_invoice_parents.reference_id', 'supplier_invoices.id')
                    ->where('supplier_invoice_parents.model_reference', 'App\Models\SupplierInvoice');
            })
            ->leftJoin('account_payable_details', function ($q) {
                $q->on('account_payable_details.supplier_invoice_parent_id', 'supplier_invoices.id');
            })
            ->leftJoin('account_payables', function ($q) {
                $q->on('account_payables.id', 'account_payable_details.account_payable_id')
                    ->whereNotIn('account_payables.status', ['pending', 'revert', 'void', 'reject'])
                    ->leftJoin('bank_code_mutations', function ($join) {
                        $join->on('bank_code_mutations.ref_id', '=', 'account_payables.id')
                            ->where('bank_code_mutations.ref_model', AccountPayable::class);
                    });
            })
            ->select(
                'vendors.nama as vendor_name',
                'vendors.code as vendor_code',
                'purchases.kode as purchase_code',
                'purchases.tanggal as purchase_date',
                'purchases.model_id as purchase_model_id',
                'purchases.tipe as purchase_tipe',
                'item_receiving_reports.id as lpb_id',
                'item_receiving_reports.tipe as lpb_tipe',
                'item_receiving_reports.kode as lpb_code',
                'item_receiving_reports.date_receive as lpb_date',
                'supplier_invoices.id as si_id',
                'supplier_invoices.code as si_code',
                'supplier_invoices.date as si_date',
                'account_payables.id as ap_id',
                'account_payables.code as ap_code',
                'account_payables.date as ap_date',
                'bank_code_mutations.code as bank_code_mutation'
            )
            ->groupBy([
                'vendors.nama',
                'purchases.kode',
                'purchases.tanggal',
                'item_receiving_reports.kode',
                'item_receiving_reports.date_receive',
                'supplier_invoices.code',
                'supplier_invoices.date',
                'account_payables.code',
                'account_payables.date',
            ])
            ->get();

        $return['data'] = $purchases;
        $return['type'] = $type;
        $return['from_date'] = Carbon::parse($request->from_date);
        $return['to_date'] = Carbon::parse($request->to_date);

        return $return;
    }

    function get_purchase_invoice_details($data, $from_date, $to_date)
    {
        $details = [];

        if ($data->reference_model == 'App\Models\PurchaseOrderGeneral') {
            $pog = DB::table('purchase_order_generals')
                ->where('id', $data->reference_id)
                ->whereDate('purchase_order_generals.date', '>=', $from_date)
                ->whereDate('purchase_order_generals.date', '<=', $to_date)
                ->whereNull('purchase_order_generals.deleted_at')
                ->first();

            if ($pog) {
                $details['po_code'] = $pog->code;
                $details['po_date'] = $pog->date;
            }
        } else if ($data->reference_model == 'App\Models\PurchaseOrderService') {
            $pos = DB::table('purchase_order_services')
                ->where('id', $data->reference_id)
                ->whereDate('purchase_order_services.date', '>=', $from_date)
                ->whereDate('purchase_order_services.date', '<=', $to_date)
                ->whereNull('purchase_order_services.deleted_at')
                ->first();

            if ($pos) {
                $details['po_code'] = $pos->code;
                $details['po_date'] = $pos->date;
            }
        } else if ($data->reference_model == 'App\Models\PoTrading') {
            $pot = DB::table('purchase_orders')
                ->where('id', $data->reference_id)
                ->whereDate('purchase_orders.tanggal', '>=', $from_date)
                ->whereDate('purchase_orders.tanggal', '<=', $to_date)
                ->whereNull('purchase_orders.deleted_at')
                ->first();

            if ($pot) {
                $details['po_code'] = $pot->nomor_po;
                $details['po_date'] = $pot->tanggal;
            }
        } else {
            $pt = DB::table('purchase_transports')
                ->where('id', $data->reference_id)
                ->whereDate('purchase_transports.target_delivery', '>=', $from_date)
                ->whereDate('purchase_transports.target_delivery', '<=', $to_date)
                ->whereNull('purchase_transports.deleted_at')
                ->first();

            if ($pt) {
                $details['po_code'] = $pt->kode;
                $details['po_date'] = $pt->target_delivery;
            }
        }

        $supplier_invoices = DB::table('supplier_invoice_details as sid')
            ->where('sid.item_receiving_report_id', $data->id)
            ->join('supplier_invoices as si', 'si.id', 'sid.supplier_invoice_id')
            ->where('si.status', 'approve')
            ->join('supplier_invoice_parents as sip', function ($j) {
                $j->on('sip.reference_id', '=', 'si.id')
                    ->where('sip.model_reference', 'App\Models\SupplierInvoice');
            })
            ->whereNull('si.deleted_at')
            ->select(
                'si.id',
                'si.code',
                'si.date',
                'sip.id as supplier_invoice_parent_id'
            )
            ->get();

        $sis = clone $supplier_invoices;

        foreach ($supplier_invoices as $si) {
            $details['si_code'] = $si->code;
            $details['si_date'] = $si->date;
        }

        $account_payables = DB::table('account_payable_details')
            ->join('account_payables', 'account_payables.id', '=', 'account_payable_details.account_payable_id')
            ->whereNull('account_payable_details.deleted_at')
            ->whereNull('account_payables.deleted_at')
            ->whereIn('account_payables.status', ['approve'])
            ->where('account_payable_details.supplier_invoice_parent_id', $sis->pluck('supplier_invoice_parent_id')->toArray())
            ->select(
                'account_payables.id',
                'account_payables.code',
                'account_payables.date',
                'account_payable_details.supplier_invoice_parent_id'
            )
            ->get();

        $bank_code_mutations = DB::table('bank_code_mutations')
            ->whereNull('bank_code_mutations.deleted_at')
            ->where('bank_code_mutations.ref_model', 'App\Models\AccountPayable')
            ->whereIn('bank_code_mutations.ref_id', $account_payables->pluck('id')->toArray())
            ->select(
                'bank_code_mutations.id',
                'bank_code_mutations.code',
                'bank_code_mutations.date',
            )
            ->get();

        foreach ($bank_code_mutations as $bcm) {
            $details['ap_code'] = $bcm->code;
            $details['ap_date'] = $bcm->date;
        }

        return $details;
    }

    /**
     * Generate report for purchasing journal
     *
     * @param $request
     * @return array
     */
    public function reportJournalPurchasing($request): array
    {
        $model = DB::table('journal_details')
            ->leftJoin('journals', 'journals.id', 'journal_details.journal_id')
            ->leftJoin('coas', 'coas.id', 'journal_details.coa_id')
            ->leftJoin('currencies', 'currencies.id', 'journal_details.currency_id')
            ->whereNull('journals.deleted_at')
            ->where('journals.status', 'approve')
            ->whereIn('journals.journal_type', ['Purchase Journal', 'Purchase Return'])
            ->when($request['from_date'], function ($query) use ($request) {
                $query->whereDate('journals.date', '>=', Carbon::parse($request['from_date']));
            })
            ->when($request['to_date'], function ($query) use ($request) {
                $query->whereDate('journals.date', '<=', Carbon::parse($request['to_date']));
            })
            ->distinct('journal_details.id')
            ->selectRaw('
                journal_details.id as id,
                journals.id as journal_id,
                journals.code,
                journals.journal_type,
                journals.reference,
                journals.document_reference,
                journals.date as journal_date,
                journal_details.exchange_rate as journal_exchange_rate,
                journals.created_at as journal_created_at,

                coas.account_code as coa_code,
                coas.name as coa_name,
                coas.normal_balance,

                currencies.kode as currency_code,
                currencies.nama as currency_name,
                currencies.simbol as currency_symbol,

                journal_details.remark,
                journal_details.debit,
                journal_details.credit,
                journal_details.debit_exchanged,
                journal_details.credit_exchanged
            ')
            ->get();

        $model->each(function ($m) {
            $m->document_reference = json_decode($m->document_reference ?? null);
            $m->reference = json_decode($m->reference ?? null);

            return $m;
        });

        return [
            'data' => $model,
            'type' => 'purchasing-journal-report',
            'from_date' => Carbon::parse($request['from_date']),
            'to_date' => Carbon::parse($request['to_date']),
        ];
    }

    /**
     * Generate report for sale journal
     *
     * @param $request
     * @return array
     */
    public function reportJournalSale($request): array
    {
        $model = DB::table('journal_details')
            ->leftJoin('journals', 'journals.id', 'journal_details.journal_id')
            ->leftJoin('coas', 'coas.id', 'journal_details.coa_id')
            ->leftJoin('currencies', 'currencies.id', 'journal_details.currency_id')
            ->whereNull('journals.deleted_at')
            ->where('journals.status', 'approve')
            ->whereIn('journals.journal_type', ['Delivery Order Trading', "Delivery Order General", "Delivery Order Ship", "Sale Journal", "Invoice Return"])
            ->when($request['from_date'], function ($query) use ($request) {
                $query->whereDate('journals.date', '>=', Carbon::parse($request['from_date']));
            })
            ->when($request['to_date'], function ($query) use ($request) {
                $query->whereDate('journals.date', '<=', Carbon::parse($request['to_date']));
            })
            ->distinct('journal_details.id')
            ->selectRaw('
                journals.id as journal_id,
                journals.code,
                journals.journal_type,
                journals.reference,
                journals.document_reference,
                journals.date as journal_date,
                journal_details.exchange_rate as journal_exchange_rate,
                journals.created_at as journal_created_at,

                coas.account_code as coa_code,
                coas.name as coa_name,
                coas.normal_balance,

                currencies.kode as currency_code,
                currencies.nama as currency_name,
                currencies.simbol as currency_symbol,

                journal_details.remark,
                journal_details.debit,
                journal_details.credit,
                journal_details.debit_exchanged,
                journal_details.credit_exchanged
            ')
            ->get();

        $model->each(function ($m) {
            $m->document_reference = json_decode($m->document_reference ?? null);
            $m->reference = json_decode($m->reference ?? null);
        });

        return [
            'data' => $model,
            'type' => 'sale-journal-report',
            'from_date' => Carbon::parse($request['from_date']),
            'to_date' => Carbon::parse($request['to_date']),
        ];
    }

    // ! GET SISA HUTANG DATA
    public function sisa_hutang($type, $request)
    {
        $item_receiving_reports = ItemReceivingReport::whereDate('date_receive', '<=', Carbon::parse($request->to_date))
            ->whereIn('status', ['approve', 'done', 'return-all'])
            ->join('vendors', 'vendors.id', 'item_receiving_reports.vendor_id')
            ->when($request->currency_id, function ($query)  use ($request) {
                $query->where('item_receiving_reports.currency_id', $request->currency_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                $query->where('item_receiving_reports.vendor_id', $request->vendor_id);
            })
            ->select(
                'item_receiving_reports.id',
                'item_receiving_reports.kode as code',
                'vendors.nama as vendor_nama',
                'vendors.code as vendor_code',
                'item_receiving_reports.date_receive as date',
                'item_receiving_reports.total',
                'item_receiving_reports.total as outstanding_amount',
                'item_receiving_reports.exchange_rate',
            )
            ->get();

        $lpb_payments = DB::table('supplier_invoice_payments')
            ->whereIn('item_receiving_report_id', $item_receiving_reports->pluck('id'))
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->whereDate('supplier_invoice_payments.date', '<=', Carbon::parse($request->to_date))
            ->get();

        $item_receiving_reports =  $item_receiving_reports->each(function ($m) use ($lpb_payments) {
            $paid_amount = $lpb_payments->where('item_receiving_report_id', $m->id)
                ->sum('pay_amount');

            $paid_amount_exchanged = $lpb_payments->where('item_receiving_report_id', $m->id)
                ->map(function ($p) {
                    return $p->pay_amount * $p->exchange_rate;
                })
                ->sum();


            $m->total_exchanged = $m->total * $m->exchange_rate;
            $m->outstanding_amount_exchanged = $m->outstanding_amount * $m->exchange_rate;
            $m->paid_amount = $paid_amount;
            $m->paid_amount_exchanged = $paid_amount_exchanged;
            $m->outstanding_amount = $m->total - $paid_amount;
            $m->outstanding_amount_exchanged = $m->total_exchanged - $paid_amount_exchanged;
            $acumulated_exchange_rate_gap = $lpb_payments->where('item_receiving_reports', $m->id)
                ->map(function ($p) use ($m) {
                    $gap = ($m->total * $m->exchange_rate) - ($m->total * $p->exchange_rate);
                    return $gap;
                })->sum();

            $m->acumulated_exchange_rate_gap = $acumulated_exchange_rate_gap;

            return $m;
        });

        $item_receiving_reports = $item_receiving_reports->filter(function ($m) {
            return $m->outstanding_amount != 0;
        })->values();

        // get supplier invoice parent where not paid before to date
        $supplier_invoice_parents = DB::table('supplier_invoice_parents')
            ->where('supplier_invoice_parents.type', 'general')
            ->whereDate('supplier_invoice_parents.date', '<=', Carbon::parse($request->to_date))
            ->join('vendors', 'vendors.id', 'supplier_invoice_parents.vendor_id')
            ->whereNull('supplier_invoice_parents.deleted_at')
            ->where('supplier_invoice_parents.status', 'approve')
            ->leftJoin('supplier_invoice_payments', function ($query) use ($request) {
                $query->on('supplier_invoice_parents.model_reference', 'supplier_invoice_payments.supplier_invoice_model')
                    ->whereNull('supplier_invoice_payments.deleted_at')
                    ->whereColumn('supplier_invoice_parents.reference_id', 'supplier_invoice_payments.supplier_invoice_id')
                    ->whereDate('supplier_invoice_payments.date', '<=', Carbon::parse($request->to_date));
            })
            ->selectRaw(
                'supplier_invoice_parents.*,
                vendors.nama as vendor_nama,
                vendors.code as vendor_code,
                supplier_invoice_parents.total,
                COALESCE(SUM(supplier_invoice_payments.pay_amount), 0) as paid_amount,
                COALESCE(SUM(supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate), 0) as paid_amount_exchanged',
            )
            ->orderBy('supplier_invoice_parents.date')
            ->orderBy('supplier_invoice_parents.code')
            ->groupBy('supplier_invoice_parents.id')
            ->havingRaw('total != paid_amount')
            ->when($request->currency_id, function ($query)  use ($request) {
                $query->where('supplier_invoice_parents.currency_id', $request->currency_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                $query->where('supplier_invoice_parents.vendor_id', $request->vendor_id);
            })
            ->get();

        $supplier_invoice_payments = DB::table('supplier_invoice_payments')
            ->whereIn('supplier_invoice_id', $supplier_invoice_parents->pluck('reference_id'))
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->whereDate('supplier_invoice_payments.date', '<=', Carbon::parse($request->to_date))
            ->get();

        $supplier_invoice_parents = $supplier_invoice_parents->map(function ($m) use ($supplier_invoice_payments) {
            $m->total_exchanged = $m->total * $m->exchange_rate;
            $paid_amount = $supplier_invoice_payments->where('supplier_invoice_id', $m->reference_id)
                ->where('supplier_invoice_model', $m->model_reference)
                ->sum('pay_amount');

            $paid_amount_exchanged = $supplier_invoice_payments->where('supplier_invoice_id', $m->reference_id)
                ->where('supplier_invoice_model', $m->model_reference)
                ->map(function ($p) {
                    return $p->pay_amount * $p->exchange_rate;
                })
                ->sum();

            $m->paid_amount = $paid_amount;
            $m->paid_amount_exchanged = $paid_amount_exchanged;
            $m->return = 0;
            $m->return_exchanged = 0;
            $m->outstanding_amount = $m->total - $paid_amount;
            $m->outstanding_amount_exchanged = $m->total_exchanged - $paid_amount_exchanged;

            // get all payments gap
            $acumulated_exchange_rate_gap = $supplier_invoice_payments->where('supplier_invoice_id', $m->reference_id)
                ->map(function ($p) use ($m) {
                    // $gap = ($m->exchange_rate - $p->exchange_rate) * $p->pay_amount;
                    $gap = ($m->total * $m->exchange_rate) - ($m->total * $p->exchange_rate);
                    return $gap;
                })->sum();

            $m->acumulated_exchange_rate_gap = $acumulated_exchange_rate_gap;

            return $m;
        });

        $other_supplier_invoice_payments = DB::table('supplier_invoice_payments')
            ->join('supplier_invoices', 'supplier_invoices.id', 'supplier_invoice_payments.supplier_invoice_id')
            ->join('vendors', 'vendors.id', 'supplier_invoices.vendor_id')
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->where('pay_amount', '!=', 0)
            ->whereDate('supplier_invoice_payments.date', '<=', Carbon::parse($request->to_date))
            ->whereNull('supplier_invoice_payments.item_receiving_report_id')
            ->where('supplier_invoice_payments.supplier_invoice_model', 'App\Models\SupplierInvoice')
            ->when($request->currency_id, function ($query)  use ($request) {
                $query->where('supplier_invoices.currency_id', $request->currency_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                $query->where('supplier_invoices.vendor_id', $request->vendor_id);
            })
            ->select(
                'supplier_invoice_payments.*',
                'supplier_invoices.vendor_id',
                'supplier_invoices.exchange_rate as si_exchange_rate',
                'vendors.nama as vendor_nama',
                'vendors.code as vendor_code',
                'supplier_invoices.code',
            )
            ->get();

        $other_supplier_invoice_payments = $other_supplier_invoice_payments->map(function ($other_supplier_invoice_payment) {
            $other_supplier_invoice_payment->total_exchanged = 0;
            $paid_amount = $other_supplier_invoice_payment->pay_amount;
            $paid_amount_exchanged = $other_supplier_invoice_payment->pay_amount * $other_supplier_invoice_payment->exchange_rate;

            $other_supplier_invoice_payment->due_date = '';
            $other_supplier_invoice_payment->code = $other_supplier_invoice_payment->note;
            $other_supplier_invoice_payment->total = 0;
            $other_supplier_invoice_payment->paid_amount = $paid_amount;
            $other_supplier_invoice_payment->paid_amount_exchanged = $paid_amount_exchanged;
            $other_supplier_invoice_payment->return = 0;
            $other_supplier_invoice_payment->return_exchanged = 0;
            $other_supplier_invoice_payment->outstanding_amount = $other_supplier_invoice_payment->total - ($paid_amount);
            $other_supplier_invoice_payment->outstanding_amount_exchanged = $other_supplier_invoice_payment->total_exchanged - ($paid_amount_exchanged);
            $gap = ($other_supplier_invoice_payment->exchange_rate - $other_supplier_invoice_payment->si_exchange_rate) * $other_supplier_invoice_payment->pay_amount;
            $other_supplier_invoice_payment->acumulated_exchange_rate_gap = $gap;

            return $other_supplier_invoice_payment;
        });

        $purchase_returns = DB::table('purchase_returns')
            ->whereNull('purchase_returns.deleted_at')
            ->where('purchase_returns.status', 'approve')
            ->whereDate('purchase_returns.date', '<=', Carbon::parse($request->to_date))
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_returns.vendor_id', $request->vendor_id);
            })
            ->when($request->currency_id, function ($query)  use ($request) {
                $query->where('purchase_returns.currency_id', $request->currency_id);
            })
            ->leftJoin('purchase_return_histories', function ($join) use ($request) {
                $join->on('purchase_return_histories.purchase_return_id', 'purchase_returns.id')
                    ->whereNull('purchase_return_histories.deleted_at')
                    ->whereDate('purchase_return_histories.date', '<=', Carbon::parse($request->to_date));
            })
            ->join('currencies', 'currencies.id', 'purchase_returns.currency_id')
            ->join('vendors', 'vendors.id', 'purchase_returns.vendor_id')
            ->selectRaw(
                'purchase_returns.*,
                vendors.nama as vendor_nama,
                vendors.code as vendor_code,
                currencies.nama as currency_name,
                purchase_returns.total * purchase_returns.exchange_rate as total_exchanged,
                COALESCE(SUM(purchase_return_histories.amount), 0) as paid_amount'
            )
            ->groupBy('purchase_returns.id')
            ->havingRaw('total != paid_amount')
            ->get();

        $purchase_returns = $purchase_returns->map(function ($purchase_return) {
            $purchase_return->due_date = '';
            $purchase_return->code = $purchase_return->code;
            $purchase_return->total = $purchase_return->total * -1;
            $purchase_return->total_exchanged = $purchase_return->total_exchanged * -1;

            $paid_amount = $purchase_return->paid_amount * -1;
            $paid_amount_exchanged = $paid_amount * $purchase_return->exchange_rate;
            $purchase_return->paid_amount = $paid_amount;
            $purchase_return->paid_amount_exchanged = $paid_amount_exchanged;
            $purchase_return->outstanding_amount = $purchase_return->total - $paid_amount;
            $purchase_return->outstanding_amount_exchanged = $purchase_return->outstanding_amount * $purchase_return->exchange_rate;
            $purchase_return->acumulated_exchange_rate_gap = 0;

            return $purchase_return;
        });


        $merge_data = collect($supplier_invoice_parents)
            ->merge($item_receiving_reports)
            ->merge($other_supplier_invoice_payments)
            ->merge($purchase_returns)
            ->sortBy('date')
            ->values();

        $return['data'] = $merge_data;
        $return['type'] = $type;
        $return['from_date'] = Carbon::parse($request->from_date);
        $return['to_date'] = Carbon::parse($request->to_date);
        $return['currency'] = Currency::find($request->currency_id);
        $return['vendor'] = Vendor::find($request->vendor_id);

        return $return;
    }

    // ! GET SUMMARY HUTANG DAGANG DATA
    public function summary_hutang_dagang($type, $request)
    {
        $start_period = '01-' . $request->start_period;
        $start_period = Carbon::parse($start_period)->startOfMonth();
        $end_period = '01-' . $request->end_period;
        $end_period = Carbon::parse($end_period)->endOfMonth();

        $vendors = DB::table('vendors')->orderBy('nama')
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('id', $request->vendor_id);
            })
            ->whereNull('deleted_at')
            ->get();

        $item_receiving_reports = DB::table('item_receiving_reports')
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('vendor_id', $request->vendor_id);
            })
            ->whereNull('item_receiving_reports.deleted_at')
            ->whereIn('item_receiving_reports.status', ['approve', 'done', 'return-all'])
            ->whereDate('item_receiving_reports.date_receive', '<=', $end_period)
            ->select('item_receiving_reports.*')
            ->groupBy('item_receiving_reports.id')
            ->get();

        $transactions = DB::table('supplier_invoice_payments')
            ->join('supplier_invoices', 'supplier_invoices.id', 'supplier_invoice_payments.supplier_invoice_id')
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('supplier_invoices.vendor_id', $request->vendor_id);
            })
            ->join('vendors', 'vendors.id', 'supplier_invoices.vendor_id')
            ->where('supplier_invoices.status', 'approve')
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->whereNull('supplier_invoices.deleted_at')
            ->whereDate('supplier_invoice_payments.date', '<=', $end_period)
            ->select(
                'supplier_invoice_payments.*',
                'vendors.id as vendor_id',
            )
            ->get();

        $transaction_generals = DB::table('supplier_invoice_payments')
            ->join('supplier_invoice_generals', 'supplier_invoice_generals.id', 'supplier_invoice_payments.supplier_invoice_id')
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('supplier_invoice_generals.vendor_id', $request->vendor_id);
            })
            ->join('vendors', 'vendors.id', 'supplier_invoice_generals.vendor_id')
            ->where('supplier_invoice_generals.status', 'approve')
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->whereNull('supplier_invoice_generals.deleted_at')
            ->whereDate('supplier_invoice_payments.date', '<=', $end_period)
            ->select(
                'supplier_invoice_payments.*',
                'vendors.id as vendor_id',
            )
            ->get();

        $transactions = $transactions->merge($transaction_generals);

        $purchase_return_beginnings = DB::table('purchase_returns')
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_returns.vendor_id', $request->vendor_id);
            })
            ->whereDate('purchase_returns.date', '<', $start_period)
            ->where('purchase_returns.status', 'approve')
            ->whereNull('purchase_returns.deleted_at')
            ->selectRaw(
                'purchase_returns.*'
            )
            ->groupBy('purchase_returns.id')
            ->get();

        $purchase_return_histories_beginnings = DB::table('purchase_return_histories')
            ->whereNull('purchase_return_histories.deleted_at')
            ->join('purchase_returns', function ($query) {
                $query->on('purchase_returns.id', 'purchase_return_histories.purchase_return_id')
                    ->where('purchase_returns.status', 'approve')
                    ->whereNull('purchase_returns.deleted_at');
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_returns.vendor_id', $request->vendor_id);
            })
            ->whereDate('purchase_return_histories.date', '<', $start_period)
            ->selectRaw(
                'purchase_returns.vendor_id,
            purchase_returns.exchange_rate,
            purchase_return_histories.amount as total,
            purchase_return_histories.amount * purchase_returns.exchange_rate as total_exchanged'
            )
            ->groupBy('purchase_return_histories.id')
            ->get();

        $purchase_return_currents = DB::table('purchase_returns')
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_returns.vendor_id', $request->vendor_id);
            })
            ->whereDate('purchase_returns.date', '>=', $start_period)
            ->whereDate('purchase_returns.date', '<=', $end_period)
            ->where('purchase_returns.status', 'approve')
            ->whereNull('purchase_returns.deleted_at')
            ->selectRaw(
                'purchase_returns.*'
            )
            ->groupBy('purchase_returns.id')
            ->get();

        $purchase_return_histories_currents = DB::table('purchase_return_histories')
            ->whereNull('purchase_return_histories.deleted_at')
            ->join('purchase_returns', function ($query) {
                $query->on('purchase_returns.id', 'purchase_return_histories.purchase_return_id')
                    ->where('purchase_returns.status', 'approve')
                    ->whereNull('purchase_returns.deleted_at');
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('purchase_returns.vendor_id', $request->vendor_id);
            })
            ->whereDate('purchase_return_histories.date', '>=', $start_period)
            ->whereDate('purchase_return_histories.date', '<=', $end_period)
            ->selectRaw(
                'purchase_return_histories.date,
            purchase_returns.id,
            purchase_returns.vendor_id,
            purchase_returns.code,
            purchase_returns.exchange_rate,
            purchase_return_histories.amount as total,
            purchase_return_histories.amount * purchase_returns.exchange_rate as total_exchanged'
            )
            ->groupBy('purchase_return_histories.id')
            ->get();

        $supplier_invoice_generals = DB::table('supplier_invoice_generals')
            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->where('vendor_id', $request->vendor_id);
            })
            ->whereNull('supplier_invoice_generals.deleted_at')
            ->whereIn('supplier_invoice_generals.status', ['approve', 'done', 'return-all'])
            ->whereDate('supplier_invoice_generals.date', '<=', $end_period)
            ->select('supplier_invoice_generals.*')
            ->groupBy('supplier_invoice_generals.id')
            ->get();

        $vendors = $vendors->map(function ($vendor) use (
            $transactions,
            $start_period,
            $end_period,
            $item_receiving_reports,
            $purchase_return_beginnings,
            $purchase_return_currents,
            $supplier_invoice_generals,
            $purchase_return_histories_beginnings,
            $purchase_return_histories_currents
        ) {
            $beginning_in = $item_receiving_reports->where('vendor_id', $vendor->id)
                ->filter(function ($transaction) use ($start_period) {
                    return Carbon::parse($transaction->date_receive)->lt(Carbon::parse($start_period));
                })
                ->map(function ($transaction) {
                    return $transaction->total * $transaction->exchange_rate;
                })->sum();

            $beginning_out = $transactions->where('vendor_id', $vendor->id)
                ->filter(function ($transaction) use ($start_period) {
                    return Carbon::parse($transaction->date)->lt(Carbon::parse($start_period));
                })
                ->map(function ($transaction) {
                    return $transaction->pay_amount * $transaction->exchange_rate;
                })
                ->sum();

            $purchase_return_beginning = $purchase_return_beginnings->where('vendor_id', $vendor->id)
                ->map(function ($transaction) {
                    $outstanding = $transaction->total;
                    return $outstanding * $transaction->exchange_rate;
                })->sum();

            $purchase_return_history_beginning = $purchase_return_histories_beginnings->where('vendor_id', $vendor->id)
                ->sum('total_exchanged');

            $supplier_invoice_general = $supplier_invoice_generals->where('vendor_id', $vendor->id)
                ->filter(function ($transaction) use ($start_period) {
                    return Carbon::parse($transaction->date_receive)->lt(Carbon::parse($start_period));
                })
                ->map(function ($transaction) {
                    return $transaction->debit * $transaction->exchange_rate;
                })
                ->sum();

            $beginning = $beginning_in + $supplier_invoice_general - $beginning_out - $purchase_return_beginning + $purchase_return_history_beginning;

            $current_in = $item_receiving_reports->where('vendor_id', $vendor->id)
                ->filter(function ($transaction) use ($start_period, $end_period) {
                    return Carbon::parse($transaction->date_receive)->gte(Carbon::parse($start_period)) && Carbon::parse($transaction->date_receive)->lte(Carbon::parse($end_period));
                })
                ->map(function ($transaction) {
                    return $transaction->total * $transaction->exchange_rate;
                })
                ->sum();

            $current_out = $transactions->where('vendor_id', $vendor->id)
                ->filter(function ($transaction) use ($start_period, $end_period) {
                    return Carbon::parse($transaction->date)->gte(Carbon::parse($start_period)) && Carbon::parse($transaction->date)->lte(Carbon::parse($end_period));
                })
                ->map(function ($transaction) {
                    return $transaction->pay_amount * $transaction->exchange_rate;
                })
                ->sum();

            $purchase_return_current = $purchase_return_currents->where('vendor_id', $vendor->id)
                ->map(function ($transaction) {
                    $outstanding = $transaction->total;
                    return $outstanding * $transaction->exchange_rate;
                })->sum();

            $current_out += $purchase_return_current;

            $purchase_return_histories_current = $purchase_return_histories_currents->where('vendor_id', $vendor->id)
                ->sum('total_exchanged');

            $current_in += $purchase_return_histories_current;

            $final_balance = $beginning + $current_in - $current_out;

            $vendor->beginning = $beginning;
            $vendor->current_in = $current_in;
            $vendor->current_out = $current_out;
            $vendor->final_balance = $final_balance;

            return $vendor;
        });

        $return['data'] = collect($vendors);
        $return['type'] = $type;
        $return['from_date'] = Carbon::parse($start_period);
        $return['to_date'] = Carbon::parse($end_period);
        $return['currency'] = Currency::find($request->currency_id);
        $return['vendor'] = Vendor::find($request->vendor_id);

        return $return;
    }

    // ! GET SUMMARY UANG MUKA PEMBELIAN
    public function summary_uang_muka_pembelian($type, $request)
    {
        $from_date = Carbon::parse($request->from_date);
        $to_date = Carbon::parse($request->to_date);

        $data = DB::table('cash_advance_payments')
            ->join('currencies', 'currencies.id', 'cash_advance_payments.currency_id')
            ->join('vendors', function ($vendor) {
                $vendor->on('vendors.id', 'cash_advance_payments.to_id')
                    ->where('cash_advance_payments.to_model', 'App\Models\Vendor');
            })
            ->join('bank_code_mutations', function ($bank) {
                $bank
                    ->on('bank_code_mutations.ref_id', 'cash_advance_payments.id')
                    ->where('bank_code_mutations.ref_model', CashAdvancePayment::class);
            })
            ->leftJoin('cash_advance_payment_details as cash_advance', function ($join) {
                $join->on('cash_advance.cash_advance_payment_id', 'cash_advance_payments.id')
                    ->where('cash_advance.type', 'cash_advance');
            })
            ->leftJoin('cash_advance_payment_details as cash_advance_tax', function ($join) {
                $join->on('cash_advance_tax.cash_advance_payment_id', 'cash_advance_payments.id')
                    ->where('cash_advance_tax.type', 'tax');
            })
            ->join('fund_submissions', function ($query) {
                $query->on('fund_submissions.id', 'cash_advance_payments.fund_submission_id')
                    ->where('fund_submissions.status', '!=', 'void');
            })
            ->leftJoin('purchases', 'purchases.id', '=', 'fund_submissions.purchase_id')
            ->leftJoin('purchase_down_payments', 'purchase_down_payments.id', '=', 'fund_submissions.purchase_down_payment_id')
            ->where('cash_advance_payments.status', '!=', 'void')
            ->when($request->vendor_id, function ($q) use ($request) {
                $q->where('cash_advance_payments.to_id', $request->vendor_id);
            })
            ->when($to_date, function ($q) use ($to_date) {
                $q->whereDate('cash_advance_payments.date', '<=', $to_date);
            })
            ->selectRaw(
                'vendors.id as vendor_id,
                vendors.nama as vendor_nama,
                bank_code_mutations.code as bank_code,
                fund_submissions.code as fund_submission_code,
                fund_submissions.id as fund_submission_id,
                cash_advance_payments.date as cash_advance_date,
                currencies.nama as currency_nama,
                currencies.kode as currency_kode,
                cash_advance_payments.exchange_rate,
                (cash_advance.debit + COALESCE(cash_advance_tax.debit, 0)) as cash_advance_amount,
                cash_advance_payments.returned_amount,
                (cash_advance.debit + COALESCE(cash_advance_tax.debit, 0)) - cash_advance_payments.returned_amount as cash_advance_remaining_amount,
                ((cash_advance.debit + COALESCE(cash_advance_tax.debit, 0)) * cash_advance_payments.exchange_rate) as cash_advance_amount_exchanged,
                (cash_advance_payments.returned_amount * cash_advance_payments.exchange_rate) as returned_amount_exchanged,
                (((cash_advance.debit + COALESCE(cash_advance_tax.debit, 0)) - cash_advance_payments.returned_amount)*cash_advance_payments.exchange_rate) as cash_advance_remaining_amount_exchanged,
                purchases.kode as purchase_kode,
                purchases.model_id as purchase_kode_id,
                purchases.model_reference as purchase_kode_model_reference,
                purchase_down_payments.code as purchase_down_payment_code,
                purchase_down_payments.id as purchase_down_payment_code_id
                '
            )
            ->groupBy('cash_advance_payments.id')
            ->get();

        $previous_data = $data->filter(function ($d) use ($from_date) {
            return Carbon::parse($d->cash_advance_date)->lt($from_date);
        });

        $current_data = $data->filter(function ($d) use ($from_date, $to_date) {
            return Carbon::parse($d->cash_advance_date)->gte($from_date) && Carbon::parse($d->cash_advance_date)->lte($to_date);
        });

        $group_by_vendors = $data->unique('vendor_id');

        $return_data = $group_by_vendors->map(function ($d) use ($previous_data, $current_data) {
            $vendor_data = new stdClass();
            $vendor_data->vendor_id = $d->vendor_id;
            $vendor_data->vendor_nama = $d->vendor_nama;
            $vendor_data->previous_balance = $previous_data->where('vendor_id', $d->vendor_id)->sum('cash_advance_remaining_amount');
            $vendor_data->previous_balance_exchanged = $previous_data->where('vendor_id', $d->vendor_id)->sum('cash_advance_remaining_amount_exchanged');

            $final_balance = $vendor_data->previous_balance;
            $final_balance_exchanged = $vendor_data->previous_balance_exchanged;
            $vendor_data->current_data = $current_data->where('vendor_id', $d->vendor_id)->map(function ($d) use ($final_balance, $final_balance_exchanged) {
                $d->final_balance = $final_balance + $d->cash_advance_remaining_amount;
                $d->final_balance_exchanged = $final_balance_exchanged + $d->cash_advance_remaining_amount_exchanged;

                $final_balance = $d->final_balance;
                $final_balance_exchanged = $d->final_balance_exchanged;

                return $d;
            });

            $vendor_data->final_balance = $final_balance;
            $vendor_data->final_balance_exchanged = $final_balance_exchanged;
            $vendor_data->total_cash_advance = $vendor_data->current_data->sum('cash_advance_amount_exchanged');
            $vendor_data->total_used = $vendor_data->current_data->sum('returned_amount_exchanged');

            return $vendor_data;
        });

        $return['data'] = $return_data;
        $return['type'] = $type;
        $return['from_date'] = Carbon::parse($request->from_date);
        $return['to_date'] = Carbon::parse($request->to_date);
        $return['vendor'] = Vendor::find($request->vendor_id);

        return $return;
    }

    // ! GET KARTU HUTANG
    public function kartu_hutang($type, $request)
    {
        $from_date = Carbon::parse($request->from_date);
        $to_date = Carbon::parse($request->to_date);
        $currency_id = $request->currency_id;

        $data = DB::table('vendors')
            ->when($request->vendor_id, function ($q) use ($request) {
                $q->where('id', $request->vendor_id);
            })
            ->select('vendors.*')
            ->orderBy('vendors.nama');

        $data = $data->get();

        $data->each(function ($d) use ($from_date, $to_date, $currency_id) {
            $balance = $this->get_debt_card_balance(Carbon::parse($from_date), Carbon::parse($to_date), $d->id, $currency_id);
            $d->current_data = $this->get_debt_card($from_date, $to_date, $d->id, $currency_id);
            $d->beginning_balance = $balance->balance;
            $d->beginning_balance_exchanged = $balance->balance_exchanged;
        });

        $data = $data->filter(function ($d) {
            return $d->current_data->count() != 0 || $d->beginning_balance != 0;
        });

        $return['data'] = $data;
        $return['type'] = $type;
        $return['from_date'] = Carbon::parse($request->from_date);
        $return['to_date'] = Carbon::parse($request->to_date);
        $return['vendor'] = Vendor::find($request->vendor_id);

        return $return;
    }

    public function get_debt_card($from_date, $to_date, $vendor_id, $currency_id = null)
    {
        $data = [];

        // IN
        $item_receiving_reports = DB::table('item_receiving_reports')
            ->leftJoin('supplier_invoice_details', 'supplier_invoice_details.item_receiving_report_id', 'item_receiving_reports.id')
            ->leftJoin('supplier_invoices', function ($supplier_invoice) {
                $supplier_invoice->on('supplier_invoice_details.supplier_invoice_id', 'supplier_invoices.id');
            })
            ->groupBy('item_receiving_reports.id')
            ->where('item_receiving_reports.vendor_id', $vendor_id)
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('item_receiving_reports.date_receive', '>=', $from_date);
            })
            ->when($to_date, function ($q) use ($to_date) {
                $q->whereDate('item_receiving_reports.date_receive', '<=', $to_date);
            })
            ->when($currency_id, function ($q) use ($currency_id) {
                $q->where('item_receiving_reports.currency_id', $currency_id);
            })
            ->whereIn('item_receiving_reports.status', ['approve', 'done', 'return-all'])
            ->leftJoin('item_receiving_report_details', 'item_receiving_report_details.item_receiving_report_id', 'item_receiving_reports.id')
            ->select('item_receiving_reports.*')
            ->groupBy('item_receiving_reports.id')
            ->whereNull('item_receiving_reports.deleted_at')
            ->get();

        $po_tradings = DB::table('purchase_orders')
            ->whereIn('id', $item_receiving_reports->pluck('reference_id')->toArray())
            ->get();

        $po_generals = DB::table('purchase_order_generals')
            ->whereIn('id', $item_receiving_reports->pluck('reference_id')->toArray())
            ->get();

        $po_services = DB::table('purchase_order_services')
            ->whereIn('id', $item_receiving_reports->pluck('reference_id')->toArray())
            ->get();

        $po_transports = DB::table('purchase_transports')
            ->whereIn('id', $item_receiving_reports->pluck('reference_id')->toArray())
            ->get();

        foreach ($item_receiving_reports as $key => $item_receiving_report) {
            $tipe = $item_receiving_report->tipe;
            if ($tipe == "jasa") {
                $tipe = "service";
            }
            $push_data['date'] = $item_receiving_report->date_receive;
            $push_data['transaction_code'] = $item_receiving_report->kode;
            $push_data['link'] = route("admin.item-receiving-report-{$tipe}.show", $item_receiving_report->id);
            $push_data['lpb_number'] = $this->lpb_link($item_receiving_report->kode, $item_receiving_report->id, $item_receiving_report->tipe);
            $push_data['bank_code'] = '';

            $push_data['debit'] = 0;
            $push_data['credit'] = $item_receiving_report->total;
            $push_data['exchange_rate'] = $item_receiving_report->exchange_rate;
            $push_data['debit_exchanged'] = 0;
            $push_data['credit_exchanged'] = $item_receiving_report->total *  $item_receiving_report->exchange_rate;

            if ($item_receiving_report->tipe == "trading") {
                $po_trading = $po_tradings->where('id', $item_receiving_report->reference_id)->first();

                $push_data['transaction'] = "LPB TRADING";
                $push_data['po_number'] = $this->purchase_link($po_trading->nomor_po, $po_trading->id, 'trading');
                $push_data['note'] = "";
            }

            if ($item_receiving_report->tipe == "general") {
                $po_general = $po_generals->where('id', $item_receiving_report->reference_id)->first();

                $push_data['transaction'] = "LPB GENERAL";
                $push_data['po_number'] = $this->purchase_link($po_general->code, $po_general->id, 'general');
                $push_data['note'] = "";
            }

            if ($item_receiving_report->tipe == "jasa") {
                $po_service = $po_services->where('id', $item_receiving_report->reference_id)->first();

                $push_data['transaction'] = "LPB SERVICE";
                $push_data['po_number'] = $this->purchase_link($po_service->code, $po_service->id, 'service');
                $push_data['note'] = "";
            }

            if ($item_receiving_report->tipe == "transport") {
                $po_transport = $po_transports->where('id', $item_receiving_report->reference_id)->first();

                $push_data['transaction'] = "LPB TRANSPORT";
                $push_data['po_number'] = $this->purchase_link($po_transport->kode, $po_transport->id, 'transportir');
                $push_data['note'] = "";
            }

            $data[] = (object) $push_data;
        }

        $supplier_invoice_generals = DB::table('supplier_invoice_generals')
            ->where('supplier_invoice_generals.vendor_id', $vendor_id)
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('supplier_invoice_generals.date', '>=', $from_date);
            })
            ->when($to_date, function ($q) use ($to_date) {
                $q->whereDate('supplier_invoice_generals.date', '<=', $to_date);
            })
            ->when($currency_id, function ($q) use ($currency_id) {
                $q->where('supplier_invoice_generals.currency_id', $currency_id);
            })
            ->where('supplier_invoice_generals.status', 'approve')
            ->whereNull('supplier_invoice_generals.deleted_at')
            ->selectRaw(
                'supplier_invoice_generals.*',
            )
            ->groupBy('supplier_invoice_generals.id')
            ->get();

        foreach ($supplier_invoice_generals as $key => $supplier_invoice_general) {
            $push_data['date'] = $supplier_invoice_general->date;
            $push_data['transaction_code'] = $supplier_invoice_general->code;
            $push_data['link'] = route('admin.supplier-invoice-general.show', ['supplier_invoice_general' => $supplier_invoice_general->id]);
            $push_data['transaction'] = "PI NON LPB";
            $push_data['note'] = "";
            $push_data['debit'] = ($supplier_invoice_general->debit);
            $push_data['credit'] = 0;
            $push_data['exchange_rate'] = $supplier_invoice_general->exchange_rate;
            $push_data['debit_exchanged'] = ($supplier_invoice_general->debit) * $supplier_invoice_general->exchange_rate;
            $push_data['credit_exchanged'] = 0;
            $push_data['lpb_number'] = $supplier_invoice_general->lpb_number;
            $push_data['bank_code'] = '';
            $push_data['po_number'] = '';

            $data[] = (object) $push_data;
        }


        // OUT
        $payments = SupplierInvoicePayment::join('supplier_invoices', 'supplier_invoices.id', 'supplier_invoice_payments.supplier_invoice_id')
            ->where('supplier_invoice_model', SupplierInvoice::class)
            ->where('model', '!=', 'App\Models\PurchaseReturn')
            ->where('pay_amount', '!=', 0)
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('supplier_invoice_payments.date', '>=', $from_date);
            })
            ->when($to_date, function ($q) use ($to_date) {
                $q->whereDate('supplier_invoice_payments.date', '<=', $to_date);
            })
            ->when($vendor_id, function ($q) use ($vendor_id) {
                $q->where('supplier_invoices.vendor_id', $vendor_id);
            })
            ->when(
                $currency_id,
                function ($q) use ($currency_id) {
                    $q->where('supplier_invoices.currency_id', $currency_id);
                }
            )
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->select(
                'supplier_invoice_payments.*',
                'supplier_invoices.vendor_id',
                'supplier_invoices.exchange_rate as si_exchange_rate',
                'supplier_invoices.code',
            )
            ->get();

        $payment_generals = SupplierInvoicePayment::join('supplier_invoice_generals', 'supplier_invoice_generals.id', 'supplier_invoice_payments.supplier_invoice_id')
            ->where('supplier_invoice_model', SupplierInvoiceGeneral::class)
            ->where('pay_amount', '!=', 0)
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('supplier_invoice_payments.date', '>=', $from_date);
            })
            ->when($to_date, function ($q) use ($to_date) {
                $q->whereDate('supplier_invoice_payments.date', '<=', $to_date);
            })
            ->when($vendor_id, function ($q) use ($vendor_id) {
                $q->where('supplier_invoice_generals.vendor_id', $vendor_id);
            })
            ->when(
                $currency_id,
                function ($q) use ($currency_id) {
                    $q->where('supplier_invoice_generals.currency_id', $currency_id);
                }
            )
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->select(
                'supplier_invoice_payments.*',
                'supplier_invoice_generals.vendor_id',
                'supplier_invoice_generals.exchange_rate as si_exchange_rate',
                'supplier_invoice_generals.code',
            )
            ->get();

        $payments = $payments->merge($payment_generals);

        foreach ($payments as $key => $payment) {
            $transaction_code = '';
            $link = '';
            $transaction = '';
            $lpb_number = '';
            $po_number = '';
            $bank_code = '';

            if ($payment->model) {
                switch ($payment->model) {
                    case \App\Models\SupplierInvoiceDownPayment::class:
                        $data_ref = $payment->reference_model_ref->supplier_invoice;
                        $transaction = 'PURCHASE INVOICE DOWN PAYMENT';
                        $transaction_code = $data_ref->code;
                        $link = route('admin.supplier-invoice.show', ['supplier_invoice' => $data_ref->id]);
                        $lpb_number_array = [];
                        $po_number_array = [];
                        $data_ref->detail->each(function ($d) use (&$lpb_number_array, &$po_number_array) {
                            $lpb_number_array[] = $this->lpb_link($d->item_receiving_report->kode, $d->item_receiving_report->id, $d->item_receiving_report->tipe);
                            $po_number_array[] = $this->purchase_link($d->item_receiving_report->reference->kode, $d->item_receiving_report->reference->model_id, $d->item_receiving_report->reference->tipe);
                        });

                        $lpb_number = count(collect($lpb_number_array)->filter()) > 0 ? implode('', $lpb_number_array) : '';
                        $po_number = count(collect($po_number_array)->filter()) > 0 ? implode('', $po_number_array) : '';
                        break;
                    case \App\Models\AccountPayableDetail::class:
                        $data_ref = $payment->reference_model_ref->account_payable;
                        $transaction = 'ACCOUNT PAYABLE';
                        $transaction_code = $data_ref->code;
                        $link = route('admin.account-payable.show', ['account_payable' => $data_ref->id]);
                        $lpb = $payment->reference_model_ref->item_receiving_report;
                        $lpb_number_array = [];
                        $po_number_array = [];
                        if ($lpb) {
                            $lpb_number_array = [
                                $this->lpb_link($lpb->kode, $lpb->id, $lpb->tipe)
                            ];
                            $po_number_array = [
                                $this->purchase_link($lpb->reference->purchase->kode, $lpb->reference_id, $lpb->tipe),
                            ];
                        }

                        $lpb_number = count(collect($lpb_number_array)->filter()) > 0 ? implode('', $lpb_number_array) : '';
                        $po_number = count(collect($po_number_array)->filter()) > 0 ? implode('', $po_number_array) : '';
                        $bank_code = $data_ref->bank_code_mutation ?? '';
                        break;
                    case \App\Models\CashAdvancedReturnInvoice::class:
                        $data_ref = $payment->reference_model_ref->cash_advanced_return;
                        $transaction = 'PENGEMBALIAN UANG MUKA';
                        $transaction_code = $data_ref->code;
                        $link = route('admin.cash-advance-return-vendor.show', ['cash_advance_return_vendor' => $data_ref->id]);
                        $lpb_number = '';
                        $po_number = '';

                        break;
                    case \App\Models\ReceivablesPaymentVendor::class:
                        $data_ref = $payment->reference_model_ref->receivables_payment;
                        $transaction = 'RECEIVABLE';
                        $transaction_code = $data_ref->code;
                        $link = route('admin.receivables-payment.show', ['receivables_payment' => $data_ref->id]);
                        $lpb = $payment->item_receiving_report;
                        $lpb_number_array = [];
                        $po_number_array = [];
                        if ($lpb) {
                            $lpb_number_array = [
                                $this->lpb_link($lpb->kode, $lpb->id, $lpb->tipe)
                            ];

                            $po_number_array = [
                                $this->purchase_link($lpb->reference->purchase->kode, $lpb->reference_id, $lpb->tipe),
                            ];
                        }

                        // dd($po_number_array);

                        $lpb_number = count(collect($lpb_number_array)->filter()) > 0 ? implode('', $lpb_number_array) : '';
                        $po_number = count(collect($po_number_array)->filter()) > 0 ? implode('', $po_number_array) : '';
                        $bank_code = $data_ref->bank_code_mutation ?? '';
                        break;

                    default:

                        break;
                }

                if ($lpb_number == '') {
                    if ($payment->item_receiving_report) {
                        $lpb_number = $this->lpb_link($payment->item_receiving_report->kode, $payment->item_receiving_report->id, $payment->item_receiving_report->tipe);
                        $code = $payment->item_receiving_report->reference->kode ?? $payment->item_receiving_report->reference->nomor_po ?? $payment->item_receiving_report->reference->code ?? '';
                        $po_number = $this->purchase_link($code, $payment->item_receiving_report->reference->model_id, $payment->item_receiving_report->reference->tipe);
                    }
                }
            }

            $push_data['date'] = $payment->date;
            $push_data['transaction_code'] = $transaction_code;
            $push_data['link'] = $link;
            $push_data['transaction'] = $transaction;
            $push_data['note'] = '';
            $push_data['debit'] = $payment->pay_amount;
            $push_data['credit'] = 0;
            $push_data['exchange_rate'] = $payment->exchange_rate;
            $push_data['debit_exchanged'] = $payment->pay_amount * $payment->exchange_rate;
            $push_data['credit_exchanged'] = 0;
            $push_data['lpb_number'] = $lpb_number;
            $push_data['po_number'] = $po_number;
            $push_data['bank_code'] = $bank_code;

            $data[] = (object) $push_data;
        }

        $purchase_returns = DB::table('purchase_returns')
            ->leftJoin('item_receiving_reports', 'item_receiving_reports.id', 'purchase_returns.item_receiving_report_id')
            ->where('purchase_returns.vendor_id', $vendor_id)
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('purchase_returns.date', '>=', $from_date);
            })
            ->when($to_date, function ($q) use ($to_date) {
                $q->whereDate('purchase_returns.date', '<=', $to_date);
            })
            ->when($currency_id, function ($q) use ($currency_id) {
                $q->where('purchase_returns.currency_id', $currency_id);
            })
            ->where('purchase_returns.status', 'approve')
            ->whereNull('purchase_returns.deleted_at')
            ->selectRaw(
                'purchase_returns.*,
            item_receiving_reports.kode as lpb_number,
            item_receiving_reports.reference_model as lpb_reference_model,
            item_receiving_reports.reference_id as lpb_reference_id,
            item_receiving_reports.tipe as lpb_reference_tipe'
            )
            ->groupBy('purchase_returns.id')
            ->get();

        foreach ($purchase_returns as $key => $purchase_return) {
            $push_data['date'] = $purchase_return->date;
            $push_data['transaction_code'] = $purchase_return->code;
            $push_data['link'] = route('admin.purchase-return.show', ['purchase_return' => $purchase_return->id]);
            $push_data['transaction'] = "RETUR PEMBELIAN";
            $push_data['note'] = "";
            $push_data['debit'] = 0;
            $push_data['credit'] = $purchase_return->total * -1;
            $push_data['exchange_rate'] = $purchase_return->exchange_rate;
            $push_data['debit_exchanged'] = 0;
            $push_data['credit_exchanged'] = $purchase_return->total * $purchase_return->exchange_rate * -1;
            $push_data['lpb_number'] = $this->lpb_link(
                $purchase_return->lpb_number,
                $purchase_return->lpb_reference_id,
                $purchase_return->lpb_reference_tipe,
            );
            $push_data['bank_code'] = '';
            $po_data = $purchase_return->lpb_reference_model::find($purchase_return->lpb_reference_id);
            $push_data['po_number'] = $this->purchase_link(
                $po_data->nomor_po ?? $po_data->code ?? $po_data->kode ?? '',
                $po_data->id,
                $po_data->purchase->tipe ?? '',
            );

            $data[] = (object) $push_data;
        }

        $purchase_return_histories = DB::table('purchase_return_histories')
            ->whereNull('purchase_return_histories.deleted_at')
            ->join('purchase_returns', function ($query) {
                $query->on('purchase_return_histories.purchase_return_id', 'purchase_returns.id')
                    ->where('purchase_return_histories.status', 'approve')
                    ->whereNull('purchase_return_histories.deleted_at');
            })
            ->leftJoin('item_receiving_reports', 'item_receiving_reports.id', 'purchase_returns.item_receiving_report_id')
            ->where('purchase_returns.vendor_id', $vendor_id)
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('purchase_return_histories.date', '>=', $from_date);
            })
            ->when($to_date, function ($q) use ($to_date) {
                $q->whereDate('purchase_return_histories.date', '<=', $to_date);
            })
            ->when($currency_id, function ($q) use ($currency_id) {
                $q->where('purchase_returns.currency_id', $currency_id);
            })
            ->selectRaw(
                'purchase_returns.exchange_rate,
                purchase_returns.code,
                purchase_returns.id,
                purchase_return_histories.date,
            purchase_return_histories.amount as total,
            purchase_return_histories.amount * purchase_returns.exchange_rate as total_exchanged,
            item_receiving_reports.kode as lpb_number,
            item_receiving_reports.reference_model as lpb_reference_model,
            item_receiving_reports.reference_id as lpb_reference_id,
            item_receiving_reports.tipe as lpb_reference_tipe'
            )
            ->groupBy('purchase_returns.id')
            ->get();

        foreach ($purchase_return_histories as $key => $purchase_return) {
            $push_data['date'] = $purchase_return->date;
            $push_data['transaction_code'] = $purchase_return->code;
            $push_data['link'] = route('admin.purchase-return.show', ['purchase_return' => $purchase_return->id]);
            $push_data['transaction'] = "PENGEMBALIAN RETUR PEMBELIAN";
            $push_data['note'] = "";
            $push_data['debit'] = 0;
            $push_data['credit'] = $purchase_return->total;
            $push_data['exchange_rate'] = $purchase_return->exchange_rate;
            $push_data['debit_exchanged'] = 0;
            $push_data['credit_exchanged'] = $purchase_return->total * $purchase_return->exchange_rate;
            $push_data['lpb_number'] = $this->lpb_link(
                $purchase_return->lpb_number,
                $purchase_return->lpb_reference_id,
                $purchase_return->lpb_reference_tipe
            );
            $push_data['bank_code'] = '';
            $po_data = $purchase_return->lpb_reference_model::find($purchase_return->lpb_reference_id);
            $push_data['po_number'] = $this->purchase_link(
                $po_data->nomor_po ?? $po_data->code ?? $po_data->kode ?? '',
                $po_data->id,
                $po_data->purchase->tipe ?? '',
            );
            $data[] = (object) $push_data;
        }

        $data = collect($data)->sortBy('date');

        return $data;
    }

    public function get_debt_card_balance($from_date, $to_date, $vendor_id, $currency_id = null)
    {
        // IN
        $item_receiving_reports = DB::table('item_receiving_reports')
            ->groupBy('item_receiving_reports.id')
            ->where('item_receiving_reports.vendor_id', $vendor_id)
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('item_receiving_reports.date_receive', '<', $from_date);
            })
            ->when($vendor_id, function ($q) use ($vendor_id) {
                $q->where('item_receiving_reports.vendor_id', $vendor_id);
            })
            ->when($currency_id, function ($q) use ($currency_id) {
                $q->where('item_receiving_reports.currency_id', $currency_id);
            })
            ->whereIn('item_receiving_reports.status', ['approve', 'done', 'return-all'])
            ->whereNull('item_receiving_reports.deleted_at')
            ->selectRaw(
                'item_receiving_reports.total as total,
                item_receiving_reports.total * item_receiving_reports.exchange_rate as total_exchanged'
            )
            ->get();

        $supplier_invoice_generals = DB::table('supplier_invoice_generals')
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('supplier_invoice_generals.date', '<', $from_date);
            })
            ->when($vendor_id, function ($q) use ($vendor_id) {
                $q->where('supplier_invoice_generals.vendor_id', $vendor_id);
            })
            ->when($currency_id, function ($q) use ($currency_id) {
                $q->where('supplier_invoice_generals.currency_id', $currency_id);
            })
            ->whereIn('supplier_invoice_generals.status', ['approve'])
            ->whereNull('supplier_invoice_generals.deleted_at')
            ->selectRaw(
                'supplier_invoice_generals.debit as total,
                supplier_invoice_generals.debit * supplier_invoice_generals.exchange_rate as total_exchanged'
            )
            ->get();

        // OUT
        $payments = SupplierInvoicePayment::join('supplier_invoices', 'supplier_invoices.id', 'supplier_invoice_payments.supplier_invoice_id')
            ->where('supplier_invoice_model', SupplierInvoice::class)
            ->where('pay_amount', '!=', 0)
            ->leftJoin('account_payable_details', function ($join) {
                $join->on('account_payable_details.id', 'supplier_invoice_payments.reference_id')
                    ->where('supplier_invoice_payments.model', AccountPayableDetail::class);
            })
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('supplier_invoice_payments.date', '<', $from_date);
            })
            ->when($vendor_id, function ($q) use ($vendor_id) {
                $q->where('supplier_invoices.vendor_id', $vendor_id);
            })
            ->when($currency_id, function ($q) use ($currency_id) {
                $q->where('supplier_invoice_payments.currency_id', $currency_id);
            })
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->selectRaw('
                supplier_invoice_payments.pay_amount as total,
                supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate as total_exchanged')
            ->get();

        $payment_generals = SupplierInvoicePayment::join('supplier_invoice_generals', 'supplier_invoice_generals.id', 'supplier_invoice_payments.supplier_invoice_id')
            ->where('supplier_invoice_model', SupplierInvoiceGeneral::class)
            ->where('pay_amount', '!=', 0)
            ->leftJoin('account_payable_details', function ($join) {
                $join->on('account_payable_details.id', 'supplier_invoice_payments.reference_id')
                    ->where('supplier_invoice_payments.model', AccountPayableDetail::class);
            })
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('supplier_invoice_payments.date', '<', $from_date);
            })
            ->when($vendor_id, function ($q) use ($vendor_id) {
                $q->where('supplier_invoice_generals.vendor_id', $vendor_id);
            })
            ->when($currency_id, function ($q) use ($currency_id) {
                $q->where('supplier_invoice_payments.currency_id', $currency_id);
            })
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->selectRaw('
                supplier_invoice_payments.pay_amount as total,
                supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate as total_exchanged')
            ->get();

        $purchase_returns = DB::table('purchase_returns')
            ->when($vendor_id, function ($q) use ($vendor_id) {
                $q->where('purchase_returns.vendor_id', $vendor_id);
            })
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('purchase_returns.date', '<', $from_date);
            })
            ->when($currency_id, function ($q) use ($currency_id) {
                $q->where('purchase_returns.currency_id', $currency_id);
            })
            ->where('purchase_returns.status', 'approve')
            ->whereNull('purchase_returns.deleted_at')
            ->selectRaw(
                'purchase_returns.total as total,
            (purchase_returns.total) * purchase_returns.exchange_rate as total_exchanged'
            )
            ->groupBy('purchase_returns.id')
            ->get();

        $purchase_return_histories = DB::table('purchase_return_histories')
            ->whereNull('purchase_return_histories.deleted_at')
            ->join('purchase_returns', function ($join) {
                $join->on('purchase_returns.id', 'purchase_return_histories.purchase_return_id')
                    ->whereNull('purchase_returns.deleted_at')
                    ->where('purchase_returns.status', 'approve');
            })
            ->where('purchase_returns.status', 'approve')
            ->whereNull('purchase_returns.deleted_at')
            ->when($vendor_id, function ($q) use ($vendor_id) {
                $q->where('purchase_returns.vendor_id', $vendor_id);
            })
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('purchase_return_histories.date', '<', $from_date);
            })
            ->when($currency_id, function ($q) use ($currency_id) {
                $q->where('purchase_returns.currency_id', $currency_id);
            })
            ->selectRaw(
                'purchase_return_histories.amount as total,
            (purchase_return_histories.amount) * purchase_returns.exchange_rate as total_exchanged'
            )
            ->groupBy('purchase_return_histories.id')
            ->get();

        $balance = $item_receiving_reports->sum('total');
        $balance += $supplier_invoice_generals->sum('total');
        $balance -= $payments->sum('total');
        $balance -= $payment_generals->sum('total');
        $balance -= $purchase_returns->sum('total');
        $balance += $purchase_return_histories->sum('total');

        $balance_exchanged = $item_receiving_reports->sum('total_exchanged');
        $balance_exchanged += $supplier_invoice_generals->sum('total_exchanged');
        $balance_exchanged -= $payments->sum('total_exchanged');
        $balance_exchanged -= $payment_generals->sum('total_exchanged');
        $balance_exchanged -= $purchase_returns->sum('total_exchanged');
        $balance_exchanged += $purchase_return_histories->sum('total_exchanged');

        $class = new stdClass();
        $class->balance = $balance;
        $class->balance_exchanged = $balance_exchanged;

        return $class;
    }

    public function purchase_link($code, $id, $type)
    {
        try {
            $link = '';
            if ($id) {
                switch ($type) {
                    case 'trading':
                        $link = route('admin.purchase-order.show', ['purchase_order' => $id]);
                        break;
                    case 'general':
                        $link = route('admin.purchase-order-general.show', ['purchase_order_general' => $id]);
                        break;
                    case 'jasa':
                        $link = route('admin.purchase-order-service.show', ['purchase_order_service' => $id]);
                        break;
                    case 'transportir':
                        $link = route('admin.purchase-order-transport.show', ['purchase_order_transport' => $id]);
                        break;

                    default:
                        # code...
                        break;
                }

                if ($link) {
                    return "<p class='m-0'><a href='$link' target='_blank'>$code</a></p>";
                } else {
                    return $code;
                }
            }
        } catch (\Throwable $th) {
            return $code;
        }
    }

    public function lpb_link($code, $id, $type)
    {
        try {
            $link = '';
            if ($id) {
                switch ($type) {
                    case 'trading':
                        $link = route('admin.item-receiving-report-trading.show', ['item_receiving_report_trading' => $id]);
                        break;
                    case 'general':
                        $link = route('admin.item-receiving-report-general.show', ['item_receiving_report_general' => $id]);
                        break;
                    case 'jasa':
                        $link = route('admin.item-receiving-report-service.show', ['item_receiving_report_service' => $id]);
                        break;
                    case 'transport':
                        $link = route('admin.item-receiving-report-transport.show', ['item_receiving_report_transport' => $id]);
                        break;

                    default:
                        # code...
                        break;
                }

                if ($link) {
                    return "<p class='m-0'><a href='$link' target='_blank'>$code</a></p>";
                } else {
                    return $code;
                }
            }
        } catch (\Throwable $th) {
            return $code;
        }
    }

    // ! GET NERACA SALDO
    public function neraca_saldo($data)
    {
        $data_array = [];
        foreach ($data['childs'] as $key => $value) {
            $push['indent'] = $value['indent'];
            $push['name'] = strtoupper($value['name']);
            $push['code'] = $value['code'];
            $push['debit'] = $value['debit'];
            $push['credit'] = $value['credit'];
            $push['balance'] = $value['balance'];
            $push['total_debit'] = 0;
            $push['total_credit'] = 0;
            $push['total_balance'] = 0;
            $push['is_parent'] = count($value['childs']) > 0;
            $push['is_total'] = false;
            array_push($data_array, $push);
            foreach ($this->reformat_neraca_saldo($value) as $key => $v) {
                if (($v['debit'] != 0 || $v['credit'] != 0 || $v['balance'] != 0) || $v['is_total'] || $v['is_parent']) {
                    array_push($data_array, $v);
                }
            }
            if (count($value['childs']) > 0) {
                $push_total['indent'] = 0;
                $push_total['name'] = 'TOTAL ' . strtoupper($value['name']);
                $push_total['code'] = '';
                $push_total['debit'] = 0;
                $push_total['credit'] = 0;
                $push_total['balance'] = 0;
                $push_total['total_debit'] = $this->calculateTotalBalance($value, 'debit');
                $push_total['total_credit'] = $this->calculateTotalBalance($value, 'credit');
                $push_total['total_balance'] = $this->calculateTotalBalance($value, 'balance');
                $push_total['is_parent'] = false;
                $push_total['is_total'] = true;
                array_push($data_array, $push_total);
            }
        }

        return $data_array;
    }

    public function reformat_neraca_saldo($data)
    {
        $array = [];
        foreach ($data['childs'] as $key => $value) {
            $push['indent'] = $value['indent'];
            $push['name'] = strtoupper($value['name']);
            $push['code'] = $value['code'];
            $push['debit'] = $value['debit'];
            $push['credit'] = $value['credit'];
            $push['balance'] = $value['balance'];
            $push['total_debit'] = 0;
            $push['total_credit'] = 0;
            $push['total_balance'] = 0;
            $push['is_parent'] = count($value['childs']) > 0;
            $push['is_total'] = false;
            array_push($array, $push);
            foreach ($this->reformat_neraca_saldo($value) as $key => $v) {
                if (($v['debit'] != 0 || $v['credit'] != 0 || $v['balance'] != 0) || $v['is_total'] || $v['is_parent']) {
                    array_push($array, $v);
                }
            }
            if (count($value['childs']) > 0) {
                $push_total['indent'] = 0;
                $push_total['name'] = 'TOTAL ' . strtoupper($value['name']);
                $push_total['code'] = '';
                $push_total['debit'] = 0;
                $push_total['credit'] = 0;
                $push_total['balance'] = 0;
                $push_total['total_debit'] = $this->calculateTotalBalance($value, 'debit');
                $push_total['total_credit'] = $this->calculateTotalBalance($value, 'credit');
                $push_total['total_balance'] = $this->calculateTotalBalance($value, 'balance');
                $push_total['is_parent'] = false;
                $push_total['is_total'] = true;
                array_push($array, $push_total);
            }
        }

        return $array;
    }

    // ! GET BUKU BESAR
    public function buku_besar($type, $request)
    {
        // Fetch the start and end COAs
        $startCoa = isset($request['coa_id_start']) ? Coa::find($request['coa_id_start']) : null;
        $endCoa = isset($request['coa_id_end']) ? Coa::find($request['coa_id_end']) : null;

        $coas = DB::table('coas')
            ->whereNull('coas.deleted_at')
            ->where('is_parent', 0)
            ->when($startCoa, function ($q) use ($startCoa) {
                $q->where('coas.account_code', '>=', $startCoa->account_code);
            })
            ->when($endCoa, function ($q) use ($endCoa) {
                $q->where('coas.account_code', '<=', $endCoa->account_code);
            })
            ->orderBy('account_code', 'asc')
            ->leftJoin('journal_details', function ($query) use ($request) {
                $query->on('journal_details.coa_id', 'coas.id')
                    ->join('journals', 'journals.id', 'journal_details.journal_id')
                    ->where('journals.status', 'approve')
                    ->whereNull('journals.deleted_at');
            })
            ->where(function ($row) {
                $row->whereNotNull('journal_details.id');
            })
            ->groupBy('coas.id')
            ->selectRaw('coas.*')
            ->get();

        $amount_before_exchanged = DB::table('journal_details')
            ->whereIn('journal_details.coa_id', $coas->pluck('id')->toArray())
            ->join('coas', 'coas.id', 'journal_details.coa_id')
            ->join('journals', 'journals.id', 'journal_details.journal_id')
            ->whereNull('journals.deleted_at')
            ->where('journals.status', 'approve')
            ->whereDate('journals.date', '<', Carbon::parse($request['from_date']))
            ->groupBy('coas.id')
            ->selectRaw('(COALESCE(SUM(journal_details.debit_exchanged),0) - COALESCE(SUM(journal_details.credit_exchanged),0)) amount_before_exchanged, coas.id')
            ->get();

        $journals = DB::table('journal_details as jd')
            ->join('coas', 'coas.id', 'jd.coa_id')
            ->join('journals as j', 'j.id', 'jd.journal_id')
            ->whereIn('jd.coa_id', $coas->pluck('id')->toArray())
            ->whereNull('j.deleted_at')
            ->where('j.status', 'approve')
            ->whereDate('j.date', '>=', Carbon::parse($request['from_date']))
            ->whereDate('j.date', '<=', Carbon::parse($request['to_date']))
            ->orderBy('j.date')
            ->orderBy('jd.ordering')
            ->leftJoin('customers', 'customers.id', 'j.customer_id')
            ->leftJoin('vendors', 'vendors.id', 'j.vendor_id')
            ->selectRaw(
                'jd.*,
            j.reference,
            j.id as j_id,
            j.date as journal_date,
            jd.exchange_rate,
            j.document_reference,
            j.remark as journal_remark,
            coas.account_code,
            coas.name as coa_name,
            coas.normal_balance,
            customers.nama as customer_nama,
            vendors.nama as vendor_name'
            )
            ->get();


        $journals->each(function ($j) {
            $j->reference = json_decode($j->reference);
            $j->document_reference = json_decode($j->document_reference);
        });

        foreach ($coas as $coa) {
            $coa->amount_before_exchanged = $amount_before_exchanged->where('id', $coa->id)->first()->amount_before_exchanged ?? 0;
            $coa->details = $journals->where('coa_id', $coa->id)->values();
        }

        $return['data'] = $coas;
        $return['type'] = $type;
        $return['from_date'] = Carbon::parse($request['from_date']);
        $return['to_date'] = Carbon::parse($request['to_date']);
        $return['coa'] = [$startCoa, $endCoa];

        return $return;
    }



    // ! GET NERACA MULTI PERIOD
    public function neraca_multiperiod($data)
    {
        $data_array = [];
        foreach ($data['childs'] as $key => $value) {
            $push['indent'] = $value['indent'];
            $push['name'] = strtoupper($value['name']);
            $push['code'] = $value['code'];
            $push['balance'] = $value['balance'];
            $push['total_balance'] = $value['balance'];
            $push['is_parent'] = count($value['childs']) > 0;
            $push['is_total'] = false;
            array_push($data_array, $push);
            foreach ($this->reformat_neraca_multiperiod($value) as $key => $v) {
                array_push($data_array, $v);
            }
            if (count($value['childs']) > 0) {
                $total_balance[1] = 0;
                $total_balance[2] = 0;
                $total_balance[3] = 0;
                $total_balance[4] = 0;
                $total_balance[5] = 0;
                $total_balance[6] = 0;
                $total_balance[7] = 0;
                $total_balance[8] = 0;
                $total_balance[9] = 0;
                $total_balance[10] = 0;
                $total_balance[11] = 0;
                $total_balance[12] = 0;
                foreach (array_column($value['childs'], 'balance') as $key => $child) {
                    foreach ($child as $key => $balance) {
                        $total_balance[$key] += $balance;
                    }
                }
                $push_total['indent'] = 0;
                $push_total['name'] = 'TOTAL ' . strtoupper($value['name']);
                $push_total['code'] = '';
                $push_total['balance'] = $value['balance'];
                $push_total['total_balance'] = $total_balance;
                $push_total['is_parent'] = false;
                $push_total['is_total'] = true;
                array_push($data_array, $push_total);
            }
        }

        return $data_array;
    }

    public function reformat_neraca_multiperiod($data)
    {
        $array = [];
        foreach ($data['childs'] as $key => $value) {
            $push['indent'] = $value['indent'];
            $push['name'] = strtoupper($value['name']);
            $push['code'] = $value['code'];
            $push['balance'] = $value['balance'];
            $push['total_balance'] = $value['balance'];
            $push['is_parent'] = count($value['childs']) > 0;
            $push['is_total'] = false;
            array_push($array, $push);
            foreach ($this->reformat_neraca_multiperiod($value) as $key => $v) {
                array_push($array, $v);
            }
            if (count($value['childs']) > 0) {
                $total_balance[1] = 0;
                $total_balance[2] = 0;
                $total_balance[3] = 0;
                $total_balance[4] = 0;
                $total_balance[5] = 0;
                $total_balance[6] = 0;
                $total_balance[7] = 0;
                $total_balance[8] = 0;
                $total_balance[9] = 0;
                $total_balance[10] = 0;
                $total_balance[11] = 0;
                $total_balance[12] = 0;
                foreach (array_column($value['childs'], 'balance') as $key => $child) {
                    foreach ($child as $key => $balance) {
                        $total_balance[$key] += $balance;
                    }
                }
                $push_total['indent'] = 0;
                $push_total['name'] = 'TOTAL ' . strtoupper($value['name']);
                $push_total['code'] = '';
                $push_total['balance'] = $value['balance'];
                $push_total['total_balance'] = $total_balance;
                $push_total['is_parent'] = false;
                $push_total['is_total'] = true;
                array_push($array, $push_total);
            }
        }

        return $array;
    }

    // ! GET NERACA
    public function neraca($data)
    {
        $data_array = [];
        foreach ($data['childs'] as $key => $value) {
            $push['indent'] = $value['indent'];
            $push['name'] = strtoupper($value['name']);
            $push['code'] = $value['code'];
            $push['balance'] = $value['balance'];
            $push['prev_balance'] = $value['prev_balance'] ?? 0;
            $push['total_balance'] = 0;
            $push['total_prev_balance'] = 0;
            $push['is_parent'] = count($value['childs']) > 0;
            $push['is_total'] = false;
            array_push($data_array, $push);
            foreach ($this->reformat_neraca($value) as $key => $v) {
                if (($v['balance'] != 0 || $v['prev_balance'] != 0) || $v['is_total'] || $v['is_parent']) {
                    array_push($data_array, $v);
                }
            }
            if (count($value['childs']) > 0) {
                $push_total['indent'] = 0;
                $push_total['name'] = 'TOTAL ' . strtoupper($value['name']);
                $push_total['code'] = '';
                $push_total['balance'] = 0;
                $push_total['prev_balance'] = 0;
                $push_total['total_balance'] = $this->calculateTotalBalance($value, 'balance');
                $push_total['total_prev_balance'] = $this->calculateTotalBalance($value, 'prev_balance');
                $push_total['is_parent'] = false;
                $push_total['is_total'] = true;
                array_push($data_array, $push_total);
            }
        }

        return $data_array;
    }

    public function reformat_neraca($data)
    {
        $array = [];
        foreach ($data['childs'] as $key => $value) {
            $push['indent'] = $value['indent'];
            $push['name'] = strtoupper($value['name']);
            $push['code'] = $value['code'];
            $push['balance'] = $value['balance'];
            $push['prev_balance'] = $value['prev_balance'] ?? 0;
            $push['total_balance'] = 0;
            $push['total_prev_balance'] = 0;
            $push['is_parent'] = count($value['childs']) > 0;
            $push['is_total'] = false;
            array_push($array, $push);
            foreach ($this->reformat_neraca($value) as $key => $v) {
                if (($v['balance'] != 0 || $v['prev_balance'] != 0) || $v['is_total'] || $v['is_parent']) {
                    array_push($array, $v);
                }
            }
            if (count($value['childs']) > 0) {
                $push_total['indent'] = 0;
                $push_total['name'] = 'TOTAL ' . strtoupper($value['name']);
                $push_total['code'] = '';
                $push_total['balance'] = 0;
                $push_total['prev_balance'] = 0;
                $push_total['total_balance'] = $this->calculateTotalBalance($value, 'balance');
                $push_total['total_prev_balance'] = $this->calculateTotalBalance($value, 'prev_balance');
                $push_total['is_parent'] = false;
                $push_total['is_total'] = true;
                array_push($array, $push_total);
            }
        }

        return $array;
    }

    function calculateTotalBalance($node, $column)
    {
        $total = $node[$column] ?? 0;

        if (isset($node['childs']) && is_array($node['childs'])) {
            foreach ($node['childs'] as $child) {
                $total += $this->calculateTotalBalance($child, $column);
            }
        }

        return $total;
    }

    /**
     * Cash bond report
     *
     * @param $request
     *
     * @return array|Collection
     */
    public function cashBond($request): array
    {
        $cash_bonds = DB::table('cash_bonds')
            ->leftJoin('employees', 'cash_bonds.employee_id', '=', 'employees.id')
            // ->leftJoin('branches', 'cash_bonds.branch_id', '=', 'branches.id')
            ->whereNull('cash_bonds.deleted_at')
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                return $query->where('cash_bonds.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('cash_bonds.branch_id', get_current_branch()->id);
            })
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('cash_bonds.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('cash_bonds.date', '<=', Carbon::parse($request->to_date));
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('cash_bonds.status', $request->status);
            })
            ->when($request->employee_id, function ($query) use ($request) {
                return $query->where('cash_bonds.employee_id', $request->employee_id);
            })
            ->distinct('cash_bonds.id')
            ->selectRaw('
                cash_bonds.id,
                cash_bonds.date,
                cash_bonds.code,
                cash_bonds.status,
                cash_bonds.description,
                cash_bonds.exchange_rate,

                employees.name as employee_name,
                employees.NIK as employee_nik
            ')
            ->get();

        $cash_bond_ids = $cash_bonds->pluck('id')->toArray();
        $cash_bond_details = DB::table('cash_bond_details')
            ->whereIn('cash_bond_id', $cash_bond_ids)
            ->where('type', 'cash_bank')
            ->selectRaw('
                cash_bond_id,
                credit
            ')
            ->get();

        $cash_bond_return_details = DB::table('cash_bond_return_details')
            ->leftJoin('cash_bond_returns', 'cash_bond_return_details.cash_bond_return_id', '=', 'cash_bond_returns.id')
            ->whereIn('cash_bond_id', $cash_bond_ids)
            ->where('status', 'approve')
            ->whereNull('cash_bond_returns.deleted_at')
            ->distinct('cash_bond_return_details.id')
            ->selectRaw('
                cash_bond_return_details.cash_bond_id,
                cash_bond_return_details.cash_bond_return_id,
                cash_bond_returns.date as date,
                cash_bond_returns.code as code,
                cash_bond_return_details.amount_to_return as amount_to_return,
                cash_bond_return_details.exchange_rate
            ')
            ->get();

        $results = $cash_bonds->map(function ($cash_bond) use ($cash_bond_details, $cash_bond_return_details) {
            $cash_bond_return_details = $cash_bond_return_details->where('cash_bond_id', $cash_bond->id);
            $amount = $cash_bond_details->where('cash_bond_id', $cash_bond->id)->sum('credit');

            $cash_bond->amount = $amount;
            $cash_bond->amount_local = $amount * $cash_bond->exchange_rate;
            $cash_bond->employee = "$cash_bond->employee_name - $cash_bond->employee_nik";
            $cash_bond->exchange_rate = $cash_bond->exchange_rate;

            $remaining_amount = $amount;

            $cash_bond->details = $cash_bond_return_details->map(function ($cash_bond_return_detail) use ($amount, &$remaining_amount) {
                $cash_bond_return_detail->date = $cash_bond_return_detail->date;
                $cash_bond_return_detail->code = $cash_bond_return_detail->code;
                $cash_bond_return_detail->amount_to_return = $cash_bond_return_detail->amount_to_return;
                $cash_bond_return_detail->amount_to_return_local = $cash_bond_return_detail->amount_to_return * $cash_bond_return_detail->exchange_rate;
                $cash_bond_return_detail->amount_remain = $amount - $cash_bond_return_detail->amount_to_return;
                $cash_bond_return_detail->amount_remain_local = ($remaining_amount - $cash_bond_return_detail->amount_to_return) * $cash_bond_return_detail->exchange_rate;

                $remaining_amount -= $cash_bond_return_detail->amount_to_return;

                return $cash_bond_return_detail;
            });

            return $cash_bond;
        });

        return [
            "type" => "laporan kasbon",
            "data" => $results,
            "from_date" => Carbon::parse($request->from_date),
            "to_date" => Carbon::parse($request->to_date),
        ];
    }

    /**
     * Debt card report
     */
    private function debtCardReportSaleOrderTrading($request)
    {
        $invoicePayments = DB::table('invoice_payments')
            ->where('invoice_payments.invoice_model', '!=', \App\Models\InvoiceDownPayment::class)
            ->join("invoice_parents", function ($join) {
                $join->on("invoice_parents.reference_id", "=", "invoice_payments.invoice_id")
                    ->on("invoice_parents.model_reference", "=", "invoice_payments.invoice_model")
                    ->where('invoice_parents.status', 'approve');
            })
            ->when($request->customer_id, function ($q) use ($request) {
                return $q->where('invoice_parents.customer_id', $request->customer_id);
            })
            ->when($request->type, function ($q) use ($request) {
                return $q->where('invoice_payments.invoice_model', $request->type);
            })
            ->when($request->active, function ($q) {
                return $q->where('invoice_parents.lock_status', 0);
            })
            ->when($request->to_date, function ($q) use ($request) {
                return $q->whereDate('invoice_payments.date', '<=', Carbon::parse($request->to_date));
            })
            ->leftJoin('invoice_down_payments', function ($query) {
                $query->on('invoice_payments.reference_id', 'invoice_down_payments.id')
                    ->where('invoice_payments.model', InvoiceDownPayment::class);
            })
            ->leftJoin('receivables_payment_details', function ($query) {
                $query->on('invoice_payments.reference_id', 'receivables_payment_details.id')
                    ->where('invoice_payments.model', \App\Models\ReceivablesPaymentDetail::class);
            })
            ->leftJoin('bank_code_mutations', function ($query) {
                $query->on('bank_code_mutations.ref_id', 'receivables_payment_details.receivables_payment_id')
                    ->where('bank_code_mutations.ref_model', \App\Models\ReceivablesPayment::class);
            })
            ->join("customers", "customers.id", "=", "invoice_parents.customer_id")
            ->join("currencies", "currencies.id", "=", "invoice_parents.currency_id")
            ->whereNull('invoice_payments.deleted_at')
            ->selectRaw('
            invoice_parents.exchange_rate as invoice_exchange_rate,
            invoice_parents.code as invoice_code,
            invoice_down_payments.code as invoice_down_payment_code,
            invoice_parents.type as invoice_type,
            invoice_payments.id,
            invoice_payments.invoice_id,
            invoice_payments.invoice_model,
            invoice_payments.note,
            invoice_payments.date,
            invoice_payments.exchange_rate,
            invoice_payments.amount_to_receive,
            invoice_payments.receive_amount,
            invoice_payments.model,
            invoice_payments.reference_id,
            invoice_parents.total as invoice_total,
            invoice_parents.exchange_rate as invoice_exchange_rate,
            receivables_payment_details.receivables_payment_id as receivables_payment_id,

            invoice_parents.due_date,

            case when invoice_payments.exchange_rate = 1
                then invoice_payments.amount_to_receive
                else invoice_payments.amount_to_receive * invoice_payments.exchange_rate
            end as amount_to_receive_idr,

            case when invoice_payments.exchange_rate = 1
                then invoice_payments.receive_amount
                else invoice_payments.receive_amount * invoice_payments.exchange_rate
            end as receive_amount_idr,

            currencies.nama as currency_name,

            customers.id as customer_id,
            customers.nama as customer_name,
            customers.code as customer_code,
            DATEDIFF(invoice_parents.due_date, invoice_parents.date) as due,

            GROUP_CONCAT(DISTINCT bank_code_mutations.code) as bank_code
        ')
            ->groupBy('invoice_payments.id')
            ->orderBy('invoice_payments.date')
            ->orderBy('invoice_payments.id')
            ->get();


        $invoice_return_beginnings = DB::table('invoice_returns')
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_returns.customer_id', $request->customer_id);
            })
            ->whereDate('invoice_returns.date', '<', Carbon::parse($request->from_date))
            ->where('invoice_returns.status', 'approve')
            ->whereNull('invoice_returns.deleted_at')
            ->selectRaw(
                'invoice_returns.*'
            )
            ->groupBy('invoice_returns.id')
            ->get();

        $invoice_return_histories_beginnings = DB::table('invoice_return_histories')
            ->whereNull('invoice_return_histories.deleted_at')
            ->join('invoice_returns', function ($query) {
                $query->on('invoice_returns.id', 'invoice_return_histories.invoice_return_id')
                    ->where('invoice_returns.status', 'approve')
                    ->whereNull('invoice_returns.deleted_at');
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_returns.customer_id', $request->customer_id);
            })
            ->whereDate('invoice_return_histories.date', '<', Carbon::parse($request->from_date))
            ->selectRaw(
                'invoice_returns.customer_id,
            invoice_returns.exchange_rate,
            invoice_return_histories.amount as total,
            invoice_return_histories.amount * invoice_returns.exchange_rate as total_exchanged'
            )
            ->groupBy('invoice_return_histories.id')
            ->get();

        $invoice_return_currents = DB::table('invoice_returns')
            ->join('currencies', 'currencies.id', 'invoice_returns.currency_id')
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_returns.customer_id', $request->customer_id);
            })
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('invoice_returns.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('invoice_returns.date', '<=', Carbon::parse($request->to_date));
            })
            ->where('invoice_returns.status', 'approve')
            ->whereNull('invoice_returns.deleted_at')
            ->selectRaw(
                'invoice_returns.*,
            currencies.nama as currency_name,
            invoice_returns.total * invoice_returns.exchange_rate as total_exchanged'
            )
            ->groupBy('invoice_returns.id')
            ->get();

        $invoice_return_histories_currents = DB::table('invoice_return_histories')
            ->whereNull('invoice_return_histories.deleted_at')
            ->join('invoice_returns', function ($query) {
                $query->on('invoice_returns.id', 'invoice_return_histories.invoice_return_id')
                    ->where('invoice_returns.status', 'approve')
                    ->whereNull('invoice_returns.deleted_at');
            })
            ->join('currencies', 'currencies.id', 'invoice_returns.currency_id')
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_returns.customer_id', $request->customer_id);
            })
            ->when($request->from_date, function ($query) use ($request) {
                return $query->whereDate('invoice_return_histories.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->whereDate('invoice_return_histories.date', '<=', Carbon::parse($request->to_date));
            })
            ->selectRaw(
                'invoice_return_histories.date,
            invoice_returns.id,
            invoice_returns.customer_id,
            invoice_returns.code, 
            invoice_returns.exchange_rate,
            currencies.nama as currency_name,
            invoice_return_histories.amount as total,
            invoice_return_histories.amount * invoice_returns.exchange_rate as total_exchanged'
            )
            ->groupBy('invoice_return_histories.id')
            ->get();


        $customers = Customer::when($request->customer_id, function ($query) use ($request) {
            return $query->where('id', $request->customer_id);
        })
            ->whereIn('id', $invoicePayments->pluck('customer_id')->unique())
            ->get();

        $results = $customers->map(function ($customer) use ($invoicePayments, $request, $invoice_return_beginnings, $invoice_return_currents, $invoice_return_histories_beginnings, $invoice_return_histories_currents) {
            $beginning_balance = $invoicePayments->where('customer_id', $customer->id)
                ->where('date', '<', Carbon::parse($request->from_date)->format('Y-m-d'));

            $total_invoice = $beginning_balance->sum('amount_to_receive');
            $total_payment = $beginning_balance->sum('receive_amount');

            $total_invoice_idr = $beginning_balance->sum('amount_to_receive_idr');
            $total_payment_idr = $beginning_balance->sum('receive_amount_idr');

            $invoice_return_beginning = $invoice_return_beginnings->where('customer_id', $customer->id)
                ->sum('total');

            $invoice_return_beginning_idr = $invoice_return_beginnings->where('customer_id', $customer->id)
                ->map(function ($invoice_return) {
                    return ($invoice_return->total) * $invoice_return->exchange_rate;
                })->sum();

            $invoice_return_histories_beginning = $invoice_return_histories_beginnings->where('customer_id', $customer->id)
                ->sum('total');

            $invoice_return_histories_beginning_idr = $invoice_return_histories_beginnings->where('customer_id', $customer->id)
                ->sum('total_exchanged');

            $customer_name = $customer->nama;
            $customer_code = $customer->code;

            $total = $total_invoice - $total_payment;
            $total_idr = $total_invoice_idr - $total_payment_idr;

            $parentPayments = $invoicePayments->where('customer_id', $customer->id)
                ->where('date', '>=', Carbon::parse($request->from_date)->format('Y-m-d'))
                ->where('date', '<=', Carbon::parse($request->to_date)->format('Y-m-d'))
                ->map(function ($payment) use (&$total, &$total_idr) {
                    // $payment->exchange_rate_gap = ($payment->invoice_exchange_rate - $payment->exchange_rate) * $payment->receive_amount;
                    $payment->exchange_rate_gap = ($payment->invoice_total * $payment->invoice_exchange_rate) - ($payment->invoice_total * $payment->exchange_rate);

                    if ($payment->receive_amount != 0) {
                        $payment->type = "Invoice Payment";
                        $payment->invoice_code = '';
                    } else {
                        $payment->type = "Invoice";
                    }

                    $total -= $payment->receive_amount;
                    $total += $payment->amount_to_receive;

                    $total_idr += $payment->amount_to_receive_idr;
                    $total_idr -= $payment->receive_amount_idr;

                    return $payment;
                });

            $invoice_return_current = $invoice_return_currents->where('customer_id', $customer->id)
                ->map(function ($invoice_return) use ($customer_name, $customer_code) {
                    $stdClass = new \stdClass();
                    $stdClass->invoice_exchange_rate = $invoice_return->exchange_rate;
                    $stdClass->invoice_code = $invoice_return->code;
                    $stdClass->invoice_down_payment_code = "";
                    $stdClass->invoice_type = $invoice_return->type;
                    $stdClass->id = $invoice_return->id;
                    $stdClass->invoice_id = $invoice_return->id;
                    $stdClass->invoice_model = InvoiceReturn::class;
                    $stdClass->note = "";
                    $stdClass->date = $invoice_return->date;
                    $stdClass->exchange_rate = $invoice_return->exchange_rate;
                    $stdClass->amount_to_receive = $invoice_return->total * -1;
                    $stdClass->receive_amount = 0;
                    $stdClass->model = InvoiceReturn::class;
                    $stdClass->reference_id = $invoice_return->id;
                    $stdClass->invoice_total = $invoice_return->total * -1;
                    $stdClass->receivables_payment_id = null;
                    $stdClass->due_date = null;
                    $stdClass->amount_to_receive_idr = $invoice_return->total_exchanged * -1;
                    $stdClass->receive_amount_idr = 0;
                    $stdClass->currency_name = $invoice_return->currency_name;
                    $stdClass->customer_id = $invoice_return->customer_id;
                    $stdClass->customer_name = $customer_name;
                    $stdClass->customer_code = $customer_code;
                    $stdClass->due = null;
                    $stdClass->bank_code = null;
                    $stdClass->exchange_rate_gap = 0;
                    $stdClass->type = "Invoice Return";

                    return $stdClass;
                });

            $invoice_return_histories_current = $invoice_return_histories_currents->where('customer_id', $customer->id)
                ->map(function ($invoice_return) use ($customer_name, $customer_code) {
                    $stdClass = new \stdClass();
                    $stdClass->invoice_exchange_rate = $invoice_return->exchange_rate;
                    $stdClass->invoice_code = $invoice_return->code;
                    $stdClass->invoice_down_payment_code = "";
                    $stdClass->invoice_type = null;
                    $stdClass->id = $invoice_return->id;
                    $stdClass->invoice_id = $invoice_return->id;
                    $stdClass->invoice_model = InvoiceReturn::class;
                    $stdClass->note = "";
                    $stdClass->date = $invoice_return->date;
                    $stdClass->exchange_rate = $invoice_return->exchange_rate;
                    $stdClass->amount_to_receive = $invoice_return->total;
                    $stdClass->receive_amount = 0;
                    $stdClass->model = InvoiceReturn::class;
                    $stdClass->reference_id = $invoice_return->id;
                    $stdClass->invoice_total = $invoice_return->total;
                    $stdClass->receivables_payment_id = null;
                    $stdClass->due_date = null;
                    $stdClass->amount_to_receive_idr = $invoice_return->total_exchanged;
                    $stdClass->receive_amount_idr = 0;
                    $stdClass->currency_name = $invoice_return->currency_name;
                    $stdClass->customer_id = $invoice_return->customer_id;
                    $stdClass->customer_name = $customer_name;
                    $stdClass->customer_code = $customer_code;
                    $stdClass->due = null;
                    $stdClass->bank_code = null;
                    $stdClass->exchange_rate_gap = 0;
                    $stdClass->type = "Pengembalian Retur";

                    return $stdClass;
                });

            $parentPayments = $parentPayments->merge($invoice_return_current)
                ->merge($invoice_return_histories_current)
                ->sortBy(function ($q) {
                    return Carbon::parse($q->date)->format('Ymd') . sprintf('%010d', $q->id);
                })->values();

            return [
                'customer_id' => $customer->id,
                'customer_name' => $customer_name,
                'customer_code' => $customer_code,
                'invoices' => $parentPayments,
                'beginning_balance' => $beginning_balance->sum('amount_to_receive') - $beginning_balance->sum('receive_amount') - $invoice_return_beginning + $invoice_return_histories_beginning,
                'beginning_balance_idr' => $beginning_balance->sum('amount_to_receive_idr') - $beginning_balance->sum('receive_amount_idr') - $invoice_return_beginning_idr + $invoice_return_histories_beginning_idr,
            ];
        });

        $results = $results->filter(function ($d) {
            return count($d['invoices']) != 0 || $d['beginning_balance'] != 0;
        });

        return [
            "title" => "Kartu Piutang",
            "type" => "Kartu Piutang",
            "from_date" => Carbon::parse($request->from_date),
            "to_date" => Carbon::parse($request->to_date),
            "data" => $results,
        ];
    }

    // ! GET SUMMARY PIUTANG DAGANG DATA
    public function summary_piutang_dagang($type, $request)
    {
        $start_period = '01-' . $request->start_period;
        $start_period = Carbon::parse($start_period)->startOfMonth();
        $end_period = '01-' . $request->end_period;
        $end_period = Carbon::parse($end_period)->endOfMonth();

        $customers = DB::table('customers')->orderBy('nama')
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('id', $request->customer_id);
            })
            ->whereNull('deleted_at')
            ->get();

        $transactions = DB::table('invoice_payments')
            ->join('invoice_parents', function ($q) use ($customers) {
                $q->on('invoice_parents.reference_id', 'invoice_payments.invoice_id')
                    ->on('invoice_parents.model_reference', 'invoice_payments.invoice_model')
                    ->where('invoice_parents.status', 'approve');
            })
            ->whereIn('invoice_parents.customer_id', $customers->pluck('id'))
            ->join('customers', 'customers.id', 'invoice_parents.customer_id')
            ->whereNull('invoice_payments.deleted_at')
            ->whereNull('invoice_parents.deleted_at')
            ->whereDate('invoice_payments.date', '<=', $end_period)
            ->when($request->active, function ($q) {
                return $q->where('invoice_parents.lock_status', 0);
            })
            ->select(
                'invoice_payments.*',
                'customers.id as customer_id',
            )
            ->get();

        $invoice_return_beginnings = DB::table('invoice_returns')
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_returns.customer_id', $request->customer_id);
            })
            ->whereDate('invoice_returns.date', '<', $start_period)
            ->where('invoice_returns.status', 'approve')
            ->whereNull('invoice_returns.deleted_at')
            ->selectRaw(
                'invoice_returns.*'
            )
            ->groupBy('invoice_returns.id')
            ->get();

        $invoice_return_histories_beginnings = DB::table('invoice_return_histories')
            ->whereNull('invoice_return_histories.deleted_at')
            ->join('invoice_returns', function ($query) {
                $query->on('invoice_returns.id', 'invoice_return_histories.invoice_return_id')
                    ->where('invoice_returns.status', 'approve')
                    ->whereNull('invoice_returns.deleted_at');
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_returns.customer_id', $request->customer_id);
            })
            ->whereDate('invoice_return_histories.date', '<', $start_period)
            ->selectRaw(
                'invoice_returns.customer_id,
            invoice_returns.exchange_rate,
            invoice_return_histories.amount as total,
            invoice_return_histories.amount * invoice_returns.exchange_rate as total_exchanged'
            )
            ->groupBy('invoice_return_histories.id')
            ->get();

        $invoice_return_currents = DB::table('invoice_returns')
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_returns.customer_id', $request->customer_id);
            })
            ->whereDate('invoice_returns.date', '>=', $start_period)
            ->whereDate('invoice_returns.date', '<=', $end_period)
            ->where('invoice_returns.status', 'approve')
            ->whereNull('invoice_returns.deleted_at')
            ->selectRaw(
                'invoice_returns.*'
            )
            ->groupBy('invoice_returns.id')
            ->get();

        $invoice_return_histories_currents = DB::table('invoice_return_histories')
            ->whereNull('invoice_return_histories.deleted_at')
            ->join('invoice_returns', function ($query) {
                $query->on('invoice_returns.id', 'invoice_return_histories.invoice_return_id')
                    ->where('invoice_returns.status', 'approve')
                    ->whereNull('invoice_returns.deleted_at');
            })
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_returns.customer_id', $request->customer_id);
            })
            ->whereDate('invoice_return_histories.date', '>=', $start_period)
            ->whereDate('invoice_return_histories.date', '<=', $end_period)
            ->selectRaw(
                'invoice_return_histories.date,
            invoice_returns.id,
            invoice_returns.customer_id,
            invoice_returns.code,
            invoice_returns.exchange_rate,
            invoice_return_histories.amount as total,
            invoice_return_histories.amount * invoice_returns.exchange_rate as total_exchanged'
            )
            ->groupBy('invoice_return_histories.id')
            ->get();

        $customers = $customers->map(function ($customer) use ($transactions, $start_period, $end_period, $invoice_return_beginnings, $invoice_return_currents, $invoice_return_histories_beginnings, $invoice_return_histories_currents) {
            $beginning_in = $transactions->where('customer_id', $customer->id)
                ->filter(function ($q) use ($start_period) {
                    return Carbon::parse($q->date)->lt(Carbon::parse($start_period));
                })
                ->map(function ($transaction) {
                    return $transaction->amount_to_receive * $transaction->exchange_rate;
                })->sum();

            $beginning_out = $transactions->where('customer_id', $customer->id)
                ->filter(function ($q) use ($start_period) {
                    return Carbon::parse($q->date)->lt(Carbon::parse($start_period));
                })
                ->map(function ($transaction) {
                    return $transaction->receive_amount * $transaction->exchange_rate;
                })
                ->sum();

            $invoice_return_beginning = $invoice_return_beginnings->where('customer_id', $customer->id)
                ->map(function ($invoice_return) {
                    return ($invoice_return->total) * $invoice_return->exchange_rate;
                })->sum();

            $invoice_return_histories_beginning = $invoice_return_histories_beginnings->where('customer_id', $customer->id)
                ->sum('total_exchanged');

            $beginning = $beginning_in - $beginning_out - $invoice_return_beginning + $invoice_return_histories_beginning;

            $current_in = $transactions->where('customer_id', $customer->id)
                ->filter(function ($q) use ($start_period, $end_period) {
                    return Carbon::parse($q->date)->gte(Carbon::parse($start_period)) && Carbon::parse($q->date)->lte(Carbon::parse($end_period));
                })
                // ->where('date', '>=', Carbon::parse($start_period)->format('Y-m-d'))
                // ->where('date', '<=', Carbon::parse($end_period)->format('Y-m-d'))
                ->map(function ($transaction) {
                    return $transaction->amount_to_receive * $transaction->exchange_rate;
                })
                ->sum();

            $current_out = $transactions->where('customer_id', $customer->id)
                ->filter(function ($q) use ($start_period, $end_period) {
                    return Carbon::parse($q->date)->gte(Carbon::parse($start_period)) && Carbon::parse($q->date)->lte(Carbon::parse($end_period));
                })
                // ->where('date', '>=', Carbon::parse($start_period)->format('Y-m-d'))
                // ->where('date', '<=', Carbon::parse($end_period)->format('Y-m-d'))
                ->map(function ($transaction) {
                    return $transaction->receive_amount * $transaction->exchange_rate;
                })
                ->sum();

            $invoice_return_current = $invoice_return_currents->where('customer_id', $customer->id)
                ->map(function ($invoice_return) {
                    return $invoice_return->total * $invoice_return->exchange_rate;
                })->sum();

            $current_out += $invoice_return_current;

            $invoice_return_histories_current = $invoice_return_histories_currents->where('customer_id', $customer->id)
                ->sum('total_exchanged');

            $current_in += $invoice_return_histories_current;

            $final_balance = $beginning + $current_in - $current_out;

            $customer->beginning = $beginning;
            $customer->current_in = $current_in;
            $customer->current_out = $current_out;
            $customer->final_balance = $final_balance;

            return $customer;
        });

        $return['data'] = collect($customers);
        $return['type'] = $type;
        $return['from_date'] = Carbon::parse($start_period);
        $return['to_date'] = Carbon::parse($end_period);
        $return['currency'] = Currency::find($request->currency_id);
        $return['customer'] = Customer::find($request->customer_id);

        return $return;
    }

    public function exportAsset(Request $request)
    {
        $data = DB::table('assets')
            ->leftJoin('dispositions', function ($query) use ($request) {
                $query->on('assets.id', '=', 'dispositions.asset_id')
                    ->whereNull('dispositions.deleted_at');
            })
            ->leftJoin('coas as asset_coas', function ($query) {
                $query->on('asset_coas.id', '=', 'assets.asset_coa_id')
                    ->whereNull('asset_coas.deleted_at');
            })
            ->leftJoin('coas as child_coa', function ($query) {
                $query->on('child_coa.parent_id', '=', 'asset_coas.id')
                    ->whereNull('child_coa.deleted_at');
            })
            ->when($request->coa_id, function ($query) use ($request) {
                $query->where('asset_coas.id', $request->coa_id);
            })
            ->when($request->to_date, function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->whereDate('assets.purchase_date', '<=', Carbon::parse($request->to_date));
                    // $query->where(function ($query) use ($request) {
                    //     $query->whereNull('dispositions.date');
                    //     $query->orWhere(function ($q) use ($request) {
                    //         $q->whereDate('dispositions.date', '>=', Carbon::parse($request->to_date));
                    //     });
                    // });
                });
            })
            ->selectRaw('
                assets.id,
                assets.asset_name,
                dispositions.id as disposition_id,
                dispositions.date as date,
                assets.usage_date,
                assets.depreciation_end_date,
                assets.estimated_life,
                assets.value,
                assets.depreciation_value,
                assets.purchase_date,
                assets.depreciation_percentage,
                assets.note,
                asset_coas.id as coa_id,
                asset_coas.name as coa_name,
                asset_coas.account_code as coa_code
            ')
            ->whereNull('assets.deleted_at')
            ->groupBy('assets.id')
            ->orderBy('asset_coas.account_code', 'asc')
            ->get();

        $depreciations = Depreciation::whereIn('asset_id', $data->pluck('id'))
            ->whereDate('date', '<=', Carbon::parse($request->to_date)->endOfMonth())
            ->whereNull('deleted_at')
            ->get();

        $data = $data->map(function ($asset) use ($request, $depreciations) {
            $all_depreciations = $depreciations->where('asset_id', $asset->id);
            $depreciation_before_date = $depreciations->where('asset_id', $asset->id)
                ->filter(function ($q) use ($request) {
                    return Carbon::parse($q->date)->lt(Carbon::parse($request->to_date)->startofMonth());
                });

            $depreciation_this_month = $depreciations->where('asset_id', $asset->id)
                ->filter(function ($q) use ($request) {
                    return Carbon::parse($q->date)->format('Y-m') == Carbon::parse($request->to_date)->format('Y-m');
                });

            $depreciation_count = $all_depreciations->sum('amount') != 0 && $asset->depreciation_value != 0 ? $all_depreciations->sum('amount') / $asset->depreciation_value : 0;
            $depreciation_count = round($depreciation_count, 0, PHP_ROUND_HALF_DOWN);
            $total_depreciation_this_month = $depreciation_this_month->sum('amount');
            $acumulated_depreciation = $depreciation_before_date->sum('amount') + $total_depreciation_this_month;
            $final_book_value = $asset->value - $acumulated_depreciation;

            $asset->depreciation_count = $depreciation_count;
            $asset->total_depreciation_this_month = $total_depreciation_this_month;
            $asset->acumulated_depreciation = $acumulated_depreciation;
            $asset->final_book_value = $final_book_value;

            return $asset;
        });

        $res['data'] = $data;
        $res['to_date'] = $request->to_date;

        return $res;
    }

    public function exportBDM(Request $request)
    {
        DB::statement('SET group_concat_max_len = 10000000000000000000');

        $data = DB::table('leases')
            ->leftJoin('coas as asset_coas', function ($query) {
                $query->on('asset_coas.id', '=', 'leases.asset_coa_id')
                    ->whereNull('asset_coas.deleted_at');
            })
            ->leftJoin('coas as child_coa', function ($query) {
                $query->on('child_coa.parent_id', '=', 'asset_coas.id')
                    ->whereNull('child_coa.deleted_at');
            })
            ->when($request->coa_id, function ($query) use ($request) {
                $query->where('asset_coas.id', $request->coa_id);
            })
            ->when($request->to_date, function ($query) use ($request) {
                $query->whereDate('leases.from_date', '<=', Carbon::parse($request->to_date));
            })
            ->selectRaw('
                leases.id,
                leases.lease_name,
                leases.date,
                leases.from_date,
                leases.to_date,
                leases.month_duration,
                leases.value,
                leases.depreciation_value,
                leases.note,
                asset_coas.id as coa_id,
                asset_coas.name as coa_name,
                asset_coas.account_code as coa_code,
                GROUP_CONCAT(child_coa.name) as child_coa_name
            ')
            ->whereNull('leases.deleted_at')
            ->groupBy('leases.id')
            ->orderBy('asset_coas.account_code', 'asc')
            ->get();

        $amortizations = Amortization::whereIn('lease_id', $data->pluck('id'))
            ->whereDate('date', '<=', Carbon::parse($request->to_date)->endOfMonth())
            ->whereNull('deleted_at')
            ->get();

        $data = $data->map(function ($lease) use ($request, $amortizations) {
            $all_amortizations = $amortizations->where('lease_id', $lease->id);
            $depreciation_before_date = $amortizations->where('lease_id', $lease->id)
                ->filter(function ($q) use ($request) {
                    return Carbon::parse($q->date)->lt(Carbon::parse($request->to_date)->startofMonth());
                });

            $depreciation_this_month = $amortizations->where('lease_id', $lease->id)
                ->filter(function ($q) use ($request) {
                    return Carbon::parse($q->date)->format('Y-m') == Carbon::parse($request->to_date)->format('Y-m');
                });

            $depreciation_count = $all_amortizations->sum('amount') != 0 && $lease->depreciation_value != 0 ? $all_amortizations->sum('amount') / $lease->depreciation_value : 0;
            $depreciation_count = round($depreciation_count, 0, PHP_ROUND_HALF_DOWN);
            $total_depreciation_this_month = $depreciation_this_month->sum('amount');
            $acumulated_depreciation = $depreciation_before_date->sum('amount') + $total_depreciation_this_month;
            $final_book_value = $lease->value - $acumulated_depreciation;

            $lease->depreciation_count = $depreciation_count;
            $lease->total_depreciation_this_month = $total_depreciation_this_month;
            $lease->acumulated_depreciation = $acumulated_depreciation;
            $lease->final_book_value = $final_book_value;

            return $lease;
        });
        $res['data'] = $data;
        $res['to_date'] = $request->to_date;

        return $res;
    }

    // ! GET SISA PIUTANG DATA
    public function sisa_piutang($type, $request)
    {
        // get supplier invoice parent where not paid before to date
        $invoice_parents = DB::table('invoice_parents')
            ->whereDate('invoice_parents.date', '<=', Carbon::parse($request->to_date))
            ->join('customers', 'customers.id', 'invoice_parents.customer_id')
            ->whereNull('invoice_parents.deleted_at')
            ->where('invoice_parents.status', 'approve')
            ->leftJoin('invoice_payments', function ($query) use ($request) {
                $query->on('invoice_parents.model_reference', 'invoice_payments.invoice_model')
                    ->whereNull('invoice_payments.deleted_at')
                    ->whereColumn('invoice_parents.reference_id', 'invoice_payments.invoice_id')
                    ->whereDate('invoice_payments.date', '<=', Carbon::parse($request->to_date));
            })
            ->selectRaw(
                'invoice_parents.*,
                   customers.nama as customer_nama,
                   customers.code as customer_code,
                   invoice_parents.total,
                   COALESCE(SUM(invoice_payments.receive_amount), 0) as paid_amount,
                   COALESCE(SUM(invoice_payments.receive_amount * invoice_payments.exchange_rate), 0) as paid_amount_exchanged',
            )
            ->orderBy('invoice_parents.date')
            ->orderBy('invoice_parents.code')
            ->groupBy('invoice_parents.id')
            ->havingRaw('total != paid_amount')
            ->when($request->currency_id, function ($query)  use ($request) {
                $query->where('invoice_parents.currency_id', $request->currency_id);
            })
            ->when($request->customer_id, function ($query) use ($request) {
                $query->where('invoice_parents.customer_id', $request->customer_id);
            })
            ->when($request->active, function ($q) {
                return $q->where('invoice_parents.lock_status', 0);
            })
            ->get();

        $invoice_payments = DB::table('invoice_payments')
            ->whereIn('invoice_id', $invoice_parents->pluck('reference_id'))
            ->whereNull('invoice_payments.deleted_at')
            ->whereDate('invoice_payments.date', '<=', Carbon::parse($request->to_date))
            ->get();

        $invoice_parents = $invoice_parents->map(function ($m) use ($invoice_payments, $request) {
            $m->total_exchanged = $m->total * $m->exchange_rate;
            $paid_amount = $invoice_payments->where('invoice_id', $m->reference_id)
                ->where('invoice_model', $m->model_reference)
                ->sum('receive_amount');

            $paid_amount_exchanged = $invoice_payments->where('invoice_id', $m->reference_id)
                ->where('invoice_model', $m->model_reference)
                ->map(function ($p) {
                    return $p->receive_amount * $p->exchange_rate;
                })
                ->sum();

            $m->overdue = Carbon::parse($m->due_date)->lt(Carbon::parse($request->to_date)) ? Carbon::parse($m->due_date)->diffInDays(Carbon::parse($request->to_date)) : '';
            $m->paid_amount = $paid_amount;
            $m->paid_amount_exchanged = $paid_amount_exchanged;
            $m->return = 0;
            $m->return_exchanged = 0;
            $m->outstanding_amount = $m->total - ($paid_amount);
            $m->outstanding_amount_exchanged = $m->total_exchanged - ($paid_amount_exchanged);

            // get all payments gap
            $acumulated_exchange_rate_gap = $invoice_payments->where('invoice_id', $m->reference_id)
                ->map(function ($p) use ($m) {
                    $gap = ($m->exchange_rate - $p->exchange_rate) * $p->receive_amount;
                    return $gap;
                })->sum();

            $m->acumulated_exchange_rate_gap = $acumulated_exchange_rate_gap;

            return $m;
        });

        $invoice_return_generals = DB::table('invoice_returns')
            ->whereNull('invoice_returns.deleted_at')
            ->where('invoice_returns.status', 'approve')
            ->whereDate('invoice_returns.date', '<=', Carbon::parse($request->to_date))
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_returns.customer_id', $request->customer_id);
            })
            ->when($request->currency_id, function ($query)  use ($request) {
                $query->where('invoice_returns.currency_id', $request->currency_id);
            })
            ->leftJoin('invoice_return_histories', function ($join) use ($request) {
                $join->on('invoice_return_histories.invoice_return_id', 'invoice_returns.id')
                    ->whereNull('invoice_return_histories.deleted_at')
                    ->whereDate('invoice_return_histories.date', '<=', Carbon::parse($request->to_date));
            })
            ->join('currencies', 'currencies.id', 'invoice_returns.currency_id')
            ->join('customers', 'customers.id', 'invoice_returns.customer_id')
            ->selectRaw(
                'invoice_returns.*,
                customers.nama as customer_nama,
                customers.code as customer_code,
                currencies.nama as currency_name,
                COALESCE(SUM(invoice_return_histories.amount), 0) as paid_amount'
            )
            ->groupBy('invoice_returns.id')
            ->havingRaw('total != paid_amount')
            ->get();

        $return_without_invoice = $invoice_return_generals->map(function ($m) use ($request) {
            $m->customer_nama = $m->customer_nama;
            $m->customer_code = $m->customer_code;
            $m->total = $m->total * -1;
            $m->total_exchanged = $m->total * $m->exchange_rate;
            $m->paid_amount = $m->paid_amount * -1;
            $m->paid_amount_exchanged = $m->paid_amount * $m->exchange_rate;
            $m->return = 0;
            $m->return_exchanged = 0;
            $m->outstanding_amount = $m->total - $m->paid_amount;
            $m->outstanding_amount_exchanged = $m->outstanding_amount * $m->exchange_rate;
            $m->acumulated_exchange_rate_gap = 0;
            $m->overdue = '';
            $m->due_date = '';

            return $m;
        });

        $merge_data = collect($invoice_parents)
            ->merge($return_without_invoice)
            ->filter(function ($m) {
                return $m->outstanding_amount != 0;
            })
            ->sortBy('date')
            ->values();

        $return['data'] = $merge_data;
        $return['type'] = $type;
        $return['from_date'] = Carbon::parse($request->from_date);
        $return['to_date'] = Carbon::parse($request->to_date);
        $return['currency'] = Currency::find($request->currency_id);
        $return['customer'] = Customer::find($request->customer_id);

        return $return;
    }

    // ! GET SISA PIUTANG PER CUSTOMER DATA
    public function sisa_piutang_per_customer($type, $request)
    {
        // get supplier invoice parent where not paid before to date
        $invoice_parents = DB::table('invoice_parents')
            ->whereDate('invoice_parents.date', '<=', Carbon::parse($request->to_date))
            ->join('customers', 'customers.id', 'invoice_parents.customer_id')
            ->whereNull('invoice_parents.deleted_at')
            ->where('invoice_parents.status', 'approve')
            ->leftJoin('invoice_payments', function ($query) use ($request) {
                $query->on('invoice_parents.model_reference', 'invoice_payments.invoice_model')
                    ->whereNull('invoice_payments.deleted_at')
                    ->whereColumn('invoice_parents.reference_id', 'invoice_payments.invoice_id')
                    ->whereDate('invoice_payments.date', '<=', Carbon::parse($request->to_date));
            })
            ->selectRaw(
                'invoice_parents.*,
                    customers.nama as customer_nama,
                    customers.code as customer_code,
                    invoice_parents.total,
                    COALESCE(SUM(invoice_payments.receive_amount), 0) as paid_amount,
                    COALESCE(SUM(invoice_payments.receive_amount * invoice_payments.exchange_rate), 0) as paid_amount_exchanged',
            )
            ->orderBy('invoice_parents.date')
            ->orderBy('invoice_parents.code')
            ->groupBy('invoice_parents.id')
            ->havingRaw('total != paid_amount')
            ->when($request->customer_id, function ($query) use ($request) {
                $query->where('invoice_parents.customer_id', $request->customer_id);
            })
            ->when($request->active, function ($q) {
                return $q->where('invoice_parents.lock_status', 0);
            })
            ->get();

        $invoice_payments = DB::table('invoice_payments')
            ->whereIn('invoice_id', $invoice_parents->pluck('reference_id'))
            ->whereNull('invoice_payments.deleted_at')
            ->whereDate('invoice_payments.date', '<=', Carbon::parse($request->to_date))
            ->get();

        $invoice_parents = $invoice_parents->map(function ($m) use ($invoice_payments) {
            $m->total_exchanged = $m->total * $m->exchange_rate;
            $paid_amount = $invoice_payments->where('invoice_id', $m->reference_id)
                ->where('invoice_model', $m->model_reference)
                ->sum('receive_amount');

            $paid_amount_exchanged = $invoice_payments->where('invoice_id', $m->reference_id)
                ->where('invoice_model', $m->model_reference)
                ->map(function ($p) {
                    return $p->receive_amount * $p->exchange_rate;
                })
                ->sum();


            $m->paid_amount = $paid_amount;
            $m->paid_amount_exchanged = $paid_amount_exchanged;
            $m->outstanding_amount = $m->total - ($paid_amount);
            $m->outstanding_amount_exchanged = $m->total_exchanged - ($paid_amount_exchanged);

            // get all payments gap
            $acumulated_exchange_rate_gap = $invoice_payments->where('invoice_id', $m->reference_id)
                ->map(function ($p) use ($m) {
                    $gap = ($m->exchange_rate - $p->exchange_rate) * $p->receive_amount;
                    return $gap;
                })->sum();

            $m->acumulated_exchange_rate_gap = $acumulated_exchange_rate_gap;

            return $m;
        });

        $invoice_return_generals = DB::table('invoice_returns')
            ->whereNull('invoice_returns.deleted_at')
            ->where('invoice_returns.status', 'approve')
            ->whereDate('invoice_returns.date', '<=', Carbon::parse($request->to_date))
            ->when($request->customer_id, function ($query) use ($request) {
                return $query->where('invoice_returns.customer_id', $request->customer_id);
            })
            ->when($request->currency_id, function ($query)  use ($request) {
                $query->where('invoice_returns.currency_id', $request->currency_id);
            })
            ->leftJoin('invoice_return_histories', function ($join) use ($request) {
                $join->on('invoice_return_histories.invoice_return_id', 'invoice_returns.id')
                    ->whereNull('invoice_return_histories.deleted_at')
                    ->whereDate('invoice_return_histories.date', '<=', Carbon::parse($request->to_date));
            })
            ->join('currencies', 'currencies.id', 'invoice_returns.currency_id')
            ->join('customers', 'customers.id', 'invoice_returns.customer_id')
            ->selectRaw(
                'invoice_returns.*,
                customers.nama as customer_nama,
                customers.code as customer_code,
                currencies.nama as currency_name,
                COALESCE(SUM(invoice_return_histories.amount), 0) as paid_amount'
            )
            ->groupBy('invoice_returns.id')
            ->havingRaw('total != paid_amount')
            ->get();

        $return_without_invoice = $invoice_return_generals->map(function ($m) use ($request) {
            $m->model_reference = InvoiceReturn::class;
            $m->reference_id = $m->id;
            $m->due_date = $m->date;
            $m->customer_nama = $m->customer_nama;
            $m->customer_code = $m->customer_code;
            $m->total = $m->total * -1;
            $m->total_exchanged = $m->total * $m->exchange_rate;
            $m->paid_amount = $m->paid_amount * -1;
            $m->paid_amount_exchanged = $m->paid_amount * $m->exchange_rate;
            $m->outstanding_amount = $m->total - $m->paid_amount;
            $m->outstanding_amount_exchanged = $m->outstanding_amount * $m->exchange_rate;
            $m->acumulated_exchange_rate_gap = 0;

            return $m;
        })->values();

        $merge_data = collect($invoice_parents)
            ->merge($return_without_invoice)
            ->filter(function ($m) {
                return $m->outstanding_amount != 0;
            })
            ->sortBy('date')
            ->values();

        $new_data = new Collection();
        $invoice_parents->groupBy('customer_id')->each(function ($m) use ($merge_data, $new_data) {
            $push_data = new stdClass;
            $push_data->customer_nama = $m->first()->customer_nama;
            $push_data->data = $merge_data->where('customer_id', $m->first()->customer_id);
            $push_data->total_outstanding =  $merge_data->where('customer_id', $m->first()->customer_id)
                ->sum('outstanding_amount');
            $push_data->total_outstanding_exchanged =  $merge_data->where('customer_id', $m->first()->customer_id)
                ->sum('outstanding_amount_exchanged');

            $new_data->push($push_data);
        });

        $return['data'] = $new_data;
        $return['type'] = $type;
        $return['total_exchanged'] = $merge_data->sum('total_exchanged');
        $return['paid_amount_exchanged'] = $merge_data->sum('paid_amount_exchanged');
        $return['outstanding_amount_exchanged'] = $merge_data->sum('outstanding_amount_exchanged');
        $return['from_date'] = Carbon::parse($request->from_date);
        $return['to_date'] = Carbon::parse($request->to_date);
        $return['currency'] = Currency::find($request->currency_id);
        $return['customer'] = Customer::find($request->customer_id);

        return $return;
    }

    // ! GET SISA HUTANG PER VENDOR DATA
    public function sisa_hutang_per_vendor($type, $request)
    {
        // IN
        // get supplier invoice parent where not paid before to date
        $item_receiving_reports = DB::table('item_receiving_reports')
            ->whereDate('item_receiving_reports.date_receive', '<=', Carbon::parse($request->to_date))
            ->join('vendors', 'vendors.id', 'item_receiving_reports.vendor_id')
            ->whereNull('item_receiving_reports.deleted_at')
            ->whereIn('item_receiving_reports.status', ['approve', 'done', 'return-all'])
            ->leftJoin('supplier_invoice_payments', function ($query) use ($request) {
                $query->on('supplier_invoice_payments.item_receiving_report_id', 'item_receiving_reports.id')
                    ->whereNull('supplier_invoice_payments.deleted_at')
                    ->whereDate('supplier_invoice_payments.date', '<=', Carbon::parse($request->to_date));
            })
            ->selectRaw(
                'item_receiving_reports.*,
                item_receiving_reports.date_receive as date,
                item_receiving_reports.kode as code,
                   vendors.nama as vendor_nama,
                   vendors.code as vendor_code,
                   item_receiving_reports.total,
                   COALESCE(SUM(supplier_invoice_payments.pay_amount), 0) as paid_amount,
                   COALESCE(SUM(supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate), 0) as paid_amount_exchanged',
            )
            ->orderBy('item_receiving_reports.date_receive')
            ->orderBy('item_receiving_reports.kode')
            ->groupBy('item_receiving_reports.id')
            ->havingRaw('total != paid_amount')
            ->when($request->vendor_id, function ($query) use ($request) {
                $query->where('item_receiving_reports.vendor_id', $request->vendor_id);
            })
            ->get();

        $item_receiving_reports = $item_receiving_reports->map(function ($item_receiving_report) {
            $item_receiving_report->total_exchanged = $item_receiving_report->total * $item_receiving_report->exchange_rate;
            $item_receiving_report->outstanding_amount = $item_receiving_report->total - $item_receiving_report->paid_amount;
            $item_receiving_report->outstanding_amount_exchanged = $item_receiving_report->total_exchanged - $item_receiving_report->paid_amount_exchanged;
            return $item_receiving_report;
        });

        $supplier_invoice_parents = DB::table('supplier_invoice_parents')
            ->where('supplier_invoice_parents.type', 'general')
            ->whereDate('supplier_invoice_parents.date', '<=', Carbon::parse($request->to_date))
            ->when($request->currency_id, function ($query)  use ($request) {
                $query->where('supplier_invoice_parents.currency_id', $request->currency_id);
            })
            ->when($request->vendor_id, function ($query) use ($request) {
                $query->where('supplier_invoice_parents.vendor_id', $request->vendor_id);
            })
            ->join('vendors', 'vendors.id', 'supplier_invoice_parents.vendor_id')
            ->whereNull('supplier_invoice_parents.deleted_at')
            ->where('supplier_invoice_parents.status', 'approve')
            ->leftJoin('supplier_invoice_payments', function ($query) use ($request) {
                $query->on('supplier_invoice_parents.model_reference', 'supplier_invoice_payments.supplier_invoice_model')
                    ->whereNull('supplier_invoice_payments.deleted_at')
                    ->whereColumn('supplier_invoice_parents.reference_id', 'supplier_invoice_payments.supplier_invoice_id')
                    ->whereDate('supplier_invoice_payments.date', '<=', Carbon::parse($request->to_date));
            })
            ->selectRaw(
                'supplier_invoice_parents.*,
                supplier_invoice_parents.date as date,
                supplier_invoice_parents.code as code,
                vendors.nama as vendor_nama,
                vendors.code as vendor_code,
                supplier_invoice_parents.total,
                COALESCE(SUM(supplier_invoice_payments.pay_amount), 0) as paid_amount,
                COALESCE(SUM(supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate), 0) as paid_amount_exchanged',
            )
            ->orderBy('supplier_invoice_parents.date')
            ->orderBy('supplier_invoice_parents.code')
            ->groupBy('supplier_invoice_parents.id')
            ->havingRaw('total != paid_amount')
            ->get();

        // OUT
        $other_payments = DB::table('supplier_invoice_payments')
            ->join('supplier_invoices', function ($join) {
                $join->on('supplier_invoices.id', 'supplier_invoice_payments.supplier_invoice_id')
                    ->where('supplier_invoice_payments.supplier_invoice_model', SupplierInvoice::class);
            })
            ->join('vendors', 'supplier_invoices.vendor_id', 'vendors.id')
            ->whereNull('item_receiving_report_id')
            ->whereNull('supplier_invoice_payments.deleted_at')
            ->whereDate('supplier_invoice_payments.date', '<=', Carbon::parse($request->to_date))
            ->where('pay_amount', '!=', 0)
            ->when($request->vendor_id, function ($q) use ($request) {
                $q->where('vendors.id', $request->vendor_id);
            })
            ->selectRaw(
                'supplier_invoice_payments.date,
                vendors.id as vendor_id,
                supplier_invoice_payments.exchange_rate,
                supplier_invoice_payments.note as code,
                0 as total,
                0 as total_exchanged,
                supplier_invoice_payments.pay_amount * -1 as outstanding_amount,
                supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate * -1 as outstanding_amount_exchanged,
                supplier_invoice_payments.pay_amount as paid_amount,
                supplier_invoice_payments.pay_amount * supplier_invoice_payments.exchange_rate as paid_amount_exchanged'
            )
            ->get();

        $purchase_returns = DB::table('purchase_returns')
            ->whereNull('purchase_returns.deleted_at')
            ->where('purchase_returns.status', 'approve')
            ->whereDate('purchase_returns.date', '<=', Carbon::parse($request->to_date))
            ->when($request->vendor_id, function ($q) use ($request) {
                $q->where('purchase_returns.vendor_id', $request->vendor_id);
            })
            ->leftJoin('purchase_return_histories', function ($join) use ($request) {
                $join->on('purchase_return_histories.purchase_return_id', 'purchase_returns.id')
                    ->whereNull('purchase_return_histories.deleted_at')
                    ->whereDate('purchase_return_histories.date', '<=', Carbon::parse($request->to_date));
            })
            ->selectRaw(
                'purchase_returns.*,
                COALESCE(SUM(purchase_return_histories.amount), 0) as paid_amount'
            )
            ->groupBy('purchase_returns.id')
            ->havingRaw('purchase_returns.total != paid_amount')
            ->get();

        $purchase_returns = $purchase_returns->map(function ($purchase_return) {
            $purchase_return->total = $purchase_return->total * -1;
            $purchase_return->total_exchanged = $purchase_return->total * $purchase_return->exchange_rate;
            $purchase_return->paid_amount = $purchase_return->paid_amount * -1;
            $purchase_return->paid_amount_exchanged = $purchase_return->paid_amount * $purchase_return->exchange_rate;
            $purchase_return->outstanding_amount = $purchase_return->total - $purchase_return->paid_amount;
            $purchase_return->outstanding_amount_exchanged = $purchase_return->outstanding_amount * $purchase_return->exchange_rate;

            return $purchase_return;
        });

        $merge_data = collect($item_receiving_reports)
            ->merge($supplier_invoice_parents)
            ->merge($other_payments)
            ->merge($purchase_returns)
            ->sortBy('date');

        $new_data = new Collection();
        $item_receiving_reports->groupBy('vendor_id')->each(function ($m) use ($merge_data, $new_data) {
            $push_data = new stdClass;
            $push_data->vendor_nama = $m->first()->vendor_nama;
            $push_data->data = $merge_data->where('vendor_id', $m->first()->vendor_id);
            $push_data->total_outstanding =  $merge_data->where('vendor_id', $m->first()->vendor_id)
                ->sum('outstanding_amount');
            $push_data->total_outstanding_exchanged = $merge_data->where('vendor_id', $m->first()->vendor_id)
                ->sum('outstanding_amount_exchanged');

            $new_data->push($push_data);
        });

        $return['data'] = $new_data->sortBy('vendor_nama')->values();
        $return['type'] = $type;
        $return['total_exchanged'] = $merge_data->sum('total_exchanged');
        $return['paid_amount_exchanged'] = $merge_data->sum('paid_amount_exchanged');
        $return['outstanding_amount_exchanged'] = $merge_data->sum('outstanding_amount_exchanged');
        $return['from_date'] = Carbon::parse($request->from_date);
        $return['to_date'] = Carbon::parse($request->to_date);
        $return['currency'] = Currency::find($request->currency_id);
        $return['vendor'] = Vendor::find($request->vendor_id);

        return $return;
    }

    // ! GET SUMMARY UANG MUKA PEMBELIAN
    public function summary_uang_muka_penjualan($type, $request)
    {
        $from_date = Carbon::parse($request->from_date);
        $to_date = Carbon::parse($request->to_date);

        $data = DB::table('cash_advance_receives')
            ->join('currencies', 'currencies.id', 'cash_advance_receives.currency_id')
            ->join('customers', function ($customer) {
                $customer->on('customers.id', 'cash_advance_receives.customer_id');
            })
            ->join('bank_code_mutations', function ($bank) {
                $bank
                    ->on('bank_code_mutations.ref_id', 'cash_advance_receives.id')
                    ->where('bank_code_mutations.ref_model', CashAdvanceReceive::class);
            })
            ->join('cash_advance_receive_details', 'cash_advance_receive_details.cash_advance_receive_id', 'cash_advance_receives.id')
            ->where('cash_advance_receive_details.type', 'cash_advance')
            ->where('cash_advance_receives.status', '!=', 'void')
            ->when($request->customer_id, function ($q) use ($request) {
                $q->where('cash_advance_receives.customer_id', $request->customer_id);
            })
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('cash_advance_receives.date', '>=', $from_date);
            })
            ->when($to_date, function ($q) use ($to_date) {
                $q->whereDate('cash_advance_receives.date', '<=', $to_date);
            })
            ->selectRaw(
                '
                cash_advance_receives.id as cash_advance_receive_id,
                customers.nama as customer_nama,
                bank_code_mutations.code as bank_code,
                cash_advance_receives.date as cash_advance_date,
                currencies.nama as currency_nama,
                currencies.kode as currency_kode,
                cash_advance_receives.reference,
                cash_advance_receives.exchange_rate,
                cash_advance_receive_details.credit as cash_advance_amount,
                cash_advance_receives.returned_amount,
                cash_advance_receive_details.credit - cash_advance_receives.returned_amount as cash_advance_remaining_amount,
                (cash_advance_receive_details.credit * cash_advance_receives.exchange_rate) as cash_advance_amount_exchanged,
                (cash_advance_receives.returned_amount * cash_advance_receives.exchange_rate) as returned_amount_exchanged,
                ((cash_advance_receive_details.credit - cash_advance_receives.returned_amount)*cash_advance_receives.exchange_rate) as cash_advance_remaining_amount_exchanged'
            )
            ->get();

        $invoice_down_payment_data = DB::table('invoice_down_payments')
            ->join('currencies', 'currencies.id', 'invoice_down_payments.currency_id')
            ->join('customers', function ($customer) {
                $customer->on('customers.id', 'invoice_down_payments.customer_id');
            })
            ->leftJoin('sale_order_generals', function ($query) {
                $query->on('sale_order_generals.id', 'invoice_down_payments.sale_order_model_id')
                    ->where('invoice_down_payments.sale_order_model', SaleOrderGeneral::class)
                    ->whereNull('sale_order_generals.deleted_at');
            })
            ->leftJoin('sale_orders', function ($query) {
                $query->on('sale_orders.id', 'invoice_down_payments.sale_order_model_id')
                    ->where('invoice_down_payments.sale_order_model', SoTrading::class)
                    ->whereNull('sale_orders.deleted_at');
            })
            ->leftJoin('invoice_payments', function ($query) {
                $query->on('invoice_payments.reference_id', 'invoice_down_payments.id')
                    ->where('invoice_payments.model', InvoiceDownPayment::class)
                    ->whereNull('invoice_payments.deleted_at');
            })
            ->where('invoice_down_payments.status', 'approve')
            ->when($request->customer_id, function ($q) use ($request) {
                $q->where('invoice_down_payments.customer_id', $request->customer_id);
            })
            ->when($from_date, function ($q) use ($from_date) {
                $q->whereDate('invoice_down_payments.date', '>=', $from_date);
            })
            ->when($to_date, function ($q) use ($to_date) {
                $q->whereDate('invoice_down_payments.date', '<=', $to_date);
            })
            ->selectRaw(
                '
                invoice_down_payments.id as invoice_down_payment_id,
                customers.nama as customer_nama,
                null as bank_code,
                invoice_down_payments.date as cash_advance_date,
                currencies.nama as currency_nama,
                currencies.kode as currency_kode,
                invoice_down_payments.code as reference,
                invoice_down_payments.exchange_rate,
                invoice_down_payments.grand_total as cash_advance_amount,
                COALESCE(SUM(invoice_payments.receive_amount), 0) as returned_amount,
                invoice_down_payments.grand_total - COALESCE(SUM(invoice_payments.receive_amount), 0) as cash_advance_remaining_amount,
                (invoice_down_payments.grand_total * invoice_down_payments.exchange_rate) as cash_advance_amount_exchanged,
                (COALESCE(SUM(invoice_payments.receive_amount), 0) * invoice_down_payments.exchange_rate) as returned_amount_exchanged,
                ((invoice_down_payments.grand_total - COALESCE(SUM(invoice_payments.receive_amount), 0)) * invoice_down_payments.exchange_rate) as cash_advance_remaining_amount_exchanged,
                invoice_down_payments.sale_order_model,
                invoice_down_payments.sale_order_model_id,
                sale_order_generals.kode as so_code,
                sale_orders.nomor_so as so_trading_code'
            )
            ->groupBy('invoice_down_payments.id')
            ->get();

        $data = $data->merge($invoice_down_payment_data);

        $return['data'] = $data;
        $return['type'] = $type;
        $return['from_date'] = Carbon::parse($request->from_date);
        $return['to_date'] = Carbon::parse($request->to_date);
        $return['customer'] = Customer::find($request->customer_id);

        return $return;
    }
}
