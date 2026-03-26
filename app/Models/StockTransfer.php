<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class StockTransfer extends Model
{
    use HasFactory;
    use ActivityLogHelper;

    protected $fillable = [
        'branch_id',
        'item_id',
        'price_id',
        'code',
        'date',
        'from',
        'to',
        'qty',
        'note',
        'status',
        'receiving_status',
        'created_by',
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
        $activity_logs = ActivityLog::where('subject_type', StockTransfer::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', StockTransfer::class)
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
        static::created(function ($model) {
            if (!checkAvailableDate($model->date)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }
        });

        static::updated(function ($model) {
            if ($model->status != $model->getOriginal('status')) {
                if (in_array($model->status, ['revert'])) {
                    StockMutation::where('type', 'stock transfer')
                        ->where('document_id', $model->id)
                        ->delete();
                }
            }
        });

        static::deleting(function ($model) {
            StockMutation::where('type', 'stock transfer')
                ->where('document_id', $model->id)
                ->delete();
        });
    }

    /**
     * getCheckAvailableDateAttribute for generated journal
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function details()
    {
        return $this->hasMany(StockTransferDetail::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(WareHouse::class, 'from', 'id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(WareHouse::class, 'to', 'id');
    }
}
