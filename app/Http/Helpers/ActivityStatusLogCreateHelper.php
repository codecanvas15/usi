<?php

use App\Models\ActivityStatusLog;

/**
 * create_activity_status_log
 *
 * @return void
 */
function create_activity_status_log_not_trait($model_reference, $reference_id, $message, $from_status, $to_status): void
{
    $new_data = new ActivityStatusLog();
    $new_data->loadModel([
        'reference_model' => $model_reference,
        'reference_id' => $reference_id,
        'message' => $message,
        'from_status' => $from_status,
        'to_status' => $to_status,
        'user_id' => auth()->id(),
    ]);

    try {
        $new_data->save();
    } catch (\Throwable $th) {
        throw $th;
    }

    return;
}
