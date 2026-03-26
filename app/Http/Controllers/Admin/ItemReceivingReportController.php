<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ItemReceivingReport as model;
use App\Models\SupplierInvoiceDetail;

class ItemReceivingReportController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder|view item-receiving-report-general|view item-receiving-report-service|view item-receiving-report-trading", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder|create item-receiving-report-general|create item-receiving-report-service|create item-receiving-report-trading", ['only' => ['create', 'store']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'item-receiving-report';

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
    public function index()
    {
        return view("admin.$this->view_folder.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //
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
        //
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
        //
    }

    public function select(Request $request)
    {
        $model = model::whereIn('status', ['approve', 'done'])
            ->when($request->tipe, function ($query, $tipe) {
                return $query->where('tipe', $tipe);
            });

        if ($request->search) {
            $model->where(function ($query) use ($request) {
                $query->orWhere('kode', 'like', "%$request->search%");
            });
        }
        $model = $model->where('vendor_id', $request->vendor_id);
        if ($request->branch_id) {
            $model = $model->where('branch_id', $request->branch_id);
        }

        $model = $model->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    static public function hasInvoice($id)
    {
        return SupplierInvoiceDetail::whereHas('supplier_invoice', function ($query) use ($id) {
            $query->whereIn('status', ['pending', 'revert', 'approve']);
        })
            ->where('item_receiving_report_id', $id)
            ->exists();
    }
}
