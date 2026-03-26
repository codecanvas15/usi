<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DailyRefreshStockJob;
use App\Models\Authorization;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Depreciation;
use App\Models\Journal;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{
    function menu()
    {
        DailyRefreshStockJob::dispatch();
        return view('menu');
    }

    public function regenerate_customer_code()
    {
        $customers = Customer::all();
        foreach ($customers as $customer) {
            $generateCode = generate_vendor_customer_code(strtoupper($customer->nama));
            $code = 'CUS-' . $generateCode . '-0001';

            $exist_customer = Customer::where('code', $code)->first();
            if ($exist_customer) {
                $replaceCode = explode('-', $exist_customer->code);
                $tmpNumber = sprintf('%04d', (int)$replaceCode[2] + 1);
                $code = 'CUS-' . $generateCode . '-' . $tmpNumber;
            }
            $customer->code = $code;
            $customer->save();
        }

        return response()->json($customers);
    }

    public function regenerate_vendor_code()
    {
        $vendors = Vendor::all();
        foreach ($vendors as $vendor) {
            $generateCode = generate_vendor_customer_code(strtoupper($vendor->nama));
            $code = 'VEN-' . $generateCode . '-0001';

            $exist_vendor = Vendor::where('code', $code)->first();
            if ($exist_vendor) {
                $replaceCode = explode('-', $exist_vendor->code);
                $tmpNumber = sprintf('%04d', (int)$replaceCode[2] + 1);
                $code = 'VEN-' . $generateCode . '-' . $tmpNumber;
            }
            $vendor->code = $code;
            $vendor->save();
        }

        return response()->json($vendors);
    }

    public function update_link(Request $request)
    {
        try {
            $authorizations = Authorization::all();
            foreach ($authorizations as $authorization) {
                $base_url = getBaseUrlFromLink($authorization->update_status_link);
                $local_base_url = url('/');

                $update_status_link = str_replace($base_url, $local_base_url, $authorization->update_status_link);
                $link = str_replace($base_url, $local_base_url, $authorization->link);

                $authorization->link = $link;
                $authorization->update_status_link = $update_status_link;
                $authorization->save();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update_journal_do_trading()
    {
        try {
            $journals = Journal::where('journal_type', 'Delivery Order Trading')
                ->selectRaw("id,reference_id,document_reference,reference")
                ->get();

            foreach ($journals as $j) {
                $journal = Journal::find($j->id);

                $trading_id = $j->reference_id;
                $trading_code = $j->document_reference['code'];
                $sale_id = $j->reference['id'];

                $journal->document_reference = [
                    'id' => $trading_id,
                    'model' => DeliveryOrder::class,
                    'code' => $trading_code,
                    'link' => route('admin.delivery-order.list-delivery-order.show', ['sale_order_id' => $sale_id, 'delivery_order_id' => $trading_id]),
                ];

                $journal->save();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
