<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\InvoiceCoaHelpers;
use App\Http\Helpers\JournalHelpers;
use App\Models\AccountPayable;
use App\Models\CashBond;
use App\Models\InvoiceGeneral;
use App\Models\InvoiceTrading;
use App\Models\Journal;
use App\Models\PurchaseReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GenerateJournalController extends Controller
{
    public function store(Request $request)
    {
        $type = $request->type;
        $class = $request->model;
        $id = $request->id;

        $model = $class::findOrFail($id);
        DB::beginTransaction();
        try {
            Journal::where('reference_model', $class)->where('reference_id', $id)->delete();

            switch ($type) {
                case 'item-receiving-report':
                    if ($model->total == 0) {
                        $model->rollbackObserverAfterCreate();
                        $model->observerAfterCreate();
                    }

                    $lpb_coa = new \App\Http\Helpers\ItemReceivingReportCoaHelpers($model->tipe, $model->reference_id, $model->id);
                    $lpb_coa->create_item_receiving_report_coa();

                    $journal = new JournalHelpers($model->tipe, $model->id);
                    $journal->generate();
                    break;

                case 'invoice-trading':
                    $coa_helper = new InvoiceCoaHelpers($id, 'invoice-trading');
                    $coa_helper->generateCoaDataForInvoiceTrading();

                    Journal::where('reference_model', InvoiceTrading::class)->where('reference_id', $id)->delete();

                    $journal = new JournalHelpers('invoice-trading', $model->id);
                    $journal->generate();
                    break;

                case 'invoice-general':
                    Journal::where('reference_model', InvoiceGeneral::class)->where('reference_id', $id)->delete();

                    $invoiceCoa = new \App\Http\Helpers\InvoiceCoaHelpers($model->id, 'invoice-general');
                    $invoiceCoa->generateCoaDataInvoiceGeneral();

                    $journal = new JournalHelpers('invoice-general', $model->id);
                    $journal->generate();

                    break;

                case 'cash-bond':
                    Journal::where('reference_model', CashBond::class)->where('reference_id', $id)->delete();

                    $journal = new JournalHelpers('cash-bond', $model->id);
                    $journal->generate();

                    break;

                case 'account-payable':
                    Journal::where('reference_model', AccountPayable::class)->where('reference_id', $id)->delete();

                    $journal = new JournalHelpers('account-payable', $model->id);
                    $journal->generate();

                    break;

                case 'purchase-return':
                    Journal::where('reference_model', PurchaseReturn::class)->where('reference_id', $id)->delete();

                    $journal = new JournalHelpers('purchase-return', $model->id);
                    $journal->generate();

                    break;

                default:
                    # code...
                    break;
            }

            DB::commit();

            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update'));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', $th->getMessage()));
        }
    }
}
