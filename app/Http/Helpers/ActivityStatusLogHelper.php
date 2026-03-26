<?php

namespace App\Http\Helpers;

use App\Models\ActivityStatusLog;

trait ActivityStatusLogHelper
{
    /**
     * create_activity_status_log
     *
     * @return void
     */
    public function create_activity_status_log($model_reference, $reference_id, $message, $from_status, $to_status): void
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

    /**
     * get activity status log
     *
     * @param  string  $model
     * @param  int  $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActivityStatusLog(string $model, int $id)
    {
        $model = 'App\\Models\\' . $model;
        $model = new $model();
        $model = $model->find($id);
        $logs = $model->activity()->get();
        return $logs;
    }
}
