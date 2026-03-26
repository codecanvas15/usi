<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Authorization;
use App\Models\ReceivePayment as model;
use App\Models\IncomingPayment;
use App\Models\ReceivablesPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReceivePaymentController extends Controller
{
    use ActivityStatusLogHelper;


    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }


    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'receive-payment';

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
        if ($request->ajax()) {
            $data = model::join('currencies', 'currencies.id', 'receive_payments.currency_id')
                ->leftjoin('customers', 'customers.id', 'receive_payments.customer_id')
                ->select(
                    'receive_payments.*',
                    'currencies.kode as currency_kode',
                    'customers.nama as customer_nama'
                );

            if (!get_current_branch()->is_primary) {
                $data->where('receive_payments.branch_id', get_current_branch_id());
            }
            if ($request->branch_id) {
                $data->where('receive_payments.branch_id', $request->branch_id);
            }
            if ($request->from_date) {
                $data->whereDate('receive_payments.date', '>=', Carbon::parse($request->from_date));
            }
            if ($request->to_date) {
                $data->whereDate('receive_payments.date', '<=', Carbon::parse($request->to_date));
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('amount', function ($row) {
                    return floatDotFormat($row->amount);
                })
                ->editColumn('customer_nama', function ($row) {
                    return $row->customer->nama ?? $row->from_name;
                })
                ->addColumn('exchange_rate', function ($row) {
                    return floatDotFormat($row->exchange_rate);
                })
                ->addColumn('date', function ($row) {
                    return localDate($row->date);
                })
                ->addColumn('due_date', function ($row) {
                    return localDate($row->due_date);
                })
                ->addColumn('realization_date', function ($row) {
                    return $row->realization_date ? localDate($row->realization_date) : '';
                })
                ->editColumn('status', function ($row) {
                    $status = fund_submission_status()[$row->status];
                    $badge = '<div class="badge badge-lg badge-' . $status['color'] . '">
                                    ' . $status['text'] . '
                                </div>';

                    return $badge;
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
                                'display' => !in_array($row->status, ['approve', 'reject', 'cancel']),
                            ],
                            'delete' => [
                                'display' => !in_array($row->status, ['approve', 'reject', 'cancel']),
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        } else {
            return view('admin.' . $this->view_folder . '.index');
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

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'pay_from' => 'required|in:customer,other',
            'customer_id' => 'required_if:pay_from,customer|exists:customers,id',
            'from_name' => 'required_if:pay_from,other',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'cheque_no' => 'required|unique:receive_payments,cheque_no',
            'from_bank' => 'required',
            'realization_bank' => 'required',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'required',
            'amount' => 'required',
        ]);

        $errors = [];

        if (Carbon::parse($request->date) > Carbon::parse($request->due_date)) {
            $errors['due_date'] = ['Tanggal jatuh tempo tidak boleh kurang dari tanggal transaksi'];
        }

        if ($request->ajax()) {
            if ($errors) {
                return response()->json(
                    [
                        'errors' => $errors,
                    ],
                    422
                );
            }

            return response()->json('success');
        }

        $model = new model();
        DB::beginTransaction();

        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }

        $model->loadModel([
            'branch_id' => $request->branch_id,
            'pay_from' => $request->pay_from,
            'customer_id' => $request->customer_id,
            'from_name' => $request->from_name,
            'date' => Carbon::parse($request->date),
            'due_date' => Carbon::parse($request->due_date),
            'cheque_no' => $request->cheque_no,
            'from_bank' => $request->from_bank,
            'realization_bank' => $request->realization_bank,
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'amount' => thousand_to_float($request->amount),
        ]);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->amount ?? 0,
                title: "Giro Masuk",
                subtitle: Auth::user()->name . " mengajukan Giro Masuk " . $model->code,
                link: route('admin.receive-payment.show', $model),
                update_status_link: route('admin.receive-payment.update-status', ['id' => $model->id]),
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
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'create'));
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
        $model = model::with('currency')
            ->findOrFail($id);

        if ($request->ajax()) {
            $model->due_status_by_date = $model->getDueStatus($request->date);
            return $this->ResponseJsonData($model);
        }

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == 'approve' && count($model->receivables_payments) == 0 && count($model->incoming_payments) == 0;
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve' && count($model->receivables_payments) == 0 && count($model->incoming_payments) == 0;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->check_available_date && $model->status == 'approve' && count($model->receivables_payments) == 0 && count($model->incoming_payments) == 0;
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve' && count($model->receivables_payments) == 0 && count($model->incoming_payments) == 0;
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

        if (in_array($model->status, ['approve', 'reject', 'cancel'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Data tidak bisa di edit'));
        }

        if (!$model->check_available_date) {
            return abort(403);
        }

        return view("admin.{$this->view_folder}.edit", compact('model'));
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
        $model = model::find($id);

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'pay_from' => 'required|in:customer,other',
            'customer_id' => 'required_if:pay_from,customer|exists:customers,id',
            'from_name' => 'required_if:pay_from,other',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'cheque_no' => 'required|unique:receive_payments,cheque_no,' . $id,
            'from_bank' => 'required',
            'realization_bank' => 'required',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'required',
            'amount' => 'required',
        ]);

        $errors = [];

        if (Carbon::parse($request->date) > Carbon::parse($request->due_date)) {
            $errors['due_date'] = ['Tanggal jatuh tempo tidak boleh kurang dari tanggal transaksi'];
        }

        if ($request->ajax()) {
            if ($errors) {
                return response()->json(
                    [
                        'errors' => $errors,
                    ],
                    422
                );
            }

            return response()->json('success');
        }

        DB::beginTransaction();

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        }

        $model->loadModel([
            'branch_id' => $request->branch_id,
            'pay_from' => $request->pay_from,
            'customer_id' => $request->pay_from == "customer" ? $request->customer_id : null,
            'from_name' =>  $request->pay_from == "other" ? $request->from_name : '',
            'date' => Carbon::parse($request->date),
            'due_date' => Carbon::parse($request->due_date),
            'cheque_no' => $request->cheque_no,
            'from_bank' => $request->from_bank,
            'realization_bank' => $request->realization_bank,
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'amount' => thousand_to_float($request->amount),
        ]);

        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->amount ?? 0,
                title: "Giro Masuk",
                subtitle: Auth::user()->name . " mengajukan Giro Masuk " . $model->code,
                link: route('admin.receive-payment.show', $model),
                update_status_link: route('admin.receive-payment.update-status', ['id' => $model->id]),
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
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
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
            $model = model::find($id);
            $model->delete();

            Authorization::where('model', model::class)->where('model_id', $id)->delete();
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
    public function select(Request $request)
    {
        $model = model::where(function ($query) use ($request) {
            $query->orWhere('cheque_no', 'like', "%$request->search%");
        })
            ->when($request->date, function ($query) use ($request) {
                $query->whereDate('due_date', '<=', Carbon::parse($request->date));
            });

        if ($request->branch_id) {
            $model->where('branch_id', $request->branch_id);
        }
        if ($request->currency_id) {
            $model->where('currency_id', $request->currency_id);
        }

        if ($request->customer_id) {
            $model->where('customer_id', $request->customer_id);
        }

        if ($request->status) {
            $model->where('status', $request->status);
        }

        if ($request->pay_from) {
            $model->where('pay_from', $request->pay_from);
        }

        $model = $model->limit(10)->get();

        $model->each(function ($data, $index) use ($model) {
            if ($data->outstanding_amount <= 0) {
                $model->forget($index);
            }
        });

        return $this->ResponseJsonData($model);
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
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, 'Tanggal sudah closing'));
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

            if ($model->status == "cancel") {
                $accounts_receivables =  ReceivablesPayment::where('receive_payment_id', $id)->get();
                foreach ($accounts_receivables as $key => $accounts_receivable) {
                    $accounts_receivable->status = 'void';
                    $accounts_receivable->reject_reason = 'giro batal cair';
                    $accounts_receivable->save();
                }

                $incoming_payments =  IncomingPayment::where('receive_payment_id', $id)->get();
                foreach ($incoming_payments as $key => $incoming_payment) {
                    $incoming_payment->status = 'void';
                    $incoming_payment->reject_reason = 'giro batal cair';
                    $incoming_payment->save();
                }
            }
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
}
