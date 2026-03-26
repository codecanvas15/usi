<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PurchaseRequest\PurchaseRequestByProjectReport;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrderGeneral;
use App\Models\PurchaseOrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseRequestReportController extends Controller
{

    public function __construct() {}

    /**
     * The route name for the pages.
     *
     * @var string
     */
    protected $route = 'admin.purchase-request-report';

    /**
     * The file folder for the pages.
     *
     * @var string
     */
    protected $folder = 'admin.purchase-request-report';

    /**
     * Display or render available purchase request report types.
     */
    public function index()
    {
        return view($this->folder . '.index');
    }

    /**
     * Get data and display or render purchase request report.
     */
    public function show(Request $request, string $type)
    {
        $data = [];

        switch ($type) {
            case "purchase-request-by-project":
                $data = $this->reportPurchaseRequestByProject($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = PurchaseRequestByProjectReport::class;
                break;
            default:
                return redirect()->route("$this->route.index")->with($this->ResponseMessageCRUD(false, "report", "selected report type was not found"));
        }

        $file_path = "$this->folder.$type.$request->format";

        // return $request;

        if ($request->format == 'preview') {
            return view($file_path, $data);
        } elseif ($request->format == 'pdf') {
            $pdf = Pdf::loadView($file_path, $data)
                ->setPaper($paper_size ?? 'a4', $orientation ?? 'potrait');

            return $pdf->stream($type . '.pdf');
        } elseif ($request->format == 'excel') {
            return Excel::download(new $excel_export($file_path, $data), $type . '.xlsx');
        } else {
            return redirect()->route("admin.$this->route.report")->with($this->ResponseMessageCRUD(false, "report", "selected export format was not found"));
        }
    }

    private function reportPurchaseRequestByProject(Request $request)
    {
        $purchase_request_generals = new Collection();
        $purchase_request_services = new Collection();

        if ($request->type === 'general' || !$request->type) {
            $purchase_request_generals = DB::table('purchase_request_details')
                ->join('purchase_requests', 'purchase_requests.id', 'purchase_request_details.purchase_request_id')
                ->whereNotIn('purchase_requests.status', ['void', 'reject', 'revert', 'pending'])
                ->where('purchase_requests.type', 'general')
                ->whereNull('purchase_requests.deleted_at')
                ->leftJoin('items', 'items.id', 'purchase_request_details.item_id')
                ->leftJoin('projects', 'projects.id', 'purchase_requests.project_id')
                ->leftJoin('branches', 'branches.id', 'purchase_requests.branch_id')
                ->when($request->purchase_order_id, function ($query) use ($request) {
                    return $query->whereIn('purchase_order_generals.code', $request->purchase_order_id);
                })
                ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                    return $query->where('purchase_requests.branch_id', $request->branch_id);
                })
                ->when(!get_current_branch()->is_primary, function ($query) use ($request) {
                    return $query->where('purchase_requests.branch_id', get_current_branch()->id);
                })
                ->when($request->from_date, function ($query) use ($request) {
                    return $query->whereDate('purchase_requests.created_at', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($query) use ($request) {
                    return $query->whereDate('purchase_requests.created_at', '<=', Carbon::parse($request->to_date));
                })
                ->when($request->project_id, function ($query) use ($request) {
                    return $query->where('purchase_requests.project_id', $request->project_id);
                })
                ->when($request->item, function ($query) use ($request) {
                    return $query->where('purchase_request_details.item', 'like', "%$request->item%");
                })
                ->when($request->status, function ($query) use ($request) {
                    return $query->where('purchase_requests.status', $request->status);
                })
                ->selectRaw('
                projects.id as project_id,
                projects.name as project_name,
                projects.code as project_code,

                branches.id as branch_id,
                branches.name as branch_name,

                purchase_requests.kode as purchase_request_code,
                purchase_requests.tanggal as purchase_request_date,
                purchase_requests.status as purchase_request_status,
                purchase_requests.type as purchase_request_type,
                purchase_requests.keterangan as purchase_request_note,

                purchase_request_details.id as purchase_request_detail_id,
                purchase_request_details.jumlah as purchase_request_quantity,
                purchase_request_details.jumlah_diapprove as purchase_request_quantity_approved,

                items.nama as item_name,
                items.kode as item_code
            ')
                ->orderBy('purchase_requests.tanggal')
                ->get();

            $purchase_order_generals = DB::table('purchase_order_general_detail_items')
                ->join('purchase_order_general_details', 'purchase_order_general_details.id', 'purchase_order_general_detail_items.purchase_order_general_detail_id')
                ->join('purchase_order_generals', 'purchase_order_generals.id', 'purchase_order_general_details.purchase_order_general_id')
                ->whereIn('purchase_order_general_detail_items.purchase_request_detail_id', $purchase_request_generals->pluck('purchase_request_detail_id'))
                ->whereNotIn('purchase_order_generals.status', ['void', 'reject', 'revert', 'pending'])
                ->whereNull('purchase_order_generals.deleted_at')
                ->leftJoin('vendors', 'vendors.id', 'purchase_order_generals.vendor_id')
                ->select(
                    'purchase_order_generals.id',
                    'purchase_order_generals.code',
                    'purchase_order_generals.date as purchase_date',
                    'vendors.nama as vendor_name',
                    'purchase_order_general_detail_items.id as purchase_order_general_detail_item_id',
                    'purchase_order_general_detail_items.purchase_request_detail_id',
                    'purchase_order_general_detail_items.quantity as purchase_quantity',
                )
                ->get();

            $item_receiving_report_generals = DB::table('item_receiving_report_details')
                ->join('item_receiving_reports', 'item_receiving_reports.id', 'item_receiving_report_details.item_receiving_report_id')
                ->where('item_receiving_report_details.reference_model', \App\Models\PurchaseOrderGeneralDetailItem::class)
                ->whereIn('item_receiving_report_details.reference_id', $purchase_order_generals->pluck('purchase_order_general_detail_item_id'))
                ->whereNull('item_receiving_reports.deleted_at')
                ->whereNotIn('item_receiving_reports.status', ['void', 'reject', 'revert', 'pending'])
                ->select(
                    'item_receiving_reports.id',
                    'item_receiving_reports.kode',
                    'item_receiving_report_details.id as item_receiving_report_detail_id',
                    'item_receiving_report_details.reference_model',
                    'item_receiving_report_details.reference_id',
                    'item_receiving_report_details.jumlah_diterima as receiving_report_quantity',
                )
                ->get();

            $purchase_request_generals = $purchase_request_generals->map(function ($item) use ($request, $purchase_order_generals, $item_receiving_report_generals) {
                $purchase_order_general = $purchase_order_generals->where('purchase_request_detail_id', $item->purchase_request_detail_id);
                $item_receiving_report_general = $item_receiving_report_generals->filter(function ($item) use ($purchase_order_general) {
                    return $purchase_order_general->contains('purchase_order_general_detail_item_id', $item->reference_id);
                });

                $item->purchase_code = $purchase_order_general->map(function ($item) use ($request) {
                    return $item->code;
                })->values()->toArray();

                $item->purchase_date = $purchase_order_general->map(function ($item) use ($request) {
                    $date = localDate($item->purchase_date);
                    return $date;
                })->values()->toArray();

                $item->vendor_name = $purchase_order_general->map(function ($item) use ($request) {
                    return $item->vendor_name;
                })->values()->toArray();

                $item->purchase_quantity = $purchase_order_general->sum('purchase_quantity');

                $item->outstanding_quantity = $item->purchase_request_quantity - $item->purchase_quantity;

                $item->receiving_report_code = $item_receiving_report_general->map(function ($item) use ($request) {
                    return $item->kode;
                })->values()->toArray();

                $item->receiving_report_quantity = $item_receiving_report_general->sum('receiving_report_quantity');

                return $item;
            });
        }

        if ($request->type === 'jasa' || !$request->type) {
            $purchase_request_services = DB::table('purchase_request_details')
                ->join('purchase_requests', 'purchase_requests.id', 'purchase_request_details.purchase_request_id')
                ->whereNotIn('purchase_requests.status', ['void', 'reject', 'revert', 'pending'])
                ->where('purchase_requests.type', 'jasa')
                ->whereNull('purchase_requests.deleted_at')
                ->leftJoin('items', 'items.id', 'purchase_request_details.item_id')
                ->leftJoin('projects', 'projects.id', 'purchase_requests.project_id')
                ->leftJoin('branches', 'branches.id', 'purchase_requests.branch_id')
                ->when($request->purchase_order_id, function ($query) use ($request) {
                    return $query->whereIn('purchase_order_services.code', $request->purchase_order_id);
                })
                ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                    return $query->where('purchase_requests.branch_id', $request->branch_id);
                })
                ->when(!get_current_branch()->is_primary, function ($query) use ($request) {
                    return $query->where('purchase_requests.branch_id', get_current_branch()->id);
                })
                ->when($request->from_date, function ($query) use ($request) {
                    return $query->whereDate('purchase_requests.created_at', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($query) use ($request) {
                    return $query->whereDate('purchase_requests.created_at', '<=', Carbon::parse($request->to_date));
                })
                ->when($request->project_id, function ($query) use ($request) {
                    return $query->where('purchase_requests.project_id', $request->project_id);
                })
                ->when($request->item, function ($query) use ($request) {
                    return $query->where('purchase_request_details.item', 'like', "%$request->item%");
                })
                ->when($request->status, function ($query) use ($request) {
                    return $query->where('purchase_requests.status', $request->status);
                })
                ->selectRaw('
                    projects.id as project_id,
                    projects.name as project_name,
                    projects.code as project_code,

                    branches.id as branch_id,
                    branches.name as branch_name,

                    purchase_requests.kode as purchase_request_code,
                    purchase_requests.tanggal as purchase_request_date,
                    purchase_requests.status as purchase_request_status,
                    purchase_requests.type as purchase_request_type,
                    purchase_requests.keterangan as purchase_request_note,

                    purchase_request_details.id as purchase_request_detail_id,
                    purchase_request_details.jumlah as purchase_request_quantity,
                    purchase_request_details.jumlah_diapprove as purchase_request_quantity_approved,

                    items.nama as item_name,
                    items.kode as item_code
                ')
                ->orderBy('purchase_requests.tanggal')
                ->get();

            $purchase_order_services = DB::table('purchase_order_service_detail_items')
                ->join('purchase_order_service_details', 'purchase_order_service_details.id', 'purchase_order_service_detail_items.purchase_order_service_detail_id')
                ->join('purchase_order_services', 'purchase_order_services.id', 'purchase_order_service_details.purchase_order_service_id')
                ->whereIn('purchase_order_service_detail_items.purchase_request_detail_id', $purchase_request_services->pluck('purchase_request_detail_id'))
                ->whereNotIn('purchase_order_services.status', ['void', 'reject', 'revert', 'pending'])
                ->whereNull('purchase_order_services.deleted_at')
                ->leftJoin('vendors', 'vendors.id', 'purchase_order_services.vendor_id')
                ->select(
                    'purchase_order_services.id',
                    'purchase_order_services.code',
                    'purchase_order_services.date as purchase_date',
                    'vendors.nama as vendor_name',
                    'purchase_order_service_detail_items.id as purchase_order_service_detail_item_id',
                    'purchase_order_service_detail_items.purchase_request_detail_id',
                    'purchase_order_service_detail_items.quantity as purchase_quantity',
                )
                ->get();

            $item_receiving_report_services = DB::table('item_receiving_report_details')
                ->join('item_receiving_reports', 'item_receiving_reports.id', 'item_receiving_report_details.item_receiving_report_id')
                ->where('item_receiving_report_details.reference_model', \App\Models\PurchaseOrderServiceDetailItem::class)
                ->whereIn('item_receiving_report_details.reference_id', $purchase_order_services->pluck('purchase_order_service_detail_item_id'))
                ->whereNull('item_receiving_reports.deleted_at')
                ->whereNotIn('item_receiving_reports.status', ['void', 'reject', 'revert', 'pending'])
                ->select(
                    'item_receiving_reports.id',
                    'item_receiving_reports.kode',
                    'item_receiving_report_details.id as item_receiving_report_detail_id',
                    'item_receiving_report_details.reference_id',
                    'item_receiving_report_details.jumlah_diterima as receiving_report_quantity',
                )
                ->get();

            $purchase_request_services = $purchase_request_services->map(function ($item) use ($request, $purchase_order_services, $item_receiving_report_services) {
                $purchase_order_service = $purchase_order_services->where('purchase_request_detail_id', $item->purchase_request_detail_id);
                $item_receiving_report_service = $item_receiving_report_services->filter(function ($item) use ($purchase_order_service) {
                    return $purchase_order_service->contains('purchase_order_service_detail_item_id', $item->reference_id);
                });

                $item->purchase_code = $purchase_order_service->map(function ($item) use ($request) {
                    return $item->code;
                })->values()->toArray();

                $item->purchase_date = $purchase_order_service->map(function ($item) use ($request) {
                    $date = localDate($item->purchase_date);
                    return $date;
                })->values()->toArray();

                $item->vendor_name = $purchase_order_service->map(function ($item) use ($request) {
                    return $item->vendor_name;
                })->values()->toArray();

                $item->purchase_quantity = $purchase_order_service->sum('purchase_quantity');

                $item->outstanding_quantity = $item->purchase_request_quantity - $item->purchase_quantity;

                $item->receiving_report_code = $item_receiving_report_service->map(function ($item) use ($request) {
                    return $item->kode;
                })->values()->toArray();

                $item->receiving_report_quantity = $item_receiving_report_service->sum('receiving_report_quantity');

                return $item;
            });
        }



        if ($request->type === 'general') {
            $purchaseRequests = $purchase_request_generals;
        } elseif ($request->type === 'jasa') {
            $purchaseRequests = $purchase_order_services;
        } else {
            $purchaseRequests = $purchase_request_generals->merge($purchase_request_services);
        }
        $purchaseRequests = $purchaseRequests->groupBy('project_id')->map(function ($item) use ($request) {
            $result = [];
            $result['project_name'] = $item->first()->project_name;
            $result['project_code'] = $item->first()->project_code;
            $result['data'] = $item;

            return $result;
        });

        return [
            'type' => 'purchase-request-by-project',
            'title' => 'Purchase Request By Project',
            'from_date' => localDate($request->from_date),
            'to_date' => localDate($request->to_date),
            'data' => $purchaseRequests,
        ];
    }

    public function select(Request $request)
    {
        // $model = PurchaseOrderGeneral::when($request->search, function ($query, $search) {
        //     return $query->where('code', 'like', "%$search%");
        // })
        //     ->orderByDesc('created_at')
        //     ->paginate(10);

        // return $this->ResponseJson($model);

        $model = PurchaseOrderService::when($request->search, function ($query, $search) {
            return $query->where('code', 'like', "%$search%");
        })
            ->orderByDesc('created_at')
            ->paginate(10);

        $modelGeneral = PurchaseOrderGeneral::when($request->search, function ($query, $search) {
            return $query->where('code', 'like', "%$search%");
        })
            ->orderByDesc('created_at')
            ->paginate(10);

        $model = $model->merge($modelGeneral);

        return $this->ResponseJsonData($model);
    }
}
