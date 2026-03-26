<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ClosingPeriodHelpers;
use App\Models\Authorization;
use App\Models\ClosingPeriod as model;
use App\Models\ClosingPeriod;
use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ClosingPeriodController extends Controller
{
    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'closing-period';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = model::orderByDesc('created_at')
                ->when($request->to_date, fn($q) => $q->whereDate('to_date', '<=', Carbon::parse($request->to_date)))
                ->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('to_date', function ($row) {
                    return Carbon::parse($row->to_date)->translatedFormat('d M Y');
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => true,
                            ],
                            'delete' => [
                                'display' => true,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $prev_closing = ClosingPeriod::orderByDesc('to_date')->first();
        $prev_closing_currencies = $prev_closing ? $prev_closing->closingPeriodCurrencies()->get() : [];

        $currencies = \App\Models\Currency::where('currencies.is_local', false)
            ->select('currencies.*')
            ->get();

        $currencies->map(function ($currency) use ($prev_closing_currencies) {
            if (count($prev_closing_currencies) > 0) {
                $prev_closing_currency = $prev_closing_currencies->where('currency_id', $currency->id)->first();
                $currency->default_rate = $prev_closing_currency ? $prev_closing_currency->exchange_rate : 0;
            }
            return $currency;
        });

        $model = [];

        return view("admin.$this->view_folder.create", compact('currencies', 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), model::rules());
        } else {
            $this->validate($request, model::rules());
        }
        // * create data
        $checkClosedDate = ClosingPeriod::whereDate('to_date', '>=', Carbon::parse($request->to_date))
            ->first();

        if ($checkClosedDate) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'gagal menambahkan closing'))->withInput()->withErrors([
                'to_date' => 'Sudah ada closing di tanggal yang sama atau setelahnya',
            ]);
        }

        $get_last_closing_period = ClosingPeriod::orderByDesc('to_date')
            ->whereDate('to_date', '<', Carbon::parse($request->to_date))
            ->first();

        $get_next_closing_period = ClosingPeriod::orderByDesc('to_date')
            ->whereDate('to_date', '>', Carbon::parse($request->to_date))
            ->first();

        if ($request->status == "open") {
            if ($get_next_closing_period) {
                if ($get_next_closing_period->status != "open") {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'pastikan sudah melakukan open closing di periode setelahnya'));
                }
            }
        } else {
            if ($get_last_closing_period) {
                if ($get_last_closing_period->status != "close") {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'pastikan sudah melakukan close closing di periode sebelumnya'));
                }
            }
        }

        $model = new model();
        $request['to_date'] = Carbon::parse($request->to_date);
        $model->loadModel($request->all());

        // * saving and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        // * creating data details
        $data_details = [];
        if (is_array($request->currency_id)) {
            foreach ($request->currency_id as $key => $currency) {
                $data_details[] = [
                    'closing_period_id' => $model->id,
                    'currency_id' => $currency,
                    'exchange_rate' => thousand_to_float($request->exchange_rate[$key])
                ];
            }
        }

        // * saving details data
        try {
            $model->closingPeriodCurrencies()->delete();
            $model->closingPeriodCurrencies()->createMany($data_details);

            if ($model->status == 'close') {
                $closing_helper = new ClosingPeriodHelpers();
                $closing_helper->execute($model->id);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.closing-period.index")->with($this->ResponseMessageCRUD());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = model::with(['closingPeriodCurrencies.currency'])->findOrFail($id);
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );
        $authorization_logs['can_revert'] = false;
        $authorization_logs['can_void'] = false;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'activity_logs', 'authorization_log_view'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = model::with(['closingPeriodCurrencies.currency'])->findOrFail($id);

        $closingPeriodCurrencies = $model->closingPeriodCurrencies()->get();

        $NotInCurrencies = \App\Models\Currency::where('currencies.is_local', false)
            ->whereNotIn('currencies.id', $closingPeriodCurrencyIds = $closingPeriodCurrencies->pluck('currency_id')->toArray())
            ->select('currencies.*')
            ->get();

        $currencies = \App\Models\Currency::where('currencies.is_local', false)
            ->select('currencies.*')
            ->get();

        return view("admin.$this->view_folder.edit", compact('model', 'currencies', 'NotInCurrencies'));
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
        $model = model::with(['closingPeriodCurrencies.currency'])->findOrFail($id);

        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }
        // * update data
        $checkClosedDate = ClosingPeriod::where('id', '!=', $id)
            ->where(function ($c) use ($request) {
                $c->where(function ($c) use ($request) {
                    $c->whereDate('to_date', '>=', Carbon::parse($request->to_date));
                });
            })
            ->first();

        if ($checkClosedDate && Carbon::parse($request->to_date)->format('Y-m-d') != $model->to_date) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'gagal memperbarui closing'))->withInput()->withErrors([
                'to_date' => 'tanggal tersebut sudah digunakain di closing lain',
            ]);;
        }

        $get_last_closing_period = ClosingPeriod::orderByDesc('to_date')
            ->where('id', '!=', $id)
            ->whereDate('to_date', '<', Carbon::parse($request->to_date))
            ->first();

        $get_next_closing_period = ClosingPeriod::orderByDesc('to_date')
            ->where('id', '!=', $id)
            ->whereDate('to_date', '>', Carbon::parse($request->to_date))
            ->first();


        if ($request->status == "open") {
            if ($get_next_closing_period) {
                if ($get_next_closing_period->status != "open") {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'pastikan sudah melakukan open closing di periode setelahnya'));
                }
            }
        } else {
            if ($get_last_closing_period) {
                if ($get_last_closing_period->status != "close") {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'pastikan sudah melakukan close closing di periode sebelumnya'));
                }
            }
        }

        $model = model::find($id);
        $model->to_date = Carbon::parse($request->to_date);

        if ($model->status == "close" && $request->status == "open") {
            $model->approval_status = "pending";
            $model->status = "close";

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "open closing",
                subtitle: Auth::user()->name . " mengajukan open closing " . localDate($model->to_date),
                link: route('admin.closing-period.show', $model),
                update_status_link: route('admin.closing-period.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } else {
            $model->status = $request->status;
        }

        // * saving and make reponse
        DB::beginTransaction();
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        if ($request->status == "close") {
            // * creating data details
            $data_details = [];
            if (is_array($request->currency_id)) {
                foreach ($request->currency_id as $key => $currency) {
                    $data_details[] = [
                        'closing_period_id' => $model->id,
                        'currency_id' => $currency,
                        'exchange_rate' => thousand_to_float($request->exchange_rate[$key])
                    ];
                }
            }

            // * saving details data
            try {
                $model->closingPeriodCurrencies()->delete();
                $model->closingPeriodCurrencies()->createMany($data_details);

                //check if status change
                if ($model->wasChanged('status') && $request->status == 'close') {
                    $closing_helper = new ClosingPeriodHelpers();
                    $closing_helper->execute($model->id);
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
            }
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $model = model::findOrFail($id);
        DB::beginTransaction();
        try {
            $check_next_closing_period = ClosingPeriod::where('id', '!=', $id)
                ->whereDate('to_date', '>', Carbon::parse($model->to_date))
                ->first();

            if ($check_next_closing_period) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', 'pastikan sudah menghapus terlebih dahulu di periode setelahnya'));
            }
            $model->delete();

            Journal::where('reference_model', ClosingPeriod::class)
                ->where('reference_id', $model->id)
                ->delete();

            Authorization::where('model', ClosingPeriod::class)
                ->where('model_id', $model->id)
                ->delete();
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

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    public function check(Request $request)
    {
        $date = $request->date;
        if (count(explode('-', $request->date)) == 2) {
            $date = '01-' . $request->date;
        }

        $date = Carbon::parse($date);
        $closing = model::whereDate('to_date', '>=', $date)
            ->first();

        $data['success'] = true;
        $data['message'] = '';
        if ($closing) {
            if ($closing->status == "close") {
                $data['success'] = false;
                $data['message'] = 'Maaf, tanggal yang dipilih telah close!';
            }
        }


        return response()->json($data);
    }

    /**
     * update status (approve, reject, cancel)
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        Db::beginTransaction();

        $model = model::findOrFail($id);
        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                if ($request->status == 'approve') {
                    $model->status = 'open';

                    Journal::where('reference_model', ClosingPeriod::class)
                        ->where('reference_id', $model->id)
                        ->delete();
                }
                $model->approval_status = null;
                $model->save();
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }
}
