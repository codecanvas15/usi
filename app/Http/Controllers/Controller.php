<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ControllerHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Employee;
use App\Models\Authorization;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use ControllerHelper;

    public function notifToRole($branch_id, $role, $title, $body, $type, $content, $link, $is_auth = false, $model = "", $subject_id = null, $data_branch_id = null)
    {
        $query = Employee::where('position_id', $role)->where('branch_id', $branch_id);

        $user_id = clone $query;
        $user_id = $query->pluck('id')->toArray();

        if ($is_auth) {
            $this->createAuthorization($query->get(), $type, $body, $model, $subject_id, $title, $data_branch_id);
        }

        return response()->json(json_encode("success"));
    }

    public function createAuthorization($users, $type, $subtitle, $model, $subject_id, $title = '', $data_branch_id = null)
    {
        foreach ($users as $key => $user) {
            $authorization = new Authorization();
            $authorization->branch_id = auth()->user()->branch_id ?? $data_branch_id ?? null;
            $authorization->user_id = $user->id;
            $authorization->type = $type;
            $authorization->title = $title;
            $authorization->subtitle = $subtitle;
            $authorization->model = $model;
            $authorization->subject_id = $subject_id;
            $authorization->status = 'pending';
            $authorization->save();
        }
    }
}
