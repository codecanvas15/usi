<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\CashAdvancePayment;
use App\Models\CashAdvancePaymentDetail;
use App\Models\IncomingPayment as model;
use App\Models\IncomingPayment;
use App\Models\IncomingPaymentDetail as modelDetail;
use App\Models\IncomingPaymentDetail;
use App\Models\PurchaseReturn;
use App\Models\ReceivePayment;
use App\Models\Vendor;
use App\Models\VendorCoa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class IncomingPaymentController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'incoming-payment';

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
            $data = model::whereDate('incoming_payments.date', '>=', Carbon::parse($request->from_date))
                ->whereDate('incoming_payments.date', '<=', Carbon::parse($request->to_date))
                ->leftJoin('bank_code_mutations', function ($q) {
                    $q->on('incoming_payments.id', 'bank_code_mutations.ref_id')
                        ->where('bank_code_mutations.ref_model', model::class);
                })
                ->groupBy('incoming_payments.id')
                ->select('incoming_payments.*');

            if (!get_current_branch()->is_primary) {
                $data->where('incoming_payments.branch_id', get_current_branch_id());
            }

            if ($request->branch_id) {
                $data->where('incoming_payments.branch_id', $request->branch_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn(
                    'code',
                    fn($row) => view('components.datatable.detail-link', [
                        'field' => $row->bank_code_mutation ?? $row->code,
                        'row' => $row,
                        'main' => $this->view_folder,
                    ]) . '<br>' .
                        view("components.datatable.export-button", [
                            'route' => route("incoming-payment.export.id", ['id' => encryptId($row->id)]),
                            'onclick' => "",
                        ])
                )
                ->editColumn('total', function ($row) {
                    return $row->currency->simbol . " " . formatNumber($row->total);
                })
                ->editColumn('date', function ($row) {
                    return localDate($row->date);
                })
                ->editColumn('status', function ($row) {
                    $status = incoming_payment_status()[$row->status];
                    $badge = '<div class="badge badge-lg badge-' . $status['color'] . '">
                                    ' . $status['text'] . '
                                </div>';

                    return $badge;
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
                ->rawColumns(['action', 'status', 'code'])
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

        $code = generate_code(model::class, 'code', 'date', 'BBM', branch_sort: get_current_branch()->sort ?? null);
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
            $bank_code = "BM";
            $bank_code .= \App\Models\BankInternal::where('coa_id', $request->coa_id)->first()?->code;
            $branch = Branch::find($request->branch_id);


            $branch_id = $request->branch_id ?? get_current_branch_id();
            $branch = Branch::find($branch_id);

            $incoming_payment = new model();
            $incoming_payment->code = generate_code(model::class, 'code', 'date', $bank_code, branch_sort: $branch->sort ?? null, date: $request->date);
            $incoming_payment->branch_id = $request->branch_id;
            $incoming_payment->receive_payment_id = $request->receive_payment_id;
            $incoming_payment->currency_id = $request->currency_id;
            $incoming_payment->coa_id = $request->coa_id;
            $incoming_payment->project_id = $request->project_id;
            $incoming_payment->from_name = $request->from_name;
            $incoming_payment->date = Carbon::parse($request->date);
            $incoming_payment->exchange_rate = thousand_to_float($request->exchange_rate);
            $incoming_payment->reference = $request->reference ?? '-';
            $incoming_payment->purchase_return_id = $request->purchase_return_id;
            $incoming_payment->cash_advance_payment_id = $request->cash_advance_payment_id;

            if (!$incoming_payment->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }

            $incoming_payment->save();

            foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                $incoming_payment_detail = new modelDetail();
                $incoming_payment_detail->incoming_payment_id = $incoming_payment->id;
                if ($request->is_return[$key] ?? '' == "true") {
                    $incoming_payment_detail->purchase_return_id = $request->purchase_return_id;
                }
                $incoming_payment_detail->coa_id = $coa_detail_id;
                $incoming_payment_detail->note = $request->note[$key] ?? '-';
                $incoming_payment_detail->debit = thousand_to_float($request->debit[$key] ?? 0);
                $incoming_payment_detail->credit = thousand_to_float($request->credit[$key] ?? 0);
                $incoming_payment_detail->type = $request->type[$key] ?? null;
                $incoming_payment_detail->save();
            }

            $incoming_payment->total = $incoming_payment->incoming_payment_details->sum('credit');
            $incoming_payment->save();

            try {
                $code = generate_bank_code(
                    ref_model: IncomingPayment::class,
                    ref_id: $incoming_payment->id,
                    coa_id: $incoming_payment->coa_id,
                    type: 'in',
                    date: $request->date,
                    is_save: true,
                    code: $request->sequence_code,
                );

                if (!$code) {
                    DB::rollBack();
                    $incoming_payment->delete();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih'));
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih'));
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $incoming_payment->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $incoming_payment->id,
                amount: $incoming_payment->total ?? 0,
                title: "Kas Masuk",
                subtitle: Auth::user()->name . " mengajukan Kas Masuk " . $code,
                link: route('admin.incoming-payment.show', $incoming_payment),
                update_status_link: route('admin.incoming-payment.update-status', ['id' => $incoming_payment->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
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

        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve';
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] =  $model->check_available_date && $model->status == 'approve';
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
            return  abort(403);
        }

        if (in_array($model->status, ['approve', 'reject']) || !$model->check_available_date) {
            return redirect()->route("admin.{$this->view_folder}.show", $model)->with($this->ResponseMessageCRUD(false, 'edit', null, 'data tidak dapat diedit'));
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
        DB::beginTransaction();
        try {
            $incoming_payment = model::find($id);

            if (!$incoming_payment->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
            }

            if (!$incoming_payment->check_available_date) {
                abort(403);
            }

            $incoming_payment->branch_id = $request->branch_id;
            $incoming_payment->receive_payment_id = $request->receive_payment_id;
            $incoming_payment->currency_id = $request->currency_id;
            $incoming_payment->coa_id = $request->coa_id;
            $incoming_payment->purchase_return_id = $request->purchase_return_id;
            $incoming_payment->project_id = $request->project_id;
            $incoming_payment->from_name = $request->from_name;
            $incoming_payment->date = Carbon::parse($request->date);
            $incoming_payment->exchange_rate = thousand_to_float($request->exchange_rate);
            $incoming_payment->reference = $request->reference ?? '-';
            $incoming_payment->cash_advance_payment_id = $request->cash_advance_payment_id;
            $incoming_payment->save();

            $existing_detail = collect($request->incoming_payment_detail_id ?? [])->filter(fn($item) => $item != null)->toArray();
            if (count($existing_detail ?? []) > 0) {
                modelDetail::where('incoming_payment_id', $incoming_payment->id)
                    ->whereNotIn('id', $existing_detail)->delete();
            } else {
                modelDetail::where('incoming_payment_id', $incoming_payment->id)->delete();
            }

            foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                $incoming_payment_detail = modelDetail::find($request->incoming_payment_detail_id[$key]);
                if (!$incoming_payment_detail) {
                    $incoming_payment_detail = new modelDetail();
                }
                $incoming_payment_detail->incoming_payment_id = $incoming_payment->id;
                $incoming_payment_detail->coa_id = $coa_detail_id;
                if ($request->is_return[$key] ?? '' == "true") {
                    $incoming_payment_detail->purchase_return_id = $request->purchase_return_id;
                }
                $incoming_payment_detail->note = $request->note[$key] ?? '-';
                $incoming_payment_detail->debit = thousand_to_float($request->debit[$key] ?? 0);
                $incoming_payment_detail->credit = thousand_to_float($request->credit[$key] ?? 0);
                $incoming_payment_detail->type = $request->type[$key] ?? null;
                $incoming_payment_detail->save();
            }

            $incoming_payment->total = $incoming_payment->incoming_payment_details->sum('credit');
            $incoming_payment->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $incoming_payment->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $incoming_payment->id,
                amount: $incoming_payment->total ?? 0,
                title: "Kas Masuk",
                subtitle: Auth::user()->name . " mengajukan Kas Masuk " . ($incoming_payment->bank_code_mutation ?? $incoming_payment->code),
                link: route('admin.incoming-payment.show', $incoming_payment),
                update_status_link: route('admin.incoming-payment.update-status', ['id' => $incoming_payment->id]),
                division_id: auth()->user()->division_id ?? null
            );

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

        if (!$model->check_available_date) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            IncomingPaymentDetail::where('incoming_payment_id', $id)->delete();
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

    public function export($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $fileName = 'KAS MASUK - ' . ucfirst($model->bank_code_mutation) . '.pdf';

        $qr_url = route('incoming-payment.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream($fileName);
    }

    public function purchase_return_select(Request $request)
    {
        $purchase_returns = PurchaseReturn::leftJoin('purchase_return_histories', function ($purchase_return_histories) {
            $purchase_return_histories->on('purchase_return_histories.purchase_return_id', 'purchase_returns.id')
                ->whereIn('purchase_return_histories.status', ['pending', 'revert', 'approve'])
                ->whereNull('purchase_return_histories.deleted_at');
        })
            ->selectRaw(
                'purchase_returns.*,
        COALESCE(sum(purchase_return_histories.amount), 0) as total_used,
        purchase_returns.total - COALESCE(sum(purchase_return_histories.amount), 0) as remaining_amount,
        vendors.nama as vendor_name',
            )
            ->havingRaw('purchase_returns.total > coalesce(total_used, 0)')
            ->where('purchase_returns.status', 'approve')
            ->whereHas('currency', function ($currency) use ($request) {
                $currency->where('currency_id', $request->currency_id);
            })
            ->when(get_current_branch()->is_primary, function ($purchase_returns) use ($request) {
                $purchase_returns->where('purchase_returns.branch_id', $request->branch_id ?? get_current_branch_id());
            })
            ->when(!get_current_branch()->is_primary, function ($purchase_returns) use ($request) {
                $purchase_returns->where('purchase_returns.branch_id', get_current_branch_id());
            })
            ->join('vendors', 'vendors.id', 'purchase_returns.vendor_id')
            ->when($request->search, function ($purchase_returns) use ($request) {
                $purchase_returns->where(function ($purchase_returns) use ($request) {
                    $purchase_returns->where('purchase_returns.code', 'like', "%$request->search%")
                        ->orWhere('vendors.nama', 'like', "%$request->search%");
                });
            })
            ->when($request->date, function ($purchase_returns) use ($request) {
                $purchase_returns->whereDate('purchase_returns.date', $request->date);
            })
            ->groupBy('purchase_returns.id')
            ->get();

        return $this->ResponseJsonData($purchase_returns);
    }

    public function purchase_return_detail(Request $request, $id)
    {
        $purchase_return = PurchaseReturn::findOrFail($id);
        $purchase_return_histories = $purchase_return->purchase_return_histories()
            ->whereIn('status', ['pending', 'revert', 'approve'])
            ->whereNull('deleted_at')
            ->get();

        $purchase_return->used_amount = $purchase_return_histories->sum('amount');
        $purchase_return->remaining_amount = $purchase_return->total - $purchase_return->used_amount;

        $vendor = Vendor::where('id', $purchase_return->vendor_id)
            ->first();

        $vendor_account_payable_coa = VendorCoa::where('vendor_id', $purchase_return->vendor_id)
            ->where('type', 'Account Payable Coa')
            ->first();

        $data = [
            'purchase_return' => $purchase_return,
            'vendor' => $vendor,
            'vendor_account_payable_coa' => $vendor_account_payable_coa->coa,
        ];

        return response()->json($data);
    }

    public function cash_advance_detail(Request $request, $id)
    {
        $cash_advance = CashAdvancePayment::findOrFail($id);
        $cash_advance_detail = CashAdvancePaymentDetail::where('cash_advance_payment_id', $cash_advance->id)
            ->where('type', 'cash_advance')
            ->first();
        $cash_advance->cash_advance_coa = $cash_advance_detail->coa;
        $cash_advance->outstanding_amount = $cash_advance_detail->credit - $cash_advance->returned_amount;

        return response()->json($cash_advance);
    }
}
