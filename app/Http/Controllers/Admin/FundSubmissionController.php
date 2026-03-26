<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FundSubmissionExport;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\AccountPayable;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\CashAdvancePayment;
use App\Models\CashAdvanceReceive;
use App\Models\CashAdvanceReceiveDetail;
use App\Models\Customer;
use App\Models\CustomerCoa;
use App\Models\FundSubmission as model;
use App\Models\FundSubmission;
use App\Models\FundSubmissionCashAdvance;
use App\Models\FundSubmissionGeneral;
use App\Models\FundSubmissionSupplierDetail;
use App\Models\FundSubmissionSupplierLpb;
use App\Models\InvoiceReturn;
use App\Models\ItemReceivingReport;
use App\Models\Purchase;
use App\Models\PurchaseDownPayment;
use App\Models\PurchaseReturn;
use App\Models\SendPayment;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceGeneral;
use App\Models\SupplierInvoiceParent;
use App\Models\SupplierInvoicePayment;
use App\Models\Vendor;
use App\Models\VendorCoa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FundSubmissionController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'fund-submission';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $repository = new \App\Repository\FundSubmissionRepository();
            return $repository->datatable($request, $request->isGiro, $this->view_folder);
        }

        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(FundSubmission::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        $model = [];

        return view("admin.$this->view_folder.$request->item.create", compact('model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        // * create data
        $model = new model();
        $branch = Branch::find($request->branch_id);

        $to_name = $request->to_name ?? '-';
        if ($request->to_model) {
            $to_name = $request->to_model::find($request->to_id);
        }

        if ($request->is_giro && $request->due_date) {
            // if due date less than date
            if (Carbon::parse($request->due_date)->lt(Carbon::parse($request->date))) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'tanggal jatuh tempo tidak boleh lebih kecil dari tanggal pengajuan dana!'));
            }
        }

        $branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $branch = Branch::find($branch_id);
        $purchase_down_payment = PurchaseDownPayment::find($request->purchase_down_payment_id);

        $model->loadModel([
            'code' => generate_code(model::class, 'code', 'date', "PD", branch_sort: $branch->sort ?? null, date: $request->date),
            'status' => 'pending',
            'created_by' => auth()->user()->id,
            'branch_id' => $branch_id,
            'date' => Carbon::parse($request->date),
            'item' => strtolower($request->item),
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'project_id' => $request->project_id,
            'reference' => $request->referensi,
            'coa_id' => $request->coa_id,
            'purchase_id' => $purchase_down_payment ? $purchase_down_payment->purchase_id : null,
            'purchase_down_payment_id' => $request->purchase_down_payment_id ?? null,
            'is_giro' => $request->is_giro,
            'giro_number' => $request->cheque_no,
            'giro_liquid_date' => Carbon::parse($request->due_date),
            'to_model' => $request->to_model,
            'to_id' => $request->to_id,
            'tax_id' => $request->tax_id,
            'tax_number' => $request->tax_number,
            'to_name' => $to_name->name ?? $to_name->nama ?? $to_name,
            'keterangan' => $request->keterangan ?? '-',
            'customer_id' => $request->customer_id ?? null,
            'invoice_return_id' => $request->invoice_return_id ?? null,
            'cash_advance_receive_id' => $request->cash_advance_receive_id ?? null,
        ]);

        if ($request->hasFile('attachment')) {
            $model->attachment = $this->upload_file($request->file('attachment'), 'fund_submission');
        }

        if (strtolower($request->item) == "dp") {
            $file_path = '';
            if ($request->file('tax_attachment')) {
                $file_path =  $this->upload_file($request->file('tax_attachment'), 'purchase-down-payment');
            } else {
                $file_path = $purchase_down_payment ? $purchase_down_payment->tax_attachment : '';
            }
            $model->tax_attachment = $file_path;

            if ($purchase_down_payment) {
                $purchase_down_payment->tax_number = $request->tax_number ?? $purchase_down_payment->tax_number;
                $purchase_down_payment->tax_attachment = $file_path;
                $purchase_down_payment->save();
            }
        }

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        // * saving
        try {
            $model->save();

            if ($request->is_giro) {
                $send_payment = new SendPayment();
                $branch = Branch::find($model->branch_id);

                $send_payment->loadModel(
                    [
                        'fund_submission_id' => $model->id,
                        'branch_id' => $model->branch_id,
                        'code' => generate_code(SendPayment::class, 'code', 'date', 'GRK', branch_sort: $branch->sort ?? null, date: Carbon::parse($request->date)),
                        'date' => Carbon::parse($request->date),
                        'due_date' => Carbon::parse($request->due_date),
                        'cheque_no' => $request->cheque_no,
                        'from_bank' => $model->coa->bank_internal->nama_bank ?? '',
                        'realization_bank' => $request->realization_bank,
                        'status' => 'pending',
                        'reject_reason' => '',
                    ]
                );

                $send_payment->save();
            }

            if (strtolower($request->item) == "general") {
                foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                    $fund_submission_general = new FundSubmissionGeneral();
                    $fund_submission_general->fund_submission_id = $model->id;
                    $fund_submission_general->coa_id = $coa_detail_id;
                    $fund_submission_general->note = $request->note[$key] ?? '-';
                    $fund_submission_general->debit = thousand_to_float($request->debit[$key] ?? 0);
                    if ($request->is_return[$key] ?? '' == "true") {
                        $fund_submission_general->invoice_return_id = $model->invoice_return_id;
                    }
                    $fund_submission_general->type = $request->type[$key] ?? '-';
                    $fund_submission_general->save();
                }

                $model->total = $model->fund_submission_generals->sum('debit');
                $model->save();
            }

            if (strtolower($request->item) == "dp") {
                $note = $request->note[1] ?? $request->referensi;

                foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                    $note_detail = $request->note[$key] ?? $note;
                    $position = $request->position[$key];
                    $fund_submission_cash_advance = new FundSubmissionCashAdvance();
                    $fund_submission_cash_advance->fund_submission_id = $model->id;
                    $fund_submission_cash_advance->coa_id = $coa_detail_id;
                    $fund_submission_cash_advance->type = $request->type[$key];
                    $fund_submission_cash_advance->note = $note_detail;
                    if ($position == "debit") {
                        $fund_submission_cash_advance->debit = thousand_to_float($request->amount[$key] ?? 0);
                    } else {
                        $fund_submission_cash_advance->credit = thousand_to_float($request->amount[$key] ?? 0);
                    }
                    $fund_submission_cash_advance->save();
                }

                $model->total = $model->fund_submission_cash_advances->sum('debit');
                $model->save();
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: FundSubmission::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Pengajuan Dana $model->item",
                subtitle: Auth::user()->name . " mengajukan Pengajuan Dana $model->item " . $model->code,
                link: route('admin.fund-submission.show', $model),
                update_status_link: route('admin.fund-submission.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = model::with('send_payment')
            ->with('send_payments')
            ->findOrFail($id);

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        $check_can_void = model::where('id', $id)
            ->whereDoesntHave('outgoing_payments', function ($o) {
                $o->where('status', 'pending');
                $o->orWhere('status', 'approve');
                $o->orWhere('status', 'revert');
            })
            ->whereDoesntHave('cash_advance_payments', function ($o) {
                $o->where('status', 'pending');
                $o->orWhere('status', 'approve');
                $o->orWhere('status', 'revert');
            })
            ->whereDoesntHave('account_payables', function ($o) {
                $o->where('status', 'pending');
                $o->orWhere('status', 'approve');
                $o->orWhere('status', 'revert');
            })
            ->first();

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        foreach ($model->fund_submission_supplier_details ?? [] as $key => $fund_submission_supplier_detail) {
            $fund_submission_supplier_detail->payment_informations = SupplierInvoicePayment::where('supplier_invoice_model', $fund_submission_supplier_detail->supplier_invoice_parent->model_reference)
                ->where('supplier_invoice_id', $fund_submission_supplier_detail->supplier_invoice_parent->reference_id)
                ->whereDate('date', '<=', $model->date)
                ->get();

            if ($fund_submission_supplier_detail->supplier_invoice_parent->type != "general") {
                foreach ($fund_submission_supplier_detail->supplier_invoice_parent->reference_model->detail as $key => $detail) {
                    $detail->item_receiving_report->payment_informations = SupplierInvoicePayment::where('item_receiving_report_id', $detail->item_receiving_report->id)
                        ->whereDate('date', '<=', $model->date)
                        ->get();
                }
            }
        }

        $check_unapproved_submission_before = null;
        $check_changed_outstanding_amount = [];
        if ($model->item == "lpb") {
            $check_unapproved_submission_before = model::where('id', '<', $model->id)
                ->whereHas('fund_submission_supplier_details', function ($query) use ($model) {
                    $query->whereIn('supplier_invoice_parent_id', $model->fund_submission_supplier_details->pluck('supplier_invoice_parent_id'));
                })
                ->whereIn('status', ['pending', 'revert'])
                ->orderBy('id', 'desc')
                ->first();

            $check_changed_outstanding_amount = $model->fund_submission_supplier_details;
            $check_changed_outstanding_amount = $check_changed_outstanding_amount->filter(function ($item) {
                return floatFormat($item->outstanding_amount) != floatFormat($item->original_outstanding);
            });
        }

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: FundSubmission::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );
        $authorization_logs['can_approve'] = !$check_unapproved_submission_before && count($check_changed_outstanding_amount) == 0 && ($model->status == 'pending' or $model->status == 'revert');
        $authorization_logs['can_reject'] = !$check_unapproved_submission_before && count($check_changed_outstanding_amount) == 0 && ($model->status == 'pending' or $model->status == 'revert');
        $authorization_logs['cant_approve_reason'] = null;
        if ($check_unapproved_submission_before) {
            $authorization_logs['cant_approve_reason'] = 'Terdapat pengajuan dana sebelumnya yang belum di approve, di nomor transaksi: ' . $check_unapproved_submission_before->code;
        }
        if (count($check_changed_outstanding_amount) > 0) {
            $authorization_logs['cant_approve_reason'] .= ' | Terdapat perubahan outstanding amount';
        }


        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $authorization_logs['approved_count'] > 0 && $check_can_void && $model->can_change_sensitive_data;
        $authorization_logs['can_void_request'] = $authorization_logs['approved_count'] > 0 && $check_can_void && $model->can_change_sensitive_data;

        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();
        $can_edit_or_delete = $authorization_logs['approved_count'] == 0;

        $related_down_payments = [];
        if ($model->item == "dp") {
            $related_down_payments = CashAdvancePayment::where('purchase_id', $model->purchase_id)
                ->whereDoesntHave('fund_submission', function ($query) use ($model) {
                    $query->where('id', $model->id);
                })
                ->orderBy('date', 'asc')
                ->whereIn('status', ['pending', 'approve', 'revert'])
                ->get();
        }

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'check_can_void', 'check_unapproved_submission_before', 'check_changed_outstanding_amount', 'authorization_log_view', 'auth_revert_void_button', 'can_edit_or_delete', 'related_down_payments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = model::with('send_payment')
            ->with('send_payments')
            ->findOrFail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        if ($model->item == "lpb") {
            $fund_submission_supplier_details = $model->fund_submission_supplier_details;
            foreach ($fund_submission_supplier_details as $key => $detail) {
                foreach ($detail->fund_submission_supplier_lpbs as $key => $lpb) {
                    $lpb->code = $lpb->item_receiving_report->kode;
                }
            }
        }
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        if ($model->item == "lpb") {
            $check_changed_outstanding_amount = [];
            if ($model->item == "lpb") {
                $check_changed_outstanding_amount = $model->fund_submission_supplier_details;
                $check_changed_outstanding_amount = $check_changed_outstanding_amount->filter(function ($item) {
                    return floatFormat($item->outstanding_amount) != floatFormat($item->original_outstanding);
                });
            }

            return view("admin.$this->view_folder.$model->item.edit", compact('model', 'fund_submission_supplier_details', 'check_changed_outstanding_amount'));
        } else {
            return view("admin.$this->view_folder.$model->item.edit", compact('model'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        // * create data
        $model = model::findOrFail($id);
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }
        $to_name = $request->to_name ?? '-';
        if ($request->to_model) {
            $to_name = $request->to_model::find($request->to_id);
        }

        if (strtolower($request->item) != $model->item) {
            $model->fund_submission_generals()->delete();
            $model->fund_submission_cash_advance()->delete();
        }

        if ($request->is_giro && $request->due_date) {
            // if due date less than date
            if (strtotime($request->due_date) < strtotime($model->date)) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'tanggal jatuh tempo tidak boleh lebih kecil dari tanggal pengajuan dana!'));
            }
        }

        $branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $branch = Branch::find($branch_id);
        $purchase_down_payment = PurchaseDownPayment::find($request->purchase_down_payment_id);
        $model->loadModel([
            'branch_id' =>  $branch_id,
            'date' => Carbon::parse($request->date),
            'item' => strtolower($request->item),
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'project_id' => $request->project_id,
            'reference' => $request->referensi,
            'is_giro' => $request->is_giro,
            'giro_number' => $request->cheque_no,
            'giro_liquid_date' => Carbon::parse($request->due_date),
            'coa_id' => $request->coa_id,
            'purchase_id' => $purchase_down_payment ? $purchase_down_payment->purchase_id : null,
            'purchase_down_payment_id' => $request->purchase_down_payment_id ?? null,
            'to_model' => $request->to_model,
            'to_id' => $request->to_id,
            'to_name' => $to_name->name ?? $to_name->nama ?? $to_name,
            'tax_id' => $request->tax_id,
            'tax_number' => $request->tax_number,
            'keterangan' => $request->keterangan ?? '-',
            'customer_id' => $request->customer_id ?? null,
            'invoice_return_id' => $request->invoice_return_id ?? null,
            'cash_advabce_receive_id' => $request->cash_advabce_receive_id ?? null,
        ]);

        if ($request->hasFile('attachment')) {
            Storage::delete($model->attachment);
            $model->attachment = $this->upload_file($request->file('attachment'), 'fund_submission');
        }

        if (strtolower($request->item) == "dp") {
            $file_path = '';
            if ($request->file('tax_attachment')) {
                Storage::delete($model->tax_attachment);
                $file_path =  $this->upload_file($request->file('tax_attachment'), 'purchase-down-payment');
            } else {
                $file_path = $purchase_down_payment ? $purchase_down_payment->tax_attachment : '';
            }
            $model->tax_attachment = $file_path;

            if ($purchase_down_payment) {
                $purchase_down_payment->tax_number = $request->tax_number ?? $purchase_down_payment->tax_number;
                $purchase_down_payment->tax_attachment = $file_path;
                $purchase_down_payment->save();
            }
        }

        // * saving
        try {
            $model->save();

            if ($request->is_giro) {
                $send_payment = SendPayment::where('fund_submission_id', $model->id)
                    ->first();

                if (!$send_payment) {
                    $send_payment = new SendPayment();
                    $send_payment->status = "pending";
                }

                $branch = Branch::find($model->branch_id);

                $send_payment->loadModel(
                    [
                        'fund_submission_id' => $model->id,
                        'branch_id' => $model->branch_id,
                        'code' => generate_code(SendPayment::class, 'code', 'date', 'GRK', branch_sort: $branch->sort ?? null, date: Carbon::parse($request->date)),
                        'date' => Carbon::parse($request->date),
                        'due_date' => Carbon::parse($request->due_date),
                        'cheque_no' => $request->cheque_no,
                        'from_bank' => $model->coa->bank_internal->nama_bank ?? '',
                        'realization_bank' => $request->realization_bank,
                        'reject_reason' => '',
                    ]
                );

                $send_payment->save();
            } else {
                $send_payment = SendPayment::where('fund_submission_id', $model->id)
                    ->delete();
            }

            if (strtolower($request->item) == "general") {
                $fund_submission_general_ids = $request->fund_submission_general_id ?? [];
                foreach ($fund_submission_general_ids as $key => $fund_submission_general_id) {
                    if (!$fund_submission_general_id) {
                        $fund_submission_general_ids[$key] = '';
                    }
                }


                $existing_detail = collect($request->fund_submission_general_id ?? [])->filter(fn($item) => $item != null)->toArray();
                if (count($existing_detail ?? []) > 0) {
                    FundSubmissionGeneral::where('fund_submission_id', $model->id)
                        ->whereNotIn('id', $existing_detail)->delete();
                } else {
                    FundSubmissionGeneral::where('fund_submission_id', $model->id)->delete();
                }

                foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                    $fund_submission_general = FundSubmissionGeneral::find($request->fund_submission_general_id[$key]);
                    if (!$fund_submission_general) {
                        $fund_submission_general = new FundSubmissionGeneral();
                    }
                    $fund_submission_general->fund_submission_id = $model->id;
                    $fund_submission_general->coa_id = $coa_detail_id;
                    $fund_submission_general->note = $request->note[$key] ?? '-';
                    $fund_submission_general->type = $request->type[$key] ?? '-';
                    $fund_submission_general->debit = thousand_to_float($request->debit[$key] ?? 0);
                    if ($request->is_return[$key] ?? '' == "true") {
                        $fund_submission_general->invoice_return_id = $model->invoice_return_id;
                    }
                    $fund_submission_general->save();
                }

                $model->total = $model->fund_submission_generals->sum('debit');
                $model->save();
            }

            if (strtolower($request->item) == "dp") {
                $fund_submission_cash_advance_ids = $request->fund_submission_cash_advance_id ?? [];
                foreach ($fund_submission_cash_advance_ids as $key => $fund_submission_cash_advance_id) {
                    if (!$fund_submission_cash_advance_id) {
                        $fund_submission_cash_advance_ids[$key] = '';
                    }
                }

                FundSubmissionCashAdvance::where('fund_submission_id', $model->id)
                    ->whereNotIn('id', $fund_submission_cash_advance_ids)
                    ->delete();

                $note = $request->note[1] ?? $request->referensi;
                foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                    $note_detail = $request->note[$key] ?? $note;
                    $position = $request->position[$key];
                    $fund_submission_cash_advance = FundSubmissionCashAdvance::find($request->fund_submission_cash_advance_id[$key]);
                    if (!$fund_submission_cash_advance) {
                        $fund_submission_cash_advance = new FundSubmissionCashAdvance();
                    }
                    $fund_submission_cash_advance->fund_submission_id = $model->id;
                    $fund_submission_cash_advance->coa_id = $coa_detail_id;
                    $fund_submission_cash_advance->type = $request->type[$key];
                    $fund_submission_cash_advance->note = $note_detail;
                    if ($position == "debit") {
                        $fund_submission_cash_advance->debit = thousand_to_float($request->amount[$key] ?? 0);
                    } else {
                        $fund_submission_cash_advance->credit = thousand_to_float($request->amount[$key] ?? 0);
                    }
                    $fund_submission_cash_advance->save();
                }

                $model->total = $model->fund_submission_cash_advances->sum('debit');
                $model->save();
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: FundSubmission::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Pengajuan Dana $model->item",
                subtitle: Auth::user()->name . " mengajukan Pengajuan Dana $model->item " . $model->code,
                link: route('admin.fund-submission.show', $model),
                update_status_link: route('admin.fund-submission.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $model = model::findOrFail($id);
        DB::beginTransaction();
        try {
            $model = model::find($id);
            $model->fund_submission_generals()->delete();
            $model->fund_submission_cash_advances()->delete();
            $model->fund_submission_supplier()->delete();
            $model->fund_submission_supplier_details()->delete();
            $model->fund_submission_supplier_others()->delete();

            $model->delete();

            Authorization::where('model', FundSubmission::class)
                ->where('model_id', $model->id)
                ->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }
        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(false, 'delete');
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    public function vendor_coa(Request $request, $id)
    {
        $model = VendorCoa::where('vendor_id', $id)
            ->where('type', $request->type)
            ->with('coa')
            ->first();

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }
    }

    /**
     * update status item receiving report
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        DB::beginTransaction();
        $model = model::findOrfail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        if ($model->is_used) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Pengajuan dana sudah digunakan'));
        }
        validate_branch($model->branch_id);

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id,  $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->status = $request->status;
                $model->save();
            } else {
                $this->create_activity_status_log(model::class, $id,  $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request, $type = 'general')
    {
        $date = Carbon::now();
        if ($request->date) {
            $date = Carbon::parse($request->date);
        }

        $branch_id = Auth::user()->branch->is_primary ? null : Auth::user()->branch_id;
        $model = model::where('status', '=', 'approve')
            ->when($date, function ($query, $date) {
                $query->whereDate('date', '<=', $date);
            })
            ->where(function ($query) use ($request) {
                $query->orWhere('code', 'like', "%$request->search%");
                $query->orWhere('to_name', 'like', "%$request->search%");
            })
            ->when($branch_id, function ($query, $branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->orderByDesc('date');

        if ($request->item) {
            $model->where('item', $request->item);
            if ($request->item == "lpb") {
                $model->where('to_id', $request->to_id);
            }
        }

        if ($request->to_id) {
            $model->where('to_id', $request->to_id);
        }

        if ($request->available) {
            $model->whereDoesntHave('outgoing_payments', function ($o) {
                $o->where('status', 'pending');
                $o->orWhere('status', 'approve');
                $o->orWhere('status', 'revert');
            });
            $model->whereDoesntHave('cash_advance_payments', function ($o) {
                $o->where('status', 'pending');
                $o->orWhere('status', 'approve');
                $o->orWhere('status', 'revert');
            });
            $model->whereDoesntHave('account_payables', function ($o) {
                $o->where('status', 'pending');
                $o->orWhere('status', 'approve');
                $o->orWhere('status', 'revert');
            });
        }
        $model = $model->limit(10)->get();

        $data = [];
        foreach ($model as $key => $m) {
            $m->total = formatNumber($m->total);
            if ($m->is_giro) {
                if (Carbon::parse($m->giro_liquid_date)->lte($date)) {
                    array_push($data, $m);
                }
            } else {
                array_push($data, $m);
            }
        }

        return $this->ResponseJsonData($data);
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function supplier_invoice_select(Request $request)
    {
        $branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $branch = Branch::find($branch_id);

        $supplier_invoices_query = SupplierInvoiceParent::where('vendor_id', $request->vendor_id)
            ->where('lock_status', 0)
            ->where('status', 'approve')
            ->where('payment_status', '!=', 'paid')
            ->when(!$branch->is_primary, fn($query) => $query->where('branch_id', $branch_id))
            ->whereHas('currency', fn($currency) => $currency->where('currency_id', $request->currency_id))
            ->when($request->selected_id, fn($query) => $query->whereIn('id', $request->selected_id))
            ->when($request->except_id, fn($query) => $query->whereNotIn('id', $request->except_id))
            ->when($request->date, fn($query) => $query->whereDate('date', '<=', Carbon::parse($request->date)))
            ->with(['currency', 'vendor.vendor_coas.coa'])
            ->orderBy('date');

        if ($request->supplier_invoice_parent_id) {
            $supplier_invoice = $supplier_invoices_query->where('id', $request->supplier_invoice_parent_id)->first();

            $unapproved_submission = FundSubmissionSupplierDetail::where('supplier_invoice_parent_id', $supplier_invoice->id)
                ->join('fund_submissions', 'fund_submissions.id', 'fund_submission_supplier_details.fund_submission_id')
                ->whereIn('fund_submissions.status', ['pending', 'revert', 'approve'])
                ->whereNull('fund_submissions.deleted_at')
                ->where('fund_submissions.is_used', 0)
                ->selectRaw('supplier_invoice_parent_id, COALESCE(SUM(total_foreign), 0) as total_submission')
                ->groupBy('supplier_invoice_parent_id')
                ->when($request->fund_submission_id, fn($query) => $query->where('fund_submission_id', '<', $request->fund_submission_id))
                ->when($supplier_invoice->outstanding_amount < 0, fn($query) => $query->where('total_foreign', '<', 0))
                ->get();

            $supplier_invoice->total_submission = $unapproved_submission->first()->total_submission ?? 0;
            $supplier_invoice->temp_outstanding = $supplier_invoice->outstanding_amount - $supplier_invoice->total_submission;

            $invoice_model = $supplier_invoice->model_reference::find($supplier_invoice->reference_id);
            $supplier_invoice->supplier_invoice_payment = $invoice_model->supplier_invoice_payment() ?? [];

            if ($supplier_invoice->type == "trading") {
                $item_receiving_reports = ItemReceivingReport::whereIn('id', $invoice_model->detail->pluck('item_receiving_report_id')->toArray())->get();

                $pending_payments = FundSubmissionSupplierLpb::whereIn('item_receiving_report_id', $item_receiving_reports->pluck('id')->toArray())
                    ->whereHas('fund_submission_supplier_detail', fn($query) => $query
                        ->whereNull('fund_submission_supplier_details.deleted_at')
                        ->whereHas('fund_submission', fn($query) => $query
                            ->whereNull('fund_submissions.deleted_at')
                            ->whereIn('fund_submissions.status', ['pending', 'revert', 'approve'])
                            ->where('is_used', 0))
                        ->when($request->fund_submission_id, fn($query) => $query->where('fund_submission_id', '<', $request->fund_submission_id)
                            ->where('fund_submission_id', '!=', $request->fund_submission_id)))
                    ->get();

                foreach ($item_receiving_reports as $report) {
                    $report->outstanding_temp = $report->outstanding - $pending_payments->where('item_receiving_report_id', $report->id)->sum('amount_foreign');
                }

                $supplier_invoice->item_receiving_reports = $item_receiving_reports;
            } else {
                $supplier_invoice->item_receiving_reports = [];
            }

            return response()->json($supplier_invoice);
        }

        $supplier_invoices = $supplier_invoices_query->get();

        $get_unapproved_submissions = FundSubmissionSupplierDetail::with('fund_submission_supplier_lpbs')
            ->whereIn('supplier_invoice_parent_id', $supplier_invoices->pluck('id')->toArray())
            ->join('fund_submissions', 'fund_submissions.id', 'fund_submission_supplier_details.fund_submission_id')
            ->whereIn('fund_submissions.status', ['pending', 'revert', 'approve'])
            ->whereNull('fund_submissions.deleted_at')
            ->where('fund_submissions.is_used', 0)
            ->selectRaw('supplier_invoice_parent_id, COALESCE(SUM(total_foreign), 0) as total_submission')
            ->groupBy('supplier_invoice_parent_id')
            ->when($request->fund_submission_id, fn($query) => $query->where('fund_submission_id', '<', $request->fund_submission_id)
                ->where('fund_submission_id', '!=', $request->fund_submission_id))
            ->get();

        $supplier_invoice_data = SupplierInvoice::with('detail.item_receiving_report')
            ->whereIn('id', $supplier_invoices->where('model_reference', SupplierInvoice::class)->pluck('reference_id')->toArray())
            ->get();


        $supplier_invoice_data = SupplierInvoice::with('detail.item_receiving_report')
            ->whereIn('id', $supplier_invoices->where('model_reference', SupplierInvoice::class)->pluck('reference_id')->toArray())
            ->get();

        $item_receiving_reports = ItemReceivingReport::whereIn('id', $supplier_invoice_data->flatMap(fn($invoice) => $invoice->detail->pluck('item_receiving_report_id'))->toArray())
            ->get();

        $pending_item_receiving_report_payments = FundSubmissionSupplierLpb::whereIn('item_receiving_report_id', $item_receiving_reports->pluck('id')->toArray())
            ->whereHas('fund_submission_supplier_detail', function ($query) {
                $query
                    ->whereNull('fund_submission_supplier_details.deleted_at')
                    ->whereHas('fund_submission', function ($query) {
                        $query
                            ->whereNull('fund_submissions.deleted_at')
                            ->whereIn('status', ['pending', 'revert', 'approve'])
                            ->where('is_used', 0);
                    })
                    ->when(request()->fund_submission_id, function ($query, $fund_submission_id) {
                        $query->where('fund_submission_id', '<', $fund_submission_id)
                            ->where('fund_submission_id', '!=', $fund_submission_id);
                    });
            })
            ->get();

        $supplier_invoice_general_data = SupplierInvoiceGeneral::whereIn('id', $supplier_invoices->where('model_reference', SupplierInvoiceGeneral::class)->pluck('reference_id')->toArray())
            ->get();

        foreach ($supplier_invoices as $invoice) {
            if ($invoice->type == "trading") {
                $inv = $supplier_invoice_data->where('id', $invoice->reference_id)->first();
                $item_receiving_report_data = $item_receiving_reports->filter(function ($item) use ($inv) {
                    return in_array($item->id, $inv->detail->pluck('item_receiving_report_id')->toArray());
                });

                $invoice->item_receiving_reports = $item_receiving_report_data->map(function ($item) use ($pending_item_receiving_report_payments) {
                    $cash_advance = $item_receiving_report->reference->purchase->cash_advance_payments ?? [];
                    if (count($cash_advance) > 0) {
                        $item->is_has_cash_advance = true;
                    } else {
                        $item->is_has_cash_advance = false;
                    }

                    $item->outstanding_temp = $item->outstanding - $pending_item_receiving_report_payments->where('item_receiving_report_id', $item->id)->sum('amount_foreign');


                    return $item;
                });
            } else {
                $inv = $supplier_invoice_general_data->where('id', $invoice->reference_id)->first();
                $invoice->item_receiving_reports = [];
            }
            $invoice->total_submission = $get_unapproved_submissions->where('supplier_invoice_parent_id', $invoice->id)->first()->total_submission ?? 0;
            $invoice->temp_outstanding = $invoice->outstanding_amount - $invoice->total_submission;
        }

        return response()->json($supplier_invoices->filter(fn($invoice) => $invoice->total_submission != $invoice->total));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function supplier_invoice_general_select(Request $request)
    {
        $supplier_invoices = SupplierInvoiceGeneral::where('vendor_id', $request->vendor_id)
            ->where('status', 'approved')
            ->whereHas('currency', function ($currency) use ($request) {
                $currency->where('currency_id', $request->currency_id);
            });

        if ($request->selected_id) {
            $supplier_invoices->whereIn('id', $request->selected_id);
        }

        if ($request->except_id) {
            $supplier_invoices->whereNotIn('id', $request->except_id);
        }

        $supplier_invoices = $supplier_invoices
            ->with('currency')
            ->with('vendor.vendor_coas.coa')
            ->orderBy('date')
            ->where('status', '!=', 'paid');

        if ($request->supplier_invoice_general_id) {
            $supplier_invoices = $supplier_invoices->where('id', $request->supplier_invoice_general_id)->first();
        } else {
            $supplier_invoices = $supplier_invoices->get();
        }

        return response()->json($supplier_invoices);
    }

    public function export($id, Request $request)
    {
        $model = model::with(['fund_submission_supplier', 'fund_submission_supplier_details'])->findOrFail(decryptId($id));
        $vendor = $model->to_model == Vendor::class ? Vendor::find($model->to_id) : null;

        $is_payment_history = false;

        $fileName = 'PERMINTAAN DANA ' . strtoupper($model->item) . '.pdf';

        $qr_url = route('fund-submission.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $get_all_dp_with_related_po = [];
        $related_down_payments = [];

        if ($model->item == "lpb") {
            // get all deposit vendors
            $fund_submission_supplier_detail_ids = $model->fund_submission_supplier_details->pluck('id')->toArray();
            $fund_submission_lpbs = FundSubmissionSupplierLpb::whereIn('fund_submission_supplier_detail_id', $fund_submission_supplier_detail_ids)
                ->get();

            $item_receiving_reports = ItemReceivingReport::wherein('id', $fund_submission_lpbs->pluck('item_receiving_report_id')->toArray())
                ->get();

            $purchases = [];
            foreach ($item_receiving_reports as $key => $item_receiving_report) {
                $purchase = $item_receiving_report->reference->purchase;
                $purchases[] = $purchase;
            }

            $purchases = collect($purchases)->unique('id');
            $cash_advance_payments = CashAdvancePayment::whereIn('purchase_id', $purchases->pluck('id')->toArray())
                ->where('status', 'approve')
                ->get();


            // get payment information
            foreach ($model->fund_submission_supplier_details ?? [] as $key => $fund_submission_supplier_detail) {
                $payments = SupplierInvoicePayment::where('supplier_invoice_model', $fund_submission_supplier_detail->supplier_invoice_parent->model_reference)
                    ->where('supplier_invoice_id', $fund_submission_supplier_detail->supplier_invoice_parent->reference_id)
                    ->where('date', '<', $model->date)
                    ->get();

                if (count($payments->where('pay_amount', '!=',  0)) > 0) {
                    $is_payment_history = true;
                }
                $fund_submission_supplier_detail->payment_informations = $payments;

                if ($fund_submission_supplier_detail->supplier_invoice_parent->type != "general") {
                    foreach ($fund_submission_supplier_detail->supplier_invoice_parent->reference_model->detail as $key => $detail) {
                        $payments = SupplierInvoicePayment::where('item_receiving_report_id', $detail->item_receiving_report->id)
                            ->where('date', '<', $model->date)
                            ->get();

                        if (count($payments->where('pay_amount', '!=',  0)) > 0) {
                            $is_payment_history = true;
                        }
                        $detail->item_receiving_report->payment_informations = $payments;
                    }
                }
            }

            $pdf = PDF::loadview("admin/.$this->view_folder./export_lpb", compact('model', 'qr', 'is_payment_history', 'cash_advance_payments', 'vendor'))
                ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        } else {
            if ($model->item == "dp") {
                $get_all_dp_with_related_po = FundSubmission::where('purchase_id', $model->purchase_id)
                    ->where('id', '!=', $model->id)
                    ->where('status', 'approve')
                    ->get();

                $related_down_payments = CashAdvancePayment::where('purchase_id', $model->purchase_id)
                    ->whereDoesntHave('fund_submission', function ($query) use ($model) {
                        $query->where('id', $model->id);
                    })
                    ->orderBy('date', 'asc')
                    ->where('status', 'approve')
                    ->get();
            }

            $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr', 'is_payment_history', 'get_all_dp_with_related_po', 'related_down_payments', 'vendor'))
                ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        }

        return $pdf->stream($fileName);
    }

    public function download_recap(Request $request)
    {
        try {
            $data = model::leftJoin('projects', 'projects.id', 'fund_submissions.project_id')
                ->select('fund_submissions.*', 'projects.name as project_name')
                ->whereNull('fund_submissions.deleted_at')
                ->where('fund_submissions.status', '!=', 'void');
            if ($request->from_date) {
                $data = $data->whereDate('fund_submissions.date', '>=', Carbon::parse($request->from_date))
                    ->whereDate('fund_submissions.date', '<=', Carbon::parse($request->to_date));
            }
            if (!get_current_branch()->is_primary) {
                $data->where('fund_submissions.branch_id', get_current_branch_id());
            }
            if ($request->branch_id && $request->branch_id != 'null') {
                $data->where('fund_submissions.branch_id', $request->branch_id);
            }
            if (($request->is_used || $request->is_used == 0) && $request->is_used != '') {
                $data->where('fund_submissions.is_used', $request->is_used);
            }
            $data = $data->get();

            $return['data'] = $data;
            $return['from_date'] = Carbon::parse($request->from_date);
            $return['to_date'] = Carbon::parse($request->to_date);

            if ($request->format == "pdf") {
                $view_file = 'admin.' . $this->view_folder  . '.recap_pdf';
                $pdf = Pdf::loadView($view_file, $return)
                    ->setPaper('a4', 'potrait');

                return $pdf->stream("LIST PENGAJUAN DANA" . '.pdf');
            } else {
                $view_file = 'admin.' . $this->view_folder  . '.recap_excel';
                return Excel::download(new FundSubmissionExport($view_file, $return), 'LIST PENGAJUAN DANA.xlsx');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select_purchase(Request $request)
    {
        $branch_id = !get_current_branch()->is_primary ? get_current_branch_id() : ($request->branch_id ?? Auth::user()->branch_id);
        $model = Purchase::when($request->search, function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->where('purchases.kode', 'like', "%$request->search%");
            });
        })
            ->whereNotNull('purchases.kode')
            ->join('vendors', 'vendors.id', 'purchases.vendor_id')
            ->select('purchases.*', 'vendors.nama as vendor_name')
            ->when($request->vendor_id, function ($query, $vendor_id) {
                $query->where('vendor_id', $vendor_id);
            })
            ->when($request->currency_id, function ($query, $currency_id) {
                $query->where('currency_id', $currency_id);
            })
            ->orderBy('tanggal', 'desc')
            ->whereNotIn('status', ['pending', 'void', 'reject', 'cancel'])
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    public function show_purchase(Request $request, $id)
    {
        if ($request->ajax()) {
            $purchase = Purchase::with('vendor')->findOrFail($id);
            if ($purchase) {
                $purchase->total = $purchase->model_reference::find($purchase->model_id)->total;
            }

            return $this->ResponseJsonData($purchase);
        }
    }

    public function select_purchase_down_payment(Request $request)
    {
        $model = PurchaseDownPayment::when($request->search, function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->where('purchases.kode', 'like', "%$request->search%")
                    ->orWhere('purchase_down_payments.code', 'like', "%$request->search%");
            });
        })
            ->whereNotNull('purchases.kode')
            ->join('purchases', 'purchases.id', 'purchase_down_payments.purchase_id')
            ->join('vendors', 'vendors.id', 'purchases.vendor_id')
            ->select('purchase_down_payments.*', 'vendors.nama as vendor_name', 'purchases.kode')
            ->when($request->vendor_id, function ($query, $vendor_id) {
                $query->where('purchase_down_payments.vendor_id', $vendor_id);
            })
            ->when($request->currency_id, function ($query, $currency_id) {
                $query->where('purchase_down_payments.currency_id', $currency_id);
            })
            ->where(function ($query) use ($request) {
                $query->whereDoesntHave('fund_submissions', function ($q) use ($request) {
                    $q->whereIn('status', ['pending', 'approve', 'revert']);
                })
                    ->when($request->selected_id, function ($q) use ($request) {
                        if (is_array($request->selected_id)) {
                            $q->orWhereIn('purchase_down_payments.id', $request->selected_id);
                        } else {
                            $q->orWhere('purchase_down_payments.id', $request->selected_id);
                        }
                    });
            })
            ->orderBy('purchase_down_payments.date', 'desc')
            ->whereNotIn('purchase_down_payments.status', ['pending', 'void', 'reject', 'cancel'])
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    public function show_purchase_down_payment(Request $request, $id)
    {
        if ($request->ajax()) {
            $purchase_down_payment = PurchaseDownPayment::with('purchase_down_payment_taxes.tax')
                ->with('vendor.vendor_coas.coa')
                ->findOrFail($id);
            if ($purchase_down_payment) {
                $purchase_down_payment->purchase_total = $purchase_down_payment->purchase->model_reference::find($purchase_down_payment->purchase->model_id)->total;
            }

            return $this->ResponseJsonData($purchase_down_payment);
        }
    }

    public function checkdateFundSubmission(Request $request, $id)
    {
        $date = Carbon::parse($request->date);
        $model = model::whereDate('date', '<=', $date)
            ->find($id);

        if ($model) {
            if ($model->is_giro) {
                if ($date->lt(Carbon::parse($model->giro_liquid_date))) {
                    return $this->ResponseJsonData([
                        'status' => false,
                    ]);
                }
            }

            return $this->ResponseJsonData([
                'status' => true,
            ]);
        } else {
            return $this->ResponseJsonData([
                'status' => false,
            ]);
        }
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function purchase_return_select(Request $request)
    {
        $branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $purchase_returns = PurchaseReturn::where('vendor_id', $request->vendor_id)
            ->leftJoin('purchase_return_histories', function ($purchase_return_histories) {
                $purchase_return_histories->on('purchase_return_histories.purchase_return_id', 'purchase_returns.id')
                    ->whereIn('purchase_return_histories.status', ['pending', 'revert', 'approve'])
                    ->whereNull('purchase_return_histories.deleted_at');
            })
            ->selectRaw('purchase_returns.*, COALESCE(sum(purchase_return_histories.amount), 0) as total_used')
            ->havingRaw('purchase_returns.total > total_used')
            ->where('purchase_returns.status', 'approve')
            ->where('purchase_returns.branch_id', $branch_id)
            ->whereHas('currency', function ($currency) use ($request) {
                $currency->where('currency_id', $request->currency_id);
            })
            ->when($request->selected_id, function ($purchase_returns) use ($request) {
                $purchase_returns->whereIn('purchase_returns.id', $request->selected_id);
            })
            ->when($request->except_id, function ($purchase_returns) use ($request) {
                $purchase_returns->whereNotIn('purchase_returns.id', $request->except_id);
            })
            ->when($request->date, function ($purchase_returns) use ($request) {
                $purchase_returns->whereDate('purchase_returns.date', '<=', Carbon::parse($request->date));
            })
            ->with('currency')
            ->with('vendor.vendor_coas.coa')
            ->orderBy('purchase_returns.date');

        if ($request->purchase_return_id) {
            $purchase_returns = $purchase_returns->where('id', $request->purchase_return_id)->first();
            $purchase_returns->outstanding = $purchase_returns->total - $purchase_returns->total_used;
        } else {
            $purchase_returns = $purchase_returns
                ->groupBy('purchase_returns.id')
                ->get();
            $purchase_returns->each(function ($purchase_return) {
                $purchase_return->outstanding = $purchase_return->total - $purchase_return->total_used;
            });
        }

        return response()->json($purchase_returns);
    }

    public function invoice_return_select(Request $request)
    {
        $invoice_returns = InvoiceReturn::leftJoin('invoice_return_histories', function ($invoice_return_histories) {
            $invoice_return_histories->on('invoice_return_histories.invoice_return_id', 'invoice_returns.id')
                ->whereIn('invoice_return_histories.status', ['pending', 'revert', 'approve'])
                ->whereNull('invoice_return_histories.deleted_at');
        })
            ->selectRaw(
                'invoice_returns.*,
        COALESCE(sum(invoice_return_histories.amount), 0) as total_used,
        invoice_returns.total - COALESCE(sum(invoice_return_histories.amount), 0) as remaining_amount,
        customers.nama as customer_name',
            )
            ->havingRaw('invoice_returns.total > coalesce(total_used,0)')
            ->where('invoice_returns.status', 'approve')
            ->whereHas('currency', function ($currency) use ($request) {
                $currency->where('currency_id', $request->currency_id);
            })
            ->when(get_current_branch()->is_primary, function ($invoice_returns) use ($request) {
                $invoice_returns->where('invoice_returns.branch_id', $request->branch_id ?? get_current_branch_id());
            })
            ->when(!get_current_branch()->is_primary, function ($invoice_returns) use ($request) {
                $invoice_returns->where('invoice_returns.branch_id', get_current_branch_id());
            })
            ->join('customers', 'customers.id', 'invoice_returns.customer_id')
            ->when($request->search, function ($invoice_returns) use ($request) {
                $invoice_returns->where(function ($invoice_returns) use ($request) {
                    $invoice_returns->where('invoice_returns.code', 'like', "%$request->search%")
                        ->orWhere('customers.nama', 'like', "%$request->search%");
                });
            })
            ->when($request->date, function ($invoice_returns) use ($request) {
                $invoice_returns->whereDate('invoice_returns.date', '<=', Carbon::parse($request->date));
            })
            ->groupBy('invoice_returns.id')
            ->get();

        return $this->ResponseJsonData($invoice_returns);
    }

    public function invoice_return_detail(Request $request, $id)
    {
        $invoice_return = InvoiceReturn::findOrFail($id);
        $invoice_return_histories = $invoice_return->invoice_return_histories()
            ->whereIn('status', ['pending', 'revert', 'approve'])
            ->whereNull('deleted_at')
            ->get();

        $invoice_return->used_amount = $invoice_return_histories->sum('amount');
        $invoice_return->remaining_amount = $invoice_return->total - $invoice_return->used_amount;

        $customer = Customer::where('id', $invoice_return->customer_id)
            ->first();

        $customer_account_receivable_coa = CustomerCoa::where('customer_id', $invoice_return->customer_id)
            ->where('tipe', 'Account Receivable Coa')
            ->first();

        $data = [
            'invoice_return' => $invoice_return,
            'customer' => $customer,
            'customer_account_receivable_coa' => $customer_account_receivable_coa->coa,
        ];

        return response()->json($data);
    }

    public function history($id, Request $request)
    {
        try {
            $fund_submission = DB::table('fund_submissions')
                ->where('id', $id)
                ->first();

            $fund_submissions = DB::table('fund_submission_supplier_details')
                ->join('fund_submissions', 'fund_submissions.id', '=', 'fund_submission_supplier_details.fund_submission_id')
                ->whereNull('fund_submissions.deleted_at')
                ->whereNull('fund_submission_supplier_details.deleted_at')
                ->where('fund_submissions.id', $id)
                ->select(
                    'fund_submissions.id',
                    'fund_submissions.item',
                    'fund_submissions.code',
                    'fund_submissions.date',
                    'fund_submission_supplier_details.supplier_invoice_parent_id'
                )
                ->get();

            if ($fund_submission->item == "lpb") {
                $supplier_invoices = DB::table('supplier_invoice_details')
                    ->join('supplier_invoices', 'supplier_invoices.id', '=', 'supplier_invoice_details.supplier_invoice_id')
                    ->join('supplier_invoice_parents', function ($j) {
                        $j->on('supplier_invoice_parents.reference_id', '=', 'supplier_invoices.id')
                            ->where('supplier_invoice_parents.model_reference', 'App\Models\SupplierInvoice');
                    })
                    ->whereNull('supplier_invoices.deleted_at')
                    ->whereNull('supplier_invoice_details.deleted_at')
                    ->whereIn('supplier_invoice_parents.id', $fund_submissions->pluck('supplier_invoice_parent_id')->toArray())
                    ->select(
                        'supplier_invoices.id',
                        'supplier_invoices.code',
                        'supplier_invoices.accepted_doc_date as date',
                        'supplier_invoice_details.item_receiving_report_id',
                        'supplier_invoice_parents.id as supplier_invoice_parent_id'
                    )
                    ->get();

                $item_receiving_reports = DB::table('item_receiving_reports')
                    ->whereIn('id', $supplier_invoices->pluck('item_receiving_report_id')->toArray())
                    ->whereNull('item_receiving_reports.deleted_at')
                    ->select(
                        'item_receiving_reports.id',
                        'item_receiving_reports.kode as code',
                        'item_receiving_reports.date_receive as date',
                        'item_receiving_reports.reference_id',
                        'item_receiving_reports.reference_model',
                        'item_receiving_reports.tipe'
                    )
                    ->get();

                $purchase_order_generals = DB::table('purchase_order_general_details')
                    ->join('purchase_order_generals', 'purchase_order_generals.id', '=', 'purchase_order_general_details.purchase_order_general_id')
                    ->whereNull('purchase_order_generals.deleted_at')
                    ->whereIn('purchase_order_general_id', $item_receiving_reports
                        ->where('reference_model', 'App\Models\PurchaseOrderGeneral')
                        ->pluck('reference_id')->toArray())
                    ->select(
                        'purchase_order_generals.id',
                        'purchase_order_generals.code',
                        'purchase_order_generals.date',
                        'purchase_order_generals.status',
                        'purchase_order_general_details.purchase_request_id',
                        'purchase_order_general_details.sales_order_general_id',
                    )
                    ->get();

                $purchase_order_services = DB::table('purchase_order_service_details')
                    ->join('purchase_order_services', 'purchase_order_services.id', '=', 'purchase_order_service_details.purchase_order_service_id')
                    ->whereNull('purchase_order_services.deleted_at')
                    ->whereIn('purchase_order_services.id', $item_receiving_reports
                        ->where('reference_model', 'App\Models\PurchaseOrderService')
                        ->pluck('reference_id')->toArray())
                    ->select(
                        'purchase_order_services.id',
                        'purchase_order_services.code',
                        'purchase_order_services.date',
                        'purchase_order_services.status',
                        'purchase_order_service_details.purchase_request_id',
                    )
                    ->get();

                $purchase_orders = DB::table('purchase_orders')
                    ->whereIn('id', $item_receiving_reports
                        ->where('reference_model', 'App\Models\PoTrading')
                        ->pluck('reference_id')->toArray())
                    ->select(
                        'purchase_orders.id',
                        'purchase_orders.nomor_po as code',
                        'purchase_orders.tanggal as date',
                        'purchase_orders.status',
                    )
                    ->get();

                $purchase_transports = DB::table('purchase_transports')
                    ->whereIn('id', $item_receiving_reports
                        ->where('reference_model', 'App\Models\PurchaseTransport')
                        ->pluck('reference_id')->toArray())
                    ->select(
                        'purchase_transports.id',
                        'purchase_transports.kode as code',
                        'purchase_transports.target_delivery as date',
                        'purchase_transports.status',
                    )
                    ->get();

                $purchase_request_id = $purchase_order_generals->pluck('purchase_request_id')->toArray();
                $purchase_request_id = array_merge($purchase_request_id, $purchase_order_services->pluck('purchase_request_id')->toArray());

                $sales_order_general_id = $purchase_order_generals->pluck('sales_order_general_id')->toArray();

                $purhase_requests = DB::table('purchase_requests')
                    ->whereIn('id', $purchase_request_id)
                    ->whereNull('deleted_at')
                    ->whereIn('status', ['approve', 'done', 'partial'])
                    ->select(
                        'id',
                        'kode as code',
                        'tanggal as date'
                    )
                    ->get();

                $sale_order_generals = DB::table('sale_order_generals')
                    ->whereIn('id', $sales_order_general_id)
                    ->whereNull('deleted_at')
                    ->whereNotIn('status', ['reject', 'void'])
                    ->select(
                        'id',
                        'kode as code',
                        'tanggal as date'
                    )
                    ->get();

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

                $supplier_invoice_generals = DB::table('supplier_invoice_generals')
                    ->join('supplier_invoice_parents', function ($j) {
                        $j->on('supplier_invoice_parents.reference_id', '=', 'supplier_invoice_generals.id')
                            ->where('supplier_invoice_parents.model_reference', 'App\Models\SupplierInvoiceGeneral');
                    })
                    ->whereNull('supplier_invoice_generals.deleted_at')
                    ->whereIn('supplier_invoice_parents.id', $fund_submissions->pluck('supplier_invoice_parent_id')->toArray())
                    ->select(
                        'supplier_invoice_generals.id',
                        'supplier_invoice_generals.code',
                        'supplier_invoice_generals.date',
                        'supplier_invoice_parents.id as supplier_invoice_parent_id'
                    )
                    ->get();

                $supplier_invoice_generals = $supplier_invoice_generals->map(function ($item) {
                    $item->link = route('admin.supplier-invoice-general.show', $item->id);
                    $item->menu = 'purchase invoice (non LPB)';
                    return $item;
                });

                $supplier_invoices = $supplier_invoices->map(function ($item) {
                    $item->link = route('admin.supplier-invoice.show', $item->id);
                    $item->menu = 'purchase invoice';
                    return $item;
                });

                $purchase_order_generals = $purchase_order_generals->map(function ($item) {
                    $item->link = route('admin.purchase-order-general.show', $item->id);
                    $item->menu = 'purchase order general';
                    return $item;
                });

                $purchase_order_services = $purchase_order_services->map(function ($item) {
                    $item->link = route('admin.purchase-order-service.show', $item->id);
                    $item->menu = 'purchase order service';
                    return $item;
                });

                $purchase_orders = $purchase_orders->map(function ($item) {
                    $item->link = route('admin.purchase-order.show', $item->id);
                    $item->menu = 'purchase order trading';
                    return $item;
                });

                $purchase_transports = $purchase_transports->map(function ($item) {
                    $item->link = route('admin.purchase-order-transport.show', $item->id);
                    $item->menu = 'purchase order transport';
                    return $item;
                });

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

                $sale_order_generals = $sale_order_generals->map(function ($item) {
                    $item->link = route('admin.sales-order-general.show', $item->id);
                    $item->menu = 'sale order general';
                    return $item;
                });

                $purhase_requests = $purhase_requests->map(function ($item) {
                    $item->link = route('admin.purchase-request.show', $item->id);
                    $item->menu = 'purchase request';
                    return $item;
                });

                $purchase_returns = $purchase_returns->map(function ($item) {
                    $item->link = route('admin.purchase-return.show', $item->id);
                    $item->menu = 'retur pembelian';
                    return $item;
                });
                $fund_submissions = $fund_submissions->map(function ($item) {
                    $item->link = route('admin.fund-submission.show', $item->id);
                    $item->menu = 'pengajuan dana';
                    return $item;
                });

                $account_payables = $account_payables->map(function ($item) {
                    $item->link = route('admin.account-payable.show', $item->id);
                    $item->menu = 'pelunasan hutang';
                    return $item;
                });

                $histories = $purhase_requests->unique('id')
                    ->merge($sale_order_generals->unique('id'))
                    ->merge($purchase_order_generals->unique('id'))
                    ->merge($purchase_order_services->unique('id'))
                    ->merge($purchase_orders->unique('id'))
                    ->merge($purchase_transports->unique('id'))
                    ->merge($item_receiving_reports->unique('id'))
                    ->merge($supplier_invoices->unique('id'))
                    ->merge($fund_submissions->unique('id'))
                    ->merge($account_payables->unique('id'))
                    ->merge($purchase_returns->unique('id'))
                    ->merge($supplier_invoice_generals->unique('id'))
                    ->sortBy('date')
                    ->values()
                    ->all();
            } else if ($fund_submission->item == "dp") {
                $purchases = DB::table('purchases')
                    ->whereIn('id', [$fund_submission->purchase_id])
                    ->get();

                $purchase_order_generals = DB::table('purchase_order_general_details')
                    ->join('purchase_order_generals', 'purchase_order_generals.id', '=', 'purchase_order_general_details.purchase_order_general_id')
                    ->whereNull('purchase_order_generals.deleted_at')
                    ->whereIn('purchase_order_general_id', $purchases
                        ->where('tipe', 'general')
                        ->pluck('model_id')->toArray())
                    ->select(
                        'purchase_order_generals.id',
                        'purchase_order_generals.code',
                        'purchase_order_generals.date',
                        'purchase_order_generals.status',
                        'purchase_order_general_details.purchase_request_id',
                        'purchase_order_general_details.sales_order_general_id',

                    )
                    ->get();

                $purchase_order_services = DB::table('purchase_order_service_details')
                    ->join('purchase_order_services', 'purchase_order_services.id', '=', 'purchase_order_service_details.purchase_order_service_id')
                    ->whereNull('purchase_order_services.deleted_at')
                    ->whereIn('purchase_order_services.id', $purchases
                        ->where('tipe', 'jasa')
                        ->pluck('model_id')->toArray())
                    ->select(
                        'purchase_order_services.id',
                        'purchase_order_services.code',
                        'purchase_order_services.date',
                        'purchase_order_services.status',
                        'purchase_order_service_details.purchase_request_id',
                    )
                    ->get();

                $purchase_orders = DB::table('purchase_orders')
                    ->whereIn('id', $purchases
                        ->where('tipe', 'trading')
                        ->pluck('model_id')->toArray())
                    ->select(
                        'purchase_orders.id',
                        'purchase_orders.nomor_po as code',
                        'purchase_orders.tanggal as date',
                        'purchase_orders.status',
                    )
                    ->get();

                $purchase_transports = DB::table('purchase_transports')
                    ->whereIn('id', $purchases
                        ->where('tipe', 'transport')
                        ->pluck('model_id')->toArray())
                    ->select(
                        'purchase_transports.id',
                        'purchase_transports.kode as code',
                        'purchase_transports.target_delivery as date',
                        'purchase_transports.status',
                    )
                    ->get();

                $purchase_request_id = $purchase_order_generals->pluck('purchase_request_id')->toArray();
                $purchase_request_id = array_merge($purchase_request_id, $purchase_order_services->pluck('purchase_request_id')->toArray());

                $sales_order_general_id = $purchase_order_generals->pluck('sales_order_general_id')->toArray();

                $purhase_requests = DB::table('purchase_requests')
                    ->whereIn('id', $purchase_request_id)
                    ->whereNull('deleted_at')
                    ->whereIn('status', ['approve', 'done', 'partial'])
                    ->select(
                        'id',
                        'kode as code',
                        'tanggal as date'
                    )
                    ->get();

                $sale_order_generals = DB::table('sale_order_generals')
                    ->whereIn('id', $sales_order_general_id)
                    ->whereNull('deleted_at')
                    ->whereNotIn('status', ['reject', 'void'])
                    ->select(
                        'id',
                        'kode as code',
                        'tanggal as date'
                    )
                    ->get();

                $cash_advance_payments = DB::table('cash_advance_payments')
                    ->whereIn('fund_submission_id', [$fund_submission->id])
                    ->whereNotIn('status', ['reject', 'void'])
                    ->whereNull('deleted_at')
                    ->select('id', 'code', 'date')
                    ->get();

                $cash_advanced_return_details = DB::table('cash_advanced_return_details')
                    ->join('cash_advanced_returns', 'cash_advanced_returns.id', '=', 'cash_advanced_return_details.cash_advanced_return_id')
                    ->where('cash_advanced_return_details.reference_model', CashAdvancePayment::class)
                    ->whereIn('cash_advanced_return_details.reference_id', $cash_advance_payments->pluck('id')->toArray())
                    ->whereNotIn('cash_advanced_returns.status', ['reject', 'void'])
                    ->whereNull('cash_advanced_returns.deleted_at')
                    ->select('cash_advanced_returns.id', 'cash_advanced_returns.code', 'cash_advanced_returns.date')
                    ->get();

                $cash_advance_payments = $cash_advance_payments->map(function ($item) {
                    $item->link = route('admin.cash-advance-payment.show', $item->id);
                    $item->menu = 'pembayaran uang muka';
                    return $item;
                });

                $cash_advanced_return_details = $cash_advanced_return_details->map(function ($item) {
                    $item->link = route('admin.cash-advance-return-vendor.show', $item->id);
                    $item->menu = 'pengembalian uang muka';
                    return $item;
                });

                $purchase_order_generals = $purchase_order_generals->map(function ($item) {
                    $item->link = route('admin.purchase-order-general.show', $item->id);
                    $item->menu = 'purchase order general';
                    return $item;
                });

                $purchase_order_services = $purchase_order_services->map(function ($item) {
                    $item->link = route('admin.purchase-order-service.show', $item->id);
                    $item->menu = 'purchase order service';
                    return $item;
                });

                $purchase_orders = $purchase_orders->map(function ($item) {
                    $item->link = route('admin.purchase-order.show', $item->id);
                    $item->menu = 'purchase order trading';
                    return $item;
                });

                $purchase_transports = $purchase_transports->map(function ($item) {
                    $item->link = route('admin.purchase-order-transport.show', $item->id);
                    $item->menu = 'purchase order transport';
                    return $item;
                });

                $sale_order_generals = $sale_order_generals->map(function ($item) {
                    $item->link = route('admin.sales-order-general.show', $item->id);
                    $item->menu = 'sale order general';
                    return $item;
                });

                $purhase_requests = $purhase_requests->map(function ($item) {
                    $item->link = route('admin.purchase-request.show', $item->id);
                    $item->menu = 'purchase request';
                    return $item;
                });

                $histories = $purhase_requests->unique('id')
                    ->merge($sale_order_generals->unique('id'))
                    ->merge($purchase_order_generals->unique('id'))
                    ->merge($purchase_order_services->unique('id'))
                    ->merge($purchase_orders->unique('id'))
                    ->merge($purchase_transports->unique('id'))
                    ->merge($cash_advance_payments->unique('id'))
                    ->merge($cash_advanced_return_details->unique('id'))
                    ->sortBy('date')
                    ->values()
                    ->all();
            } else if ($fund_submission->item == "general") {
                $outgoing_payments = DB::table('outgoing_payments')
                    ->whereIn('fund_submission_id', [$fund_submission->id])
                    ->whereNull('deleted_at')
                    ->whereNotIn('status', ['reject', 'void'])
                    ->select(
                        'id',
                        'code',
                        'date'
                    )
                    ->get();

                $outgoing_payments = $outgoing_payments->map(function ($item) {
                    $item->link = route('admin.outgoing-payment.show', $item->id);
                    $item->menu = 'kas keluar';
                    return $item;
                });

                $histories = $outgoing_payments->unique('id')
                    ->sortBy('date')
                    ->values()
                    ->all();
            }

            return response()->json([
                'success' => true,
                'data' => $histories
            ]);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function cash_advance_detail(Request $request, $id)
    {
        $cash_advance = CashAdvanceReceive::with('customer')
            ->findOrFail($id);

        $cash_advance_detail = CashAdvanceReceiveDetail::where('cash_advance_receive_id', $cash_advance->id)
            ->where('type', 'cash_advance')
            ->first();
        $cash_advance->cash_advance_coa = $cash_advance_detail->coa;
        $cash_advance->outstanding_amount = $cash_advance_detail->credit - $cash_advance->returned_amount;

        return response()->json($cash_advance);
    }
}
