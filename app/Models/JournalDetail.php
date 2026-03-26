<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JournalDetail extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'currency_id',
        'exchange_rate',
        'journal_id',
        'coa_id',
        'debit',
        'credit',
        'debit_exchanged',
        'credit_exchanged',
        'remark',
        'item_receiving_report_coa_id',
        'reference_id',
        'reference_model',
        'timestamp',
        'ordering',
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
     * validation rules
     *
     * @param  string  $method
     * @param  int  $id
     * @return array
     */
    public static function rules($method = 'create', $id = null)
    {
        $validate = [
            'journal_id' => '',
            'coa_id' => '',
            'debit' => '',
            'credit' => '',
            'remark' => '',
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

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::creating(function ($model) {
            if (!$model->currency_id) {
                $model->currency_id = $model->journal->currency_id;
            }
            if (!$model->exchange_rate) {
                $model->exchange_rate = $model->journal->exchange_rate;
            }
            $model->credit_exchanged = $model->credit * $model->exchange_rate;
            $model->debit_exchanged = $model->debit * $model->exchange_rate;
            $model->ordering = generate_journal_order($model->journal->date);
            $model->timestamp = $model->journal->date . ' ' . Carbon::now()->format('H:i:s');
        });

        self::created(function ($model) {
            $coa = $model->coa;
            $coa->updated_at = Carbon::now();
            $coa->save();
        });

        self::updating(function ($model) {
            if (!$model->currency_id) {
                $model->currency_id = $model->journal->currency_id;
            }
            if (!$model->exchange_rate) {
                $model->exchange_rate = $model->journal->exchange_rate;
            }
            $model->credit_exchanged = $model->credit * $model->exchange_rate;
            $model->debit_exchanged = $model->debit * $model->exchange_rate;
        });

        self::deleted(function ($model) {
            $coa = $model->coa;
            $coa->updated_at = Carbon::now();
            $coa->save();
        });
    }

    /**
     * Get the journal that owns the JournalDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    /**
     * Get the item_receiving_report_coa that owns the data.
     */
    public function item_receiving_report_coa()
    {
        return $this->belongsTo(ItemReceivingReportCoa::class);
    }

    /**
     * Get the reference that owns the JournalDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo($this->reference_model, 'reference_id');
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

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
