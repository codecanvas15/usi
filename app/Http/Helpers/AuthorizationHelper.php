<?php

namespace App\Http\Helpers;

use App\Models\Authorization;
use App\Models\AuthorizationDetail;
use App\Models\ModelAuthorization;
use App\Models\ModelTable;
use Exception;
use Str;

class AuthorizationHelper
{
    public function is_authoirization_exist(
        $model,
        $type = null
    ) {
        $model_table = ModelTable::where('name', $model)
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->first();

        if ($model_table) {
            $get_master_authorizations = ModelAuthorization::where('model_id', $model_table->id)
                ->get();

            if (count($get_master_authorizations) == 0) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public function init(
        $branch_id,
        $user_id,
        $model,
        $model_id,
        $amount = 0,
        $title = '',
        $subtitle = '',
        $link,
        $update_status_link,
        $type = null,
        $division_id = null,
        $auto_approve = false,
    ) {
        $model_table = ModelTable::where('name', $model)
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->first();

        if ($model_table) {
            $get_master_authorizations = ModelAuthorization::where('model_id', $model_table->id)
                ->when($division_id, function ($q) use ($division_id) {
                    $q->where(function ($q) use ($division_id) {
                        $q->whereHas('model_authorization_divisions', function ($q) use ($division_id) {
                            $q->where('division_id', $division_id);
                        })
                            ->orWhereDoesntHave('model_authorization_divisions');
                    });
                })
                ->when(!$division_id, function ($q) {
                    $q->whereDoesntHave('model_authorization_divisions');
                })
                ->when($branch_id, function ($q) use ($branch_id) {
                    $q->where(function ($q) use ($branch_id) {
                        $q->whereHas('model_authorization_branches', function ($q) use ($branch_id) {
                            $q->where('branch_id', $branch_id);
                        })
                            ->orWhereDoesntHave('model_authorization_branches');
                    });
                })
                ->when(!$branch_id, function ($q) {
                    $q->whereDoesntHave('model_authorization_branches');
                })
                ->when($model_table->need_to_check_amount, function ($q) use ($amount) {
                    $q->where('minimum_value', '<=', $amount);
                })
                ->orderBy('minimum_value', 'desc')
                ->get();

            // get higher amount group by level
            $higher_amounts = $get_master_authorizations->groupBy('level')
                ->map(function ($item, $key) {
                    return $item->max('minimum_value');
                });

            // get master authorization with higher amount
            $get_master_authorizations = $get_master_authorizations->filter(function ($item, $key) use ($higher_amounts) {
                return $item->minimum_value == $higher_amounts->get($item->level);
            });

            $init_authorizations = $get_master_authorizations
                ->sortBy('minimum_value')
                ->sortBy('level')

                ->values();

            if (count($init_authorizations) > 0) {
                $authorization = Authorization::where('model', $model)
                    ->where('model_id', $model_id)
                    ->first();

                if (!$authorization) {
                    $authorization = new Authorization();
                }

                $authorization->branch_id = $branch_id;
                $authorization->user_id = $user_id;
                $authorization->title = $title;
                $authorization->subtitle = $subtitle;
                $authorization->model = $model;
                $authorization->model_id = $model_id;
                $authorization->link = $link;
                $authorization->update_status_link = $update_status_link;
                $authorization->amount = $amount ?? 0;
                $authorization->save();

                AuthorizationDetail::where('authorization_id', $authorization->id)
                    ->delete();

                foreach ($init_authorizations as $key => $init_authorization) {
                    $authorization_detail = new AuthorizationDetail();
                    $authorization_detail->authorization_id = $authorization->id;
                    $authorization_detail->user_id = $init_authorization->user_id;
                    $authorization_detail->level = $init_authorization->level;
                    if ($auto_approve) {
                        $authorization_detail->status = 'approve';
                    } else {
                        $authorization_detail->status = $key == 0 ? 'pending' : 'draft';
                    }
                    $authorization_detail->note = '';
                    $authorization_detail->save();
                }

                if (!$auto_approve) {
                    $first_level = $authorization->details->first();
                    $get_same_levels = $authorization->details->where('level', $first_level->level)
                        ->where('id', '!=', $first_level->id);

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
                }
            } else {
                // return excecption must setting authorization
                throw new Exception('silahkan setting otorisasi untuk ' . Str::headline($model_table->alias) . ' terlebih dahulu');
            }
        } else {
            throw new Exception('silahkan setting otorisasi terlebih dahulu');
        }
    }

    public function info(
        $authorization_detail_id,
        $status,
        $note = '',
    ) {
        if (!$authorization_detail_id) {
            return [
                'status' => '',
                'note' => '',
                'is_last_level' => false,
            ];
        }
        $authorization_detail = AuthorizationDetail::find($authorization_detail_id);
        $authorization = $authorization_detail->authorization;

        if (!in_array($authorization_detail->status, ['approve', 'pending']) && $authorization_detail->status != $status) {
            throw new Exception('status tidak valid');
        }

        $max_level = $authorization->details->max('level');
        $is_last_level = $authorization_detail->level == $max_level || in_array($status, ['revert', 'void']);

        return [
            'status' => $status,
            'note' => $note,
            'authorization_note' => $authorization->note,
            'is_last_level' => $is_last_level ?? false,
        ];
    }

    public function authorize(
        $authorization_detail_id,
        $status,
        $note = '',
    ) {
        $authorization_detail = AuthorizationDetail::find($authorization_detail_id);

        if ($authorization_detail) {
            if (!in_array($authorization_detail->status, ['approve', 'pending']) && $authorization_detail->status != $status) {
                throw new Exception('status tidak valid');
            }
            $authorization_detail->status = $status;
            $authorization_detail->note = $note;
            $authorization_detail->status_at = now();
            $authorization_detail->save();

            $authorization = $authorization_detail->authorization;

            if ($status == "reject") {
                $get_same_levels = $authorization_detail->authorization->details
                    ->where('id', '!=', $authorization_detail->id);
            } else {
                $get_same_levels = $authorization_detail->authorization->details->where('level', $authorization_detail->level)
                    ->where('id', '!=', $authorization_detail->id);
            }

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
                    title: $authorization->title,
                    body: Str::upper($status) . ' - ' . $dotted_note,
                    reference_model: $authorization->model,
                    reference_id: $authorization->model_id,
                    branch_id: $authorization->branch_id,
                    user_id: $authorization->user_id,
                    roles: [],
                    permissions: [],
                    link: $authorization->link,
                );
            } else if ($status == "approve") {
                $max_level = $authorization->details->max('level');
                $is_last_level = $authorization_detail->level == $max_level;
                if ($is_last_level) {
                    $notification = new NotificationHelper();
                    $notification->send_notification(
                        title: $authorization->title,
                        body: Str::upper($status) . ' - ' . $dotted_note,
                        reference_model: $authorization->model,
                        reference_id: $authorization->model_id,
                        branch_id: $authorization->branch_id,
                        user_id: $authorization->user_id,
                        roles: [],
                        permissions: [],
                        link: $authorization->link,
                    );
                } else {
                    // get next level
                    $next_level = $authorization->details->where('level', '>', $authorization_detail->level)
                        ->where('status', 'draft')
                        ->sortBy('level')
                        ->first();

                    $next_level = $authorization->details->where('level', $next_level->level)
                        ->sortBy('level')
                        ->values();

                    foreach ($next_level as $key => $next) {
                        $next->status = 'pending';
                        $next->save();

                        $notification = new NotificationHelper();
                        $notification->send_notification(
                            branch_id: $authorization->branch_id,
                            user_id: $next->user_id,
                            roles: [],
                            permissions: [],
                            title: $authorization->title,
                            body: $authorization->subtitle,
                            reference_model: $authorization->model,
                            reference_id: $authorization->model_id,
                            link: $authorization->link,
                        );
                    }
                }
            } else if (in_array($status, ['revert', 'void'])) {
                AuthorizationDetail::where('authorization_id', $authorization->id)
                    ->update(
                        [
                            'status' => $status
                        ]
                    );
            }
        }
    }

    public function get_authorization_logs(
        $model,
        $model_id,
        $user_id,
    ) {
        $authorization = Authorization::where('model', $model)
            ->where('model_id', $model_id)
            ->first();

        $approved_count = 0;

        if ($authorization) {
            $authorization_details = $authorization->details;
            $max_level = $authorization_details->max('level');

            $authorization_details->each(function ($item, $key) use ($max_level) {
                $item->is_last_level = $item->level == $max_level;
            });

            $approved_count =  $authorization_details->where('status', 'approve')->count();
            $loged_user_authorization_detail = $authorization_details->where('user_id', $user_id)->first();
        }

        $data = [
            'authorization' => $authorization ?? null,
            'authorization_details' => $authorization_details ?? [],
            'loged_user_authorization_detail' => $loged_user_authorization_detail ?? null,
            'approved_count' => $approved_count,
        ];

        return $data;
    }
}
