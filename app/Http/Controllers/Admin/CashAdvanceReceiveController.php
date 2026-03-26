<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\NotificationHelper;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\FundSubmission;
use App\Models\CashAdvanceReceive as model;
use App\Models\CashAdvanceReceive;
use App\Models\CashAdvanceReceiveDetail;
use App\Models\CustomerCoa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class CashAdvanceReceiveController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'cash-advance-receive';

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
            $data = model::leftJoin('projects', 'projects.id', 'cash_advance_receives.project_id')
                ->join('customers', 'customers.id', 'cash_advance_receives.customer_id')
                ->leftJoin('bank_code_mutations', function ($q) {
                    $q->on('cash_advance_receives.id', 'bank_code_mutations.ref_id')
                        ->where('bank_code_mutations.ref_model', model::class);
                })
                ->leftJoin('cash_advance_receive_details', function ($q) {
                    $q->on('cash_advance_receives.id', '=', 'cash_advance_receive_details.cash_advance_receive_id')
                        ->where('cash_advance_receive_details.type', 'cash_advance');
                })
                ->whereDate('cash_advance_receives.date', '>=', Carbon::parse($request->from_date))
                ->whereDate('cash_advance_receives.date', '<=', Carbon::parse($request->to_date))
                ->select(
                    'cash_advance_receives.*',
                    'projects.name as project_name',
                    'customers.nama as customer_name',
                    DB::raw('SUM(cash_advance_receive_details.credit) as cash_advance_value')
                )
                ->groupBy('cash_advance_receives.id', 'projects.name', 'customers.nama');

            if (!get_current_branch()->is_primary) {
                $data->where('cash_advance_receives.branch_id', get_current_branch_id());
            }
            if ($request->branch_id) {
                $data->where('cash_advance_receives.branch_id', $request->branch_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('project_name', function ($row) {
                    return $row->project_name ?? '';
                })
                ->editColumn('cash_advance_value', function ($row) {
                    return $row->currency->simbol . ' ' . formatNumber($row->cash_advance_value ?? 0);
                })
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->bank_code_mutation ?? $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
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
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => ['display' => true],
                            'edit' => ['display' => $row->status != 'approve' && $row->status != 'reject' && $row->status != 'void'],
                            'delete' => ['display' => $row->status != 'approve' && $row->status != 'reject' && $row->status != 'void'],
                        ],
                    ]) . view("components.datatable.export-button", [
                        'route' => route("cash-advance-receive.export.id", ['id' => encryptId($row->id)]),
                        'onclick' => "",
                    ]) . view("components.datatable.export-button", [
                        'route' => route("cash-advance-receive.export-proforma.id", ['id' => encryptId($row->id)]),
                        'onclick' => "",
                        'label' => 'invoice',
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
        $code = generate_code(model::class, 'code', 'date', 'UMM', branch_sort: $branch->sort);

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

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
        $branch = Branch::find($request->branch_id);

        try {
            $branch_id = $request->branch_id ?? Auth::user()->branch_id;
            $branch = Branch::find($branch_id);

            $cash_advance_receive = new model();
            $cash_advance_receive->code = generate_code(model::class, 'code', 'date', 'UMM', date: $request->date, branch_sort: $branch->sort);
            $cash_advance_receive->branch_id = $branch_id;
            $cash_advance_receive->project_id = $request->project_id;
            $cash_advance_receive->customer_id = $request->customer_id;
            $cash_advance_receive->tax_id = $request->tax_id;
            $cash_advance_receive->tax_number = $request->tax_number;
            $cash_advance_receive->date = Carbon::parse($request->date);
            $cash_advance_receive->reference = $request->reference;
            $cash_advance_receive->currency_id = $request->currency_id;
            $cash_advance_receive->exchange_rate = thousand_to_float($request->exchange_rate);
            $cash_advance_receive->keterangan = "";

            if (!$cash_advance_receive->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }

            $cash_advance_receive->save();

            $model = $cash_advance_receive;

            foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                if ($key == 0) {
                    $note = $request->note[1] ?? $request->reference;
                } else {
                    $note = $request->note[$key] ?? '';
                }
                $cash_advance_receive_detail = new CashAdvanceReceiveDetail();
                $cash_advance_receive_detail->cash_advance_receive_id = $cash_advance_receive->id;
                $cash_advance_receive_detail->coa_id = $coa_detail_id;
                $cash_advance_receive_detail->type = $request->type[$key];
                $cash_advance_receive_detail->note = $note;
                if ($request->position[$key] == "debit") {
                    $cash_advance_receive_detail->debit = thousand_to_float($request->amount[$key] ?? 0);
                } else {
                    $cash_advance_receive_detail->credit = thousand_to_float($request->amount[$key] ?? 0);
                }
                $cash_advance_receive_detail->save();
            }

            try {
                $code = generate_bank_code(
                    ref_model: CashAdvanceReceive::class,
                    ref_id: $cash_advance_receive->id,
                    coa_id: $cash_advance_receive->getCashAdvanceCashBankAttribute()->coa_id,
                    type: 'in',
                    date: $request->date,
                    is_save: true,
                    code: $request->sequence_code,
                );

                if (!$code) {
                    DB::rollBack();
                    CashAdvanceReceiveDetail::where('cash_advance_receive_id', $cash_advance_receive->id)->delete();
                    $cash_advance_receive->delete();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih'));
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih'));
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $cash_advance_receive->cash_advance_cash_advance->debit ?? 0,
                title: "Penerimaan Deposit",
                subtitle: auth()->user()->name . " mengajukan penerimaan deposit " . $code,
                link: route('admin.cash-advance-receive.show', $model->id),
                update_status_link: route('admin.cash-advance-receive.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            $notification = new NotificationHelper();
            $notification->send_notification(
                branch_id: $cash_advance_receive->branch_id,
                user_id: null,
                roles: [],
                permissions: ['approve ' . $this->view_folder],
                title: "UANG MUKA CUSTOMER",
                body: $cash_advance_receive->code . ' - ' . $cash_advance_receive->customer->nama,
                reference_model: get_class($cash_advance_receive),
                reference_id: $cash_advance_receive->id,
                link: route("admin.{$this->view_folder}.show", $cash_advance_receive),
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }


        DB::commit();
        return redirect()->route("admin.incoming-payment.index", ['tab' => 'cash-advance-receive-tab'])->with($this->ResponseMessageCRUD());
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
        $authorization_logs['can_revert'] = $model->status == "approve" && $model->check_available_date;
        $authorization_logs['can_void'] =  $model->status == "approve" && $model->check_available_date;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->status == "approve" && $model->check_available_date;
        $authorization_logs['can_void_request'] = $model->status == "approve" && $model->check_available_date;
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
            $cash_advance_receive = model::find($id);

            if (!$cash_advance_receive->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
            }

            $cash_advance_receive->branch_id = $request->branch_id ?? Auth::user()->branch_id;
            $cash_advance_receive->project_id = $request->project_id;
            $cash_advance_receive->customer_id = $request->customer_id;
            $cash_advance_receive->date = Carbon::parse($request->date);
            $cash_advance_receive->tax_id = $request->tax_id;
            $cash_advance_receive->tax_number = $request->tax_number;
            $cash_advance_receive->reference = $request->reference;
            $cash_advance_receive->currency_id = $request->currency_id;
            $cash_advance_receive->exchange_rate = thousand_to_float($request->exchange_rate);
            $cash_advance_receive->keterangan = "";
            $cash_advance_receive->save();

            $model = $cash_advance_receive;

            CashAdvanceReceiveDetail::when(is_array($request->cash_advance_receive_detail_id), function ($query) use ($request) {
                $query->whereNotIn('id', collect($request->cash_advance_receive_detail_id)->filter(function ($value, $key) {
                    return $value != null;
                })
                    ->toArray());
            })
                ->where('cash_advance_receive_id', $cash_advance_receive->id)
                ->delete();

            foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                if ($key == 0) {
                    $note = $request->note[1] ?? $request->reference;
                } else {
                    $note = $request->note[$key] ?? '';
                }
                $cash_advance_receive_detail = CashAdvanceReceiveDetail::find($request->cash_advance_receive_detail_id[$key]);
                if (!$cash_advance_receive_detail) {
                    $cash_advance_receive_detail = new CashAdvanceReceiveDetail();
                }
                $cash_advance_receive_detail->cash_advance_receive_id = $cash_advance_receive->id;
                $cash_advance_receive_detail->coa_id = $coa_detail_id;
                $cash_advance_receive_detail->type = $request->type[$key];
                $cash_advance_receive_detail->note = $note;
                if ($request->position[$key] == "debit") {
                    $cash_advance_receive_detail->debit = thousand_to_float($request->amount[$key] ?? 0);
                } else {
                    $cash_advance_receive_detail->credit = thousand_to_float($request->amount[$key] ?? 0);
                }
                $cash_advance_receive_detail->save();
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $cash_advance_receive->cash_advance_cash_advance->debit ?? 0,
                title: "Penerimaan Deposit",
                subtitle: auth()->user()->name . " mengajukan penerimaan deposit " . ($model->bank_code_mutation ?? $model->code),
                link: route('admin.cash-advance-receive.show', $model->id),
                update_status_link: route('admin.cash-advance-receive.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            $notification = new NotificationHelper();
            $notification->send_notification(
                branch_id: $cash_advance_receive->branch_id,
                user_id: null,
                roles: [],
                permissions: ['approve ' . $this->view_folder],
                title: "UANG MUKA CUSTOMER DIPERBARUI",
                body: $cash_advance_receive->code . ' - ' . $cash_advance_receive->customer->nama,
                reference_model: get_class($cash_advance_receive),
                reference_id: $cash_advance_receive->id,
                link: route("admin.{$this->view_folder}.show", $cash_advance_receive),
            );

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        return redirect()->route("admin.incoming-payment.index", ['tab' => 'cash-advance-receive-tab'])->with($this->ResponseMessageCRUD(true, 'edit'));
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
            CashAdvanceReceiveDetail::where('cash_advance_receive_id', $id)
                ->where('cash_advance_receive_id', $id)
                ->delete();
            $model->delete();

            Authorization::where('model', model::class)->where('model_id', $model->id)->delete();
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

        return redirect()->route("admin.incoming-payment.index", ['tab' => 'cash-advance-receive-tab'])->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        $model = DB::table('cash_advance_receives')
            ->where('cash_advance_receives.status',  'approve')
            ->join('cash_advance_receive_details', function ($q) {
                $q->on('cash_advance_receives.id', '=', 'cash_advance_receive_details.cash_advance_receive_id')
                    ->where('cash_advance_receive_details.type', 'cash_advance');
            })
            ->join('customers', 'customers.id', '=', 'cash_advance_receives.customer_id')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) {
                    $query->where('customers.nama', 'like', '%' . request('search') . '%')
                        ->orWhere('cash_advance_receives.code', 'like', '%' . request('search') . '%');
                });
            })
            ->when($request->branch_id, function ($q) use ($request) {
                $q->where('cash_advance_receives.branch_id', $request->branch_id);
            })
            ->when($request->currency_id, function ($q) use ($request) {
                $q->where('cash_advance_receives.currency_id', $request->currency_id);
            })
            ->whereColumn('cash_advance_receive_details.credit', '>', 'cash_advance_receives.returned_amount')
            ->select(
                'cash_advance_receives.id',
                'cash_advance_receives.code',
                'customers.nama as customer_name',
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

        return view('admin.cash-advance-receive._readonly_form', ['model' => $model])->render();
    }

    public function customer_coa(Request $request, $id)
    {
        $model = CustomerCoa::where('customer_id', $id)
            ->where('tipe', $request->type)
            ->with('coa')
            ->first();

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }
    }

    public function export($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $fileName = 'KAS MASUK - ' . ucfirst($model->bank_code_mutation) . '.pdf';

        $qr_url = route('cash-advance-receive.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $pdf = Pdf::loadview("admin/.$this->view_folder./export", compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream($fileName);
    }

    public function export_proforma($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $fileName = 'INVOICE - ' . ucfirst($model->bank_code_mutation) . '.pdf';

        $qr_url = route('cash-advance-receive.export-proforma.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $pdf = Pdf::loadview("admin/.$this->view_folder./export_proforma", compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream($fileName);
    }

    public function update_tax($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $model = CashAdvanceReceive::findOrFail($id);

            $tax_number = $request->tax_number;
            $file_path = '';
            if ($request->file('tax_attachment')) {
                $file_path =  $this->upload_file($request->file('tax_attachment'), 'purchase-down-payment');
            } else {
                $file_path = $model->tax_attachment ?? '';
            }

            $model->tax_number = $tax_number;
            $model->tax_attachment = $file_path;
            $model->save();
            DB::commit();
            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'edit', null));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }
    }
}
