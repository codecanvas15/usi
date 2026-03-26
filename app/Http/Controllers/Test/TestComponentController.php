<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Http\Helpers\JournalHelpers;
use App\Models\InvoiceGeneral;
use App\Models\ItemReceivingReport;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestComponentController extends Controller
{
    public function index()
    {
        return view('test.index');
    }

    public function test_component(Request $request)
    {
        return view('test.test-components');
    }

    public function repair_data_lpb_transport()
    {
        DB::beginTransaction();
        try {
            $item_receiving_report_transports = ItemReceivingReport::where('tipe', 'transport')->get();
            $item_receiving_report_transports->each(function ($item_receiving_report_transport) {
                $item_receiving_report_transport->observerAfterCreate();
            });

            DB::commit();
            return response()->json($item_receiving_report_transports);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function repair_data_lpb_general()
    {
        DB::beginTransaction();
        try {
            $item_receiving_reports = ItemReceivingReport::where('tipe', 'general')->get();
            $item_receiving_reports->each(function ($item_receiving_report) {
                $lpb_coa = new \App\Http\Helpers\ItemReceivingReportCoaHelpers($item_receiving_report->tipe, $item_receiving_report->reference_id, $item_receiving_report->id);
                $lpb_coa->create_item_receiving_report_coa();

                if ($item_receiving_report->status == 'approve' || $item_receiving_report->status == 'done') {
                    Journal::where('reference_id', $item_receiving_report->id)->where('reference_model', ItemReceivingReport::class)
                        ->delete();
                    $journal = new JournalHelpers($item_receiving_report->tipe, $item_receiving_report->id);
                    $journal->generate();
                }
            });

            $delivery_order_generals = \App\Models\DeliveryOrderGeneral::all();
            $delivery_order_generals->each(function ($delivery_order_general) {
                Journal::where('reference_id', $delivery_order_general->id)->where('reference_model', DeliveryOrderGeneral::class)
                    ->delete();

                // * CREATE journal
                $journal = new \App\Http\Helpers\JournalHelpers('delivery-order-general', $delivery_order_general->id);
                $journal->generate();
            });

            $invoice_generals = InvoiceGeneral::all();
            $invoice_generals->each(function ($invoice_general) {
                $invoiceCoa = new \App\Http\Helpers\InvoiceCoaHelpers($invoice_general->id, 'invoice-general');
                $invoiceCoa->generateCoaDataInvoiceGeneral();

                if ($invoice_general->status == 'approve' || $invoice_general->status == 'done') {
                    Journal::where('reference_id', $invoice_general->id)->where('reference_model', InvoiceGeneral::class)
                        ->delete();

                    $journal = new JournalHelpers('invoice-general', $invoice_general->id);
                    $journal->generate();
                }
            });

            DB::commit();
            return response()->json($item_receiving_reports);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
