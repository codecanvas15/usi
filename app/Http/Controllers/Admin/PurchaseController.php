<?php

namespace App\Http\Controllers\Admin;

use App\Models\Purchase as model;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\PoTrading;
use App\Models\Purchase;
use App\Models\PurchaseRequest;
use App\Models\PurchaseTransport;
use App\Models\PurchaseTransportDetail;
use App\Models\PurchaseTransportTax;
use App\Models\SoTrading;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view purchase-order|view purchase-transport|view purchase-service|view purchase-general", ['only' => ['index']]);
        $this->middleware("permission:create purchase-order|create purchase-transport|create purchase-service|create purchase-general", ['only' => ['create', 'store']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'purchase';

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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currency = Currency::where('is_local', true)->first();
        return view("admin.$this->view_folder.create", compact('currency'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        DB::beginTransaction();

        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), [
                'type_purchase' => 'required|in:general,trading,transportir,jasa'
            ]);
        } else {
            $this->validate($request, [
                'type_purchase' => 'required|in:general,trading,transportir,jasa'
            ]);
        }

        // * creating purchase
        $model = new model();
        $model->loadModel([
            'tipe' => $request->type_purchase,
            'tanggal' => Carbon::parse($request->tanggal),
        ]);
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        if ($request->type_purchase == 'transportir') {
            if (Auth::user()->hasPermissionTo('create purchase-transport')) {
                // * transportir =========================================================================================================================================================
                if ($request->ajax()) {
                    $this->validate_api($request->all(), PurchaseTransport::rules());
                } else {
                    $this->validate($request, PurchaseTransport::rules());
                }

                $so = SoTrading::findOrFail($request->so_trading_id);
                // $total = $so->jumlah_number - $so->so_trading_detail->sudah_dikirim;
                $total = 0;

                // * creating purchase transport
                $purchase_transport = new PurchaseTransport();
                $purchase_transport->loadModel([
                    'purchase_id' => $model->id,
                    'target_delivery' => $request->target_delivery,
                    'purchase_request_id' => $request->purchase_request_id,
                    'so_trading_id' => $request->so_trading_id,
                    'vendor_id' => $request->vendor_id,
                    'harga' => thousand_to_float($request->harga),
                    'total' => $total * thousand_to_float($request->harga),
                    'currency_id' => $request->currency_id,
                    'exchange_rate' => thousand_to_float($request->exchange_rate ?? 0),
                    'ppn' => $request->with_ppn == 'on' ? get_ppn() : 0,
                ]);

                // * saving transport
                try {
                    $purchase_transport->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'create', 'create purchase transport', $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create purchase transport', $th->getMessage()));
                }

                // * creating transport detail
                foreach ($request->jumlah_do as $key => $value) {
                    $purchase_transport_detaill = new PurchaseTransportDetail();
                    $purchase_transport_detaill->loadModel([
                        'jumlah_do' => $value,
                        'jumlah' => thousand_to_float($request->jumlah[$key]),
                        'purchase_transport_id' => thousand_to_float($purchase_transport->id),
                    ]);

                    $total += ($value * thousand_to_float($request->jumlah[$key])) * thousand_to_float($request->harga);

                    try {
                        $purchase_transport_detaill->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'create', 'create purchase transport detail', $th->getMessage(), 422);
                        }

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create purchase transport detail', $th->getMessage()));
                    }
                }

                // * update status purchase request
                if ($request->purchase_request_id) {
                    $purchase_request = PurchaseRequest::find($request->purchase_request_id);
                    $purchase_request->status = 'done';
                    try {
                        $purchase_request->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'create', 'updating status purchase request.', $th->getMessage(), 422);
                        }

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'updating status purchase request.', $th->getMessage()));
                    }
                }

                // * updating purchase code and reference
                $model->kode = $purchase_transport->kode;
                $model->model_reference = PurchaseTransport::class;
                try {
                    $model->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'create', 'updating purchase code and reference.', $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'updating purchase code and reference.', $th->getMessage()));
                }

                // * calculate tax with and update total value
                $tax_list = [];
                if ($request->tax_id != null) {
                    foreach ($request->tax_id as $key => $value) {
                        $tax = Tax::find($value);
                        array_push($tax_list, $tax->value);

                        $model_tax = new PurchaseTransportTax();
                        $model_tax->loadModel([
                            'tax_id' => $tax->id,
                            'purchase_transport_id' => $purchase_transport->id,
                            'value' => $tax->value,
                        ]);

                        try {
                            $model_tax->save();
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            if ($request->ajax()) {
                                return $this->ResponseJsonMessageCRUD(false, 'create', 'create tax', $th->getMessage(), 422);
                            }

                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create tax', $th->getMessage()));
                        }
                    }
                }

                $sub_total = $total + (count($tax_list) > 0 ? $total * array_reduce($tax_list, fn ($a, $b) => $a + $b, 0) : 0);
                $total = $sub_total;

                // * calculate with ppn
                if ($request->with_ppn == 'on') {
                    $total = $sub_total + ($sub_total * get_ppn());
                }

                // * updating the total if has any tax
                $purchase_transport->sub_total = $sub_total;
                $purchase_transport->total = $total;
                try {
                    $purchase_transport->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                }
            }
            // * transportir =========================================================================================================================================================
        } else {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'failed type'));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
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
    }

    /**m
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
    }

    /**
     * sale_order export
     *
     *
     *
     */
    public function export()
    {
        $file = public_path('/pdf_reports/Report-Purchase-' . microtime(true) . '.pdf');
        $fileName = 'Report-Purchase-' . microtime(true) . '.pdf';

        $pdf = PDF::loadview('admin/purchase/export')->setPaper('a4', 'portrait');

        return $pdf->download($fileName);
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        $model = model::where('branch_id', get_current_branch_id())
            ->orWhere('status', '=', 'approve');


        if ($request->search) {
            // $model->where(function ($query) use ($request) {
            //     $query->orWhere('kode', 'like', "%$request->search%");
            // });
        }

        if ($request->vendor_id) {
            $model = $model->whereHas('reference', function ($r) use ($request) {
                $r->where('vendor_id', $request->vendor_id);
            });
        }

        $model = $model->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return $this->ResponseJsonData($model);
    }
}
