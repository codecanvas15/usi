<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TaxReconciliationExport;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Jobs\InvoiceSummaryJob;
use App\Jobs\ItemReceivingReportTaxSummaryJob;
use App\Jobs\SupplierInvoiceTaxSummaryJob;
use App\Models\Authorization;
use App\Models\CashAdvancePayment;
use App\Models\CashAdvanceReceive;
use App\Models\Disposition;
use App\Models\InvoiceDownPayment;
use App\Models\InvoiceGeneral;
use App\Models\InvoiceTax;
use App\Models\InvoiceTaxSummary;
use App\Models\InvoiceTrading;
use App\Models\ItemReceivingReport;
use App\Models\ItemReceivingReportTax;
use App\Models\Journal;
use App\Models\LpbTaxSummary;
use App\Models\PurchaseReturn;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceTaxSummary;
use Illuminate\Http\Request;
use App\Models\TaxReconciliation as model;
use App\Models\TaxReconciliation;
use App\Models\TaxReconciliationBalance;
use App\Models\TaxReconciliationDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TaxReconciliationController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'tax-reconciliation';

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
            $data = model::where('id', '!=', null);

            if ($request->from_date) {
                $data = $data->whereDate('tax_reconciliations.date', '>=', Carbon::parse($request->from_date))
                    ->whereDate('tax_reconciliations.date', '<=', Carbon::parse($request->to_date));
            }

            if (!get_current_branch()->is_primary) {
                $data->where('tax_reconciliations.branch_id', get_current_branch_id());
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', fn($row) => Carbon::parse($row->tax_period)->format('M-Y'))
                ->editColumn('total_in', fn($row) => formatNumber($row->total_in))
                ->editColumn('total_out', fn($row) => formatNumber($row->total_out))
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . fund_submission_status()[$row->status]['color'] . '">
                    ' . fund_submission_status()[$row->status]['text'] . '
                                    </div>';

                    return $badge;
                })
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]) . '<br>' .
                    view("components.datatable.export-button", [
                        'route' => route("admin.tax-reconciliation.export", ['id' => $row->id]),
                        'onclick' => "",
                    ]))
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => 'tax-reconciliation',
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
                    ]);
                })
                ->addColumn('asset_name', function ($row) {
                    return $row->asset_name ?? '';
                })
                ->rawColumns(['status', 'action', 'export', 'code'])
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
            $model = new model();
            $model->loadModel([
                'branch_id' => get_current_branch_id(),
                'coa_id' => $request->coa_id,
                'code' => generate_code(model::class, 'code', 'date', 'TRC', branch_sort: get_current_branch()->sort ?? null, date: Carbon::parse($request->date)),
                'date' => Carbon::parse($request->date),
                'from_date' =>  Carbon::parse($request->from_date),
                'to_date' =>  Carbon::parse($request->to_date),
                'tax_period' => Carbon::parse("01-$request->tax_period"),
            ]);

            if (!$model->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }

            $model->save();

            $total_in = 0;
            $total_out = 0;

            foreach ($request->out_id ?? [] as $key => $out_id) {
                if ($request->out_is_checked[$key] == "true") {
                    $invoice_tax = InvoiceTax::find($out_id);
                    $tax_reconciliation_detail = new TaxReconciliationDetail();
                    $tax_reconciliation_detail->tax_reconciliation_id = $model->id;
                    $tax_reconciliation_detail->reference_model = InvoiceTax::class;
                    $tax_reconciliation_detail->reference_id = $invoice_tax->id;
                    $tax_reconciliation_detail->reference_parent_model = $invoice_tax->reference_parent_model;
                    $tax_reconciliation_detail->reference_parent_id = $invoice_tax->reference_parent_id;
                    $tax_reconciliation_detail->customer_id = $invoice_tax->customer_id;
                    $tax_reconciliation_detail->tax_id = $invoice_tax->tax_id;
                    $tax_reconciliation_detail->dpp = $invoice_tax->dpp;
                    $tax_reconciliation_detail->value = $invoice_tax->value;
                    $tax_reconciliation_detail->amount = $invoice_tax->amount;
                    $tax_reconciliation_detail->type = 'invoice-tax';
                    $tax_reconciliation_detail->out = $request->out[$key];
                    $tax_reconciliation_detail->used_amount = $request->out[$key];
                    $tax_reconciliation_detail->tax_number = $request->out_tax_number[$key];
                    $tax_reconciliation_detail->save();

                    $total_out += $request->out[$key];
                }
            }
            foreach ($request->in_id ?? [] as $key => $in_id) {
                if ($request->in_is_checked[$key] == "true") {
                    $item_receiving_report_tax = ItemReceivingReportTax::find($in_id);
                    $tax_reconciliation_detail = new TaxReconciliationDetail();
                    $tax_reconciliation_detail->tax_reconciliation_id = $model->id;
                    $tax_reconciliation_detail->reference_model = ItemReceivingReportTax::class;
                    $tax_reconciliation_detail->reference_id = $item_receiving_report_tax->id;
                    $tax_reconciliation_detail->reference_parent_model = $item_receiving_report_tax->reference_parent_model;
                    $tax_reconciliation_detail->reference_parent_id = $item_receiving_report_tax->reference_parent_id;
                    $tax_reconciliation_detail->vendor_id = $item_receiving_report_tax->vendor_id;
                    $tax_reconciliation_detail->tax_id = $item_receiving_report_tax->tax_id;
                    $tax_reconciliation_detail->dpp = $item_receiving_report_tax->dpp;
                    $tax_reconciliation_detail->value = $item_receiving_report_tax->value;
                    $tax_reconciliation_detail->amount = $item_receiving_report_tax->amount;
                    $tax_reconciliation_detail->type = 'purchase-tax';
                    $tax_reconciliation_detail->in = $request->in[$key];
                    $tax_reconciliation_detail->used_amount = $request->in[$key];
                    $tax_reconciliation_detail->note = $item_receiving_report_tax->note;
                    $tax_reconciliation_detail->tax_number = $request->in_tax_number[$key];
                    $tax_reconciliation_detail->save();

                    $total_in += $request->in[$key];
                }
            }

            $model->total_in = $total_in;
            $model->total_out = $total_out;
            $model->gap = $total_in - $total_out;
            $model->save();

            if ($model->gap > 0 && !$model->coa) {
                $last_in_data = $model->tax_reconciliation_details()->where('type', 'purchase-tax')
                    ->orderBy('id', 'desc')
                    ->first();

                $last_in_data->used_amount = $last_in_data->in - $model->gap;
                $last_in_data->save();

                $new_item_receiving_report_tax = new ItemReceivingReportTax();
                $new_item_receiving_report_tax->reference_model = TaxReconciliation::class;
                $new_item_receiving_report_tax->reference_id = $model->id;
                $new_item_receiving_report_tax->reference_parent_model = TaxReconciliation::class;
                $new_item_receiving_report_tax->reference_parent_id = $model->id;
                $new_item_receiving_report_tax->vendor_id = null;
                $new_item_receiving_report_tax->tax_id = $last_in_data->tax_id;
                $new_item_receiving_report_tax->date = $model->date;
                $new_item_receiving_report_tax->dpp = 0;
                $new_item_receiving_report_tax->value = 0;
                $new_item_receiving_report_tax->amount = abs($model->gap);
                $new_item_receiving_report_tax->note = "Lebih Bayar " . Carbon::parse($model->date)->translatedFormat('F Y');
                $new_item_receiving_report_tax->save();
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Rekonsiliasi Pajak",
                subtitle: Auth::user()->name . " mengajukan Rekonsiliasi Pajak " . $model->code,
                link: route('admin.tax-reconciliation.show', $model),
                update_status_link: route('admin.tax-reconciliation.update-status', ['id' => $model->id]),
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
        if (Carbon::parse($request->date)->lt(Carbon::parse("01-" . $request->tax_period))) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal tidak boleh kurang dari periode pajak'));
        }

        DB::beginTransaction();
        try {
            $model = model::find($id);
            $model->loadModel([
                'branch_id' => get_current_branch_id(),
                'coa_id' => $request->coa_id,
                'date' => Carbon::parse($request->date),
                'from_date' =>  Carbon::parse($request->from_date),
                'to_date' =>  Carbon::parse($request->to_date),
            ]);

            if (!$model->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
            }

            $model->save();

            $total_in = 0;
            $total_out = 0;

            TaxReconciliationDetail::where('tax_reconciliation_id', $id)->delete();
            TaxReconciliationBalance::where('tax_reconciliation_id', $id)->delete();

            foreach ($request->out_id ?? [] as $key => $out_id) {
                if ($request->out_is_checked[$key] == "true") {
                    $invoice_tax = InvoiceTax::find($out_id);
                    $tax_reconciliation_detail = new TaxReconciliationDetail();
                    $tax_reconciliation_detail->tax_reconciliation_id = $model->id;
                    $tax_reconciliation_detail->reference_model = InvoiceTax::class;
                    $tax_reconciliation_detail->reference_id = $invoice_tax->id;
                    $tax_reconciliation_detail->reference_parent_model = $invoice_tax->reference_parent_model;
                    $tax_reconciliation_detail->reference_parent_id = $invoice_tax->reference_parent_id;
                    $tax_reconciliation_detail->customer_id = $invoice_tax->customer_id;
                    $tax_reconciliation_detail->tax_id = $invoice_tax->tax_id;
                    $tax_reconciliation_detail->dpp = $invoice_tax->dpp;
                    $tax_reconciliation_detail->value = $invoice_tax->value;
                    $tax_reconciliation_detail->amount = $invoice_tax->amount;
                    $tax_reconciliation_detail->type = 'invoice-tax';
                    $tax_reconciliation_detail->out = $request->out[$key];
                    $tax_reconciliation_detail->used_amount = $request->out[$key];
                    $tax_reconciliation_detail->tax_number = $request->out_tax_number[$key];
                    $tax_reconciliation_detail->save();

                    $total_out += $request->out[$key];
                }
            }
            foreach ($request->in_id ?? [] as $key => $in_id) {
                if ($request->in_is_checked[$key] == "true") {
                    $item_receiving_report_tax = ItemReceivingReportTax::find($in_id);
                    $tax_reconciliation_detail = new TaxReconciliationDetail();
                    $tax_reconciliation_detail->tax_reconciliation_id = $model->id;
                    $tax_reconciliation_detail->reference_model = ItemReceivingReportTax::class;
                    $tax_reconciliation_detail->reference_id = $item_receiving_report_tax->id;
                    $tax_reconciliation_detail->reference_parent_model = $item_receiving_report_tax->reference_parent_model;
                    $tax_reconciliation_detail->reference_parent_id = $item_receiving_report_tax->reference_parent_id;
                    $tax_reconciliation_detail->vendor_id = $item_receiving_report_tax->vendor_id;
                    $tax_reconciliation_detail->tax_id = $item_receiving_report_tax->tax_id;
                    $tax_reconciliation_detail->dpp = $item_receiving_report_tax->dpp;
                    $tax_reconciliation_detail->value = $item_receiving_report_tax->value;
                    $tax_reconciliation_detail->amount = $item_receiving_report_tax->amount;
                    $tax_reconciliation_detail->type = 'purchase-tax';
                    $tax_reconciliation_detail->in = $request->in[$key];
                    $tax_reconciliation_detail->used_amount = $request->in[$key];
                    $tax_reconciliation_detail->note = $item_receiving_report_tax->note;
                    $tax_reconciliation_detail->tax_number = $request->in_tax_number[$key];
                    $tax_reconciliation_detail->save();

                    $total_in += $request->in[$key];
                }
            }

            $model->total_in = $total_in;
            $model->total_out = $total_out;
            $model->gap = $total_in - $total_out;
            $model->save();

            if ($model->gap > 0 && !$model->coa) {
                $last_in_data = $model->tax_reconciliation_details()->where('type', 'purchase-tax')
                    ->orderBy('id', 'desc')
                    ->first();

                $last_in_data->used_amount = $last_in_data->in - $model->gap;
                $last_in_data->save();

                $new_item_receiving_report_tax = ItemReceivingReportTax::where('reference_model', TaxReconciliation::class)
                    ->where('reference_id', $model->id)
                    ->first();
                if (!$new_item_receiving_report_tax) {
                    $new_item_receiving_report_tax = new ItemReceivingReportTax();
                }
                $new_item_receiving_report_tax->reference_model = TaxReconciliation::class;
                $new_item_receiving_report_tax->reference_id = $model->id;
                $new_item_receiving_report_tax->reference_parent_model = TaxReconciliation::class;
                $new_item_receiving_report_tax->reference_parent_id = $model->id;
                $new_item_receiving_report_tax->vendor_id = null;
                $new_item_receiving_report_tax->tax_id = $last_in_data->tax_id;
                $new_item_receiving_report_tax->date = $model->date;
                $new_item_receiving_report_tax->dpp = 0;
                $new_item_receiving_report_tax->value = 0;
                $new_item_receiving_report_tax->amount = abs($model->gap);
                $new_item_receiving_report_tax->note = "Lebih Bayar " . Carbon::parse($model->date)->translatedFormat('F Y');
                $new_item_receiving_report_tax->save();
            } else {
                ItemReceivingReportTax::where('reference_model', TaxReconciliation::class)
                    ->where('reference_id', $model->id)
                    ->delete();
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Rekonsiliasi Pajak",
                subtitle: Auth::user()->name . " mengajukan Rekonsiliasi Pajak " . $model->code,
                link: route('admin.tax-reconciliation.show', $model),
                update_status_link: route('admin.tax-reconciliation.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollback();

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
            TaxReconciliationDetail::where('tax_reconciliation_id', $id)->delete();
            TaxReconciliationBalance::where('tax_reconciliation_id', $id)->delete();
            ItemReceivingReportTax::where('reference_model', TaxReconciliation::class)
                ->where('reference_id', $model->id)
                ->delete();

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

    public function get_data(Request $request)
    {
        $tax_period = Carbon::parse("01-$request->date")->format('Y-m-d');
        try {
            $ppn_keluaran = InvoiceTax::with('customer')
                ->with('tax')
                ->leftJoin('tax_reconciliation_details', function ($d) {
                    $d->on('tax_reconciliation_details.reference_id', 'invoice_taxes.id');
                    $d->where('tax_reconciliation_details.reference_model', InvoiceTax::class);
                    $d->whereNull('tax_reconciliation_details.deleted_at');
                    $d->join('tax_reconciliations', function ($t) {
                        $t->on('tax_reconciliation_details.tax_reconciliation_id', 'tax_reconciliations.id');
                        $t->whereNotIn('status', ['reject', 'void']);
                    });
                })
                ->whereMonth('invoice_taxes.date',  Carbon::parse($tax_period))
                ->whereYear('invoice_taxes.date',  Carbon::parse($tax_period))
                ->selectRaw('invoice_taxes.*, COALESCE(SUM(tax_reconciliation_details.used_amount),0) as used_amount')
                ->havingRaw('invoice_taxes.amount != used_amount')
                ->orderBy('invoice_taxes.date', 'asc')
                ->groupBy('invoice_taxes.id')
                ->get();

            $data_ppn_keluaran = [];
            foreach ($ppn_keluaran as $key => $data) {
                // InvoiceGeneral, InvoiceTrading, CashAdvanceReceive, Disposition, InvoiceDownPayment

                if ($data->reference_parent->status == "approve") {
                    $data->outstanding = $data->amount - $data->used_amount;
                    $parent = $data->reference_parent;

                    $tax_number = null;
                    if (in_array($data->reference_parent_model, [InvoiceGeneral::class, InvoiceTrading::class])) {
                        $tax_number = $parent->reference;
                    } else if (in_array($data->reference_parent_model, [CashAdvanceReceive::class, Disposition::class, InvoiceDownPayment::class])) {
                        $tax_number = $parent->tax_number;
                    }

                    $push_ppn_keluaran['data'] = $data;
                    $push_ppn_keluaran['customer'] = $data->customer;
                    $push_ppn_keluaran['reference_parent'] =  $data->reference_parent;
                    $push_ppn_keluaran['faktur_pajak'] =  $tax_number;
                    if (Carbon::parse($data->date)->endOfMonth()->diffInMonths(Carbon::parse($tax_period)->endOfMonth()) > 3) {
                        $push_ppn_keluaran['is_disabled'] = true;
                    } else {
                        $push_ppn_keluaran['is_disabled'] = false;
                    }

                    array_push($data_ppn_keluaran, $push_ppn_keluaran);
                }
            }

            $ppn_masukan = ItemReceivingReportTax::with('vendor')
                ->with('tax')
                ->whereDate('item_receiving_report_taxes.date', '<=', Carbon::parse($tax_period)->endOfMonth())
                ->whereDate('item_receiving_report_taxes.date', '>=', Carbon::parse($tax_period)->startOfMonth()->subMonths(3))
                ->leftJoin('tax_reconciliation_details', function ($d) {
                    $d->on('tax_reconciliation_details.reference_id', 'item_receiving_report_taxes.id');
                    $d->where('tax_reconciliation_details.reference_model', ItemReceivingReportTax::class);
                    $d->whereNull('tax_reconciliation_details.deleted_at');
                    $d->join('tax_reconciliations', function ($t) {
                        $t->on('tax_reconciliation_details.tax_reconciliation_id', 'tax_reconciliations.id');
                        $t->whereNotIn('status', ['reject', 'void']);
                    });
                })
                ->selectRaw('item_receiving_report_taxes.*, COALESCE(SUM(tax_reconciliation_details.used_amount),0) as used_amount')
                ->havingRaw('used_amount = 0')
                ->orderBy('item_receiving_report_taxes.date', 'asc')
                ->groupBy('item_receiving_report_taxes.id')
                ->get();

            $data_ppn_masukan = [];
            foreach ($ppn_masukan as $key => $data) {
                // SupplierInvoice, TaxReconciliation, CashAdvancePayment
                $parent = $data->reference_parent ?? null;

                $tax_number = null;
                if (in_array($data->reference_parent_model, [ItemReceivingReport::class])) {
                    $tax_number = $parent->supplier_invoice_detail->supplier_invoice->tax_reference ?? '-';
                } else if (in_array($data->reference_parent_model, [SupplierInvoice::class])) {
                    $tax_number = $parent->tax_reference;
                } else if (in_array($data->reference_parent_model, [CashAdvancePayment::class])) {
                    $tax_number = $parent->tax_number;
                }

                $push_ppn_masukan['data'] = $data;
                $push_ppn_masukan['vendor'] = $data->vendor;
                $push_ppn_masukan['reference_parent'] =  $parent;
                $push_ppn_masukan['faktur_pajak'] = $tax_number;

                if ($data->reference_parent_model == TaxReconciliation::class) {
                    $data->outstanding = $data->amount - $data->used_amount;

                    $push_ppn_masukan['vendor'] = $data->note;
                    $push_ppn_masukan['reference_parent'] =  '';
                    $push_ppn_masukan['is_disabled'] = false;

                    array_push($data_ppn_masukan, $push_ppn_masukan);
                } else {
                    if (in_array($parent->status, ["approve", "done"])) {
                        $data->outstanding = $data->amount - $data->used_amount;
                        if (Carbon::parse($data->date)->endOfMonth()->diffInMonths(Carbon::parse($tax_period)->endOfMonth()) > 3) {
                            $push_ppn_masukan['is_disabled'] = true;
                        } else {
                            $push_ppn_masukan['is_disabled'] = false;
                        }
                        array_push($data_ppn_masukan, $push_ppn_masukan);
                    }
                }
            }

            return $this->ResponseJsonData([
                'ppn_keluaran' => $data_ppn_keluaran,
                'ppn_masukan' => $data_ppn_masukan,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function export($id)
    {
        try {
            $model = model::findOrFail($id);
            $invoice_taxes = TaxReconciliationDetail::where('tax_reconciliation_id', $model->id)
                ->where('type', 'invoice-tax')
                ->get();

            $purchase_taxes = TaxReconciliationDetail::where('tax_reconciliation_id', $model->id)
                ->where('type', 'purchase-tax')
                ->get();


            $return['model'] = $model;
            $return['invoice_taxes'] = $invoice_taxes;
            $return['purchase_taxes'] = $purchase_taxes;

            $view_file = 'admin.' . $this->view_folder  . '.export_excel';
            return Excel::download(new TaxReconciliationExport($view_file, $return), 'LIST-PPN-TERLAPOR.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function generate_tax_number()
    {
        DB::beginTransaction();
        try {
            $tax_reconciliation_details = TaxReconciliationDetail::all();
            foreach ($tax_reconciliation_details as $key => $tax_reconciliation_detail) {
                $parent =  $tax_reconciliation_detail->reference_parent;
                $tax_number = null;
                if (in_array($tax_reconciliation_detail->reference_parent_model, [InvoiceGeneral::class, InvoiceTrading::class])) {
                    $tax_number = $parent->reference;
                } else if (in_array($tax_reconciliation_detail->reference_parent_model, [CashAdvanceReceive::class, Disposition::class, InvoiceDownPayment::class])) {
                    $tax_number = $parent->tax_number;
                } else if (in_array($tax_reconciliation_detail->reference_parent_model, [ItemReceivingReport::class])) {
                    $tax_number = $parent->supplier_invoice_detail->supplier_invoice->tax_reference;
                } else if (in_array($tax_reconciliation_detail->reference_parent_model, [SupplierInvoice::class])) {
                    $tax_number = $parent->tax_reference;
                } else if (in_array($tax_reconciliation_detail->reference_parent_model, [CashAdvancePayment::class])) {
                    $tax_number = $parent->tax_number;
                }

                $tax_reconciliation_detail->tax_number = $tax_number;
                $tax_reconciliation_detail->save();
            }

            DB::commit();

            return response()->json('success');
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json('failed ' . $th->getMessage());
        }
    }

    public function regenerate(Request $request)
    {
        DB::beginTransaction();
        try {
            $period = null;
            if ($request->period) {
                $period = Carbon::parse('01-' . $request->period);
            }

            $tax_reconciliations = TaxReconciliation::when($request->period, function ($query) use ($period) {
                return $query->whereMonth('tax_period', $period)
                    ->whereYear('tax_period', $period);
            })->get();

            foreach ($tax_reconciliations as $key => $tax_reconciliation) {
                TaxReconciliationDetail::where('tax_reconciliation_id', $tax_reconciliation->id)->forceDelete();
                Authorization::where('model', TaxReconciliation::class)
                    ->where('model_id', $tax_reconciliation->id)
                    ->forceDelete();

                $journals = Journal::where('reference_model', TaxReconciliation::class)
                    ->where('reference_id', $tax_reconciliation->id)
                    ->get();

                foreach ($journals as $key => $journal) {
                    $journal->journal_details()->forceDelete();
                    $journal->forceDelete();
                }

                $tax_reconciliation->forceDelete();
            }

            InvoiceTaxSummary::join('invoice_generals', function ($q) {
                $q->on('invoice_tax_summaries.model_id', 'invoice_generals.id')
                    ->where('invoice_tax_summaries.model_class', InvoiceGeneral::class);
            })
                ->when($period, function ($query) use ($period) {
                    return $query->whereMonth('invoice_generals.date', Carbon::parse($period)->format('m'))
                        ->whereYear('invoice_generals.date', Carbon::parse($period));
                })
                ->delete();

            InvoiceTaxSummary::join('invoice_tradings', function ($q) {
                $q->on('invoice_tax_summaries.model_id', 'invoice_tradings.id')
                    ->where('invoice_tax_summaries.model_class', InvoiceTrading::class);
            })
                ->when($period, function ($query) use ($period) {
                    return $query->whereMonth('invoice_tradings.date', Carbon::parse($period))
                        ->whereYear('invoice_tradings.date', Carbon::parse($period));
                })
                ->delete();

            LpbTaxSummary::when($period, function ($query) use ($period) {
                return $query->whereHas('item_receiving_report', function ($q) use ($period) {
                    $q->whereMonth('date_receive', Carbon::parse($period))
                        ->whereYear('date_receive', Carbon::parse($period));
                });
            })->delete();
            SupplierInvoiceTaxSummary::when($period, function ($query) use ($period) {
                return $query->whereHas('supplier_invoice', function ($q) use ($period) {
                    $q->whereMonth('date', Carbon::parse($period))
                        ->whereYear('date', Carbon::parse($period));
                });
            })->delete();

            InvoiceTax::whereIn('reference_parent_model', [InvoiceGeneral::class, InvoiceTrading::class])
                ->when($period, function ($query) use ($period) {
                    $query->whereMonth('date', Carbon::parse($period))
                        ->whereYear('date', Carbon::parse($period));
                })
                ->withTrashed()->forceDelete();

            ItemReceivingReportTax::whereIn('reference_parent_model', [ItemReceivingReport::class, SupplierInvoice::class])
                ->when($period, function ($query) use ($period) {
                    $query->whereMonth('date', Carbon::parse($period))
                        ->whereYear('date', Carbon::parse($period));
                })
                ->withTrashed()->forceDelete();

            InvoiceSummaryJob::dispatch($period);
            ItemReceivingReportTaxSummaryJob::dispatch($period);
            SupplierInvoiceTaxSummaryJob::dispatch($period);

            DB::commit();
            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'create', null, 'Berhasil menghapus rekonsiliasi pajak'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }
    }
}
