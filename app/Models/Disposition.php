<?php

namespace App\Models;

use App\Http\Helpers\JournalHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Disposition extends Model
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
        'code',
        'branch_id',
        'asset_id',
        'gain_loss_coa_id',
        'selling_coa_id',
        'is_selling_asset',
        'tax_id',
        'tax_number',
        'date',
        'last_journal_date',
        'last_book_value',
        'selling_price',
        'tax_value',
        'tax_amount',
        'total',
        'location',
        'note',
        'status',
        'reject_reason',
        'customer_id',
        'bank_internal_id',
        'due',
        'due_date',
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
        $activity_logs = ActivityLog::where('subject_type', Disposition::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', Disposition::class)
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
            'branch_id' => ['required'],
            'asset_id' => ['required'],
            'gain_loss_coa_id' => ['required'],
            'is_selling_asset' => ['required'],
            'date' => ['required'],
            'last_journal_date' => ['required'],
            'last_book_value' => ['required'],
            'location' => ['required'],
            'note' => ['required'],
            'tax_id' => ['nullable', 'exists:taxes,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'bank_internal_id' => ['required', 'exists:bank_internals,id'],
            'due' => ['required'],
            'due_date' => ['required'],
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
                    if ($model->tax && ($model->tax->type ?? '' == 'ppn')) {
                        $invoice_tax = InvoiceTax::where('reference_id', $model->id)
                            ->where('reference_model', Disposition::class)
                            ->first();

                        if (!$invoice_tax) {
                            $invoice_tax = new InvoiceTax();
                        }
                        $invoice_tax->loadModel(
                            [
                                'reference_model' => Disposition::class,
                                'reference_id' => $model->id,
                                'reference_parent_model' => Disposition::class,
                                'reference_parent_id' => $model->id,
                                'date' => Carbon::parse($model->date),
                                'customer_id' => NULL,
                                'tax_id' => $model->tax_id,
                                'dpp' => $model->selling_price,
                                'value' => $model->tax_value,
                                'amount' => $model->tax_amount,
                            ]
                        );
                        $invoice_tax->save();
                    }

                    $journal = new JournalHelpers('disposition', $model->id);
                    $journal->generate();

                    $model->asset->update(['status' => 'inactive']);

                    Depreciation::updateOrCreate(
                        [
                            'model' => Disposition::class,
                            'model_id' => $model->id,
                        ],
                        [
                            'branch_id' => $model->branch_id,
                            'asset_id' => $model->asset_id,
                            'date' => $model->date,
                            'from_date' => $model->date,
                            'to_date' => $model->date,
                            'amount' => $model->asset->book_value,
                            'note' => 'Disposisi aset',
                        ]
                    );
                } else {
                    $journals = Journal::where('reference_id', $model->id)
                        ->where('reference_model', Disposition::class)
                        ->get();

                    foreach ($journals as $journal) {
                        $journal->delete();
                        $journal->journal_details->each(function ($detail) {
                            $detail->delete();
                        });
                    }

                    Depreciation::where('model', Disposition::class)
                        ->where('model_id', $model->id)
                        ->delete();

                    InvoiceTax::where('reference_id', $model->id)
                        ->where('reference_model', Disposition::class)
                        ->delete();

                    $model->asset->update(['status' => 'active']);
                }
            }
        });

        static::deleted(function ($model) {
            $journals = Journal::where('reference_id', $model->id)
                ->where('reference_model', Disposition::class)
                ->get();

            foreach ($journals as $journal) {
                $journal->delete();
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
            }

            InvoiceTax::where('reference_id', $model->id)
                ->where('reference_model', Disposition::class)
                ->delete();

            Depreciation::where('model', Disposition::class)
                ->where('model_id', $model->id)
                ->delete();

            $model->asset->update(['status' => 'active']);
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function gain_loss_coa()
    {
        return $this->belongsTo(Coa::class, 'gain_loss_coa_id');
    }

    public function selling_coa()
    {
        return $this->belongsTo(Coa::class, 'selling_coa_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function bank_internal()
    {
        return $this->belongsTo(BankInternal::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
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
}
