<?php

namespace App\Services;

use App\Http\Helpers\ControllerHelper;
use App\Models\Purchase;
use App\Models\PurchaseOrderGeneral;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PurchaseOrderGeneralService
{
    use ControllerHelper;

    /**
     * Create a new PurchaseOrder
     */
    public function create(Request $request)
    {
        if ($request->type == 'purchase-request') {
            return $this->createPurchaseOrderPurchaseRequest($request);
        } elseif ($request->type == 'sales-order') {
            return $this->createPurchaseOrderSalesOrder($request);
        } else {
            throw new \Exception("Tipe tidak ditemukan");
        }
    }

    /**
     * Update a PurchaseOrder
     */
    public function update(Request $request, $id)
    {
        $model = \App\Models\PurchaseOrderGeneral::findOrFail($id);

        if ($request->type == 'purchase-request') {
            return $this->updatePurchaseOrderPurchaseRequest($request, $id);
        } elseif ($request->type == 'sales-order') {
            return $this->updatePurchaseOrderSalesOrder($request, $id);
        } else {
            throw new \Exception("Tipe tidak ditemukan");
        }
    }

    /**
     * Create a new PurchaseOrder from purchase request
     */
    private function createPurchaseOrderPurchaseRequest(Request $request)
    {
        $data = json_decode($request->values);

        // Check main quantity not NaN and 0
        if (is_array($request->main_quantity)) {
            foreach ($request->main_quantity as $quantity) {
                if ($quantity == 0) {
                    throw new \Exception("Jumlah tidak boleh sama dengan 0");
                } elseif ($quantity == "NaN") {
                    throw new \Exception("Jumlah tidak boleh string");
                }
            }
        }

        // ? CALCULATION VARIABLES
        $total = 0;
        $total_main = 0;
        $total_additional = 0;
        $total_tax_main = 0;
        $total_tax_additional = 0;
        // ? CALCULATION VARIABLES

        try {
            // ! CREATE DATA PURCHASE ORDER GENERAL

            // ? CREATE PURCHASES
            $purchase = new \App\Models\Purchase();
            $purchase->fill([
                'kode',
                'tanggal' => Carbon::parse($data->date),
                'tipe' => 'general',
                'model_reference' => \App\Models\PurchaseOrderGeneral::class,
                'status' => 'pending',
                'branch_id' => $data->branch_id ?? Auth::user()->branch_id,
                'vendor_id' => $data->vendor_id,
            ]);

            if (!$purchase->check_available_date) {
                throw new \Exception("Tanggal yang dipilih telah closing");
            }

            $purchase->save();

            // ? CREATE PARENT DATA #########################################################
            $model = new \App\Models\PurchaseOrderGeneral();
            $model->fill([
                'purchase_id' => $purchase->id,
                'branch_id' => $data->branch_id ?? Auth::user()->branch_id,
                'vendor_id' => $data->vendor_id,
                'currency_id' => $data->currency_id,
                'type' => 'purchase-request',
                'date' => Carbon::parse($data->date),
                'quotation' => $request->hasFile('quotation') ? $this->upload_file($request->file('quotation'), 'purchase-order-general') : null,
                'term_of_payment' => $data->term_of_payment,
                'term_of_payment_days' => $data->term_of_payment_days,
                'payment_description' => $request->payment_description,
                'exchange_rate' => thousand_to_float($data->exchange_rate),
                'is_include_tax' => $request->is_include_tax ?? 0,
            ]);

            $model->save();

            $purchase->currency_id = $model->currency_id;
            $purchase->model_id = $model->id;
            $purchase->save();
            // ? / END CREATE PARENT DATA #########################################################

            // ? CREATE FROM PURCHASE REQUEST #########################################################
            foreach ($data->main as $key => $value) {
                if (!is_null($value)) {
                    // ? calculation variable for each purchase request
                    $single_purchase_request_sub_total = 0;
                    $single_purchase_request_sub_total_after_tax = 0;
                    $single_purchase_request_amount_discount = 0;
                    $single_purchase_request_tax_total = 0;
                    $single_purchase_request_total = 0;
                    // ? calculation variable for each purchase request

                    // * find purchase request
                    $model_purchase_request = \App\Models\PurchaseRequest::findOrFail($value->purchase_request_id);
                    $purchase_request_done_count = $model_purchase_request->purchase_request_details->whereNotIn('status', ['reject', 'pending'])->where('status', 'done')->count();
                    $model_purchase_request_count_done = $model_purchase_request->purchase_request_details->whereNotIn('status', ['reject', 'pending'])->count();

                    // * validate date purchase request
                    $purchase_request = \App\Models\PurchaseRequest::find($value->purchase_request_id);
                    $datePurchaseRequest = \Carbon\Carbon::parse($purchase_request->tanggal);
                    $datePurchaseOrder = \Carbon\Carbon::parse($data->date);

                    // * validate branch
                    if (auth()->user()->branch->is_primary == 0) {
                        if ($model->branch_id != $model_purchase_request->branch_id) {
                            throw new \Exception("Branch PR {$model_purchase_request->kode} dan branch PO tidak sama");
                        }
                    }

                    // ! purchase request date greaten than date purchase order
                    if ($datePurchaseRequest->gt($datePurchaseOrder)) {
                        throw new \Exception("Tanggal PR {$model_purchase_request->kode} tidak boleh lebih besar dari tanggal PO");
                    }

                    // * create child data
                    $model_child = new \App\Models\PurchaseOrderGeneralDetail();
                    $model_child->fill([
                        'purchase_order_general_id' => $model->id,
                        'purchase_request_id' => $value->purchase_request_id,
                        'type' => 'main',
                    ]);

                    $model_child->save();

                    // * create child item
                    foreach ($value->purchase_request_detail_id as $key2 => $value2) {
                        if (!is_null($value2)) {
                            $single_purchase_request_detail_sub_total = $value2->quantity * $value2->price;
                            $single_purchase_request_detail_sub_total = $single_purchase_request_detail_sub_total;
                            $single_purchase_request_detail_sub_total_after_tax = $single_purchase_request_detail_sub_total;

                            $single_purchase_request_detail_amount_discount = 0;
                            $single_purchase_request_detail_tax_total = 0;
                            $single_purchase_request_detail_total = $single_purchase_request_detail_sub_total;

                            $item = \App\Models\Item::find($value2->item_id);

                            // * update purchase request detail item
                            $model_purchase_request_detail_item = \App\Models\PurchaseRequestDetail::findOrFail($value2->purchase_request_detail_id);
                            $purchase_detail_model = \App\Models\PurchaseOrderGeneralDetailItem::whereHas('purchase_order_general_detail', function ($p) {
                                $p->whereHas('purchase_order_general', function ($q) {
                                    $q->whereNull('deleted_at');
                                });
                            })
                                ->whereNotIn('status', ['reject', 'void'])
                                ->where('purchase_request_detail_id', $model_purchase_request_detail_item->id)
                                ->get()
                                ->sum('quantity');

                            if ($purchase_detail_model + $value2->quantity > $model_purchase_request_detail_item->jumlah_diapprove) {
                                throw new \Exception("Jumlah PO melebihi jumlah PR");
                            }

                            if ($purchase_detail_model + $value2->quantity == $model_purchase_request_detail_item->jumlah_diapprove) {
                                $model_purchase_request_detail_item->update([
                                    'status' => 'done'
                                ]);
                                // * increment purchase request detail item done count
                                $purchase_request_done_count++;
                            } else {
                                $model_purchase_request_detail_item->update([
                                    'status' => 'partial'
                                ]);
                            }

                            // * create child item data
                            $model_child_item = new \App\Models\PurchaseOrderGeneralDetailItem();
                            $model_child_item->fill([
                                'purchase_order_general_detail_id' => $model_child->id,
                                'purchase_request_detail_id' => $value2->purchase_request_detail_id,
                                'item_id' => $item->id,
                                'unit_id' => $item->unit_id,
                                'quantity' => $value2->quantity,
                                'price_before_discount' => (float) $value2->price_before_discount,
                                'discount' => (float) $value2->discount,
                                'price' => (float) $value2->price,
                                'sub_total' => (float) $single_purchase_request_detail_sub_total,
                                'sub_total_after_tax' => (float) $single_purchase_request_detail_sub_total_after_tax,
                                'amount_discount' => (float) $single_purchase_request_detail_amount_discount,
                                'tax_total' => (float) $single_purchase_request_detail_tax_total,
                                'total' => (float) $single_purchase_request_detail_total,
                            ]);

                            $model_child_item->save();

                            if (($model_child_item->price_before_discount != $model_child_item->price) && $model_child_item->discount == 0 && !$request->is_include_tax) {
                                Log::info('Error Request: ' . json_encode($request->all()));
                                throw new \Exception("Invalid Data Price Before Discount on Create");
                            }

                            // * create purchase order general detail item tax
                            foreach ($value2->tax_id as $key3 => $value3) {
                                $tax = \App\Models\Tax::find($value3);
                                $single_purchase_request_detail_item_tax_total = $single_purchase_request_detail_sub_total * $tax->value;

                                $single_purchase_request_detail_sub_total_after_tax += $single_purchase_request_detail_item_tax_total;
                                $single_purchase_request_detail_tax_total += $single_purchase_request_detail_item_tax_total;
                                $single_purchase_request_detail_total += $single_purchase_request_detail_item_tax_total;

                                // * create purchase order general detail item tax data
                                $model_child_item_tax = new \App\Models\PurchaseOrderGeneralDetailItemTax();
                                $model_child_item_tax->fill([
                                    'purchase_order_general_detail_item_id' => $model_child_item->id,
                                    'tax_id' => $tax->id,
                                    'value' => $tax->value,
                                    'total' => $single_purchase_request_detail_item_tax_total,
                                ]);

                                $model_child_item_tax->save();
                            }

                            // * update purchase request detail item calculation
                            $model_child_item->update([
                                'sub_total' => $single_purchase_request_detail_sub_total,
                                'sub_total_after_tax' => $single_purchase_request_detail_sub_total_after_tax,
                                'amount_discount' => $single_purchase_request_detail_amount_discount,
                                'tax_total' => $single_purchase_request_detail_tax_total,
                                'total' => $single_purchase_request_detail_total,
                            ]);

                            $single_purchase_request_sub_total += $single_purchase_request_detail_sub_total;
                            $single_purchase_request_sub_total_after_tax += $single_purchase_request_detail_sub_total_after_tax;
                            $single_purchase_request_amount_discount += $single_purchase_request_detail_amount_discount;
                            $single_purchase_request_tax_total += $single_purchase_request_detail_tax_total;
                            $single_purchase_request_total += $single_purchase_request_detail_total;
                        }
                    }

                    // * update child data calculation
                    $model_child->update([
                        'sub_total' => $single_purchase_request_sub_total,
                        'sub_total_after_tax' => $single_purchase_request_sub_total_after_tax,
                        'amount_discount' => $single_purchase_request_amount_discount,
                        'tax_total' => $single_purchase_request_tax_total,
                        'total' => $single_purchase_request_total,
                    ]);

                    // * update purchase request status
                    if ($model_purchase_request_count_done == $purchase_request_done_count) {
                        $model_purchase_request->update([
                            'status' => 'done'
                        ]);
                    } elseif (
                        $model_purchase_request_count_done > $purchase_request_done_count && $model_purchase_request->status != 'partial'
                    ) {
                        $model_purchase_request->update([
                            'status' => 'partial'
                        ]);
                    }

                    $total += $single_purchase_request_total;
                    $total_main += $single_purchase_request_total;
                    $total_tax_main += $single_purchase_request_tax_total;
                }
            }
            // ? CREATE FROM PURCHASE REQUEST #########################################################

            // ? CREATE ADDITIONAL ITEM ############################################
            if (is_array($data->additional) && count($data->additional) > 0) {
                if ($data->additional[0] != null) {
                    // ? calculation variable for each purchase request
                    $additional_sub_total = 0;
                    $additional_sub_total_after_tax = 0;
                    $additional_amount_discount = 0;
                    $additional_tax_total = 0;
                    $additional_total = 0;
                    // ? calculation variable for each purchase request

                    $model_additional = new \App\Models\PurchaseOrderGeneralDetail();
                    $model_additional->fill([
                        'purchase_order_general_id' => $model->id,
                        'purchase_request_id',
                        'type' => 'additional',
                        'sub_total' => $additional_sub_total,
                        'sub_total_after_tax' => $additional_sub_total_after_tax,
                        'amount_discount' => $additional_amount_discount,
                        'tax_total' => $additional_tax_total,
                        'total' => $additional_total,
                    ]);

                    $model_additional->save();

                    // * create additional item detail
                    foreach ($data->additional as $key => $value) {
                        if (!is_null($value)) {
                            $item = \App\Models\Item::find($value->item_id);

                            $additional_item_sub_total = $value->quantity * $value->price;
                            $additional_item_sub_total = $additional_item_sub_total;
                            $additional_item_sub_total_after_tax = $additional_item_sub_total;
                            $additional_item_amount_discount = 0;
                            $additional_item_tax_total = 0;
                            $additional_item_total = $additional_item_sub_total;

                            // * create additional item detail data
                            $model_additional_item = new \App\Models\PurchaseOrderGeneralDetailItem();
                            $model_additional_item->fill([
                                'purchase_order_general_detail_id' => $model_additional->id,
                                // 'purchase_request_detail_id',
                                'item_id' => $item->id,
                                'unit_id' => $item->unit_id,
                                'quantity' => $value->quantity,
                                // 'quantity_received',
                                'price' => (float) $value->price,
                                'price_before_discount' => (float) $value->price,
                                // 'discount_type',
                                // 'discount_value',
                                // 'discount_value_percent',
                                'sub_total' => $additional_item_sub_total,
                                'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                'amount_discount' => $additional_item_amount_discount,
                                'tax_total' => $additional_item_tax_total,
                                'total' => $additional_item_total,
                            ]);

                            $model_additional_item->save();

                            // * create additional item detail tax
                            foreach ($value->tax_id as $key2 => $value2) {
                                $tax = \App\Models\Tax::find($value2);
                                $single_item_tax_total = $additional_item_sub_total_after_tax * $tax->value;

                                $additional_item_tax_total += $single_item_tax_total;
                                $additional_item_total += $single_item_tax_total;

                                // * create additional item detail tax data
                                $model_additional_item_tax = new \App\Models\PurchaseOrderGeneralDetailItemTax();
                                $model_additional_item_tax->fill([
                                    'purchase_order_general_detail_item_id' => $model_additional_item->id,
                                    'tax_id' => $tax->id,
                                    'value' => $tax->value,
                                    'total' => $single_item_tax_total,
                                ]);

                                $model_additional_item_tax->save();
                            }

                            // * update additional item detail calculation
                            $model_additional_item->update([
                                'sub_total' => $additional_item_sub_total,
                                'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                'amount_discount' => $additional_item_amount_discount,
                                'tax_total' => $additional_item_tax_total,
                                'total' => $additional_item_total,
                            ]);

                            $additional_sub_total += $additional_item_sub_total;
                            $additional_sub_total_after_tax += $additional_item_sub_total_after_tax;
                            $additional_amount_discount += $additional_item_amount_discount;
                            $additional_tax_total += $additional_item_tax_total;
                            $additional_total += $additional_item_total;
                        }
                    }

                    // * update additional item calculation
                    $model_additional->update([
                        'sub_total' => $additional_sub_total,
                        'sub_total_after_tax' => $additional_sub_total_after_tax,
                        'amount_discount' => $additional_amount_discount,
                        'tax_total' => $additional_tax_total,
                        'total' => $additional_total,
                    ]);

                    $total += $additional_total;
                    $total_additional += $additional_total;
                    $total_tax_additional += $additional_tax_total;
                }
            }
            // ? CREATE ADDITIONAL ITEM ############################################

            // ? update parent data
            $model->update([
                'total' => $total,
                'total_main' => $total_main,
                'total_additional' => $total_additional,
                'total_tax_main' => $total_tax_main,
                'total_tax_additional' => $total_tax_additional,
            ]);

            $purchase->fill([
                'kode' => $model->code,
                'tanggal' => Carbon::parse($model->date),
                'tipe' => 'general',
                'model_reference' => \App\Models\PurchaseOrderGeneral::class,
                'status' => $model->status,
                'branch_id' => $model->branch_id,
                'vendor_id' => $model->vendor_id,
            ]);

            $purchase->save();

            $model->purchaseOrderGeneralDetails()
                ->each(function ($query) {
                    app('App\Http\Controllers\Admin\PurchaseRequestController')->check_purchase_request_status($query->purchase_request_id);
                });

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $purchase->branch_id,
                user_id: auth()->user()->id,
                model: \App\Models\PurchaseOrderGeneral::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "PO General",
                subtitle: Auth::user()->name . " mengajukan PO General " . $model->code,
                link: route('admin.purchase-order-general.show', $model),
                update_status_link: route('admin.purchase-order-general.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            // ! END CREATE DATA PURCHASE ORDER GENERAL
        } catch (\Throwable $th) {
            throw $th;
        }

        return;
    }

    /**
     * Create a new PurchaseOrder from sales order
     */
    private function createPurchaseOrderSalesOrder(Request $request)
    {
        $data = json_decode($request->values);

        // Check main quantity not NaN and 0
        if (is_array($request->main_quantity)) {
            foreach ($request->main_quantity as $quantity) {
                if ($quantity == 0) {
                    throw new \Exception("Jumlah tidak boleh sama dengan 0");
                } elseif ($quantity == "NaN") {
                    throw new \Exception("Jumlah tidak boleh string");
                }
            }
        }

        // ? CALCULATION VARIABLES
        $total = 0;
        $total_main = 0;
        $total_additional = 0;
        $total_tax_main = 0;
        $total_tax_additional = 0;
        // ? CALCULATION VARIABLES

        try {
            // ! CREATE DATA PURCHASE ORDER GENERAL

            // ? CREATE PURCHASES
            $purchase = new \App\Models\Purchase();
            $purchase->fill([
                'kode',
                'tanggal' => Carbon::parse($data->date),
                'tipe' => 'general',
                'model_reference' => \App\Models\PurchaseOrderGeneral::class,
                'status' => 'pending',
                'branch_id' => $data->branch_id ?? Auth::user()->branch_id,
                'vendor_id' => $data->vendor_id,
            ]);

            if (!$purchase->check_available_date) {
                throw new \Exception("Tanggal yang dipilih telah closing");
            }

            $purchase->save();

            // ? CREATE PARENT DATA #########################################################
            $model = new \App\Models\PurchaseOrderGeneral();
            $model->fill([
                'purchase_id' => $purchase->id,
                'branch_id' => $data->branch_id ?? Auth::user()->branch_id,
                'vendor_id' => $data->vendor_id,
                'currency_id' => $data->currency_id,
                'type' => 'sales-order',
                'date' => Carbon::parse($data->date),
                'quotation' => $request->hasFile('quotation') ? $this->upload_file($request->file('quotation'), 'purchase-order-general') : null,
                'term_of_payment' => $data->term_of_payment,
                'term_of_payment_days' => $data->term_of_payment_days,
                'payment_description' => $request->payment_description,
                'exchange_rate' => thousand_to_float($data->exchange_rate),
                'is_include_tax' => $request->is_include_tax ?? 0,
            ]);

            $model->save();

            $purchase->currency_id = $model->currency_id;
            $purchase->model_id = $model->id;
            $purchase->save();
            // ? / END CREATE PARENT DATA #########################################################

            // ? CREATE FROM SALES ORDER #########################################################
            foreach ($data->main as $key => $value) {
                if (!is_null($value)) {
                    // ? calculation variable for each sales order
                    $single_sales_order_sub_total = 0;
                    $single_sales_order_sub_total_after_tax = 0;
                    $single_sales_order_amount_discount = 0;
                    $single_sales_order_tax_total = 0;
                    $single_sales_order_total = 0;
                    // ? calculation variable for each sales order

                    // * find the sales order
                    $model_sale_order = \App\Models\SaleOrderGeneral::findOrFail($value->sale_order_id);

                    // * validate date sales order
                    $sales_order = \App\Models\SaleOrderGeneral::find($value->sale_order_id);
                    $dateSalesOrder = \Carbon\Carbon::parse($sales_order->tanggal);
                    $datePurchaseOrder = \Carbon\Carbon::parse($data->date);

                    // * validate branch
                    if (auth()->user()->branch->is_primary == 0) {
                        if ($model->branch_id != $model_sale_order->branch_id) {
                            throw new \Exception("Branch SO {$model_sale_order->kode} dan branch PO tidak sama");
                        }
                    }

                    // ! sales order date greaten than date purchase order
                    if ($dateSalesOrder->gt($datePurchaseOrder)) {
                        throw new \Exception("Tanggal SO {$model_sale_order->kode} tidak boleh lebih besar dari tanggal PO");
                    }

                    // * create child data
                    $model_child = new \App\Models\PurchaseOrderGeneralDetail();
                    $model_child->fill([
                        'purchase_order_general_id' => $model->id,
                        'sales_order_general_id' => $value->sale_order_id,
                        'type' => 'main',
                    ]);

                    $model_child->save();

                    // * create child item
                    foreach ($value->sale_order_detail_id as $key2 => $value2) {
                        if (!is_null($value2)) {
                            $single_sales_order_detail_sub_total = $value2->quantity * $value2->price;
                            $single_sales_order_detail_sub_total = $single_sales_order_detail_sub_total;
                            $single_sales_order_detail_sub_total_after_tax = $single_sales_order_detail_sub_total;

                            $single_sales_order_detail_amount_discount = 0;
                            $single_sales_order_detail_tax_total = 0;
                            $single_sales_order_detail_total = $single_sales_order_detail_sub_total;

                            $item = \App\Models\Item::find($value2->item_id);

                            // * update sales order detail item
                            $model_sale_order_detail_item = \App\Models\SaleOrderGeneralDetail::findOrFail($value2->sale_order_detail_id);
                            $sale_order_detail_model = \App\Models\PurchaseOrderGeneralDetailItem::whereHas('purchase_order_general_detail', function ($p) {
                                $p->whereHas('purchase_order_general', function ($q) {
                                    $q->whereNull('deleted_at');
                                });
                            })
                                ->whereNotIn('status', ['reject', 'void'])
                                ->where('sale_order_general_detail_id', $model_sale_order_detail_item->id)
                                ->get()
                                ->sum('quantity');

                            if ($value2->quantity > ($model_sale_order_detail_item->amount - $model_sale_order_detail_item->amount_paired)) {
                                throw new \Exception("Jumlah PO melebihi jumlah SO");
                            }

                            if ($sale_order_detail_model + $value2->quantity == ($model_sale_order_detail_item->amount - $model_sale_order_detail_item->amount_paired)) {
                                $model_sale_order_detail_item->update([
                                    'status_pairing' => 'done',
                                    'amount_paired' => $model_sale_order_detail_item->amount_paired + $value2->quantity
                                ]);
                            } else {
                                $model_sale_order_detail_item->update([
                                    'status_pairing' => 'partial',
                                    'amount_paired' => $model_sale_order_detail_item->amount_paired + $value2->quantity
                                ]);
                            }

                            // * create child item data
                            $model_child_item = new \App\Models\PurchaseOrderGeneralDetailItem();
                            $model_child_item->fill([
                                'purchase_order_general_detail_id' => $model_child->id,
                                'sale_order_general_detail_id' => $value2->sale_order_detail_id,
                                'item_id' => $item->id,
                                'unit_id' => $item->unit_id,
                                'quantity' => $value2->quantity,
                                'price_before_discount' => (float) $value2->price_before_discount,
                                'discount' => (float) $value2->discount,
                                'price' => (float) $value2->price,
                                'sub_total' => (float) $single_sales_order_detail_sub_total,
                                'total' => (float) $single_sales_order_detail_total,
                            ]);

                            $model_child_item->save();

                            // * create purchase order general detail item tax
                            foreach ($value2->tax_id as $key3 => $value3) {
                                $tax = \App\Models\Tax::find($value3);
                                $single_sales_order_detail_item_tax_total = $single_sales_order_detail_sub_total * $tax->value;

                                $single_sales_order_detail_sub_total_after_tax += $single_sales_order_detail_item_tax_total;
                                $single_sales_order_detail_tax_total += $single_sales_order_detail_item_tax_total;
                                $single_sales_order_detail_total += $single_sales_order_detail_item_tax_total;

                                // * create purchase order general detail item tax data
                                $model_child_item_tax = new \App\Models\PurchaseOrderGeneralDetailItemTax();
                                $model_child_item_tax->fill([
                                    'purchase_order_general_detail_item_id' => $model_child_item->id,
                                    'tax_id' => $tax->id,
                                    'value' => $tax->value,
                                    'total' => $single_sales_order_detail_item_tax_total,
                                ]);

                                $model_child_item_tax->save();
                            }

                            // * update sales order detail item calculation
                            $model_child_item->update([
                                'sub_total' => $single_sales_order_detail_sub_total,
                                'sub_total_after_tax' => $single_sales_order_detail_sub_total_after_tax,
                                'amount_discount' => $single_sales_order_detail_amount_discount,
                                'tax_total' => $single_sales_order_detail_tax_total,
                                'total' => $single_sales_order_detail_total,
                            ]);

                            $single_sales_order_sub_total += $single_sales_order_detail_sub_total;
                            $single_sales_order_sub_total_after_tax += $single_sales_order_detail_sub_total_after_tax;
                            $single_sales_order_amount_discount += $single_sales_order_detail_amount_discount;
                            $single_sales_order_tax_total += $single_sales_order_detail_tax_total;
                            $single_sales_order_total += $single_sales_order_detail_total;
                        }
                    }

                    // * update child data calculation
                    $model_child->update([
                        'sub_total' => $single_sales_order_sub_total,
                        'sub_total_after_tax' => $single_sales_order_sub_total_after_tax,
                        'amount_discount' => $single_sales_order_amount_discount,
                        'tax_total' => $single_sales_order_tax_total,
                        'total' => $single_sales_order_total,
                    ]);

                    $total += $single_sales_order_total;
                    $total_main += $single_sales_order_total;
                    $total_tax_main += $single_sales_order_tax_total;
                }
            }
            // ? CREATE FROM SALES ORDER #########################################################

            // ? CREATE ADDITIONAL ITEM ############################################
            if (is_array($data->additional) && count($data->additional) > 0) {
                if ($data->additional[0] != null) {
                    // ? calculation variable for each purchase request
                    $additional_sub_total = 0;
                    $additional_sub_total_after_tax = 0;
                    $additional_amount_discount = 0;
                    $additional_tax_total = 0;
                    $additional_total = 0;
                    // ? calculation variable for each purchase request

                    $model_additional = new \App\Models\PurchaseOrderGeneralDetail();
                    $model_additional->fill([
                        'purchase_order_general_id' => $model->id,
                        'purchase_request_id',
                        'type' => 'additional',
                        'sub_total' => $additional_sub_total,
                        'sub_total_after_tax' => $additional_sub_total_after_tax,
                        'amount_discount' => $additional_amount_discount,
                        'tax_total' => $additional_tax_total,
                        'total' => $additional_total,
                    ]);

                    $model_additional->save();

                    // * create additional item detail
                    foreach ($data->additional as $key => $value) {
                        if (!is_null($value)) {
                            $item = \App\Models\Item::find($value->item_id);

                            $additional_item_sub_total = $value->quantity * $value->price;
                            $additional_item_sub_total = $additional_item_sub_total;
                            $additional_item_sub_total_after_tax = $additional_item_sub_total;
                            $additional_item_amount_discount = 0;
                            $additional_item_tax_total = 0;
                            $additional_item_total = $additional_item_sub_total;

                            // * create additional item detail data
                            $model_additional_item = new \App\Models\PurchaseOrderGeneralDetailItem();
                            $model_additional_item->fill([
                                'purchase_order_general_detail_id' => $model_additional->id,
                                // 'purchase_request_detail_id',
                                'item_id' => $item->id,
                                'unit_id' => $item->unit_id,
                                'quantity' => $value->quantity,
                                // 'quantity_received',
                                'price_before_discount' => (float) $value->price,
                                'price' => (float) $value->price,
                                // 'discount_type',
                                // 'discount_value',
                                // 'discount_value_percent',
                                'sub_total' => $additional_item_sub_total,
                                'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                'amount_discount' => $additional_item_amount_discount,
                                'tax_total' => $additional_item_tax_total,
                                'total' => $additional_item_total,
                            ]);

                            $model_additional_item->save();

                            // * create additional item detail tax
                            foreach ($value->tax_id as $key2 => $value2) {
                                $tax = \App\Models\Tax::find($value2);
                                $single_item_tax_total = $additional_item_sub_total_after_tax * $tax->value;

                                $additional_item_tax_total += $single_item_tax_total;
                                $additional_item_total += $single_item_tax_total;

                                // * create additional item detail tax data
                                $model_additional_item_tax = new \App\Models\PurchaseOrderGeneralDetailItemTax();
                                $model_additional_item_tax->fill([
                                    'purchase_order_general_detail_item_id' => $model_additional_item->id,
                                    'tax_id' => $tax->id,
                                    'value' => $tax->value,
                                    'total' => $single_item_tax_total,
                                ]);

                                $model_additional_item_tax->save();
                            }

                            // * update additional item detail calculation
                            $model_additional_item->update([
                                'sub_total' => $additional_item_sub_total,
                                'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                'amount_discount' => $additional_item_amount_discount,
                                'tax_total' => $additional_item_tax_total,
                                'total' => $additional_item_total,
                            ]);

                            $additional_sub_total += $additional_item_sub_total;
                            $additional_sub_total_after_tax += $additional_item_sub_total_after_tax;
                            $additional_amount_discount += $additional_item_amount_discount;
                            $additional_tax_total += $additional_item_tax_total;
                            $additional_total += $additional_item_total;
                        }
                    }

                    // * update additional item calculation
                    $model_additional->update([
                        'sub_total' => $additional_sub_total,
                        'sub_total_after_tax' => $additional_sub_total_after_tax,
                        'amount_discount' => $additional_amount_discount,
                        'tax_total' => $additional_tax_total,
                        'total' => $additional_total,
                    ]);

                    $total += $additional_total;
                    $total_additional += $additional_total;
                    $total_tax_additional += $additional_tax_total;
                }
            }
            // ? CREATE ADDITIONAL ITEM ############################################

            // ? update parent data
            $model->update([
                'total' => $total,
                'total_main' => $total_main,
                'total_additional' => $total_additional,
                'total_tax_main' => $total_tax_main,
                'total_tax_additional' => $total_tax_additional,
            ]);

            $purchase->fill([
                'kode' => $model->code,
                'tanggal' => Carbon::parse($model->date),
                'tipe' => 'general',
                'model_reference' => \App\Models\PurchaseOrderGeneral::class,
                'status' => $model->status,
                'branch_id' => $model->branch_id,
                'vendor_id' => $model->vendor_id,
            ]);

            $purchase->save();

            $model->purchaseOrderGeneralDetails()
                ->each(function ($query) {
                    app('App\Http\Controllers\Admin\PurchaseRequestController')->check_purchase_request_status($query->purchase_request_id);
                });

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $purchase->branch_id,
                user_id: auth()->user()->id,
                model: \App\Models\PurchaseOrderGeneral::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "PO General",
                subtitle: Auth::user()->name . " mengajukan PO General " . $model->code,
                link: route('admin.purchase-order-general.show', $model),
                update_status_link: route('admin.purchase-order-general.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            // ! END CREATE DATA PURCHASE ORDER GENERAL
        } catch (\Throwable $th) {
            throw $th;
        }

        return;
    }

    /**
     * Update a PurchaseOrder from purchase request
     */
    private function updatePurchaseOrderPurchaseRequest(Request $request, $id)
    {
        $data = json_decode($request->values);

        // Check main quantity not NaN and 0
        if (is_array($request->main_quantity)) {
            foreach ($request->main_quantity as $quantity) {
                if ($quantity == 0) {
                    throw new \Exception("Jumlah tidak boleh sama dengan 0");
                } elseif ($quantity == "NaN") {
                    throw new \Exception("Jumlah tidak boleh string");
                }
            }
        }

        // ? CALCULATION VARIABLES
        $total = 0;
        $total_main = 0;
        $total_additional = 0;
        $total_tax_main = 0;
        $total_tax_additional = 0;
        // ? CALCULATION VARIABLES

        $purchase_request_ids = [];
        try {
            // * PARENT DATA ------------------------------------------------------------
            $model = \App\Models\PurchaseOrderGeneral::findOrFail($id);

            $purchase_request_ids = $model->purchaseOrderGeneralDetails()
                ->where('type', 'main')
                ->pluck('purchase_request_id')
                ->unique()
                ->toArray();

            $model->fill([
                'branch_id' => $data->branch_id ?? $model->branch_id ?? Auth::user()->branch_id,
                'vendor_id' => $data->vendor_id,
                'currency_id' => $data->currency_id,
                'quotation' => $request->hasFile('quotation') ? $this->upload_file($request->file('quotation'), 'purchase-order-general') : $model->quotation,
                'term_of_payment' => $data->term_of_payment,
                'term_of_payment_days' => $data->term_of_payment_days,
                'payment_description' => $data->payment_description ?? $model->payment_description ?? '',
                'exchange_rate' => thousand_to_float($data->exchange_rate),
                'is_include_tax' => $request->is_include_tax ?? 0,
            ]);

            if ($model->status == 'revert') {
                $model->code  = generate_code_update($model->code);
            }

            $model->save();

            $purchase = Purchase::where('model_reference', \App\Models\PurchaseOrderGeneral::class)
                ->where('model_id', $model->id)
                ->first();

            $purchase->fill([
                'code' => $model->code,
                'tanggal' => Carbon::parse($model->date),
                'tipe' => 'general',
                'model_reference' => \App\Models\PurchaseOrderGeneral::class,
                'branch_id' => $model->branch_id ?? Auth::user()->branch_id,
                'vendor_id' => $model->vendor_id,
            ]);

            $purchase->save();
            // * END PARENT DATA ------------------------------------------------------------

            // * FROM PURCHASE REQUEST ------------------------------------------------------------
            foreach ($data->main as $key => $value) {
                // ? calculation variable for each purchase request
                $single_purchase_request_sub_total = 0;
                $single_purchase_request_sub_total_after_tax = 0;
                $single_purchase_request_amount_discount = 0;
                $single_purchase_request_tax_total = 0;
                $single_purchase_request_total = 0;
                // ? calculation variable for each purchase request

                // * find purchase request
                $model_purchase_request = \App\Models\PurchaseRequest::findOrFail($value->purchase_request_id);
                $purchase_request_done_count = $model_purchase_request->purchase_request_details->whereNotIn('status', ['reject', 'pending'])->where('status', 'done')->count();
                $model_purchase_request_count_done = $model_purchase_request->purchase_request_details->whereNotIn('status', ['reject', 'pending'])->count();

                $model_child = \App\Models\PurchaseOrderGeneralDetail::findOrFail($value->purchase_order_general_detail_id);

                $exist_purchase_order_general_detail_items = collect($value->purchase_order_general_detail_items)->map(function ($item) {
                    return $item->purchase_order_general_detail_item_id;
                })->toArray();

                \App\Models\PurchaseOrderGeneralDetailItem::when(count($exist_purchase_order_general_detail_items) > 0, function ($q) use ($exist_purchase_order_general_detail_items) {
                    $q->whereNotIn('id', $exist_purchase_order_general_detail_items);
                })
                    ->where('purchase_order_general_detail_id', $value->purchase_order_general_detail_id)
                    ->get()
                    ->each(function ($item) {
                        $item->purchase_order_general_detail_item_taxes()->delete();
                        $item->delete();
                    });


                if ($model_child->purchase_order_general_detail_items->count() > 0) {
                    foreach ($value->purchase_order_general_detail_items as $key2 => $value2) {
                        $single_purchase_request_detail_sub_total = $value2->quantity * $value2->price;
                        $single_purchase_request_detail_sub_total = $single_purchase_request_detail_sub_total;
                        $single_purchase_request_detail_sub_total_after_tax = $single_purchase_request_detail_sub_total;

                        $single_purchase_request_detail_amount_discount = 0;
                        $single_purchase_request_detail_tax_total = 0;
                        $single_purchase_request_detail_total = $single_purchase_request_detail_sub_total;

                        $item = \App\Models\Item::find($value2->item_id);
                        $model_child_item = \App\Models\PurchaseOrderGeneralDetailItem::findOrFail($value2->purchase_order_general_detail_item_id);

                        // * update purchase request detail item
                        $model_purchase_request_detail_item = \App\Models\PurchaseRequestDetail::findOrFail($value2->purchase_request_detail_id);
                        $purchase_detail_model = \App\Models\PurchaseOrderGeneralDetailItem::whereHas('purchase_order_general_detail', function ($p) {
                            $p->whereHas('purchase_order_general', function ($q) {
                                $q->whereNull('deleted_at');
                            });
                        })
                            ->whereNotIn('status', ['reject', 'done', 'void'])
                            ->where('purchase_request_detail_id', $model_purchase_request_detail_item->id)
                            ->get()
                            ->sum('quantity');

                        if ($purchase_detail_model - $model_child_item->quantity + $value2->quantity > $model_purchase_request_detail_item->jumlah_diapprove) {
                            throw new \Exception("Jumlah PO melebihi jumlah PR");
                        }

                        if ($purchase_detail_model - $model_child_item->quantity + $value2->quantity == $model_purchase_request_detail_item->jumlah_diapprove) {
                            $model_purchase_request_detail_item->update([
                                'status' => 'done'
                            ]);
                            // * increment purchase request detail item done count
                            $purchase_request_done_count++;
                        } else {
                            $model_purchase_request_detail_item->update([
                                'status' => 'partial'
                            ]);
                        }

                        // * update item child item
                        $model_child_item->fill([
                            'item_id' => $item->id,
                            'unit_id' => $item->unit_id,
                            'quantity' => $value2->quantity,
                            'price_before_discount' => (float) $value2->price_before_discount,
                            'discount' => (float) $value2->discount,
                            'price' => (float) $value2->price,
                            'sub_total' => (float) $single_purchase_request_detail_sub_total,
                            'sub_total_after_tax' => (float) $single_purchase_request_detail_sub_total_after_tax,
                            'amount_discount' => (float) $single_purchase_request_detail_amount_discount,
                            'tax_total' => (float) $single_purchase_request_detail_tax_total,
                            'total' => (float) $single_purchase_request_detail_total,
                        ]);

                        $model_child_item->save();

                        if (($model_child_item->price_before_discount != $model_child_item->price) && $model_child_item->discount == 0 && !$request->is_include_tax) {
                            Log::info('Error Request: ' . json_encode($request->all()));
                            throw new \Exception("Invalid Data Price Before Discount on Update");
                        }

                        // * delete and create new detail item tax
                        \App\Models\PurchaseOrderGeneralDetailItemTax::where('purchase_order_general_detail_item_id', $model_child_item->id)->delete();
                        foreach ($value2->tax_id as $key3 => $value3) {
                            $tax = \App\Models\Tax::find($value3);
                            $single_purchase_request_detail_item_tax_total = $single_purchase_request_detail_sub_total * $tax->value;

                            $single_purchase_request_detail_sub_total_after_tax += $single_purchase_request_detail_item_tax_total;
                            $single_purchase_request_detail_tax_total += $single_purchase_request_detail_item_tax_total;
                            $single_purchase_request_detail_total += $single_purchase_request_detail_item_tax_total;

                            // * create purchase order general detail item tax data
                            $model_child_item_tax = new \App\Models\PurchaseOrderGeneralDetailItemTax();
                            $model_child_item_tax->fill([
                                'purchase_order_general_detail_item_id' => $model_child_item->id,
                                'tax_id' => $tax->id,
                                'value' => $tax->value,
                                'total' => $single_purchase_request_detail_item_tax_total,
                            ]);

                            $model_child_item_tax->save();
                        }

                        // * update purchase request detail item calculation
                        $model_child_item->update([
                            'sub_total' => $single_purchase_request_detail_sub_total,
                            'sub_total_after_tax' => $single_purchase_request_detail_sub_total_after_tax,
                            'amount_discount' => $single_purchase_request_detail_amount_discount,
                            'tax_total' => $single_purchase_request_detail_tax_total,
                            'total' => $single_purchase_request_detail_total,
                        ]);

                        $single_purchase_request_sub_total += $single_purchase_request_detail_sub_total;
                        $single_purchase_request_sub_total_after_tax += $single_purchase_request_detail_sub_total_after_tax;
                        $single_purchase_request_amount_discount += $single_purchase_request_detail_amount_discount;
                        $single_purchase_request_tax_total += $single_purchase_request_detail_tax_total;
                        $single_purchase_request_total += $single_purchase_request_detail_total;
                    }

                    // * update child data calculation
                    $model_child->update([
                        'sub_total' => $single_purchase_request_sub_total,
                        'sub_total_after_tax' => $single_purchase_request_sub_total_after_tax,
                        'amount_discount' => $single_purchase_request_amount_discount,
                        'tax_total' => $single_purchase_request_tax_total,
                        'total' => $single_purchase_request_total,
                    ]);

                    $total += $single_purchase_request_total;
                    $total_main += $single_purchase_request_total;
                    $total_tax_main += $single_purchase_request_tax_total;
                } else {
                    $model_child->delete();
                }

                // * update purchase request status
                if ($model_purchase_request_count_done == $purchase_request_done_count) {
                    $model_purchase_request->update([
                        'status' => 'done'
                    ]);
                } elseif (
                    $model_purchase_request_count_done > $purchase_request_done_count && $model_purchase_request->status != 'partial'
                ) {
                    $model_purchase_request->update([
                        'status' => 'partial'
                    ]);
                }
            }
            // * FROM PURCHASE REQUEST ------------------------------------------------------------

            // * ADDITIONAL ITEM ------------------------------------------------------------
            if ($data->additional) {
                if ($data->additional->purchase_order_general_detail_items[0] && $data->additional->purchase_order_general_detail_items[0]->quantity != 0 && $data->additional->purchase_order_general_detail_items[0]->price != 0) {
                    // * find or create data additional
                    $model_additional = \App\Models\PurchaseOrderGeneralDetail::where('purchase_order_general_id', $model->id)
                        ->where('type', 'additional')
                        ->where('id', $data->additional?->purchase_order_general_detail_id ?? null)
                        ->first();

                    if (!$model_additional) {
                        $model_additional = new \App\Models\PurchaseOrderGeneralDetail();
                    } else {
                        // * delete and create new detail item
                        $items = \App\Models\PurchaseOrderGeneralDetailItem::where('purchase_order_general_detail_id', $model_additional->id)->get();

                        foreach ($items as $key => $value) {
                            \App\Models\PurchaseOrderGeneralDetailItemTax::where('purchase_order_general_detail_item_id', $value->id)->delete();
                            $value->delete();
                        }
                    }

                    if (count($data->additional?->purchase_order_general_detail_items) > 0 and $data->additional?->purchase_order_general_detail_items[0] != null) {
                        // ? calculation variable for each purchase request
                        $additional_sub_total = 0;
                        $additional_sub_total_after_tax = 0;
                        $additional_amount_discount = 0;
                        $additional_tax_total = 0;
                        $additional_total = 0;
                        // ? calculation variable for each purchase request

                        $model_additional->fill([
                            'purchase_order_general_id' => $model->id,
                            'type' => 'additional',
                            'sub_total' => $additional_sub_total,
                            'sub_total_after_tax' => $additional_sub_total_after_tax,
                            'amount_discount' => $additional_amount_discount,
                            'tax_total' => $additional_tax_total,
                            'total' => $additional_total,
                        ]);

                        $model_additional->save();

                        // * create purchase order general detail item
                        foreach ($data->additional->purchase_order_general_detail_items as $key => $value) {
                            if (!is_null($value)) {
                                if ($value->quantity != 0 && $value->price != 0) {
                                    $item = \App\Models\Item::find($value->item_id);

                                    $additional_item_sub_total = $value->quantity * $value->price;
                                    $additional_item_sub_total = $additional_item_sub_total;
                                    $additional_item_sub_total_after_tax = $additional_item_sub_total;
                                    $additional_item_amount_discount = 0;
                                    $additional_item_tax_total = 0;
                                    $additional_item_total = $additional_item_sub_total;

                                    // * create additional item detail data
                                    $model_additional_item = new \App\Models\PurchaseOrderGeneralDetailItem();
                                    $model_additional_item->fill([
                                        'purchase_order_general_detail_id' => $model_additional->id,
                                        // 'purchase_request_detail_id',
                                        'item_id' => $item->id,
                                        'unit_id' => $item->unit_id,
                                        'quantity' => $value->quantity,
                                        // 'quantity_received',
                                        'price_before_discount' => (float) $value->price,
                                        'price' => (float) $value->price,
                                        // 'discount_type',
                                        // 'discount_value',
                                        // 'discount_value_percent',
                                        'sub_total' => $additional_item_sub_total,
                                        'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                        'amount_discount' => $additional_item_amount_discount,
                                        'tax_total' => $additional_item_tax_total,
                                        'total' => $additional_item_total,
                                    ]);

                                    $model_additional_item->save();

                                    // * create additional item detail tax
                                    foreach ($value->tax_id as $key2 => $value2) {
                                        $tax = \App\Models\Tax::find($value2);
                                        $single_item_tax_total = $additional_item_sub_total_after_tax * $tax->value;

                                        $additional_item_tax_total += $single_item_tax_total;
                                        $additional_item_total += $single_item_tax_total;

                                        // * create additional item detail tax data
                                        $model_additional_item_tax = new \App\Models\PurchaseOrderGeneralDetailItemTax();
                                        $model_additional_item_tax->fill([
                                            'purchase_order_general_detail_item_id' => $model_additional_item->id,
                                            'tax_id' => $tax->id,
                                            'value' => $tax->value,
                                            'total' => $single_item_tax_total,
                                        ]);

                                        $model_additional_item_tax->save();
                                    }

                                    // * update additional item detail calculation
                                    $model_additional_item->update([
                                        'sub_total' => $additional_item_sub_total,
                                        'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                        'amount_discount' => $additional_item_amount_discount,
                                        'tax_total' => $additional_item_tax_total,
                                        'total' => $additional_item_total,
                                    ]);

                                    $additional_sub_total += $additional_item_sub_total;
                                    $additional_sub_total_after_tax += $additional_item_sub_total_after_tax;
                                    $additional_amount_discount += $additional_item_amount_discount;
                                    $additional_tax_total += $additional_item_tax_total;
                                    $additional_total += $additional_item_total;
                                }
                            }
                        }

                        // * update additional item calculation
                        $model_additional->update([
                            'sub_total' => $additional_sub_total,
                            'sub_total_after_tax' => $additional_sub_total_after_tax,
                            'amount_discount' => $additional_amount_discount,
                            'tax_total' => $additional_tax_total,
                            'total' => $additional_total,
                        ]);

                        $total += $additional_total;
                        $total_additional += $additional_total;
                        $total_tax_additional += $additional_tax_total;
                    }
                } else {
                    // * find or create data additional
                    $model_additional = \App\Models\PurchaseOrderGeneralDetail::where('purchase_order_general_id', $model->id)
                        ->where('type', 'additional')
                        ->where('id', $data->additional?->purchase_order_general_detail_id ?? null)
                        ->first();

                    if ($model_additional) {
                        // * delete and create new detail item
                        $items = \App\Models\PurchaseOrderGeneralDetailItem::where('purchase_order_general_detail_id', $model_additional->id)->get();

                        foreach ($items as $key => $value) {
                            \App\Models\PurchaseOrderGeneralDetailItemTax::where('purchase_order_general_detail_item_id', $value->id)->delete();
                            $value->delete();
                        }

                        $model_additional->delete();
                    }
                }
            } else {
                // * find or create data additional
                $model_additional = \App\Models\PurchaseOrderGeneralDetail::where('purchase_order_general_id', $model->id)
                    ->where('type', 'additional')
                    ->where('id', $data->additional?->purchase_order_general_detail_id ?? null)
                    ->first();

                if ($model_additional) {
                    // * delete and create new detail item
                    $items = \App\Models\PurchaseOrderGeneralDetailItem::where('purchase_order_general_detail_id', $model_additional->id)->get();

                    foreach ($items as $key => $value) {
                        \App\Models\PurchaseOrderGeneralDetailItemTax::where('purchase_order_general_detail_item_id', $value->id)->delete();
                        $value->delete();
                    }

                    $model_additional->delete();
                }
            }
            // * ADDITIONAL ITEM ------------------------------------------------------------

            // ? update parent total
            $model->update([
                'total' => $total,
                'total_main' => $total_main,
                'total_additional' => $total_additional,
                'total_tax_main' => $total_tax_main,
                'total_tax_additional' => $total_tax_additional,
            ]);



            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: PurchaseOrderGeneral::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "PO General",
                subtitle: Auth::user()->name . " mengajukan PO General " . $model->code,
                link: route('admin.purchase-order-general.show', $model),
                update_status_link: route('admin.purchase-order-general.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            $purchase_request_ids = array_merge($purchase_request_ids, $model->purchaseOrderGeneralDetails()
                ->where('type', 'main')
                ->pluck('purchase_request_id')
                ->toArray());

            foreach (array_unique($purchase_request_ids) as $purchase_request_id) {
                app('App\Http\Controllers\Admin\PurchaseRequestController')->check_purchase_request_status($purchase_request_id);
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return;
    }

    /**
     * Update a PurchaseOrder from sales order
     */
    private function updatePurchaseOrderSalesOrder(Request $request, $id)
    {
        $data = json_decode($request->values);

        // Check main quantity not NaN and 0
        if (is_array($request->main_quantity)) {
            foreach ($request->main_quantity as $quantity) {
                if ($quantity == 0) {
                    throw new \Exception("Jumlah tidak boleh sama dengan 0");
                } elseif ($quantity == "NaN") {
                    throw new \Exception("Jumlah tidak boleh string");
                }
            }
        }

        // ? CALCULATION VARIABLES
        $total = 0;
        $total_main = 0;
        $total_additional = 0;
        $total_tax_main = 0;
        $total_tax_additional = 0;
        // ? CALCULATION VARIABLES

        $purchase_request_ids = [];

        try {
            // ! UPDATING DATA #########################################################

            // * PARENT DATA ------------------------------------------------------------
            $model = \App\Models\PurchaseOrderGeneral::findOrFail($id);

            $purchase_request_ids = $model->purchaseOrderGeneralDetails()
                ->where('type', 'main')
                ->pluck('purchase_request_id')
                ->unique()
                ->toArray();

            $model->fill([
                'branch_id' => $data->branch_id ?? $model->branch_id ?? Auth::user()->branch_id,
                'vendor_id' => $data->vendor_id,
                'currency_id' => $data->currency_id,
                'quotation' => $request->hasFile('quotation') ? $this->upload_file($request->file('quotation'), 'purchase-order-general') : $model->quotation,
                'term_of_payment' => $data->term_of_payment,
                'term_of_payment_days' => $data->term_of_payment_days,
                'payment_description' => $data->payment_description ?? $model->payment_description ?? '',
                'exchange_rate' => thousand_to_float($data->exchange_rate),
                'is_include_tax' => $request->is_include_tax ?? 0,
            ]);

            if ($model->status == 'revert') {
                $model->code  = generate_code_update($model->code);
            }

            $model->save();

            $purchase = Purchase::where('model_reference', \App\Models\PurchaseOrderGeneral::class)
                ->where('model_id', $model->id)
                ->first();

            $purchase->fill([
                'code' => $model->code,
                'tanggal' => Carbon::parse($model->date),
                'tipe' => 'general',
                'model_reference' => \App\Models\PurchaseOrderGeneral::class,
                'branch_id' => $model->branch_id ?? Auth::user()->branch_id,
                'vendor_id' => $model->vendor_id,
            ]);

            $purchase->save();
            // * END PARENT DATA ------------------------------------------------------------

            // * FROM SALES ORDER ------------------------------------------------------------
            foreach ($data->main as $key => $value) {
                // ? calculation variable for each sales order
                $single_sales_order_sub_total = 0;
                $single_sales_order_sub_total_after_tax = 0;
                $single_sales_order_amount_discount = 0;
                $single_sales_order_tax_total = 0;
                $single_sales_order_total = 0;
                // ? calculation variable for each sales order

                // * find sales order
                $model_sales_order = \App\Models\SaleOrderGeneral::findOrFail($value->sale_order_id);

                $model_child = \App\Models\PurchaseOrderGeneralDetail::findOrFail($value->purchase_order_general_detail_id);


                $exist_purchase_order_general_detail_items = collect($value->purchase_order_general_detail_items)->map(function ($item) {
                    return $item->purchase_order_general_detail_item_id;
                })->toArray();

                \App\Models\PurchaseOrderGeneralDetailItem::when(count($exist_purchase_order_general_detail_items) > 0, function ($q) use ($exist_purchase_order_general_detail_items) {
                    $q->whereNotIn('id', $exist_purchase_order_general_detail_items);
                })
                    ->where('purchase_order_general_detail_id', $value->purchase_order_general_detail_id)
                    ->get()
                    ->each(function ($item) {
                        $item->purchase_order_general_detail_item_taxes()->delete();
                        $item->delete();
                    });

                if ($model_child->purchase_order_general_detail_items->count() > 0) {
                    foreach ($value->purchase_order_general_detail_items as $key2 => $value2) {
                        $single_sales_order_detail_sub_total = $value2->quantity * $value2->price;
                        $single_sales_order_detail_sub_total = $single_sales_order_detail_sub_total;
                        $single_sales_order_detail_sub_total_after_tax = $single_sales_order_detail_sub_total;

                        $single_sales_order_detail_amount_discount = 0;
                        $single_sales_order_detail_tax_total = 0;
                        $single_sales_order_detail_total = $single_sales_order_detail_sub_total;

                        $item = \App\Models\Item::find($value2->item_id);
                        $model_child_item = \App\Models\PurchaseOrderGeneralDetailItem::findOrFail($value2->purchase_order_general_detail_item_id);

                        // * update sales order detail item
                        $model_sales_order_detail_item = \App\Models\SaleOrderGeneralDetail::findOrFail($value2->sale_order_general_detail_id);
                        $sale_order_detail_model = \App\Models\PurchaseOrderGeneralDetailItem::whereHas('purchase_order_general_detail', function ($p) {
                            $p->whereHas('purchase_order_general', function ($q) {
                                $q->whereNull('deleted_at');
                            });
                        })
                            ->whereNotIn('status', ['reject', 'done', 'void'])
                            ->where('sale_order_general_detail_id', $model_sales_order_detail_item->id)
                            ->get()
                            ->sum('quantity');

                        $outstanding_so = $model_sales_order_detail_item->amount - ($sale_order_detail_model - $model_child_item->quantity);
                        if ($outstanding_so < $value2->quantity) {
                            throw new \Exception("Jumlah PO melebihi jumlah SO");
                        }

                        if ($sale_order_detail_model - $model_child_item->quantity + $value2->quantity == $model_sales_order_detail_item->amount) {
                            $model_sales_order_detail_item->update([
                                'status_pairing' => 'done',
                                'amount_paired' => $model_sales_order_detail_item->amount_paired - $model_child_item->quantity + $value2->quantity
                            ]);
                        } else {
                            $model_sales_order_detail_item->update([
                                'status_pairing' => 'partial',
                                'amount_paired' => $model_sales_order_detail_item->amount_paired - $model_child_item->quantity + $value2->quantity
                            ]);
                        }

                        // * update item child item
                        $model_child_item->fill([
                            'item_id' => $item->id,
                            'unit_id' => $item->unit_id,
                            'quantity' => $value2->quantity,
                            'price_before_discount' => (float) $value2->price_before_discount,
                            'discount' => (float) $value2->discount,
                            'price' => (float) $value2->price,
                            'sub_total' => (float) $single_sales_order_detail_sub_total,
                            'sub_total_after_tax' => (float) $single_sales_order_detail_sub_total_after_tax,
                            'amount_discount' => (float) $single_sales_order_detail_amount_discount,
                            'tax_total' => (float) $single_sales_order_detail_tax_total,
                            'total' => (float) $single_sales_order_detail_total,
                        ]);

                        $model_child_item->save();

                        // * delete and create new detail item tax
                        \App\Models\PurchaseOrderGeneralDetailItemTax::where('purchase_order_general_detail_item_id', $model_child_item->id)->delete();
                        foreach ($value2->tax_id as $key3 => $value3) {
                            $tax = \App\Models\Tax::find($value3);
                            $single_sales_order_detail_item_tax_total = $single_sales_order_detail_sub_total * $tax->value;

                            $single_sales_order_detail_sub_total_after_tax += $single_sales_order_detail_item_tax_total;
                            $single_sales_order_detail_tax_total += $single_sales_order_detail_item_tax_total;
                            $single_sales_order_detail_total += $single_sales_order_detail_item_tax_total;

                            // * create purchase order general detail item tax data
                            $model_child_item_tax = new \App\Models\PurchaseOrderGeneralDetailItemTax();
                            $model_child_item_tax->fill([
                                'purchase_order_general_detail_item_id' => $model_child_item->id,
                                'tax_id' => $tax->id,
                                'value' => $tax->value,
                                'total' => $single_sales_order_detail_item_tax_total,
                            ]);

                            $model_child_item_tax->save();
                        }

                        // * update sales order detail item calculation
                        $model_child_item->update([
                            'sub_total' => $single_sales_order_detail_sub_total,
                            'sub_total_after_tax' => $single_sales_order_detail_sub_total_after_tax,
                            'amount_discount' => $single_sales_order_detail_amount_discount,
                            'tax_total' => $single_sales_order_detail_tax_total,
                            'total' => $single_sales_order_detail_total,
                        ]);

                        $single_sales_order_sub_total += $single_sales_order_detail_sub_total;
                        $single_sales_order_sub_total_after_tax += $single_sales_order_detail_sub_total_after_tax;
                        $single_sales_order_amount_discount += $single_sales_order_detail_amount_discount;
                        $single_sales_order_tax_total += $single_sales_order_detail_tax_total;
                        $single_sales_order_total += $single_sales_order_detail_total;
                    }

                    // * update child data calculation
                    $model_child->update([
                        'sub_total' => $single_sales_order_sub_total,
                        'sub_total_after_tax' => $single_sales_order_sub_total_after_tax,
                        'amount_discount' => $single_sales_order_amount_discount,
                        'tax_total' => $single_sales_order_tax_total,
                        'total' => $single_sales_order_total,
                    ]);

                    $total += $single_sales_order_total;
                    $total_main += $single_sales_order_total;
                    $total_tax_main += $single_sales_order_tax_total;
                } else {
                    $model_child->delete();
                }
            }
            // * FROM SALES ORDER ------------------------------------------------------------

            // * ADDITIONAL ITEM ------------------------------------------------------------
            if ($data->additional) {
                if ($data->additional->purchase_order_general_detail_items[0] && $data->additional->purchase_order_general_detail_items[0]->quantity != 0 && $data->additional->purchase_order_general_detail_items[0]->price != 0) {
                    // * find or create data additional
                    $model_additional = \App\Models\PurchaseOrderGeneralDetail::where('purchase_order_general_id', $model->id)
                        ->where('type', 'additional')
                        ->where('id', $data->additional?->purchase_order_general_detail_id ?? null)
                        ->first();

                    if (!$model_additional) {
                        $model_additional = new \App\Models\PurchaseOrderGeneralDetail();
                    } else {
                        // * delete and create new detail item
                        $items = \App\Models\PurchaseOrderGeneralDetailItem::where('purchase_order_general_detail_id', $model_additional->id)->get();

                        foreach ($items as $key => $value) {
                            \App\Models\PurchaseOrderGeneralDetailItemTax::where('purchase_order_general_detail_item_id', $value->id)->delete();
                            $value->delete();
                        }
                    }

                    if (count($data->additional?->purchase_order_general_detail_items) > 0 and $data->additional?->purchase_order_general_detail_items[0] != null) {
                        // ? calculation variable for each purchase request
                        $additional_sub_total = 0;
                        $additional_sub_total_after_tax = 0;
                        $additional_amount_discount = 0;
                        $additional_tax_total = 0;
                        $additional_total = 0;
                        // ? calculation variable for each purchase request

                        $model_additional->fill([
                            'purchase_order_general_id' => $model->id,
                            'type' => 'additional',
                            'sub_total' => $additional_sub_total,
                            'sub_total_after_tax' => $additional_sub_total_after_tax,
                            'amount_discount' => $additional_amount_discount,
                            'tax_total' => $additional_tax_total,
                            'total' => $additional_total,
                        ]);

                        $model_additional->save();

                        // * create purchase order general detail item
                        foreach ($data->additional->purchase_order_general_detail_items as $key => $value) {
                            if (!is_null($value)) {
                                if ($value->quantity != 0 && $value->price != 0) {
                                    $item = \App\Models\Item::find($value->item_id);

                                    $additional_item_sub_total = $value->quantity * $value->price;
                                    $additional_item_sub_total = $additional_item_sub_total;
                                    $additional_item_sub_total_after_tax = $additional_item_sub_total;
                                    $additional_item_amount_discount = 0;
                                    $additional_item_tax_total = 0;
                                    $additional_item_total = $additional_item_sub_total;

                                    // * create additional item detail data
                                    $model_additional_item = new \App\Models\PurchaseOrderGeneralDetailItem();
                                    $model_additional_item->fill([
                                        'purchase_order_general_detail_id' => $model_additional->id,
                                        // 'purchase_request_detail_id',
                                        'item_id' => $item->id,
                                        'unit_id' => $item->unit_id,
                                        'quantity' => $value->quantity,
                                        // 'quantity_received',
                                        'price_before_discount' => (float) $value->price,
                                        'price' => (float) $value->price,
                                        // 'discount_type',
                                        // 'discount_value',
                                        // 'discount_value_percent',
                                        'sub_total' => $additional_item_sub_total,
                                        'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                        'amount_discount' => $additional_item_amount_discount,
                                        'tax_total' => $additional_item_tax_total,
                                        'total' => $additional_item_total,
                                    ]);

                                    $model_additional_item->save();

                                    // * create additional item detail tax
                                    foreach ($value->tax_id as $key2 => $value2) {
                                        $tax = \App\Models\Tax::find($value2);
                                        $single_item_tax_total = $additional_item_sub_total_after_tax * $tax->value;

                                        $additional_item_tax_total += $single_item_tax_total;
                                        $additional_item_total += $single_item_tax_total;

                                        // * create additional item detail tax data
                                        $model_additional_item_tax = new \App\Models\PurchaseOrderGeneralDetailItemTax();
                                        $model_additional_item_tax->fill([
                                            'purchase_order_general_detail_item_id' => $model_additional_item->id,
                                            'tax_id' => $tax->id,
                                            'value' => $tax->value,
                                            'total' => $single_item_tax_total,
                                        ]);

                                        $model_additional_item_tax->save();
                                    }

                                    // * update additional item detail calculation
                                    $model_additional_item->update([
                                        'sub_total' => $additional_item_sub_total,
                                        'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                        'amount_discount' => $additional_item_amount_discount,
                                        'tax_total' => $additional_item_tax_total,
                                        'total' => $additional_item_total,
                                    ]);

                                    $additional_sub_total += $additional_item_sub_total;
                                    $additional_sub_total_after_tax += $additional_item_sub_total_after_tax;
                                    $additional_amount_discount += $additional_item_amount_discount;
                                    $additional_tax_total += $additional_item_tax_total;
                                    $additional_total += $additional_item_total;
                                }
                            }
                        }

                        // * update additional item calculation
                        $model_additional->update([
                            'sub_total' => $additional_sub_total,
                            'sub_total_after_tax' => $additional_sub_total_after_tax,
                            'amount_discount' => $additional_amount_discount,
                            'tax_total' => $additional_tax_total,
                            'total' => $additional_total,
                        ]);

                        $total += $additional_total;
                        $total_additional += $additional_total;
                        $total_tax_additional += $additional_tax_total;
                    }
                } else {
                    // * find or create data additional
                    $model_additional = \App\Models\PurchaseOrderGeneralDetail::where('purchase_order_general_id', $model->id)
                        ->where('type', 'additional')
                        ->where('id', $data->additional?->purchase_order_general_detail_id ?? null)
                        ->first();

                    if ($model_additional) {
                        // * delete and create new detail item
                        $items = \App\Models\PurchaseOrderGeneralDetailItem::where('purchase_order_general_detail_id', $model_additional->id)->get();

                        foreach ($items as $key => $value) {
                            \App\Models\PurchaseOrderGeneralDetailItemTax::where('purchase_order_general_detail_item_id', $value->id)->delete();
                            $value->delete();
                        }

                        $model_additional->delete();
                    }
                }
            } else {
                // * find or create data additional
                $model_additional = \App\Models\PurchaseOrderGeneralDetail::where('purchase_order_general_id', $model->id)
                    ->where('type', 'additional')
                    ->where('id', $data->additional?->purchase_order_general_detail_id ?? null)
                    ->first();

                if ($model_additional) {
                    // * delete and create new detail item
                    $items = \App\Models\PurchaseOrderGeneralDetailItem::where('purchase_order_general_detail_id', $model_additional->id)->get();

                    foreach ($items as $key => $value) {
                        \App\Models\PurchaseOrderGeneralDetailItemTax::where('purchase_order_general_detail_item_id', $value->id)->delete();
                        $value->delete();
                    }

                    $model_additional->delete();
                }
            }

            // * ADDITIONAL ITEM ------------------------------------------------------------

            // ? update parent total
            $model->update([
                'total' => $total,
                'total_main' => $total_main,
                'total_additional' => $total_additional,
                'total_tax_main' => $total_tax_main,
                'total_tax_additional' => $total_tax_additional,
            ]);

            $authorization = new \App\Http\Helpers\AuthorizationHelper();

            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: \App\Models\PurchaseOrderGeneral::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "PO General",
                subtitle: Auth::user()->name . " mengajukan PO General " . $model->code,
                link: route('admin.purchase-order-general.show', $model),
                update_status_link: route('admin.purchase-order-general.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            $purchase_request_ids = array_merge($purchase_request_ids, $model->purchaseOrderGeneralDetails()
                ->where('type', 'main')
                ->pluck('purchase_request_id')
                ->toArray());

            foreach (array_unique($purchase_request_ids) as $purchase_request_id) {
                app('App\Http\Controllers\Admin\PurchaseRequestController')->check_purchase_request_status($purchase_request_id);
            }

            // ! END UPDATING DATA #########################################################
        } catch (\Throwable $th) {
            throw $th;
        }

        return;
    }
}
