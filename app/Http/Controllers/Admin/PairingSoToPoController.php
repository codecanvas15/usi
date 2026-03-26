<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PairingSoToPo;
use App\Models\PoTradingDetail;
use App\Models\SoTradingDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PairingSoToPoController extends Controller
{
    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'pairing-so-to-po';

    /**
     * sales order detail nor pairing completely
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function so_not_pairing_completely(Request $request)
    {
        /**
         * ! explanation query
         *
         * ==========================================================================
         * select *
         * if type == Kilo Liter ? jumlah * 1000 - sudah dialokasikan : jumlah - sudah dialokasikan as final_jumlah
         * having final_jumlah > sudah dialokasikan
         * ==========================================================================
         */
        $model = SoTradingDetail::select('*')
            ->where('jumlah', '>', 'sudah_dialokasikan')
            ->whereIn('status', ['pairing', 'partial'])
            ->when($request->from_date, fn($q) => $q->whereDate('created_at', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('created_at', '<=', Carbon::parse($request->to_date)))
            ->get();

        if ($request->ajax()) {
            return datatables()->of($model)
                ->addIndexColumn()
                ->addColumn('nomor_so', fn($row) => $row->so_trading->nomor_so)
                ->addColumn('item', fn($row) => $row->item->nama)
                ->addColumn('alokasi_tersedia', function ($row) {
                    $unit = $row->item->unit->name ?? '';
                    return formatNumber($row->alokasi_tersedia) . ' ' . $unit;
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.pairing.pairing', $row) . '" class="btn btn-primary btn-sm btn-pairing-so-to-po">Pairing</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.so-not-pairing-completely', compact('model'));
    }

    /**
     * available po trading for a so detail
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function available_po_for_a_so(Request $request, $id)
    {
        $so = SoTradingDetail::findOrFail($id);

        if (!in_array($so->status, ['pairing', 'partial'])) {
            return redirect()->back()->with('error', 'Sales Order tidak bisa di pairing');
        }

        // if alokasi sudah cukup
        if ($so->sudah_dialokasikan >= $so->jumlah) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'alokasi', 'alokasi sudah cukup');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'alokasi', 'alokasi sudah cukup'));
        }

        /**
         * ! explanation query
         *
         * ==========================================================================
         * * if po_trading_details.type is Kilo liter 1000 * jumlah as final_jumlah
         * * where po_trading_details.price_id == sale_order-details.price_id
         * * where po_trading_details.item_id == sale_order-details.item_id
         * * having po_trading_details.final_jumlah > sale_order-details.jumlah (is type kilo liter) jumlah * 1000
         * ==========================================================================
         */
        $model = PoTradingDetail::leftJoin('purchase_orders', 'purchase_orders.id', 'purchase_order_details.po_trading_id')
            ->select(['purchase_order_details.*'])
            ->with(['po_trading', 'item'])
            ->where('purchase_order_details.item_id', $so->item_id)
            ->where('purchase_order_details.price_id', $so->price_id)
            ->whereColumn('purchase_order_details.jumlah_lpbs', '>', 'purchase_order_details.sudah_dialokasikan')
            ->whereIn('purchase_orders.pairing_status', ['pairing', 'partial', 'ready'])
            ->whereNotNull('purchase_orders.sale_confirmation')
            ->orderByDesc('purchase_orders.created_at')
            ->get();

        // if request is ajax or from api
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        return view("admin.$this->view_folder.available-po-for-a-so", [
            'so' => $so,
            'model' => $model,
        ]);
    }

    /**
     * pairing so to po
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function pairing(Request $request, $id)
    {
        $so = SoTradingDetail::findOrFail($id);

        // if alokasi sudah cukup
        if ($so->sudah_dialokasikan >= $so->jumlah) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'alokasi', 'alokasi sudah cukup');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'alokasi', 'alokasi sudah cukup'));
        }

        $jumlah_so = $so->jumlah;
        $sudah_dialokasikan_so = $so->sudah_dialokasikan ?? 0;

        $validate = [
            'po_trading_detail_id.*' => 'required|exists:purchase_order_details,id',
            'alokasi.*' => 'required',
        ];

        // valiate
        if ($request->ajax()) {
            $this->validate_api($request->all(), $validate);
        } else {
            $this->validate($request, $validate);
        }

        DB::beginTransaction();

        $total = $sudah_dialokasikan_so;
        // creating data
        foreach ($request->alokasi as $key => $value) {
            $value_float = thousand_to_float($value);

            // * convert Kilo liter to Liter
            $po = PoTradingDetail::find($request->po_trading_detail_id[$key]);
            $jumlah_po = $po->type == 'Kilo Liter' ? $po->jumlah * 1000 : $po->jumlah;

            $total += $value_float;

            // check if total alokasi is greater than total alokasi
            if ($total > $jumlah_so) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(true, 'alokasi', 'Alokasi melebihi jumlah sales order tersedia', null, 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'pairing', 'Alokasi melebihi jumlah sales order tersedia.'));
            }

            // create data
            $pairing = new PairingSoToPo();
            $pairing->loadModel([
                'so_trading_detail_id' => $so->id,
                'po_trading_detail_id' => $request->po_trading_detail_id[$key],
                'alokasi' => $value_float,
            ]);

            try {
                $pairing->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(true, 'alokasi', null, $th->getMessage());
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'alokasi', null, $th->getMessage()));
            }

            // update so trading sudah  di alokasikan
            $so->sudah_dialokasikan += $value_float;
            try {
                $so->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(true, 'alokasi', null, $th->getMessage());
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'alokasi', null, $th->getMessage()));
            }

            // update po trading sudah di alokasikan
            $po->sudah_dialokasikan += $value_float;
            try {
                $po->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(true, 'alokasi', null, $th->getMessage());
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'alokasi', null, $th->getMessage()));
            }

            DB::commit();
        }

        return redirect()->route('admin.sales-order.show', $so->so_trading)->with($this->ResponseMessageCRUD(true, 'pairing', 'Pairing berhasil.'));
    }
}
