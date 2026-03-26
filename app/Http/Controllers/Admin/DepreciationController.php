<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\JournalHelpers;
use App\Models\Asset;
use App\Models\Depreciation;
use App\Models\Depreciation as model;
use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DepreciationController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'depreciation';

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
            $data = model::join('assets', 'assets.id', 'depreciations.asset_id')
                ->select('depreciations.*', 'assets.asset_name');

            if ($request->from_date) {
                $data = $data->whereDate('depreciations.date', '>=', Carbon::parse($request->from_date))
                    ->whereDate('depreciations.date', '<=', Carbon::parse($request->to_date));
            }
            if (!get_current_branch()->is_primary) {
                $data->where('depreciations.branch_id', get_current_branch_id());
            }
            if ($request->branch_id) {
                $data->where('depreciations.branch_id', $request->branch_id);
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

            $assets = Asset::leftJoin('depreciations', function ($join) use ($date) {
                $join->on('depreciations.asset_id', 'assets.id')
                    ->whereNull('depreciations.deleted_at');
            })
                ->whereDate('assets.usage_date', '<=', $date)
                ->whereNotNull('assets.depreciation_coa_id')
                ->whereDoesntHave('dispositions', function ($query) use ($date) {
                    $query->whereDate('date', '<=', Carbon::parse($date)->startOfMonth());
                })
                ->where('assets.depreciation_value', '>', 0)
                ->selectRaw('assets.*, coalesce(sum(depreciations.amount),0) as total_depreciation')
                ->havingRaw('total_depreciation < assets.depreciated_value')
                ->groupBy('assets.id')
                ->get();

            $depreciations = Depreciation::get();

            foreach ($assets as $key => $asset) {
                $depreciation_end_date = Carbon::parse($asset->usage_date)->addMonth($asset->estimated_life);
                if (Carbon::parse($asset->depreciation_end_date) != Carbon::parse($depreciation_end_date)) {
                    $asset->depreciation_end_date = $depreciation_end_date;
                    $asset->save();
                }
                $depreciation_value = $asset->value / $asset->estimated_life;
                $lastDepreciation = $depreciations->where('asset_id', $asset->id)->sortByDesc('to_date')->first();

                // * IF THE START USAGE ASSET IF GREATER THEN THIS CURRENT MONTH
                if (Carbon::parse($date)->lt($asset->usage_date)) {
                    continue;
                }

                // * IF THE ASSET ALREADY HAVE A DEPRECIATION
                if ($lastDepreciation) {
                    // * if the last depreciation is not same as this current month
                    if (Carbon::parse($lastDepreciation->date)->format('mY') != $month) {
                        // * find gap between the last depreciation and this current month
                        $gapMonth = $date->floatDiffInRealMonths(Carbon::parse($lastDepreciation->date)->endOfMonth());
                        $gapMonth = round($gapMonth);

                        $count_depreciated = $asset->total_depreciation / $depreciation_value;
                        if (($gapMonth + $count_depreciated) > $asset->estimated_life) {
                            if ($asset->total_depreciation == 0 || $depreciation_value == 0) {
                                $count_depreciated = 0;
                            } else {
                                $count_depreciated = round($count_depreciated);
                            }

                            $gapMonth = $asset->estimated_life - $count_depreciated;
                        }

                        // * calculate the depreciation value
                        $value = $depreciation_value * $gapMonth;

                        $depreciation_count = $lastDepreciation->counter + $gapMonth;

                        $new_depreciation_from_date = Carbon::parse(Carbon::parse($lastDepreciation->date)->addMonthNoOverflow()->startOfMonth());
                        $new_depreciation_to_date = $date;

                        if ($new_depreciation_from_date->lt($new_depreciation_to_date) && $value > 0) {
                            // * create a new depreciation
                            $depreciation = new Depreciation();

                            $depreciation->date = $date;
                            $depreciation->from_date = $new_depreciation_from_date;
                            $depreciation->to_date = $new_depreciation_to_date;

                            $depreciation->branch_id = $asset->branch_id;
                            $depreciation->asset_id = $asset->id;
                            $depreciation->amount = $value;
                            $depreciation->note = "depresiasi ke $depreciation_count, $asset->code / $asset->asset_name";
                            $depreciation->counter = $depreciation_count;

                            if (!$depreciation->check_available_date) {
                                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
                            }
                            $depreciation->save();

                            // * create new journal
                            $journal = new JournalHelpers('depreciation', $depreciation->id);
                            $journal->generate();
                        }
                    }

                    continue;
                }

                // * IF THE ASSET DON'T HAVE ANY DEPRECIATION
                if (!$lastDepreciation) {
                    // * find gap between the asset and this current month
                    $gapMonth = $date->floatDiffInRealMonths(Carbon::parse($asset->usage_date)->startOfMonth());
                    $gapMonth = round($gapMonth);

                    $count_depreciated = $asset->total_depreciation / $depreciation_value;
                    if (($gapMonth + $count_depreciated) > $asset->estimated_life) {
                        if ($asset->total_depreciation == 0 || $depreciation_value == 0) {
                            $count_depreciated = 0;
                        } else {
                            $count_depreciated = round($count_depreciated);
                        }
                        $gapMonth = $asset->estimated_life - $count_depreciated;
                    }

                    // * calculate the depreciation value
                    $value = $depreciation_value * $gapMonth;

                    if ($value > 0) {
                        $depreciation_count = $gapMonth;

                        // * create a new depreciation
                        $depreciation = new Depreciation();

                        $depreciation->date = $date;
                        $depreciation->from_date = Carbon::parse($asset->usage_date);
                        $depreciation->to_date = $date;

                        $depreciation->branch_id = $asset->branch_id;
                        $depreciation->asset_id = $asset->id;
                        $depreciation->amount = $value;
                        $depreciation->note = "depresiasi ke $depreciation_count, $asset->code / $asset->asset";
                        $depreciation->counter = $depreciation_count;

                        if (!$depreciation->check_available_date) {
                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
                        }
                        $depreciation->save();

                        // * create new journal
                        $journal = new JournalHelpers('depreciation', $depreciation->id);
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
        $model = Depreciation::findOrFail($id);

        // * check the closing period
        if (!checkAvailableDate($model->date)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', 'Periode sudah di tutup'));
        }

        try {
            $model->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', 'delete depreciation', $th->getMessage()));
        }

        // * deleting journal
        try {
            Journal::where('reference_model', Depreciation::class)
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
        $models = Depreciation::whereMonth('date', $selected_month)->whereYear('date', $selected_year)
            ->whereNull('model')
            ->get();
        $model_ids = $models->pluck('id')->toArray();

        // * delete depreciation
        try {
            Depreciation::whereMonth('date', $selected_month)->whereYear('date', $selected_year)->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', $th->getMessage()));
        }

        // * delete journal
        try {
            Journal::where('reference_model', Depreciation::class)
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
