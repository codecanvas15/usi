<?php

namespace App\Models;

use App\Http\Helpers\JournalHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TaxReconciliation extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'coa_id',
        'code',
        'date',
        'tax_period',
        'from_date',
        'to_date',
        'total_in',
        'total_out',
        'gap',
        'status',
        'reject_reason',
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
     * getCheckAvailableDateAttribute for generated journal
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', TaxReconciliation::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', TaxReconciliation::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    /**
     * validation rules
     *
     * @param  string  $method
     * @param  int  $id
     * @return array
     */
    public static function rules($method = 'create', $id = null)
    {
        $validate = [
            'branch_id' => 'required',
            'date' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'total_in' => 'required',
            'total_out' => 'required',
            'gap' => 'required',
            'status' => 'required',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

    /**
     * set attrbutes model
     *
     * @param $request
     */
    public function loadModel($request)
    {
        foreach ($this->fillable as $key_field) {
            foreach ($request as $key_request => $value) {
                if ($key_field == $key_request) {
                    $this->setAttribute($key_field, $value);
                }
            }
        }
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->status = "pending";
            $model->created_by = Auth::user()->id;
        });

        static::created(function ($model) {});

        static::updated(function ($model) {
            if ($model->getOriginal('status') != $model->status) {
                if ($model->status == 'approve') {
                    $journal = new JournalHelpers('tax-reconciliation', $model->id);
                    $journal->generate();
                } else {
                    $journals = Journal::where('reference_id', $model->id)
                        ->where('reference_model', TaxReconciliation::class)
                        ->get();

                    foreach ($journals as $journal) {
                        $journal->delete();
                        $journal->journal_details->each(function ($detail) {
                            $detail->delete();
                        });
                    }

                    ItemReceivingReportTax::where('reference_model', TaxReconciliation::class)
                        ->where('reference_id', $model->id)
                        ->delete();
                }
            }
        });

        static::deleted(function ($model) {
            $journals = Journal::where('reference_id', $model->id)
                ->where('reference_model', TaxReconciliation::class)
                ->get();

            foreach ($journals as $journal) {
                $journal->delete();
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
            }

            ItemReceivingReportTax::where('reference_model', TaxReconciliation::class)
                ->where('reference_id', $model->id)
                ->delete();
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class);
    }

    public function tax_reconciliation_details()
    {
        return $this->hasMany(TaxReconciliationDetail::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
