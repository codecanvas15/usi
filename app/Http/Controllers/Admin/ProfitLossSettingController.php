<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Coa;
use App\Models\ProfitLossDetail;
use App\Models\ProfitLossSubcategory;
use Illuminate\Http\Request;

class ProfitLossSettingController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'profit-loss-setting';

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
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {}

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
    public function destroy(Request $request, $id) {}

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request) {}

    public function get_data()
    {
        $profit_loss_categories = ProfitLossSubcategory::all();
        $coas = Coa::withTrashed()
            ->where(function ($query) {
                $query->where('deleted_at', null)
                    ->orWhereHas('journal_details', function ($q) {
                        $q->whereHas('journal', function ($qr) {
                            $qr->whereNull('deleted_at');
                        });
                    });
            })
            ->get();

        $profit_loss_categories = $profit_loss_categories->map(function ($item) use ($coas) {
            $item->profit_loss_details = $item->profit_loss_details
                ->filter(function ($detail) use ($coas) {
                    return in_array($detail->coa_id, $coas->pluck('id')->toArray());
                })
                ->map(function ($detail) {
                    $detail->coa_code = $detail->coa->account_code;
                    return $detail;
                })
                ->sortBy('coa_code');

            return $item;
        });
        return view('admin.' . $this->view_folder . '.data', ['data' => $profit_loss_categories])->render();
    }

    public function refresh()
    {
        try {
            $inserted_coa = ProfitLossDetail::all()->pluck('coa_id');
            $revenue_coa = Coa::withTrashed()
                ->where(function ($query) {
                    $query->where('deleted_at', null)
                        ->orWhereHas('journal_details', function ($q) {
                            $q->whereHas('journal', function ($qr) {
                                $qr->whereNull('deleted_at');
                            });
                        });
                })
                ->where('account_category', 'revenue')
                ->whereDoesntHave('childs')
                ->whereNotIn('id', $inserted_coa)
                ->orderBy('account_code')
                ->get();

            $profit_loss_subcategory_other_revenue = ProfitLossSubcategory::where('name', 'pendapatan-diluar-usaha')->first();
            foreach ($revenue_coa as $key => $revenue) {
                $profit_loss_detail = new ProfitLossDetail();
                $profit_loss_detail->profit_loss_subcategory_id = $profit_loss_subcategory_other_revenue->id;
                $profit_loss_detail->coa_id = $revenue->id;
                $profit_loss_detail->save();
            }

            $expense_coa = Coa::where('account_category', 'expense')
                ->whereDoesntHave('childs')
                ->whereNotIn('id', $inserted_coa)
                ->get();

            $profit_loss_subcategory_other_expense = ProfitLossSubcategory::where('name', 'biaya-diluar-usaha')->first();
            foreach ($expense_coa as $key => $expense) {
                $profit_loss_detail = new ProfitLossDetail();
                $profit_loss_detail->profit_loss_subcategory_id = $profit_loss_subcategory_other_expense->id;
                $profit_loss_detail->coa_id = $expense->id;
                $profit_loss_detail->save();
            }

            return response()->json('success');
        } catch (\Throwable $th) {
            throw $th;
            return response()->json('failed');
        }
    }

    public function update_position(Request $request)
    {
        try {
            $detail = ProfitLossDetail::find($request->detail_id);
            if ($detail) {
                $detail->profit_loss_subcategory_id = $request->subcategory_id;
                $detail->position = $request->position;
                $detail->save();
            }

            return response()->json('success');
        } catch (\Throwable $th) {
            throw $th;
            return response()->json('failed');
        }
    }

    public function update_order(Request $request)
    {
        try {
            $detail = ProfitLossDetail::find($request->id);
            if ($detail) {
                $detail->position = $request->position;
                $detail->save();
            }

            return response()->json('success');
        } catch (\Throwable $th) {
            throw $th;
            return response()->json('failed');
        }
    }
}
