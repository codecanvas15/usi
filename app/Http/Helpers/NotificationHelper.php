<?php

namespace App\Http\Helpers;

use App\Models\User;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Notification;

class NotificationHelper
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
        $link = '',
    ) {
        if (!is_array($user_id)) {
            $user_id = [$user_id];
        }

        try {
            $url = 'https://fcm.googleapis.com/fcm/send';
            $users = User::where('id', '!=', null)
                ->when($branch_id && count($user_id) == 0, function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                })
                ->when($user_id, function ($q) use ($user_id) {
                    $q->whereIn('id', $user_id);
                })
                ->when(count($roles) > 0, function ($q) use ($roles) {
                    $q->whereHas('roles', function ($r) use ($roles) {
                        $r->whereIn('name', $roles);
                    });
                })->when(count($permissions) > 0, function ($q) use ($permissions) {
                    $q->whereHas('permissions', function ($r) use ($permissions) {
                        $r->whereIn('name', $permissions);
                    });
                })->get();

            $tokens = $users
                ->whereNotNull('device_token')
                ->pluck('device_token')->toArray();

            $serverKey = 'AAAA6jlderk:APA91bFq_-c_MJBTcjuTPv6gE0Z0OooTQp55bgCk2tbBvYsFuHXGhEzeACjnpSbpPTpD6Pl2AyardFP6H7fWJV6tS4lXVqYq4ly1r9IGFBD4edLMqjJFnEGgYfefl-d-O7VIx4pKukgF';
            if (count($tokens) > 0) {
                $data = [
                    "registration_ids" => $tokens,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
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

            Notification::send($users, new SendNotification($notification));

            return response()->json($notification);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
