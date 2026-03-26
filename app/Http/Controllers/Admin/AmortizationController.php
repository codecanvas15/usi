<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\JournalHelpers;
use App\Models\Amortization;
use App\Models\Amortization as model;
use App\Models\Lease;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AmortizationController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'amortization';

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
            $data = model::join('leases', 'leases.id', 'amortizations.lease_id')
                ->select('amortizations.*', 'leases.lease_name')
                ->where('status', 'active');

            if ($request->from_date) {
                $data = $data->whereDate('amortizations.date', '>=', Carbon::parse($request->from_date))
                    ->whereDate('amortizations.date', '<=', Carbon::parse($request->to_date));
            }
            if (!get_current_branch()->is_primary) {
                $data->where('amortizations.branch_id', get_current_branch_id());
            }
            if ($request->branch_id) {
                $data->where('amortizations.branch_id', $request->branch_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', fn($row) => Carbon::parse($row->date)->format('d-m-Y'))
                ->editColumn('from_date', fn($row) => Carbon::parse($row->from_date)->format('d-m-Y'))
                ->editColumn('to_date', fn($row) => Carbon::parse($row->to_date)->format('d-m-Y'))
                ->editColumn('amount', fn($row) => formatNumber($row->amount))
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
        return view('admin.' . $this->view_folder . '.create');
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
        try {
            $date = '01-' . $request->date;
            $month = Carbon::parse($date)->format('mY');
            $date = Carbon::parse($date)->endOfMonth();

            $leases = Lease::where('status', 'active')
                ->whereDate('leases.from_date', '<=', Carbon::parse($date))
                ->leftjoin('amortizations', function ($q) {
                    $q->on('amortizations.lease_id', 'leases.id')
                        ->whereNull('amortizations.deleted_at');
                })
                ->selectRaw('leases.*, COALESCE(SUM(amortizations.amount), 0) as total_amortization')
                ->havingRaw('total_amortization < leases.value')
                ->groupBy('leases.id')
                ->get();

            $amortizations = Amortization::get();

            foreach ($leases as $key => $lease) {
                $month_duration = Carbon::parse($lease->from_date)->startOfMonth()->diffInMonths(Carbon::parse($lease->to_date)->endOfMonth());
                if ($month_duration == 0) {
                    $month_duration = 1;
                }
                if ($lease->month_duration != $month_duration) {
                    $lease->month_duration = $month_duration;
                }

                $depreciation_value = $lease->value / $lease->month_duration;

                if ($lease->depreciation_value != $depreciation_value) {
                    $lease->depreciation_value = $depreciation_value;
                }

                // save model if there is any changes
                if ($lease->isDirty()) {
                    $lease->save();
                }

                $lastAmortization = $amortizations->where('lease_id', $lease->id)->sortByDesc('id')->first();

                // * IF THE START USAGE lease IF GREATER THEN THIS CURRENT MONTH
                if ($date->lt($lease->from_date)) {
                    continue;
                }

                // * IF THE lease ALREADY HAVE A AMORTIZATION
                if ($lastAmortization) {
                    // * if the last amortization is not same as this current month
                    if (Carbon::parse($lastAmortization->date)->format('mY') != $month) {
                        // * find gap between the last amortization and this current month
                        $gapMonth = $date->floatDiffInRealMonths(Carbon::parse($lastAmortization->date)->endOfMonth());
                        $gapMonth = round($gapMonth);

                        $count_depreciated = $lease->total_amortization / $depreciation_value;
                        if (($gapMonth + $count_depreciated) > $lease->month_duration) {
                            if ($lease->total_amortization == 0 || $depreciation_value == 0) {
                                $count_depreciated = 0;
                            } else {
                                $count_depreciated = round($count_depreciated);
                            }

                            $gapMonth = $lease->month_duration - $count_depreciated;
                        }

                        // * calculate the amortization value
                        $value = $depreciation_value * $gapMonth;

                        $amortization_count = $lastAmortization->counter + $gapMonth;

                        $new_amortization_from_date = Carbon::parse(Carbon::parse($lastAmortization->date)->addMonthNoOverflow()->startOfMonth());
                        $new_amortization_to_date = $date;

                        if ($new_amortization_from_date->lt($new_amortization_to_date) && $value > 0) {
                            // * create a new amortization
                            $amortization = new Amortization();

                            $amortization->date = $date;
                            $amortization->from_date = $new_amortization_from_date;
                            $amortization->to_date = $new_amortization_to_date;

                            $amortization->branch_id = $lease->branch_id;
                            $amortization->lease_id = $lease->id;
                            $amortization->amount = $value;
                            $amortization->note = "amortisasi ke $amortization_count, {$lease->code} / $lease->lease_name";
                            $amortization->counter = $amortization_count;

                            if (!$amortization->check_available_date) {
                                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
                            }
                            $amortization->save();

                            // * create new journal
                            $journal = new JournalHelpers('amortization', $amortization->id);
                            $journal->generate();
                        }
                    }

                    continue;
                }

                // * IF THE lease DON'T HAVE ANY AMORTIZATION
                if (!$lastAmortization) {
                    // * find gap between the lease and this current month
                    $gapMonth = $date->floatDiffInRealMonths(Carbon::parse($lease->from_date)->startOfMonth());
                    $gapMonth = round($gapMonth);

                    $count_depreciated = $lease->total_amortization / $depreciation_value;
                    if ($gapMonth > $lease->month_duration) {
                        if ($lease->total_amortization == 0 || $depreciation_value == 0) {
                            $count_depreciated = 0;
                        } else {
                            $count_depreciated = round($count_depreciated);
                        }
                        $gapMonth = $lease->month_duration - $count_depreciated;
                    }

                    // * calculate the amortization value
                    $value = $depreciation_value * $gapMonth;

                    if ($value > 0) {     # code...
                        $amortization_count = $gapMonth;

                        // * create a new amortization
                        $amortization = new Amortization();

                        $amortization->date = $date;
                        $amortization->from_date = Carbon::parse($lease->from_date);
                        $amortization->to_date = $date;

                        $amortization->branch_id = $lease->branch_id;
                        $amortization->lease_id = $lease->id;
                        $amortization->amount = $value;
                        $amortization->note = "amortisasi ke $amortization_count, {$lease->code} / $lease->lease_name";
                        $amortization->counter = $amortization_count;

                        if (!$amortization->check_available_date) {
                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
                        }
                        $amortization->save();

                        // * create new journal
                        $journal = new JournalHelpers('amortization', $amortization->id);
                        $journal->generate();
                    }

                    continue;
                }
            }

            DB::commit();
            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(message: 'berhasil memproses depresiasi'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $int
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        $model = Amortization::findOrFail($id);

        // * check closing period
        if (!checkAvailableDate($model->date)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', 'Periode sudah di tutup'));
        }

        try {
            $model->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', 'delete amortization', $th->getMessage()));
        }

        // * deleting journal
        try {
            \App\Models\Journal::where('reference_model', Amortization::class)
                ->where('reference_id', $id)
                ->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', 'delete journal', $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * Destroy in range months
     */
    public function destroyRange(Request $request)
    {
        $this->validate($request, [
            'month' => 'required'
        ]);

        $selected_month = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->format('m');
        $selected_year = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->format('Y');

        // * check the closing period
        $thisMonth = \Carbon\Carbon::createFromFormat('m-Y', $request->month)->format('Y-m-d');
        if (!checkAvailableDate($thisMonth)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', 'Periode sudah di tutup'));
        }

        DB::beginTransaction();

        // * find depreciation ids
        $models = Amortization::whereMonth('date', $selected_month)->whereYear('date', $selected_year)->get();
        $model_ids = $models->pluck('id')->toArray();

        // * delete depreciation
        try {
            Amortization::whereMonth('date', $selected_month)->whereYear('date', $selected_year)->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', $th->getMessage()));
        }

        // * delete journal
        try {
            \App\Models\Journal::where('reference_model', Amortization::class)
                ->whereIn('reference_id', $model_ids)
                ->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request) {}
}
