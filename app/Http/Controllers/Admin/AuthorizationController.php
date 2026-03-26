<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\NotificationHelper;
use App\Models\AuthorizationDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class AuthorizationController extends Controller
{
    /**
     * permission parent
     */
    private $permissionParent = [
        'approve',
        'reject',
    ];

    /**
     * feature permission
     */
    private $featurePermission = AUTHORIZATIONS;

    /**
     * final permission
     */
    private $finalPermission = [];

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */


    /**
     * Display a listing authorization page.
     */
    public function index()
    {
        return view('admin.authorization.index', [
            'finalPermission' => $this->finalPermission,
            'featurePermission' => $this->featurePermission,
            'parentPermission' => $this->permissionParent,
        ]);
    }

    /**
     * Get datatable for authorizations page
     *
     * @param Request $request
     * @return JsonResponse|Datatable
     */
    public function datatables(Request $request)
    {
        $authorization_categories = AUTHORIZATIONS[$request->model];
        // get all child of AUTHORIZATION
        $all_categories = Arr::flatten(AUTHORIZATIONS);

        if ($request->ajax()) {
            $data = \App\Models\Authorization::leftJoin('authorization_details', function ($authorization_detail) {
                $authorization_detail->on('authorizations.id', 'authorization_details.authorization_id')
                    ->where('authorization_details.user_id', Auth::user()->id);
            })
                ->join('users', 'users.id', 'authorizations.user_id')
                ->join('models', 'models.name', 'authorizations.model')
                ->whereNotNull('authorization_details.id')
                ->when(empty($authorization_categories), function ($q) use ($all_categories) {
                    $q->whereNotIn('models.alias', $all_categories);
                })
                ->whereNull('authorization_details.deleted_at')
                ->when(!empty($authorization_categories), function ($q) use ($authorization_categories) {
                    $q->whereIn('models.alias', $authorization_categories);
                })
                ->selectRaw(
                    'authorizations.id,
                    authorizations.branch_id,
                    authorizations.user_id,
                    authorizations.title,
                    authorizations.subtitle,
                    authorizations.note,
                    authorizations.model,
                    authorizations.model_id,
                    authorizations.link,
                    authorizations.update_status_link,
                    authorizations.amount,
                    authorizations.created_at,
                    authorizations.updated_at,
                    authorizations.revert_or_void_necessary,
                    authorization_details.status,
                    authorization_details.revert_status,
                    authorization_details.void_status,
                    authorization_details.authorization_id,
                    users.name as user_name,
                    CASE
                        WHEN authorization_details.status = "pending" THEN 1
                        WHEN authorization_details.revert_status = "submitted" THEN 2
                        WHEN authorization_details.void_status = "submitted" THEN 3
                        ELSE 10
                    END AS priority
                    ',
                )
                ->groupBy('authorization_id')
                ->when($request->search[0] ?? null, function ($q) use ($request) {
                    $q->where(function ($q) use ($request) {
                        $q->where('authorizations.title', 'like', "%{$request->search[0]}%")
                            ->orWhere('authorizations.subtitle', 'like', "%{$request->search[0]}%")
                            ->orWhere('authorizations.note', 'like', "%{$request->search[0]}%")
                            ->orWhere('users.name', 'like', "%{$request->search[0]}%");
                    });
                })
                ->when($request->from_date, function ($query) {
                    $query->whereDate('authorizations.created_at', '>=', Carbon::parse(request()->from_date));
                })
                ->when($request->to_date, function ($query) {
                    $query->whereDate('authorizations.created_at', '<=', Carbon::parse(request()->to_date));
                });

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('link', function ($row) {
                    $html = '<div class="badge badge-info badge-pill">
                    ' .  $row->title . '
                </div> ';

                    if ($row->amount > 0) {
                        $html .= '<div class="badge badge-info badge-pill">' . $row->reference?->currency?->simbol .  formatNumber($row->amount) . '</div> ';
                    }

                    $html .= "<br><a href='{$row->link}' target='_blank'>{$row->subtitle}</a>";

                    return $html;
                })
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-dark me-2">
                                            ' .  $row->getApprovalCount() . '
                                        </div>';

                    $text =  AUTHORIZATION_STATUS[$row->status]['label'] . ' - ' . AUTHORIZATION_STATUS[$row->status]['text'];
                    $badge .= '<div class="badge badge-' . AUTHORIZATION_STATUS[$row->status]['color'] . '">
                                            ' . $text . '
                                        </div>';

                    if ($row->revert_status == "submitted" || $row->void_status == "submitted") {
                        if ($row->revert_status == "submitted") {
                            $badge .= '<br><div class="mt-2 badge badge-' . REVERT_VOID_REQ_STATUS[$row->revert_status]['color'] . '">
                                           Revert ' . REVERT_VOID_REQ_STATUS[$row->revert_status]['text'] . '
                                        </div>';
                        }

                        if ($row->void_status == "submitted") {
                            $badge .= '<br><div class="mt-2 badge badge-' . REVERT_VOID_REQ_STATUS[$row->void_status]['color'] . '">
                                           Void ' . REVERT_VOID_REQ_STATUS[$row->void_status]['text'] . '
                                        </div>';
                        }
                    }

                    return $badge;
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m /y H:i');
                })
                ->rawColumns(['link', 'status'])
                ->make();
        }

        abort(403);
    }

    /**
     * Get count to show authorization for sidebar.
     *
     * @return JsonResponse
     */
    public function getCountTotalAuthorizationSidebar()
    {
        $count = \App\Models\Authorization::leftJoin('authorization_details', function ($authorization_detail) {
            $authorization_detail->on('authorizations.id', 'authorization_details.authorization_id')
                ->where('authorization_details.user_id', Auth::user()->id);
        })
            ->whereNotNull('authorization_details.id')
            ->where(function ($q) {
                $q->where('authorization_details.status', 'pending')
                    ->orWhere('authorization_details.revert_status', 'submitted')
                    ->orWhere('authorization_details.void_status', 'submitted');
            })
            ->whereNull('authorization_details.deleted_at')
            ->count();

        return $this->ResponseJsonData([
            "total_count" => $count,
        ]);
    }

    /**
     * get count each authorization model
     */
    public function getCountEachAuthorizationModel()
    {
        $all_categories = Arr::flatten(AUTHORIZATIONS);
        $query = \App\Models\Authorization::leftJoin('authorization_details', function ($authorization_detail) {
            $authorization_detail->on('authorizations.id', 'authorization_details.authorization_id')
                ->where('authorization_details.user_id', Auth::user()->id);
        })
            ->join('models', 'models.name', 'authorizations.model')
            ->whereNotNull('authorization_details.id')
            ->where(function ($q) {
                $q->where('authorization_details.status', 'pending')
                    ->orWhere('authorization_details.revert_status', 'submitted')
                    ->orWhere('authorization_details.void_status', 'submitted');
            })
            ->whereNull('authorization_details.deleted_at')
            ->select(
                'authorizations.*',
                'authorization_details.status as status',
                'models.alias as model_alias'
            )
            ->get();

        $query = $query->unique(function ($item) {
            return $item->model . $item->model_id;
        })->values();


        $data = [];
        foreach (AUTHORIZATIONS as $key => $item) {
            if (count($item) == 0) {
                $data[$key] = $query->whereNotIn('model_alias', $all_categories)->count();
            } else {
                $data[$key] = $query->whereIn('model_alias', $item)->count();
            }
        }

        return $this->ResponseJsonData($data);
    }

    public function request_revert_void($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $authorization = \App\Models\Authorization::findOrFail($id);
            $details = $authorization->details;

            if ($details->where('status', 'approve')->count() > 0 && in_array($request->status, ['revert', 'void'])) {
                if ($request->status == 'revert') {
                    if ($authorization->revert_status == 'submitted') {
                        new Exception('permintaan revert telah diajukan');
                    }

                    $authorization->revert_status = 'submitted';
                }
                if ($request->status == 'void') {
                    if ($authorization->void_status == 'submitted') {
                        new Exception('permintaan void telah diajukan');
                    }

                    $authorization->void_status = 'submitted';
                }
                $authorization->revert_or_void_necessary = $request->note;
                $authorization->save();

                AuthorizationDetail::where('authorization_id', $authorization->id)
                    ->where('status', 'approve')
                    ->when($request->status == 'revert', function ($query) {
                        $query->update([
                            'revert_status' => 'submitted',
                        ]);
                    })
                    ->when($request->status == 'void', function ($query) {
                        $query->update([
                            'void_status' => 'submitted',
                        ]);
                    });

                $notification = new NotificationHelper();
                $notification->send_notification(
                    title: "{$authorization->user->name} mengajukan permintaan {$request->status}",
                    body: $authorization->revert_or_void_necessary,
                    reference_model: $authorization->model,
                    reference_id: $authorization->model_id,
                    branch_id: $authorization->branch_id,
                    user_id: AuthorizationDetail::where('authorization_id', $authorization->id)->where('status', 'approve')->pluck('user_id')->toArray(),
                    roles: [],
                    permissions: [],
                    link: $authorization->link,
                );
            }

            DB::commit();

            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'create', null, 'Permintaan ' . $request->status . ' berhasil'));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }
    }

    public function response_revert_void($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $authorization_detail = AuthorizationDetail::findOrFail($id);
            $authorization = $authorization_detail->authorization;
            $note = $authorization->revert_or_void_necessary;

            $response = null;

            if (in_array($request->status_submitted, ['revert', 'void'])) {
                // if respoonse is reject
                if ($request->status == 'reject') {
                    $authorization->revert_or_void_necessary = '';
                    if ($request->status_submitted == 'revert') {
                        $authorization->revert_status = 'available';

                        AuthorizationDetail::where('authorization_id', $authorization->id)
                            ->update(
                                [
                                    'revert_status' => null,
                                ]
                            );
                    } else {
                        $authorization->void_status = 'available';
                        AuthorizationDetail::where('authorization_id', $authorization->id)
                            ->update(
                                [
                                    'void_status' => null,
                                ]
                            );
                    }

                    $notification = new NotificationHelper();
                    $notification->send_notification(
                        title: "permintaan {$request->submitted_status} ditolak",
                        body: $request->message,
                        reference_model: $authorization->model,
                        reference_id: $authorization->model_id,
                        branch_id: $authorization->branch_id,
                        user_id: $authorization->user_id,
                        roles: [],
                        permissions: [],
                        link: $authorization->link,
                    );
                } else {
                    if ($request->status_submitted == 'revert') {
                        if ($authorization_detail->revert_status != $request->status) {
                            AuthorizationDetail::where('authorization_id', $authorization->id)
                                ->where('level', '=', $authorization_detail->level)
                                ->update(
                                    [
                                        'revert_status' => $request->status
                                    ]
                                );
                        }
                    }

                    if ($request->status_submitted == 'void') {
                        if ($authorization_detail->void_status != $request->status) {
                            AuthorizationDetail::where('authorization_id', $authorization->id)
                                ->where('level', '=', $authorization_detail->level)
                                ->update(
                                    [
                                        'void_status' => $request->status
                                    ]
                                );
                        }
                    }

                    $authorization_detail->save();

                    $count_approved = $authorization->details->where('status', 'approve')->count();
                    $count_revert_approved = $authorization->details->where('revert_status', 'approve')->count();
                    $count_void_approved = $authorization->details->where('void_status', 'approve')->count();

                    if ($request->status_submitted == 'revert' && $count_approved == $count_revert_approved) {
                        AuthorizationDetail::where('authorization_id', $authorization->id)
                            ->update(
                                [
                                    'revert_status' => null,
                                ]
                            );

                        $authorization->revert_status = 'available';
                        $authorization->revert_or_void_necessary = '';
                        $authorization->save();

                        $notification = new NotificationHelper();
                        $notification->send_notification(
                            title: "permintaan {$request->submitted_status} disetujui",
                            body: $request->message,
                            reference_model: $authorization->model,
                            reference_id: $authorization->model_id,
                            branch_id: $authorization->branch_id,
                            user_id: $authorization->user_id,
                            roles: [],
                            permissions: [],
                            link: $authorization->link,
                        );

                        $response['status'] = 'revert';
                        $response['authorize_revert'] = true;
                        $response['note'] = $note;
                        $response['message'] = $request->message;
                        $response['authorization_detail_id'] = $id;
                    } else if ($request->status_submitted == 'revert' && $count_approved != $count_void_approved) {
                        $response['status'] = 'revert';
                        $response['authorize_revert'] = false;
                        $response['note'] = $note;
                        $response['message'] = $request->message;
                        $response['authorization_detail_id'] = $id;
                    } else if ($request->status_submitted == 'void' && $count_approved == $count_void_approved) {
                        AuthorizationDetail::where('authorization_id', $authorization->id)
                            ->update(
                                [
                                    'revert_status' => null,
                                ]
                            );

                        $authorization->void_status = 'available';
                        $authorization->revert_or_void_necessary = '';
                        $authorization->save();

                        $notification = new NotificationHelper();
                        $notification->send_notification(
                            title: "permintaan {$request->submitted_status} disetujui",
                            body: $request->message,
                            reference_model: $authorization->model,
                            reference_id: $authorization->model_id,
                            branch_id: $authorization->branch_id,
                            user_id: $authorization->user_id,
                            roles: [],
                            permissions: [],
                            link: $authorization->link,
                        );

                        $response['status'] = 'void';
                        $response['authorize_void'] = true;
                        $response['note'] = $note;
                        $response['message'] = $request->message;
                        $response['authorization_detail_id'] = $id;
                    } else if ($request->status_submitted == 'void' && $count_approved != $count_revert_approved) {
                        $response['status'] = 'void';
                        $response['authorize_void'] = false;
                        $response['note'] = $note;
                        $response['message'] = $request->message;
                        $response['authorization_detail_id'] = $id;
                    }
                }
            }

            $authorization->save();

            DB::commit();

            return response()->json($response);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(null);
        }
    }
}
