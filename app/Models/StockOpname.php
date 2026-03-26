<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class StockOpname extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLogHelper;

    protected $fillable = [
        'ware_house_id',
        'branch_id',
        'coa_id',
        'code',
        'date',
        'created_by',
        'less_difference',
        'more_difference',
        'status',
        'owner_status',
        'manager_marketing_status',
    ];

    /**
     * init activity logs
     *
     * @return LogsOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['created_at', 'updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "This data has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', StockOpname::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', StockOpname::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::updated(function ($model) {
            if ($model->isDirty('status')  && $model->status == 'approve') {
                // * create journal
                $journal = new \App\Http\Helpers\JournalHelpers("stock-opname", $model->id);
                $journal->generate();
            }

            if ($model->isDirty('status')  && in_array($model->status, ['revert', 'void', 'reject'])) {
                StockMutation::where('document_model', StockOpname::class)
                    ->where('document_id', $model->id)
                    ->delete();

                Journal::where('reference_id', $model->id)
                    ->where('reference_model', StockOpname::class)
                    ->delete();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'ware_house_id');
    }

    public function details()
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    public function getCheckAvailableDateAttribute()
    {
        return checkAvailableDate($this->date);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }
}
