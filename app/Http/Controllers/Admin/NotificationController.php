<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function data(Request $request)
    {
        try {
            $notifications = DB::table('notifications')
                ->where('notifiable_id', auth()->user()->id)
                ->limit(10)
                ->offset($request->offset)
                ->orderBy('read_at', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($notifications as $key => $notification) {
                $notification->data = json_decode($notification->data);
                $notification->created_at = Carbon::parse($notification->created_at)->format('d F Y H:i');
            }

            return $this->ResponseJsonData($notifications);
        } catch (\Throwable $th) {
            return $this->ResponseJsonData($th->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $notification = auth()->user()->notifications
                ->where('id', $id)
                ->first();

            $data = $notification->data;
            $notification->markAsRead();

            return redirect($data['links']);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function counter()
    {
        try {
            $notification = auth()->user()->unreadNotifications
                ->count();

            return $this->ResponseJsonData($notification);
        } catch (\Throwable $th) {
            return $this->ResponseJsonData([]);
        }
    }

    public function clear()
    {
        try {
            DB::table('notifications')
                ->delete();

            return $this->ResponseJsonData('success');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
