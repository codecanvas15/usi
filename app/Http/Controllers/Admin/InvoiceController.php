<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\InvoiceGeneral;
use App\Models\InvoiceParent;
use App\Models\InvoicePayment;
use App\Models\InvoiceTrading;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
protected string $view_folder = 'invoice';

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
        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
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
            $model = InvoiceParent::where('code', 'like', "%$request->search%")->orderByDesc('created_at')->limit(10);
        } else {
            $model = InvoiceParent::orderByDesc('created_at')->limit(10);
        }

        if ($request->customer_id) {
            $model->where('customer_id', $request->customer_id);
        }

        if ($request->payment_status) {
            $model->where('payment_status', $request->payment_status);
        }

        if ($request->status) {
            $model->where('status', $request->status);
        }

        $model = $model->get();
        return $this->ResponseJsonData($model);
    }

    public function generate_receipt_code()
    {
        DB::beginTransaction();
        try {
            $invoice_tradings = InvoiceTrading::withTrashed()
                ->orderBy('date')
                ->orderBy('id')
                ->get();

            $number = 0;
            $date = date('Y-m-d');
            foreach ($invoice_tradings as $key => $invoice_trading) {
                if (Carbon::parse($invoice_trading->date)->format('Ym') != Carbon::parse($date)->format('Ym')) {
                    $number = 0;
                }
                $last_code = sprintf("%04s", $number) . "/KW/USI/X/" . Carbon::parse($invoice_trading->date)->format('Y');
                $code = generate_receipt_code($last_code ?? null, $invoice_trading->date, 'KW');

                DB::table('invoice_tradings')
                    ->where('id', $invoice_trading->id)
                    ->update(['receipt_number' => $code]);

                $date = $invoice_trading->date;
                $number++;
            }

            $invoice_generals = InvoiceGeneral::withTrashed()
                ->orderBy('date')
                ->orderBy('id')
                ->get();

            $number = 0;
            $date = date('Y-m-d');
            foreach ($invoice_generals as $key => $invoice_general) {
                if (Carbon::parse($invoice_general->date)->format('Ym') != Carbon::parse($date)->format('Ym')) {
                    $number = 0;
                }
                $last_code = sprintf("%04s", $number) . "/KWG/USI/X/" . Carbon::parse($invoice_general->date)->format('Y');
                $code = generate_receipt_code($last_code ?? null, $invoice_general->date, 'KWG');

                DB::table('invoice_generals')
                    ->where('id', $invoice_general->id)
                    ->update(['receipt_number' => $code]);

                $date = $invoice_general->date;
                $number++;
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json($th->getMessage());
        }
    }

    public function payment_information(Request $request)
    {
        $invoices = InvoiceParent::whereIn('id', $request->invoice_ids)->get();
        $invoice_payments = InvoicePayment::whereIn('invoice_id', $invoices->pluck('reference_id'))
            ->when($request->date, function ($query) use ($request) {
                return $query->whereDate('date', '<', Carbon::parse($request->date));
            })
            ->with('currency')
            ->get();

        $invoices = $invoices->map(function ($item) use ($invoice_payments) {
            $item->payment_informations = $invoice_payments->where('invoice_id', $item->reference_id)
                ->where('invoice_model', $item->model_reference)
                ->sortBy('date')
                ->values()
                ->all();

            return $item;
        });

        return response()->json($invoices);
    }
}
