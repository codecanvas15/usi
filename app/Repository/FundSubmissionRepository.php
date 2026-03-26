<?php

namespace App\Repository;

use App\Models\AuthorizationDetail;
use App\Models\FundSubmission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FundSubmissionRepository
{
    /**
     * Get list of fund submission
     */
    public function datatable(Request $request, $isGiro = false, $view_folder = 'fund-submission')
    {
        $data = \App\Models\FundSubmission::leftJoin('projects', 'projects.id', 'fund_submissions.project_id')
            ->leftJoin('send_payments', 'send_payments.fund_submission_id', 'fund_submissions.id')
            ->select('fund_submissions.*', 'projects.name as project_name', 'send_payments.realization_date')
            ->when($isGiro, function ($query) {
                $query->where('fund_submissions.is_giro', 1);
            })
            ->when($request->from_date, function ($query) use ($request) {
                $query->whereDate('fund_submissions.date', '>=', Carbon::parse($request->from_date))
                    ->whereDate('fund_submissions.date', '<=', Carbon::parse($request->to_date));
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                $query->where('fund_submissions.branch_id', get_current_branch_id());
            })
            ->when($request->branch_id and get_current_branch()->is_primary, function ($query) use ($request) {
                $query->where('fund_submissions.branch_id', $request->branch_id);
            })
            ->when(!is_null($request->is_used), function ($query) use ($request) {
                $query->where('fund_submissions.is_used', (int)$request->is_used);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('fund_submissions.status', $request->status);
            })
            ->groupBy('fund_submissions.id');

        $authorization_details = AuthorizationDetail::leftJoin('authorizations', 'authorizations.id', 'authorization_details.authorization_id')
            ->select('authorization_details.*', 'authorizations.model_id', 'authorizations.model')
            ->where('authorizations.model', FundSubmission::class)
            ->whereIn('authorizations.model_id', $data->pluck('id'))
            ->get();

        if (!$isGiro) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', fn($row) => Carbon::parse($row->date)->format('d-m-Y'))
                ->editColumn('amount', fn($row) => formatNumber($row->amount))
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . fund_submission_status()[$row->status]['color'] . '">
                    ' . fund_submission_status()[$row->status]['text'] . '
                                    </div>';

                    return $badge;
                })
                ->editColumn('is_used', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . fund_submission_usage_status()[$row->is_used]['color'] . '">
                    ' . fund_submission_usage_status()[$row->is_used]['text'] . '
                                    </div>';

                    return $badge;
                })
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $view_folder,
                ]) . '<br>' .
                    view("components.datatable.export-button", [
                        'route' => route("fund-submission.export.id", ['id' => encryptId($row->id)]),
                        'onclick' => "",
                    ]))
                ->addColumn('action', function ($row) use ($authorization_details) {
                    $can_delete_or_void = $authorization_details->where('model_id', $row->id)
                        ->where('status', 'approve')->count() == 0;

                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => 'fund-submission',
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => $row->status != "approve" &&  $row->status != "reject" &&  $row->status != "void" && $row->can_change_sensitive_data && $can_delete_or_void,
                            ],
                            'delete' => [
                                'display' => $row->status != "approve" &&  $row->status != "reject" &&  $row->status != "void" && $row->can_change_sensitive_data && $can_delete_or_void,
                            ],
                        ],
                    ]);
                })
                ->addColumn('project_name', function ($row) {
                    return $row->project_name ?? '';
                })
                ->addColumn('total', function ($row) {
                    return $row->currency->simbol . " " . formatNumber($row->total);
                })
                ->rawColumns(['status', 'action', 'export', 'is_used', 'code'])
                ->make(true);
        }

        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('date', fn($row) => Carbon::parse($row->date)->format('d-m-Y'))
            ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                'field' => $row->code,
                'row' => $row,
                'main' => $view_folder,
            ]))
            ->editColumn('realization_date', fn($row) => localDate($row->realization_date))
            ->editColumn('giro_liquid_date', fn($row) => Carbon::parse($row->giro_liquid_date)->format('d-m-Y'))
            ->editColumn('total', fn($row) => formatNumber($row->total))
            ->editColumn('status', function ($row) {
                $badge = '<div class="badge badge-lg badge-' . fund_submission_status()[$row->status]['color'] . '">
            ' . fund_submission_status()[$row->status]['text'] . '
                            </div>';

                return $badge;
            })

            ->editColumn('export', function ($row) use ($view_folder) {
                $link = route("fund-submission.export.id", ['id' => encryptId($row->id)]);
                $export = '<a target="_blank" href="' . $link . '" class="btn btn-sm btn-light" onclick="show_print_out_modal(event)"><i class="fa fa-file-pdf"></i></a>';

                return $export;
            })
            ->addColumn('action', function ($row) {
                return view('components.datatable.button-datatable', [
                    'row' => $row,
                    'main' => 'fund-submission',
                    'btn_config' => [
                        'detail' => [
                            'display' => true,
                        ],
                        'edit' => [
                            'display' => $row->status != "approve" &&  $row->status != "reject" &&  $row->status != "void" && $row->can_change_sensitive_data,
                        ],
                        'delete' => [
                            'display' => $row->status != "approve" &&  $row->status != "reject" &&  $row->status != "void" && $row->can_change_sensitive_data,
                        ],
                    ],
                ]);
            })
            ->rawColumns(['status', 'action', 'export'])
            ->make(true);
    }
}
