<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CustomerReceivablesExport;
use App\Http\Controllers\Controller;
use App\Models\BankInternal;
use App\Models\Branch;
use App\Models\Coa;
use App\Models\Currency;
use App\Models\Customer as model;
use App\Models\Customer;
use App\Models\CustomerCoa;
use App\Models\DeliveryOrderGeneral;
use App\Models\DeliveryOrderGeneralDetail;
use App\Models\InvoiceGeneral;
use App\Models\InvoiceGeneralDetail;
use App\Models\InvoicePayment;
use App\Models\SaleOrderGeneral;
use App\Models\SaleOrderGeneralDetail;
use App\Models\WareHouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CustomerReceivablesController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:import customer", ['only' => ['create', 'template', 'preview', 'import']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'customer-receivables';

    public function create()
    {
        $data['data'] = [];
        return view("admin.$this->view_folder.create", $data);
    }

    public function template()
    {
        $data['customers'] = model::orderBy('nama', 'asc')->get();

        return Excel::download(new CustomerReceivablesExport("admin.$this->view_folder.template", $data), 'format-import-customer.xlsx');
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

            $customers = DB::table('customers')->get();

            foreach ($row_range as $row) {
                $date = $sheet->getCell('T' . $row)->getValue() != '' ? Date::excelToDateTimeObject($sheet->getCell('T' . $row)->getValue())->format('Y-m-d') : '';
                $due_date = $sheet->getCell('U' . $row)->getValue() != '' ? Date::excelToDateTimeObject($sheet->getCell('U' . $row)->getValue())->format('Y-m-d') : '';

                $nama = $sheet->getCell('A' . $row)->getValue();
                $alamat = $sheet->getCell('B' . $row)->getValue();
                $npwp = $sheet->getCell('C' . $row)->getValue();
                $email = $sheet->getCell('D' . $row)->getValue();
                $mobile_phone = $sheet->getCell('E' . $row)->getValue();
                $bussiness_phone = $sheet->getCell('F' . $row)->getValue();
                $whatsapp_number = $sheet->getCell('G' . $row)->getValue();
                $fax = $sheet->getCell('H' . $row)->getValue();
                $term_of_payment = $sheet->getCell('I' . $row)->getValue();
                $top_days = $sheet->getCell('J' . $row)->getValue();
                $account_receivable_coa = $sheet->getCell('K' . $row)->getValue();
                $sale_discounts_coa = $sheet->getCell('L' . $row)->getValue();
                $customer_deposite_coa = $sheet->getCell('M' . $row)->getValue();
                $lost_tolerance_type = $sheet->getCell('N' . $row)->getValue();
                $lost_tolerance = $sheet->getCell('O' . $row)->getValue();
                $website = $sheet->getCell('P' . $row)->getValue();
                $invoice_code = $sheet->getCell('Q' . $row)->getValue();
                $currency = $sheet->getCell('R' . $row)->getValue();
                $exchange_rate = $sheet->getCell('S' . $row)->getValue();
                $invoice_date = $date;
                $invoice_due_date = $due_date;
                $tax_number = $sheet->getCell('V' . $row)->getValue();
                $invoice_amount = $sheet->getCell('W' . $row)->getValue();
                $is_data_customer_exists = $customers->where('nama', $nama)->first() ? true : false;

                $data[] = [
                    'nama' => $nama,
                    'alamat' => $alamat,
                    'npwp' => $npwp,
                    'email' => $email,
                    'mobile_phone' => $mobile_phone,
                    'bussiness_phone' => $bussiness_phone,
                    'whatsapp_number' => $whatsapp_number,
                    'fax' => $fax,
                    'term_of_payment' => $term_of_payment,
                    'top_days' => $top_days,
                    'account_receivable_coa' => $account_receivable_coa,
                    'sale_discounts_coa' => $sale_discounts_coa,
                    'customer_deposite_coa' => $customer_deposite_coa,
                    'lost_tolerance_type' => $lost_tolerance_type,
                    'lost_tolerance' => $lost_tolerance,
                    'website' => $website,
                    'invoice_code' => $invoice_code,
                    'currency' => $currency,
                    'exchange_rate' => $exchange_rate,
                    'invoice_date' => $invoice_date,
                    'invoice_due_date' => $invoice_due_date,
                    'tax_number' => $tax_number,
                    'invoice_amount' => $invoice_amount,
                    'is_data_customer_exists' => $is_data_customer_exists
                ];

                $startcount++;
            }

            $compact = [
                'data' => $data,
                'nama_array' => json_encode(collect($data)->pluck('nama')->toArray()),
                'alamat_array' => json_encode(collect($data)->pluck('alamat')->toArray()),
                'npwp_array' => json_encode(collect($data)->pluck('npwp')->toArray()),
                'email_array' => json_encode(collect($data)->pluck('email')->toArray()),
                'mobile_phone_array' => json_encode(collect($data)->pluck('mobile_phone')->toArray()),
                'bussiness_phone_array' => json_encode(collect($data)->pluck('bussiness_phone')->toArray()),
                'whatsapp_number_array' => json_encode(collect($data)->pluck('whatsapp_number')->toArray()),
                'fax_array' => json_encode(collect($data)->pluck('fax')->toArray()),
                'term_of_payment_array' => json_encode(collect($data)->pluck('term_of_payment')->toArray()),
                'top_days_array' => json_encode(collect($data)->pluck('top_days')->toArray()),
                'account_receivable_coa_array' => json_encode(collect($data)->pluck('account_receivable_coa')->toArray()),
                'sale_discounts_coa_array' => json_encode(collect($data)->pluck('sale_discounts_coa')->toArray()),
                'customer_deposite_coa_array' => json_encode(collect($data)->pluck('customer_deposite_coa')->toArray()),
                'lost_tolerance_type_array' => json_encode(collect($data)->pluck('lost_tolerance_type')->toArray()),
                'lost_tolerance_array' => json_encode(collect($data)->pluck('lost_tolerance')->toArray()),
                'website_array' => json_encode(collect($data)->pluck('website')->toArray()),
                'invoice_code_array' => json_encode(collect($data)->pluck('invoice_code')->toArray()),
                'currency_array' => json_encode(collect($data)->pluck('currency')->toArray()),
                'exchange_rate_array' => json_encode(collect($data)->pluck('exchange_rate')->toArray()),
                'invoice_date_array' => json_encode(collect($data)->pluck('invoice_date')->toArray()),
                'invoice_due_date_array' => json_encode(collect($data)->pluck('invoice_due_date')->toArray()),
                'tax_number_array' => json_encode(collect($data)->pluck('tax_number')->toArray()),
                'invoice_amount_array' => json_encode(collect($data)->pluck('invoice_amount')->toArray()),
                'is_data_customer_exists_array' => json_encode(collect($data)->pluck('is_data_customer_exists')->toArray()),
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
            $bussiness_phone_array = json_decode($request->bussiness_phone_array);
            $whatsapp_number_array = json_decode($request->whatsapp_number_array);
            $fax_array = json_decode($request->fax_array);
            $term_of_payment_array = json_decode($request->term_of_payment_array);
            $top_days_array = json_decode($request->top_days_array);
            $account_receivable_coa_array = json_decode($request->account_receivable_coa_array);
            $sale_discounts_coa_array = json_decode($request->sale_discounts_coa_array);
            $customer_deposite_coa_array = json_decode($request->customer_deposite_coa_array);
            $lost_tolerance_type_array = json_decode($request->lost_tolerance_type_array);
            $lost_tolerance_array = json_decode($request->lost_tolerance_array);
            $website_array = json_decode($request->website_array);
            $invoice_code_array = json_decode($request->invoice_code_array);
            $currency_array = json_decode($request->currency_array);
            $exchange_rate_array = json_decode($request->exchange_rate_array);
            $invoice_date_array = json_decode($request->invoice_date_array);
            $invoice_due_date_array = json_decode($request->invoice_due_date_array);
            $tax_number_array = json_decode($request->tax_number_array);
            $invoice_amount_array = json_decode($request->invoice_amount_array);

            foreach ($nama_array as $key => $nama) {
                $date = Carbon::parse($invoice_date_array[$key])->format('Y-m-d');
                $due_date = Carbon::parse($invoice_due_date_array[$key])->format('Y-m-d');
                $due = Carbon::parse($invoice_due_date_array[$key])->diffInDays(Carbon::parse($invoice_date_array[$key]));
                $currency = Currency::where('kode', $currency_array[$key])->first();
                $exchange_rate = $exchange_rate_array[$key] ?? 1;

                // !! save customer
                $customer = Customer::where('nama', $nama)->first();
                if (!$customer) {
                    $customer = new Customer();
                    $customer->nama = $nama_array[$key];
                    $customer->npwp = $npwp_array[$key];
                    $customer->email = $email_array[$key];
                    $customer->mobile_phone = $mobile_phone_array[$key];
                    $customer->bussiness_phone = $bussiness_phone_array[$key];
                    $customer->whatsapp_number = $whatsapp_number_array[$key];
                    $customer->fax = $fax_array[$key];
                    $customer->term_of_payment = $term_of_payment_array[$key];
                    $customer->top_days = $top_days_array[$key] ?? 0;
                    $customer->lost_tolerance_type = $lost_tolerance_type_array[$key];
                    $customer->lost_tolerance = $lost_tolerance_array[$key];
                    $customer->website = $website_array[$key];
                    $customer->alamat = $alamat_array[$key];
                    $customer->is_complete = 1;
                    $customer->save();

                    // !! customer coa
                    CustomerCoa::updateOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'tipe' => 'Account Receivable Coa'
                        ],
                        [
                            'customer_id' => $customer->id,
                            'tipe' => 'Account Receivable Coa',
                            'coa_id' => Coa::where('account_code', $account_receivable_coa_array[$key])->withTrashed()->first()->id ?? null,
                        ],
                    );

                    CustomerCoa::updateOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'tipe' => 'Sale Discounts Coa'
                        ],
                        [
                            'customer_id' => $customer->id,
                            'tipe' => 'Sale Discounts Coa',
                            'coa_id' => Coa::where('account_code', $sale_discounts_coa_array[$key])->withTrashed()->first()->id ?? null,
                        ],
                    );

                    CustomerCoa::updateOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'tipe' => 'Customer Deposite Coa'
                        ],
                        [
                            'customer_id' => $customer->id,
                            'tipe' => 'Customer Deposite Coa',
                            'coa_id' => Coa::where('account_code', $customer_deposite_coa_array[$key])->withTrashed()->first()->id ?? null,
                        ],
                    );
                }


                if (isset($invoice_amount_array[$key]) && thousand_to_float($invoice_amount_array[$key]) != 0) {
                    // !! sales order
                    $item = \App\Models\Item::whereHas('item_category', function ($item) {
                        $item->whereHas('item_type', function ($item) {
                            $item->where('nama', 'purchase item');
                        });
                    })
                        ->first();

                    $sales_order = new SaleOrderGeneral();
                    $sales_order->kode = generate_code(SaleOrderGeneral::class, 'kode', 'tanggal', 'SOG', branch_sort: get_current_branch()->sort ?? null, date: $date);
                    $sales_order->loadModel([
                        'branch_id' => get_current_branch_id(),
                        'customer_id' => $customer->id,
                        'currency_id' => $currency->id,
                        'approved_by' => auth()->user()->id,
                        'tanggal' => Carbon::parse($date),
                        'sub_total' => thousand_to_float($invoice_amount_array[$key]),
                        'total' => thousand_to_float($invoice_amount_array[$key]),
                        'exchange_rate' => $exchange_rate,
                        'status' => 'done',
                        'payment_status' => 'unpaid',
                        'quotation',
                    ]);
                    $sales_order->save();

                    $detail = new SaleOrderGeneralDetail();
                    $detail->loadModel([
                        'sale_order_general_id' => $sales_order->id,
                        'item_id' => $item->id,
                        'unit_id' => $item->unit_id,
                        'price' => thousand_to_float($invoice_amount_array[$key]),
                        'amount' => 1,
                        'sended' => 1,
                        'sub_total' => thousand_to_float($invoice_amount_array[$key]),
                        'total' => thousand_to_float($invoice_amount_array[$key]),
                        'status' => 'done',
                    ]);
                    $detail->save();

                    // !! delivery order
                    $delivery_order_general = new DeliveryOrderGeneral();
                    $delivery_order_general->code = generate_code(DeliveryOrderGeneral::class, 'code', 'date', 'DOG', branch_sort: get_current_branch()->sort ?? null, date: $date);
                    $delivery_order_general->loadModel([
                        'branch_id' => Branch::first()->id,
                        'sale_order_general_id' => $sales_order->id,
                        'customer_id' => $customer->id,
                        'warehouse_id' => WareHouse::first()->id,
                        'external_code' => '-',
                        'date' => $date,
                        'date_send' => $date,
                        'date_receive' => $date,
                        'target_delivery' => $date,
                        'supply' => $customer->alamat ?? '-',
                        'drop' => $customer->alamat ?? '-',
                        'description',
                        'status' => 'done',
                        'is_invoice_created' => 1,
                        'created_by' => Auth::user()->id,
                        'approved_by' => Auth::user()->id,
                    ]);
                    $delivery_order_general->save();

                    $delivery_order_general_detail = new DeliveryOrderGeneralDetail();
                    $delivery_order_general_detail->loadModel([
                        'delivery_order_general_id' => $delivery_order_general->id,
                        'sale_order_general_detail_id' => $detail->id,
                        'item_id' => $detail->item_id,
                        'unit_id' => $detail->unit_id,
                        'quantity' => 1,
                        'hpp' => $detail->item->getCurrentValue() ?? 0,
                        'quantity_received' => 1,
                        'description' => 'SALDO AWAL ' . $customer->nama,
                        'is_invoice_created' => 1,
                    ]);
                    $delivery_order_general_detail->save();

                    // !! invoice
                    $invoice = new InvoiceGeneral();
                    $invoice->loadModel([
                        'branch_id' => $delivery_order_general->branch_id,
                        'sale_order_general_id' => $sales_order->id,
                        'customer_id' => $delivery_order_general->customer_id,
                        'currency_id' => $sales_order->currency_id,
                        'bank_internal_id' => BankInternal::first()->id ?? null,
                        'exchange_rate' => $sales_order->exchange_rate,
                        // 'code' => generate_code_transaction("INVG", InvoiceGeneral::orderByDesc('id')->first()->code ?? null, date: $date),
                        'code' => $invoice_code_array[$key],
                        'reference' => '-',
                        'date' => $date,
                        'due_date' => $due_date,
                        'due' => $due,
                        'term_of_payments' => $customer->term_of_payment,
                        'sub_total_main' => thousand_to_float($invoice_amount_array[$key]),
                        'total_tax_main' => 0,
                        'total_main' => thousand_to_float($invoice_amount_array[$key]),
                        'sub_total_additional' => 0,
                        'total_tax_additional' => 0,
                        'total_additional' => 0,
                        'total' => thousand_to_float($invoice_amount_array[$key]),
                        'status' => 'approve',
                        'payment_status' => 'unpaid',
                        'created_by' => Auth::user()->id,
                        'approved_by' => Auth::user()->id,
                        'reference' => $tax_number_array[$key],
                    ]);
                    $invoice->save();

                    $invoice_general_detail = new InvoiceGeneralDetail();
                    $invoice_general_detail->loadModel([
                        'invoice_general_id' => $invoice->id,
                        'sale_order_general_detail_id' => $detail->id,
                        'item_id' => $delivery_order_general_detail->item_id,
                        'unit_id' => $delivery_order_general_detail->unit_id,
                        'quantity' => 1,
                        'price' => $detail->price,
                        'sub_total' => thousand_to_float($invoice_amount_array[$key]),
                        'total_tax' => 0,
                        'total' => thousand_to_float($invoice_amount_array[$key]),
                        'delivery_order_general_detail_id' => $delivery_order_general_detail->id,
                        'delivery_order_general_id' => $delivery_order_general->id,
                    ]);
                    $invoice_general_detail->save();

                    $invoiceCoa = new \App\Http\Helpers\InvoiceCoaHelpers($invoice->id, 'invoice-general');
                    $invoiceCoa->generateCoaDataInvoiceGeneral();

                    InvoicePayment::updateOrCreate([
                        'invoice_model' => InvoiceGeneral::class,
                        'invoice_id' => $invoice->id,
                    ], [
                        'invoice_model' => InvoiceGeneral::class,
                        'invoice_id' => $invoice->id,
                        'currency_id' => $invoice->currency_id,
                        'exchange_rate' => $invoice->exchange_rate,
                        'model' => InvoiceGeneral::class,
                        'reference_id' => $invoice->id,
                        'date' => $invoice->date,
                        'amount_to_receive' => $invoice->total,
                        'receive_amount' => 0,
                        'note' => "Invoice - $invoice->code",
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
