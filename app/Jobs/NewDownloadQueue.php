<?php

namespace App\Jobs;

use App\Http\Traits\NotificationTrait;
use App\Models\Download;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewDownloadQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, NotificationTrait;

    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $id, public $className, public $methodName, public $params = [])
    {
        $this->user = auth()->id();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        ini_set('memory_limit', -1);
        set_time_limit(0);

        $download = Download::findOrFail($this->id);

        DB::beginTransaction();
        try {

            $this->send_notification(
                branch_id: $params['branch_id'] ?? null,
                user_id: $this->user,
                roles: [],
                permissions: [],
                title: 'Download anda sudah selesai',
                body: 'Lihat di menu download untuk melihatnya',
                reference_model: Download::class,
                reference_id: $download->id,
                link: route('download-report.index'),
            );

            $class = new $this->className;
            $path = $class->{$this->methodName}(...$this->params);

            $download->update([
                'status' => 'done',
                'done_at' => now(),
                'path' => $path,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $download->update([
                'status' => 'failed',
                'done_at' => now(),
            ]);
        }
    }
}
