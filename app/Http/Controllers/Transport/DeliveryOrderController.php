<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\DeliveryOrder;
use App\Models\PurchaseTransport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryOrderController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'delivery-order';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PurchaseTransport::where('vendor_id', '=', Auth::user()->user_vendor->id)
                ->where(function ($query) {
                    $query->where('status', '=', 'approve')
                        ->orWhere('status', '=', 'partial-sent')
                        ->orWhere('status', '=', 'done');
                })
                ->orderByDesc('created_at')
                ->with(['vendor', 'so_trading.customer'])
                ->select('purchase_transports.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('kode', fn($row) => view('transport.delivery-order.datatable.detail-link', [
                    'field' => $row->kode,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn(
                    'status',
                    function ($row) {
                        $do_done = 0;
                        $do_total = 0;
                        foreach ($row->purchase_transport_details as $key => $value) {
                            $do_done += $value->delivery_orders->where('status', '=', 'done')->count();
                            $do_total += $value->delivery_orders->count();
                        }

                        if ($do_done == $do_total) {
                            return '<span class="badge badge-success">Done</span>';
                        } elseif ($do_done > 0) {
                            return '<span class="badge badge-warning">Partial</span>';
                        } else {
                            return '<span class="badge badge-danger">Not Yet</span>';
                        }
                    }
                )
                ->addColumn('do_done', function ($row) {
                    $do_done = 0;
                    $do_total = 0;
                    foreach ($row->purchase_transport_details as $key => $value) {
                        $do_done += $value->delivery_orders->where('status', '=', 'done')->count();
                        $do_total += $value->delivery_orders->count();
                    }

                    return $do_done . ' / ' . $do_total;
                })
                ->rawColumns(['kode', 'status'])
                ->make(true);
        }

        return view("transport.$this->view_folder.index");
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = PurchaseTransport::findOrFail($id);

        if ($model->vendor_id != Auth::user()->user_vendor->id) {
            abort(404);
        }

        return view("transport.$this->view_folder.show", compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * delivery_orders
     *
     * @param arguments
     * @return mixed
     */
    public function delivery_orders($id)
    {
        $model = PurchaseTransport::findOrFail($id);
        $data = DeliveryOrder::where('so_trading_id', $model->so_trading_id);

        return  DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('code', function ($row) use ($model) {
                return view("transport.delivery-order.datatable.detail-link-list-do", [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                    'model' => $model,
                ]);
            })
            ->editColumn('target_delivery', fn($row) => localDate($row->target_delivery) ?? "-")
            ->editColumn('load_date', fn($row) => localDate($row->load_date) ?? "-")
            ->editColumn('unload_date', fn($row) => localDate($row->unload_date) ?? "-")
            ->editColumn('load_quantity', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                formatNumber($row->load_quantity) . ' ' . $unit;
            })
            ->editColumn('unload_quantity', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                formatNumber($row->unload_quantity) . ' ' . $unit;
            })
            ->editColumn('status', function ($row) {
                $badge = '<div class="badge badge-lg badge-' . get_delivery_order_status()[$row->status]['color'] . '">
                            ' . get_delivery_order_status()[$row->status]['label'] . ' - ' . get_delivery_order_status()[$row->status]['text'] . '
                        </div>';

                $badge .= '<div class="ms-10 badge badge-lg badge-' . ($row->status_print ? "success" : "warning") . '">
                                    ' . ($row->status_print ? 'Sudah Dicetak' : 'Belum Dicetak')  . '
                                </div>';
                return $badge;
            })
            ->addColumn('action', function ($row) use ($model) {
                return view("transport.$this->view_folder.datatable.button", [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                    'model' => $model,
                ]);
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    /**
     * request_print_all
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function request_print_all($id)
    {
        $model = PurchaseTransport::findOrFail($id);

        if ($model->vendor_id != Auth::user()->user_vendor->id) {
            abort(404);
        }

        DB::beginTransaction();
        foreach ($model->purchase_transport_details as $detail) {
            foreach ($detail->delivery_orders->whereNotIn('status', ['reject', 'cancel', 'done']) as $do) {
                $old_status = $do->status;
                $do->status = 'request-print';
                try {
                    $do->save();
                } catch (\Throwable $th) {
                    DB::rollback();
                    return redirect()->back()->with('error', $th->getMessage());
                }

                $this->create_activity_status_log(DeliveryOrder::class, $do->id, 'request print', $old_status, 'request-print');
            }
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'print', 'request print all'));
    }


    /**
     * request_print
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function request_print($purchase_transport_id, $delivery_order_id)
    {
        $model = PurchaseTransport::findOrFail($purchase_transport_id);
        $data = DeliveryOrder::findOrFail($delivery_order_id);
        $old_status = $data->status;

        if ($model->vendor_id != Auth::user()->user_vendor->id) {
            abort(404);
        }

        DB::beginTransaction();
        $data->status = 'request-print';

        try {
            $data->save();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'print', 'request print'));
        }

        $this->create_activity_status_log(DeliveryOrder::class, $data->id, 'request print', $old_status, 'request-print');
        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'request', 'request print'));
    }

    /**
     * show single delivery order
     *
     * @param $purchase_transport_id
     * @param $delivery_order_id
     * @return \Illuminate\Http\Response
     */
    public function show_single_delivery_order($purchase_transport_id, $delivery_order_id)
    {
        $model = PurchaseTransport::findOrFail($purchase_transport_id);
        $data = DeliveryOrder::findOrFail($delivery_order_id);

        if ($model->vendor_id != Auth::user()->user_vendor->id) {
            abort(404);
        }

        if ($data->so_trading_id != $model->so_trading_id) {
            abort(404);
        }

        return view("transport.$this->view_folder.show-single-delivery-order", compact('model', 'data'));
    }

    /**
     * print
     *
     * @param $purchase_transport_id,
     * @param $delivery_order_id
     * @return mixed
     */
    public function print($purchase_transport_id, $delivery_order_id)
    {
        DB::beginTransaction();
        $model = PurchaseTransport::findOrFail($purchase_transport_id);
        $data = DeliveryOrder::findOrFail($delivery_order_id);

        if ($model->vendor_id != Auth::user()->user_vendor->id) {
            abort(404);
        }

        if ($data->so_trading_id != $model->so_trading_id) {
            abort(404);
        }

        $data->status_print = true;
        $data->save();

        $file = public_path('/pdf_reports/Report-Delivery-Order-' . microtime(true) . '.pdf');
        $fileName = 'Report-Delivery-Order-' . microtime(true) . '.pdf';

        $pdf = PDF::loadview("admin/.$this->view_folder./export", ['model' => $data])->setPaper('a4', 'portrait');

        DB::commit();
        return $pdf->download($fileName);
    }

    /**
     * edit single delivery prder
     *
     * @param $purchase_transport_id
     * @param $delivery_order_id
     * @return mixed
     */
    public function edit_single_delivery_order($purchase_transport_id, $delivery_order_id)
    {
        $model = PurchaseTransport::findOrFail($purchase_transport_id);
        $data = DeliveryOrder::findOrFail($delivery_order_id);

        if ($model->vendor_id != Auth::user()->user_vendor->id) {
            abort(404);
        }

        if ($data->so_trading_id != $model->so_trading_id) {
            abort(404);
        }

        return view("transport.$this->view_folder.edit-single-delivery-order", compact('model', 'data'));
    }

    /**
     * update single delivery order
     *
     * @param Request $request
     * @param $purchase_transport_id
     * @param $delivery_order_id
     * @return mixed
     */
    public function update_single_delivery_order(Request $request, $purchase_transport_id, $delivery_order_id)
    {
        $model = PurchaseTransport::findOrFail($purchase_transport_id);
        $data = DeliveryOrder::findOrFail($delivery_order_id);
        $data_detail = $data->delivery_order_detail;

        if ($model->vendor_id != Auth::user()->user_vendor->id) {
            abort(404);
        }

        if ($data->so_trading_id != $model->so_trading_id) {
            abort(404);
        }

        DB::beginTransaction();

        // * delivery order
        $data->loadModel([
            'load_date' => $request->load_date,
            'unload_date' => $request->unload_date,
            'load_quantity' => thousand_to_float($request->load_quantity ?? 0),
            'load_quantity_realization' => thousand_to_float($request->load_quantity_realization ?? 0),
            'unload_quantity' => thousand_to_float($request->unload_quantity ?? 0),
            'unload_quantity_realization' => thousand_to_float($request->unload_quantity_realization ?? 0),
            'description' => $request->description,
            'top_seal' => $request->top_seal,
            'bottom_seal' => $request->bottom_seal,
            'temperature' => $request->temperature,
            'initial_meter' => $request->initial_meter,
            'initial_final' => $request->initial_final,
            'sg_meter' => $request->sg_meter,
            'driver_name' => $request->driver_name,
            'driver_phone' => $request->driver_phone,
            'vehicle_information' => $request->vehicle_information,
        ]);

        // * if file delivery order detail
        if ($request->hasFile('file')) {
            try {
                $this->delete_file($data->file);
            } catch (\Throwable $th) {
                //throw $th;
            }
            $data->file = $this->upload_file($request->file('file'), 'delivery-order');
        }

        try {
            $data->save();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'delivery order', $th->getMessage()));
        }

        // * updaTe status waiting approval
        $data->status = 'submitted';
        try {
            $data->save();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'delivery order status', $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("transport.delivery-order.show.show-single-delivery-order", [$purchase_transport_id, $delivery_order_id])->with($this->ResponseMessageCRUD(true, 'update'));
    }
}
