<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\Coa;
use App\Models\Currency;
use App\Models\SupplierInvoiceGeneral as model;
use App\Models\SupplierInvoiceGeneralDetail as modelDetail;
use App\Models\Vendor;
use App\Models\VendorCoa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SupplierInvoiceGeneralController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'supplier-invoice-general';

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
            $branch = Branch::find(get_current_branch_id());

            $data = model::with(['vendor', 'currency', 'branch', 'project'])
                ->join('vendors', 'vendors.id', 'supplier_invoice_generals.vendor_id')
                ->when($request->from_date, fn($q) => $q->where('supplier_invoice_generals.date', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->where('supplier_invoice_generals.date', '>=', Carbon::parse($request->to_date)))
                ->select('supplier_invoice_generals.*');

            if (!get_current_branch()->is_primary) {
                $data->where('supplier_invoice_generals.branch_id', get_current_branch_id());
            }
            if ($request->branch_id) {
                $data->where('supplier_invoice_generals.branch_id', $request->branch_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->addColumn('vendor', function ($row) {
                    return $row->vendor->nama;
                })
                ->addColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('d-m-Y');
                })
                // ->addColumn('debit', function ($row) {
                //     return formatNumber($row->debit);
                // })
                // ->addColumn('credit', function ($row) {
                //     return formatNumber($row->credit);
                // })
                ->addColumn('status', function ($row) {
                    if ($row->status == 'approve') {
                        $badge =  '<span class="badge badge-info">Approved</span>';
                    } elseif ($row->status == 'pending') {
                        $badge =  '<span class="badge badge-warning">Pending - Waiting Approval</span>';
                    } elseif ($row->status == 'rejected') {
                        $badge =  '<span class="badge badge-dark">Reject - Purchase Invoice (Non LPB) Rejected</span>';
                    } elseif ($row->status == 'void') {
                        $badge =  '<span class="badge badge-dark">Void - Purchase Invoice (Non LPB) Void</span>';
                    } else {
                        $badge =  '<span class="badge badge-dark">Revert - Purchase Invoice (Non LPB) Reverted</span>';
                    }

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

        $branch_id = $request->branch_id ?? get_current_branch_id();
        $branch   = Branch::find($branch_id);
        $currency_id = $request->currency_id;

        // * create data
        $model = new model();
        $model->code = generate_code(model::class, 'code', 'date', 'SIG', date: $request->date, branch_sort: $branch->sort);
        $model->loadModel([
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'reference' => $request->reference ?? '-',
            'vendor_id' => $request->vendor_id,
            'currency_id' => $currency_id,
            'exchange_rate' => $currency_id == 105 ? 1 : thousand_to_float($request->exchange_rate),
            'branch_id' => $branch_id,
            'project_id' => $request->project_id,
            'term_of_payment' => $request->term_of_payment,
            'top_days' => $request->top_days,
            'top_due_date' => $request->top_due_date ? Carbon::parse($request->top_due_date)->format('Y-m-d') : Carbon::parse($request->date)->addDays($request->top_days),
            'debit' => 0,
            'credit' => 0,
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        // * saving
        try {
            if ($request->hasFile('attachment')) {
                $model->attachment = $this->upload_file($request->file('attachment'), 'supplier_invoice_general');
            }
            $model->save();

            $debit = 0;
            $credit = 0;

            foreach ($request->general_detail_coa_id as $key => $coa) {
                $amount = thousand_to_float($request->general_detail_amount[$key]);

                if ($key == 0) {
                    $credit += $amount;
                } else {
                    $debit += $amount;
                }

                $detail = new modelDetail();
                $detail->supplier_invoice_general_id = $model->id;
                $detail->coa_id = $coa;

                if ($key == 0) {
                    $detail->credit = $amount;
                    $detail->debit = 0;
                } else {
                    $detail->debit = $amount;
                    $detail->credit = 0;
                }

                $detail->type = 'general';
                $detail->notes = $request->general_detail_notes[$key];
                $detail->save();
            }

            foreach ($request->adjustment_coa_id ?? [] as $key => $coa) {
                $reqDebit = thousand_to_float($request->adjustment_debit[$key]);
                // $reqCredit = thousand_to_float($request->adjustment_credit[$key]);

                if ($reqDebit > 0) {
                    $debit += $reqDebit;
                } else {
                    $credit += abs($reqDebit);
                }

                $detail = new modelDetail();
                $detail->supplier_invoice_general_id = $model->id;
                $detail->coa_id = $coa;
                $detail->debit = $reqDebit;
                $detail->credit = 0;
                $detail->notes = $request->adjustment_notes[$key];
                $detail->type = 'journal';
                $detail->save();
            }

            $update = model::find($model->id);
            $update->debit = $debit;
            $update->credit = $credit;
            $update->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->debit ?? 0,
                title: "Puchase Invoice (Non LPB)",
                subtitle: Auth::user()->name . " mengajukan Puchase Invoice (Non LPB) " . $model->code,
                link: route('admin.supplier-invoice-general.show', $model),
                update_status_link: route('admin.supplier-invoice-general.update-status', ['id' => $model->id]),
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
    public function show($id)
    {
        $model = model::with(['vendor', 'currency', 'branch', 'project', 'detail'])->find($id);
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );
        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == "approve";
        $authorization_logs['can_void'] = false;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->status == "approve" && $model->check_available_date;
        $authorization_logs['can_void_request'] = $model->status == "approve" && $model->check_available_date;
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view('admin.' . $this->view_folder . '.show', compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = model::with(['vendor', 'currency', 'branch', 'project', 'detail', 'detail.coa'])->find($id);

        if (!$model->check_available_date) {
            return abort(403);
        }

        return view('admin.' . $this->view_folder . '.edit', compact('model'));
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
        $currency_id = $request->currency_id;

        DB::beginTransaction();

        $model = model::findOrFail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        }

        $model->loadModel([
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'reference' => $request->reference ?? '-',
            'vendor_id' => $request->vendor_id,
            'currency_id' => $currency_id,
            'exchange_rate' => $currency_id == 105 ? 1 : thousand_to_float($request->exchange_rate),
            'branch_id' => $request->branch_id,
            'project_id' => $request->project_id,
            'code' => $model->code,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'term_of_payment' => $request->term_of_payment,
            'top_days' => $request->top_days,
            'top_due_date' => $request->top_due_date ? Carbon::parse($request->top_due_date)->format('Y-m-d') : Carbon::parse($request->date)->addDays($request->top_days),
            'debit' => 0,
            'credit' => 0,
            'payment_status' => 'unpaid'
        ]);

        try {
            if ($request->hasFile('attachment')) {
                Storage::delete($model->attachment);
                $model->attachment = $this->upload_file($request->file('attachment'), 'supplier_invoice_general');
            }
            $model->save();
            $debit = 0;
            $credit = 0;

            $model->detail()->delete();

            foreach ($request->general_detail_coa_id as $key => $coa) {
                $amount = thousand_to_float($request->general_detail_amount[$key]);

                if ($key == 0) {
                    $credit += $amount;
                } else {
                    $debit += $amount;
                }

                $detail = new modelDetail();
                $detail->supplier_invoice_general_id = $model->id;
                $detail->coa_id = $coa;

                if ($key == 0) {
                    $detail->credit = $amount;
                    $detail->debit = 0;
                } else {
                    $detail->debit = $amount;
                    $detail->credit = 0;
                }

                $detail->type = 'general';
                $detail->notes = $request->general_detail_notes[$key];
                $detail->save();
            }

            foreach ($request->adjustment_coa_id ?? [] as $key => $coa) {
                $reqDebit = thousand_to_float($request->adjustment_debit[$key]);
                // $reqCredit = thousand_to_float($request->adjustment_credit[$key]);

                if ($reqDebit > 0) {
                    $debit += $reqDebit;
                } else {
                    $credit += abs($reqDebit);
                }

                $detail = new modelDetail();
                $detail->supplier_invoice_general_id = $model->id;
                $detail->coa_id = $coa;
                $detail->debit = $reqDebit;
                $detail->credit = 0;
                $detail->notes = $request->adjustment_notes[$key];
                $detail->type = 'journal';
                $detail->save();
            }

            $update = model::find($model->id);
            $update->debit = $debit;
            $update->credit = $credit;
            $update->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->debit ?? 0,
                title: "Puchase Invoice (Non LPB)",
                subtitle: Auth::user()->name . " mengajukan Puchase Invoice (Non LPB) " . $model->code,
                link: route('admin.supplier-invoice-general.show', $model),
                update_status_link: route('admin.supplier-invoice-general.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        $model = model::findOrfail($id);
        $detail = modelDetail::where('supplier_invoice_general_id', $id);

        // * saving and make reponse
        try {
            $detail->delete();
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
            return $this->ResponseJsonMessageCRUD(true, 'delete');
        }

        return redirect()->back()->with($this->ResponseMessageCRUD());
    }

    public function update_status(Request $request, $id)
    {
        DB::beginTransaction();
        $model = model::findOrfail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, 'Tanggal sudah closing'));
        }

        validate_branch($model->branch_id);
        // * saving and make reponse
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
                $model->approved_by = auth()->user()->id;
                $model->save();
            } else {
                $this->create_activity_status_log(model::class, $id,  $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization_response = $authorization->authorize(
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

    public function getCurrency()
    {
        $currencies = Currency::all();
        return $this->ResponseJsonData($currencies);
    }

    public function getVendor()
    {
        $vendors = Vendor::all();
        return $this->ResponseJsonData($vendors);
    }

    public function coa_with_code_name(Request $request)
    {
        $coa = Coa::find($request->id);

        $data['id'] = $coa->id;
        $data['account_code'] = $coa->account_code;
        $data['name'] = $coa->name;

        return $this->ResponseJsonData($data);
    }

    public function vendor_with_top(Request $request)
    {
        $vendor = Vendor::find($request->id);

        $data['top'] = $vendor->term_of_payment;

        if ($vendor->term_of_payment == 'by days') {
            $data['top_days'] = $vendor->top_days;
        }

        return $this->ResponseJsonData($data);
    }

    public function vendor_coa(Request $request)
    {
        $coa = VendorCoa::where('vendor_id', $request->id)
            ->where('type', 'Account Payable Coa')
            ->first()
            ->coa;

        return ['data' => $coa];
    }
}
