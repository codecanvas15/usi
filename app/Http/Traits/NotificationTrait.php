<?php

namespace App\Http\Traits;

use App\Models\Authorization;
use App\Models\User;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

trait NotificationTrait
{
    public function send_notification(
        $branch_id = null,
        $user_id = null,
        $roles = [],
        $permissions = [],
        $title,
        $body,
        $reference_model = '',
        $reference_id = '',
        $link = ''
    ) {
        try {
            $url = 'https://fcm.googleapis.com/fcm/send';
            $users = User::where('id', '!=', null);

            if ($user_id) {
                $users->where('id', $user_id);
            }
            if ($branch_id) {
                $users->where('branch_id', $branch_id);
            }
            $users->whereHas('roles', function ($r) use ($roles, $permissions) {
                if (count($roles) > 0) {
                    $r->whereIn('name', $roles);
                }
                if (count($permissions) > 0) {
                    $r->whereHas('permissions', function ($r) use ($permissions) {
                        $r->whereIn('name', $permissions);
                    });
                }
            });

            $tokens = clone $users->whereNotNull('device_token');
            $tokens = $users->pluck('device_token')->toArray();

            $serverKey = 'AAAA8GE_0ZM:APA91bGG-fomXI7cJQebRktaqO61D5o1ZD-_M5zzfMyLuxwvFqcXXipEeHzBMQ_nRI6n6768yXTahCxt5p9DimBR3cWWd1nA6KWq0jHByVTxjcdFdm6EtN2R-gUptQKji8TtWVkytOFv';
            if (count($tokens) > 0) {
                $data = [
                    "registration_ids" => $tokens,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                        "sound" => public_path('audio/notification.wav'),
                        "click_action" => $link,
                    ]
                ];
                $encodedData = json_encode($data);

                $headers = [
                    'Authorization:key=' . $serverKey,
                    'Content-Type:application/json',
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
                $result = curl_exec($ch);
                curl_close($ch);
                // Execute post
                $result = curl_exec($ch);

                if ($result === false) {
                    die('Curl failed: ' . curl_error($ch));
                }
                // Close connection
                curl_close($ch);
                // FCM response
            }

            $notification = [
                'title' => $title,
                'text' => $body,
                'links' => $link,
                'reference_model' => $reference_model,
                'reference_id' => $reference_id,
                'branch_id' => $branch_id,
            ];

            $users = $users->get();
            Notification::send($users, new SendNotification($notification));

            return response()->json($notification);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function update_authorization ($reference_model,$reference_id, $status = 'approved')
    {
        $auth = auth()->id();
        $authorization = Authorization::latest()->where('reference', $reference_model)
            ->where('reference_id', $reference_id)->first();

            if($authorization) {
                $authorization->status = $status;
                $authorization->notification = 0;
                
                if ($status === 'approved') {
                    $authorization->approver_user = $auth;
                }
                $authorization->save();
                DB::commit();
            }
    }

    public function generate_authorization(
        $reference_model,
        $reference_id,
        $user_id,
        $branch_id,
        $link,
        $title = null,
        $sub_title = null,
        $other = null,
    ) {
        $user = User::find($user_id);
        $authorization = new Authorization();
        $authorization->title = $title;
        $authorization->subtitle = $sub_title;
        $authorization->reference = $reference_model;
        $authorization->reference_id = $reference_id;
        $authorization->user_id = $user_id;
        $authorization->branch_id = $branch_id;
        $authorization->other = $other;

        try {
            $authorization->save();

            $this->send_notification(
                branch_id: $branch_id,
                user_id: $user_id,
                roles: $user->roles?->pluck('name')->toArray(),
                permissions:[],
                title:$title,
                body:$sub_title,
                reference_model:$reference_model,
                reference_id: $reference_id,
                link: $link,
            );
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}