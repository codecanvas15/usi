<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\Employee;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\FundSubmission;
use App\Models\CashAdvancePayment as model;
use App\Models\CashAdvancePayment;
use App\Models\CashAdvancePaymentDetail;
use App\Models\ItemReceivingReportTax;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class CashAdvancePaymentController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'cash-advance-payment';

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

    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            $data = model::leftJoin('projects', 'projects.id', 'cash_advance_payments.project_id')
                ->join('cash_advance_payment_details', function ($q) {
                    $q->on('cash_advance_payments.id', '=', 'cash_advance_payment_details.cash_advance_payment_id')
                        ->where('cash_advance_payment_details.type', 'cash_bank');
                })
                ->leftJoin('employees', function ($e) {
                    $e->on('employees.id', 'cash_advance_payments.to_id')
                        ->where('cash_advance_payments.to_model', 'App\Models\Employee');
                })
                ->leftJoin('vendors', function ($e) {
                    $e->on('vendors.id', 'cash_advance_payments.to_id')
                        ->where('cash_advance_payments.to_model', 'App\Models\Vendor');
                })
                ->leftJoin('bank_code_mutations', function ($q) {
                    $q->on('cash_advance_payments.id', 'bank_code_mutations.ref_id')
                        ->where('bank_code_mutations.ref_model', model::class);
                })
                ->whereDate('cash_advance_payments.date', '>=', Carbon::parse($request->from_date))
                ->whereDate('cash_advance_payments.date', '<=', Carbon::parse($request->to_date))
                ->when(!get_current_branch()->is_primary, function ($query) {
                    $query->where('cash_advance_payments.branch_id', get_current_branch_id());
                })
                ->when($request->branch_id, function ($q) use ($request) {
                    $q->where('cash_advance_payments.branch_id', $request->branch_id);
                })
                ->groupBy('cash_advance_payments.id')
                ->select('cash_advance_payments.*', 'projects.name as project_name', 'cash_advance_payment_details.credit');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('project_name', function ($row) {
                    return $row->project_name ?? '';
                })
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->bank_code_mutation ?? $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('date', function ($row) {
                    return localDate($row->date);
                })
                ->editColumn('to_model', function ($row) {
                    return "Vendor";
                    if ($row->to_model == Employee::class) {
                        return "Karyawan";
                    }
                })
                ->editColumn('status', function ($row) {
                    $status = incoming_payment_status()[$row->status];
                    $badge = '<div class="badge badge-lg badge-' . $status['color'] . '">
                                    ' . $status['text'] . '
                                </div>';

                    return $badge;
                })
                ->editColumn('credit', function ($row) {
                    return formatNumber($row->credit);
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => $row->status != 'approve' && $row->status != 'reject' && $row->status != 'void',
                            ],
                            'delete' => [
                                'display' => $row->status != 'approve' && $row->status != 'reject' && $row->status != 'void',
                            ],
                        ],
                    ]) . view("components.datatable.export-button", [
                        'route' => route("cash-advance-payment.export.id", ['id' => encryptId($row->id)]),
                        'onclick' => "",
                    ]);
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $branch = Auth::user()->branch;
        $code = generate_code(model::class, 'code', 'date', 'UMK', branch_sort: $branch->branch_sort);
        return view('admin.' . $this->view_folder . '.create', compact('code'));
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
        try {
            $fund_submission = FundSubmission::findOrFail($request->fund_submission_id);
            $branch = Branch::find($fund_submission->branch_id);

            // Condition to check date more then fund submission
            if (Carbon::parse($fund_submission->date)->gt(Carbon::parse($request->date))) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal pembayaran tidak boleh kurang dari tanggal pengajuan dana!'));
            }

            $purchase_down_payment = $fund_submission->purchase_down_payment;
            $tax_number = $request->tax_number ?? $fund_submission->tax_number ?? $purchase_down_payment->tax_number ?? '';

            $cash_advance_payment = new model();
            $cash_advance_payment->code = generate_code(model::class, 'code', 'date', 'BBK', date: $request->date, branch_sort: $fund_submission->branch->sort);
            $cash_advance_payment->branch_id = $fund_submission->branch_id;
            $cash_advance_payment->currency_id = $fund_submission->currency_id;
            $cash_advance_payment->project_id = $fund_submission->project_id;
            $cash_advance_payment->purchase_id = $fund_submission->purchase_id;
            $cash_advance_payment->fund_submission_id = $request->fund_submission_id;
            $cash_advance_payment->to_model = $fund_submission->to_model;
            $cash_advance_payment->to_id = $fund_submission->to_id;
            $cash_advance_payment->to_name = $fund_submission->to_name;
            $cash_advance_payment->date = Carbon::parse($request->date);
            $cash_advance_payment->reference = $fund_submission->reference;
            $cash_advance_payment->currency_id = $fund_submission->currency_id;
            $cash_advance_payment->exchange_rate = thousand_to_float($request->exchange_rate ?? $fund_submission->exchange_rate);
            $cash_advance_payment->tax_id = $fund_submission->tax_id;
            $cash_advance_payment->tax_number = $tax_number;
            $cash_advance_payment->keterangan = "";
            $cash_advance_payment->change_reason_bank = $request->change_reason_bank;

            if (!$cash_advance_payment->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }

            $file_path = '';
            if ($request->file('tax_attachment')) {
                $file_path =  $this->upload_file($request->file('tax_attachment'), 'purchase-down-payment');
            } else {
                $file_path = $fund_submission->tax_attachment ?? $purchase_down_payment->tax_attachment ?? '';
            }

            $cash_advance_payment->tax_attachment = $file_path;
            $cash_advance_payment->save();

            $fund_submission->tax_attachment = $file_path;
            $fund_submission->tax_number = $tax_number;
            $fund_submission->save();

            if ($purchase_down_payment) {
                $purchase_down_payment->tax_number = $tax_number;
                $purchase_down_payment->tax_attachment = $file_path;
                $purchase_down_payment->save();
            }

            $coa_bank = null;
            foreach ($request->type ?? [] as $key => $type) {
                $cash_advance_payment_detail = new CashAdvancePaymentDetail();
                $cash_advance_payment_detail->cash_advance_payment_id = $cash_advance_payment->id;
                $cash_advance_payment_detail->coa_id = $request->coa_detail_id[$key];
                $cash_advance_payment_detail->type = $type;
                $cash_advance_payment_detail->note = $request->note[$key];
                if ($request->position[$key] == 'debit') {
                    $cash_advance_payment_detail->debit = thousand_to_float($request->amount[$key] ?? 0);
                    $cash_advance_payment_detail->credit = 0;
                } else {
                    $cash_advance_payment_detail->debit = 0;
                    $cash_advance_payment_detail->credit = thousand_to_float($request->amount[$key] ?? 0);
                }
                $cash_advance_payment_detail->save();

                if ($type == 'cash_bank') {
                    $coa_bank = $request->coa_detail_id[$key];
                }
            }

            try {
                $code = generate_bank_code(
                    ref_model: CashAdvancePayment::class,
                    ref_id: $cash_advance_payment->id,
                    coa_id: $cash_advance_payment->getCashAdvanceCashBankAttribute()->coa_id,
                    type: 'out',
                    date: $cash_advance_payment->date,
                    is_save: true,
                    code: $request->sequence_code,
                );

                if (!$code) {
                    DB::rollBack();
                    CashAdvancePaymentDetail::where('cash_advance_payment_id', $cash_advance_payment->id)->delete();
                    $cash_advance_payment->delete();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih'));
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih' . $th->getMessage()));
            }

            $fund_submission_coa = $fund_submission
                ->fund_submission_cash_advances()
                ->where('type', 'cash_bank')
                ->first()
                ->coa_id;

            if ($coa_bank == $fund_submission_coa) {
                $cash_advance_payment->status = "approve";
                $cash_advance_payment->save();
                $authorization = new \App\Http\Helpers\AuthorizationHelper();
                $authorization->init(
                    branch_id: $cash_advance_payment->branch_id,
                    user_id: auth()->user()->id,
                    model: model::class,
                    model_id: $cash_advance_payment->id,
                    amount: $cash_advance_payment->total ?? 0,
                    title: "Pembayaran Uang Muka",
                    subtitle: Auth::user()->name . " menambahkan pembayaran uang muka " . $code,
                    link: route('admin.cash-advance-payment.show', $cash_advance_payment),
                    update_status_link: route('admin.cash-advance-payment.update-status', ['id' => $cash_advance_payment->id]),
                    division_id: auth()->user()->division_id ?? null,
                    auto_approve: true
                );
            } else {
                $authorization = new \App\Http\Helpers\AuthorizationHelper();
                $authorization->init(
                    branch_id: $cash_advance_payment->branch_id,
                    user_id: auth()->user()->id,
                    model: model::class,
                    model_id: $cash_advance_payment->id,
                    amount: $cash_advance_payment->total ?? 0,
                    title: "Pembayaran Uang Muka",
                    subtitle: Auth::user()->name . " mengajukan perubahan kas bank - pembayaran uang muka " . $code,
                    link: route('admin.cash-advance-payment.show', $cash_advance_payment),
                    update_status_link: route('admin.cash-advance-payment.update-status', ['id' => $cash_advance_payment->id]),
                    division_id: auth()->user()->division_id ?? null
                );
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->route("admin.outgoing-payment.index", ['tab' => 'deposite'])->with($this->ResponseMessageCRUD());
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $int
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = model::findOrFail($id);
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = false;
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve';
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = false;
        $authorization_logs['can_void_request'] = $model->status == "approve" && $model->check_available_date;
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.{$this->view_folder}.show", compact('model', 'status_logs', 'activity_logs', 'auth_revert_void_button', 'authorization_log_view'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = model::findOrFail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        $readonly = $model->fund_submission != null ? 'readonly' : '';

        return view("admin.{$this->view_folder}.edit", compact('model', 'readonly'));
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
        try {
            $cash_advance_payment = model::find($id);
            if (!$cash_advance_payment->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }
            $old_fund_submission_id = $cash_advance_payment->fund_submission_id;
            $cash_advance_payment->date = Carbon::parse($request->date);

            if ($old_fund_submission_id != $request->fund_submission_id) {
                $fund_submission = FundSubmission::findOrFail($request->fund_submission_id);
                $cash_advance_payment->branch_id = $fund_submission->branch_id;
                $cash_advance_payment->currency_id = $fund_submission->currency_id;
                $cash_advance_payment->project_id = $fund_submission->project_id;
                $cash_advance_payment->purchase_id = $fund_submission->purchase_id;
                $cash_advance_payment->fund_submission_id = $request->fund_submission_id;
                $cash_advance_payment->to_model = $fund_submission->to_model;
                $cash_advance_payment->to_id = $fund_submission->to_id;
                $cash_advance_payment->to_name = $fund_submission->to_name;
                $cash_advance_payment->reference = $fund_submission->reference;
                $cash_advance_payment->currency_id = $fund_submission->currency_id;
                $cash_advance_payment->exchange_rate = thousand_to_float($request->exchange_rate ?? $fund_submission->exchange_rate);
                $cash_advance_payment->keterangan = "";
                $cash_advance_payment->status = "pending";
                $cash_advance_payment->save();

                CashAdvancePaymentDetail::where('cash_advance_payment_id', $cash_advance_payment->id)->delete();

                foreach ($fund_submission->fund_submission_cash_advances ?? [] as $key => $fund_submission_cash_advance) {
                    $cash_advance_payment_detail = new CashAdvancePaymentDetail();
                    $cash_advance_payment_detail->cash_advance_payment_id = $cash_advance_payment->id;
                    $cash_advance_payment_detail->coa_id = $fund_submission_cash_advance->coa_id;
                    $cash_advance_payment_detail->type = $fund_submission_cash_advance->type;
                    $cash_advance_payment_detail->note = $fund_submission_cash_advance->note;
                    $cash_advance_payment_detail->debit = $fund_submission_cash_advance->debit ?? 0;
                    $cash_advance_payment_detail->credit = $fund_submission_cash_advance->credit ?? 0;
                    $cash_advance_payment_detail->save();
                }
            }
            $cash_advance_payment->save();


            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        return redirect()->route("admin.outgoing-payment.index", ['tab' => 'deposite'])->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $model = model::findOrFail($id);
        DB::beginTransaction();
        try {
            CashAdvancePaymentDetail::where('cash_advance_payment_id', $id)
                ->where('cash_advance_payment_id', $id)
                ->delete();
            $model->delete();

            Authorization::where('model', model::class)
                ->where('model_id', $id)
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

        return redirect()->route("admin.outgoing-payment.index", ['tab' => 'deposite'])->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        $model = DB::table('cash_advance_payments')
            ->where('cash_advance_payments.status',  'approve')
            ->join('cash_advance_payment_details', function ($q) {
                $q->on('cash_advance_payments.id', '=', 'cash_advance_payment_details.cash_advance_payment_id')
                    ->where('cash_advance_payment_details.type', 'cash_advance');
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) {
                    $query->where('cash_advance_payments.to_name', 'like', '%' . request('search') . '%')
                        ->orWhere('cash_advance_payments.code', 'like', '%' . request('search') . '%');
                });
            })
            ->when($request->branch_id, function ($q) use ($request) {
                $q->where('cash_advance_payments.branch_id', $request->branch_id);
            })
            ->when($request->currency_id, function ($q) use ($request) {
                $q->where('cash_advance_payments.currency_id', $request->currency_id);
            })
            ->whereColumn('cash_advance_payment_details.debit', '>', 'cash_advance_payments.returned_amount')
            ->select(
                'cash_advance_payments.id',
                'cash_advance_payments.code',
                'cash_advance_payments.to_name',
            )
            ->paginate(10);

        return $this->ResponseJson($model);
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
        validate_branch($model->branch_id);
        $this->create_activity_status_log(model::class, $id, $request->message ?? 'message not available', $model->status, $request->status);


        // * saving and make response
        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->status = $request->status;
                $model->save();
            } else {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    public function fund_submission(Request $request)
    {
        $model = FundSubmission::find($request->fund_submission_id);

        if ($model->send_payment) {
            $model->send_payment->due_status_by_date = $model->send_payment->getDueStatus($request->date);
        }

        $data = [
            'html' => view('admin.cash-advance-payment._readonly_form', ['model' => $model])->render(),
            'data' => $model,
        ];

        return response()->json($data);
    }

    public function export($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $vendor = $model->to_model == Vendor::class ? Vendor::find($model->to_id) : null;
        $fileName = 'KAS KELUAR - ' . ucfirst($model->bank_code_mutation) . '.pdf';

        $qr_url = route('cash-advance-payment.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $pdf = Pdf::loadview("admin/.$this->view_folder./export", compact('model', 'qr', 'vendor'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream($fileName);
    }

    public function export_proforma($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $fileName = 'INVOICE - ' . ucfirst($model->bank_code_mutation) . '.pdf';

        $qr_url = route('cash-advance-payment.export-proforma.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $pdf = Pdf::loadview("admin/.$this->view_folder./export_proforma", compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream($fileName);
    }

    public function update_tax($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $cash_advance_payment = CashAdvancePayment::findOrFail($id);
            $fund_submission = $cash_advance_payment->fund_submission;
            $purchase_down_payment = $fund_submission->purchase_down_payment;

            $tax_number = $request->tax_number ?? $fund_submission->tax_number ??  $purchase_down_payment->tax_number  ?? '';
            $file_path = '';
            if ($request->file('tax_attachment')) {
                $file_path =  $this->upload_file($request->file('tax_attachment'), 'purchase-down-payment');
            } else {
                $file_path = $fund_submission->tax_attachment ?? $purchase_down_payment->tax_attachment ?? '';
            }

            if ($fund_submission) {
                $fund_submission->tax_number = $tax_number;
                $fund_submission->tax_attachment = $file_path;
                $fund_submission->save();
            }

            if ($cash_advance_payment) {
                $cash_advance_payment->tax_number = $tax_number;
                $cash_advance_payment->tax_attachment = $file_path;
                $cash_advance_payment->save();
            }

            if ($purchase_down_payment) {
                $purchase_down_payment->tax_number = $tax_number;
                $purchase_down_payment->tax_attachment = $file_path;
                $purchase_down_payment->save();
            }

            DB::commit();
            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'edit', null));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }
    }

    public function detail(Request $request)
    {
        try {
            $model = CashAdvancePayment::whereIn('id', $request->id ?? [])
                ->get();

            return $this->ResponseJsonData($model);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function generate_tax()
    {
        DB::beginTransaction();
        try {
            $models = CashAdvancePayment::where('status', 'approve')->get();

            foreach ($models as $key => $model) {
                if ($model->tax && ($model->tax->type ?? '') == 'ppn') {
                    $cash_advance_detail_tax = $model->cash_advance_payment_details()->where('type', 'tax')->first();
                    $dpp = $model->cash_advance_payment_details()->where('type', 'cash_advance')->first();
                    $supplier_taxes = ItemReceivingReportTax::where('reference_model', get_class($cash_advance_detail_tax))
                        ->where('reference_id', $cash_advance_detail_tax->id)
                        ->where('reference_parent_model', CashAdvancePayment::class)
                        ->where('reference_parent_id', $model->id)
                        ->where('tax_id', $model->tax_id)
                        ->get();

                    if ($supplier_taxes->count() == 0) {
                        $supplier_tax = new ItemReceivingReportTax();
                    } else {
                        $supplier_tax = $supplier_taxes->first();
                    }
                    $supplier_tax->reference_model = get_class($cash_advance_detail_tax);
                    $supplier_tax->reference_id = $cash_advance_detail_tax->id;
                    $supplier_tax->reference_parent_model = CashAdvancePayment::class;
                    $supplier_tax->reference_parent_id = $model->id;
                    $supplier_tax->date = Carbon::parse($model->date);
                    $supplier_tax->vendor_id = $model->to_id;
                    $supplier_tax->tax_id = $model->tax_id;
                    $supplier_tax->dpp = ($dpp->debit * $model->exchange_rate);
                    $supplier_tax->value = $model->tax->value;
                    $supplier_tax->amount = ($dpp->debit * $model->exchange_rate) * $model->tax->value;
                    $supplier_tax->save();

                    $supplier_taxes->where('id', '!=', $supplier_tax->id)
                        ->each(function ($item) {
                            $item->delete();
                        });
                }
            }

            DB::commit();

            return response()->json('success');
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage());
        }
    }
}
