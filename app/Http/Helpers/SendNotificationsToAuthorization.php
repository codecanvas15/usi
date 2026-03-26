<?php

namespace App\Http\Helpers;

use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Notification;

class SendNotificationsToAuthorization
{
    /**
     * private array notifications
     * 
     * @var array
     */
    private array $notifications = [];

    /**
     * Instantiate a new SendNotificationsToAuthorization instance.
     */
    public function __construct(
        private string $modelReference,
        private string $type,
        private int|float $amount,
        private string $title,
        private string $body,
        private string $reference_model,
        private string $reference_id,
        private string $link,
    ) {
        // body
    }

    /**
     * Make notification to authorization users 
     */
    public function send()
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        // * find model authorization for this current model reference and aliase
        $AuthorizationModel = \App\Models\ModelTable::where('name', $this->modelReference)
            ->where('alias', $this->modelReference)
            ->first();

        // * if not found, find model authorization for this current model reference
        if (!$AuthorizationModel) {
            throw new \Exception("Data Otorisasi untuk {$this->modelReference} tidak ditemukan.");
        }

        // * find authorization details
        $AuthorizationDetails = \App\Models\ModelAuthorization::where('authorization_id', $AuthorizationModel->id)
            ->when($AuthorizationModel->need_to_check_amount, function ($q) {
                $q->where('minimum_value', '<=', $this->amount);
            })
            ->orderBy('level')
            ->get();

        // * if count is 0, throw exception
        if ($AuthorizationDetails->count() == 0) {
            throw new \Exception("Data Otorisasi untuk {$this->modelReference} tidak ditemukan atau belum lengkap.");
        }

        // * make users data and tokens.
        $users = \App\Models\User::whereIn('id', $AuthorizationDetails->pluck('user_id')->toArray())->get();
        $tokens = $users
            ->whereNotNull('device_token')
            ->pluck('device_token')->toArray();

        $serverKey = 'AAAA6jlderk:APA91bFq_-c_MJBTcjuTPv6gE0Z0OooTQp55bgCk2tbBvYsFuHXGhEzeACjnpSbpPTpD6Pl2AyardFP6H7fWJV6tS4lXVqYq4ly1r9IGFBD4edLMqjJFnEGgYfefl-d-O7VIx4pKukgF';
        if (count($tokens) > 0) {
            $data = [
                "registration_ids" => $tokens,
                "notification" => [
                    "title" => $this->title,
                    "body" => $this->body,
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

        $this->notifications[] = [
            'title' => $this->title,
            'text' => $this->body,
            'links' => $this->link,
            'reference_model' => $this->reference_model,
            'reference_id' => $this->reference_id,
        ];

        try {
            Notification::send($users, new SendNotification($this->notifications));
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json($this->notifications);
    }
}
