<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PairingSoToPo;
use App\Models\PoTradingDetail;
use App\Models\SoTradingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PairingPoToSoController extends Controller
{
    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'pairing-po-to-so';

    /**
     * sales order detail nor pairing completely
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function po_not_pairing_completely(Request $request)
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
        $model = PoTradingDetail::leftJoin('purchase_orders', 'purchase_orders.id', 'purchase_order_details.po_trading_id')
            ->select(['purchase_order_details.*'])
            ->whereColumn('purchase_order_details.jumlah_lpbs', '>', 'purchase_order_details.sudah_dialokasikan')
            ->whereIn('purchase_order_details.status', ['pairing', 'partial', 'ready'])
            ->whereNotNull('purchase_orders.sale_confirmation')
            ->whereNotNull('purchase_order_details.jumlah_lpbs')
            ->get();

        if ($request->ajax()) {
            return datatables()->of($model)
                ->addIndexColumn()
                ->addColumn('customer', fn($row) => $row->po_trading->customer->nama)
                ->addColumn('nomor_po', fn($row) => $row->po_trading->nomor_po)
                ->addColumn('item', fn($row) => $row->item->nama)
                ->addColumn('alokasi_tersedia', function ($row) {
                    $unit = $row->item->unit->name;
                    return formatNumber($row->alokasi_tersedia) . ' ' . $unit;
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.pairing.po_pairing', $row) . '" class="btn btn-primary btn-sm btn-pairing-so-to-po">Pairing</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.po-not-pairing-completely', compact('model'));
    }

    /**
     * available po trading for a po detail
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function available_so_for_a_po(Request $request, $id)
    {
        $po = PoTradingDetail::findOrFail($id);

        // if lpbs and sale confirmation null
        if ($po->po_trading->sale_confirmation == null) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'alokasi', 'sale confirmation masih kosong');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'alokasi', 'sale confirmation masih kosong'));
        }

        if ($po->alokasi_tersedia == 0) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'alokasi', 'kuantitas tersedia tidak cukup');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'alokasi', 'kuantitas tersedia tidak cukup'));
        }

        // if alokasi tidak cukup
        if ($po->sudah_dialokasikan >= $po->jumlah_lpbs or $po->jumlah_lpbs == null) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'alokasi', 'kuantitas tersedia tidak cukup.');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'alokasi', 'kuantitas tersedia tidak cukup.'));
        }

        /**
         * ! explanation query
         *
         * ==========================================================================
         * * select *
         * * where sale_order_details.item_id == po_trading_detail.item_id
         * * where sale_order_details.price_id == po_trading_detail.price_id
         * * having final_jumlah < sudah_dialokasikan
         * ==========================================================================
         */
        $model = SoTradingDetail::select('*')
            ->with(['so_trading'])
            ->where('item_id', $po->item_id)
            ->where('price_id', $po->price_id)
            ->whereRaw('jumlah > sudah_dialokasikan')
            ->orderByDesc('created_at')
            ->get();

        // if request is ajax or from api
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        return view("admin.$this->view_folder.available-so-for-a-po", [
            'po' => $po,
            'model' => $model,
        ]);
    }

    /**
     * select for pairing
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function select(Request $request, $id)
    {
        $po = PoTradingDetail::findOrFail($id);

        // if lpbs and sale confirmation null
        if ($po->po_trading->sale_confirmation == nullValue()) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'alokasi', 'sale confirmation masih kosong');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'alokasi', 'sale confirmation  masih kosong'));
        }

        /**
         * ! explanation query
         *
         * ==========================================================================
         * * join to so_tradings
         * * join to customers
         * * where sale_order_details.price_id == po_trading_details.price_id
         * * where sale_order_details.item_id == po_trading_details.item_id
         * ==========================================================================
         */
        $model = SoTradingDetail::leftJoin('sale_orders', 'sale_orders.id', 'sale_order_details.so_trading_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->select([
                'sale_order_details.*',
                DB::raw('concat(`sale_orders.nomor_so`, "-", `customers.nama`) as final_value '),
            ])
            ->with(['so_trading'])
            ->where('item_id', $po->item_id)
            ->where('price_id', $po->price_id)
            ->whereRaw('sale_order_details.jumlah < sale_order_details.sudah_dialokasikan')
            ->orderByDesc('sale_order_details.created_at')
            ->limit(10);

        // search customer and so trading kode
        if ($request->search) {
            $model->orWhere('customers.nama', 'like', '%' . $request->search . '%');
            $model->orWhere('sale_orders.nomor_so', 'like', '%' . $request->search . '%');
        }

        return $this->ResponseJsonData($model->get());
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
        $po = PoTradingDetail::findOrFail($id);

        // if lpbs and sale confirmation null
        if ($po->po_trading->sale_confirmation == null) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'alokasi', ' sale confirmation masih kosong');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'alokasi', ' sale confirmation masih kosong'));
        }

        // if alokasi sudah cukup
        if (($po->type == 'Kilo Liter' ? $po->sudah_dialokasikan * 1000 : $po->sudah_dialokasikan) >= $po->jumlah_lpbs) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'alokasi', 'kuantitas tersedia tidak cukup');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'alokasi', 'kuantitas tersedia tidak cukup'));
        }

        $jumlah_so = $po->type == 'Kilo Liter' ? $po->jumlah * 1000 : $po->jumlah;
        $sudah_dialokasikan_so = $po->sudah_dialokasikan ?? 0;

        $validate = [
            'so_trading_detail_id.*' => 'required|exists:sale_order_details,id',
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
            $so = SoTradingDetail::find($request->po_trading_detail_id[$key]);
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
                'po_trading_detail_id' => $po->id,
                'so_trading_detail_id' => $request->po_trading_detail_id[$key],
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

            // update po trading sudah di alokasikan
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

            DB::commit();
        }

        return redirect()->route('admin.purchase-order.show', $po->po_trading)->with($this->ResponseMessageCRUD(true, 'pairing', 'Pairing berhasil.'));
    }
}
