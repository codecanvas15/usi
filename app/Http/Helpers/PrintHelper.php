<?php

namespace App\Http\Helpers;

use App\Models\DocumentPrint;
use App\Models\DocumentPrintApproval;
use App\Models\ModelTable;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Str;

class PrintHelper
{
    public function check_can_print(
        $model,
        $model_id,
        $type
    ) {
        $can_print = false;
        $show_request_modal = false;
        $message = '';
        $document_print = DocumentPrint::where('model', $model)
            ->where('model_id', $model_id)
            ->where('type', $type)
            ->get();

        if ($document_print->count() == 0) {
            $document_print = new DocumentPrint();
            $document_print->user_id = Auth::user()->id;
            $document_print->model = $model;
            $document_print->model_id = $model_id;
            $document_print->status = 'approve';
            $document_print->reason = 'first print';
            $document_print->type = $type;
            $document_print->save();

            $can_print = true;
        } else {
            $last_document_print = DocumentPrint::where('model', $model)
                ->where('model_id', $model_id)
                ->where('type', $type)
                ->orderBy('id', 'desc')
                ->first();

            if ($last_document_print->status == 'approve' && $last_document_print->user_id == Auth::user()->id) {
                $can_print = true;
            } else {
                if ($last_document_print->status == 'pending') {
                    $message = 'Sudah ada pengajuan print sebelumnya';
                } else {
                    $message = 'Silahkan ajukan print ulang';
                    $show_request_modal = true;
                }
            }
        }

        return [
            'can_print' => $can_print,
            'show_request_modal' => $show_request_modal,
            'message' => $message,
        ];
    }
    public function print_request(
        $model,
        $model_id,
        $reason,
        $link,
        $export_link,
        $print_type = null,
        $type = null,
        $branch_id = null,
        $title = '',
        $subtitle = '',
    ) {
        DB::beginTransaction();
        try {
            $last_document_print = DocumentPrint::where('model', $model)
                ->where('model_id', $model_id)
                ->orderBy('id', 'desc')
                ->first();

            if ($last_document_print->status  != 'pending') {
                $document_print = new DocumentPrint();
                $document_print->model = $model;
                $document_print->model_id = $model_id;
                $document_print->user_id = Auth::user()->id;
                $document_print->type = $print_type;
                $document_print->link = $link;
                $document_print->export_link = $export_link;
                $document_print->status = 'pending';
                $document_print->reason = $reason;
                $document_print->title = $title;
                $document_print->subtitle = $subtitle;
                $document_print->save();

                $model_data = ModelTable::where('name', $model)
                    ->when($type, function ($q) use ($type) {
                        $q->where('type', $type);
                    })
                    ->first();

                foreach ($model_data->model_authorizations->sortBy('level') as $key => $model_authorization) {
                    $document_print_approval = new DocumentPrintApproval();
                    $document_print_approval->document_print_id = $document_print->id;
                    $document_print_approval->user_id = $model_authorization->user_id;
                    $document_print_approval->status = $key == 0 ? 'pending' : 'draft';
                    $document_print_approval->level = $model_authorization->level;
                    $document_print_approval->save();
                }

                $first_level = $document_print->document_print_approvals->first();
                $get_same_levels = $document_print->document_print_approvals->where('level', $first_level->level);

                foreach ($get_same_levels as $key => $get_same_level) {
                    $get_same_level->status = 'pending';
                    $get_same_level->save();

                    $notification = new NotificationHelper();
                    $notification->send_notification(
                        title: $title,
                        body: $subtitle,
                        reference_model: $model,
                        reference_id: $model_id,
                        branch_id: $branch_id,
                        user_id: $get_same_level->user_id,
                        roles: [],
                        permissions: [],
                        link: $link,
                    );
                }
            } else {
                DB::rollBack();
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Pengajuan print gagal. Sudah ada pengajuan print sebelumnya',
                    ]
                );
            }

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Pengajuan print berhasil',
                ]
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Pengajuan print gagal',
                    'error' => $th->getMessage(),
                ]
            );
        }
    }

    public function authorize(
        $document_print_approval_id,
        $status,
        $note = '',
    ) {
        $document_print_approval = DocumentPrintApproval::find($document_print_approval_id);

        if ($document_print_approval) {
            if (!in_array($document_print_approval->status, ['approve', 'pending']) && $document_print_approval->status != $status) {
                throw new Exception('status tidak valid');
            }
            $document_print_approval->status = $status;
            $document_print_approval->note = $note;
            $document_print_approval->status_at = now();
            $document_print_approval->save();

            $document_print = $document_print_approval->document_print;

            $get_same_levels = $document_print_approval->document_print->document_print_approvals->where('level', $document_print_approval->level)
                ->where('id', '!=', $document_print_approval->id);

            foreach ($get_same_levels as $key => $get_same_level) {
                $get_same_level->status = $status;
                $get_same_level->note = "Otomatis $status";
                $get_same_level->status_at = now();
                $get_same_level->save();
            }

            $dotted_note = $note ? substr($note, 0, 32) . '...' : '';

            if ($status == "reject") {
                $notification = new NotificationHelper();
                $notification->send_notification(
                    title: $document_print->title,
                    body: Str::upper($status) . ' - ' . $dotted_note,
                    reference_model: $document_print->model,
                    reference_id: $document_print->model_id,
                    branch_id: $document_print->branch_id,
                    user_id: $document_print->user_id,
                    roles: [],
                    permissions: [],
                    link: $document_print->link,
                );

                $document_print->status = 'reject';
                $document_print->save();
            } else if ($status == "approve") {
                $max_level = $document_print->document_print_approvals->max('level');
                $is_last_level = $document_print_approval->level == $max_level;
                if ($is_last_level) {
                    $notification = new NotificationHelper();
                    $notification->send_notification(
                        title: $document_print->title,
                        body: Str::upper($status) . ' - ' . $dotted_note,
                        reference_model: $document_print->model,
                        reference_id: $document_print->model_id,
                        branch_id: $document_print->branch_id,
                        user_id: $document_print->user_id,
                        roles: [],
                        permissions: [],
                        link: $document_print->link,
                    );

                    $document_print->status = 'approve';
                    $document_print->save();
                } else {
                    // get next level
                    $next_level = $document_print->document_print_approvals->where('level', '>', $document_print_approval->level)
                        ->where('status', 'draft')
                        ->sortBy('level')
                        ->first();

                    $next_level = $document_print->document_print_approvals->where('level', $next_level->level)
                        ->sortBy('level')
                        ->values();

                    foreach ($next_level as $key => $next) {
                        $next->status = 'pending';
                        $next->save();

                        $notification = new NotificationHelper();
                        $notification->send_notification(
                            branch_id: $document_print->branch_id,
                            user_id: $next->user_id,
                            roles: [],
                            permissions: [],
                            title: $document_print->title,
                            body: $document_print->subtitle,
                            reference_model: $document_print->model,
                            reference_id: $document_print->model_id,
                            link: $document_print->link,
                        );
                    }
                }
            }
        }
    }

    public function check_available_for_print(
        $model,
        $model_id,
        $type
    ) {
        $document_print = DocumentPrint::where('model', $model)
            ->where('model_id', $model_id)
            ->where('type', $type)
            ->orderBy('id', 'desc')
            ->get();

        if ($document_print->count() == 0) {
            return true;
        } else {
            $user_permission_document = $document_print->where('user_id', Auth::user()->id)->first();
            if ($user_permission_document->status == 'approve') {
                $user_permission_document->status = 'printed';
                $user_permission_document->save();

                return true;
            }
        }

        return false;
    }
}
