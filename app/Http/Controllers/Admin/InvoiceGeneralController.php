<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\PrintHelper;
use App\Models\DeliveryOrderGeneral;
use App\Models\DownPaymentInvoice;
use App\Models\Employee;
use App\Models\InvoiceGeneral as model;
use App\Models\InvoiceGeneral;
use App\Models\InvoiceGeneralAdditional;
use App\Models\InvoiceGeneralAdditionalTax;
use App\Models\InvoiceGeneralDetail;
use App\Models\InvoiceGeneralDetailTax;
use App\Models\InvoiceParent;
use App\Models\InvoiceTaxSummary;
use App\Models\Item;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\SaleOrderGeneral;
use App\Models\SaleOrderGeneralDetail;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceGeneralController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'invoice-general';

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
            $query = Model::query()
                ->with(['customer', 'sale_order_general', 'invoice_general_details.sale_order_general'])
                ->when(!get_current_branch()->is_primary, function ($q) {
                    $q->where('branch_id', get_current_branch_id());
                })
                ->when($request->from_date, function ($q) use ($request) {
                    $q->whereDate('date', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($q) use ($request) {
                    $q->whereDate('date', '<=', Carbon::parse($request->to_date));
                })
                ->when($request->status, function ($q) use ($request) {
                    $q->where('status', $request->status);
                })
                ->when($request->customer_id, function ($q) use ($request) {
                    $q->where('customer_id', $request->customer_id);
                })
                ->when($request->branch_id, function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });

            return datatables()->of($query)
                ->filterColumn('no_po_external', function ($query, $keyword) {
                    $query->whereHas('invoice_general_details.sale_order_general', function ($q) use ($keyword) {
                        $q->where('no_po_external', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('sale_order_general', function ($query, $keyword) {
                    $query->whereHas('invoice_general_details.sale_order_general', function ($q) use ($keyword) {
                        $q->where('kode', 'like', "%{$keyword}%");
                    });
                })
                ->addIndexColumn()
                ->editColumn('date', function ($value) {
                    return localDate($value->date);
                })
                ->editColumn('code', function ($value) {
                    return '<a href="' . route("admin.invoice-general.index") . '/' . $value->id . '" class="text-primary">' . $value->code . '</a>';
                })
                ->editColumn('reference', function ($value) {
                    return $value->reference;
                })
                ->addColumn('sale_order_general', function ($value) {
                    return [
                        'kode' => $value->sale_order_general?->kode ? "<a href='" . route('admin.sales-order-general.show', ['sales_order_general' => $value->sale_order_general->id]) . "' target='_blank'>" . $value->sale_order_general->kode . "</a>" : $value->invoice_general_details->map(function ($data) {
                            return "<a href='" . route('admin.sales-order-general.show', ['sales_order_general' => $data->sale_order_general->id]) . "' target='_blank'>" . $data->sale_order_general->kode . "</a> <br>";
                        })->unique()->implode(','),
                    ];
                })
                ->addColumn('no_po_external', function ($value) {
                    return $value->sale_order_general?->no_po_external ?? $value->invoice_general_details->map(function ($data) {
                        return $data?->sale_order_general?->no_po_external;
                    })->unique()->implode(',');
                })
                ->addColumn('customer', function ($value) {
                    return ['nama' => $value->customer?->nama];
                })
                ->addColumn('status', function ($value) {
                    $badge = '<div class="badge badge-lg badge-' . get_invoice_status()[$value->status]['color'] . '">' . get_invoice_status()[$value->status]['label'] . ' - ' . get_invoice_status()[$value->status]['text'] . '</div>';
                    return $badge;
                })
                ->editColumn('created_at', function ($value) {
                    return toDayDateTimeString($value->created_at);
                })
                ->addColumn('export', function ($value) {
                    $checkAuthorizePrint = authorizePrint('invoice_general');
                    $checkAuthorizePrintReceipt = authorizePrint('invoice_general_receipt');
                    return $this->generateExportLinks($value, $checkAuthorizePrint, $checkAuthorizePrintReceipt);
                })
                ->addColumn('export_with_delivery_order', function ($value) {
                    $checkAuthorizePrint = authorizePrint('invoice_general');
                    return $this->generateExportWithDeliveryOrderLink($value, $checkAuthorizePrint);
                })
                ->addColumn('payment_status', function ($value) {
                    $payment_badge = '<div class="badge badge-lg badge-' . payment_status()[$value->payment_status]['color'] . '">' . payment_status()[$value->payment_status]['label'] . '</div>';
                    return $payment_badge;
                })
                ->addColumn('print_status', function ($value) {
                    return $this->generatePrintStatusHtml($value);
                })
                ->escapeColumns([])
                ->make(true);
        }
    }

    protected function generateExportLinks($value, $checkAuthorizePrint, $checkAuthorizePrintReceipt)
    {
        $model = Model::class;
        $link = route('admin.invoice-general.show', ['invoice_general' => $value->id]);
        $link_export = route("invoice-general.export.id", ['id' => encryptId($value->id)]);
        $link_export_receipt = route("invoice-general.export-receipt.id", ['id' => encryptId($value->id)]);

        return "<a href='$link_export' class='btn btn-sm btn-flat btn-info' target='_blank' onclick='show_print_out_modal(event)' " . ($checkAuthorizePrint ? "data-model='$model' data-id='$value->id' data-print-type='invoice_general' data-link='$link' data-code='$value->code'" : "") . ">Export</a>&nbsp;" .
            "<a href='$link_export_receipt' class='btn btn-sm btn-flat btn-info' target='_blank' onclick='show_print_out_modal(event)' " . ($checkAuthorizePrintReceipt ? "data-model='$model' data-id='$value->id' data-print-type='invoice_general_receipt' data-link='$link' data-code='$value->receipt_number'" : "") . ">Kwitansi</a>";
    }

    protected function generateExportWithDeliveryOrderLink($value, $checkAuthorizePrint)
    {
        $model = Model::class;
        $link = route('admin.invoice-general.show', ['invoice_general' => $value->id]);
        $link_export_with_do = route("invoice-general.export.id.with-delivery-order", ['id' => encryptId($value->id)]);

        return '<a href="' . $link_export_with_do . '" class="btn btn-sm btn-flat btn-info" target="_blank" onclick="show_print_out_modal(event)" ' . ($checkAuthorizePrint ? 'data-model="' . $model . '" data-id="' . $value->id . '" data-print-type="invoice_general_with_do" data-link="' . $link . '" data-code="' . $value->code . '"' : '') . '>Export With Delivery Order</a>';
    }

    protected function generatePrintStatusHtml($value)
    {
        $text_status = [
            'without_do' => $value->printedData?->where('type', 'invoice_general')->last()?->status === 'printed' ? 'Sudah dicetak' : ($value->printedData?->where('type', 'invoice_general')->last()?->status === 'pending' ? 'Sedang diajukan' : 'Belum Dicetak'),
            'with_do' => $value->printedData?->where('type', 'invoice_general_with_do')->last()?->status === 'printed' ? 'Sudah dicetak (With DO)' : ($value->printedData?->where('type', 'invoice_general_with_do')->last()?->status === 'pending' ? 'Sedang diajukan (With DO)' : 'Belum Dicetak (With DO)')
        ];

        $color_status = [
            'without_do' => $value->printedData?->where('type', 'invoice_general')->last()?->status === 'printed' ? 'success' : ($value->printedData?->where('type', 'invoice_general')->last()?->status === 'pending' ? 'warning' : 'danger'),
            'with_do' => $value->printedData?->where('type', 'invoice_general_with_do')->last()?->status === 'printed' ? 'success' : ($value->printedData?->where('type', 'invoice_general_with_do')->last()?->status === 'pending' ? 'warning' : 'danger')
        ];

        $html = '';
        foreach ($text_status as $key => $value) {
            $html .= '<div class="badge badge-lg badge-' . $color_status[$key] . ' me-1">' . $value . '</div>';
        }

        return $html;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view("admin.$this->view_folder.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'date_invoice' => 'required|date',
            'due_date' => 'required|date',
            'bank_internal_ids' => 'required|array|min:1',
            'bank_internal_ids.*' => 'required|exists:bank_internals,id',
        ]);

        // * initialize variables
        $main_sub_total = 0;
        $main_total_tax = 0;
        $main_total = 0;

        $additional_sub_total = 0;
        $additional_total_tax = 0;
        $additional_total = 0;

        // * find data

        DB::beginTransaction();

        $customer = \App\Models\Customer::findOrFail($request->customer_id);

        // * top
        $top = $request->term_of_payments ?? $customer?->term_of_payment ?? 0;

        if ($top == 'cash') {
            $top_due = 0;
        } else {
            $top_due = $request->top_days ?? $customer?->top_days ?? 0;
        }

        // * create invoice
        $model = new model();
        $last_code = model::orderByDesc('id')
            ->withTrashed()
            ->whereMonth('date', Carbon::parse($request->date_invoice))
            ->whereYear('date', Carbon::parse($request->date_invoice))
            ->first();

        $model->loadModel([
            'code' => generate_code_transaction("INVG", $last_code->code ?? null, date: $request->date_invoice),
            'receipt_number' => generate_receipt_code($last_code->receipt_number ?? null, $request->date_invoice, 'KWG'),
            'branch_id' => $request->branch_id,
            'reference' => $request->reference,
            'customer_id' => $request->customer_id,
            'currency_id' => $request->currency_id,
            'bank_internal_id' => $request->bank_internal_ids[0],
            'bank_internal_ids' => $request->bank_internal_ids,
            'exchange_rate' => $request->exchange_rate,
            'date' => Carbon::parse($request->date_invoice),
            'due_date' => Carbon::parse($request->due_date),
            'term_of_payments' => $top,
            'due' => $top_due,
            'sub_total_main' => 0,
            'total_tax_main' => 0,
            'total_main' => 0,
            'sub_total_additional' => 0,
            'total_tax_additional' => 0,
            'total_additional' => 0,
            'total' => 0,
            'created_by' => Auth::user()->id,
            'is_old' => false,
            'so_references' => $request->so_references ?? []
        ]);

        try {
            if ($request->hasFile('attachment')) {
                $model->attachment = $this->upload_file($request->file('attachment'), 'invoice_general');
            }
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice", $th->getMessage()));
        }

        $data_details = [];
        // * create invoice detail
        foreach ($request->delivery_order_id ?? [] as $key => $do) {
            $delivery_order = DeliveryOrderGeneral::findOrFail($do);

            foreach ($delivery_order->delivery_order_general_details as $key => $delivery_order_detail) {
                $qty_for_invoice = isset($request->{'invoice_quantity_' . $delivery_order_detail->id}) ?  thousand_to_float($request->{'invoice_quantity_' . $delivery_order_detail->id}) : $delivery_order_detail->quantity_received;
                $sale_order_general_detail = $delivery_order_detail->sale_order_general_detail;

                $single_total_tax = 0;
                $single_sub_total = $qty_for_invoice * $sale_order_general_detail->price;

                // ? create invoice detail
                $model_detail = new InvoiceGeneralDetail();
                $model_detail->loadModel([
                    'invoice_general_id' => $model->id,
                    'sale_order_general_id' => $delivery_order->sale_order_general_id,
                    'sale_order_general_detail_id' => $delivery_order_detail->sale_order_general_detail_id,
                    'delivery_order_general_detail_id' => $delivery_order_detail->id,
                    'delivery_order_general_id' => $delivery_order->id,
                    'item_id' => $delivery_order_detail->item_id,
                    'unit_id' => $delivery_order_detail->unit_id,
                    'quantity' => $delivery_order_detail->quantity_received,
                    'invoice_quantity' => $qty_for_invoice,
                    'price' => $sale_order_general_detail->price,
                    'sub_total' => $single_sub_total,
                    'total_tax' => 0,
                    'total' => $single_sub_total,
                ]);

                try {
                    $model_detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice detail", $th->getMessage()));
                }

                $data_details[] = $model_detail;

                // ? create invoice detail tax
                foreach ($sale_order_general_detail->sale_order_general_detail_taxes as $key => $value) {
                    $single_total_tax_value = $single_sub_total * $value->value;

                    $model_detail_tax = new InvoiceGeneralDetailTax();
                    $model_detail_tax->loadModel([
                        'invoice_general_detail_id' => $model_detail->id,
                        'tax_id' => $value->tax_id,
                        'value' => $value->value,
                        'total' => $single_total_tax_value,
                    ]);

                    try {
                        $model_detail_tax->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice detail tax", $th->getMessage()));
                    }

                    $single_total_tax += $single_total_tax_value;
                }

                // ? update invoice detail total
                $model_detail->loadModel([
                    'total_tax' => $single_total_tax,
                    'total' => $single_sub_total + $single_total_tax,
                ]);

                try {
                    $model_detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating invoice detail", $th->getMessage()));
                }

                // update invoice total
                $main_sub_total += $single_sub_total;
                $main_total_tax += $single_total_tax;
                $main_total += $single_sub_total + $single_total_tax;
            }
        }

        // * update invoice total
        $model->loadModel([
            'sub_total_main' => $main_sub_total,
            'total_tax_main' => $main_total_tax,
            'total_main' => $main_total,
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating invoice", $th->getMessage()));
        }

        // * update delivery order general
        try {
            DeliveryOrderGeneral::whereHas('delivery_order_general_details', function ($d) use ($model) {
                $d->whereIn('id', $model->invoice_general_details->pluck('delivery_order_general_detail_id')->toArray());
            })
                ->update(
                    [
                        'is_invoice_created' => true,
                    ]
                );
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating delivery order general", $th->getMessage()));
        }

        // * invoice additional
        if (is_array($request->additional_item_type)) {
            foreach ($request->additional_item_type ?? [] as $key => $additional) {
                if (isset(
                    $request->additional_item_id[$key],
                    $request->additional_price[$key],
                    $request->additional_quantity[$key]
                )) {
                    // check if contains all data currently
                    if (!is_null($request->additional_item_id[$key]) && !is_null($request->additional_price[$key]) && !is_null($request->additional_quantity[$key])) {
                        if ($request->additional_quantity[$key] > 0) {
                            $single_sub_total = thousand_to_float($request->additional_price[$key]) * thousand_to_float($request->additional_quantity[$key]);
                            $single_tax_total = 0;
                            $single_total = $single_sub_total + $single_tax_total;

                            // ? get item
                            $item = Item::find($request->additional_item_id[$key]);

                            // ? create invoice additional
                            $model_additional = new InvoiceGeneralAdditional();
                            $model_additional->loadModel([
                                'invoice_general_id' => $model->id,
                                'item_id' => $request->additional_item_id[$key],
                                'unit_id' => $item->unit_id,
                                'quantity' => thousand_to_float($request->additional_quantity[$key]),
                                'price' => thousand_to_float($request->additional_price[$key]),
                                'sub_total' => $single_sub_total,
                                'total_tax' => $single_tax_total,
                                'total' => $single_total,
                            ]);

                            try {
                                $model_additional->save();
                            } catch (\Throwable $th) {
                                DB::rollBack();
                                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice additional", $th->getMessage()));
                            }

                            // * create invoice additional tax
                            // $tax_key = $request->additional_item_row_index[$key];
                            // if (isset($_REQUEST['additional_tax_id_' . $tax_key])) {
                            //     foreach ($_REQUEST['additional_tax_id_' . $tax_key] as $key2 => $value2) {
                            //         $tax = Tax::find($value2);
                            //         if ($tax) {
                            //             $single_total_tax_value = $single_sub_total * $tax->value;

                            //             $model_additional_tax = new InvoiceGeneralAdditionalTax();
                            //             $model_additional_tax->loadModel([
                            //                 'invoice_general_additional_id' => $model_additional->id,
                            //                 'tax_id' => $tax->id,
                            //                 'value' => $tax->value,
                            //                 'total' => $single_total_tax_value,
                            //             ]);

                            //             try {
                            //                 $model_additional_tax->save();
                            //             } catch (\Throwable $th) {
                            //                 DB::rollBack();

                            //                 return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice additional tax", $th->getMessage()));
                            //             }

                            //             $single_tax_total += $single_total_tax_value;
                            //         }
                            //     }
                            // }

                            $total_item_tax = 0;
                            if ($request->tax_data) {
                                $tax_list = $request->tax_data;
                                foreach ($tax_list as $tax_value) {
                                    $data_tax = Tax::find($tax_value);
                                    if ($data_tax) {
                                        $total_item_tax += $single_sub_total * $data_tax->value;
                                        $tax = new InvoiceGeneralAdditionalTax();
                                        $tax->loadModel([
                                            'invoice_general_additional_id' => $model_additional->id,
                                            'tax_id' => $data_tax->id,
                                            'value' => $data_tax->value,
                                            'total' => $single_sub_total * $data_tax->value,
                                        ]);

                                        try {
                                            $tax->save();
                                        } catch (\Throwable $th) {
                                            DB::rollBack();
                                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                                        }
                                    }
                                }
                            }

                            // ? update invoice additional total
                            $model_additional->loadModel([
                                'total_tax' => $total_item_tax,
                                'total' => $single_sub_total + $total_item_tax,
                            ]);

                            try {
                                $model_additional->save();
                            } catch (\Throwable $th) {
                                DB::rollBack();
                                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating invoice additional", $th->getMessage()));
                            }

                            // ? update invoice total
                            $additional_sub_total += $single_sub_total;
                            $additional_total_tax += $total_item_tax;
                            $additional_total += $single_sub_total + $total_item_tax;
                        }
                    }
                }
            }
        }

        // * update invoice total
        $model->loadModel([
            'sub_total_additional' => $additional_sub_total,
            'total_tax_additional' => $additional_total_tax,
            'total_additional' => $additional_total,
            'total' => $main_total + $additional_total,
        ]);

        $model->save();

        // !! create invoice tax summary
        $invoice_general_detail_taxes = InvoiceGeneralDetailTax::whereHas('invoice_general_detail', function ($q) use ($model) {
            $q->where('invoice_general_id', $model->id);
        })
            ->get();

        $all_invoice_taxes = new Collection();
        $invoice_general_detail_taxes->map(function ($item) use (&$all_invoice_taxes) {
            $all_invoice_taxes->push($item);
        });

        $invoice_general_additional_taxes = InvoiceGeneralAdditionalTax::whereHas('invoice_general_additional', function ($q) use ($model) {
            $q->where('invoice_general_id', $model->id);
        })
            ->get();

        $invoice_general_additional_taxes->map(function ($item) use (&$all_invoice_taxes) {
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

        $invoice_parent = InvoiceParent::where('model_reference', model::class)->where('reference_id', $model->id)->first();
        if ($request->invoice_down_payment_id) {
            DownPaymentInvoice::whereNotIn('invoice_down_payment_id', $request->invoice_down_payment_id)->where('invoice_parent_id', $invoice_parent->id)->delete();
            foreach ($request->invoice_down_payment_id ?? [] as $key => $invoice_down_payment_id) {
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

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Invoice General",
                subtitle: Auth::user()->name . " mengajukan Invoice General " . $model->code,
                link: route('admin.invoice-general.show', $model),
                update_status_link: route('admin.invoice-general.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating invoice", $th->getMessage()));
        }

        // * invoice general coa
        $invoiceCoa = new \App\Http\Helpers\InvoiceCoaHelpers($model->id, 'invoice-general');
        try {
            $invoiceCoa->generateCoaDataInvoiceGeneral();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice coa", $th->getMessage()));
        }

        DB::commit();
        return redirect()->route("admin.invoice.index")->with($this->ResponseMessageCRUD(true, 'create', "creating invoice"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = model::findOrFail($id);
        $down_payments = DownPaymentInvoice::where('invoice_parent_id', $model->invoice_parent()->id)->get();
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

        $authorization_logs['can_revert_request'] =  $model->check_available_date && $model->status == 'approve' && $model->payment_status == 'unpaid';
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve' && $model->payment_status == 'unpaid';
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button', 'down_payments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $model = model::findOrFail($id);

        $tax_data = InvoiceGeneralAdditionalTax::with(['tax'])->whereHas('invoice_general_additional', function ($query) use ($model) {
            $query->where('invoice_general_id', $model->id);
        })
            ->groupBy('tax_id')
            ->get();

        $down_payments = DownPaymentInvoice::where('invoice_parent_id', $model->invoice_parent()->id)->get();
        $so_references = SaleOrderGeneral::whereIn('id', $model->so_references ?? [])->get()
            ->pluck('id')
            ->toArray();

        return view("admin.$this->view_folder.edit", compact('model', 'tax_data', 'so_references', 'down_payments'));
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
        $this->validate($request, [
            'date_invoice' => 'required|date',
            'due_date' => 'required|date',
            'bank_internal_ids' => 'required|array|min:1',
            'bank_internal_ids.*' => 'required|exists:bank_internals,id',
        ]);

        // * initialize variables
        $main_sub_total = 0;
        $main_total_tax = 0;
        $main_total = 0;

        $additional_sub_total = 0;
        $additional_total_tax = 0;
        $additional_total = 0;

        $model = model::findOrFail($id);

        // // * find data
        // $sale_order_general = SaleOrderGeneral::findOrFail($request->sales_order_id ?? $model->sale_order_general_id);
        $customer = \App\Models\Customer::findOrFail($request->customer_id ?? $model->customer_id);

        DB::beginTransaction();

        $model->reference = $request->reference;
        // $model->currency_id = $sale_order_general->currency_id;
        $model->bank_internal_id = $request->bank_internal_ids[0];
        $model->bank_internal_ids = $request->bank_internal_ids;
        // $model->exchange_rate = $sale_order_general->exchange_rate;
        $model->date = Carbon::parse($request->date_invoice);
        $model->due_date = Carbon::parse($request->due_date);
        $model->due = $request->top_days ?? $customer?->top_days ?? 0;
        $model->so_references = $request->so_references;

        try {
            if ($request->hasFile('attachment')) {
                Storage::delete($model->attachment);
                $model->attachment = $this->upload_file($request->file('attachment'), 'invoice_general');
            }

            // * update invoice
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', "updating invoice", $th->getMessage()));
        }

        $data_details = [];
        // * create invoice detail
        $dataIdDOG = [];
        foreach ($request->delivery_order_id ?? [] as $key => $do) {
            $delivery_order = DeliveryOrderGeneral::findOrFail($do);

            foreach ($delivery_order->delivery_order_general_details as $key => $delivery_order_detail) {
                $invoice_quantity = isset($request->{"invoice_quantity_$delivery_order_detail->id"}) ? thousand_to_float($request->{"invoice_quantity_$delivery_order_detail->id"}) : $delivery_order_detail->quantity_received;
                $sale_order_general_detail = $delivery_order_detail->sale_order_general_detail;

                array_push($dataIdDOG, $delivery_order_detail->id);

                $single_total_tax = 0;
                $single_sub_total = $invoice_quantity * $sale_order_general_detail->price;

                // ? create invoice detail
                $model_detail = InvoiceGeneralDetail::where('invoice_general_id', $model->id)->where('delivery_order_general_detail_id', $delivery_order_detail->id)->first();
                if ($model_detail) {
                    try {
                        $model_detail->update([
                            'invoice_general_id' => $model->id,
                            'sale_order_general_detail_id' => $delivery_order_detail->sale_order_general_detail_id,
                            'delivery_order_general_detail_id' => $delivery_order_detail->id,
                            'delivery_order_general_id' => $delivery_order->id,
                            'item_id' => $delivery_order_detail->item_id,
                            'unit_id' => $delivery_order_detail->unit_id,
                            'quantity' => $delivery_order_detail->quantity_received,
                            'invoice_quantity' => $invoice_quantity,
                            'price' => $sale_order_general_detail->price,
                            'sub_total' => $single_sub_total,
                            'total_tax' => 0,
                            'total' => $single_sub_total,
                        ]);
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', "updating invoice detail", $th->getMessage()));
                    }
                } else {
                    $model_detail = new InvoiceGeneralDetail();
                    $model_detail->loadModel([
                        'invoice_general_id' => $model->id,
                        'sale_order_general_detail_id' => $delivery_order_detail->sale_order_general_detail_id,
                        'delivery_order_general_detail_id' => $delivery_order_detail->id,
                        'delivery_order_general_id' => $delivery_order->id,
                        'item_id' => $delivery_order_detail->item_id,
                        'unit_id' => $delivery_order_detail->unit_id,
                        'quantity' => $delivery_order_detail->quantity_received,
                        'invoice_quantity' => $invoice_quantity,
                        'price' => $sale_order_general_detail->price,
                        'sub_total' => $single_sub_total,
                        'total_tax' => 0,
                        'total' => $single_sub_total,
                    ]);
                    try {
                        $model_detail->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', "updating invoice detail", $th->getMessage()));
                    }
                }


                $data_details[] = $model_detail;

                // Get invoice detail tax where tax id equals tax on sale order general
                $checkInvoiceDetailTax = InvoiceGeneralDetailTax::where('invoice_general_detail_id', $model_detail->id)->whereIn('tax_id', $sale_order_general_detail->sale_order_general_detail_taxes?->pluck('tax_id')->toArray())->get();

                // ? create invoice detail tax
                if ($sale_order_general_detail->sale_order_general_detail_taxes->count() > 0) {
                    InvoiceGeneralDetailTax::where('invoice_general_detail_id', $model_detail->id)
                        ->whereNotIn('tax_id', $sale_order_general_detail->sale_order_general_detail_taxes->pluck('tax_id')->toArray())
                        ->delete();
                    foreach ($sale_order_general_detail->sale_order_general_detail_taxes as $key => $value) {
                        $single_total_tax_value = $single_sub_total * $value->value;
                        // Check invoice general tax for not duplicate value
                        $model_detail_tax = InvoiceGeneralDetailTax::where('invoice_general_detail_id', $model_detail->id)
                            ->where('tax_id', $value->tax_id)
                            ->first();

                        if (!$model_detail_tax) {
                            $model_detail_tax = new InvoiceGeneralDetailTax();
                        }

                        $model_detail_tax->loadModel([
                            'invoice_general_detail_id' => $model_detail->id,
                            'tax_id' => $value->tax_id,
                            'value' => $value->value,
                            'total' => $single_total_tax_value,
                        ]);

                        try {
                            // save model detail tax
                            $model_detail_tax->save();
                        } catch (\Throwable $th) {
                            DB::rollBack();

                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice detail tax", $th->getMessage()));
                        }
                        $single_total_tax += $single_total_tax_value;
                    }
                } else {
                    InvoiceGeneralDetailTax::where('invoice_general_detail_id', $model_detail->id)->delete();
                }

                // ? update invoice detail total
                $model_detail->total_tax = $single_total_tax;
                $model_detail->total = $single_sub_total + $single_total_tax;

                try {
                    $model_detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating invoice detail", $th->getMessage()));
                }

                // update invoice total
                $main_sub_total += $single_sub_total;
                $main_total_tax += $single_total_tax;
                $main_total += $single_sub_total + $single_total_tax;
            }
        }

        $invoice_general_detail = InvoiceGeneralDetail::where('invoice_general_id', $model->id)->whereNotIn('delivery_order_general_detail_id', $dataIdDOG);
        if ($invoice_general_detail->get()->count() > 0) {
            InvoiceGeneralDetailTax::whereIn('invoice_general_detail_id', $invoice_general_detail->pluck('id')->toArray())->delete();
        }
        $invoice_general_detail->delete();

        $invoice_parent = InvoiceParent::where('model_reference', model::class)->where('reference_id', $model->id)->first();
        if ($request->invoice_down_payment_id) {
            DownPaymentInvoice::whereNotIn('invoice_down_payment_id', $request->invoice_down_payment_id)->where('invoice_parent_id', $invoice_parent->id)->delete();
            foreach ($request->invoice_down_payment_id ?? [] as $key => $invoice_down_payment_id) {
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

        try {
            // * update invoice total
            $model->update([
                'sub_total_main' => $main_sub_total,
                'total_tax_main' => $main_total_tax,
                'total_main' => $main_total,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating invoice", $th->getMessage()));
        }

        $additional_ids = array_filter($request->additional_id ?? [], function ($value) {
            return $value !== null;
        });

        // * remove invoice additional and tax
        if (count($additional_ids) > 0) {
            InvoiceGeneralAdditionalTax::whereNotIn('invoice_general_additional_id', $additional_ids)
                ->whereHas('invoice_general_additional', function ($q) use ($model) {
                    $q->where('invoice_general_id', $model->id);
                })
                ->delete();

            InvoiceGeneralAdditional::whereNotIn('id', $additional_ids)
                ->where('invoice_general_id', $model->id)
                ->delete();
        }

        if (is_array($request->additional_item_type)) {
            foreach ($request->additional_item_type ?? [] as $key => $additional) {
                if (isset(
                    $request->additional_item_id[$key],
                    $request->additional_price[$key],
                    $request->additional_quantity[$key]
                )) {
                    // check if contains all data currently
                    if (!is_null($request->additional_item_id[$key]) && !is_null($request->additional_price[$key]) && !is_null($request->additional_quantity[$key])) {
                        if (isset($request->additional_id[$key])) {
                            if ($request->additional_quantity[$key] > 0) {
                                InvoiceGeneralAdditional::where('invoice_general_id', $model->id)->whereNotIn('id', $request->additional_id)->delete();
                                $single_sub_total = thousand_to_float($request->additional_price[$key]) * thousand_to_float($request->additional_quantity[$key]);
                                $single_tax_total = 0;
                                $single_total = $single_sub_total + $single_tax_total;

                                // ? get item
                                $item = Item::find($request->additional_item_id[$key]);

                                // ? create invoice additional
                                $model_additional = InvoiceGeneralAdditional::find($request->additional_id[$key]);
                                try {
                                    $model_additional->update([
                                        'invoice_general_id' => $model->id,
                                        'item_id' => $request->additional_item_id[$key],
                                        'unit_id' => $item->unit_id,
                                        'quantity' => thousand_to_float($request->additional_quantity[$key]),
                                        'price' => thousand_to_float($request->additional_price[$key]),
                                        'sub_total' => $single_sub_total,
                                        'total_tax' => $single_tax_total,
                                        'total' => $single_total,
                                    ]);
                                } catch (\Throwable $th) {
                                    DB::rollBack();
                                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice additional", $th->getMessage()));
                                }

                                // recreate invoice additional tax
                                InvoiceGeneralAdditionalTax::where('invoice_general_additional_id', $model_additional->id)->delete();

                                $total_item_tax = 0;
                                if ($request->tax_data) {
                                    $tax_list = $request->tax_data;
                                    foreach ($tax_list as $tax_value) {
                                        $data_tax = Tax::find($tax_value);
                                        if ($data_tax) {
                                            $total_item_tax += $single_sub_total * $data_tax->value;
                                            $tax = new InvoiceGeneralAdditionalTax();
                                            $tax->loadModel([
                                                'invoice_general_additional_id' => $model_additional->id,
                                                'tax_id' => $data_tax->id,
                                                'value' => $data_tax->value,
                                                'total' => $single_sub_total * $data_tax->value,
                                            ]);

                                            try {
                                                $tax->save();
                                            } catch (\Throwable $th) {
                                                DB::rollBack();
                                                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                                            }
                                        }
                                    }
                                }

                                // ? update invoice additional total
                                $model_additional->total_tax = $total_item_tax;
                                $model_additional->total = $single_sub_total + $total_item_tax;

                                try {
                                    $model_additional->save();
                                } catch (\Throwable $th) {
                                    DB::rollBack();
                                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating invoice additional", $th->getMessage()));
                                }

                                // ? update invoice total
                                $additional_sub_total += $single_sub_total;
                                $additional_total_tax += $total_item_tax;
                                $additional_total += $single_sub_total + $total_item_tax;
                            }
                        } else {
                            if ($request->additional_quantity[$key] > 0) {
                                $single_sub_total = thousand_to_float($request->additional_price[$key]) * thousand_to_float($request->additional_quantity[$key]);
                                $single_tax_total = 0;
                                $single_total = $single_sub_total + $single_tax_total;

                                // ? get item
                                $item = Item::find($request->additional_item_id[$key]);

                                // ? create invoice additional
                                $model_additional = new InvoiceGeneralAdditional();
                                $model_additional->loadModel([
                                    'invoice_general_id' => $model->id,
                                    'item_id' => $request->additional_item_id[$key],
                                    'unit_id' => $item->unit_id,
                                    'quantity' => thousand_to_float($request->additional_quantity[$key]),
                                    'price' => thousand_to_float($request->additional_price[$key]),
                                    'sub_total' => $single_sub_total,
                                    'total_tax' => $single_tax_total,
                                    'total' => $single_total,
                                ]);

                                try {
                                    $model_additional->save();
                                } catch (\Throwable $th) {
                                    DB::rollBack();
                                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice additional", $th->getMessage()));
                                }

                                // * create invoice additional tax
                                // $tax_key = $request->additional_item_row_index[$key];
                                // if (isset($_REQUEST['additional_tax_id_' . $tax_key])) {
                                //     InvoiceGeneralAdditionalTax::where('invoice_general_additional_id', $model_additional->id)->whereNotIn('tax_id', $_REQUEST['additional_tax_id_' . $tax_key])->delete();
                                //     foreach ($_REQUEST['additional_tax_id_' . $tax_key] as $key2 => $value2) {
                                //         $tax = Tax::find($value2);
                                //         if ($tax) {
                                //             $single_total_tax_value = $single_sub_total * $tax->value;

                                //             $model_additional_tax = new InvoiceGeneralAdditionalTax();
                                //             $model_additional_tax->loadModel([
                                //                 'invoice_general_additional_id' => $model_additional->id,
                                //                 'tax_id' => $tax->id,
                                //                 'value' => $tax->value,
                                //                 'total' => $single_total_tax_value,
                                //             ]);

                                //             try {
                                //                 $model_additional_tax->save();
                                //             } catch (\Throwable $th) {
                                //                 DB::rollBack();

                                //                 return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice additional tax", $th->getMessage()));
                                //             }

                                //             $single_tax_total += $single_total_tax_value;
                                //         }
                                //     }
                                // }

                                $total_item_tax = 0;
                                if ($request->tax_data) {
                                    $tax_list = $request->tax_data;
                                    foreach ($tax_list as $tax_value) {
                                        $data_tax = Tax::find($tax_value);
                                        if ($data_tax) {
                                            $total_item_tax += $single_sub_total * $data_tax->value;
                                            $tax = new InvoiceGeneralAdditionalTax();
                                            $tax->loadModel([
                                                'invoice_general_additional_id' => $model_additional->id,
                                                'tax_id' => $data_tax->id,
                                                'value' => $data_tax->value,
                                                'total' => $single_sub_total * $data_tax->value,
                                            ]);

                                            try {
                                                $tax->save();
                                            } catch (\Throwable $th) {
                                                DB::rollBack();
                                                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                                            }
                                        }
                                    }
                                }

                                // ? update invoice additional total
                                $model_additional->loadModel([
                                    'total_tax' => $total_item_tax,
                                    'total' => $single_sub_total + $total_item_tax,
                                ]);

                                try {
                                    $model_additional->save();
                                } catch (\Throwable $th) {
                                    DB::rollBack();
                                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating invoice additional", $th->getMessage()));
                                }

                                // ? update invoice total
                                $additional_sub_total += $single_sub_total;
                                $additional_total_tax += $total_item_tax;
                                $additional_total += $single_sub_total + $total_item_tax;
                            }
                        }
                    }
                }
            }
        }


        // * update invoice total
        $model->sub_total_additional = $additional_sub_total;
        $model->total_tax_additional = $additional_total_tax;
        $model->total_additional = $additional_total;
        $model->total = $main_total + $additional_total;
        try {
            $model->save();

            InvoiceTaxSummary::where('model_class', get_class($model))
                ->where('model_id', $model->id)
                ->delete();

            $all_invoice_taxes = new Collection();
            // !! create invoice tax summary
            $invoice_general_detail_taxes = InvoiceGeneralDetailTax::whereHas('invoice_general_detail', function ($q) use ($model) {
                $q->where('invoice_general_id', $model->id);
            })
                ->get();

            $invoice_general_detail_taxes->map(function ($item) use (&$all_invoice_taxes) {
                $all_invoice_taxes->push($item);
            });

            $invoice_general_additional_taxes = InvoiceGeneralAdditionalTax::whereHas('invoice_general_additional', function ($q) use ($model) {
                $q->where('invoice_general_id', $model->id);
            })
                ->get();

            $invoice_general_additional_taxes->map(function ($item) use (&$all_invoice_taxes) {
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
                title: "Invoice General",
                subtitle: Auth::user()->name . " mengajukan Invoice General " . $model->code,
                link: route('admin.invoice-general.show', $model),
                update_status_link: route('admin.invoice-general.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating invoice", $th->getMessage()));
        }

        // * invoice general coa
        $invoiceCoa = new \App\Http\Helpers\InvoiceCoaHelpers($model->id, 'invoice-general');
        try {
            $invoiceCoa->generateCoaDataInvoiceGeneral();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice coa", $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.invoice.index")->with($this->ResponseMessageCRUD(true, 'create', "creating invoice"));
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
     * update_status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        DB::beginTransaction();
        $model = model::findOrFail($id);

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                // * create status log
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);

                $model->loadModel([
                    'status' => $request->status,
                ]);

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
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', "updating invoice status", $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', "updating invoice status"));
    }

    public function export($id,  Request $request)
    {
        $model = model::with('customer', 'approved_by_user')->findOrFail(decryptId($id));
        $invoice_general_details = $model->invoice_general_details;
        $invoice_general_additionals = $model->invoice_general_additionals;
        $so_references = SaleOrderGeneral::whereIn('id', $model->so_references ?? [])->get()
            ->map(function ($item) {
                return $item->no_po_external ?? $item->kode;
            })
            ->toArray();

        if (!$request->preview && authorizePrint('invoice_general')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'invoice_general',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $invoice_general_detail_taxes = InvoiceGeneralDetailTax::whereIn('invoice_general_detail_id', $invoice_general_details->pluck('id')->toArray())->get();
        $invoice_general_additional_taxes = InvoiceGeneralAdditionalTax::whereIn('invoice_general_additional_id', $invoice_general_additionals->pluck('id')->toArray())->get();

        $unique_taxes = collect($invoice_general_detail_taxes)->unique('tax_id');
        $unique_taxes = $unique_taxes->each(function ($item) use ($invoice_general_detail_taxes) {
            $item->total = $invoice_general_detail_taxes->where('tax_id', $item->tax_id)->sum('total');
        });

        $unique_additional_taxes = collect($invoice_general_additional_taxes)->unique('tax_id');
        $unique_additional_taxes = $unique_additional_taxes->each(function ($item) use ($invoice_general_additional_taxes) {
            $item->total = $invoice_general_additional_taxes->where('tax_id', $item->tax_id)->sum('total');
        });

        $unique_all_taxes = $unique_taxes->merge($unique_additional_taxes);
        $unique_all_taxes = collect($unique_all_taxes)->unique('tax_id');
        $unique_all_taxes = $unique_all_taxes->each(function ($item) use ($unique_taxes, $unique_additional_taxes) {
            $item->total_final = $unique_taxes->where('tax_id', $item->tax_id)->sum('total') + $unique_additional_taxes->where('tax_id', $item->tax_id)->sum('total');
        });

        $file = public_path('/pdf_reports/Report-Invoice-General-' . microtime(true) . '.pdf');
        $fileName = 'Report-Invoice-General-' . microtime(true) . '.pdf';

        $qr_url = route('invoice-general.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = \App\Models\Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve');
            }])
            ->where('model_id', $model->id)
            ->first();

        $direktur = Employee::whereHas('position', function ($q) {
            $q->where('nama', 'like', '%direktur%');
        })->first();

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact(
            'model',
            'qr',
            'unique_taxes',
            'unique_additional_taxes',
            'unique_all_taxes',
            'approval',
            'direktur',
            'so_references'
        ))->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

        if ($request->ajax() || $request->preview) {
            $canvas->page_text($w / 5, $h / 1.7, 'PREVIEW ONLY', null, 60, array(0, 0, 0, 0.3), 0, 0, -30);
        }

        if ($request->ajax()) {
            Storage::disk('public')->deleteDirectory('tmp_invoice_general');
            $tmp_file_name = 'invoice_general_' . time() . '.pdf';
            $path = 'tmp_invoice_general/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }


        return $pdf->stream($fileName);
    }

    public function export_with_delivery_orders($id, Request $request)
    {
        $model = model::with('customer', 'bank_internal', 'approved_by_user')->findOrFail(decryptId($id));
        $invoice_general_details = $model->invoice_general_details;
        $invoice_general_additionals = $model->invoice_general_additionals;
        $so_references = SaleOrderGeneral::whereIn('id', $model->so_references ?? [])->get()
            ->map(function ($item) {
                return $item->no_po_external ?? $item->kode;
            })
            ->toArray();

        if (!$request->preview && authorizePrint('invoice_general')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'invoice_general_with_do',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $invoice_general_detail_taxes = InvoiceGeneralDetailTax::whereIn('invoice_general_detail_id', $invoice_general_details->pluck('id')->toArray())->get();
        $invoice_general_additional_taxes = InvoiceGeneralAdditionalTax::whereIn('invoice_general_additional_id', $invoice_general_additionals->pluck('id')->toArray())->get();

        $unique_taxes = collect($invoice_general_detail_taxes)->unique('tax_id');
        $unique_taxes = $unique_taxes->each(function ($item) use ($invoice_general_detail_taxes) {
            $item->total = $invoice_general_detail_taxes->where('tax_id', $item->tax_id)->sum('total');
        });

        $unique_additional_taxes = collect($invoice_general_additional_taxes)->unique('tax_id');
        $unique_additional_taxes = $unique_additional_taxes->each(function ($item) use ($invoice_general_additional_taxes) {
            $item->total = $invoice_general_additional_taxes->where('tax_id', $item->tax_id)->sum('total');
        });

        $unique_all_taxes = $unique_taxes->merge($unique_additional_taxes);
        $unique_all_taxes = collect($unique_all_taxes)->unique('tax_id');
        $unique_all_taxes = $unique_all_taxes->each(function ($item) use ($unique_taxes, $unique_additional_taxes) {
            $item->total_final = $unique_taxes->where('tax_id', $item->tax_id)->sum('total') + $unique_additional_taxes->where('tax_id', $item->tax_id)->sum('total');
        });

        $file = public_path('/pdf_reports/Report-Invoice-General-' . microtime(true) . '.pdf');
        $fileName = 'Report-Invoice-General-' . microtime(true) . '.pdf';

        $qr_url = route('invoice-general.export.id.with-delivery-order', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = \App\Models\Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve');
            }])
            ->where('model_id', $model->id)
            ->first();


        $model->invoice_general_details = $model->invoice_general_details->map(function ($item) {
            $delivery_order = $item->delivery_order_general_detail->delivery_order_general;
            $item->delivery_order_id = $delivery_order->id;

            return $item;
        });

        $pdf = PDF::loadview("admin/.$this->view_folder./export-with-delivery-order", compact('model', 'qr', 'unique_taxes', 'unique_additional_taxes', 'unique_all_taxes', 'approval', 'so_references'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

        return $pdf->stream($fileName);
    }

    public function export_receipt($id, Request $request)
    {
        if (!$request->preview && authorizePrint('invoice_general_receipt')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'invoice_general_receipt',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = model::with('customer', 'approved_by_user')->findOrFail(decryptId($id));

        $fileName = 'kwitansi-' . microtime(true) . '.pdf';

        $qr_url = route('invoice-general.export-receipt.id', ['id' => encryptId($model->id)]);
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
            Storage::disk('public')->deleteDirectory('tmp_invoice_general_receipt');
            $tmp_file_name = 'invoice_general_receipt_' . time() . '.pdf';
            $path = 'tmp_invoice_general_receipt/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream($fileName);
    }

    public function history($id, Request $request)
    {
        try {
            $invoice_generals = DB::table('invoice_generals')
                ->join('invoice_parents', function ($query) {
                    $query->on('invoice_generals.id', '=', 'invoice_parents.reference_id')
                        ->where('invoice_parents.model_reference', '=', 'App\Models\InvoiceGeneral');
                })
                ->where('invoice_generals.id', $id)
                ->whereNull('invoice_generals.deleted_at')
                ->select(
                    'invoice_generals.id',
                    'invoice_generals.code',
                    'invoice_generals.date',
                    'invoice_generals.status',
                    'invoice_parents.id as invoice_parent_id',
                    'invoice_generals.sale_order_general_id'
                )->get();

            $invoice_general_details = DB::table('invoice_general_details')
                ->whereIn('invoice_general_id', $invoice_generals->pluck('id')->toArray())
                ->get();

            $delivery_order_general_details = DB::table('delivery_order_general_details')
                ->whereIn('id', $invoice_general_details->pluck('delivery_order_general_detail_id')->toArray())
                ->get();


            $delivery_order_generals = DB::table('delivery_order_generals')
                ->whereIn('id', $delivery_order_general_details->pluck('delivery_order_general_id')->toArray())
                ->whereNotIn('status', ['rejected', 'void'])
                ->whereNull('deleted_at')
                ->select(
                    'id',
                    'sale_order_general_id',
                    'code',
                    'date',
                    'status',
                )->get();

            $sale_order_generals = DB::table('sale_order_generals')
                ->whereNull('sale_order_generals.deleted_at')
                ->whereIn('sale_order_generals.id', $delivery_order_generals->pluck('sale_order_general_id')->toArray())
                ->select(
                    'sale_order_generals.id',
                    'sale_order_generals.kode as code',
                    'sale_order_generals.tanggal as date',
                    'sale_order_generals.status',
                )
                ->get();

            $invoice_returns = DB::table('invoice_returns')
                ->whereIn('reference_id', $delivery_order_generals->pluck('id')->toArray())
                ->where('reference_model', 'App\Models\DeliveryOrderGeneral')
                ->whereNotIn('status', ['rejected', 'void'])
                ->whereNull('deleted_at')
                ->select(
                    'id',
                    'code',
                    'date',
                    'status',
                )->get();

            $receivables_payments = DB::table('receivables_payment_details')
                ->where('invoice_parent_id', $invoice_generals->pluck('invoice_parent_id')->toArray())
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

            $invoice_generals = $invoice_generals->map(function ($item) {
                // $item->date = localDate($item->date);
                $item->link = route('admin.invoice-general.show', $item->id);
                $item->menu = 'invoice general';
                return $item;
            });


            $sale_order_generals = $sale_order_generals->map(function ($item) {
                // $item->date = localDate($item->date);
                $item->link = route('admin.sales-order-general.show', $item->id);
                $item->menu = 'sales order general';
                return $item;
            });

            $delivery_order_generals = $delivery_order_generals->map(function ($item) {
                // $item->date = localDate($item->date);
                $item->link = route('admin.delivery-order-general.show', $item->id);
                $item->menu = 'delivery order general';
                return $item;
            });


            $invoice_returns = $invoice_returns->map(function ($item) {
                // $item->date = localDate($item->date);
                $item->link = route('admin.invoice-return.show', $item->id);
                $item->menu = 'invoice return';
                return $item;
            });
            $receivables_payments = $receivables_payments->map(function ($item) {
                // $item->date = localDate($item->date);
                $item->link = route('admin.receivables-payment.show', $item->id);
                $item->menu = 'receivables payment';
                return $item;
            });

            $histories = $sale_order_generals->unique('id')
                ->merge($delivery_order_generals->unique('id'))
                ->merge($invoice_generals->unique('id'))
                ->merge($invoice_returns->unique('id'))
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
        if (!$request->preview && authorizePrint('invoice_general_tax')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'invoice_general_tax',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = model::query()
            ->with(['invoice_general_details.item', 'invoice_general_additionals.item'])
            ->findOrFail(decryptId($id));

        $qr_url = route('invoice-general.export-tax.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $pdf = Pdf::loadView("admin.$this->view_folder.export-tax", compact('model', 'qr'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->render();

        if ($request->ajax()) {
            Storage::disk('public')->deleteDirectory('tmp_invoice_general_tax');
            $tmp_file_name = 'invoice_general_tax_' . time() . '.pdf';
            $path = 'tmp_invoice_general_tax/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream("Report-Invoice-General-Tax-$model->code.pdf");
    }


    public function update_reference(Request $request, $id)
    {
        $request->validate([
            'reference' => 'required'
        ]);

        $model = model::findOrFail($id);
        $model->update([
            'reference' => $request->reference
        ]);

        return $this->ResponseJsonMessageCRUD(true, 'update', "updating invoice reference");
    }


    public function generate_invoice_journal()
    {
        DB::beginTransaction();
        try {
            $journals = Journal::where('reference_model', InvoiceGeneral::class)
                ->whereHas('journal_details', function ($q) {
                    $q->where('debit', 0)
                        ->where('credit', 0);
                })
                ->get();

            foreach ($journals as $key => $journal) {
                $journal->journal_details()->where('debit', 0)
                    ->where('credit', 0)
                    ->forceDelete();

                $invoice = $journal->reference_model::find($journal->reference_id);
                foreach ($invoice->invoice_general_details as $key => $invoice_general_detail) {
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
                            'remark' => "{$invoice_general_detail->item->nama}"
                        ]);

                        $journal->journal_details()->create([
                            'coa_id' => $hpp_coa->id,
                            'debit' => $delivery_journal->debit  != 0 ? $delivery_journal->debit : $delivery_journal->credit,
                            'credit' => 0,
                            'remark' => "{$invoice_general_detail->item->nama}"
                        ]);
                    }
                }

                $journal->update([
                    'debit_total' => $journal->journal_details()->sum('debit'),
                    'credit_total' => $journal->journal_details()->sum('credit'),
                ]);
            }

            DB::commit();

            return response()->json($journals->count());
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json($th->getMessage());
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
}
