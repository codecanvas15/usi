<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\InvoiceTrading as model;
use App\Models\InvoiceTrading;
use App\Models\InvTradingAddOnTax;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesReportByTradingPeriodController extends Controller
{
    public function get_data($type, $request, $multiperiod = false, $year = null)
    {
        $data = [];

        try {
            $invoiceTradings = InvoiceTrading::query()
                ->with(['customer', 'invoice_trading_details', 'invoice_trading_taxes'])
                ->whereHas('invoice_trading_details', function ($q) {
                    $q->whereHas('delivery_order', function ($q) {
                        $q->where('id', '=', DB::raw('invoice_trading_details.delivery_order_id'));
                    });
                })
                ->whereNull('deleted_at')
                ->whereBetween('date', [Carbon::parse($request->from_date), Carbon::parse($request->to_date)])
                ->get();

            foreach ($invoiceTradings as $invoiceTrading) {
                $hpp_transport = $invoiceTrading->invoice_trading_details->map(function ($item) {
                    $sended = $item->load_quantity_realization;
                    if ($item->delivery_order->type == 'delivery-order' && is_null($item->delivery_order->delivery_order_id)) {
                        $sended = $item->delivery_order->unload_quantity_realization;
                    }

                    $transport_price = $item->delivery_order->purchase_transport->harga ??0;

                    return $sended * $transport_price;
                })->sum();

                $transport_tax = $invoiceTrading->invoice_trading_details->map(function ($item) {
                    $sended = $item->load_quantity_realization;
                    if ($item->delivery_order->type == 'delivery-order' && is_null($item->delivery_order->delivery_order_id)) {
                        $sended = $item->delivery_order->unload_quantity_realization;
                    }

                    $transport_price = $item->delivery_order->purchase_transport->harga ?? 0;
                    $transport_tax_percent = $item->delivery_order->purchase_transport ? $item->delivery_order->purchase_transport->purchase_transport_taxes->sum('value') : 0;

                    return ($sended * $transport_price) * $transport_tax_percent;
                })->sum();

                // dd($item_sended);
                $invoice_trading_addon_taxes = InvTradingAddOnTax::whereHas('inv_trading_add_on', function ($q) use ($invoiceTrading) {
                    $q->whereHas('invoice_trading', function ($q) use ($invoiceTrading) {
                        $q->where('id', $invoiceTrading->id)
                            ->whereHas('invoice_trading_details', function ($q) {
                                $q->whereHas('delivery_order', function ($q) {
                                    $q->where('id', '=', DB::raw('invoice_trading_details.delivery_order_id'));
                                });
                            });
                    });
                })->get();
                
                $invoiceTrading->addon_taxes = $invoice_trading_addon_taxes;
                // $item_sended = $invoiceTrading->invoice_trading_details->load('delivery_order.load_quantity_realization');
                $invoiceTrading->hpp_transport = $hpp_transport;
                $invoiceTrading->transport_tax = $transport_tax;
                array_push($data, $invoiceTrading);
            }

            $throw_data['type'] = $type;
            $throw_data['data'] = $data;
            $throw_data['from_date'] = $request->from_date;
            $throw_data['to_date'] = $request->to_date;
            $throw_data['branch'] = Branch::find($request->branch_id);

            return $throw_data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
