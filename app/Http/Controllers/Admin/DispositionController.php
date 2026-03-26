<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Authorization;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Disposition as model;
use App\Models\Disposition;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode as FacadesQrCode;
use Yajra\DataTables\Facades\DataTables;

class DispositionController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'disposition';

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
        $this->middleware("permission:view asset-disposition", ['only' => ['index', 'show', 'data']]);
        $this->middleware("permission:create asset-disposition", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit asset-disposition", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete asset-disposition", ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = model::leftJoin('assets', 'assets.id', 'dispositions.asset_id')
                ->select('dispositions.*', 'assets.asset_name');

            if ($request->from_date) {
                $data = $data->whereDate('dispositions.date', '>=', Carbon::parse($request->from_date))
                    ->whereDate('dispositions.date', '<=', Carbon::parse($request->to_date));
            }

            if (!get_current_branch()->is_primary) {
                $data->where('dispositions.branch_id', get_current_branch_id());
            }

            $checkAuthorizePrint = authorizePrint('disposition');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', fn ($row) => Carbon::parse($row->date)->format('d-m-Y'))
                ->editColumn('last_book_value', fn ($row) => formatNumber($row->last_book_value))
                ->editColumn('selling_price', fn ($row) => formatNumber($row->selling_price))
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . fund_submission_status()[$row->status]['color'] . '">
                    ' . fund_submission_status()[$row->status]['text'] . '
                                    </div>';

                    return $badge;
                })
                ->addColumn('action', function ($row) use ($checkAuthorizePrint) {
                    $link_export = route('disposition.export.id', ['id' => encryptId($row->id)]);
                    $model = get_class($row);
                    $export_btn = "<a href='$link_export' class='btn btn-sm btn-flat btn-info' target='_blank' onclick='show_print_out_modal(event)' " . ($checkAuthorizePrint ? "data-model='$model' data-id='$row->id' data-print-type='disposition' data-link='$link_export' data-code='$row->code'" : "") . ">Export</a>&nbsp";

                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => 'disposition',
                        "permission_name" => "asset-disposition",
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => $row->status != "approve" &&  $row->status != "reject" &&  $row->status != "void",
                            ],
                            'delete' => [
                                'display' => $row->status != "approve" &&  $row->status != "reject" &&  $row->status != "void",
                            ],
                        ],
                    ]) . $export_btn;
                })
                ->addColumn('asset_name', function ($row) {
                    return $row->asset_name ?? '';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index');
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
            $selling_price = thousand_to_float($request->selling_price ?? 0);
            $tax = Tax::find($request->tax_id);
            $tax_amount = ($selling_price * ($tax->value ?? 0));
            $total = $selling_price + $tax_amount;
            $branch = Branch::find($request->branch_id);

            $model = new model();
            $model->loadModel([
                'code' => generate_code(Disposition::class, 'code', 'date', 'DISP', $branch->sort, $request->date),
                'branch_id' => $request->branch_id,
                'asset_id' => $request->asset_id,
                'gain_loss_coa_id' => $request->gain_loss_coa_id,
                'selling_coa_id' => $request->selling_coa_id,
                'tax_id' => $request->tax_id,
                'tax_number' => $request->tax_number,
                'is_selling_asset' => $request->is_selling_asset ?? 0,
                'date' => Carbon::parse($request->date),
                'last_journal_date' => Carbon::parse($request->last_journal_date),
                'last_book_value' => thousand_to_float($request->last_book_value),
                'selling_price' => $selling_price,
                'tax_value' => $tax->value ?? 0,
                'tax_amount' => $tax_amount,
                'total' => $total,
                'location' => $request->location,
                'customer_id' => $request->customer_id,
                'bank_internal_id' => $request->bank_internal_id,
                'due' => $request->due,
                'due_date' => Carbon::parse($request->due_date),
                'note' => $request->note ?? '-',
            ]);

            if (!$model->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }

            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Disposisi Aktiva Tetap",
                subtitle: Auth::user()->name . " mengajukan Disposisi Aktiva Tetap " . $model->code,
                link: route('admin.disposition.show', $model),
                update_status_link: route('admin.disposition.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollback();
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $int
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = model::findOrFail($id);
        if ($request->ajax()) {
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

        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve';
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->status == "approve" && $model->check_available_date;
        $authorization_logs['can_void_request'] = $model->status == "approve" && $model->check_available_date;
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
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

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        return view("admin.$this->view_folder.$model->item.edit", compact('model'));
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
            $selling_price = thousand_to_float($request->selling_price ?? 0);
            $tax = Tax::find($request->tax_id);
            $tax_amount = ($selling_price * ($tax->value ?? 0));
            $total = $selling_price + $tax_amount;

            $model = model::find($id);
            $model->loadModel([
                'branch_id' => $request->branch_id,
                'asset_id' => $request->asset_id,
                'gain_loss_coa_id' => $request->gain_loss_coa_id,
                'selling_coa_id' => $request->selling_coa_id,
                'tax_id' => $request->tax_id,
                'tax_number' => $request->tax_number,
                'is_selling_asset' => $request->is_selling_asset ?? 0,
                'date' => Carbon::parse($request->date),
                'last_journal_date' => Carbon::parse($request->last_journal_date),
                'last_book_value' => thousand_to_float($request->last_book_value),
                'selling_price' => $selling_price,
                'tax_value' => $tax->value ?? 0,
                'tax_amount' => $tax_amount,
                'total' => $total,
                'location' => $request->location,
                'customer_id' => $request->customer_id,
                'bank_internal_id' => $request->bank_internal_id,
                'due' => $request->due,
                'due_date' => Carbon::parse($request->due_date),
                'note' => $request->note ?? '-',
            ]);

            if (!$model->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
            }

            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? $model->selling_price,
                title: "Disposisi Aktiva Tetap",
                subtitle: Auth::user()->name . " mengajukan Disposisi Aktiva Tetap " . $model->code,
                link: route('admin.disposition.show', $model),
                update_status_link: route('admin.disposition.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollback();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
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

            Authorization::where('model', model::class)
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

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
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

    public function export($id,  Request $request)
    {
        $model = model::with('customer', 'bank_internal')->findOrFail(decryptId($id));
        $fileName = 'invoice-penjualan-asset-' . microtime(true) . '.pdf';

        $qr_url = route('disposition.export.id', ['id' => $id]);
        $qr = base64_encode(FacadesQrCode::size(250)->generate($qr_url));
        $approval = \App\Models\Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve');
            }])
            ->where('model_id', $model->id)
            ->first();

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact(
            'model',
            'qr',
            'approval',
        ))->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

        return $pdf->stream($fileName);
    }
}
