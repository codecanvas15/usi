<?php

namespace App\Models;

use App\Http\Helpers\JournalHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SupplierInvoiceGeneral extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vendor_id',
        'currency_id',
        'branch_id',
        'project_id',
        'code',
        'reference',
        'exchange_rate',
        'term_of_payment',
        'top_days',
        'top_due_date',
        'date',
        'debit',
        'credit',
        'status',
        'payment_status',
        'approved_by',
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
        $activity_logs = ActivityLog::where('subject_type', SupplierInvoiceGeneral::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', SupplierInvoiceGeneral::class)
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
            'vendor_id' => 'required',
            'branch_id' => 'required',
            'currency_id' => 'required',
            'date' => 'required|date',
            'exchange_rate' => 'required',
            'term_of_payment' => 'required',
            'top_days' => 'required_if:term_of_payment,by days',
            'top_due_date' => 'required_if:term_of_payment,by days',
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

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(SupplierInvoiceGeneral::class, 'code', 'date', 'SIG', branch_sort: $branch->sort ?? null, date: $model->date);
            }

            if (!checkAvailableDate($model->date)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }
        });

        static::created(function ($model) {
            SupplierInvoiceParent::updateOrCreate(
                [
                    'model_reference' => SupplierInvoiceGeneral::class,
                    'reference_id' => $model->id,
                ],
                [
                    'model_reference' => SupplierInvoiceGeneral::class,
                    'reference_id' => $model->id,
                    'branch_id' => $model->branch_id,
                    'vendor_id' => $model->vendor_id,
                    'currency_id' => $model->currency_id,
                    'exchange_rate' => $model->exchange_rate,
                    'date' => $model->date,
                    'due_date' => $model->top_due_date,
                    'type' => 'general',
                    'code' => $model->code,
                    'reference' => $model->reference,
                    'total' => $model->debit,
                    'status' => $model->status,
                    'payment_status' => $model->payment_status,
                ]
            );
        });

        static::updated(function ($model) {
            if ($model->getOriginal('status') != $model->status) {
                if ($model->status == 'approve') {
                    $journal = new JournalHelpers('supplier-invoice-general', $model->id);
                    $journal->generate();

                    //CREATE OR UPDATE INVOICE PAYMENT
                    SupplierInvoicePayment::updateOrCreate([
                        'supplier_invoice_model' => SupplierInvoiceGeneral::class,
                        'supplier_invoice_id' => $model->id,
                    ], [
                        'supplier_invoice_model' => SupplierInvoiceGeneral::class,
                        'supplier_invoice_id' => $model->id,
                        'currency_id' => $model->currency_id,
                        'exchange_rate' => $model->exchange_rate,
                        'model' => SupplierInvoiceGeneral::class,
                        'reference_id' => $model->id,
                        'date' => $model->date,
                        'amount_to_pay' => $model->debit,
                        'pay_amount' => 0,
                        'note' => "Invoice - $model->code",
                    ]);
                } else {
                    $journals = Journal::where('reference_id', $model->id)
                        ->where('reference_model', SupplierInvoiceGeneral::class)->get();
                    foreach ($journals as $journal) {
                        $journal->delete();
                        $journal->journal_details->each(function ($detail) {
                            $detail->delete();
                        });
                    }
                }
            }

            SupplierInvoiceParent::updateOrCreate(
                [
                    'model_reference' => SupplierInvoiceGeneral::class,
                    'reference_id' => $model->id,
                ],
                [
                    'model_reference' => SupplierInvoiceGeneral::class,
                    'reference_id' => $model->id,
                    'branch_id' => $model->branch_id,
                    'vendor_id' => $model->vendor_id,
                    'currency_id' => $model->currency_id,
                    'exchange_rate' => $model->exchange_rate,
                    'date' => $model->date,
                    'due_date' => $model->top_due_date,
                    'type' => 'general',
                    'code' => $model->code,
                    'reference' => $model->reference,
                    'total' => $model->debit,
                    'status' => $model->status,
                    'payment_status' => $model->payment_status,
                ]
            );
        });

        static::deleted(function ($model) {
            $journals = Journal::where('reference_id', $model->id)
                ->where('reference_model', SupplierInvoiceGeneral::class)->get();
            foreach ($journals as $journal) {
                $journal->delete();
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
            }

            //DELETE INVOICE PAYMENT
            SupplierInvoicePayment::where('supplier_invoice_model', SupplierInvoiceGeneral::class)
                ->where('supplier_invoice_id', $model->id)
                ->delete();

            // DELETE PARENT
            SupplierInvoiceParent::where('model_reference', SupplierInvoiceGeneral::class)
                ->where('reference_id', $model->id)->delete();
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

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function detail()
    {
        return $this->hasMany(SupplierInvoiceGeneralDetail::class);
    }

    public function supplier_invoice_payment()
    {
        return SupplierInvoicePayment::where('supplier_invoice_model', SupplierInvoiceGeneral::class)
            ->with('item_receiving_report')
            ->where('supplier_invoice_id', $this->id)
            ->get();
    }
}
