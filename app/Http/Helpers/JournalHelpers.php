<?php

namespace App\Http\Helpers;

use App\Jobs\JournalJob;
use App\Models\AccountPayable;
use App\Models\Amortization;
use App\Models\Asset;
use App\Models\CashAdvancedReturn;
use App\Models\CashAdvancePayment;
use App\Models\CashAdvancePaymentDetail;
use App\Models\CashAdvanceReceive;
use App\Models\CashAdvanceReceiveDetail;
use App\Models\CashBond;
use App\Models\CashBondReturn;
use App\Models\Customer;
use App\Models\CustomerCoa;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderGeneral;
use App\Models\DeliveryOrderGeneralDetail;
use App\Models\Depreciation;
use App\Models\Disposition;
use App\Models\FundSubmission;
use App\Models\IncomingPayment;
use App\Models\IncomingPaymentDetail;
use App\Models\InvoiceDownPayment;
use App\Models\InvoiceDownPaymentTax;
use App\Models\InvoiceGeneral;
use App\Models\InvoicePayment;
use App\Models\InvoiceReturn;
use App\Models\InvoiceReturnDetail;
use App\Models\InvoiceTax;
use App\Models\InvoiceTaxSummary;
use App\Models\InvoiceTrading;
use App\Models\InvoiceTradingCoa;
use App\Models\ItemCategoryCoa;
use App\Models\ItemReceivingReport;
use App\Models\ItemReceivingReportCoa;
use App\Models\ItemReceivingReportTax;
use App\Models\ItemTypeCoa;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\Lease;
use App\Models\OutgoingPayment;
use App\Models\OutgoingPaymentDetail;
use App\Models\PoTrading;
use App\Models\PurchaseOrderGeneral;
use App\Models\PurchaseOrderService;
use App\Models\PurchaseOrderServiceDetailItemTax;
use App\Models\PurchaseOrderTax;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\ReceivablesPayment;
use App\Models\PurchaseTransport;
use App\Models\PurchaseTransportTax;
use App\Models\SaleOrderGeneral;
use App\Models\SaleOrderGeneralDetail;
use App\Models\SoTrading;
use App\Models\StockMutation;
use App\Models\StockOpname;
use App\Models\StockUsage;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceGeneral;
use App\Models\SupplierInvoiceGeneralDetail;
use App\Models\SupplierInvoicePayment;
use App\Models\TaxReconciliation;
use App\Models\TaxReconciliationDetail;
use App\Models\Vendor;
use App\Models\VendorCoa;
use App\Models\WareHouse;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class JournalHelpers
{
    /**
     * type
     *
     * @var string|null
     */
    protected $type = null;

    /**
     *
     * $model_id
     * @var int|null
     */
    protected $model_id = null;

    protected $dispatch_now = false;


    /**
     * initial
     *
     * @param string|null $type
     * @param int|null $purchase_id
     * @param int|null $model_id
     * @return void
     */
    public function __construct(string $type, int $model_id, bool $dispatch_now = false)
    {
        $this->type = $type;
        $this->model_id = $model_id;
        $this->dispatch_now = $dispatch_now;
    }

    /**
     * generate journal
     *
     * @return void
     */
    public function generate(): void
    {
        if ($this->dispatch_now) {
            JournalJob::dispatchSync($this->type, $this->model_id);
            return;
        }
        JournalJob::dispatch($this->type, $this->model_id);
    }

    public function generate_now(): void
    {
        if ($this->type == 'trading') {
            $this->generate_trading_journal();
        } elseif ($this->type == 'general') {
            $this->generate_general_journal();
        } elseif ($this->type == 'jasa') {
            $this->generate_service_journal();
        } elseif ($this->type == 'transport') {
            $this->generate_purchase_transport_journal();
        } elseif ($this->type == 'invoice-trading') {
            $this->generate_invoice_trading_journal();
        } elseif ($this->type == 'invoice-general') {
            $this->generate_invoice_general_journal();
        } elseif ($this->type == 'delivery-order-trading') {
            $this->generate_delivery_order_trading_journal();
        } elseif ($this->type == 'delivery-order-general') {
            $this->generate_delivery_order_general_journal();
        } elseif ($this->type == 'cash-advance-return') {
            $this->generate_cash_advance_return_journal();
        } elseif ($this->type == 'cash-bond') {
            $this->generate_cash_bond_journal();
        } elseif ($this->type == 'cash-bond-return') {
            $this->generate_cash_bond_return_journal();
        } elseif ($this->type == 'stock-usage') {
            $this->generate_stock_usage_journal();
        } elseif ($this->type == 'stock-opname') {
            $this->generate_stock_opname_journal();
        } elseif ($this->type == 'delivery-order-general-losses') {
            $this->generate_delivery_order_general_losses_journal();
        } elseif ($this->type == 'supplier-invoice') {
            $this->generate_supplier_invoice_journal();
        } else if ($this->type == 'amortization') {
            $this->generate_amortization_journal();
        } else if ($this->type == 'depreciation') {
            $this->generate_depreciation_journal();
        } else if ($this->type == 'purchase-return') {
            $this->generate_purchase_return_journal();
        } else if ($this->type == 'invoice-return') {
            $this->generate_invoice_return_journal();
        } else if ($this->type == 'account-payable') {
            $this->generate_account_payable_journal();
        } else if ($this->type == 'cash-advance-payment') {
            $this->generate_cash_advance_payment_journal();
        } else if ($this->type == 'cash-advance-receive') {
            $this->generate_cash_advance_receive_journal();
        } else if ($this->type == 'disposition') {
            $this->generate_disposition_journal();
        } else if ($this->type == 'outgoing-payment') {
            $this->generate_outgoing_payment_journal();
        } else if ($this->type == 'receivables-payment') {
            $this->generate_receivables_payment_journal();
        } else if ($this->type == 'tax-reconciliation') {
            $this->generate_tax_reconciliation_journal();
        } else if ($this->type == 'incoming-payment') {
            $this->incoming_payment_journal();
        } else if ($this->type == 'supplier-invoice-general') {
            $this->generate_supplier_invoice_general_journal();
        } else {
            throw new Exception("Auto generate journal for journal $this->type not found", 1);
        }
    }

    /**
     * get property journalGeneralRules
     *
     * @param journalRules
     * @return array
     */
    protected static function getJournalPurchaseRules(): array
    {
        return [
            'coa_inventory' => 'debit',
            'coa_tax' => 'debit',
            'coa_vendor' => 'credit',
            'coa_purchase_discount' => 'credit',
            // 'coa_cash_different' => 'debit',
        ];
    }


    /**
     * get property JournalInvoiceRules
     *
     * @param JournalInvoiceRules
     * @return array
     */
    protected function getJournalInvoiceRules(): array
    {
        return [
            'coa_sale' => 'credit',
            'coa_tax' => 'credit',
            'coa_customer' => 'debit',
            'coa_sale_discount' => 'debit',
            'coa_revenue' => 'credit',
            // 'coa_cash_different' => 'debit',
        ];
    }

    /**
     * generate_general_journal
     *
     * @return void
     */
    public function generate_general_journal()
    {
        DB::beginTransaction();

        // get needed data
        $item_receiving_report = ItemReceivingReport::findOrFail($this->model_id);
        $purchase_general = PurchaseOrderGeneral::findOrFail($item_receiving_report->reference_id);

        $journal = Journal::where('reference_model', ItemReceivingReport::class)
            ->where('reference_id', $item_receiving_report->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $item_receiving_report->branch_id,
            'reference_id' => $item_receiving_report->id,
            'reference_model' => ItemReceivingReport::class,
            'vendor_id' => $item_receiving_report->vendor_id,
            'document_reference' => [
                'id' => $item_receiving_report->id,
                'model' => ItemReceivingReport::class,
                'code' => $item_receiving_report->kode,
                'link' => route('admin.item-receiving-report-general.show', $item_receiving_report->id),
            ],
            'reference' => [
                'id' => $purchase_general->id,
                'model' => PurchaseOrderGeneral::class,
                'code' => $purchase_general->code,
                'link' => route('admin.purchase-order-general.show', ['purchase_order_general' => $purchase_general->id]),
            ],
            'date' => $item_receiving_report->date_receive,
            'exchange_rate' => $purchase_general->exchange_rate,
            'currency_id' => $purchase_general->currency_id,
            'journal_type' => "Purchase Journal",
            'remark' => "LPB - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        $credit_total = 0;
        $debit_total = 0;

        /**
         * ! ==================================================================================================================
         * ! create journal details
         * ! ==================================================================================================================
         *
         */

        // * inventory and asset
        $total = 0;
        foreach ($purchase_general->purchaseOrderGeneralDetails->where('status', '!=', 'reject') as $purchase_order_general) {
            foreach ($purchase_order_general->purchase_order_general_detail_items as $purchase_order_general_detail) {
                // * find data coa
                $item_receiving_report_coa_inventory = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
                    ->where('reference_id', $purchase_order_general_detail->id)
                    ->where('reference_model', \App\Models\PurchaseOrderGeneralDetailItem::class)
                    ->where(function ($query) {
                        $query->orWhere('type', 'Inventory');
                        $query->orWhere('type', 'Asset');
                        $query->orWhere('type', 'biaya dibayar dimuka');
                        $query->orWhere('type', 'Expense');
                    })->first();

                // * find data item receiving report
                $item_receiving_report_detail = $item_receiving_report
                    ->item_receiving_report_details()
                    ->where('reference_id', $purchase_order_general_detail->id)
                    ->where('reference_model', \App\Models\PurchaseOrderGeneralDetailItem::class)
                    ->first();


                // * if data find
                if ($item_receiving_report_coa_inventory and $item_receiving_report_detail) {
                    // * if item receive greater than 0
                    if ($item_receiving_report_detail->jumlah_diterima > 0) {
                        // * calculate total
                        $total = ($item_receiving_report_detail->jumlah_diterima * $purchase_order_general_detail->price);

                        // * create journal detail
                        $journal->journal_details()->create([
                            'item_receiving_report_coa_id' => $item_receiving_report_coa_inventory->id,
                            'coa_id' => $item_receiving_report_coa_inventory->coa_id,
                            'debit' => $total,
                            'credit' => 0,
                            'remark' =>  "LPB - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} - {$purchase_order_general_detail->item->nama}",
                        ]);

                        // * make list total inventory and increase debit total
                        $debit_total += $total;
                    }
                }

                $total = 0;
            }
        }

        // * tax =========================================================================================================================================================
        foreach ($purchase_general->purchaseOrderGeneralDetails->where('status', '!=', 'reject') as $key => $purchase_order_general) {
            foreach ($purchase_order_general->purchase_order_general_detail_items as $purchase_order_general_detail) {
                foreach ($purchase_order_general_detail->purchase_order_general_detail_item_taxes as $purchase_order_general_detail_tax) {
                    // * find data coa
                    $item_receiving_report_coa_tax = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
                        ->where('reference_id', $purchase_order_general_detail_tax->id)
                        ->where('reference_model', \App\Models\PurchaseOrderGeneralDetailItemTax::class)
                        ->where('type', 'Tax')
                        ->first();

                    // * find data item receiving report
                    $item_receiving_report_detail = $item_receiving_report
                        ->item_receiving_report_details
                        ->where('reference_id', $purchase_order_general_detail->id)
                        ->where('reference_model', \App\Models\PurchaseOrderGeneralDetailItem::class)
                        ->first();

                    // * if data find
                    if ($item_receiving_report_coa_tax and $item_receiving_report_detail) {
                        // * if item receive greater than 0
                        if ($item_receiving_report_detail->jumlah_diterima > 0) {
                            // * calculate total
                            $total = ($item_receiving_report_detail->jumlah_diterima * $purchase_order_general_detail->price);
                            $total *= $purchase_order_general_detail_tax->value;

                            // * create journal detail
                            $journal->journal_details()->create([
                                'item_receiving_report_coa_id' => $item_receiving_report_coa_tax->id,
                                'coa_id' => $item_receiving_report_coa_tax->coa_id,
                                'debit' => $total,
                                'credit' => 0,
                                'remark' => "LPB - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} - {$purchase_order_general_detail_tax->tax->name}",
                            ]);

                            //  item receiving report tax
                            $dpp = ($item_receiving_report_detail->jumlah_diterima * ($purchase_order_general_detail->price * ($purchase_general->exchange_rate)));
                            $tax_value = $purchase_order_general_detail_tax->value;

                            // * increase debit total
                            $debit_total += $total;
                        }
                    }
                }
            }
        }
        // * / tax =========================================================================================================================================================

        // * vendor
        $item_receiving_report_coa_vendor = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
            ->where('reference_id', $purchase_general->vendor_id)
            ->where('reference_model', Vendor::class)
            ->where('type', 'Account Payable Coa')
            ->first();

        if ($item_receiving_report_coa_vendor) {
            $journal->journal_details()->create([
                'item_receiving_report_coa_id' => $item_receiving_report_coa_vendor->id,
                'coa_id' => $item_receiving_report_coa_vendor->coa_id,
                'debit' => 0,
                'credit' => $debit_total,
                'remark' => "LPB - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode}",
            ]);
            $credit_total += $debit_total;
        }

        /**
         * ! ==================================================================================================================
         * ! / end create journal details
         * ! ==================================================================================================================
         *
         */

        // * update total credit and debit
        $journal->update([
            'credit_total' => $credit_total,
            'debit_total' => $debit_total,
        ]);

        DB::commit();
    }

    /**
     * generate_service_journal
     *
     * @return void
     */
    public function generate_service_journal()
    {
        DB::beginTransaction();

        // get needed data
        $item_receiving_report = ItemReceivingReport::findOrFail($this->model_id);
        $purchase_service = PurchaseOrderService::findOrFail($item_receiving_report->reference_id);

        $journal = Journal::where('reference_model', ItemReceivingReport::class)
            ->where('reference_id', $item_receiving_report->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $item_receiving_report->branch_id,
            'reference_id' => $item_receiving_report->id,
            'reference_model' => ItemReceivingReport::class,
            'vendor_id' => $item_receiving_report->vendor_id,
            'document_reference' => [
                'id' => $item_receiving_report->id,
                'model' => ItemReceivingReport::class,
                'code' => $item_receiving_report->kode,
                'link' => route('admin.item-receiving-report-service.show', $item_receiving_report->id),
            ],
            'reference' => [
                'id' => $purchase_service->id,
                'model' => PurchaseOrderService::class,
                'code' => $purchase_service->code,
                'link' => route('admin.purchase-order-service.show', ['purchase_order_service' => $purchase_service->id]),
            ],
            'date' => $item_receiving_report->date_receive,
            'exchange_rate' => $purchase_service->exchange_rate,
            'currency_id' => $purchase_service->currency_id,
            'journal_type' => "Purchase Journal",
            'remark' => "BAST - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }


        $credit_total = 0;
        $debit_total = 0;

        /**
         * ! ==================================================================================================================
         * ! create journal details
         * ! ==================================================================================================================
         *
         */
        // * inventory and asset
        $total = 0;
        foreach ($purchase_service->purchaseOrderServiceDetails->where('status', '!=', 'reject') as $purchase_order_service) {
            foreach ($purchase_order_service->purchase_order_service_detail_items as $purchase_order_service_detail) {
                $item_receiving_report_coa_inventory = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
                    ->where('reference_id', $purchase_order_service_detail->id)
                    ->where('reference_model', \App\Models\PurchaseOrderServiceDetailItem::class)
                    ->where(function ($query) {
                        $query->orWhere('type', 'Expense');
                        $query->orWhere('type', 'biaya dibayar dimuka');
                    })->first();

                // * find data item receiving report
                $item_receiving_report_detail = $item_receiving_report
                    ->item_receiving_report_details()
                    ->where('reference_id', $purchase_order_service_detail->id)
                    ->where('reference_model', \App\Models\PurchaseOrderServiceDetailItem::class)
                    ->first();


                // * if data find
                if ($item_receiving_report_coa_inventory and $item_receiving_report_detail) {
                    // * if item receive greater than 0
                    if ($item_receiving_report_detail->jumlah_diterima > 0) {
                        // * calculate total
                        $total = ($item_receiving_report_detail->jumlah_diterima * $purchase_order_service_detail->price);

                        // * create journal detail
                        $journal->journal_details()->create([
                            'item_receiving_report_coa_id' => $item_receiving_report_coa_inventory->id,
                            'coa_id' => $item_receiving_report_coa_inventory->coa_id,
                            'debit' => $total,
                            'credit' => 0,
                            'remark' => "BAST - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} - {$purchase_order_service_detail->item->nama}",
                        ]);

                        // * make list total inventory and increase debit total
                        $debit_total += $total;
                    }
                }

                $total = 0;
            }
        }

        // * tax =========================================================================================================================================================
        foreach ($purchase_service->purchaseOrderServiceDetails->where('status', '!=', 'reject') as $purchase_order_service) {
            foreach ($purchase_order_service->purchase_order_service_detail_items as $purchase_order_service_detail_item) {
                foreach ($purchase_order_service_detail_item->purchase_order_service_detail_item_taxes as $key => $purchase_order_service_detail_tax) {
                    // * find data coa
                    $item_receiving_report_coa_tax = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
                        ->where('reference_id', $purchase_order_service_detail_tax->id)
                        ->where('reference_model', PurchaseOrderServiceDetailItemTax::class)
                        ->where('type', 'Tax')
                        ->first();

                    // * find data item receiving report
                    $item_receiving_report_detail = $item_receiving_report
                        ->item_receiving_report_details
                        ->where('reference_id', $purchase_order_service_detail_item->id)
                        ->where('reference_model', \App\Models\PurchaseOrderServiceDetailItem::class)
                        ->first();

                    // * if data find
                    if ($item_receiving_report_coa_tax and $item_receiving_report_detail) {
                        // * if item receive greater than 0
                        if ($item_receiving_report_detail->jumlah_diterima > 0) {
                            // * calculate total
                            $total = ($item_receiving_report_detail->jumlah_diterima * $purchase_order_service_detail_item->price);
                            $total *= $purchase_order_service_detail_tax->value;

                            // * create journal detail
                            $journal->journal_details()->create([
                                'item_receiving_report_coa_id' => $item_receiving_report_coa_tax->id,
                                'coa_id' => $item_receiving_report_coa_tax->coa_id,
                                'debit' => $total,
                                'credit' => 0,
                                'remark' => "BAST - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} - {$purchase_order_service_detail_tax->tax->name}",
                            ]);

                            // * increase debit total
                            $debit_total += $total;
                        }
                    }
                }
            }
        }
        // * / tax =========================================================================================================================================================

        // * vendor
        $item_receiving_report_coa_vendor = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
            ->where('reference_id', $purchase_service->vendor_id)
            ->where('reference_model', Vendor::class)
            ->where('type', 'Account Payable Coa')
            ->first();

        if ($item_receiving_report_coa_vendor) {
            $journal->journal_details()->create([
                'item_receiving_report_coa_id' => $item_receiving_report_coa_vendor->id,
                'coa_id' => $item_receiving_report_coa_vendor->coa_id,
                'debit' => 0,
                'credit' => $debit_total,
                'remark' => "BAST - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode}"
            ]);
            $credit_total += $debit_total;
        }


        /**
         * ! ==================================================================================================================
         * ! / end create journal details
         * ! ==================================================================================================================
         *
         */

        // * update total credit and debit
        $journal->update([
            'credit_total' => $credit_total,
            'debit_total' => $debit_total,
        ]);

        Db::commit();
    }

    /**
     * generate_trading_journal
     *
     * @return void
     */
    public function generate_trading_journal()
    {
        // // get needed data
        $item_receiving_report = ItemReceivingReport::findOrFail($this->model_id);

        $purchase_trading = PoTrading::findOrFail($item_receiving_report->reference_id);
        $purchase_trading_detail = \App\Models\PoTradingDetail::where('po_trading_id', $purchase_trading->id)->first();

        $item_receiving_report_po_trading = $item_receiving_report->item_receiving_report_po_trading;
        $sub_total = ($purchase_trading_detail->harga - $purchase_trading_detail->discount_per_liter) * $item_receiving_report_po_trading->liter_15;

        // ! create journal
        $journal = Journal::where('reference_model', ItemReceivingReport::class)
            ->where('reference_id', $item_receiving_report->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $item_receiving_report->branch_id,
            'reference_id' => $item_receiving_report->id,
            'reference_model' => ItemReceivingReport::class,
            'vendor_id' => $item_receiving_report->vendor_id,
            'document_reference' => [
                'id' => $item_receiving_report->id,
                'model' => ItemReceivingReport::class,
                'code' => $item_receiving_report->kode,
                'link' => route('admin.item-receiving-report-trading.show', $item_receiving_report->id),
            ],
            'reference' => [
                'id' => $purchase_trading->id,
                'model' => PoTrading::class,
                'code' => $purchase_trading->nomor_po,
                'link' => route('admin.purchase-order.show', ['purchase_order' => $purchase_trading->id]),
            ],
            'date' => $item_receiving_report->date_receive,
            'exchange_rate' => $purchase_trading->exchange_rate,
            'currency_id' => $purchase_trading->currency_id,
            'journal_type' => "Purchase Journal",
            'remark' => "LPB - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        $credit_total = 0;
        $debit_total = 0;

        /**
         * ! ==================================================================================================================
         * ! create journal details
         * ! ==================================================================================================================
         *
         */

        $JOURNAL_DETAILS_DATA = [];

        // * inventory
        $item_receiving_report_coa_inventory = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
            ->where('reference_id', $purchase_trading->id)
            ->where('reference_model', PoTrading::class)
            ->where('type', 'inventory')
            ->first();

        $JOURNAL_DETAILS_DATA[] = [
            'coa_id' => $item_receiving_report_coa_inventory->coa_id,
            'debit' => $sub_total,
            'credit' => 0,
            'remark' => "LPB - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} - {$item_receiving_report->item_receiving_report_po_trading->item->nama}",
        ];

        $debit_total += $sub_total;

        // * tax
        foreach ($purchase_trading->purchase_order_taxes as $key => $value) {
            $item_receiving_report_coa_taxes = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
                ->where('reference_id', $value->id)
                ->where('reference_model', PurchaseOrderTax::class)
                ->where('type', 'TAX')
                ->get();

            foreach ($item_receiving_report_coa_taxes as $key => $tax_value) {
                $data = $tax_value->reference;

                if (is_null($data->tax_trading_id)) {
                    $tax_total = ($purchase_trading_detail->harga) * $item_receiving_report_po_trading->liter_15 * $data->value;

                    $JOURNAL_DETAILS_DATA[] = [
                        'coa_id' => $tax_value->coa_id,
                        'debit' => $tax_total,
                        'credit' => 0,
                        'remark' => "LPB - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} - {$data->tax->name}"
                    ];

                    $debit_total += $tax_total;
                } else {
                    $tax_total = (($purchase_trading_detail->harga - $purchase_trading_detail->discount_per_liter) * $item_receiving_report_po_trading->liter_15) * $data->value;

                    $JOURNAL_DETAILS_DATA[] = [
                        'coa_id' => $tax_value->coa_id,
                        'debit' => $tax_total,
                        'credit' => 0,
                        'remark' => "LPB - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} - {$data->tax_trading->name}"
                    ];

                    $dpp = (($purchase_trading_detail->harga - $purchase_trading_detail->discount_per_liter) * $purchase_trading->exchange_rate) * $item_receiving_report_po_trading->liter_15;

                    $debit_total += $tax_total;
                }
            }
        }

        // additional item
        foreach ($item_receiving_report->item_receiving_po_trading_additionals as $key => $additional) {
            $debit_total += $additional->total;
            $po_additional_item = $additional->purchase_order_additional_items;
            $po_additional_taxes = $po_additional_item->purchase_order_additional_taxes;

            $item_category_coa = ItemCategoryCoa::where('item_category_id', $po_additional_item->item->item_category_id)
                ->whereRaw('LOWER(type) = ?', ['expense'])
                ->first();

            $JOURNAL_DETAILS_DATA[] = [
                'coa_id' => $item_category_coa->coa_id,
                'credit' => 0,
                'debit' => $additional->subtotal,
                'remark' => "LPB - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} - {$additional->purchase_order_additional_items->item->nama}",
            ];

            foreach ($po_additional_taxes as $key => $po_additional_tax) {
                $JOURNAL_DETAILS_DATA[] = [
                    'coa_id' => $po_additional_tax->tax->coa_purchase,
                    'credit' => 0,
                    'debit' => $po_additional_tax->value * $additional->subtotal,
                    'remark' => "LPB - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} {$po_additional_tax->tax->name}",
                ];
            }
        }

        // * vendor
        $item_receiving_report_coa_vendor = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
            ->where('reference_id', $purchase_trading->vendor_id)
            ->where('reference_model', Vendor::class)
            ->where('type', 'Account Payable Coa')
            ->first();

        $JOURNAL_DETAILS_DATA[] = [
            'item_receiving_report_coa_id' => $item_receiving_report_coa_vendor->id,
            'coa_id' => $item_receiving_report_coa_vendor->coa_id,
            'debit' => 0,
            'credit' => $debit_total,
            'remark' => "LPB - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} {$purchase_trading->vendor->nama}"
        ];

        $credit_total += $debit_total;

        try {
            $journal->journal_details()->createMany($JOURNAL_DETAILS_DATA);
        } catch (\Throwable $th) {
            throw $th;
        }

        /**
         * ! ==================================================================================================================
         * ! / end create journal details
         * ! ==================================================================================================================
         *  */

        // * update total credit and debit
        $journal->update([
            'credit_total' => $credit_total,
            'debit_total' => $debit_total,
        ]);
    }

    /**
     * generate_purchase_transport_journal
     *
     * @return void
     */
    public function generate_purchase_transport_journal()
    {
        $item_receiving_report = ItemReceivingReport::findOrFail($this->model_id);
        $purchase_transport = PurchaseTransport::find($item_receiving_report->reference_id);
        $item_receive_report_purchase_transport = $item_receiving_report->item_receiving_report_purchase_transport;

        // * calculate
        $calculation_result = [
            'price' => $item_receive_report_purchase_transport->harga,
            'quantity' => $item_receive_report_purchase_transport->sended,
            'subtotal' => $item_receiving_report->sub_total,
        ];

        // * create journal
        $journal = Journal::where('reference_model', ItemReceivingReport::class)
            ->where('reference_id', $item_receiving_report->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $item_receiving_report->branch_id,
            'reference_id' => $item_receiving_report->id,
            'reference_model' => ItemReceivingReport::class,
            'vendor_id' => $item_receiving_report->vendor_id,
            'document_reference' => [
                'id' => $item_receiving_report->id,
                'model' => ItemReceivingReport::class,
                'code' => $item_receiving_report->kode,
                'link' => route('admin.item-receiving-report-transport.show', $item_receiving_report->id),
            ],
            'reference' => [
                'id' => $purchase_transport->id,
                'model' => PurchaseTransport::class,
                'code' => $purchase_transport->kode,
                'link' => route('admin.purchase-order-transport.show', ['purchase_order_transport' => $purchase_transport->id]),
            ],
            'date' => $item_receiving_report->date_receive,
            'exchange_rate' => $purchase_transport->exchange_rate,
            'currency_id' => $purchase_transport->currency_id,
            'journal_type' => "Purchase Journal",
            'remark' => "BASTTP - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        /**
         * ! ==================================================================================================================
         * ! create journal details
         * ! ==================================================================================================================
         *  */


        // * create journal details
        $debit_total = 0;
        $credit_total = 0;
        $calculation_subtotal = $calculation_result['subtotal'];

        // * item
        $item_receiving_report_coa_item = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
            ->where('reference_id', $purchase_transport->id)
            ->where('reference_model', PurchaseTransport::class)
            ->whereIn('type', ['Inventory', 'Expense'])
            ->first();

        if (!$item_receiving_report_coa_item) {
            throw new Exception("Item Coa not found");
        }
        $journal->journal_details()->create([
            'item_receiving_report_coa_id' => $item_receiving_report_coa_item->id,
            'coa_id' => $item_receiving_report_coa_item->coa_id,
            'debit' => $calculation_subtotal,
            'credit' => 0,
            'remark' => "BASTTP - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} - {$purchase_transport->item->nama}"
        ]);

        $debit_total += $calculation_subtotal;

        // * tax
        $item_receiving_report_coa_tax = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
            // ->where('reference_id', $purchase_transport->id)
            ->where('reference_model', PurchaseTransportTax::class)
            ->where('type', 'Tax')
            ->get();

        foreach ($item_receiving_report_coa_tax as $key => $value) {
            $data = $value->reference;
            $total = $calculation_subtotal;
            if ($item_receiving_report->item_receiving_report_purchase_transport->tax_option == 'full') {
                $total -= $item_receiving_report->item_receiving_report_purchase_transport->lost_discount ?? 0;
            } else if ($item_receiving_report->item_receiving_report_purchase_transport->tax_option == 'by_po') {
                $total = $item_receiving_report->item_receiving_report_purchase_transport->subtotal_by_po;
            }

            $total = $total * $data->value;

            $journal->journal_details()->create([
                'item_receiving_report_coa_id' => $value->id,
                'coa_id' => $value->coa_id,
                'debit' => $total,
                'credit' => 0,
                'remark' => "BASTTP - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode} - {$data->tax->name}"
            ]);

            $debit_total += $total;
        }

        // * vendor
        $credit_total += $debit_total;
        $item_receiving_report_coa_vendor = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receiving_report->id)
            ->where('reference_id', $purchase_transport->vendor_id)
            ->where('reference_model', Vendor::class)
            ->where('type', 'Account Payable Coa')
            ->first();

        if (!$item_receiving_report_coa_vendor) {
            throw new Exception("Vendor Coa not found");
        }

        $journal->journal_details()->create([
            'item_receiving_report_coa_id' => $item_receiving_report_coa_vendor->id,
            'coa_id' => $item_receiving_report_coa_vendor->coa_id,
            'debit' => 0,
            'credit' => $debit_total,
            'remark' => "BASTTP - {$item_receiving_report->vendor->nama} - {$item_receiving_report->kode}"
        ]);

        /**
         * ! ==================================================================================================================
         * ! / end create journal details
         * ! ==================================================================================================================
         *  */

        // * update total credit and debit
        $journal->update([
            'credit_total' => $credit_total,
            'debit_total' => $debit_total,
        ]);
    }

    /**
     * generate_invoice_trading_journal
     *
     * @return void
     */
    public function generate_invoice_trading_journal()
    {
        // * get needed data
        $invoice_trading = InvoiceTrading::findOrFail($this->model_id);
        $sale_order = SoTrading::find($invoice_trading->so_trading_id);
        $customer_deposit_coa = CustomerCoa::where('customer_id', $sale_order->customer_id)
            ->where('tipe', 'Customer Deposite Coa')
            ->first();

        $customer_receivable_coa = CustomerCoa::where('customer_id', $sale_order->customer_id)
            ->where('tipe', 'Account Receivable Coa')
            ->first();

        // * calculate
        $calculation_result = [
            'price' => $invoice_trading->harga,
            'quantity' => $invoice_trading->jumlah,
            'subtotal' => $invoice_trading->subtotal ?? $invoice_trading->harga * $invoice_trading->jumlah,
            'total' => $invoice_trading->total
        ];

        // * create journal
        $journal = Journal::where('reference_model', InvoiceTrading::class)
            ->where('reference_id', $invoice_trading->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }


        $default_remark = "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode}";

        $journal->loadModel([
            'branch_id' => $invoice_trading->branch_id,
            'reference_id' => $invoice_trading->id,
            'reference_model' => InvoiceTrading::class,
            'customer_id' => $invoice_trading->customer_id,
            'document_reference' => [
                'id' => $invoice_trading->id,
                'model' => InvoiceTrading::class,
                'code' => $invoice_trading->kode,
                'link' => route('admin.invoice-trading.show', ['invoice_trading' => $invoice_trading->id]),
            ],
            'reference' => [
                'id' => $sale_order->id,
                'model' => SoTrading::class,
                'code' => $sale_order->nomor_so,
                'link' => route('admin.purchase-order-service.show', ['purchase_order_service' => $sale_order->id]),
            ],
            'date' => $invoice_trading->date,
            'exchange_rate' => $sale_order->exchange_rate,
            'currency_id' => $sale_order->currency_id,
            'journal_type' => "Sale Journal",
            'remark' => "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        /**
         * ! ==================================================================================================================
         * ! create journal details
         * ! ==================================================================================================================
         *
         */

        // ? TRADING ITEM ################################################################

        // * sales or trading item
        $sales_invoice_coas = InvoiceTradingCoa::where('invoice_trading_id', $this->model_id)
            ->where('type', 'sales')
            ->get();


        $journal_details_data = [];
        foreach ($sales_invoice_coas as $key => $value) {
            $data = $value->invoice_trading;
            $total = $calculation_result['subtotal'];

            $journal_details_data[] = [
                'reference_id' => $data->id,
                'reference_model' => InvoiceTradingCoa::class,
                'coa_id' => $value->coa_id,
                'debit' => 0,
                'credit' => $total,
                'remark' => "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode} - {$data->item->nama}"
            ];
        }

        // * trading item tax
        $tax_invoice_coas = InvoiceTradingCoa::where('invoice_trading_id', $this->model_id)
            ->where('type', 'trading-tax')
            ->get();

        foreach ($tax_invoice_coas as $key => $value) {
            $data = $value->reference;
            $tax_value = $data->value * 100;

            $total = $calculation_result['subtotal'];
            $total = $total * $data->value;

            $journal_details_data[] = [
                'invoice_trading_coa_id' => $value->id,
                'coa_id' => $value->coa_id,
                'debit' => 0,
                'credit' => $total,
                'remark' => "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode} - {$data->tax->name}"
            ];
        }

        $invoice_tax_summary = InvoiceTaxSummary::where('model_class', InvoiceTrading::class)
            ->where('model_id', $invoice_trading->id)
            ->get();

        $down_payment_invoice = $invoice_trading->invoice_parent()->down_payment_invoices;

        $down_payment_taxes = InvoiceDownPaymentTax::whereHas('invoice_down_payment', function ($q) use ($down_payment_invoice) {
            $q->whereIn('id', $down_payment_invoice->pluck('invoice_down_payment_id')->toArray());
        })
            ->get();

        $invoice_tax_summary = $invoice_tax_summary->map(function ($item) use ($down_payment_taxes) {
            $item->final_amount = $item->tax_amount - $down_payment_taxes->where('tax_id', $item->tax_id)
                ->where('value', $item->tax_value)
                ->sum('amount');

            return $item;
        })
            ->filter(function ($item) {
                return $item->final_amount > 0;
            });

        foreach ($invoice_tax_summary as $key => $value) {
            $dpp = $value->final_amount / $value->tax_value;

            if ($value->tax->type == 'ppn') {
                InvoiceTax::where('reference_parent_id', $invoice_trading->id)
                    ->where('reference_parent_model', InvoiceTrading::class)
                    ->delete();

                $invoice_tax = new InvoiceTax();
                $invoice_tax->loadModel(
                    [
                        'reference_model' => InvoiceTrading::class,
                        'reference_id' => $invoice_trading->id,
                        'reference_parent_model' => InvoiceTrading::class,
                        'reference_parent_id' => $invoice_trading->id,
                        'date' => Carbon::parse($invoice_trading->date),
                        'customer_id' => $invoice_trading->customer_id,
                        'tax_id' => $value->tax_id,
                        'dpp' => ($dpp * $invoice_trading->exchange_rate),
                        'value' => $value->tax_value,
                        'amount' => ($value->final_amount * $invoice_trading->exchange_rate),
                    ]
                );
                $invoice_tax->save();
            }
        }

        // ? END TRADING ITEM ################################################################

        // ? ADDITIONAL ITEM ###############################################################

        // * additional item
        $additional_invoice_coas = InvoiceTradingCoa::where('invoice_trading_id', $this->model_id)
            ->where('type', 'item-additional')
            ->get();

        foreach ($additional_invoice_coas as $key => $value) {
            $data = $value->reference;
            $total = $data->sub_total;

            $journal_details_data[] = [
                'invoice_trading_coa_id' => $value->id,
                'coa_id' => $value->coa_id,
                'debit' => 0,
                'credit' => $total,
                'remark' => "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode} - {$data?->item?->nama}"
            ];
        }

        // * additional tax
        $additional_tax_invoice_coas = InvoiceTradingCoa::where('invoice_trading_id', $this->model_id)
            ->where('type', 'item-additional-tax')
            ->get();

        foreach ($additional_tax_invoice_coas as $key => $value) {
            $data = $value->reference;
            $tax_value = $data->value * 100;

            $total = $data?->inv_trading_add_on?->sub_total ?? $calculation_result['subtotal'];
            $total = $total * $data->value;

            $journal_details_data[] = [
                'invoice_trading_coa_id' => $value->id,
                'coa_id' => $value->coa_id,
                'debit' => 0,
                'credit' => $total,
                'remark' => "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode} - {$data->tax->name}"
            ];
        }

        // ? END ADDITIONAL ITEM ###############################################################

        // ? CUSTOMER ################################################################

        // * customer
        $customer_invoice_coas = InvoiceTradingCoa::where('invoice_trading_id', $this->model_id)
            ->where('type', 'customer')
            ->get();

        foreach ($customer_invoice_coas as $key => $value) {
            $data = $value->reference;
            $total = $calculation_result['total'];

            $journal_details_data[] = [
                'invoice_trading_coa_id' => $value->id,
                'coa_id' => $value->coa_id,
                'debit' => $total,
                'credit' => 0,
                'remark' => "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode} - {$data->nama}"
            ];
        }


        $delivery_orders = DeliveryOrder::whereIn('id', $invoice_trading->invoice_trading_details->pluck('delivery_order_id'))
            ->get();

        $double_handling = DeliveryOrder::whereIn('id', $delivery_orders->pluck('delivery_order_id'))
            ->get();
        $data_deliveries = $delivery_orders->where('type', 'delivery-order')
            ->where('delivery_order_id', null);

        $data_deliveries = $data_deliveries->merge($double_handling);

        $delivery_journals = JournalDetail::with('journal')
            ->whereHas('journal', function ($q) use ($data_deliveries) {
                $q->where('reference_model', DeliveryOrder::class)
                    ->whereIn('reference_id', $data_deliveries->pluck('id')->toArray());
            })
            ->get();

        foreach ($data_deliveries as $key => $delivery) {
            $so_trading_detail = $delivery->so_trading->so_trading_detail;
            $coa_goods_in_transit = $so_trading_detail->item->item_category->item_category_coas
                ->filter(function ($query) {
                    return strtolower($query->type) == 'goods_in_transit';
                })
                ->first()
                ->coa ?? null;

            $hpp_coa = $so_trading_detail->item->item_category->item_category_coas
                ->filter(function ($query) {
                    return strtolower($query->type) == 'hpp';
                })
                ->first()
                ->coa ?? null;

            $delivery_journal = $delivery_journals->where('journal.reference_id', $delivery->id)
                ->where('coa_id', $coa_goods_in_transit->id ?? null)
                ->first();

            if ($delivery_journal) {
                $journal_details_data[] = [
                    'coa_id' => $coa_goods_in_transit->id,
                    'debit' => 0,
                    'credit' => $delivery_journal->debit,
                    'remark' => "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode} - {$so_trading_detail->item->nama}"
                ];

                $journal_details_data[] = [
                    'coa_id' => $hpp_coa->id,
                    'debit' => $delivery_journal->debit,
                    'credit' => 0,
                    'remark' => "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode} - {$so_trading_detail->item->nama}"
                ];
            }
        }

        // down payment
        foreach ($invoice_trading->invoice_parent()->down_payment_invoices as $key => $down_payment_invoice) {
            $invoice_down_payment = $down_payment_invoice->invoice_down_payment;
            // PIUTANG
            $journal_details_data[] = [
                'coa_id' => $customer_receivable_coa->coa_id,
                'debit' => 0,
                'credit' => $down_payment_invoice->invoice_down_payment->grand_total,
                'remark' => "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode}"
            ];

            // UANG MUKA
            $journal_details_data[] = [
                'exchange_rate' => $invoice_down_payment->exchange_rate,
                'coa_id' => $customer_deposit_coa->coa_id,
                'debit' => $down_payment_invoice->invoice_down_payment->down_payment,
                'credit' => 0,
                'remark' => "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode}"
            ];

            foreach ($down_payment_invoice->invoice_down_payment->invoice_down_payment_taxes as $key => $invoice_down_payment_tax) {
                $journal_details_data = [
                    'exchange_rate' => $invoice_down_payment->exchange_rate,
                    'coa_id' => $invoice_down_payment_tax->tax->coa_sale_data->id,
                    'debit' => $invoice_down_payment_tax->amount,
                    'credit' => 0,
                    'remark' => "INVOICE - {$invoice_trading->customer->nama} - {$invoice_trading->kode} - {$invoice_down_payment_tax->tax->tax_name_with_percent}"
                ];
            }

            // exchange rate gap
            $rate_gap = ($invoice_trading->exchange_rate - $invoice_down_payment->exchange_rate) * ($invoice_down_payment->debit ?? 0);
            if ($rate_gap != 0) {
                $exchange_rate_gap_coa = get_default_coa('finance', 'Exchange Rate Gap')->coa_id;
                $journal_details_data[] = [
                    'currency_id' => get_local_currency()->id,
                    'exchange_rate' => 1,
                    'reference_id' => $down_payment_invoice->invoice_down_payment->id,
                    'reference_model' => get_class($down_payment_invoice),
                    'coa_id' => $exchange_rate_gap_coa,
                    'debit' => $rate_gap,
                    'remark' => $default_remark,
                ];
            }


            InvoicePayment::updateOrCreate([
                'invoice_model' => InvoiceTrading::class,
                'invoice_id' => $invoice_trading->id,
                'model' => InvoiceDownPayment::class,
                'reference_id' => $down_payment_invoice->invoice_down_payment->id
            ], [
                'invoice_model' => InvoiceTrading::class,
                'invoice_id' => $invoice_trading->id,
                'model' => InvoiceDownPayment::class,
                'reference_id' => $down_payment_invoice->invoice_down_payment->id,

                'currency_id' => $down_payment_invoice->invoice_down_payment->currency_id,
                'exchange_rate' => $invoice_trading->exchange_rate,
                'date' => $invoice_trading->date,
                'amount_to_receive' => 0,
                'receive_amount' => $down_payment_invoice->invoice_down_payment->grand_total,
                'note' => $down_payment_invoice->invoice_down_payment->note,
            ]);
        }

        // ? END CUSTOMER ################################################################

        /**
         * ! ==================================================================================================================
         * ! / end create journal details
         * ! ==================================================================================================================
         *  */

        $journal->journal_details()->createMany($journal_details_data);

        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details->sum('credit'),
            'debit_total' => $journal->journal_details->sum('debit'),
        ]);
    }

    /**
     * generate invoice trading journal
     *
     * @return void
     */
    public function generate_invoice_general_journal()
    {
        // * get needed data
        $invoice_general = \App\Models\InvoiceGeneral::with(['invoice_general_details.invoice_general_detail_taxes', 'invoice_general_additionals.invoice_general_additional_taxes'])->find($this->model_id);
        $customer_deposit_coa = CustomerCoa::where('customer_id', $invoice_general->customer_id)
            ->where('tipe', 'Customer Deposite Coa')
            ->first();

        $customer_receivable_coa = CustomerCoa::where('customer_id', $invoice_general->customer_id)
            ->where('tipe', 'Account Receivable Coa')
            ->first();

        // * create journal
        $journal = Journal::where('reference_model', \App\Models\InvoiceGeneral::class)
            ->where('reference_id', $invoice_general->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $default_remark = "Invoice - $invoice_general->code";

        $journal->loadModel([
            'branch_id' => $invoice_general->branch_id,
            'reference_id' => $invoice_general->id,
            'reference_model' => \App\Models\InvoiceGeneral::class,
            'customer_id' => $invoice_general->customer_id,
            'document_reference' => [
                'id' => $invoice_general->id,
                'model' => InvoiceGeneral::class,
                'code' => $invoice_general->code,
                'link' => route('admin.invoice-general.show', ['invoice_general' => $invoice_general->id]),
            ],
            'reference' => [
                'id' => $invoice_general->id,
                'model' => InvoiceGeneral::class,
                'code' => $invoice_general->code,
                'link' => route('admin.invoice-general.show', ['invoice_general' => $invoice_general->id]),
            ],
            'date' => $invoice_general->date,
            'exchange_rate' => $invoice_general->exchange_rate,
            'currency_id' => $invoice_general->currency_id,
            'journal_type' => "Sale Journal",
            'remark' => "INVOICE - {$invoice_general->customer->nama} - {$invoice_general->code}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw new Exception("Failed to create journal for invoice trading $invoice_general->code " . $th->getMessage(), 1);
        }

        /**
         * ! ==================================================================================================================
         * ! create journal details
         * ! ==================================================================================================================
         *
         */

        // ? MAIN ITEM ###############################################################

        // * sales coa
        $sae_coas = \App\Models\InvoiceGeneralCoa::where('invoice_general_id', $this->model_id)
            ->where('reference_model', \App\Models\InvoiceGeneralDetail::class)
            ->where('type', 'sales')
            ->get();

        foreach ($sae_coas as $sale_key => $sale) {
            $reference_subtotal = $sale->reference->sub_total;

            $journal->journal_details()->create([
                'reference_id' => $sale->id,
                'reference_model' => \App\Models\InvoiceGeneralCoa::class,
                'coa_id' => $sale->coa_id,
                'debit' => 0,
                'credit' => $reference_subtotal,
                'remark' => "INVOICE - {$invoice_general->customer->nama} - {$invoice_general->code} - {$sale->reference->item->nama}"
            ]);
        }
        // ? END MAIN ITEM ###########################################################

        // ? ADDITIONAL ITEM #########################################################

        // * item coa
        $additional_coas = \App\Models\InvoiceGeneralCoa::where('invoice_general_id', $this->model_id)
            ->where('type', 'item-additional')
            ->get();

        foreach ($additional_coas as $additional_key => $additional) {
            $additional_reference_subtotal = $additional->reference->sub_total;

            $journal->journal_details()->create([
                'reference_id' => $additional->id,
                'reference_model' => \App\Models\InvoiceGeneralCoa::class,
                'coa_id' => $additional->coa_id,
                'debit' => 0,
                'credit' => $additional_reference_subtotal,
                'remark' => "INVOICE - {$invoice_general->customer->nama} - {$invoice_general->code} - {$additional->reference->item->nama}"
            ]);
        }

        // ? END ADDITIONAL ITEM #####################################################

        // ? CUSTOMER ################################################################
        $customer_coa = \App\Models\InvoiceGeneralCoa::where('invoice_general_id', $this->model_id)
            ->where('type', 'customer')
            ->first();

        $invoice_general_total = $invoice_general->total;

        $journal->journal_details()->create([
            'reference_id' => $customer_coa->id,
            'reference_model' => \App\Models\InvoiceGeneralCoa::class,
            'coa_id' => $customer_coa->coa_id,
            'debit' => $invoice_general_total,
            'credit' => 0,
            'remark' => "INVOICE - {$invoice_general->customer->nama} - {$invoice_general->code}"
        ]);
        // ? END CUSTOMER ############################################################

        $invoice_tax_summary = InvoiceTaxSummary::where('model_class', InvoiceGeneral::class)
            ->where('model_id', $invoice_general->id)
            ->get();

        $down_payment_invoice = $invoice_general->invoice_parent()->down_payment_invoices;

        $down_payment_taxes = InvoiceDownPaymentTax::whereHas('invoice_down_payment', function ($q) use ($down_payment_invoice) {
            $q->whereIn('id', $down_payment_invoice->pluck('invoice_down_payment_id')->toArray());
        })
            ->get();

        $invoice_tax_summary = $invoice_tax_summary->map(function ($item) use ($down_payment_taxes) {
            $item->final_amount = $item->tax_amount - $down_payment_taxes->where('tax_id', $item->tax_id)
                ->where('value', $item->tax_value)
                ->sum('amount');

            return $item;
        })
            ->filter(function ($item) {
                return $item->final_amount > 0;
            });;

        foreach ($invoice_tax_summary as $key => $value) {
            $journal->journal_details()->create([
                'invoice_trading_coa_id' => $value->id,
                'coa_id' => $value->tax->coa_sale_data->id,
                'debit' => 0,
                'credit' => $value->tax_amount,
                'remark' => "INVOICE - {$invoice_general->customer->nama} - {$invoice_general->code}-  {$value->tax->tax_name_with_percent}"
            ]);

            $dpp = $value->final_amount / $value->tax_value;

            if ($value->tax->type == 'ppn') {
                $invoice_tax = new InvoiceTax();
                $invoice_tax->loadModel(
                    [
                        'reference_model' => InvoiceGeneral::class,
                        'reference_id' => $invoice_general->id,
                        'reference_parent_model' => InvoiceGeneral::class,
                        'reference_parent_id' => $invoice_general->id,
                        'date' => Carbon::parse($invoice_general->date),
                        'customer_id' => $invoice_general->customer_id,
                        'tax_id' => $value->tax_id,
                        'dpp' => ($dpp * $invoice_general->exchange_rate),
                        'value' => $value->tax_value,
                        'amount' => ($value->final_amount * $invoice_general->exchange_rate),
                    ]
                );
                $invoice_tax->save();
            }
        }

        foreach ($invoice_general->invoice_parent()->down_payment_invoices as $key => $down_payment_invoice) {
            $invoice_down_payment = $down_payment_invoice->invoice_down_payment;

            // PIUTANG
            $journal->journal_details()->create([
                'coa_id' => $customer_receivable_coa->coa_id,
                'debit' => 0,
                'credit' => $down_payment_invoice->invoice_down_payment->grand_total,
                'remark' => "INVOICE - {$invoice_general->customer->nama} - {$invoice_general->code}"
            ]);

            // UANG MUKA
            $journal->journal_details()->create([
                'exchange_rate' => $invoice_down_payment->exchange_rate,
                'coa_id' => $customer_deposit_coa->coa_id,
                'debit' => $down_payment_invoice->invoice_down_payment->down_payment,
                'credit' => 0,
                'remark' => "INVOICE - {$invoice_general->customer->nama} - {$invoice_general->code}"
            ]);

            foreach ($down_payment_invoice->invoice_down_payment->invoice_down_payment_taxes as $key => $invoice_down_payment_tax) {
                $journal->journal_details()->create([
                    'exchange_rate' => $invoice_down_payment->exchange_rate,
                    'coa_id' => $invoice_down_payment_tax->tax->coa_sale_data->id,
                    'debit' => $invoice_down_payment_tax->amount,
                    'credit' => 0,
                    'remark' => "INVOICE - {$invoice_general->customer->nama} - {$invoice_general->code} - {$invoice_down_payment_tax->tax->tax_name_with_percent}"
                ]);
            }

            // exchange rate gap
            $rate_gap = ($invoice_general->exchange_rate - $invoice_down_payment->exchange_rate) * ($invoice_down_payment->down_payment ?? 0);
            if ($rate_gap != 0) {
                $exchange_rate_gap_coa = get_default_coa('finance', 'Exchange Rate Gap')->coa_id;
                $journal->journal_details()->create([
                    'currency_id' => get_local_currency()->id,
                    'exchange_rate' => 1,
                    'reference_id' => $down_payment_invoice->id,
                    'reference_model' => get_class($down_payment_invoice),
                    'coa_id' => $exchange_rate_gap_coa,
                    'debit' => $rate_gap,
                    'remark' => $default_remark,
                ]);
            }

            InvoicePayment::updateOrCreate([
                'invoice_model' => InvoiceGeneral::class,
                'invoice_id' => $invoice_general->id,
                'model' => InvoiceDownPayment::class,
                'reference_id' => $down_payment_invoice->invoice_down_payment->id
            ], [
                'invoice_model' => InvoiceGeneral::class,
                'invoice_id' => $invoice_general->id,
                'model' => InvoiceDownPayment::class,
                'reference_id' => $down_payment_invoice->invoice_down_payment->id,

                'currency_id' => $down_payment_invoice->invoice_down_payment->currency_id,
                'exchange_rate' => $invoice_general->exchange_rate,
                'date' => $invoice_general->date,
                'amount_to_receive' => 0,
                'receive_amount' => $down_payment_invoice->invoice_down_payment->grand_total,
                'note' => $down_payment_invoice->invoice_down_payment->note,
            ]);
        }

        // HPP & GODS IN TRANSIT
        foreach ($invoice_general->invoice_general_details as $key => $invoice_general_detail) {
            $coa_goods_in_transit = $invoice_general_detail->item->item_category->item_category_coas
                ->filter(function ($query) {
                    return strtolower($query->type) == 'goods_in_transit';
                })
                ->first()
                ->coa ?? null;

            $hpp_coa = $invoice_general_detail->item->item_category->item_category_coas
                ->filter(function ($query) {
                    return strtolower($query->type) == 'hpp';
                })
                ->first()
                ->coa ?? null;

            $delivery_journal = JournalDetail::whereHas('journal', function ($q) use ($invoice_general_detail) {
                $q->where('reference_model', DeliveryOrderGeneral::class)
                    ->where('reference_id', $invoice_general_detail->delivery_order_general_detail->delivery_order_general_id);
            })
                ->where('reference_model', SaleOrderGeneralDetail::class)
                ->where('reference_id', $invoice_general_detail->sale_order_general_detail_id)
                ->where('coa_id', $coa_goods_in_transit->id ?? null)
                ->first();

            if ($delivery_journal) {
                $journal->journal_details()->create([
                    'coa_id' => $coa_goods_in_transit->id,
                    'debit' => 0,
                    'credit' => $delivery_journal->debit != 0 ? $delivery_journal->debit : $delivery_journal->credit,
                    'remark' => "INVOICE - {$invoice_general->customer->nama} - {$invoice_general->code} - {$invoice_general_detail->item->nama}"
                ]);

                $journal->journal_details()->create([
                    'coa_id' => $hpp_coa->id,
                    'debit' => $delivery_journal->debit  != 0 ? $delivery_journal->debit : $delivery_journal->credit,
                    'credit' => 0,
                    'remark' =>  "INVOICE - {$invoice_general->customer->nama} - {$invoice_general->code} - {$invoice_general_detail->item->nama}"
                ]);
            }
        }

        /**
         * ! ==================================================================================================================
         * ! end create journal details
         * ! ==================================================================================================================
         *
         */

        //  * update journal credit and debit
        $journal->update([
            'debit_total' => $journal->journal_details()->sum('debit'),
            'credit_total' => $journal->journal_details()->sum('credit'),
        ]);
    }

    /**
     * generate_delivery_order_trading_journal
     *
     * @return void
     */
    public function generate_delivery_order_trading_journal()
    {
        // * get needed data
        $delivery_order = \App\Models\DeliveryOrder::find($this->model_id);
        $sale_order = \App\Models\SoTrading::find($delivery_order->so_trading_id);

        // * create journal

        $journal = Journal::where('reference_model', \App\Models\DeliveryOrder::class)
            ->where('reference_id', $delivery_order->id)
            ->first();

        if (!$journal) {
            $journal = new \App\Models\Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $delivery_order->branch_id,
            'reference_id' => $delivery_order->id,
            'reference_model' => \App\Models\DeliveryOrder::class,
            'customer_id' => $sale_order->customer_id,
            'reference_number' => $sale_order->nomor_po_external,
            'document_reference' => [
                'id' => $delivery_order->id,
                'model' => DeliveryOrder::class,
                'code' => $delivery_order->code,
                'link' => route('admin.delivery-order.list-delivery-order.show', ['sale_order_id' => $sale_order->id, 'delivery_order_id' => $delivery_order->id]),
            ],
            'reference' => [
                'id' => $sale_order->id,
                'model' => SoTrading::class,
                'code' => $sale_order->nomor_so,
                'link' => route('admin.sales-order.show', ['sales_order' => $sale_order->id]),
            ],
            'date' => $delivery_order->load_date,
            'exchange_rate' => 1,
            'currency_id' => get_local_currency()->id,
            'journal_type' => "Delivery Order Trading",
            'remark' => "DO - {$delivery_order->so_trading->customer->nama} - {$delivery_order->code}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        $credit_total = 0;
        $debit_total = 0;

        /**
         * ! ==================================================================================================================
         * ! create journal details
         * ! ==================================================================================================================
         *
         */

        // * item
        $item = $sale_order->so_trading_detail->item;
        $item_type = $item->item_category->item_type;
        $item_category = $item->item_category;

        $last_stock = \App\Models\StockMutation::orderBy('id', 'desc')
            ->where('item_id', $item->id)
            ->first();

        if (!$last_stock) {
            throw new \Throwable("Stock tidak ditermukan");
        }

        // * item sended to customer
        $item_sended = $delivery_order->load_quantity_realization;
        if ($delivery_order->type == 'delivery-order-2' && is_null($delivery_order->delivery_order_id)) {
            $item_sended = $delivery_order->unload_quantity_realization;
        }
        $item_price = $delivery_order->hpp;

        if (!$item_category->item_category_coas
            ->filter(function ($query) {
                return strtolower($query->type) == 'goods_in_transit';
            })
            ->first()->coa_id ?? null) {
            throw new \Exception("Goods In Transit COA not found");
        }

        foreach ($item_category->item_category_coas as $item_category_coa) {
            $item_sended_price = $item_sended * $item_price;
            // ? item inventory
            if (strtolower($item_category_coa->type) == 'inventory') {
                $journal->journal_details()->create([
                    'reference_id' => $sale_order->so_trading_detail->id,
                    'reference_model' => \App\Models\SoTradingDetail::class,
                    'coa_id' => $item_category_coa->coa_id,
                    'credit' => $item_sended_price,
                    'debit' => 0,
                    'remark' => "DO - {$delivery_order->so_trading->customer->nama} - {$delivery_order->code} - $item->nama"
                ]);

                $credit_total += $item_sended_price;
            }

            // ? hpp
            if (strtolower($item_category_coa->type) == 'goods_in_transit') {
                $journal->journal_details()->create([
                    'reference_id' => $sale_order->so_trading_detail->id,
                    'reference_model' => \App\Models\SoTradingDetail::class,
                    'coa_id' => $item_category_coa->coa_id,
                    'credit' => 0,
                    'debit' => $item_sended_price,
                    'remark' => "DO - {$delivery_order->so_trading->customer->nama} - {$delivery_order->code} - $item->nama"
                ]);

                $debit_total += $item_sended_price;
            }
        }

        /**
         * ! ==================================================================================================================
         * ! end create journal details
         * ! ==================================================================================================================
         *
         */

        // * update journal credit and debit
        $journal->update([
            'debit_total' => $debit_total,
            'credit_total' => $credit_total
        ]);
    }

    /**
     * generate_delivery_order_general_journal
     *
     * @return void
     */
    public function generate_delivery_order_general_journal()
    {
        // * get needed data
        $delivery_order = \App\Models\DeliveryOrderGeneral::find($this->model_id);
        $sale_order_general = \App\Models\SaleOrderGeneral::find($delivery_order->sale_order_general_id);

        // * create journal
        $journal = \App\Models\Journal::where('reference_id', $delivery_order->id)
            ->where('reference_model', \App\Models\DeliveryOrderGeneral::class)
            ->first();

        if (!$journal) {
            $journal = new \App\Models\Journal();
        } else {
            $journal->journal_details()->delete();
        }
        $journal->loadModel([
            'branch_id' => $delivery_order->branch_id,
            'reference_id' => $delivery_order->id,
            'reference_model' => \App\Models\DeliveryOrderGeneral::class,
            'customer_id' => $delivery_order->vendor_id,
            'document_reference' => [
                'id' => $delivery_order->id,
                'model' => DeliveryOrderGeneral::class,
                'code' => $delivery_order->code,
                'link' => route('admin.delivery-order-general.show', ['delivery_order_general' => $delivery_order->id]),
            ],
            'reference' => [
                'id' => $sale_order_general->id,
                'model' => SaleOrderGeneral::class,
                'code' => $sale_order_general->kode,
                'link' => route('admin.sales-order-general.show', ['sales_order_general' => $sale_order_general->id]),
            ],
            'reference_number' => $delivery_order->external_code,
            'date' => $delivery_order->date,
            'exchange_rate' => 1,
            'currency_id' => get_local_currency()->id,
            'journal_type' => "Delivery Order General",
            'remark' => "DO - {$delivery_order->customer->nama} - {$delivery_order->code}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        $credit_total = 0;
        $debit_total = 0;

        /**
         * ! ==================================================================================================================
         * ! create journal details
         * ! ==================================================================================================================
         *
         */

        foreach ($delivery_order->delivery_order_general_details as $delivery_order_detail) {
            // * item
            $item = $delivery_order_detail->item;
            $item_type = $item->item_category->item_type;
            $item_category = $item->item_category;

            if ($item_type->nama == 'purchase item') {
                // * item sended to customer
                $item_sended = $delivery_order_detail->quantity;
                $item_price = $delivery_order_detail->hpp;
                $item_sended_price = $item_sended * $item_price;

                if (!$item_category->item_category_coas
                    ->filter(function ($query) {
                        return strtolower($query->type) == 'goods_in_transit';
                    })
                    ->first()->coa_id ?? null) {
                    throw new \Exception("Goods In Transit COA not found");
                }

                foreach ($item_category->item_category_coas as $item_category_coa) {
                    // ? item inventory
                    if (strtolower($item_category_coa->type) == 'inventory') {
                        $journal->journal_details()->create([
                            'reference_id' => $delivery_order_detail->sale_order_general_detail_id,
                            'reference_model' => \App\Models\SaleOrderGeneralDetail::class,
                            'coa_id' => $item_category_coa->coa_id,
                            'credit' => $item_sended_price,
                            'debit' => 0,
                            'remark' => "DO - {$delivery_order->customer->nama} - {$delivery_order->code} - $item->nama"
                        ]);

                        $credit_total += $item_sended_price;
                    }

                    // ? hpp
                    if (strtolower($item_category_coa->type) == 'goods_in_transit') {
                        $journal->journal_details()->create([
                            'reference_id' => $delivery_order_detail->sale_order_general_detail_id,
                            'reference_model' => \App\Models\SaleOrderGeneralDetail::class,
                            'coa_id' => $item_category_coa->coa_id,
                            'credit' => 0,
                            'debit' => $item_sended_price,
                            'remark' => "DO - {$delivery_order->customer->nama} - {$delivery_order->code} - $item->nama"
                        ]);

                        $debit_total += $item_sended_price;
                    }
                }
            }
        }

        /**
         * ! ==================================================================================================================
         * ! end create journal details
         * ! ==================================================================================================================
         *
         */

        // * update journal credit and debit
        $journal->update([
            'debit_total' => $debit_total,
            'credit_total' => $credit_total
        ]);
    }

    /**
     * generate journal cash advance return journal
     *
     * @return void
     */
    public function generate_cash_advance_return_journal()
    {
        // * get needed data
        $cash_advance_return = \App\Models\CashAdvancedReturn::find($this->model_id);
        $cash_advance_return_details = $cash_advance_return->cashAdvancedReturnDetails;
        $cash_advance_return_invoices = $cash_advance_return->cashAdvancedReturnInvoices;
        $cash_advance_return_other_transactions = $cash_advance_return->cashAdvancedReturnTransactions;
        $cash_advance_return_type = $cash_advance_return->type;
        $cash_advance_return_coa_id = null;
        $exchange_rate_coa = \App\Models\DefaultCoa::where('type', 'finance')->where('name', 'Exchange Rate Gap')->first()->coa_id;


        if ($cash_advance_return_type == 'customer') {
            $cash_advance_return_coa_id = \App\Models\CustomerCoa::where('customer_id', $cash_advance_return->reference_id)->where('tipe', "Account Receivable Coa")->first()->coa_id;
            if ($cash_advance_return_coa_id == null) {
                $cash_advance_return_coa_id = \App\Models\DefaultCoa::where('type', 'customer')->where('name', 'Account Receivable Coa')->first()->coa_id;
            }
            $customer = Customer::find($cash_advance_return->reference_id);
            $link = route('admin.cash-advance-return-customer.show', ['cash_advance_return_customer' => $cash_advance_return->id]);
        } elseif ($cash_advance_return_type == 'vendor') {
            $cash_advance_return_coa_id = \App\Models\VendorCoa::where('vendor_id', $cash_advance_return->reference_id)->where('type', "Account Payable Coa")->first()->coa_id;

            if ($cash_advance_return_coa_id == null) {
                $cash_advance_return_coa_id = \App\Models\DefaultCoa::where('type', 'vendor')->where('name', 'Account Payable Coa')->first()->coa_id;
            }
            $vendor = Vendor::find($cash_advance_return->reference_id);
            $link = route('admin.cash-advance-return-vendor.show', ['cash_advance_return_vendor' => $cash_advance_return->id]);
        }

        // * create journal
        $journal = new \App\Models\Journal();
        $journal->fill([
            'branch_id' => $cash_advance_return->branch_id,
            'reference_id' => $cash_advance_return->id,
            'reference_model' => \App\Models\CashAdvancedReturn::class,
            'customer_id' => $cash_advance_return_type == "customer" ? $customer->id : null,
            'vendor_id' => $cash_advance_return_type == "vendor" ? $vendor->id : null,
            'document_reference' => [
                'id' => $cash_advance_return->id,
                'model' => CashAdvancedReturn::class,
                'code' => $cash_advance_return->code,
                'link' => $link,
            ],
            'date' => $cash_advance_return->date,
            'exchange_rate' => $cash_advance_return->currency->is_local ? 1 : $cash_advance_return->exchange_rate,
            'currency_id' => $cash_advance_return->currency_id,
            'journal_type' => "Cash Advance Return",
            'remark' => "PENG. UANG MUKA - {$cash_advance_return->reference->nama} - {$cash_advance_return->code}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        $credit_total = 0;
        $debit_total = 0;
        $new_journal_details = [];

        // ! CASH ADVANCED DETAILS ############################
        $details_exchange_rate_gap_journals = [];
        $cash_advance_tax_amount = 0;

        foreach ($cash_advance_return_details as $cash_advance_return_detail) {
            // ? exchange rate gap
            if (!$cash_advance_return->currency->is_local) {
                $exchange_rate_gap = ($cash_advance_return->exchange_rate - $cash_advance_return_detail->exchange_rate) * $cash_advance_return_detail->amount_to_return;
                $exchange_rate_gap /= $cash_advance_return->exchange_rate;
            } else {
                $exchange_rate_gap = 0;
            }

            $this_debit = $cash_advance_return_type == 'customer' ? $cash_advance_return_detail->amount_to_return : 0;
            $this_credit = $cash_advance_return_type == 'vendor' ? $cash_advance_return_detail->amount_to_return : 0;

            if ($cash_advance_return_type == 'vendor') {
                $this_credit -= $exchange_rate_gap;
            } else {
                $this_debit -= $exchange_rate_gap;
            }

            $this_debit = $this_debit;
            $this_credit = $this_credit;

            $new_journal_details[] = [
                'reference_id' => $cash_advance_return_detail->id,
                'reference_model' => \App\Models\CashAdvancedReturnDetail::class,
                'coa_id' => $cash_advance_return_detail->coa_id,
                'credit' => $this_credit,
                'debit' => $this_debit,
                'remark' => "PENG. UANG MUKA - {$cash_advance_return->reference->nama} - {$cash_advance_return->code}"
            ];

            $credit_total += $this_credit;
            $debit_total += $this_debit;

            if ($cash_advance_return_detail->reference->tax) {
                $tax = $cash_advance_return_detail->reference->tax;
                $cash_advance = $cash_advance_return_detail->reference;
                if ($cash_advance_return_type == 'vendor') {
                    $cash_advance_tax =  $cash_advance->cash_advance_payment_details->where('type', 'tax')->first();
                    $cash_advance_tax_amount += $cash_advance_tax->debit;
                } else {
                    $cash_advance_tax =  $cash_advance->cash_advance_return_details->where('type', 'tax')->first();
                    $cash_advance_tax_amount += $cash_advance_tax->credit;
                }

                $new_journal_details[] = [
                    'reference_id' => $cash_advance_return_detail->id,
                    'reference_model' => \App\Models\CashAdvancedReturnDetail::class,
                    'coa_id' => $cash_advance_return_type == 'vendor' ? $tax->coa_purchase : $tax->coa_sale,
                    'credit' => $cash_advance_return_type == 'vendor' ? $cash_advance_tax->debit : 0,
                    'debit' => $cash_advance_return_type == 'customer' ? $cash_advance_tax->credit : 0,
                    'remark' => "PENG. UANG MUKA - {$cash_advance_return->reference->nama} - {$cash_advance_return->code}"
                ];
            }

            if ($exchange_rate_gap != 0) {
                $this_debit = 0;
                $this_credit = 0;

                if ($cash_advance_return_type == 'vendor') {
                    $this_credit += $exchange_rate_gap;
                } else {
                    $this_debit += $exchange_rate_gap;
                }

                $this_debit = $this_debit;
                $this_credit = $this_credit;

                $details_exchange_rate_gap_journals[] = [
                    'reference_id' => $cash_advance_return_detail->id,
                    'reference_model' => \App\Models\CashAdvancedReturnDetail::class,
                    'coa_id' => $exchange_rate_coa,
                    'credit' => $this_credit,
                    'debit' => $this_debit,
                    'remark' => "PENG. UANG MUKA - {$cash_advance_return->reference->nama} - {$cash_advance_return->code}"
                ];

                $credit_total += $this_credit;
                $debit_total += $this_debit;
            }
        }

        foreach ($details_exchange_rate_gap_journals as $details_exchange_rate_gap_journal) {
            $new_journal_details[] = $details_exchange_rate_gap_journal;
        }

        // ! / CASH ADVANCED DETAILS ############################

        // ! CASH ADVANCED INVOICES ############################
        $invoices_exchange_rate_gap_journals = [];
        foreach ($cash_advance_return_invoices as $cash_advance_return_invoice) {
            $this_credit = $cash_advance_return_type == 'customer' ? $cash_advance_return_invoice->amount_to_paid_or_return_convert : 0;
            $this_debit = $cash_advance_return_type == 'vendor' ? $cash_advance_return_invoice->amount_to_paid_or_return_convert : 0;

            $exchange_rate_gap = $cash_advance_return_invoice->exchange_rate_gap;
            if (!$cash_advance_return->currency->is_local) {
                $exchange_rate_gap /= $cash_advance_return->exchange_rate;
            }

            if ($cash_advance_return_type == 'vendor') {
                $this_debit -= $exchange_rate_gap;
            } else {
                $this_credit -= $exchange_rate_gap;
            }

            $this_credit = $this_credit;
            $this_debit = $this_debit;

            $new_journal_details[] = [
                'reference_id' => $cash_advance_return_invoice->id,
                'reference_model' => \App\Models\CashAdvancedReturnInvoice::class,
                'coa_id' => $cash_advance_return_coa_id,
                'credit' => $cash_advance_return_type == 'customer' ? ($this_credit + $cash_advance_tax_amount) : $this_credit,
                'debit' => $cash_advance_return_type == 'vendor' ? ($this_debit + $cash_advance_tax_amount) : $this_debit,
                'remark' => "PENG. UANG MUKA - {$cash_advance_return->reference->nama} - {$cash_advance_return->code} - {$cash_advance_return_invoice->transaction_code}"
            ];

            $credit_total += $this_credit;
            $debit_total += $this_debit;

            // ? exchange rate gap
            if ($exchange_rate_gap != 0) {
                $this_debit = 0;
                $this_credit = 0;

                if ($cash_advance_return_type == 'vendor') {
                    $this_debit += $exchange_rate_gap;
                } else {
                    $this_credit += $exchange_rate_gap;
                }

                $this_debit = $this_debit;
                $this_credit = $this_credit;

                $invoices_exchange_rate_gap_journals[] = [
                    'reference_id' => $cash_advance_return_invoice->id,
                    'reference_model' => \App\Models\CashAdvancedReturnInvoice::class,
                    'coa_id' => $exchange_rate_coa,
                    'credit' => $this_credit,
                    'debit' => $this_debit,
                    'remark' => "PENG. UANG MUKA - {$cash_advance_return->reference->nama} - {$cash_advance_return->code}"
                ];

                $credit_total += $this_credit;
                $debit_total += $this_debit;
            }
        }

        foreach ($invoices_exchange_rate_gap_journals as $invoices_exchange_rate_gap_journal) {
            $new_journal_details[] = $invoices_exchange_rate_gap_journal;
        }
        // ! / CASH ADVANCED INVOICES ############################

        // ! CASH ADVANCED OTHER TRANSACTIONS ############################
        foreach ($cash_advance_return_other_transactions as $cash_advance_return_other_transaction) {
            $other_debit = $cash_advance_return_other_transaction->debit;
            $other_credit = $cash_advance_return_other_transaction->credit;

            $new_journal_details[] = [
                'reference_id' => $cash_advance_return_other_transaction->id,
                'reference_model' => \App\Models\CashAdvancedReturnTransaction::class,
                'coa_id' => $cash_advance_return_other_transaction->coa_id,
                'credit' => $other_credit,
                'debit' => $other_debit,
                'remark' => "PENG. UANG MUKA - {$cash_advance_return->reference->nama} - {$cash_advance_return->code} - $cash_advance_return_other_transaction->description"
            ];

            $credit_total += $other_credit;
            $debit_total += $other_debit;
        }

        // * create journal details
        try {
            $journal->journal_details()->createMany($new_journal_details);
        } catch (\Throwable $th) {
            throw $th;
        }

        // * update journal credit and debit
        $journal->update([
            'debit_total' => $journal->journal_details()->sum('debit'),
            'credit_total' => $journal->journal_details()->sum('credit'),
        ]);
    }

    /**
     * generate journal cash bond
     *
     * @return void
     */
    public function generate_cash_bond_journal(): void
    {
        // * get cash bond
        $cash_bond = \App\Models\CashBond::find($this->model_id);

        // ! validate journal exchange rate

        // * get cash bank
        $cashBank = \App\Models\CashBondDetail::with(['coa'])->where('cash_bond_id', $this->model_id)->where('type', 'cash_bank')->first();
        if (!$cashBank) {
            throw new \Exception("Cash Bank not found");
        }

        $cashBankCurrency = $cashBank->coa->currency_id ? \App\Models\Currency::find($cashBank->coa->currency_id) : get_local_currency();

        $currency_id = $cashBankCurrency->id;
        $exchange_rate = $cash_bond->exchange_rate;

        // * create journal
        $journal = new \App\Models\Journal();
        $journal->fill([
            'branch_id' => $cash_bond->branch_id,
            'reference_id' => $cash_bond->id,
            'reference_model' => \App\Models\CashAdvancedReturn::class,
            'document_reference' => [
                'id' => $cash_bond->id,
                'model' => CashBond::class,
                'code' => $cash_bond->code,
                'link' => route('admin.cash-bond.show', ['cash_bond' => $cash_bond->id]),
            ],
            'date' => $cash_bond->date,
            'exchange_rate' => $exchange_rate,
            'currency_id' => $currency_id,
            'journal_type' => "Cash Bond",
            'remark' => "KASBON - {$cash_bond->employee->name} - {$cash_bond->code}",
            'status' => 'approve',
            'is_generated' => true,
            'bank_code_mutation' => $cash_bond->bank_code_mutation,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        $credit_total = 0;
        $debit_total = 0;
        $journal_details = [];

        foreach ($cash_bond->cashBondDetails as $cash_bond_detail) {
            $cash_bond_detail_debit = $cash_bond_detail->debit * $exchange_rate;
            $cash_bond_detail_credit = $cash_bond_detail->credit * $exchange_rate;

            $journal_details[] = [
                'reference_id' => $cash_bond_detail->id,
                'reference_model' => \App\Models\CashBondDetail::class,
                'coa_id' => $cash_bond_detail->coa_id,
                'credit' => $cash_bond_detail_credit,
                'debit' => $cash_bond_detail_debit,
                'remark' => "KASBON - {$cash_bond->employee->name} - {$cash_bond->code} - {$cash_bond_detail->note}"
            ];

            $credit_total += $cash_bond_detail_credit;
            $debit_total += $cash_bond_detail_debit;
        }

        // * create journal details
        try {
            $journal->journal_details()->createMany($journal_details);
        } catch (\Throwable $th) {
            throw $th;
        }

        // * update journal credit and debit
        $journal->update([
            'debit_total' => $debit_total,
            'credit_total' => $credit_total
        ]);
    }

    /**
     * generate journal cash bond return
     *
     * @return void
     */
    public function generate_cash_bond_return_journal(): void
    {
        // * get needed data
        $cash_bond_return = \App\Models\CashBondReturn::find($this->model_id);

        // * get cash bank
        $cashBankCurrency = $cash_bond_return->coa->currency_id ? \App\Models\Currency::find($cash_bond_return->coa->currency_id) : get_local_currency();

        $currency_id = $cashBankCurrency->id;
        $exchange_rate = $cash_bond_return->exchange_rate;

        // * create journal
        $journal = new \App\Models\Journal();
        $journal->fill([
            'branch_id' => $cash_bond_return->branch_id,
            'reference_id' => $cash_bond_return->id,
            'reference_model' => \App\Models\CashBondReturn::class,
            'document_reference' => [
                'id' => $cash_bond_return->id,
                'model' => CashBondReturn::class,
                'code' => $cash_bond_return->code,
                'link' => route('admin.cash-bond-return.show', ['cash_bond_return' => $cash_bond_return->id]),
            ],
            'date' => $cash_bond_return->date,
            'exchange_rate' => $exchange_rate,
            'currency_id' => $currency_id,
            'journal_type' => "Cash Bond Return",
            'remark' => "PENG. KASBON - {$cash_bond_return->employee->name} - {$cash_bond_return->code}",
            'status' => 'approve',
            'is_generated' => true,
            'bank_code_mutation' => $cash_bond_return->bank_code_mutation,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        // * create journal details
        $journal_details = [];
        // ? set journal details data

        // * cash bank

        // * cash bond
        foreach ($cash_bond_return->cashBondReturnDetails as $cashBondReturnDetail) {
            $amount_to_return = $cashBondReturnDetail->amount_to_return;
            // * cash bank
            $journal_details[] = [
                'reference_id' => $cash_bond_return->id,
                'reference_model' => \App\Models\CashBondReturn::class,
                'coa_id' => $cash_bond_return->coa_id,
                'credit' => 0,
                'debit' => $amount_to_return,
                'remark' => "PENG. KASBON - {$cash_bond_return->employee->name} - {$cash_bond_return->code} - $cashBondReturnDetail->note"
            ];

            $journal_details[] = [
                'reference_id' => $cashBondReturnDetail->id,
                'reference_model' => \App\Models\CashBondReturnDetail::class,
                'coa_id' => $cashBondReturnDetail->coa_id,
                'credit' => $amount_to_return,
                'debit' => 0,
                'remark' =>  "PENG. KASBON - {$cash_bond_return->employee->name} - {$cash_bond_return->code} - $cashBondReturnDetail->note"
            ];
        }

        // * adjustment
        foreach ($cash_bond_return->cashBondReturnOthers as $cashBondReturnOther) {
            $other_credit = $cashBondReturnOther->amount;
            $other_debit = 0;

            // * cash bank
            $journal_details[] = [
                'reference_id' => $cash_bond_return->id,
                'reference_model' => \App\Models\CashBondReturn::class,
                'coa_id' => $cash_bond_return->coa_id,
                'credit' => $other_credit,
                'debit' => $other_debit,
                'remark' =>  "PENG. KASBON - {$cash_bond_return->employee->name} - {$cash_bond_return->code} - {$cashBondReturnOther->description}"
            ];

            $journal_details[] = [
                'reference_id' => $cashBondReturnOther->id,
                'reference_model' => \App\Models\CashBondReturnDetail::class,
                'coa_id' => $cashBondReturnOther->coa_id,
                'credit' => $other_debit,
                'debit' => $other_credit,
                'remark' =>  "PENG. KASBON - {$cash_bond_return->employee->name} - {$cash_bond_return->code} - {$cashBondReturnOther->description}"
            ];
        }

        // ? saving
        try {
            $journal->journal_details()->createMany($journal_details);
        } catch (\Throwable $th) {
            throw $th;
        }


        // * update parent journal credit and debit
        $journal->update([
            'debit_total' => $journal->journal_details()->sum('debit'),
            'credit_total' => $journal->journal_details()->sum('credit'),
        ]);
    }

    /**
     * Generate stock usage journal
     *
     * @return void
     */
    public function generate_stock_usage_journal(): void
    {
        // * get needed data
        $stock_usage = \App\Models\StockUsage::with(['stock_usage_details'])->find($this->model_id);

        // * create journal
        $journal = Journal::where('reference_id', $stock_usage->id)
            ->where('reference_model', \App\Models\StockUsage::class)
            ->first();
        if (!$journal) {
            $journal = new \App\Models\Journal();
        } else {
            // ? delete old journal details
            $journal->journal_details()->delete();
        }

        $subject = $stock_usage->employee->name ?? $stock_usage->fleet->name;
        $journal->fill([
            'branch_id' => $stock_usage->branch_id,
            'reference_id' => $stock_usage->id,
            'reference_model' => \App\Models\StockUsage::class,
            'document_reference' => [
                'id' => $stock_usage->id,
                'model' => StockUsage::class,
                'code' => $stock_usage->code,
                'link' => route('admin.stock-usage.show', ['stock_usage' => $stock_usage->id]),
            ],
            'date' => $stock_usage->date,
            'exchange_rate' => 1,
            'currency_id' => get_local_currency()->id,
            'journal_type' => "Stock Usage",
            'remark' =>  "PEM. STOK - {$subject} - {$stock_usage->code}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        // ? VARIABLES
        $credit_total = 0;
        $debit_total = 0;

        /**
         * ! ==================================================================================================================
         * ! create journal details
         * ! ==================================================================================================================
         *
         */

        //  * if the coa parent is not null
        $is_coa_parent_null = true;

        // * is parent have an coa parent
        // * create journal details
        $is_coa_parent_null = false;

        $expense = 0;
        foreach ($stock_usage->stock_usage_details as $stockUsageDetail) {
            // * item
            $item = $stockUsageDetail->item;
            $item_category = $item->item_category;
            $value = $stockUsageDetail->price_unit * $stockUsageDetail->quantity;
            $expense += $value;

            $journal->journal_details()->create([
                'reference_id' => $stock_usage->id,
                'reference_model' => \App\Models\StockUsage::class,
                'coa_id' => $stockUsageDetail->coa_detail_id,
                'credit' => 0,
                'debit' => $value,
                'remark' => "PEM. STOK - {$subject} - {$stock_usage->code} - {$stockUsageDetail->item->nama}"
            ]);
        }


        $debit_total += $expense;


        foreach ($stock_usage->stock_usage_details as $stockUsageDetail) {
            // * item
            $item = $stockUsageDetail->item;
            $item_category = $item->item_category;

            // * item sended to customer
            foreach ($item_category->item_category_coas as $item_category_coa) {
                // ? item inventory
                if (strtolower($item_category_coa->type) == 'inventory') {
                    $journal->journal_details()->create([
                        'reference_id' => $stockUsageDetail->id,
                        'reference_model' => \App\Models\StockUsageDetail::class,
                        'coa_id' => $item_category_coa->coa_id,
                        'credit' => $stockUsageDetail->price_unit * $stockUsageDetail->quantity,
                        'debit' => 0,
                        'remark' => "PEM. STOK - {$subject} - {$stock_usage->code} - {$item->nama} - {$stockUsageDetail->necessity}"
                    ]);

                    $credit_total += $stockUsageDetail->price_unit * $stockUsageDetail->quantity;
                }

                // ! DON'T DELETE
                // ! THIS ID FOR OLD DATA WHO DOES'NT HAVE COA PARENT
                if ($is_coa_parent_null && $stockUsageDetail->coa_detail_id == null) {
                    // ? expense
                    if (strtolower($item_category_coa->type) == 'expense') {
                        $journal->journal_details()->create([
                            'reference_id' => $stockUsageDetail->id,
                            'reference_model' => \App\Models\StockUsageDetail::class,
                            'coa_id' => $item_category_coa->coa_id,
                            'credit' => 0,
                            'debit' => $stockUsageDetail->price_unit * $stockUsageDetail->quantity,
                            'remark' => "PEM. STOK - {$subject} - {$stock_usage->code} - {$item->nama} - {$stockUsageDetail->necessity}"
                        ]);

                        $debit_total += $stockUsageDetail->price_unit * $stockUsageDetail->quantity;
                    }
                }
            }
        }
        // * update parent journal credit and debit
        $journal->update([
            'debit_total' => $journal->journal_details()->sum('debit'),
            'credit_total' => $journal->journal_details()->sum('credit'),
        ]);
    }

    /**
     * generate stock opname journal
     *
     * @param void
     */
    public function generate_stock_opname_journal(): void
    {
        // * get needed data
        $stock_opname = \App\Models\StockOpname::find($this->model_id);

        // * create journal

        $journal = Journal::where('reference_id', $stock_opname->id)->where('reference_model', StockOpname::class)->first();
        if (!$journal) {
            $journal = new \App\Models\Journal();
        } else {
            // ? delete old journal details
            $journal->journal_details()->delete();
        }
        $journal->fill([
            'branch_id' => $stock_opname->branch_id,
            'reference_id' => $stock_opname->id,
            'reference_model' => \App\Models\StockOpname::class,
            'document_reference' => [
                'id' => $stock_opname->id,
                'model' => StockOpname::class,
                'code' => $stock_opname->code,
                'link' => route('admin.stock-adjustment.show', ['stock_adjustment' => $stock_opname->id]),
            ],
            'date' => $stock_opname->date,
            'exchange_rate' => 1,
            'currency_id' => get_local_currency()->id,
            'journal_type' => "Stock Opname",
            'remark' => "STOCK OPNAME - {$stock_opname->code}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        // ? VARIABLES
        $credit_total = 0;
        $debit_total = 0;

        foreach ($stock_opname->details as $stockOpnameDetail) {
            // * item
            $item = $stockOpnameDetail->item;
            $item_category = $item->item_category;

            // * item sended to customer
            foreach ($item_category->item_category_coas as $item_category_coa) {
                // ! DON'T DELETE
                // ! THIS ID FOR OLD DATA WHO DOES'NT HAVE COA PARENT

                // ? expense
                if (strtolower($item_category_coa->type) == 'expense') {
                    $journal->journal_details()->create([
                        'reference_id' => $stockOpnameDetail->id,
                        'reference_model' => \App\Models\StockOpnameDetail::class,
                        'coa_id' => $stock_opname->coa_id ?? $item_category_coa->coa_id,
                        'debit' => 0,
                        'credit' => $stockOpnameDetail->value,
                        'remark' => "STOCK OPNAME - {$stock_opname->code} - {$item->nama} - {$stockOpnameDetail->note}"
                    ]);

                    $credit_total += $stockOpnameDetail->value;
                }

                // ? item inventory
                if (strtolower($item_category_coa->type) == 'inventory') {
                    $journal->journal_details()->create([
                        'reference_id' => $stockOpnameDetail->id,
                        'reference_model' => \App\Models\StockOpnameDetail::class,
                        'coa_id' => $item_category_coa->coa_id,
                        'debit' => $stockOpnameDetail->value,
                        'credit' => 0,
                        'remark' => "STOCK OPNAME - {$stock_opname->code} - {$item->nama} - {$stockOpnameDetail->note}"
                    ]);

                    $debit_total += $stockOpnameDetail->value;
                }
            }
        }

        // * update parent journal credit and debit
        $journal->update([
            'debit_total' => $journal->journal_details()->sum('debit'),
            'credit_total' => $journal->journal_details()->sum('credit'),
        ]);
    }

    /**
     * generate_outgoing_payment_journal
     *
     * @return void
     */
    public function generate_outgoing_payment_journal()
    {
        // get needed data
        $outgoing_payment = OutgoingPayment::find($this->model_id);

        $default_remark = "KAS KELUAR - {$outgoing_payment->to_name} - {$outgoing_payment->bank_code_mutation}";

        $journal = Journal::where('reference_id', $outgoing_payment->id)
            ->where('reference_model', OutgoingPayment::class)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $outgoing_payment->branch_id,
            'date' => Carbon::parse($outgoing_payment->date),
            'bank_code_mutation' => $outgoing_payment->bank_code_mutation,
            'document_reference' => [
                'id' => $outgoing_payment->id,
                'model' => OutgoingPayment::class,
                'code' => $outgoing_payment->code,
                'link' => route('admin.outgoing-payment.show', ['outgoing_payment' => $outgoing_payment->id]),
            ],
            'remark' => "$default_remark - $outgoing_payment->reference",
            'journal_type' => "Outgoing Payment",
            'exchange_rate' => $outgoing_payment->exchange_rate,
            'currency_id' => $outgoing_payment->currency_id,
            'created_by' => auth()->user()->id ?? $outgoing_payment->created_by,
            'reference_model' => OutgoingPayment::class,
            'reference_id' => $outgoing_payment->id,
            'send_payment_id' => $outgoing_payment->fund_submission->send_payment->id ?? null,
            'is_generated' => true,
            'status' => 'approve',
        ]);

        $journal->save();
        JournalDetail::where('journal_id', $journal->id)->delete();

        $cash_bank = 0;
        foreach ($outgoing_payment->outgoing_payment_details as $key => $value) {
            $debit = $value->debit;
            $credit = 0;

            $cash_bank += $debit;
            $cash_bank -= $credit;

            $journal->journal_details()->create([
                'reference_id' => $value->id,
                'reference_model' => OutgoingPaymentDetail::class,
                'coa_id' => $value->coa_id,
                'debit' => $debit,
                'credit' => $credit,
                'remark' => "$default_remark - {$value->note}",
            ]);

            $journal->journal_details()->create([
                'reference_id' => $outgoing_payment->id,
                'reference_model' => OutgoingPayment::class,
                'coa_id' => $outgoing_payment->coa_id,
                'debit' => $credit,
                'credit' =>  $debit,
                'remark' => "$default_remark - $value->note",
            ]);
        }


        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * incoming_payment_journal
     *
     * @return void
     */
    public function incoming_payment_journal()
    {
        // get needed data
        $incoming_payment = IncomingPayment::find($this->model_id);

        $default_remark = "KAS MASUK - {$incoming_payment->from_name} - {$incoming_payment->bank_code_mutation}";
        $journal = Journal::where('reference_id', $incoming_payment->id)
            ->where('reference_model', IncomingPayment::class)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $incoming_payment->branch_id,
            'date' => Carbon::parse($incoming_payment->date),
            'bank_code_mutation' => $incoming_payment->bank_code_mutation,
            'document_reference' => [
                'id' => $incoming_payment->id,
                'model' => IncomingPayment::class,
                'code' => $incoming_payment->code,
                'link' => route('admin.incoming-payment.show', ['incoming_payment' => $incoming_payment->id]),
            ],
            'remark' => "$default_remark - $incoming_payment->reference",
            'journal_type' => "Incoming Payment",
            'exchange_rate' => $incoming_payment->exchange_rate,
            'currency_id' => $incoming_payment->currency_id,
            'created_by' => auth()->user()->id ?? $incoming_payment->created_by,
            'reference_model' => IncomingPayment::class,
            'reference_id' => $incoming_payment->id,
            'is_generated' => true,
            'status' => 'approve',
            'receive_payment_id' => $incoming_payment->receive_payment->id ?? null,
        ]);

        $journal->save();

        JournalDetail::where('journal_id', $journal->id)->delete();

        $cash_bank = 0;
        foreach ($incoming_payment->incoming_payment_details as $key => $value) {
            $credit = $value->credit;
            $debit = 0;

            $cash_bank -= $debit;
            $cash_bank += $credit;

            $journal->journal_details()->create([
                'reference_id' => $value->id,
                'reference_model' => IncomingPaymentDetail::class,
                'coa_id' => $value->coa_id,
                'debit' => $debit,
                'credit' => $credit,
                'remark' => "$default_remark - $value->note",
            ]);

            $journal->journal_details()->create([
                'reference_id' => $incoming_payment->id,
                'reference_model' => IncomingPayment::class,
                'coa_id' => $incoming_payment->coa_id,
                'debit' => $credit,
                'credit' =>  $debit,
                'remark' => "$default_remark - $value->note",
            ]);
        }


        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * generate_cash_advance_payment_journal
     *
     * @return void
     */
    public function generate_cash_advance_payment_journal()
    {
        // get needed data
        $cash_advance_payment = CashAdvancePayment::find($this->model_id);

        $default_remark = "PEM. UANG MUKA - {$cash_advance_payment->to_name} - {$cash_advance_payment->bank_code_mutation}";

        $journal = Journal::where('reference_id', $cash_advance_payment->id)
            ->where('reference_model', CashAdvancePayment::class)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $cash_advance_payment->branch_id,
            'date' => Carbon::parse($cash_advance_payment->date),
            'bank_code_mutation' => $cash_advance_payment->bank_code_mutation,
            'remark' =>  "$default_remark - $cash_advance_payment->reference",
            'document_reference' => [
                'id' => $cash_advance_payment->id,
                'model' => CashAdvancePayment::class,
                'code' => $cash_advance_payment->code,
                'link' => route('admin.cash-advance-payment.show', ['cash_advance_payment' => $cash_advance_payment->id]),
            ],
            'reference' => [
                'id' => $cash_advance_payment->fund_submission->id,
                'model' => FundSubmission::class,
                'code' => $cash_advance_payment->fund_submission->code,
                'link' => route('admin.fund-submission.show', ['fund_submission' => $cash_advance_payment->fund_submission->id]),
            ],
            'vendor_id' => $cash_advance_payment->to_id,
            'journal_type' => "Cash Advance Payment",
            'exchange_rate' => $cash_advance_payment->exchange_rate,
            'currency_id' => $cash_advance_payment->currency_id,
            'created_by' => auth()->user()->id ?? $cash_advance_payment->created_by,
            'reference_model' => CashAdvancePayment::class,
            'reference_id' => $cash_advance_payment->id,
            'is_generated' => true,
            'status' => 'approve',
            'send_payment_id' => $cash_advance_payment->fund_submission->send_payment->id ?? null,
        ]);

        $journal->save();

        JournalDetail::where('journal_id', $journal->id)->delete();
        foreach ($cash_advance_payment->cash_advance_payment_details as $key => $value) {
            if ($value->type == "other") {
                $debit = $value->debit;
                $credit = 0;
            } else {
                $debit = $value->debit;
                $credit = $value->credit;
            }

            $journal->journal_details()->create([
                'reference_id' => $value->id,
                'reference_model' => CashAdvancePaymentDetail::class,
                'coa_id' => $value->coa_id,
                'debit' => $debit,
                'credit' => $credit,
                'remark' => "$default_remark - $value->note",
            ]);
        }

        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * generate_cash_advance_receive_journal
     *
     * @return void
     */
    public function generate_cash_advance_receive_journal()
    {
        // get needed data
        $cash_advance_receive = CashAdvanceReceive::find($this->model_id);

        $default_remark = "PEN. UANG MUKA - {$cash_advance_receive->customer->nama} - {$cash_advance_receive->bank_code_mutation}";

        $journal = Journal::where('reference_id', $cash_advance_receive->id)
            ->where('reference_model', CashAdvanceReceive::class)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $cash_advance_receive->branch_id,
            'reference_model' => CashAdvanceReceive::class,
            'reference_id' => $cash_advance_receive->id,
            'customer_id' => $cash_advance_receive->customer_id,
            'document_reference' => [
                'id' => $cash_advance_receive->id,
                'model' => CashAdvanceReceive::class,
                'code' => $cash_advance_receive->code,
                'link' => route('admin.cash-advance-receive.show', ['cash_advance_receive' => $cash_advance_receive->id]),
            ],
            'reference_number' => $cash_advance_receive->reference,
            'date' => Carbon::parse($cash_advance_receive->date),
            'bank_code_mutation' => $cash_advance_receive->bank_code_mutation,
            'remark' =>  "$default_remark - $cash_advance_receive->reference",
            'journal_type' => "Cash Advance Receive",
            'exchange_rate' => $cash_advance_receive->exchange_rate,
            'currency_id' => $cash_advance_receive->currency_id,
            'created_by' => auth()->user()->id ?? $cash_advance_receive->created_by,
            'is_generated' => true,
            'status' => 'approve',
        ]);

        $journal->save();

        JournalDetail::where('journal_id', $journal->id)->delete();
        foreach ($cash_advance_receive->cash_advance_receive_details as $key => $value) {
            if ($value->type == "other") {
                $credit = $value->credit;
                $debit = 0;
            } else {
                $debit = $value->debit;
                $credit = $value->credit;
            }

            $journal->journal_details()->create([
                'reference_id' => $value->id,
                'reference_model' => CashAdvanceReceiveDetail::class,
                'coa_id' => $value->coa_id,
                'debit' => $debit,
                'credit' => $credit,
                'remark' => "$default_remark - $value->note",
            ]);
        }

        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * generate_receivables_payment_journal
     *
     * @return void
     */
    public function generate_receivables_payment_journal()
    {
        // get needed data
        $model = ReceivablesPayment::find($this->model_id);
        $source_data = '';

        $default_remark = "PELUNASAN PIUTANG - {$model->customer->nama} - {$model->bank_code_mutation}";

        $journal = Journal::where('reference_id', $model->id)
            ->where('reference_model', ReceivablesPayment::class)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $exchange_rate = $model->currency->is_local ? 1 : $model->exchange_rate;
        $journal->loadModel([
            'branch_id' => $model->branch_id,
            'date' => Carbon::parse($model->date),
            'bank_code_mutation' => $model->bank_code_mutation,
            'customer_id' => $model->customer_id,
            'remark' => "$default_remark - $model->note",
            'document_reference' => [
                'id' => $model->id,
                'model' => get_class($model),
                'code' => $model->code,
                'link' => route('admin.receivables-payment.show', ['receivables_payment' => $model->id]),
            ],
            'journal_type' => "Receivables Payment",
            'exchange_rate' => $exchange_rate,
            'currency_id' => $model->currency_id,
            'created_by' => auth()->user()->id ?? $model->created_by,
            'reference_model' => ReceivablesPayment::class,
            'reference_id' => $model->id,
            'is_generated' => true,
            'status' => 'approve',
            'receive_payment_id' => $model->receive_payment->id ?? null,
        ]);
        $journal->save();
        JournalDetail::where('journal_id', $journal->id)->delete();


        //INSERT CUSTOMER ACCOUNTS RECEIVABLE
        $customer_coa = CustomerCoa::where('customer_id', $model->customer_id)
            ->where('tipe', 'Account Receivable Coa')
            ->first();

        $customer_down_payment_coa = CustomerCoa::where('customer_id', $model->customer_id)
            ->where('tipe', 'Customer Deposite Coa')
            ->first();

        $cash_bank = 0;

        foreach ($model->receivables_payment_details as $key => $receivable_payment_detail) {
            $customer_receivable = $receivable_payment_detail->{'receive_amount' . $source_data};
            $customer_receivable = $customer_receivable;

            // !! CASH BANK
            $cash_bank += $receivable_payment_detail->{'receive_amount' . $source_data};
            $receive_amount_gap = $receivable_payment_detail->{'receive_amount_gap' . $source_data};

            if ($receivable_payment_detail->is_clearing == 1) {
                $journal->journal_details()->create([
                    'reference_id' => $model->id,
                    'reference_model' => ReceivablesPayment::class,
                    'coa_id' => $receivable_payment_detail->coa_id,
                    'debit' => $receive_amount_gap,
                    'credit' => 0,
                    'remark' => "$default_remark - $receivable_payment_detail->clearing_note",
                ]);

                $customer_receivable += $receive_amount_gap;
            }

            $exchange_rate_gap = $receivable_payment_detail->{'exchange_rate_gap_idr'};
            $exchange_rate_gap = $exchange_rate_gap;

            $exchange_rate_gap_coa = get_default_coa('finance', 'Exchange Rate Gap')->coa_id;
            if ($model->exchange_rate_gap_total != 0) {
                $journal->journal_details()->create([
                    'currency_id' => get_local_currency()->id,
                    'exchange_rate' => 1,
                    'reference_id' => $model->id,
                    'reference_model' => ReceivablesPayment::class,
                    'coa_id' => $exchange_rate_gap_coa,
                    'debit' => $exchange_rate_gap,
                    'credit' => 0,
                    'remark' => "$default_remark - $receivable_payment_detail->exchange_rate_gap_note",
                ]);
            }

            if ($receivable_payment_detail->invoice_parent->type == 'down_payment') {
                $invoice_down_payment = $receivable_payment_detail->invoice_parent->reference_model;
                $journal->journal_details()->create([
                    'exchange_rate' => $invoice_down_payment->exchange_rate,
                    'reference_id' => $model->id,
                    'reference_model' => ReceivablesPayment::class,
                    'coa_id' => $customer_down_payment_coa->coa_id,
                    'debit' => 0,
                    'credit' => $invoice_down_payment->down_payment * $invoice_down_payment->exchange_rate,
                    'remark' => "$default_remark - $invoice_down_payment->note",
                ]);

                foreach ($invoice_down_payment->invoice_down_payment_taxes as $key => $invoice_down_payment_tax) {
                    $journal->journal_details()->create([
                        'exchange_rate' => $invoice_down_payment->exchange_rate,
                        'reference_id' => $model->id,
                        'reference_model' => ReceivablesPayment::class,
                        'coa_id' => $invoice_down_payment_tax->tax->coa_sale_data->id,
                        'debit' => 0,
                        'credit' => $invoice_down_payment_tax->amount * $invoice_down_payment->exchange_rate,
                        'remark' => "$default_remark - {$invoice_down_payment_tax->tax->tax_name_with_percent}",
                    ]);
                }
            } else {
                $journal->journal_details()->create([
                    'exchange_rate' => $receivable_payment_detail->invoice_parent->exchange_rate,
                    'reference_id' => $model->id,
                    'reference_model' => ReceivablesPayment::class,
                    'coa_id' => $customer_coa->coa_id,
                    'debit' => 0,
                    'credit' =>  $customer_receivable,
                    'remark' => "$default_remark",
                ]);
            }
        }

        foreach ($model->receivables_payment_others as $key => $receivable_payment_other) {
            $credit = $receivable_payment_other->{'credit' . $source_data};

            $journal->journal_details()->create([
                'reference_id' => $model->id,
                'reference_model' => ReceivablesPayment::class,
                'coa_id' => $receivable_payment_other->coa_id,
                'debit' => 0,
                'credit' => $credit,
                'remark' => "$default_remark - $receivable_payment_other->note",
            ]);

            // !! CASH BANK
            // $cash_bank += $credit;
            $journal->journal_details()->create([
                'reference_id' => $model->id,
                'reference_model' => ReceivablesPayment::class,
                'coa_id' => $model->coa_id,
                'debit' => $credit,
                'credit' => 0,
                'remark' => $receivable_payment_other->note,
            ]);
        }

        // !! VENDOR ACCOUNT PAYABLE
        $vendor_coa = VendorCoa::where('vendor_id', $model->vendor_id)
            ->where('type', 'Account Payable Coa')
            ->first();

        foreach ($model->receivables_payment_vendors as $key => $receivables_payment_vendor) {
            $vendor_debt = $receivables_payment_vendor->{'amount' . $source_data} - $receivables_payment_vendor->{'receive_amount' . $source_data};
            $vendor_debt = $vendor_debt;

            // !! CASH BANK
            // $cash_bank -= $receivables_payment_vendor->{'amount' . $source_data};
            $journal->journal_details()->create([
                'reference_id' => $model->id,
                'reference_model' => ReceivablesPayment::class,
                'coa_id' => $model->coa_id,
                'debit' => 0,
                'credit' => $receivables_payment_vendor->{'amount' . $source_data},
                'remark' => $receivables_payment_vendor->note ?? "HUTANG USAHA",
            ]);

            $amount_gap = $receivables_payment_vendor->{'amount_gap' . $source_data};
            $amount_gap = $amount_gap;

            if ($receivables_payment_vendor->is_clearing == 1) {
                $journal->journal_details()->create([
                    'reference_id' => $model->id,
                    'reference_model' => ReceivablesPayment::class,
                    'coa_id' => $receivables_payment_vendor->coa_id,
                    'debit' => 0,
                    'credit' => $amount_gap,
                    'remark' => $receivables_payment_vendor->clearing_note,
                ]);

                $vendor_debt += $amount_gap;
            }

            $exchange_rate_gap = $receivables_payment_vendor->{'exchange_rate_gap_idr'};
            $exchange_rate_gap = $exchange_rate_gap;

            $exchange_rate_gap_coa = get_default_coa('finance', 'Exchange Rate Gap')->coa_id;
            if ($receivables_payment_vendor->exchange_rate_gap != 0) {
                $journal->journal_details()->create([
                    'reference_id' => $model->id,
                    'reference_model' => ReceivablesPayment::class,
                    'coa_id' => $exchange_rate_gap_coa,
                    'debit' => 0,
                    'credit' => $exchange_rate_gap,
                    'remark' => $receivables_payment_vendor->exchange_rate_gap_note,
                ]);

                // if ($exchange_rate_gap > 0) {
                //     $vendor_debt -= abs($exchange_rate_gap);
                // } else {
                //     $vendor_debt += abs($exchange_rate_gap);
                // }
            }

            $journal->journal_details()->create([
                'exchange_rate' => $receivables_payment_vendor->supplier_invoice_parent->exchange_rate,
                'reference_id' => $model->id,
                'reference_model' => ReceivablesPayment::class,
                'coa_id' => $vendor_coa->coa_id,
                'debit' => $vendor_debt,
                'credit' => 0,
                'remark' => "$default_remark - $receivables_payment_vendor->note",
            ]);
        }

        // INVOICE RETURN
        foreach ($model->receivables_payment_invoice_returns ?? [] as $key => $detail) {
            // !! CASH BANK
            $cash_bank -= $detail->{'amount' . $source_data};
            $invoice_return = $detail->{'amount' . $source_data};

            $exchange_rate_gap = $detail->{'exchange_rate_gap_idr'};
            $exchange_rate_gap = $exchange_rate_gap;

            $exchange_rate_gap_coa = get_default_coa('finance', 'Exchange Rate Gap')->coa_id;
            if ($model->exchange_rate_gap_total != 0) {
                $journal->journal_details()->create([
                    'reference_id' => $model->id,
                    'reference_model' => ReceivablesPayment::class,
                    'coa_id' => $exchange_rate_gap_coa,
                    'debit' => 0,
                    'credit' => $exchange_rate_gap,
                    'remark' => "$default_remark",
                ]);

                // if ($exchange_rate_gap > 0) {
                //     $invoice_return -= abs($exchange_rate_gap);
                // } else {
                //     $invoice_return += abs($exchange_rate_gap);
                // }
            }

            $invoice_return = $invoice_return;
            $journal->journal_details()->create([
                'exchange_rate' => $detail->invoice_return->exchange_rate,
                'reference_id' => $model->id,
                'reference_model' => ReceivablesPayment::class,
                'coa_id' => $customer_coa->coa_id,
                'debit' => $invoice_return,
                'credit' => 0,
                'remark' => "$default_remark - $detail->note",
            ]);
        }

        // !! CASH BANK
        if ($cash_bank != 0) {
            $cash_bank = $cash_bank;
            $journal->journal_details()->create([
                'reference_id' => $model->id,
                'reference_model' => ReceivablesPayment::class,
                'coa_id' => $model->coa_id,
                'debit' => $cash_bank,
                'credit' => 0,
                'remark' => "$default_remark - $model->reference",
            ]);
        }

        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * generate_supplier_invoice_general_journal
     *
     * @return void
     */
    public function generate_supplier_invoice_general_journal()
    {
        // get needed data
        $model = SupplierInvoiceGeneral::find($this->model_id);

        $default_remark = "PI NON LPB - {$model->vendor->nama}}";

        $journal = Journal::where('reference_id', $model->id)
            ->where('reference_model', SupplierInvoiceGeneral::class)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $model->branch_id,
            'date' => Carbon::parse($model->date),
            'remark' => "$default_remark - $model->reference",
            'document_reference' => [
                'id' => $model->id,
                'model' => get_class($model),
                'code' => $model->code,
                'link' => route('admin.supplier-invoice-general.show', ['supplier_invoice_general' => $model->id]),
            ],
            'reference_number' => $model->reference,
            'journal_type' => "Puchase Invoice (Non LPB)",
            'exchange_rate' => $model->exchange_rate,
            'currency_id' => $model->currency_id,
            'created_by' => auth()->user()->id ?? $model->created_by,
            'reference_model' => SupplierInvoiceGeneral::class,
            'reference_id' => $model->id,
            'is_generated' => true,
            'status' => 'approve'
        ]);
        $journal->save();

        foreach ($model->detail as $key => $value) {
            $debit = $value->debit;
            $credit = $value->credit;

            $journal->journal_details()->create([
                'reference_id' => $value->id,
                'reference_model' => SupplierInvoiceGeneralDetail::class,
                'coa_id' => $value->coa_id,
                'debit' => $debit,
                'credit' => $credit,
                'remark' => "$default_remark - $value->notes",
            ]);
        }

        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * generate_account_payable_journal
     *
     * @return void
     */
    public function generate_account_payable_journal()
    {
        // get needed data
        $model = AccountPayable::find($this->model_id);
        $source_data = '';

        $default_remark = "PEM. HUTANG - {$model->vendor->nama} - {$model->bank_code_mutation}";

        $journal = Journal::where('reference_id', $model->id)
            ->where('reference_model', AccountPayable::class)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $exchange_rate = $model->currency->is_local ? 1 : $model->exchange_rate;
        $journal->loadModel([
            'branch_id' => $model->branch_id,
            'date' => Carbon::parse($model->date),
            'remark' => "$default_remark - {$model->fund_submission->reference}",
            'bank_code_mutation' => $model->bank_code_mutation,
            'vendor_id' => $model->vendor_id,
            'document_reference' => [
                'id' => $model->id,
                'model' => get_class($model),
                'code' => $model->code,
                'link' => route('admin.account-payable.show', ['account_payable' => $model->id]),
            ],
            'journal_type' => "Account Payable",
            'exchange_rate' => $exchange_rate,
            'currency_id' => $model->currency_id,
            'created_by' => auth()->user()->id ?? $model->created_by,
            'reference_model' => AccountPayable::class,
            'reference_id' => $model->id,
            'is_generated' => true,
            'status' => 'approve',
            'send_payment_id' => $model->fund_submission->send_payment->id ?? null,
        ]);
        $journal->save();
        JournalDetail::where('journal_id', $journal->id)->delete();

        // VENDOR ACCOUNT PAYABLE
        $vendor_coa = VendorCoa::where('vendor_id', $model->vendor_id)
            ->where('type', 'Account Payable Coa')
            ->first();

        $cash_bank = 0;
        foreach ($model->account_payable_details as $key => $account_payable_detail) {
            $vendor_debt = $account_payable_detail->{'amount' . $source_data} - $account_payable_detail->{'receive_amount' . $source_data};

            // !! CASH BANK
            $cash_bank += $account_payable_detail->{'amount' . $source_data};
            $amount_gap = $account_payable_detail->{'amount_gap' . $source_data};
            $amount_gap = $amount_gap;

            if ($account_payable_detail->is_clearing == 1) {
                // kurang bayar
                $journal->journal_details()->create([
                    'reference_id' => $model->id,
                    'reference_model' => AccountPayable::class,
                    'coa_id' => $account_payable_detail->coa_id,
                    'debit' => 0,
                    'credit' => $amount_gap,
                    'remark' => "$default_remark - $account_payable_detail->clearing_note",
                ]);

                $vendor_debt += $amount_gap;
            }

            $exchange_rate_gap = $account_payable_detail->{'exchange_rate_gap_idr'};
            $exchange_rate_gap = $exchange_rate_gap;
            $exchange_rate_gap_coa = get_default_coa('finance', 'Exchange Rate Gap')->coa_id;
            if ($account_payable_detail->exchange_rate_gap != 0) {
                $journal->journal_details()->create([
                    'currency_id' => get_local_currency()->id,
                    'exchange_rate' => 1,
                    'reference_id' => $model->id,
                    'reference_model' => AccountPayable::class,
                    'coa_id' => $exchange_rate_gap_coa,
                    'debit' => 0,
                    'credit' => $exchange_rate_gap,
                    'remark' => "$default_remark - $account_payable_detail->exchange_rate_gap_note",
                ]);

                // exchange rate gap plus
                // if ($exchange_rate_gap > 0) {
                //     $vendor_debt += abs($exchange_rate_gap);
                // } else {
                //     // exchange rate gap minus
                //     $vendor_debt -= abs($exchange_rate_gap);
                // }
            }

            $journal->journal_details()->create([
                'exchange_rate' => $account_payable_detail->supplier_invoice_parent->exchange_rate,
                'reference_id' => $model->id,
                'reference_model' => AccountPayable::class,
                'coa_id' => $vendor_coa->coa_id,
                'debit' => $vendor_debt,
                'credit' => 0,
                'remark' => "$default_remark - $account_payable_detail->note",
            ]);
        }

        //INSERT CUSTOMER ACCOUNTS RECEIVABLE
        $customer_coa = CustomerCoa::where('customer_id', $model->customer_id)
            ->where('tipe', 'Account Receivable Coa')
            ->first();

        foreach ($model->account_payable_customers as $key => $account_payable_customer) {
            $customer_receivable = $account_payable_customer->{'receive_amount' . $source_data};

            // !! CASH BANK
            $cash_bank -= $account_payable_customer->{'receive_amount' . $source_data};
            $receive_amount_gap = $account_payable_customer->{'receive_amount_gap' . $source_data};
            $receive_amount_gap = $receive_amount_gap;

            if ($account_payable_customer->is_clearing == 1) {
                $journal->journal_details()->create([
                    'reference_id' => $model->id,
                    'reference_model' => AccountPayable::class,
                    'coa_id' => $account_payable_customer->coa_id,
                    'debit' => $receive_amount_gap,
                    'credit' => 0,
                    'remark' => $account_payable_customer->clearing_note,
                ]);

                $customer_receivable += $receive_amount_gap;
            }

            $exchange_rate_gap = $account_payable_customer->{'exchange_rate_gap_idr'};
            $exchange_rate_gap = $exchange_rate_gap;
            $exchange_rate_gap_coa = get_default_coa('finance', 'Exchange Rate Gap')->coa_id;
            if ($model->exchange_rate_gap_total != 0) {
                $journal->journal_details()->create([
                    'currency_id' => get_local_currency()->id,
                    'exchange_rate' => 1,
                    'reference_id' => $model->id,
                    'reference_model' => ReceivablesPayment::class,
                    'coa_id' => $exchange_rate_gap_coa,
                    'debit' => $exchange_rate_gap,
                    'credit' =>  0,
                    'remark' => $account_payable_customer->exchange_rate_gap_note,
                ]);

                // if ($exchange_rate_gap > 0) {
                //     $customer_receivable -= abs($exchange_rate_gap);
                // } else {
                //     $customer_receivable += abs($exchange_rate_gap);
                // }
            }

            $customer_receivable = $customer_receivable;
            $journal->journal_details()->create([
                'exchange_rate' => $account_payable_customer->invoice_parent->exchange_rate,
                'reference_id' => $model->id,
                'reference_model' => ReceivablesPayment::class,
                'coa_id' => $customer_coa->coa_id,
                'debit' => 0,
                'credit' => $customer_receivable,
                'remark' => "$default_remark - $account_payable_customer->note",
            ]);
        }

        // PURCHASE RETURN
        foreach ($model->account_payable_purchase_returns ?? [] as $key => $detail) {
            // !! CASH BANK
            $cash_bank -= $detail->{'amount' . $source_data};
            $purchase_return = $detail->{'amount' . $source_data};

            $exchange_rate_gap = $detail->{'exchange_rate_gap_idr'};
            $exchange_rate_gap = $exchange_rate_gap;

            $exchange_rate_gap_coa = get_default_coa('finance', 'Exchange Rate Gap')->coa_id;
            if ($model->exchange_rate_gap_total != 0) {
                $journal->journal_details()->create([
                    'reference_id' => $model->id,
                    'reference_model' => AccountPayable::class,
                    'coa_id' => $exchange_rate_gap_coa,
                    'debit' => $exchange_rate_gap,
                    'credit' => 0,
                    'remark' => "SELISIH KURS RETUR",
                ]);

                // if ($exchange_rate_gap > 0) {
                //     $purchase_return -= abs($exchange_rate_gap);
                // } else {
                //     $purchase_return += abs($exchange_rate_gap);
                // }
            }

            $journal->journal_details()->create([
                'exchange_rate' => $detail->purchase_return->exchange_rate,
                'reference_id' => $model->id,
                'reference_model' => AccountPayable::class,
                'coa_id' => $vendor_coa->coa_id,
                'debit' => 0,
                'credit' => $purchase_return,
                'remark' => "$default_remark - $detail->note",
            ]);
        }

        foreach ($model->account_payable_others as $key => $account_payable_other) {
            $debit = $account_payable_other->{'debit' . $source_data};

            $journal->journal_details()->create([
                'reference_id' => $model->id,
                'reference_model' => AccountPayable::class,
                'coa_id' => $account_payable_other->coa_id,
                'credit' => 0,
                'debit' => $debit,
                'remark' => "$default_remark - $account_payable_other->note",
            ]);

            // !! CASH BANK
            $cash_bank += $debit;
        }

        if ($cash_bank != 0) {
            $cash_bank = $cash_bank;
            $journal->journal_details()->create([
                'reference_id' => $model->id,
                'reference_model' => AccountPayable::class,
                'coa_id' => $model->coa_id,
                'credit' => $cash_bank,
                'debit' => 0,
                'remark' => "$default_remark - $model->note",
            ]);
        }


        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }


    /**
     * generate_purchase_return_journal
     *
     * @return void
     */
    public function generate_purchase_return_journal()
    {
        // get needed data
        $model = PurchaseReturn::find($this->model_id);
        $type = $model->item_receiving_report->tipe;
        if ($type == 'jasa') {
            $type = 'item-receiving-report-service';
        } elseif ($type == 'general') {
            $type = 'item-receiving-report-general';
        } elseif ($type == 'trading') {
            $type = 'item-receiving-report-trading';
        } elseif ($type == 'transport') {
            $type = 'item-receiving-report-transport';
        }

        $default_remark = "RETUR PEMBELIAN - {$model->vendor->nama} - $model->code";
        $journal = Journal::where('reference_model', PurchaseReturn::class)
            ->where('reference_id', $model->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $model->branch_id,
            'date' => Carbon::parse($model->date),
            'vendor_id' => $model->vendor_id,
            'remark' => "$default_remark - $model->reference",
            'reference_number' => $model->reference,
            'document_reference' => [
                'id' => $model->id,
                'model' => PurchaseReturn::class,
                'code' => $model->code,
                'link' => route('admin.purchase-return.show', ['purchase_return' => $model->id]),
            ],
            'reference' => [
                'id' => $model->item_receiving_report->id,
                'model' => ItemReceivingReport::class,
                'code' => $model->item_receiving_report->kode,
                'link' => route("admin.{$type}.show", $model->item_receiving_report),
            ],
            'journal_type' => "Purchase Return",
            'exchange_rate' => $model->exchange_rate,
            'currency_id' => $model->currency_id,
            'created_by' => auth()->user()->id ?? $model->created_by,
            'reference_model' => PurchaseReturn::class,
            'reference_id' => $model->id,
            'is_generated' => true,
            'status' => 'approve'
        ]);
        $journal->save();

        $group_tax = new Collection();
        $model->purchase_return_details->each(function ($purchase_return_detail) use ($group_tax) {
            $purchase_return_detail->purchase_return_taxes->each(function ($purchase_return_tax) use ($group_tax) {
                $group_tax->push($purchase_return_tax->tax);
            });
        });

        $group_tax = $group_tax->unique('id')
            ->map(function ($item) use ($model) {
                $item->total_tax = $model->purchase_return_details->sum(function ($detail) use ($item) {
                    return $detail->purchase_return_taxes->where('tax_id', $item->id)->sum('amount');
                });

                return $item;
            });

        foreach ($group_tax as $key => $tax) {
            if ($tax->type == "ppn") {
                $item_receiving_report_tax = ItemReceivingReportTax::where('reference_parent_model', get_class($model))
                    ->where('reference_parent_id', $model->id)
                    ->where('reference_model', get_class($model))
                    ->where('reference_id', $model->id)
                    ->where('tax_id', $tax->id)
                    ->first();

                if (!$item_receiving_report_tax) {
                    $item_receiving_report_tax = new ItemReceivingReportTax();
                }
                $item_receiving_report_tax->reference_parent_model = get_class($model);
                $item_receiving_report_tax->reference_parent_id = $model->id;
                $item_receiving_report_tax->reference_model = get_class($model);
                $item_receiving_report_tax->reference_id = $model->id;
                $item_receiving_report_tax->date = $model->date;
                $item_receiving_report_tax->vendor_id = $model->vendor_id;
                $item_receiving_report_tax->dpp = $model->subtotal;
                $item_receiving_report_tax->value = $tax->value;
                $item_receiving_report_tax->amount = $tax->total_tax;
                $item_receiving_report_tax->tax_id = $tax->id;
                $item_receiving_report_tax->save();

                $all_item_receiving_report_tax = ItemReceivingReportTax::where('reference_parent_model', get_class($model))
                    ->where('reference_parent_id', $model->id)
                    ->where('reference_model', get_class($model))
                    ->where('reference_id', $model->id)
                    ->where('tax_id', $tax->id)
                    ->get();

                if ($all_item_receiving_report_tax->count() > 1) {
                    $all_item_receiving_report_tax->skip(1)->each(function ($item) {
                        $item->delete();
                    });
                }
            }
        }

        $debit = 0;

        $item_category_coas = ItemCategoryCoa::with('coa')
            ->whereIn('item_category_id', $model->purchase_return_details->pluck('item.item_category_id'))
            ->get();

        $item_type_coas = ItemTypeCoa::with('coa')
            ->whereIn('item_type_id', $model->purchase_return_details->pluck('item.item_category.item_type_id'))
            ->get();

        $stock_mutations = StockMutation::where('document_model', PurchaseReturnDetail::class)
            ->where('document_id', $model->purchase_return_details->pluck('id'))
            ->get();

        foreach ($model->purchase_return_details as $key => $purchase_return_detail) {
            $item_coa = $item_category_coas->where('item_category_id', $purchase_return_detail->item->item_category_id)
                ->filter(function ($q) {
                    return strtolower($q->type) == "inventory" || strtolower($q->type) == "asset" || strtolower($q->type) == "biaya dibayar dimuka";
                })
                ->first()->coa ?? null;

            if (!$item_coa) {
                $item_coa = $item_type_coas->where('item_type_id', $purchase_return_detail->item->item_category->item_type_id)
                    ->filter(function ($q) {
                        return strtolower($q->type) == "inventory" || strtolower($q->type) == "asset" || strtolower($q->type) == "biaya dibayar dimuka";
                    })
                    ->first()->coa ?? null;
            }

            $item_coa_expense = $item_category_coas->where('item_category_id', $purchase_return_detail->item->item_category_id)
                ->filter(function ($q) {
                    return strtolower($q->type) == "expense";
                })
                ->first()->coa ?? null;

            if (!$item_coa_expense) {
                $item_coa_expense = $item_type_coas->where('item_type_id', $purchase_return_detail->item->item_category->item_type_id)
                    ->filter(function ($q) {
                        return strtolower($q->type) == "expense";
                    })
                    ->first()->coa ?? null;
            }

            $stock_mutation = $stock_mutations->where('document_id', $purchase_return_detail->id)->first();

            $journal->journal_details()->create([
                'reference_id' => $purchase_return_detail->id,
                'reference_model' => PurchaseReturnDetail::class,
                'coa_id' => $item_coa->id,
                'debit' => 0,
                'credit' => $stock_mutation->subtotal,
                'remark' => $default_remark,
            ]);

            $debit += $purchase_return_detail->subtotal;

            $gap_purchase_return_detail_and_stock_mutation =  $stock_mutation->subtotal - $purchase_return_detail->subtotal;
            if ($gap_purchase_return_detail_and_stock_mutation != 0) {
                $journal->journal_details()->create([
                    'reference_id' => $purchase_return_detail->id,
                    'reference_model' => PurchaseReturnDetail::class,
                    'coa_id' => $item_coa_expense->id,
                    'debit' => $gap_purchase_return_detail_and_stock_mutation,
                    'credit' => 0,
                    'remark' => "$default_remark - Selisih retur",
                ]);
            }

            foreach ($purchase_return_detail->purchase_return_taxes as $key => $purchase_return_tax) {
                $tax_name = $purchase_return_tax->tax->name ?? $purchase_return_tax->tax_trading->name;
                $journal->journal_details()->create([
                    'reference_id' => $purchase_return_detail->id,
                    'reference_model' => PurchaseReturnDetail::class,
                    'coa_id' => $purchase_return_tax->tax->coa_purchase_data->id ?? $purchase_return_tax->tax_trading->coa_purchase->id,
                    'debit' => 0,
                    'credit' => $purchase_return_tax->amount,
                    'remark' => "$tax_name " . ($purchase_return_tax->value * 100) . "%",
                ]);

                if ($purchase_return_tax->tax->type == "ppn") {
                    $dpp = $purchase_return_detail->subtotal * $model->exchange_rate;
                    $tax_value = $purchase_return_tax->value;

                    $item_receiving_report_tax = ItemReceivingReportTax::where('reference_parent_model', get_class($model))
                        ->where('reference_parent_id', $model->id)
                        ->where('reference_model', get_class($purchase_return_detail))
                        ->where('reference_id', $purchase_return_detail->id)
                        ->first();

                    ItemReceivingReportTax::where('reference_parent_model', get_class($model))
                        ->where('reference_parent_id', $model->id)
                        ->where('reference_model', get_class($purchase_return_detail))
                        ->where('reference_id', $purchase_return_detail->id)
                        ->when($item_receiving_report_tax, function ($q) use ($item_receiving_report_tax) {
                            $q->where('id', '!=', $item_receiving_report_tax->id);
                        })
                        ->delete();

                    if (!$item_receiving_report_tax) {
                        $item_receiving_report_tax = new ItemReceivingReportTax();
                    }
                    $item_receiving_report_tax->reference_parent_model = get_class($model);
                    $item_receiving_report_tax->reference_parent_id = $model->id;
                    $item_receiving_report_tax->reference_model = get_class($purchase_return_detail);
                    $item_receiving_report_tax->reference_id = $purchase_return_detail->id;
                    $item_receiving_report_tax->date = $model->date;
                    $item_receiving_report_tax->vendor_id = $model->vendor_id;
                    $item_receiving_report_tax->dpp = -$dpp;
                    $item_receiving_report_tax->value = $tax_value;
                    $item_receiving_report_tax->amount = -$dpp * $tax_value;
                    $item_receiving_report_tax->tax_id = $purchase_return_tax->tax_id ?? $purchase_return_tax->tax_trading_id;
                    $item_receiving_report_tax->save();
                }

                $debit += $purchase_return_tax->amount;
            }
        }

        $vendor_coa = VendorCoa::where('vendor_id', $model->vendor_id)
            ->where('type', 'Account Payable Coa')
            ->first();

        $journal->journal_details()->create([
            'reference_id' => $model->id,
            'reference_model' => PurchaseReturn::class,
            'coa_id' => $vendor_coa->coa_id,
            'debit' => $debit,
            'credit' => 0,
            'remark' => $default_remark,
        ]);


        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * generate_invoice_return_journal
     *
     * @return void
     */
    public function generate_invoice_return_journal()
    {
        // get needed data

        $model = InvoiceReturn::find($this->model_id);
        $delivery_order = $model->reference_model::find($model->reference_id);

        if ($delivery_order->type == "trading") {
            $link = route('admin.delivery-order.list-delivery-order.show', ['delivery_order_id' => $delivery_order->id, 'sale_order_id' => $delivery_order->so_trading_id]);
        } else {
            $link = route('admin.delivery-order-general.show', ['delivery_order_general' => $delivery_order->id]);
        }

        $default_remark = "RETUR PENJUALAN - {$model->customer->nama} - $model->code";
        $journal = Journal::where('reference_model', InvoiceReturn::class)
            ->where('reference_id', $model->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $model->branch_id,
            'date' => Carbon::parse($model->date),
            'remark' => "$default_remark - $model->reference",
            'reference_number' => $model->reference,
            'customer_id' => $model->customer_id,
            'document_reference' => [
                'id' => $model->id,
                'model' => InvoiceReturn::class,
                'code' => $model->code,
                'link' => route('admin.invoice-return.show', ['invoice_return' => $model->id]),
            ],
            'reference' => [
                'id' => $delivery_order->id,
                'model' => get_class($delivery_order),
                'code' => $delivery_order->code,
                'link' => $link,
            ],
            'journal_type' => "Invoice Return",
            'exchange_rate' => $model->exchange_rate,
            'currency_id' => $model->currency_id,
            'created_by' => auth()->user()->id ?? $model->created_by,
            'reference_model' => InvoiceReturn::class,
            'reference_id' => $model->id,
            'is_generated' => true,
            'status' => 'approve'
        ]);
        $journal->save();

        $tax_amount = 0;
        foreach ($model->invoice_return_details as $key => $invoice_return_detail) {
            $item_inventory_coa = ItemCategoryCoa::where('item_category_id', $invoice_return_detail->item->item_category_id)
                ->whereRaw('LOWER(type) = ?', ['inventory'])
                ->first()->coa ?? null;

            if (!$item_inventory_coa) {
                $item_inventory_coa = ItemTypeCoa::where('item_type_id', $invoice_return_detail->item->item_category->item_type_id)
                    ->where('type', 'Inventory')
                    ->first()->coa;
            }

            $item_hpp_coa = $invoice_return_detail->item->item_category->item_category_coas
                ->filter(function ($query) {
                    return strtolower($query->type) == 'hpp';
                })
                ->first()->coa ?? null;

            if (!$item_hpp_coa) {
                $item_hpp_coa = $invoice_return_detail->item->item_category->item_category_coas
                    ->filter(function ($query) {
                        return strtolower($query->type) == 'hpp';
                    })
                    ->first()->coa;
            }

            $hpp_total = $invoice_return_detail->hpp_total;
            if ($hpp_total != 0) {
                $hpp_total /= $model->exchange_rate;
            }
            $hpp_total = $hpp_total;

            // INVENTORY COA
            $journal->journal_details()->create([
                'reference_id' => $invoice_return_detail->id,
                'reference_model' => InvoiceReturnDetail::class,
                'coa_id' => $item_inventory_coa->id,
                'debit' => $hpp_total,
                'credit' => 0,
                'remark' => "$default_remark",
            ]);

            // HPP COA
            $journal->journal_details()->create([
                'reference_id' => $invoice_return_detail->id,
                'reference_model' => InvoiceReturnDetail::class,
                'coa_id' => $item_hpp_coa->id,
                'debit' => 0,
                'credit' => $hpp_total,
                'remark' => $default_remark,
            ]);

            // TAX COA
            foreach ($invoice_return_detail->invoice_return_taxes as $key => $invoice_return_tax) {
                $journal->journal_details()->create([
                    'reference_id' => $invoice_return_detail->id,
                    'reference_model' => InvoiceReturnDetail::class,
                    'coa_id' => $invoice_return_tax->tax->coa_sale_data->id,
                    'debit' => $invoice_return_tax->amount,
                    'credit' => 0,
                    'remark' => "$default_remark - {$invoice_return_tax->tax->tax_name_with_percent}",
                ]);
                $tax_amount += $invoice_return_tax->amount;

                if ($invoice_return_tax->tax->type == 'ppn') {
                    $dpp = $invoice_return_detail->subtotal * $model->exchange_rate;
                    $tax_value = $invoice_return_tax->value;

                    $invoice_tax = InvoiceTax::where('reference_model', get_class($invoice_return_detail))
                        ->where('reference_id', $invoice_return_detail->id)
                        ->where('reference_parent_model', get_class($model))
                        ->where('reference_parent_id', $model->id)
                        ->first();

                    InvoiceTax::where('reference_model', get_class($invoice_return_detail))
                        ->where('reference_id', $invoice_return_detail->id)
                        ->where('reference_parent_model', get_class($model))
                        ->where('reference_parent_id', $model->id)
                        ->when($invoice_tax, function ($q) use ($invoice_tax) {
                            $q->where('id', '!=', $invoice_tax->id);
                        })
                        ->delete();

                    if (!$invoice_tax) {
                        $invoice_tax = new InvoiceTax();
                    }
                    $invoice_tax->loadModel(
                        [
                            'reference_model' => get_class($invoice_return_detail),
                            'reference_id' => $invoice_return_detail->id,
                            'reference_parent_model' => get_class($model),
                            'reference_parent_id' => $model->id,
                            'date' => Carbon::parse($model->date),
                            'customer_id' => $model->customer_id,
                            'tax_id' => $invoice_return_tax->tax_id,
                            'dpp' => -$dpp,
                            'value' => $tax_value,
                            'amount' => -$dpp * $tax_value,
                        ]
                    );
                    $invoice_tax->save();
                }
            }

            // SALES RETURN
            $item_sales_return_coa = $invoice_return_detail->item->item_category->item_category_coas
                ->filter(function ($query) {
                    return strtolower($query->type) == 'sales return';
                })
                ->first()->coa ?? null;

            if (!$item_sales_return_coa) {
                $item_sales_return_coa = $invoice_return_detail->item->item_category->item_category_coas
                    ->filter(function ($query) {
                        return strtolower($query->type) == 'sales return';
                    })
                    ->first()->coa;
            }

            $journal->journal_details()->create([
                'reference_id' => $model->id,
                'reference_model' => InvoiceReturn::class,
                'coa_id' => $item_sales_return_coa->id,
                'debit' => $invoice_return_detail->subtotal,
                'credit' => 0,
                'remark' => "$default_remark - {$invoice_return_detail->item->nama}",
            ]);
        }

        $customer_coa = CustomerCoa::where('customer_id', $model->customer_id)
            ->where('tipe', 'Account Receivable Coa')
            ->first();

        // CUSTOMER RECEIVABLE
        $journal->journal_details()->create([
            'reference_id' => $model->id,
            'reference_model' => InvoiceReturn::class,
            'coa_id' => $customer_coa->coa_id,
            'debit' => 0,
            'credit' => $model->total,
            'remark' =>  $default_remark,
        ]);

        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * generate_depreciation_journal
     *
     * @return void
     */
    public function generate_depreciation_journal()
    {
        // get needed data
        $model = Depreciation::find($this->model_id);

        $default_remark = "DEPRESIASI - {$model->asset->asset_name}";

        $journal = Journal::where('reference_model', Depreciation::class)
            ->where('reference_id', $model->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $model->branch_id,
            'date' => Carbon::parse($model->date),
            'remark' => "$default_remark - $model->note",
            'reference_number' => $model->reference,
            'document_reference' => [
                'id' => $model->id,
                'model' => Depreciation::class,
                'code' => $model->note,
                'link' => route('admin.depreciation.show', ['depreciation' => $model->id]),
            ],
            'reference' => [
                'id' => $model->asset->id,
                'model' => Asset::class,
                'code' => $model->asset->asset_name,
                'link' => route('admin.asset.show', ['asset' => $model->asset->id]),
            ],
            'journal_type' => "Depreciation",
            'exchange_rate' => 1,
            'currency_id' => get_local_currency()->id,
            'created_by' => auth()->user()->id ?? $model->created_by,
            'reference_model' => Depreciation::class,
            'reference_id' => $model->id,
            'is_generated' => true,
            'status' => 'approve'
        ]);
        $journal->save();

        // DEPRECIATION COST
        $journal->journal_details()->create([
            'reference_id' => $model->id,
            'reference_model' => Depreciation::class,
            'coa_id' => $model->asset->depreciation_coa_id,
            'debit' => $model->amount,
            'credit' => 0,
            'remark' => "$default_remark - $model->note",
        ]);

        // ACUMULATED DEPRECIATION
        $journal->journal_details()->create([
            'reference_id' => $model->id,
            'reference_model' => Depreciation::class,
            'coa_id' => $model->asset->acumulated_depreciation_coa_id,
            'debit' => 0,
            'credit' => $model->amount,
            'remark' => "$default_remark - $model->note",
        ]);

        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * generate_disposition_journal
     *
     * @return void
     */
    public function generate_disposition_journal()
    {
        // get needed data
        $model = Disposition::find($this->model_id);
        $gain_loss =  $model->selling_price - $model->asset->outstanding_value;

        $default_remark = "DISPOSISI ASET - {$model->asset->asset_name}";

        $journal = Journal::where('reference_model', Disposition::class)
            ->where('reference_id', $model->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $model->branch_id,
            'date' => Carbon::parse($model->date),
            'remark' => "$default_remark - $model->note",
            'document_reference' => [
                'id' => $model->id,
                'model' => Disposition::class,
                'code' => $model->note,
                'link' => route('admin.disposition.show', ['disposition' => $model->id]),
            ],
            'reference' => [
                'id' => $model->asset->id,
                'model' => Asset::class,
                'code' => $model->asset->asset_name,
                'link' => route('admin.asset.show', ['asset' => $model->asset->id]),
            ],
            'journal_type' => "Disposition",
            'exchange_rate' => 1,
            'currency_id' => get_local_currency()->id,
            'created_by' => auth()->user()->id ?? $model->created_by,
            'reference_model' => Disposition::class,
            'reference_id' => $model->id,
            'is_generated' => true,
            'status' => 'approve'
        ]);
        $journal->save();

        // ASSET VALUE
        $journal->journal_details()->create([
            'reference_id' => $model->id,
            'reference_model' => Disposition::class,
            'coa_id' => $model->asset->asset_coa_id,
            'debit' => 0,
            'credit' => $model->asset->value,
            'remark' => $default_remark,
        ]);

        // ACUMULATED DEPRECIATION
        $journal->journal_details()->create([
            'reference_id' => $model->id,
            'reference_model' => Disposition::class,
            'coa_id' => $model->asset->acumulated_depreciation_coa_id,
            'debit' => $model->asset->depreciations->sum('amount'),
            'credit' => 0,
            'remark' => $default_remark,
        ]);

        if ($model->is_selling_asset == 1) {
            // SALE
            $journal->journal_details()->create([
                'reference_id' => $model->id,
                'reference_model' => Disposition::class,
                'coa_id' => $model->selling_coa_id,
                'debit' =>  $model->total,
                'credit' => 0,
                'remark' => $default_remark,
            ]);

            if ($model->tax_amount != 0) {
                $journal->journal_details()->create([
                    'reference_id' => $model->id,
                    'reference_model' => Disposition::class,
                    'coa_id' => $model->tax->coa_sale,
                    'debit' =>  0,
                    'credit' => $model->tax_amount,
                    'remark' => "$default_remark - {$model->tax->tax_name_with_percent}",
                ]);
            }
        }

        if ($gain_loss  != 0) {
            // GAIN LOSS
            $journal->journal_details()->create([
                'reference_id' => $model->id,
                'reference_model' => Disposition::class,
                'coa_id' => $model->gain_loss_coa_id,
                'debit' =>  0,
                'credit' => $gain_loss,
                'remark' => $default_remark,
            ]);
        }

        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * generate_tax_reconciliation_journal
     *
     * @return void
     */
    public function generate_tax_reconciliation_journal()
    {
        // get needed data
        $model = TaxReconciliation::find($this->model_id);

        $default_remark = "REKONSILIASI PAJAK - {$model->code}";

        $journal = Journal::where('reference_model', TaxReconciliation::class)
            ->where('reference_id', $model->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $model->branch_id,
            'date' => Carbon::parse($model->date),
            'remark' => $default_remark,
            'document_reference' => [
                'id' => $model->id,
                'model' => TaxReconciliation::class,
                'code' => $model->code,
                'link' => route('admin.tax-reconciliation.show', ['tax_reconciliation' => $model->id]),
            ],
            'journal_type' => "Tax Reconciliation",
            'exchange_rate' => 1,
            'currency_id' => get_local_currency()->id,
            'created_by' => auth()->user()->id ?? $model->created_by,
            'reference_model' => TaxReconciliation::class,
            'reference_id' => $model->id,
            'is_generated' => true,
            'status' => 'approve'
        ]);
        $journal->save();

        $ppn_masukan = 0;
        $ppn_keluaran = 0;
        foreach ($model->tax_reconciliation_details as $key => $detail) {
            $coa_purchase = $detail->tax->coa_purchase_data;
            $coa_sale = $detail->tax->coa_sale_data;

            if ($detail->type == "purchase-tax") {
                $ppn_masukan += $detail->used_amount;
            } else {
                $ppn_keluaran += $detail->used_amount;
            }
        }

        $ppn_keluaran = $ppn_keluaran;
        $ppn_masukan = $ppn_masukan;

        $journal->journal_details()->create([
            'reference_id' => $detail->id,
            'reference_model' => TaxReconciliationDetail::class,
            'coa_id' => $coa_purchase->id,
            'debit' => 0,
            'credit' => $ppn_masukan,
            'remark' => $default_remark,
        ]);

        if ($model->coa_id) {
            $journal->journal_details()->create([
                'reference_id' => $detail->id,
                'reference_model' => TaxReconciliationDetail::class,
                'coa_id' => $model->coa_id,
                'debit' => $ppn_masukan,
                'credit' => 0,
                'remark' => $default_remark,
            ]);
        }

        $journal->journal_details()->create([
            'reference_id' => $detail->id,
            'reference_model' => TaxReconciliationDetail::class,
            'coa_id' => $coa_sale->id,
            'debit' => $ppn_keluaran,
            'credit' => 0,
            'remark' => $default_remark,
        ]);

        if ($model->coa_id) {
            $journal->journal_details()->create([
                'reference_id' => $detail->id,
                'reference_model' => TaxReconciliationDetail::class,
                'coa_id' => $model->coa_id,
                'debit' => 0,
                'credit' => $ppn_keluaran,
                'remark' => $default_remark,
            ]);
        }
        // // GAP
        // if ($model->gap != 0 && $model->coa) {
        //     if ($model->gap > 0) {
        //         $journal->journal_details()->create([
        //             'reference_id' => $model->id,
        //             'reference_model' => TaxReconciliation::class,
        //             'coa_id' => $model->coa_id,
        //             'debit' => abs($model->gap),
        //             'credit' => 0,
        //             'remark' => "LEBIH BAYAR",
        //         ]);
        //     } else {
        //         $journal->journal_details()->create([
        //             'reference_id' => $model->id,
        //             'reference_model' => TaxReconciliation::class,
        //             'coa_id' => $model->coa_id,
        //             'debit' => 0,
        //             'credit' => abs($model->gap),
        //             'remark' => "KURANG BAYAR",
        //         ]);
        //     }
        // }
        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * generate_amortization_journal
     *
     * @return void
     */
    public function generate_amortization_journal()
    {
        // get needed data
        $model = Amortization::find($this->model_id);

        // get amortization before

        $default_remark = "AMORTISASI - {$model->lease->lease_name}";

        $journal = Journal::where('reference_model', Amortization::class)
            ->where('reference_id', $model->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $model->branch_id,
            'date' => Carbon::parse($model->date),
            'remark' => "$default_remark - $model->note",
            'reference_number' => $model->reference,
            'document_reference' => [
                'id' => $model->id,
                'model' => Amortization::class,
                'code' => $model->note,
                'link' => route('admin.amortization.show', ['amortization' => $model->id]),
            ],
            'reference' => [
                'id' => $model->lease->id,
                'model' => Lease::class,
                'code' => $model->lease->lease_name,
                'link' => route('admin.lease.show', ['lease' => $model->lease->id]),
            ],
            'journal_type' => "Amortization",
            'exchange_rate' => 1,
            'currency_id' => get_local_currency()->id,
            'created_by' => auth()->user()->id ?? $model->created_by,
            'reference_model' => Amortization::class,
            'reference_id' => $model->id,
            'is_generated' => true,
            'status' => 'approve'
        ]);
        $journal->save();

        // AMORTIZATION COST
        $journal->journal_details()->create([
            'reference_id' => $model->id,
            'reference_model' => Amortization::class,
            'coa_id' => $model->lease->depreciation_coa_id,
            'debit' => $model->amount,
            'credit' => 0,
            'remark' => "$default_remark - $model->note",
        ]);

        // ACUMULATED AMORTIZATION
        $journal->journal_details()->create([
            'reference_id' => $model->id,
            'reference_model' => Amortization::class,
            'coa_id' => $model->lease->acumulated_depreciation_coa_id,
            'debit' => 0,
            'credit' => $model->amount,
            'remark' => "$default_remark - $model->note",
        ]);

        // * update total credit and debit
        $journal->update([
            'credit_total' => $journal->journal_details()->sum('credit'),
            'debit_total' => $journal->journal_details()->sum('debit'),
        ]);
    }

    /**
     * generate_delivery_order_general_losses_journal
     *
     * @return void
     */
    public function generate_delivery_order_general_losses_journal()
    {
        $is_exists_gap = DeliveryOrderGeneralDetail::where('delivery_order_general_id', $this->model_id)
            ->whereColumn('quantity', '>', 'quantity_received')
            ->exists();

        if (!$is_exists_gap) {
            Journal::where('reference_id', $this->model_id)
                ->where('reference_model', \App\Models\DeliveryOrderGeneral::class)
                ->where('journal_type', 'Delivery Order General Losses')
                ->delete();

            StockMutation::where('type', 'delivery order general losses')
                ->where('document_model', DeliveryOrderGeneralDetail::class)
                ->whereIn('document_id', function ($query) {
                    $query->select('id')
                        ->from('delivery_order_general_details')
                        ->where('delivery_order_general_id', $this->model_id);
                })
                ->delete();

            return;
        }

        // * get needed data
        $losses_coa = get_default_coa('finance', 'Losess DO General');
        $default_warehouse = WareHouse::where('nama', 'like', '%Gudang Reject%')->first();

        if (!$losses_coa || !$default_warehouse) {
            throw new \Exception("Losses COA or Default Warehouse not found");
        }

        $delivery_order = \App\Models\DeliveryOrderGeneral::find($this->model_id);
        $sale_order_general = \App\Models\SaleOrderGeneral::find($delivery_order->sale_order_general_id);

        // * create journal
        $journal = \App\Models\Journal::where('reference_id', $delivery_order->id)
            ->where('reference_model', \App\Models\DeliveryOrderGeneral::class)
            ->where('journal_type', 'Delivery Order General Losses')
            ->first();

        $default_remark = "DO GENERAL LOSSES - {$delivery_order->code}";

        if (!$journal) {
            $journal = new \App\Models\Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $delivery_order->branch_id,
            'reference_id' => $delivery_order->id,
            'reference_model' => \App\Models\DeliveryOrderGeneral::class,
            'customer_id' => $delivery_order->vendor_id,
            'document_reference' => [
                'id' => $delivery_order->id,
                'model' => DeliveryOrderGeneral::class,
                'code' => $delivery_order->code,
                'link' => route('admin.delivery-order-general.show', ['delivery_order_general' => $delivery_order->id]),
            ],
            'reference' => [
                'id' => $sale_order_general->id,
                'model' => SaleOrderGeneral::class,
                'code' => $sale_order_general->kode,
                'link' => route('admin.sales-order-general.show', ['sales_order_general' => $sale_order_general->id]),
            ],
            'reference_number' => $delivery_order->external_code,
            'date' => $delivery_order->date,
            'exchange_rate' => 1,
            'currency_id' => get_local_currency()->id,
            'journal_type' => "Delivery Order General Losses",
            'remark' => $default_remark,
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        $credit_total = 0;
        $debit_total = 0;

        /**
         * ! ==================================================================================================================
         * ! create journal details
         * ! ==================================================================================================================
         *
         */

        JournalDetail::where('journal_id', $journal->id)->delete();

        foreach ($delivery_order->delivery_order_general_details as $delivery_order_detail) {
            // * item
            $item = $delivery_order_detail->item;
            $item_type = $item->item_category->item_type;
            $item_category = $item->item_category;

            if ($item_type->nama == 'purchase item') {
                // * item sended to customer
                $item_sended = $delivery_order_detail->quantity;
                $item_received = $delivery_order_detail->quantity_received;
                $item_gap = $item_sended - $item_received;

                $item_price = $delivery_order_detail->hpp;

                if ($item_gap > 0) {
                    // ? item inventory
                    $journal->journal_details()->create([
                        'reference_id' => $delivery_order_detail->id,
                        'reference_model' => DeliveryOrderGeneralDetail::class,
                        'coa_id' => $losses_coa->coa_id,
                        'credit' => 0,
                        'debit' => $item_gap * $item_price,
                        'remark' => "$default_remark - $item->nama"
                    ]);

                    $debit_total += $item_gap * $item_price;

                    foreach ($item_category->item_category_coas as $item_category_coa) {

                        // ? hpp
                        if (strtolower($item_category_coa->type) == 'hpp') {
                            $journal->journal_details()->create([
                                'reference_id' => $delivery_order_detail->id,
                                'reference_model' => DeliveryOrderGeneralDetail::class,
                                'coa_id' => $item_category_coa->coa_id,
                                'credit' => $item_gap * $item_price,
                                'debit' => 0,
                                'remark' => "$default_remark - $item->nama"
                            ]);

                            $credit_total += $item_gap * $item_price;
                        }
                    }

                    $stock_mutation = StockMutation::where('type', 'delivery order general losses')
                        ->where('document_model', DeliveryOrderGeneralDetail::class)
                        ->where('document_id', $delivery_order_detail->id)
                        ->first();

                    if (!$stock_mutation) {
                        $stock_mutation = new StockMutation();
                    }
                    $stock_mutation->loadModel([
                        'ware_house_id' => $default_warehouse->id,
                        'item_id' => $delivery_order_detail->item_id,
                        'price_id' => $delivery_order_detail->id,
                        'document_model' => DeliveryOrderGeneralDetail::class,
                        'document_id' => $delivery_order_detail->id,
                        'date' => $delivery_order->date_send,
                        'document_code' => $delivery_order->code,
                        'type' => 'delivery order general losses',
                        'in' => $item_gap,
                        'note' => $default_remark,
                        'price_unit' => $delivery_order_detail->hpp,
                        'subtotal' => $delivery_order_detail->hpp * $item_gap,
                    ]);

                    $stock_mutation->save();
                } else {
                    // * if item gap less than equal to 0, delete stock mutation
                    StockMutation::where('type', 'delivery order general losses')
                        ->where('document_model', DeliveryOrderGeneralDetail::class)
                        ->where('document_id', $delivery_order_detail->id)
                        ->delete();

                    JournalDetail::where('journal_id', $journal->id)
                        ->where('reference_model', DeliveryOrderGeneralDetail::class)
                        ->where('reference_id', $delivery_order_detail->id)
                        ->delete();
                }
            }
        }

        /**
         * ! ==================================================================================================================
         * ! end create journal details
         * ! ==================================================================================================================
         *
         */

        // * update journal credit and debit
        $journal->update([
            'debit_total' => $debit_total,
            'credit_total' => $credit_total
        ]);
    }

    /**
     * generate invoice trading journal
     *
     * @return void
     */
    public function generate_supplier_invoice_journal()
    {
        $supplier_invoice = SupplierInvoice::find($this->model_id);
        $vendor_payable_coa = VendorCoa::where('vendor_id', $supplier_invoice->vendor_id)
            ->where('type', 'Account Payable Coa')
            ->first();
        $vendor_deposit_coa = VendorCoa::where('vendor_id', $supplier_invoice->vendor_id)
            ->where('type', 'Vendor Deposite Coa')
            ->first();

        $default_remark = "Purchase Invoice - $supplier_invoice->code";

        // * create journal
        $journal = Journal::where('reference_model', SupplierInvoice::class)
            ->where('reference_id', $supplier_invoice->id)
            ->first();

        if (!$journal) {
            $journal = new Journal();
        } else {
            $journal->journal_details()->delete();
        }

        $journal->loadModel([
            'branch_id' => $supplier_invoice->branch_id,
            'reference_id' => $supplier_invoice->id,
            'reference_model' => \App\Models\SupplierInvoice::class,
            'vendor_id' => $supplier_invoice->vendor_id,
            'document_reference' => [
                'id' => $supplier_invoice->id,
                'model' => SupplierInvoice::class,
                'code' => $supplier_invoice->code,
                'link' => route('admin.supplier-invoice.show', ['supplier_invoice' => $supplier_invoice->id]),
            ],
            'reference' => [
                'id' => $supplier_invoice->id,
                'model' => SupplierInvoice::class,
                'code' => $supplier_invoice->code,
                'link' => route('admin.supplier-invoice.show', ['supplier_invoice' => $supplier_invoice->id]),
            ],
            'date' => $supplier_invoice->date,
            'exchange_rate' => $supplier_invoice->exchange_rate,
            'currency_id' => $supplier_invoice->currency_id,
            'journal_type' => "Supplier Invoice",
            'remark' => $default_remark,
            'status' => 'approve',
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            throw new Exception("Failed to create journal for invoice trading $supplier_invoice->code", 1);
        }

        /**
         * ! ==================================================================================================================
         * ! create journal details
         * ! ==================================================================================================================
         *
         */

        $down_payment_taxes = new Collection();
        $dpp = $supplier_invoice->sub_total;

        foreach ($supplier_invoice->supplier_invoice_down_payments as $key => $supplier_invoice_down_payment) {
            $cash_advance = $supplier_invoice_down_payment->cash_advance_payment;
            $cash_advance_cash_advance =  $cash_advance->cash_advance_payment_details->where('type', 'cash_advance')->first();
            $cash_advance_tax =  $cash_advance->cash_advance_payment_details->where('type', 'tax')->first();
            $item_receiving_report = $supplier_invoice->detail->filter(function ($query) use ($cash_advance) {
                return $query->item_receiving_report->reference->purchase_id == $cash_advance->purchase_id;
            })->first()->item_receiving_report_id ?? null;


            if ($supplier_invoice_down_payment->cash_advance_payment->tax) {
                $dpp -= $cash_advance_cash_advance->debit;
                $push_down_payment_tax = new stdClass();
                $push_down_payment_tax->tax_id = $supplier_invoice_down_payment->cash_advance_payment->tax_id;
                $push_down_payment_tax->amount = $cash_advance_tax->debit;

                $down_payment_taxes->push($push_down_payment_tax);
            }

            // HUTANG
            $journal->journal_details()->create([
                'coa_id' => $vendor_payable_coa->coa_id,
                'debit' => $cash_advance_cash_advance->debit + ($cash_advance_tax->debit ?? 0),
                'credit' => 0,
                'remark' => $default_remark,
            ]);

            // UANG MUKA
            $journal->journal_details()->create([
                'exchange_rate' => $cash_advance->exchange_rate,
                'coa_id' => $vendor_deposit_coa->coa_id,
                'debit' => 0,
                'credit' => $cash_advance_cash_advance->debit,
                'remark' => $default_remark,
            ]);

            if ($supplier_invoice_down_payment->cash_advance_payment->tax) {
                $tax = $supplier_invoice_down_payment->cash_advance_payment->tax;
                // PPN
                $journal->journal_details()->create([
                    'exchange_rate' => $cash_advance->exchange_rate,
                    'coa_id' => $tax->coa_purchase_data->id,
                    'debit' => 0,
                    'credit' => ($cash_advance_tax->debit ?? 0),
                    'remark' => "$default_remark - $tax->tax_name_with_percent",
                ]);
            }

            // exchange rate gap
            $rate_gap = ($supplier_invoice->exchange_rate - $supplier_invoice_down_payment->cash_advance_payment->exchange_rate) * ($cash_advance_cash_advance->debit ?? 0);
            if ($rate_gap != 0) {
                $exchange_rate_gap_coa = get_default_coa('finance', 'Exchange Rate Gap')->coa_id;
                $journal->journal_details()->create([
                    'currency_id' => get_local_currency()->id,
                    'exchange_rate' => 1,
                    'reference_id' => $supplier_invoice_down_payment->id,
                    'reference_model' => get_class($supplier_invoice_down_payment),
                    'coa_id' => $exchange_rate_gap_coa,
                    'credit' => $rate_gap,
                    'remark' => $default_remark,
                ]);
            }

            SupplierInvoicePayment::updateOrCreate([
                'supplier_invoice_model' => SupplierInvoice::class,
                'supplier_invoice_id' => $supplier_invoice->id,
                'model' => get_class($supplier_invoice_down_payment),
                'reference_id' => $supplier_invoice_down_payment->id
            ], [
                'item_receiving_report_id' => $item_receiving_report,
                'supplier_invoice_model' => SupplierInvoice::class,
                'supplier_invoice_id' => $supplier_invoice->id,
                'model' => get_class($supplier_invoice_down_payment),
                'reference_id' => $supplier_invoice_down_payment->id,

                'currency_id' => $supplier_invoice_down_payment->cash_advance_payment->currency_id,
                'exchange_rate' => $supplier_invoice->exchange_rate,
                'date' => $supplier_invoice->date,
                'amount_to_pay' => 0,
                'pay_amount' => $cash_advance_cash_advance->debit + ($cash_advance_tax->debit ?? 0),
                'note' => $supplier_invoice_down_payment->cash_advance_payment->reference,
            ]);

            $supplier_invoice_down_payment->cash_advance_payment->update([
                'returned_amount' => $cash_advance_cash_advance->debit
            ]);
        }

        $supplier_invoice_tax_summaries = $supplier_invoice->supplier_invoice_tax_summaries->map(function ($item) use ($down_payment_taxes) {
            $tax_down_payment = $down_payment_taxes->where('tax_id', $item->tax_id)->sum('amount');
            $item->final_amount = ($item->tax_amount - $tax_down_payment);

            return $item;
        });

        foreach ($supplier_invoice_tax_summaries as $key => $value) {
            if ($value->tax->type == 'ppn') {
                $supplier_tax = new ItemReceivingReportTax();
                $supplier_tax->loadModel(
                    [
                        'reference_model' => SupplierInvoice::class,
                        'reference_id' => $supplier_invoice->id,
                        'reference_parent_model' => SupplierInvoice::class,
                        'reference_parent_id' => $supplier_invoice->id,
                        'date' => Carbon::parse($supplier_invoice->date),
                        'vendor_id' => $supplier_invoice->vendor_id,
                        'tax_id' => $value->tax_id,
                        'dpp' => ($dpp * $supplier_invoice->exchange_rate),
                        'value' => $value->tax_value,
                        'amount' => ($value->final_amount * $supplier_invoice->exchange_rate),
                    ]
                );
                $supplier_tax->save();
            }
        }

        /**
         * ! ==================================================================================================================
         * ! end create journal details
         * ! ==================================================================================================================
         *
         */

        //  * update journal credit and debit
        $journal->update([
            'debit_total' => $journal->journal_details()->sum('debit'),
            'credit_total' => $journal->journal_details()->sum('credit'),
        ]);
    }
}
