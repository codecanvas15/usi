<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\InvoiceCoaHelpers;
use App\Http\Helpers\JournalHelpers;
use App\Http\Helpers\PrintHelper;
use App\Models\Authorization;
use App\Models\DeliveryOrder;
use App\Models\DownPaymentInvoice;
use App\Models\Employee;
use App\Models\InvoiceParent;
use App\Models\InvoiceTaxSummary;
use App\Models\InvoiceTrading as model;
use App\Models\InvoiceTrading;
use App\Models\InvoiceTradingCoa;
use App\Models\InvoiceTradingDetail;
use App\Models\InvoiceTradingTax;
use App\Models\InvTradingAddOn;
use App\Models\InvTradingAddOnTax;
use App\Models\Journal;
use App\Models\SaleOrderAdditional;
use App\Models\SoTrading;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\Node\Block\Document;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceTradingController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'invoice-trading';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = model::with(['so_trading.customer', 'printedData'])
                ->join('sale_orders', 'invoice_tradings.so_trading_id', '=', 'sale_orders.id')
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('invoice_tradings.branch_id', get_current_branch_id()))
                ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('invoice_tradings.branch_id', $request->branch_id))
                ->when($request->customer_id, fn($q) => $q->where('invoice_tradings.customer_id', $request->customer_id))
                ->when($request->status, fn($q) => $q->where('invoice_tradings.status', $request->status))
                ->when($request->from_date, fn($q) => $q->whereDate('invoice_tradings.date', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('invoice_tradings.date', '<=', Carbon::parse($request->to_date)))
                ->select('invoice_tradings.*', 'sale_orders.nomor_po_external');

            $checkAuthorizePrint = authorizePrint('invoice_trading');
            $checkAuthorizePrintReceipt = authorizePrint('invoice_trading_receipt');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('kode', function ($row) use ($checkAuthorizePrint, $checkAuthorizePrintReceipt) {
                    $html = view('components.datatable.detail-link', [
                        'field' => $row->kode,
                        'row' => $row,
                        'main' => $this->view_folder,
                    ]);

                    if ($row->reference) {
                        $html  .= "<br>FP. $row->reference";
                    }

                    $model = model::class;
                    $link = route('admin.invoice-trading.show', ['invoice_trading' => $row->id]);

                    $link_export = route('invoice-trading.export.id', ['id' => encryptId($row->id)]);
                    $html .=  '<br>' . view('components.button-auth-print', [
                        'type' => 'invoice_trading',
                        'href' => $link_export,
                        'model' => InvoiceTrading::class,
                        'did' => $row->id,
                        'link' => $link,
                        'code' => $row->code,
                        'condition' => $checkAuthorizePrint,
                        'size' => 'sm',
                        'label' => 'Invoice',
                    ]);

                    if ($row->is_separate_invoice) {
                        $link_export = route('invoice-trading.export.id', ['id' => encryptId($row->id), 'type' => 'transport']);
                        $html .= '<br>' .  view('components.button-auth-print', [
                            'type' => 'invoice_trading_transport',
                            'href' => $link_export,
                            'model' => InvoiceTrading::class,
                            'did' => $row->id,
                            'link' => $link,
                            'code' => $row->code,
                            'condition' => $checkAuthorizePrint,
                            'size' => 'sm',
                            'symbol' => '&',
                            'label' => 'Invoice Transport',
                        ]);
                    }

                    $link_export = route('invoice-trading.export.id.with-delivery-order', ['id' => encryptId($row->id)]);
                    $html .= '<a href="' . $link_export . '" class="mb-1 btn btn-sm btn-info" target="_blank" onclick="show_print_out_modal(event)"  ' . ($checkAuthorizePrint ? 'data-model="' . $model . '" data-id="' . $row->id . '" data-print-type="invoice_with_do" data-link="' . $link . '" data-code="' . $row->kode . '"' : '') . '>Export With DO</a>&nbsp;';
                    $link_receipt = route('invoice-trading.export-receipt.id', ['id' => encryptId($row->id)]);
                    $html .= "<a href='$link_receipt' class='mb-1 btn btn-sm btn-info' target='_blank' onclick='show_print_out_modal(event)' " . ($checkAuthorizePrintReceipt ? " data-model='$model' data-id='$row->id' data-print-type='invoice_trading_receipt' data-link='$link' data-code='$row->receipt_number'" : "") . ">Kwitansi</a>";


                    return $html;
                })
                ->editColumn('nomor_so', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->so_trading?->nomor_so,
                    'row' => $row->so_trading,
                    'main' => 'sales-order',
                ]))
                ->editColumn('date', fn($row) => localDate($row->date))
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . get_invoice_status()[$row->status]['color'] . '">
                                    ' . get_invoice_status()[$row->status]['label'] . ' - ' . get_invoice_status()[$row->status]['text'] . '
                                </div>';

                    return $badge;
                })
                ->editColumn('payment_status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . payment_status()[$row->payment_status]['color'] . '">
                                    ' . payment_status()[$row->payment_status]['label'] . ' - ' . payment_status()[$row->payment_status]['text'] . '
                                </div>';

                    return $badge;
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) : false,
                            ],
                            'delete' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) : false,
                            ],
                        ],
                    ]);
                })
                ->addColumn('print_status', function ($row) {
                    $text_status['without_do'] = 'Belum Dicetak';
                    $text_status['with_do'] = 'Belum Dicetak (With DO)';
                    $color_status['with_do'] = 'danger';
                    $color_status['without_do'] = 'danger';
                    $invoice = $row->printedData?->where('type', 'invoice')->last()?->status;
                    $delivery_order = $row->printedData?->where('type', 'invoice_with_do')->last()?->status;

                    if ($invoice == 'printed') {
                        $text_status['without_do'] = 'Sudah dicetak';
                        $color_status['without_do'] = 'success';
                    } else if ($invoice == 'pending') {
                        $text_status['without_do'] = 'Sedang diajukan';
                        $color_status['without_do'] = 'warning';
                    }

                    if ($delivery_order == 'printed') {
                        $text_status['with_do'] = 'Sudah dicetak (With DO)';
                        $color_status['with_do'] = 'success';
                    } else if ($delivery_order == 'pending') {
                        $text_status['with_do'] = 'Sedang diajukan (With DO)';
                        $color_status['with_do'] = 'warning';
                    }

                    $html = '';
                    foreach ($text_status as $key => $value) {
                        $html .= '<div class="badge badge-lg badge-' . $color_status[$key] . ' me-1">
                                    ' . $value . '
                                </div>';
                    }

                    return $html;
                })
                ->rawColumns(['kode', 'action', 'status',  'payment_status', 'export', 'export_with_delivery_order', 'print_status'])
                ->make(true);
        }

        return redirect()->route("admin.invoice.index");
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
        $model = model::findOrFail($id);

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == 'approve' && $model->payment_status == 'unpaid';
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve' && $model->payment_status == 'unpaid';
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->check_available_date && $model->status == 'approve' && $model->payment_status == 'unpaid';
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve' && $model->payment_status == 'unpaid';
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        $taxes_id = $model->invoice_trading_taxes;
        $taxes_id = $taxes_id->map(function ($tax) {
            return $tax->only(['tax_id', 'value']);
        });

        $invoice_trading_addon_taxes = InvTradingAddOnTax::whereHas('inv_trading_add_on', function ($q) use ($model) {
            $q->whereHas('invoice_trading', function ($q) use ($model) {
                $q->where('id', $model->id);
            });
        })->get();

        $addtion_taxes_id = $invoice_trading_addon_taxes->map(function ($tax) {
            return $tax->only(['tax_id', 'value']);
        });

        $taxes_id = $taxes_id->merge($addtion_taxes_id);
        $taxes_id = $taxes_id->unique(function ($tax) {
            return $tax['tax_id'] . $tax['value'];
        });

        $taxes = Tax::whereIn(
            'id',
            $taxes_id->pluck('tax_id')->toArray()
        )->get();

        $taxes_id = $taxes_id->map(function ($tax) use ($model, $invoice_trading_addon_taxes, $taxes) {
            $tax['amount'] = $model->invoice_trading_taxes
                ->where('value', $tax['value'])
                ->where('tax_id', $tax['tax_id'])
                ->sum('amount');
            $tax['amount'] += $invoice_trading_addon_taxes
                ->where('value', $tax['value'])
                ->where('tax_id', $tax['tax_id'])
                ->sum('total');
            $tax['name'] = $taxes->where('id', $tax['tax_id'])->first()->name;

            return $tax;
        });

        $invoice_parent = InvoiceParent::where('model_reference', model::class)->where('reference_id', $model->id)->first();
        $down_payments = DownPaymentInvoice::where('invoice_parent_id', $invoice_parent->id)->get();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'taxes_id', 'auth_revert_void_button', 'down_payments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = model::findOrFail($id);
        $invoice_parent = InvoiceParent::where('model_reference', model::class)->where('reference_id', $model->id)->first();
        $down_payments = DownPaymentInvoice::where('invoice_parent_id', $invoice_parent->id)->get();

        if (!in_array($model->status, ['pending', 'reject', 'revert'])) {
            abort(403);
        }

        if ($request->ajax()) {
            $data['model'] = $model;
            $data['delivery_order_selected'] = DeliveryOrder::whereIn('id', $model->invoice_trading_details->pluck('delivery_order_id'))->get();
            $data['so_trading_taxes'] = $model->so_trading->sale_order_taxes;
            $data['customer'] = $model->customer;
            $data['currency'] = $model->currency;
            $data['sh_number'] = $model->so_trading->sh_number;
            $data['so_trading'] = $model->so_trading;
            $data['so_trading_detail'] = $model->so_trading->so_trading_detail;
            $data['delivery_order'] = DeliveryOrder::whereIn('id', $model->invoice_trading_details->pluck('delivery_order_id'))->get();
            $data['quantity_sale_order'] = $model->so_trading->so_trading_detail->jumlah;
            $data['price_sale_order'] = $model->so_trading->so_trading_detail->harga;
            $data['sale_order_additionals'] = SaleOrderAdditional::where('sale_order_id', $model->so_trading->id)
                ->with('sale_order_additional_taxes.tax', 'item')
                ->get();
            $data['unit'] = $model->so_trading->so_trading_detail->item->unit->name ?? '';

            return response()->json($data);
        }

        return view("admin.$this->view_folder.edit", compact('model', 'down_payments'));
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
        $model = model::findOrFail($id);

        DB::beginTransaction();

        if (!in_array($model->status, ['pending', 'reject', 'revert'])) {
            abort(403);
        }

        $sale_order_data = $model->so_trading;

        $SALE_ORDER_PRICE = $sale_order_data->so_trading_detail->harga;

        $QUANTITY_SALE_ORDER_FINAL = 0;
        $QUANTITY_SALE_ORDER_FINAL_CUSTOM = thousand_to_float($request->quantity_for_invoice_after ?? 0);
        $LOST_TOLERANCE_TYPE = $model->lost_tolerance_type;
        $LOST_TOLERANCE = thousand_to_float($request->lost_tolerance);

        $TOTAL_SENDED = 0;
        $TOTAL_RECEIVED = 0;
        $TOTAL_LOST = 0;

        $SUB_TOTAL = 0;
        $SUB_TOTAL_AFTER_TAX = 0;
        $ADDITIONAL_SUB_TOTAL = 0;
        $ADDITIONAL_TOTAL = 0;
        $ADDITIONAL_TOTAL_AFTER_TAX = 0;
        $TOTAL_ALL = 0;

        $DELIVERY_ORDERS = DeliveryOrder::whereIn('id', $model->invoice_trading_details->pluck('delivery_order_id'))
            ->get();

        if ($model->calculate_from == 'sales_order') {
            foreach ($DELIVERY_ORDERS as $key => $value) {
                $TOTAL_SENDED += $value->load_quantity_realization;
                $TOTAL_RECEIVED += $value->unload_quantity_realization;
                $TOTAL_LOST += $value->load_quantity_realization - $value->unload_quantity_realization;
            }

            if ($LOST_TOLERANCE_TYPE == 'percent') {
                $LOST_TOLERANCE /= 100;
                $QTY_TOLERANCE = ($TOTAL_SENDED * $LOST_TOLERANCE);

                if ($TOTAL_LOST > $QTY_TOLERANCE) {
                    $QUANTITY_SALE_ORDER_FINAL = ($TOTAL_RECEIVED + $QTY_TOLERANCE);
                    $SUB_TOTAL = ($TOTAL_RECEIVED + $QTY_TOLERANCE) * $SALE_ORDER_PRICE;
                } else {
                    $QUANTITY_SALE_ORDER_FINAL = $TOTAL_SENDED;
                    $SUB_TOTAL = $TOTAL_SENDED * $SALE_ORDER_PRICE;
                }
            } elseif ($LOST_TOLERANCE_TYPE == 'liter') {
                if ($TOTAL_LOST > $LOST_TOLERANCE) {
                    $SUB_TOTAL = $SALE_ORDER_PRICE * ($TOTAL_RECEIVED + $LOST_TOLERANCE);
                    $QUANTITY_SALE_ORDER_FINAL = $TOTAL_RECEIVED + $LOST_TOLERANCE;
                } else {
                    $SUB_TOTAL = $SALE_ORDER_PRICE * $TOTAL_SENDED;
                    $QUANTITY_SALE_ORDER_FINAL = $TOTAL_SENDED;
                }
            } else {
                return redirect()->back()->with($this->ResponseMessageCrud(false, 'edit', 'update invoice', 'lost tolerance type not available'));
            }

            if ($QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0) {
                $SUB_TOTAL = $SALE_ORDER_PRICE * $QUANTITY_SALE_ORDER_FINAL_CUSTOM;
            }
        } elseif ($model->calculate_from == 'delivery_order') {
            if ($LOST_TOLERANCE_TYPE == 'percent') {
                $LOST_TOLERANCE /= 100;
            }

            $QUANTITY_SALE_ORDER_FINAL = 0;
            foreach ($DELIVERY_ORDERS as $DELIVERY_ORDER_KEY => $DELIVERY_ORDER) {
                $TOTAL_SENDED += $DELIVERY_ORDER->load_quantity_realization;
                $TOTAL_RECEIVED += $DELIVERY_ORDER->unload_quantity_realization;

                $single_sended = $DELIVERY_ORDER->load_quantity_realization;
                $single_received = $DELIVERY_ORDER->unload_quantity_realization;
                $single_lost = $single_sended - $single_received;

                $TOLERANCE = ($single_sended * $LOST_TOLERANCE);

                if ($LOST_TOLERANCE_TYPE == 'percent') {
                    $total_lost_as_percent = $single_lost / $single_sended;
                    if ($total_lost_as_percent > $LOST_TOLERANCE) {
                        $QUANTITY_SALE_ORDER_FINAL += $single_received + $TOLERANCE;
                    } else {
                        $SUB_TOTAL += $single_sended * $SALE_ORDER_PRICE;
                        $QUANTITY_SALE_ORDER_FINAL += $single_sended;
                    }
                } elseif ($LOST_TOLERANCE_TYPE == 'liter') {
                    if ($single_lost > $LOST_TOLERANCE) {
                        $QUANTITY_SALE_ORDER_FINAL += $single_received;
                    } else {
                        $SUB_TOTAL += $single_sended * $SALE_ORDER_PRICE;
                        $QUANTITY_SALE_ORDER_FINAL += $single_sended;
                    }
                } else {
                    return redirect()->back()->with($this->ResponseMessageCrud(false, 'edit', 'update invoice', 'lost tolerance type not available'));
                }
            }

            $SUB_TOTAL = $QUANTITY_SALE_ORDER_FINAL * $SALE_ORDER_PRICE;

            if ($QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0) {
                $SUB_TOTAL = $SALE_ORDER_PRICE * $QUANTITY_SALE_ORDER_FINAL_CUSTOM;
            }
        } else {
            return redirect()->back()->with($this->ResponseMessageCrud(false, 'edit', 'update invoice', 'calculate from not available'));
        }

        $SUB_TOTAL_AFTER_TAX = $SUB_TOTAL;

        $due = $request->due ?? $sale_order_data->customer->due ?? 0;
        $model->loadModel([
            'so_trading_id' => $model->so_trading_id,
            'reference' => $request->reference,
            'date' => Carbon::parse($request->date),
            'due' => $due,
            'due_date' => Carbon::parse($request->date)->addDays($due),
            'bank_internal_id' => $request->bank_internal_id[0] ?? null,
            'bank_internal_ids' => $request->bank_internal_id ?? [],
            'total_jumlah_diterima' => $TOTAL_RECEIVED,
            'total' => $SUB_TOTAL_AFTER_TAX,
            'lost_tolerance' => $LOST_TOLERANCE,
            'lost_tolerance_type' => $LOST_TOLERANCE_TYPE,
            'tolerance_amount' => $LOST_TOLERANCE,
            'total_lost' => $TOTAL_LOST,
            'total_jumlah_dikirim' => $TOTAL_SENDED,
            'jumlah' => $QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0 ? $QUANTITY_SALE_ORDER_FINAL_CUSTOM : $QUANTITY_SALE_ORDER_FINAL,
            'harga' => $SALE_ORDER_PRICE,
            'subtotal' => $SUB_TOTAL,
            'subtotal_after_tax' => $SUB_TOTAL_AFTER_TAX,
            'additional_tax_total' => 0,
            'after_additional_tax' => 0,
            'other_cost' => 0,
            'total_other_cost' => 0,
            'is_separate_invoice' => $request->is_separate_invoice ?? false,
        ]);

        try {
            if ($request->hasFile('attachment')) {
                Storage::delete($model->attachment);
                $model->attachment = $this->upload_file($request->file('attachment'), 'invoice_trading');
            }
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCrud(false, 'edit', 'update invoice', $th->getMessage()));
        }

        foreach ($sale_order_data->sale_order_taxes as $tax_key => $tax) {
            $model_tax = InvoiceTradingTax::where('invoice_trading_id', $model->id)
                ->where('tax_id', $tax->tax_id)
                ->first();

            if (!$model_tax) {
                $model_tax = new InvoiceTradingTax();
            }

            $model_tax->loadModel([
                'invoice_trading_id' => $model->id,
                'tax_id' => $tax->tax_id,
                'value' => $tax->value,
                'amount' => $SUB_TOTAL * $tax->value,
            ]);

            try {
                $model_tax->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCrud(false, 'edit', 'update invoice', $th->getMessage()));
            }

            $SUB_TOTAL_AFTER_TAX += $SUB_TOTAL * $tax->value;
        }

        InvTradingAddOnTax::whereIn('inv_trading_add_on_id', $model->inv_trading_add_on()->pluck('id'))
            ->delete();

        InvTradingAddOn::where('invoice_trading_id', $model->id)->delete();

        foreach ($sale_order_data->sale_order_additionals as $additional_key => $additional) {
            $final_qty = $QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0 ? $QUANTITY_SALE_ORDER_FINAL_CUSTOM : $QUANTITY_SALE_ORDER_FINAL;
            $single_additional_sub_total = $final_qty * $additional->price;
            $single_additional_total = $final_qty * $additional->price;
            $single_additional_tax_total = 0;

            $model_additional = new InvTradingAddOn();
            $model_additional->loadModel([
                'invoice_trading_id' => $model->id,
                'item_id' => $additional->item_id,
                'quantity' => $final_qty,
                'price' => $additional->price,
                'sub_total' => $single_additional_sub_total,
                'total' => $single_additional_total,
            ]);

            try {
                $model_additional->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCrud(false, 'edit', 'update invoice', $th->getMessage()));
            }

            foreach ($additional->sale_order_additional_taxes as $tax_key => $tax) {
                $single_additional_tax_total += $single_additional_sub_total * $tax->value;

                $model_additional_tax = new InvTradingAddOnTax();
                $model_additional_tax->loadModel([
                    'inv_trading_add_on_id' => $model_additional->id,
                    'tax_id' => $tax->tax_id,
                    'value' => $tax->value,
                    'total' => $single_additional_tax_total,
                ]);

                try {
                    $model_additional_tax->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return redirect()->back()->with($this->ResponseMessageCrud(false, 'edit', 'update invoice', $th->getMessage()));
                }

                $single_additional_total += $single_additional_tax_total;
            }

            $model_additional->total = $single_additional_total;
            try {
                $model_additional->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCrud(false, 'edit', 'update invoice', $th->getMessage()));
            }

            $ADDITIONAL_SUB_TOTAL += $single_additional_sub_total;
            $ADDITIONAL_TOTAL += $single_additional_total;
            $ADDITIONAL_TOTAL_AFTER_TAX += $single_additional_tax_total;
        }

        $model = \App\Models\InvoiceTrading::find($model->id);
        $model->loadModel([
            'subtotal_after_tax' => $SUB_TOTAL_AFTER_TAX,
            'total' => $SUB_TOTAL_AFTER_TAX + $ADDITIONAL_TOTAL,
            'additional_tax_total' => $ADDITIONAL_TOTAL_AFTER_TAX,
            'after_additional_tax' => $ADDITIONAL_SUB_TOTAL + $ADDITIONAL_TOTAL_AFTER_TAX,
            'other_cost' => $ADDITIONAL_SUB_TOTAL,
            'total_other_cost' => $ADDITIONAL_TOTAL,
        ]);

        try {
            $model->save();

            $invoice_parent = InvoiceParent::where('model_reference', model::class)->where('reference_id', $model->id)->first();
            if ($request->invoice_down_payment_id) {
                DownPaymentInvoice::whereNotIn('invoice_down_payment_id', $request->invoice_down_payment_id)->where('invoice_parent_id', $invoice_parent->id)->delete();
                foreach ($request->invoice_down_payment_id as $key => $invoice_down_payment_id) {
                    DownPaymentInvoice::updateOrCreate(
                        [
                            'invoice_parent_id' => $invoice_parent->id,
                            'invoice_down_payment_id' => $invoice_down_payment_id,
                        ],
                        [
                            'invoice_parent_id' => $invoice_parent->id,
                            'invoice_down_payment_id' => $invoice_down_payment_id,
                        ]
                    );
                }
            } else {
                DownPaymentInvoice::where('invoice_parent_id', $invoice_parent->id)->delete();
            }

            $all_invoice_taxes = new Collection();

            $invoice_trading_detail_taxes = InvoiceTradingTax::where('invoice_trading_id', $model->id)
                ->get();

            $invoice_trading_detail_taxes = $invoice_trading_detail_taxes->map(function ($item) {
                $item->total = $item->amount;
                return $item;
            });

            $invoice_trading_detail_taxes->map(function ($item) use (&$all_invoice_taxes) {
                $all_invoice_taxes->push($item);
            });

            $invoice_trading_additional_taxes = InvTradingAddOnTax::whereHas('inv_trading_add_on', function ($q) use ($model) {
                $q->where('invoice_trading_id', $model->id);
            })
                ->get();

            $invoice_trading_additional_taxes->map(function ($item) use (&$all_invoice_taxes) {
                $all_invoice_taxes->push($item);
            });

            $all_invoice_taxes = $all_invoice_taxes
                ->groupBy('tax_id')
                ->map(function ($item) {
                    return $item->groupBy('value');
                });

            InvoiceTaxSummary::where('model_class', model::class)
                ->where('model_id', $model->id)
                ->delete();

            foreach ($all_invoice_taxes as $key => $all_invoice_tax) {
                foreach ($all_invoice_tax as $key2 => $all_invoice_tax2) {
                    InvoiceTaxSummary::create([
                        'model_class' => get_class($model),
                        'model_id' => $model->id,
                        'tax_id' => $key,
                        'tax_value' => $all_invoice_tax2->first()->value,
                        'tax_amount' => $all_invoice_tax2->sum('total'),
                    ]);
                }
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Invoice Trading",
                subtitle: Auth::user()->name . " mengajukan Invoice Trading " . $model->kode,
                link: route('admin.invoice-trading.show', $model),
                update_status_link: route('admin.invoice-trading.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCrud(false, 'edit', 'update invoice', $th->getMessage()));
        }

        try {
            InvoiceTradingCoa::where('invoice_trading_id', $model->id)->delete();
            $coa_helper = new InvoiceCoaHelpers($model->id, 'invoice-trading');
            $coa_helper->generateCoaDataForInvoiceTrading();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCrud(false, 'edit', 'update invoice', $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.invoice-trading.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
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
        $model = model::findOrFail($id);
        DB::beginTransaction();
        try {
            $model->delete();

            Authorization::where('model', model::class)->where('model_id', $model->id)->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }
        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(false, 'delete');
        }

        return redirect()->route("admin.invoice.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        if ($request->search) {
            $model = model::where('nama', 'like', "%$request->search%")->orderByDesc('created_at')->limit(10)->get();
        } else {
            $model = model::orderByDesc('created_at')->limit(10)->get();
        }

        return $this->ResponseJsonData($model);
    }

    /**
     * update status
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        $model = model::with(['invoice_trading_details.delivery_order'])->findOrFail($id);

        if ($request->status === 'approve') {
            $details = $model->invoice_trading_details;

            $doneCount = 0;
            $detailCount = count($details);

            foreach ($details as $detail) {
                if ($detail->delivery_order->status === 'done') {
                    $doneCount++;
                }
            }

            if ($detailCount !== $doneCount) {
                return redirect()->back()->with($this->ResponseMessageCrud(false, 'update', 'update status', 'Terdapat DO dengan status belum Done'));
            }
        }

        DB::beginTransaction();

        $this->validate($request, [
            'status' => 'required'
        ]);

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->status = $request->status;
                if ($model->status == 'approve') {
                    $model->approved_by = Auth::user()->id;
                }
                $model->save();
            } else {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
            return redirect()->back()->with($this->ResponseMessageCrud(false, 'update', 'update status', $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCrud(true, 'update', 'update status'));
    }

    /**
     * generate invoice
     *
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function generate($id)
    {
        $model = SoTrading::findOrFail($id);

        return view("admin.$this->view_folder.generate", compact('model'));
    }

    /**
     * store_generate
     *
     * @param Request $request
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function store_generate(Request $request, $id)
    {
        $sale_order_data = SoTrading::findOrFail($id);

        if (!$sale_order_data->nomor_po_external && !$request->nomor_po_external) {
            return redirect()->back()->with($this->ResponseMessageCrud(false, 'update', 'update status', 'Nomor PO External tidak boleh kosong'));
        }

        if (!$sale_order_data->nomor_po_external) {
            DB::table('sale_orders')
                ->where('id', $id)
                ->update([
                    'nomor_po_external' => $request->nomor_po_external
                ]);
        }

        // * VARIABLES ############################################
        $SALE_ORDER_PRICE = $sale_order_data->so_trading_detail->harga;

        $DELIVERY_ORDER_ID_LIST = [];

        $QUANTITY_SALE_ORDER_FINAL = 0;
        $QUANTITY_SALE_ORDER_FINAL_CUSTOM = thousand_to_float($request->quantity_for_invoice_after ?? 0);
        $LOST_TOLERANCE_TYPE = $request->lost_tolerance_type;
        $LOST_TOLERANCE = thousand_to_float($request->lost_tolerance);
        $TOTAL_SENDED = 0;
        $TOTAL_RECEIVED = 0;
        $TOTAL_LOST = 0;

        $SUB_TOTAL = 0;
        $SUB_TOTAL_AFTER_TAX = 0;
        $ADDITIONAL_SUB_TOTAL = 0;
        $ADDITIONAL_TOTAL = 0;
        $ADDITIONAL_TOTAL_AFTER_TAX = 0;
        $TOTAL_ALL = 0;

        // * END VARIABLES ############################################

        // * DELIVERY ORDERS ############################################
        // find checked delivery order
        if (is_array($request->delivery_order_transport_id)) {
            foreach ($request->delivery_order_transport_id as $key => $value) {
                // if checked
                if ($request->delivery_order_transport[$key] == 'on') {
                    $DELIVERY_ORDER_ID_LIST[] = $value;
                }
            }
        }

        // get checked delivery order
        $DELIVERY_ORDERS = DeliveryOrder::whereIn('id', $DELIVERY_ORDER_ID_LIST)
            ->where('is_invoice_created', false)
            ->get();

        // * END DELIVERY ORDERS ############################################

        // ! CALCULATE ############################################

        if ($request->calculate_from == 'sales_order') {
            // ? CALCULATE FROM SALES ORDER

            // * DELIVERY ORDERS ############################################
            foreach ($DELIVERY_ORDERS as $key => $value) {
                $TOTAL_SENDED += $value->load_quantity_realization;
                $TOTAL_RECEIVED += $value->unload_quantity_realization;
                $TOTAL_LOST += $value->load_quantity_realization - $value->unload_quantity_realization;
            }
            // * END DELIVERY ORDERS ############################################

            // * CALCULATE ############################################

            // calculate sub total
            if ($LOST_TOLERANCE_TYPE == 'percent') {
                $LOST_TOLERANCE /= 100;
                $QTY_TOLERANCE = ($TOTAL_SENDED * $LOST_TOLERANCE);

                if ($TOTAL_LOST > $QTY_TOLERANCE) {
                    $QUANTITY_SALE_ORDER_FINAL = ($TOTAL_RECEIVED + $QTY_TOLERANCE);
                    $SUB_TOTAL = ($TOTAL_RECEIVED + $QTY_TOLERANCE) * $SALE_ORDER_PRICE;
                } else {
                    $QUANTITY_SALE_ORDER_FINAL = $TOTAL_SENDED;
                    $SUB_TOTAL = $TOTAL_SENDED * $SALE_ORDER_PRICE;
                }
            } elseif ($LOST_TOLERANCE_TYPE == 'liter') {
                // if lost tolerance is more than liter
                if ($TOTAL_LOST > $LOST_TOLERANCE) {
                    $SUB_TOTAL = $SALE_ORDER_PRICE * ($TOTAL_RECEIVED + $LOST_TOLERANCE);
                    $QUANTITY_SALE_ORDER_FINAL = $TOTAL_RECEIVED + $LOST_TOLERANCE;
                } else {
                    $SUB_TOTAL = $SALE_ORDER_PRICE * $TOTAL_SENDED;
                    $QUANTITY_SALE_ORDER_FINAL = $TOTAL_SENDED;
                }
            } else {
                return redirect()->back()->with($this->ResponseMessageCrud(false, 'create', 'generate invoice', 'lost tolerance type not available'));
            }

            if ($QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0) {
                $SUB_TOTAL = $SALE_ORDER_PRICE * $QUANTITY_SALE_ORDER_FINAL_CUSTOM;
            }

            // * END CALCULATE ############################################
        } elseif ($request->calculate_from == 'delivery_order') {
            // ? CALCULATE FROM DELIVERY ORDER

            // * CALCULATE ############################################
            if ($LOST_TOLERANCE_TYPE == 'percent') {
                $LOST_TOLERANCE /= 100;
            }

            $QUANTITY_SALE_ORDER_FINAL = 0;
            foreach ($DELIVERY_ORDERS as $DELIVERY_ORDER_KEY => $DELIVERY_ORDER) {
                $TOTAL_SENDED += $DELIVERY_ORDER->load_quantity_realization;
                $TOTAL_RECEIVED += $DELIVERY_ORDER->unload_quantity_realization;

                $single_sended = $DELIVERY_ORDER->load_quantity_realization;
                $single_received = $DELIVERY_ORDER->unload_quantity_realization;
                $single_lost = $single_sended - $single_received;

                $TOLERANCE = ($single_sended * $LOST_TOLERANCE);

                // calculate sub total
                if ($LOST_TOLERANCE_TYPE == 'percent') {
                    $total_lost_as_percent = $single_lost / $single_sended;

                    // if lost tolerance is more than percent
                    if ($total_lost_as_percent > $LOST_TOLERANCE) {
                        $QUANTITY_SALE_ORDER_FINAL += $single_received + $TOLERANCE;
                    } else {
                        $SUB_TOTAL += $single_sended * $SALE_ORDER_PRICE;
                        $QUANTITY_SALE_ORDER_FINAL += $single_sended;
                    }
                } elseif ($LOST_TOLERANCE_TYPE == 'liter') {
                    // if lost tolerance is more than liter
                    if ($single_lost > $LOST_TOLERANCE) {
                        $QUANTITY_SALE_ORDER_FINAL += $single_received;
                    } else {
                        $SUB_TOTAL += $single_sended * $SALE_ORDER_PRICE;
                        $QUANTITY_SALE_ORDER_FINAL += $single_sended;
                    }
                } else {
                    return redirect()->back()->with($this->ResponseMessageCrud(false, 'create', 'generate invoice', 'lost tolerance type not available'));
                }
            }

            $SUB_TOTAL = $QUANTITY_SALE_ORDER_FINAL * $SALE_ORDER_PRICE;

            if ($QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0) {
                $SUB_TOTAL = $SALE_ORDER_PRICE * $QUANTITY_SALE_ORDER_FINAL_CUSTOM;
            }

            // * END CALCULATE ############################################
        } else {
            return redirect()->back()->with($this->ResponseMessageCrud(false, 'create', 'generate invoice', 'calculate from not available'));
        }

        // ! END CALCULATE ############################################

        // ! CREATE DATA INVOICE ############################################
        DB::beginTransaction();

        // *
        $SUB_TOTAL_AFTER_TAX = $SUB_TOTAL;

        // Generate code old
        // generate_code_transaction("INVT", model::orderBy('id', 'desc')->first()?->kode ?? null, date: $request->date)

        $last_receipt = model::withTrashed()
            ->whereMonth('date', Carbon::parse($request->date))
            ->whereYear('date', Carbon::parse($request->date))
            ->orderBy('id', 'desc')
            ->first();

        $receipt_code = generate_receipt_code($last_receipt->receipt_number ?? null, $request->date, 'KW');

        // * create parent
        $model = new model();
        $due = $request->due ?? $sale_order_data->customer->due ?? 0;
        $model->loadModel([
            'receipt_number' => $receipt_code,
            'branch_id' => $sale_order_data->branch_id,
            'reference' => $request->reference,
            'customer_id' => $sale_order_data->customer_id,
            'item_id' => $sale_order_data->so_trading_detail->item_id,
            'currency_id' => $sale_order_data->currency_id,
            'exchange_rate' => $sale_order_data->exchange_rate ?? 0,
            'date' => Carbon::parse($request->date),
            'due' => $due,
            'due_date' => Carbon::parse($request->date)->addDays($due),
            'bank_internal_id' => $request->bank_internal_id[0] ?? null,
            'bank_internal_ids' => $request->bank_internal_id ?? [],
            'so_trading_id' => $sale_order_data->id,
            'nomor_po_external' => $sale_order_data->nomor_po_external,
            'total_jumlah_diterima' => $TOTAL_RECEIVED,
            'total' => $SUB_TOTAL_AFTER_TAX,
            'calculate_from' => $request->calculate_from,
            'lost_tolerance' => $LOST_TOLERANCE,
            'lost_tolerance_type' => $LOST_TOLERANCE_TYPE,
            'tolerance_amount' => $LOST_TOLERANCE,
            'total_lost' => $TOTAL_LOST,
            'total_jumlah_dikirim' => $TOTAL_SENDED,
            'jumlah' => $QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0 ? $QUANTITY_SALE_ORDER_FINAL_CUSTOM : $QUANTITY_SALE_ORDER_FINAL,
            'harga' => $SALE_ORDER_PRICE,
            'subtotal' => $SUB_TOTAL,
            'subtotal_after_tax' => $SUB_TOTAL_AFTER_TAX,
            'additional_tax_total' => 0,
            'after_additional_tax' => 0,
            'other_cost' => 0,
            'total_other_cost' => 0,
            'created_by' => Auth::user()->id,
            'is_separate_invoice' => $request->is_separate_invoice ?? false,
        ]);

        try {
            if ($request->hasFile('attachment')) {
                $model->attachment = $this->upload_file($request->file('attachment'), 'invoice_trading');
            }
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCrud(false, 'create', 'generate invoice', $th->getMessage()));
        }

        // * create tax
        foreach ($sale_order_data->sale_order_taxes as $tax_key => $tax) {
            $model_tax = new InvoiceTradingTax();
            $model_tax->loadModel([
                'invoice_trading_id' => $model->id,
                'tax_id' => $tax->tax_id,
                'value' => $tax->value,
                'amount' => $SUB_TOTAL * $tax->value,
            ]);

            try {
                $model_tax->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCrud(false, 'create', 'generate invoice', $th->getMessage()));
            }

            $SUB_TOTAL_AFTER_TAX += $SUB_TOTAL * $tax->value;
        }

        // * create child
        foreach ($DELIVERY_ORDERS as $key => $value) {
            $model_child = new InvoiceTradingDetail();
            $model_child->loadModel([
                'invoice_trading_id' => $model->id,
                'delivery_order_id' => $value->id,
                'jumlah_dikirim' => $value->load_quantity_realization,
                'jumlah_diterima' => $value->unload_quantity_realization,
            ]);

            try {
                $model_child->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCrud(false, 'create', 'generate invoice', $th->getMessage()));
            }
        }

        // * create additional
        foreach ($sale_order_data->sale_order_additionals as $additional_key => $additional) {
            $final_qty = $QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0 ? $QUANTITY_SALE_ORDER_FINAL_CUSTOM : $QUANTITY_SALE_ORDER_FINAL;
            $single_additional_sub_total = $final_qty * $additional->price;
            $single_additional_total = $final_qty * $additional->price;
            $single_additional_tax_total = 0;

            // * create parent additional
            $model_additional = new InvTradingAddOn();
            $model_additional->loadModel([
                'invoice_trading_id' => $model->id,
                'item_id' => $additional->item_id,
                'quantity' => $final_qty,
                'price' => $additional->price,
                'sub_total' => $single_additional_sub_total,
                'total' => $single_additional_total,
            ]);

            try {
                $model_additional->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCrud(false, 'create', 'generate invoice', $th->getMessage()));
            }

            // * create tax additional
            foreach ($additional->sale_order_additional_taxes as $tax_key => $tax) {
                $single_additional_tax_total += $single_additional_sub_total * $tax->value;

                $model_additional_tax = new InvTradingAddOnTax();
                $model_additional_tax->loadModel([
                    'inv_trading_add_on_id' => $model_additional->id,
                    'tax_id' => $tax->tax_id,
                    'value' => $tax->value,
                    'total' => $single_additional_tax_total,
                ]);

                try {
                    $model_additional_tax->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return redirect()->back()->with($this->ResponseMessageCrud(false, 'create', 'generate invoice', $th->getMessage()));
                }

                $single_additional_total += $single_additional_tax_total;
            }

            // * update parent additional
            $model_additional->total = $single_additional_total;
            try {
                $model_additional->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCrud(false, 'create', 'generate invoice', $th->getMessage()));
            }

            $ADDITIONAL_SUB_TOTAL += $single_additional_sub_total;
            $ADDITIONAL_TOTAL += $single_additional_total;
            $ADDITIONAL_TOTAL_AFTER_TAX += $single_additional_tax_total;
        }

        // * update parent
        $model = \App\Models\InvoiceTrading::find($model->id);
        $model->loadModel([
            'subtotal_after_tax' => $SUB_TOTAL_AFTER_TAX,
            'total' => $SUB_TOTAL_AFTER_TAX + $ADDITIONAL_TOTAL,
            'additional_tax_total' => $ADDITIONAL_TOTAL_AFTER_TAX,
            'after_additional_tax' => $ADDITIONAL_SUB_TOTAL + $ADDITIONAL_TOTAL_AFTER_TAX,
            'other_cost' => $ADDITIONAL_SUB_TOTAL,
            'total_other_cost' => $ADDITIONAL_TOTAL,
        ]);

        try {
            $model->save();

            if ($request->invoice_down_payment_id) {
                $invoice_parent = InvoiceParent::where('model_reference', model::class)->where('reference_id', $model->id)->first();
                foreach ($request->invoice_down_payment_id as $key => $invoice_down_payment_id) {
                    DownPaymentInvoice::create([
                        'invoice_parent_id' => $invoice_parent->id,
                        'invoice_down_payment_id' => $invoice_down_payment_id,
                    ]);
                }
            }

            $all_invoice_taxes = new Collection();
            // !! create invoice tax summary
            $invoice_trading_detail_taxes = InvoiceTradingTax::where('invoice_trading_id', $model->id)
                ->get();

            $invoice_trading_detail_taxes = $invoice_trading_detail_taxes->map(function ($item) {
                $item->total = $item->amount;
                return $item;
            });

            $invoice_trading_detail_taxes->map(function ($item) use (&$all_invoice_taxes) {
                $all_invoice_taxes->push($item);
            });

            $invoice_trading_additional_taxes = InvTradingAddOnTax::whereHas('inv_trading_add_on', function ($q) use ($model) {
                $q->where('invoice_trading_id', $model->id);
            })
                ->get();

            $invoice_trading_additional_taxes->map(function ($item) use (&$all_invoice_taxes) {
                $all_invoice_taxes->push($item);
            });

            $all_invoice_taxes = $all_invoice_taxes
                ->groupBy('tax_id')
                ->map(function ($item) {
                    return $item->groupBy('value');
                });

            foreach ($all_invoice_taxes as $key => $all_invoice_tax) {
                foreach ($all_invoice_tax as $key2 => $all_invoice_tax2) {
                    InvoiceTaxSummary::create([
                        'model_class' => get_class($model),
                        'model_id' => $model->id,
                        'tax_id' => $key,
                        'tax_value' => $all_invoice_tax2->first()->value,
                        'tax_amount' => $all_invoice_tax2->sum('total'),
                    ]);
                }
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Invoice Trading",
                subtitle: Auth::user()->name . " mengajukan Invoice Trading " . $model->kode,
                link: route('admin.invoice-trading.show', $model),
                update_status_link: route('admin.invoice-trading.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCrud(false, 'create', 'generate invoice', $th->getMessage()));
        }
        // ! END CREATE DATA INVOICE ############################################

        // ! Invoice Trading Coa #######################################

        try {
            $coa_helper = new InvoiceCoaHelpers($model->id, 'invoice-trading');
            $coa_helper->generateCoaDataForInvoiceTrading();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCrud(false, 'create', 'generate invoice', $th->getMessage()));
        }

        // ! End Invoice Trading Coa #######################################

        DB::commit();
        return redirect()->route("admin.invoice.index")->with($this->ResponseMessageCrud(true, 'create', 'generate invoice'));
    }

    public function export($id, Request $request)
    {
        set_time_limit(300);
        $model = model::with('customer', 'bank_internal', 'approved_by_user')->findOrFail(decryptId($id));
        if ($model->is_separate_invoice) {
            if ($request->type == 'transport') {
                if (!$request->preview && authorizePrint('invoice_trading_transport')) {
                    $document_print = new PrintHelper();
                    $result = $document_print->check_available_for_print(
                        model::class,
                        decryptId($id),
                        'invoice_trading_transport',
                    );

                    if (!$result) {
                        return abort(403);
                    }
                }

                $model->kode .= '-TP';
            } else {
                if (!$request->preview && authorizePrint('invoice_trading')) {
                    $document_print = new PrintHelper();
                    $result = $document_print->check_available_for_print(
                        model::class,
                        decryptId($id),
                        'invoice_trading',
                    );

                    if (!$result) {
                        return abort(403);
                    }
                }

                $model->kode .= '-BBM';
            }
        } else {
            if (!$request->preview && authorizePrint('invoice_trading')) {
                $document_print = new PrintHelper();
                $result = $document_print->check_available_for_print(
                    model::class,
                    decryptId($id),
                    'invoice_trading',
                );

                if (!$result) {
                    return abort(403);
                }
            }
        }

        $taxes_id = $model->invoice_trading_taxes;
        $taxes_id = $taxes_id->map(function ($tax) {
            return $tax->only(['tax_id', 'value']);
        });

        $invoice_trading_addon_taxes = InvTradingAddOnTax::whereHas('inv_trading_add_on', function ($q) use ($model) {
            $q->whereHas('invoice_trading', function ($q) use ($model) {
                $q->where('id', $model->id);
            });
        })->get();

        $addtion_taxes_id = $invoice_trading_addon_taxes->map(function ($tax) {
            return $tax->only(['tax_id', 'value']);
        });

        if ($model->is_separate_invoice) {
            if ($request->type == 'transport') {
                $taxes_id = $addtion_taxes_id;
            } else {
                $taxes_id = $taxes_id;
            }
        } else {
            $taxes_id = $taxes_id->merge($addtion_taxes_id);
        }

        $taxes_id = $taxes_id->unique(function ($tax) {
            return $tax['tax_id'] . $tax['value'];
        });

        $taxes = Tax::whereIn(
            'id',
            $taxes_id->pluck('tax_id')->toArray()
        )->get();

        $taxes_id = $taxes_id->map(function ($tax) use ($model, $invoice_trading_addon_taxes, $taxes, $request) {
            if ($model->is_separate_invoice) {
                if ($request->type == 'transport') {
                    $tax['amount'] = $invoice_trading_addon_taxes
                        ->where('value', $tax['value'])
                        ->where('tax_id', $tax['tax_id'])
                        ->sum('total');
                } else {
                    $tax['amount'] = $model->invoice_trading_taxes
                        ->where('value', $tax['value'])
                        ->where('tax_id', $tax['tax_id'])
                        ->sum('amount');
                }
            } else {
                $tax['amount'] = $model->invoice_trading_taxes
                    ->where('value', $tax['value'])
                    ->where('tax_id', $tax['tax_id'])
                    ->sum('amount');
                $tax['amount'] += $invoice_trading_addon_taxes
                    ->where('value', $tax['value'])
                    ->where('tax_id', $tax['tax_id'])
                    ->sum('total');
            }

            $tax_data  = $taxes->where('id', $tax['tax_id'])->first();
            $tax['name'] = $tax_data->is_show_percent ? $tax_data->tax_name_with_percent : $tax_data->tax_name_without_percent;

            return $tax;
        });

        $file = public_path('/pdf_reports/Report-Invoice-Trading-' . microtime(true) . '.pdf');
        $fileName = 'Report-Invoice-Trading-' . microtime(true) . '.pdf';

        $qr_url = route('invoice-trading.export.id', ['id' => encryptId($model->id)]) . '?preview=true';
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $type = $request->type;
        $direktur = Employee::whereHas('position', function ($q) {
            $q->where('nama', 'like', '%direktur%')
                ->orWhere('nama', 'like', '%DIREKTUR%');
        })->first();

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr', 'taxes_id', 'approval', 'type', 'direktur'));
        $pdf->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

        if ($request->ajax() || $request->preview) {
            $canvas->page_text($w / 5, $h / 1.7, 'PREVIEW ONLY', null, 60, array(0, 0, 0, 0.3), 0, 0, -30);
        }

        if ($request->ajax()) {
            Storage::disk('public')->deleteDirectory('tmp_invoice_trading');
            $tmp_file_name = 'invoice_trading_' . time() . '.pdf';
            $path = 'tmp_invoice_trading/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream($fileName);
    }

    public function export_receipt($id, Request $request)
    {
        if (!$request->preview && authorizePrint('invoice_trading_receipt')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'invoice_trading_receipt',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = model::with('customer', 'bank_internal', 'approved_by_user')->findOrFail(decryptId($id));

        $fileName = 'kwitansi-' . microtime(true) . '.pdf';

        $qr_url = route('invoice-trading.export-receipt.id', ['id' => encryptId($model->id)]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $direktur = Employee::whereHas('position', function ($q) {
            $q->where('nama', 'like', '%direktur%');
        })->first();

        $pdf = PDF::loadview("admin/.$this->view_folder./receipt", compact('model', 'qr', 'direktur'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();


        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

        if ($request->ajax() || $request->preview) {
            $canvas->page_text($w / 5, $h / 1.7, 'PREVIEW ONLY', null, 60, array(0, 0, 0, 0.3), 0, 0, -30);
        }

        if ($request->ajax()) {
            Storage::disk('public')->deleteDirectory('tmp_invoice_trading_receipt');
            $tmp_file_name = 'invoice_trading_receipt_' . time() . '.pdf';
            $path = 'tmp_invoice_trading_receipt/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream($fileName);
    }

    public function export_with_delivery_orders($id, Request $request)
    {
        if (!$request->preview && authorizePrint('invoice_with_do')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'invoice_with_do',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = model::with('customer', 'bank_internal', 'approved_by_user')->findOrFail(decryptId($id));
        $taxes_id = $model->invoice_trading_taxes;
        $taxes_id = $taxes_id->map(function ($tax) {
            return $tax->only(['tax_id', 'value']);
        });

        $invoice_trading_addon_taxes = InvTradingAddOnTax::whereHas('inv_trading_add_on', function ($q) use ($model) {
            $q->whereHas('invoice_trading', function ($q) use ($model) {
                $q->where('id', $model->id);
            });
        })->get();

        $addtion_taxes_id = $invoice_trading_addon_taxes->map(function ($tax) {
            return $tax->only(['tax_id', 'value']);
        });

        $taxes_id = $taxes_id->merge($addtion_taxes_id);
        $taxes_id = $taxes_id->unique(function ($tax) {
            return $tax['tax_id'] . $tax['value'];
        });

        $taxes = Tax::whereIn(
            'id',
            $taxes_id->pluck('tax_id')->toArray()
        )->get();

        $taxes_id = $taxes_id->map(function ($tax) use ($model, $invoice_trading_addon_taxes, $taxes) {
            $tax['amount'] = $model->invoice_trading_taxes
                ->where('value', $tax['value'])
                ->where('tax_id', $tax['tax_id'])
                ->sum('amount');
            $tax['amount'] += $invoice_trading_addon_taxes
                ->where('value', $tax['value'])
                ->where('tax_id', $tax['tax_id'])
                ->sum('total');

            $tax_data  = $taxes->where('id', $tax['tax_id'])->first();
            $tax['name'] = $tax_data->is_show_percent ? $tax_data->tax_name_with_percent : $tax_data->tax_name_without_percent;

            return $tax;
        });

        $file = public_path('/pdf_reports/Report-Invoice-Trading-' . microtime(true) . '.pdf');
        $fileName = 'Report-Invoice-Trading-' . microtime(true) . '.pdf';

        $qr_url = route('invoice-trading.export.id.with-delivery-order', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();


        $direktur = Employee::whereHas('position', function ($q) {
            $q->where('nama', 'like', '%direktur%');
        })->first();

        $pdf = PDF::loadview("admin/.$this->view_folder./export-with-delivery-order", compact('model', 'qr', 'taxes_id', 'approval', 'direktur'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

        return $pdf->stream($fileName);
    }

    public function delivery_orders($id)
    {
        $model = model::findOrFail($id);
    }

    /**
     * datatable delivery order for a sale order
     *
     * @param integer $id
     * @return DataTables
     */
    public function list_delivery_order(Request $request, $id)
    {
        $model = model::findOrFail($id);
        $so_trading = SoTrading::findOrFail($model->so_trading_id);
        validate_branch($model->branch_id);
        $data = DeliveryOrder::whereIn('id', $model->invoice_trading_details->pluck('delivery_order_id'));

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('code', function ($row) use ($model, $so_trading) {
                $code = '<a href="' . route("admin.delivery-order.list-delivery-order.show", ['sale_order_id' => $so_trading->id, 'delivery_order_id' => $row->id]) . '" target="_blank" class="text-primary text-decoration-underline hover_text-dark">' . $row->code . '</a>';
                return $code;
            })
            ->editColumn('target_delivery', fn($row) => $row->target_delivery ? localDate($row->target_delivery) : "-")
            ->editColumn('load_date', fn($row) => $row->load_date ? localDate($row->load_date) : "-")
            ->editColumn('unload_date', fn($row) => $row->unload_date ? localDate($row->unload_date) : "-")
            ->editColumn('load_quantity', function ($row) use ($so_trading) {
                $unit = $so_trading->so_trading_detail->item->unit->name ?? '';
                return formatNumber($row->load_quantity) . " " . $unit;
            })
            ->editColumn('load_quantity_realization', function ($row) use ($so_trading) {
                $unit = $so_trading->so_trading_detail->item->unit->name ?? '';
                return formatNumber($row->load_quantity_realization) . " " . $unit;
            })
            ->editColumn('unload_quantity', function ($row) use ($so_trading) {
                $unit = $so_trading->so_trading_detail->item->unit->name ?? '';
                return formatNumber($row->unload_quantity) . " " . $unit;
            })
            ->editColumn('unload_quantity_realization', function ($row) use ($so_trading) {
                $unit = $so_trading->so_trading_detail->item->unit->name ?? '';
                return formatNumber($row->unload_quantity_realization) . " " . $unit;
            })
            ->editColumn('status', fn($row) => view('admin.delivery-order.status', compact('row')))
            ->editColumn('export', function ($row) {
                $link = route("delivery-order.export.id", ['id' => encryptId($row->id)]);
                $export = '<a href="' . $link . '" class="btn btn-sm btn-flat btn-info" onclick="show_print_out_modal(event)">Export</a>';

                return $export;
            })
            ->addColumn('kapasitas_do', fn($row) => $row->purchase_transport_detail ? formatNumber($row->purchase_transport_detail->jumlah) : formatNumber($row->kuantitas_kirim))
            ->addColumn('moda_transport', fn($row) => $row->purchase_transport_id ? 'Transportir' : 'Own Use')
            ->rawColumns(['code', 'status'])
            ->make(true);
    }

    public function history($id, Request $request)
    {
        try {
            $invoice_tradings = DB::table('invoice_tradings')
                ->join('invoice_parents', function ($query) {
                    $query->on('invoice_parents.reference_id', '=', 'invoice_tradings.id')
                        ->where('invoice_parents.model_reference', '=', 'App\Models\InvoiceTrading');
                })
                ->where('invoice_tradings.id', $id)
                ->select(
                    'invoice_tradings.id',
                    'invoice_tradings.kode as code',
                    'invoice_tradings.date',
                    'invoice_tradings.status',
                    'invoice_parents.id as invoice_parent_id',
                    'invoice_tradings.so_trading_id'
                )->get();

            $sale_orders = DB::table('sale_orders')
                ->whereNull('deleted_at')
                ->whereIn('id', $invoice_tradings->pluck('so_trading_id')->toArray())
                ->select(
                    'id',
                    'sale_orders.nomor_so as code',
                    'sale_orders.tanggal as date',
                    'status',
                )->get();

            $delivery_orders = DB::table('delivery_orders')
                ->where('so_trading_id', $sale_orders->pluck('id')->toArray())
                ->whereNotIn('status', ['rejected', 'void'])
                ->whereNull('deleted_at')
                ->select(
                    'delivery_orders.id',
                    'delivery_orders.so_trading_id',
                    'delivery_orders.code',
                    'delivery_orders.target_delivery as date',
                    'delivery_orders.status',
                )->get();

            $receivables_payments = DB::table('receivables_payment_details')
                ->where('invoice_parent_id', $invoice_tradings->pluck('invoice_parent_id')->toArray())
                ->join('receivables_payments', 'receivables_payments.id', '=', 'receivables_payment_details.receivables_payment_id')
                ->leftJoin('bank_code_mutations', function ($query) {
                    $query->on('bank_code_mutations.ref_id', '=', 'receivables_payments.id')
                        ->where('bank_code_mutations.ref_model', '=', 'App\Models\ReceivablesPayment');
                })
                ->whereNull('receivables_payments.deleted_at')
                ->whereNotIn('receivables_payments.status', ['rejected', 'void'])
                ->select(
                    'receivables_payments.id',
                    'receivables_payments.code',
                    'bank_code_mutations.code as bank_code_mutation_code',
                    'receivables_payments.date',
                    'receivables_payments.status',
                )->get()
                ->map(function ($item) {
                    $item->code = $item->bank_code_mutation_code ?? $item->code;
                    return $item;
                });

            $sale_orders = $sale_orders->map(function ($item) {
                // $item->date = localDate($item->date);
                $item->link = route('admin.sales-order.show', $item->id);
                $item->menu = 'sales order trading';
                return $item;
            });

            try {
                $delivery_orders = $delivery_orders->map(function ($item) {
                    // $item->date = localDate($item->date);
                    $item->link = route('admin.delivery-order.list-delivery-order.show', [
                        'sale_order_id' => $item->so_trading_id,
                        'delivery_order_id' => $item->id,
                    ]);

                    $item->menu = 'delivery order trading';
                    return $item;
                });
            } catch (\Exception $e) {
                Log::error($e->getMessage(), ['file' => __FILE__, 'line' => __LINE__]);
            }

            $invoice_tradings = $invoice_tradings->map(function ($item) {
                // $item->date = localDate($item->date);
                $item->link = route('admin.invoice-trading.show', $item->id);
                $item->menu = 'invoice trading';
                return $item;
            });

            $receivables_payments = $receivables_payments->map(function ($item) {
                // $item->date = localDate($item->date);
                $item->link = route('admin.receivables-payment.show', $item->id);
                $item->menu = 'receivables payment';
                return $item;
            });

            $histories = $sale_orders->unique('id')
                ->merge($delivery_orders->unique('id'))
                ->merge($invoice_tradings->unique('id'))
                ->merge($receivables_payments->unique('id'))
                ->sortBy('date')
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'data' => $histories
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function exportTax(Request $request, $id)
    {
        $model = model::query()
            ->with('inv_trading_add_on.item')
            ->findOrFail(decryptId($id));

        if ($model->is_separate_invoice) {
            if ($request->type == 'transport') {
                if (!$request->preview && authorizePrint('invoice_trading_tax_transport')) {
                    $document_print = new PrintHelper();
                    $result = $document_print->check_available_for_print(
                        model::class,
                        decryptId($id),
                        'invoice_trading_tax_transport',
                    );

                    if (!$result) {
                        return abort(403);
                    }
                }

                $model->kode .= '-TP';
            } else {
                if (!$request->preview && authorizePrint('invoice_trading_tax')) {
                    $document_print = new PrintHelper();
                    $result = $document_print->check_available_for_print(
                        model::class,
                        decryptId($id),
                        'invoice_trading_tax',
                    );

                    if (!$result) {
                        return abort(403);
                    }
                }

                $model->kode .= '-BBM';
            }
        } else {
            if (!$request->preview && authorizePrint('invoice_trading_tax')) {
                $document_print = new PrintHelper();
                $result = $document_print->check_available_for_print(
                    model::class,
                    decryptId($id),
                    'invoice_trading_tax',
                );

                if (!$result) {
                    return abort(403);
                }
            }
        }

        $taxes_id = $model->invoice_trading_taxes;
        $taxes_id = $taxes_id->map(function ($tax) {
            return $tax->only(['tax_id', 'value']);
        });

        $invoice_trading_addon_taxes = InvTradingAddOnTax::whereHas('inv_trading_add_on', function ($q) use ($model) {
            $q->whereHas('invoice_trading', function ($q) use ($model) {
                $q->where('id', $model->id);
            });
        })->get();

        $addtion_taxes_id = $invoice_trading_addon_taxes->map(function ($tax) {
            return $tax->only(['tax_id', 'value']);
        });

        if ($model->is_separate_invoice) {
            if ($request->type == 'transport') {
                $taxes_id = $addtion_taxes_id;
            } else {
                $taxes_id = $taxes_id;
            }
        } else {
            $taxes_id = $taxes_id->merge($addtion_taxes_id);
        }

        $taxes = Tax::whereIn(
            'id',
            $taxes_id->pluck('tax_id')->toArray()
        )
            ->where('type', 'ppn')
            ->get();

        $taxes_id = $taxes_id->filter(function ($tax) use ($taxes) {
            return in_array($tax['tax_id'], $taxes->pluck('id')->toArray());
        });

        $taxes_id = $taxes_id->map(function ($tax) use ($model, $invoice_trading_addon_taxes, $taxes, $request) {
            if ($model->is_separate_invoice) {
                if ($request->type == 'transport') {
                    $tax['amount'] = $invoice_trading_addon_taxes
                        ->where('value', $tax['value'])
                        ->where('tax_id', $tax['tax_id'])
                        ->sum('total');
                } else {
                    $tax['amount'] = $model->invoice_trading_taxes
                        ->where('value', $tax['value'])
                        ->where('tax_id', $tax['tax_id'])
                        ->sum('amount');
                }
            } else {
                $tax['amount'] = $model->invoice_trading_taxes
                    ->where('value', $tax['value'])
                    ->where('tax_id', $tax['tax_id'])
                    ->sum('amount');
                $tax['amount'] += $invoice_trading_addon_taxes
                    ->where('value', $tax['value'])
                    ->where('tax_id', $tax['tax_id'])
                    ->sum('total');
            }

            $tax['name'] = $taxes->where('id', $tax['tax_id'])->first()->name ?? '';

            return $tax;
        });


        $url = route('invoice-trading.export-tax.id', ['id' => encryptId($model->id), 'type' => $request->type]);
        $qr = base64_encode(QrCode::size(250)->generate($url));

        $type = $request->type;
        $pdf = Pdf::loadView("admin.{$this->view_folder}.export-tax", compact('model', 'qr', 'type', 'taxes_id'));
        $pdf->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();

        if ($request->ajax() || $request->preview) {
            $canvas->page_text($w / 5, $h / 1.7, 'PREVIEW ONLY', null, 60, array(0, 0, 0, 0.3), 0, 0, -30);
        }

        if ($request->ajax()) {
            Storage::disk('public')->deleteDirectory('tmp_invoice_trading');
            $tmp_file_name = 'invoice_trading_' . time() . '.pdf';
            $path = 'tmp_invoice_trading/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream("Report-Invoice-Tax-{$model->kode}.pdf");
    }

    public function update_reference(Request $request, $id)
    {
        $model = model::findOrFail($id);
        DB::beginTransaction();
        try {
            if (!checkAvailableDate($model->date)) {
                return response()->json([
                    'success' => false,
                    'message' => 'periode sudah close',
                ], 500);
            }

            $reference = $request->reference;
            if ($request->reference_second) {
                $reference .= ' / ' . $request->reference_second;
            }
            DB::table('invoice_tradings')
                ->where('id', $id)
                ->update([
                    'reference' => $reference,
                ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'berhasil memperbarui faktur pajak',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function lock($id)
    {
        $model = model::findOrFail($id);
        $invoice_parent = InvoiceParent::where('model_reference', model::class)->where('reference_id', $model->id)->first();
        DB::beginTransaction();
        try {
            $invoice_parent->lock_status = $invoice_parent->lock_status == 1 ? 0 : 1;
            $invoice_parent->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' =>  $invoice_parent->lock_status == 1 ? 'berhasil mengunci invoice' : 'berhasil membuka kunci invoice',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $invoice_parent->lock_status == 1 ? 'gagal membuka kunci invoice' : 'berhasil mengunci invoice',
            ], 500);
        }
    }

    public function regenerate()
    {
        $journals = Journal::where('journals.reference_model', InvoiceTrading::class)
            ->leftJoin('journal_details', 'journal_details.journal_id', 'journals.id')
            ->selectRaw('journals.*, COALESCE(SUM(journal_details.debit_exchanged), 0) as total_debit, COALESCE(SUM(journal_details.credit_exchanged), 0) as total_credit')
            ->groupBy('journals.id')
            ->havingRaw('total_debit != total_credit')
            ->get();

        foreach ($journals as $key => $journal) {
            Journal::where('id', $journal->id)
                ->delete();

            $new_invoice_journal = new JournalHelpers('invoice-trading', $journal->reference_id);
            $new_invoice_journal->generate();
        }
    }
}
