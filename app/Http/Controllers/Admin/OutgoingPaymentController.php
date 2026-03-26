<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\FundSubmission;
use App\Models\OutgoingPayment as model;
use App\Models\OutgoingPayment;
use App\Models\OutgoingPaymentDetail as modelDetail;
use App\Models\OutgoingPaymentDetail;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class OutgoingPaymentController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'outgoing-payment';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index', 'show', 'data']]);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }

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
            $data = model::whereDate('outgoing_payments.date', '>=', Carbon::parse($request->from_date))
                ->whereDate('outgoing_payments.date', '<=', Carbon::parse($request->to_date))
                ->leftJoin('bank_code_mutations', function ($q) {
                    $q->on('outgoing_payments.id', 'bank_code_mutations.ref_id')
                        ->where('bank_code_mutations.ref_model', model::class);
                })
                ->groupBy('outgoing_payments.id')
                ->select('outgoing_payments.*');

            if (!get_current_branch()->is_primary) {
                $data->where('outgoing_payments.branch_id', get_current_branch_id());
            }
            if ($request->branch_id) {
                $data->where('outgoing_payments.branch_id', $request->branch_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->bank_code_mutation ?? $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]) . '<br>' .
                    view("components.datatable.export-button", [
                        'route' => route("outgoing-payment.export", ['id' => encryptId($row->id)]),
                        'onclick' => "",
                    ]))
                ->editColumn('status', function ($row) {
                    $status = incoming_payment_status()[$row->status];
                    $badge = '<div class="badge badge-lg badge-' . $status['color'] . '">
                                    ' . $status['text'] . '
                                </div>';

                    return $badge;
                })
                ->editColumn('total', function ($row) {
                    return $row->currency->simbol . " " . formatNumber($row->total);
                })
                ->editColumn('date', function ($row) {
                    return localDate($row->date);
                })
                ->addColumn('action', function ($row) {
                    $btn = $row->check_available_date;

                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => in_array($row->status, ['pending', 'revert']) && $btn,
                            ],
                            'delete' => [
                                'display' => in_array($row->status, ['pending', 'revert']) && $btn,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action', 'status', 'export', 'code'])
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
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view('admin.' . $this->view_folder . '.create');
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
            $fund_submission = FundSubmission::find($request->fund_submission_id);
            $branch = Branch::find($request->branch_id);


            $bank_code = "BK";
            $bank_code .= \App\Models\BankInternal::where('coa_id', $request->coa_id)->first()?->code;

            $branch_id = $request->branch_id ?? get_current_branch_id();
            $branch = \App\Models\Branch::find($branch_id);

            $outgoing_payment = new model();
            $outgoing_payment->code = generate_code(OutgoingPayment::class, 'code', 'date', $bank_code, branch_sort: $branch->sort ?? null, date: $request->date);
            $outgoing_payment->branch_id = $request->branch_id;
            $outgoing_payment->currency_id = $request->currency_id;
            $outgoing_payment->project_id = $request->project_id;
            $outgoing_payment->to_name = $request->to_name;
            $outgoing_payment->date = Carbon::parse($request->date);
            $outgoing_payment->exchange_rate = thousand_to_float($request->exchange_rate);
            $outgoing_payment->reference = $request->reference;
            $outgoing_payment->coa_id = $request->coa_id;
            $outgoing_payment->change_bank_reason = $request->change_bank_reason;
            $outgoing_payment->fund_submission_id = $request->fund_submission_id;
            $outgoing_payment->invoice_return_id = $request->invoice_return_id;
            $outgoing_payment->invoice_return_id = $request->invoice_return_id;
            $outgoing_payment->cash_advance_receive_id = $request->cash_advance_receive_id;

            if (!$outgoing_payment->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }

            $outgoing_payment->save();

            foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                $outgoing_payment_detail = new OutgoingPaymentDetail();
                $outgoing_payment_detail->outgoing_payment_id = $outgoing_payment->id;
                $outgoing_payment_detail->coa_id = $coa_detail_id;
                $outgoing_payment_detail->note = $request->note[$key] ?? '-';
                $outgoing_payment_detail->type = $request->type[$key] ?? '-';
                $outgoing_payment_detail->debit = thousand_to_float($request->debit[$key] ?? 0);
                if ($request->is_return[$key] ?? '' == "true") {
                    $outgoing_payment_detail->invoice_return_id = $outgoing_payment->invoice_return_id;
                }
                $outgoing_payment_detail->save();
            }

            $outgoing_payment->total = $outgoing_payment->outgoing_payment_details->sum('debit');
            $outgoing_payment->save();

            try {
                $code = generate_bank_code(
                    ref_model: OutgoingPayment::class,
                    ref_id: $outgoing_payment->id,
                    coa_id: $request->coa_id,
                    type: 'out',
                    date: $request->date,
                    is_save: true,
                    code: $request->sequence_code,
                );

                if (!$code) {
                    DB::rollBack();
                    $outgoing_payment->delete();
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih'));
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih'));
            }

            $title = Auth::user()->name . " mengajukan Kas Keluar " . $code;

            if ($outgoing_payment->fund_submission && $request->coa_id == $fund_submission->coa_id) {
                $outgoing_payment->status = "approve";
                $outgoing_payment->save();

                $title = Auth::user()->name . " menambahkan Kas Keluar " . $code;
                $authorization = new \App\Http\Helpers\AuthorizationHelper();
                $authorization->init(
                    branch_id: $outgoing_payment->branch_id,
                    user_id: auth()->user()->id,
                    model: model::class,
                    model_id: $outgoing_payment->id,
                    amount: $outgoing_payment->total ?? 0,
                    title: "Kas Keluar",
                    subtitle: $title,
                    link: route('admin.outgoing-payment.show', $outgoing_payment),
                    update_status_link: route('admin.outgoing-payment.update-status', ['id' => $outgoing_payment->id]),
                    division_id: auth()->user()->division_id ?? null,
                    auto_approve: true,
                );
            } else {
                if ($fund_submission) {
                    if ($request->coa_id != $fund_submission->coa_id ?? null) {
                        $title = Auth::user()->name . " mengajukan perubahan kas/bank - Kas Keluar " . $code;
                    }
                }
                $authorization = new \App\Http\Helpers\AuthorizationHelper();
                $authorization->init(
                    branch_id: $outgoing_payment->branch_id,
                    user_id: auth()->user()->id,
                    model: model::class,
                    model_id: $outgoing_payment->id,
                    amount: $outgoing_payment->total ?? 0,
                    title: "Kas Keluar",
                    subtitle: $title,
                    link: route('admin.outgoing-payment.show', $outgoing_payment),
                    update_status_link: route('admin.outgoing-payment.update-status', ['id' => $outgoing_payment->id]),
                    division_id: auth()->user()->division_id ?? null
                );
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
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

        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == 'approve' && !$model->fund_submission;
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve' && !$model->fund_submission;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->check_available_date && $model->status == 'approve' && !$model->fund_submission;
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve';
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.{$this->view_folder}.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
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
            return abort(403);
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
            $fund_submission = FundSubmission::find($request->fund_submission_id);

            $outgoing_payment = model::find($id);
            $old_fund_submission_id = $outgoing_payment->fund_submission_id;
            $outgoing_payment->branch_id = $request->branch_id;
            $outgoing_payment->currency_id = $request->currency_id;
            $outgoing_payment->project_id = $request->project_id;
            $outgoing_payment->to_name = $request->to_name;
            $outgoing_payment->coa_id = $request->coa_id;
            $outgoing_payment->date = Carbon::parse($request->date);
            $outgoing_payment->exchange_rate = thousand_to_float($request->exchange_rate);
            $outgoing_payment->reference = $request->reference;
            $outgoing_payment->fund_submission_id = $request->fund_submission_id;
            $outgoing_payment->invoice_return_id = $request->invoice_return_id;
            $outgoing_payment->cash_advance_receive_id = $request->cash_advance_receive_id;

            if (!$outgoing_payment->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }

            if ($old_fund_submission_id != $request->fund_submission_id) {
                OutgoingPaymentDetail::where('outgoing_payment_id', $outgoing_payment->id)->delete();
            }


            modelDetail::whereNotIn('id', $request->outgoing_payment_detail_id)
                ->where('outgoing_payment_id', $outgoing_payment->id)
                ->delete();

            foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                $outgoing_payment_detail = modelDetail::find($request->outgoing_payment_detail_id[$key]);
                if (!$outgoing_payment_detail) {
                    $outgoing_payment_detail = new modelDetail();
                }
                $outgoing_payment_detail->outgoing_payment_id = $outgoing_payment->id;
                $outgoing_payment_detail->coa_id = $coa_detail_id;
                $outgoing_payment_detail->note = $request->note[$key] ?? '-';
                $outgoing_payment_detail->type = $request->type[$key] ?? '-';
                $outgoing_payment_detail->debit = thousand_to_float($request->debit[$key]);
                if ($request->is_return[$key] ?? '' == "true") {
                    $outgoing_payment_detail->invoice_return_id = $outgoing_payment->invoice_return_id;
                }
                $outgoing_payment_detail->save();
            }

            $outgoing_payment->total = $outgoing_payment->outgoing_payment_details->sum('debit');
            $outgoing_payment->save();

            if (!$outgoing_payment->fund_submission_id) {
                $authorization = new \App\Http\Helpers\AuthorizationHelper();
                $authorization->init(
                    branch_id: $outgoing_payment->branch_id,
                    user_id: auth()->user()->id,
                    model: model::class,
                    model_id: $outgoing_payment->id,
                    amount: $outgoing_payment->total ?? 0,
                    title: "Kas Keluar",
                    subtitle: Auth::user()->name . " mengajukan Kas Keluar " . ($outgoing_payment->bank_code_mutation ?? $outgoing_payment->code),
                    link: route('admin.outgoing-payment.show', $outgoing_payment),
                    update_status_link: route('admin.outgoing-payment.update-status', ['id' => $outgoing_payment->id]),
                    division_id: auth()->user()->division_id ?? null
                );
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
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
            OutgoingPaymentDetail::where('outgoing_payment_id', $id)
                ->where('outgoing_payment_id', $id)
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

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request) {}

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
        if (!$request->fund_submission_id) {
            return response()->json(['html' => '']);
        }

        $model = FundSubmission::find($request->fund_submission_id);

        if ($model->send_payment) {
            $model->send_payment->due_status_by_date = $model->send_payment->getDueStatus($request->date);
        }

        $data = [
            'html' => view('admin.outgoing-payment._readonly_form', ['model' => $model])->render(),
            'data' => $model,
        ];

        return response()->json($data);
    }

    public function general_form()
    {
        return view('admin.outgoing-payment._general_form')->render();
    }

    public function export($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $vendor = $model->to_model == Vendor::class ? Vendor::find($model->to_id) : null;

        $fileName = 'KAS KELUAR - ' . ucfirst($model->bank_code_mutation) . '.pdf';

        $qr_url = route('outgoing-payment.export', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $pdf = Pdf::loadview("admin/.$this->view_folder./export", compact('model', 'qr', 'vendor'))
            ->setPaper($request->paper ?? 'a4', $request->landscape ?? 'portrait');

        return $pdf->stream($fileName);
    }
}
