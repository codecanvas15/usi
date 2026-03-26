<?php

namespace App\Http\Controllers\Admin;

use App\Exports\VendorDebtExport;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Coa;
use App\Models\Currency;
use App\Models\Division;
use App\Models\Price;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceDetail;
use App\Models\SupplierInvoicePayment;
use App\Models\Vendor as model;
use App\Models\Vendor;
use App\Models\VendorCoa;
use App\Models\WareHouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class VendorDebtController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:import vendor", ['only' => ['create', 'template', 'preview', 'import']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'vendor-debt';

    public function create()
    {
        $data['data'] = [];
        return view("admin.$this->view_folder.create", $data);
    }

    public function template()
    {
        $data['vendors'] = model::orderBy('nama', 'asc')->get();

        return Excel::download(new VendorDebtExport("admin.$this->view_folder.template", $data), 'format-import-vendor.xlsx');
    }

    public function preview(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $the_file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $row_range    = range(2, $row_limit);
            $startcount = 2;
            $data = array();

            $vendors = DB::table('vendors')->get();

            foreach ($row_range as $row) {
                $accepted_doc_date = Date::excelToDateTimeObject($sheet->getCell('Q' . $row)->getValue())->format('Y-m-d');
                $date = Date::excelToDateTimeObject($sheet->getCell('R' . $row)->getValue())->format('Y-m-d');
                $due_date = Date::excelToDateTimeObject($sheet->getCell('S' . $row)->getValue())->format('Y-m-d');

                $nama = $sheet->getCell('A' . $row)->getValue();
                $alamat = $sheet->getCell('B' . $row)->getValue();
                $npwp = $sheet->getCell('C' . $row)->getValue();
                $email = $sheet->getCell('D' . $row)->getValue();
                $mobile_phone = $sheet->getCell('E' . $row)->getValue();
                $business_phone = $sheet->getCell('F' . $row)->getValue();
                $whatsapp = $sheet->getCell('G' . $row)->getValue();
                $fax = $sheet->getCell('H' . $row)->getValue();
                $term_of_payment = $sheet->getCell('I' . $row)->getValue();
                $top_days = $sheet->getCell('J' . $row)->getValue();
                $account_payable_coa = $sheet->getCell('K' . $row)->getValue();
                $purchase_discounts_coa = $sheet->getCell('L' . $row)->getValue();
                $vendor_deposite_coa = $sheet->getCell('M' . $row)->getValue();
                $invoice_code = $sheet->getCell('N' . $row)->getValue();
                $currency = $sheet->getCell('O' . $row)->getValue();
                $exchange_rate = $sheet->getCell('P' . $row)->getValue();
                $tax_reference = $sheet->getCell('T' . $row)->getValue();
                $invoice_amount = $sheet->getCell('U' . $row)->getValue();
                $is_vendor_exists = $vendors->where('nama', $nama)->first();

                $data[] = [
                    'nama' => $nama,
                    'alamat' => $alamat,
                    'npwp' => $npwp,
                    'email' => $email,
                    'mobile_phone' => $mobile_phone,
                    'business_phone' => $business_phone,
                    'whatsapp' => $whatsapp,
                    'fax' => $fax,
                    'term_of_payment' => $term_of_payment,
                    'top_days' => $top_days,
                    'account_payable_coa' => $account_payable_coa,
                    'purchase_discounts_coa' => $purchase_discounts_coa,
                    'vendor_deposite_coa' => $vendor_deposite_coa,
                    'invoice_code' => $invoice_code,
                    'currency' => $currency,
                    'exchange_rate' => $exchange_rate,
                    'accepted_doc_date' => $accepted_doc_date,
                    'date' => $date,
                    'due_date' => $due_date,
                    'tax_reference' => $tax_reference,
                    'invoice_amount' => $invoice_amount,
                    'is_vendor_exists' => $is_vendor_exists
                ];

                $startcount++;
            }

            $compact = [
                'data' => $data,
                'nama_array' =>  json_encode(collect($data)->pluck('nama')->toArray()),
                'alamat_array' => json_encode(collect($data)->pluck('alamat')->toArray()),
                'npwp_array' => json_encode(collect($data)->pluck('npwp')->toArray()),
                'email_array' =>    json_encode(collect($data)->pluck('email')->toArray()),
                'mobile_phone_array' =>  json_encode(collect($data)->pluck('mobile_phone')->toArray()),
                'business_phone_array' =>  json_encode(collect($data)->pluck('business_phone')->toArray()),
                'whatsapp_array' =>     json_encode(collect($data)->pluck('whatsapp')->toArray()),
                'fax_array' =>  json_encode(collect($data)->pluck('fax')->toArray()),
                'term_of_payment_array' =>  json_encode(collect($data)->pluck('term_of_payment')->toArray()),
                'top_days_array' =>  json_encode(collect($data)->pluck('top_days')->toArray()),
                'account_payable_coa_array' =>  json_encode(collect($data)->pluck('account_payable_coa')->toArray()),
                'purchase_discounts_coa_array' =>  json_encode(collect($data)->pluck('purchase_discounts_coa')->toArray()),
                'vendor_deposite_coa_array' =>  json_encode(collect($data)->pluck('vendor_deposite_coa')->toArray()),
                'invoice_code_array' =>  json_encode(collect($data)->pluck('invoice_code')->toArray()),
                'currency_array' =>  json_encode(collect($data)->pluck('currency')->toArray()),
                'exchange_rate_array' =>  json_encode(collect($data)->pluck('exchange_rate')->toArray()),
                'accepted_doc_date_array' =>  json_encode(collect($data)->pluck('accepted_doc_date')->toArray()),
                'date_array' =>  json_encode(collect($data)->pluck('date')->toArray()),
                'due_date_array' =>  json_encode(collect($data)->pluck('due_date')->toArray()),
                'tax_reference_array' =>  json_encode(collect($data)->pluck('tax_reference')->toArray()),
                'invoice_amount_array' =>  json_encode(collect($data)->pluck('invoice_amount')->toArray()),
                'is_vendor_exists_array' =>  json_encode(collect($data)->pluck('is_vendor_exists')->toArray()),
            ];

            return view("admin.$this->view_folder.create", $compact);
        } catch (\Throwable $e) {
            throw $e;
            return redirect()->back()->with(['message' => 'terjadi kesalahan ketika mengupload file']);
        }
    }

    public function import(Request $request)
    {
        DB::beginTransaction();
        try {
            $nama_array = json_decode($request->nama_array);
            $alamat_array = json_decode($request->alamat_array);
            $npwp_array = json_decode($request->npwp_array);
            $email_array = json_decode($request->email_array);
            $mobile_phone_array = json_decode($request->mobile_phone_array);
            $business_phone_array = json_decode($request->business_phone_array);
            $whatsapp_array = json_decode($request->whatsapp_array);
            $fax_array = json_decode($request->fax_array);
            $term_of_payment_array = json_decode($request->term_of_payment_array);
            $top_days_array = json_decode($request->top_days_array);
            $account_payable_coa_array = json_decode($request->account_payable_coa_array);
            $purchase_discounts_coa_array = json_decode($request->purchase_discounts_coa_array);
            $vendor_deposite_coa_array = json_decode($request->vendor_deposite_coa_array);
            $invoice_code_array = json_decode($request->invoice_code_array);
            $currency_array = json_decode($request->currency_array);
            $exchange_rate_array = json_decode($request->exchange_rate_array);
            $accepted_doc_date_array = json_decode($request->accepted_doc_date_array);
            $date_array = json_decode($request->date_array);
            $due_date_array = json_decode($request->due_date_array);
            $tax_reference_array = json_decode($request->tax_reference_array);
            $invoice_amount_array = json_decode($request->invoice_amount_array);

            foreach ($nama_array as $key => $nama) {
                $accepted_doc_date = Carbon::parse($accepted_doc_date_array[$key])->format('Y-m-d');
                $date = Carbon::parse($date_array[$key])->format('Y-m-d');
                $due_date = Carbon::parse($due_date_array[$key])->format('Y-m-d');
                $due = Carbon::parse($due_date_array[$key])->diffInDays(Carbon::parse($date_array[$key]));
                $currency = Currency::where('kode', $currency_array[$key])->first();
                $exchange_rate = $exchange_rate_array[$key] ?? 1;

                // !! save vendor
                $vendor = Vendor::where('nama', $nama)->first();
                if (!$vendor) {
                    $vendor = new Vendor();
                    $vendor->nama = $nama_array[$key];
                    $vendor->npwp = $npwp_array[$key];
                    $vendor->email = $email_array[$key];
                    $vendor->mobile_phone = $mobile_phone_array[$key];
                    $vendor->business_phone = $business_phone_array[$key];
                    $vendor->whatsapp = $whatsapp_array[$key];
                    $vendor->fax = $fax_array[$key];
                    $vendor->term_of_payment = $term_of_payment_array[$key];
                    $vendor->top_days = $top_days_array[$key] ?? 0;
                    $vendor->alamat = $alamat_array[$key];
                    $vendor->save();

                    // !! vendor coa
                    VendorCoa::updateOrCreate(
                        [
                            'vendor_id' => $vendor->id,
                            'type' => 'Account Payable Coa'
                        ],
                        [
                            'vendor_id' => $vendor->id,
                            'type' => 'Account Payable Coa',
                            'coa_id' => Coa::where('account_code', $account_payable_coa_array[$key])->first()->id ?? null,
                        ],
                    );

                    VendorCoa::updateOrCreate(
                        [
                            'vendor_id' => $vendor->id,
                            'type' => 'Purchase Discounts Coa'
                        ],
                        [
                            'vendor_id' => $vendor->id,
                            'type' => 'Purchase Discounts Coa',
                            'coa_id' => Coa::where('account_code', $purchase_discounts_coa_array[$key])->first()->id ?? null,
                        ],
                    );

                    VendorCoa::updateOrCreate(
                        [
                            'vendor_id' => $vendor->id,
                            'type' => 'Vendor Deposite Coa'
                        ],
                        [
                            'vendor_id' => $vendor->id,
                            'type' => 'Vendor Deposite Coa',
                            'coa_id' => Coa::where('account_code', $vendor_deposite_coa_array[$key])->first()->id ?? null,
                        ],
                    );
                }


                if (thousand_to_float($invoice_amount_array[$key] ?? 0) != 0) {
                    // !! purchase request
                    $last_purchase_request = PurchaseRequest::where('branch_id', get_current_branch_id())
                        // ->where('type', $model->type)
                        ->whereMonth('tanggal', Carbon::parse($date))
                        ->orderBy('id', 'desc')
                        ->withTrashed()
                        ->first();

                    $purchase_request = new PurchaseRequest();
                    if ($last_purchase_request) {
                        $purchase_request->kode = generate_code_purchase_request($last_purchase_request->kode, date: $date, year: $date);
                    } else {
                        $purchase_request->kode = generate_code_purchase_request("0000/0000/00/0000", date: $date, year: $date);
                    }

                    $purchase_request->loadModel([
                        'branch_id' => get_current_branch_id(),
                        'division_id' => Division::first()->id,
                        'tanggal' => $date,
                        'status' => 'approve',
                        'type' => 'general',
                        'keterangan' => 'SALDO AWAL ' . $vendor->nama,
                        'approved_by' => Auth::user()->id,
                    ]);
                    $purchase_request->save();

                    $item = \App\Models\Item::whereHas('item_category', function ($item) {
                        $item->whereHas('item_type', function ($item) {
                            $item->where('nama', 'purchase item');
                        });
                    })
                        ->first();

                    $purchase_request_detail = new PurchaseRequestDetail();
                    $purchase_request_detail->loadModel([
                        'item_id' => $item->id,
                        'purchase_request_id' => $purchase_request->id,
                        'unit_id' => $item->unit_id,
                        'jumlah' => 1,
                        'jumlah_diapprove' => 1,
                        'status' => 'done',
                        'keterangan' => 'SALDO AWAL ' . $vendor->nama,
                    ]);
                    $purchase_request_detail->save();

                    // !! purchase order
                    $purchase = new \App\Models\Purchase();
                    $purchase->fill([
                        'branch_id' => get_current_branch_id(),
                        'kode',
                        'tanggal' => $date,
                        'tipe' => 'general',
                        'model_reference' => \App\Models\PurchaseOrderGeneral::class,
                        'status' => 'done',
                        'vendor_id' => $vendor->id,
                        'currency_id' => $currency->id,
                        'exchange_rate' => $exchange_rate,
                    ]);

                    $purchase->save();

                    $purchase_general = new \App\Models\PurchaseOrderGeneral();
                    $purchase_general->fill([
                        'purchase_id' => $purchase->id,
                        'branch_id' => get_current_branch_id(),
                        'vendor_id' => $vendor->id,
                        'project_id',
                        'currency_id' => $currency->id,
                        'exchange_rate' => $exchange_rate,
                        'created_by',
                        'approved_by',
                        'code',
                        'date' => $date,
                        'status' => 'done',
                        'quotation',
                        'term_of_payment' => $vendor->term_of_payment,
                        'term_of_payment_days' => $vendor->top_days,
                        'total' => thousand_to_float($invoice_amount_array[$key]),
                        'total_main' => thousand_to_float($invoice_amount_array[$key]),
                    ]);
                    $purchase_general->save();

                    $purchase->update([
                        'model_id' => $purchase_general->id,
                    ]);

                    $purchase_general_detail = new \App\Models\PurchaseOrderGeneralDetail();
                    $purchase_general_detail->fill([
                        'purchase_order_general_id' => $purchase_general->id,
                        'purchase_request_id' => $purchase_request->id,
                        'type' => 'main',
                        'status' => 'done',
                        'sub_total' => thousand_to_float($invoice_amount_array[$key]),
                        'sub_total_after_tax' => thousand_to_float($invoice_amount_array[$key]),
                        'total' => thousand_to_float($invoice_amount_array[$key]),
                    ]);
                    $purchase_general_detail->save();

                    $purchase_general_detail_item = new \App\Models\PurchaseOrderGeneralDetailItem();
                    $purchase_general_detail_item->fill(
                        [
                            'purchase_order_general_detail_id' => $purchase_general_detail->id,
                            'purchase_request_detail_id' => $purchase_request_detail->id,
                            'item_id' => $item->id,
                            'unit_id' => $item->unit_id,
                            'status' => 'done',
                            'quantity' => 1,
                            'quantity_received' => 1,
                            'price' => thousand_to_float($invoice_amount_array[$key]),
                            'discount_type',
                            'discount_value',
                            'discount_value_percent',
                            'sub_total' => thousand_to_float($invoice_amount_array[$key]),
                            'sub_total_after_tax' => thousand_to_float($invoice_amount_array[$key]),
                            'amount_discount',
                            'tax_total',
                            'total' => thousand_to_float($invoice_amount_array[$key]),
                        ]
                    );
                    $purchase_general_detail_item->save();

                    // !! item receiving report
                    $price = new Price();
                    $price->fill(
                        [
                            'item_id' => $item->id,
                            'nama' => '-',
                            'harga_beli' => thousand_to_float($invoice_amount_array[$key]),
                        ]
                    );
                    $price->save();

                    $item_receiving_report = new \App\Models\ItemReceivingReport();
                    $item_receiving_report->fill(
                        [
                            'branch_id' => $purchase_general->branch_id,
                            'date_receive' => $date,
                            'date_receive_time' => Carbon::now()->format('H:i:s'),
                            'kode' => generate_code_transaction("LPBG", null, date: $date),
                            'tipe' => 'general',
                            'reference_model' => \App\Models\PurchaseOrderGeneral::class,
                            'reference_id' => $purchase_general->id,
                            'ware_house_id' => WareHouse::first()->id,
                            'status' => 'pending',
                            'sub_total' => thousand_to_float($invoice_amount_array[$key]),
                            'tax_total' => 0,
                            'total' => thousand_to_float($invoice_amount_array[$key]),
                            'currency_id' => $currency->id,
                            'exchange_rate' => $exchange_rate,
                            'vendor_id' => $vendor->id,
                        ]
                    );
                    $item_receiving_report->save();

                    DB::table('item_receiving_reports')
                        ->where('id', $item_receiving_report->id)
                        ->update(['status' => 'approve']);

                    $item_receiving_report_detail = new \App\Models\ItemReceivingReportDetail();
                    $item_receiving_report_detail->fill(
                        [
                            'item_receiving_report_id' => $item_receiving_report->id,
                            'price_id' => $price->id,
                            'item_id' => $item->id,
                            'jumlah_diterima' => 1,
                            'reference_id' => $purchase_general_detail_item->id,
                            'reference_model' => \App\Models\PurchaseOrderGeneralDetailItem::class,
                            'sub_total' => thousand_to_float($invoice_amount_array[$key]),
                            'tax_total' => 0,
                            'total' => thousand_to_float($invoice_amount_array[$key]),
                        ]
                    );
                    $item_receiving_report_detail->save();

                    $lpb_coa = new \App\Http\Helpers\ItemReceivingReportCoaHelpers($item_receiving_report->tipe, $item_receiving_report->reference_id, $item_receiving_report->id);
                    $lpb_coa->create_item_receiving_report_coa();

                    $branch = Branch::find($purchase_general->branch_id);

                    // !! supplier invoice
                    $supplier_invoice = new \App\Models\SupplierInvoice();
                    // $supplier_invoice->code = generate_code(SupplierInvoice::class, 'code', 'date', 'SI', branch_sort: $branch->sort ?? null, date: $date);
                    $supplier_invoice->code = $invoice_code_array[$key];
                    $supplier_invoice->loadModel([
                        'branch_id' => $purchase_general->branch_id,
                        'vendor_id' => $vendor->id,
                        'currency_id' => $currency->id,
                        'exchange_rate' => $exchange_rate,
                        'reference' => $invoice_code_array[$key],
                        'tax_reference' => $tax_reference_array[$key],
                        'date' => $date,
                        'term_of_payment' => $vendor->term_of_payment,
                        'top_days' => $due,
                        'top_due_date' => $due_date,
                        'payment_status' => 'unpaid',
                        'status' => 'approve',
                        'approved_by' => Auth::user()->id,
                        'sub_total' => thousand_to_float($invoice_amount_array[$key]),
                        'tax_total' => 0,
                        'grand_total' => thousand_to_float($invoice_amount_array[$key]),
                        'accepted_doc_date' => $accepted_doc_date,
                    ]);

                    $supplier_invoice->save();

                    $detail = new SupplierInvoiceDetail();
                    $detail->supplier_invoice_id = $supplier_invoice->id;
                    $detail->item_receiving_report_id = $item_receiving_report->id;
                    $detail->reference_id = $purchase_general->id;
                    $detail->reference_model = \App\Models\PurchaseOrderGeneral::class;
                    $detail->sub_total = thousand_to_float($invoice_amount_array[$key]);
                    $detail->tax = 0;
                    $detail->total = thousand_to_float($invoice_amount_array[$key]);
                    $detail->notes = 'SALDO AWAL ' . $vendor->nama;
                    $detail->save();

                    SupplierInvoicePayment::updateOrCreate([
                        'supplier_invoice_model' => SupplierInvoice::class,
                        'supplier_invoice_id' => $supplier_invoice->id,
                    ], [
                        'supplier_invoice_model' => SupplierInvoice::class,
                        'supplier_invoice_id' => $supplier_invoice->id,
                        'currency_id' => $supplier_invoice->currency_id,
                        'exchange_rate' => $supplier_invoice->exchange_rate,
                        'model' => SupplierInvoice::class,
                        'reference_id' => $supplier_invoice->id,
                        'date' => $supplier_invoice->date,
                        'amount_to_pay' => $supplier_invoice->grand_total,
                        'pay_amount' => 0,
                        'note' => "Invoice - $supplier_invoice->reference",
                    ]);
                }
            }

            DB::commit();
            return redirect()->route("admin.$this->view_folder.create")->with($this->ResponseMessageCRUD());
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return redirect()->route("admin.$this->view_folder.create")->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }
    }
}
