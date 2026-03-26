<?php

namespace App\Models;

use App\Http\Helpers\JournalHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InvoiceReturn extends Model
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
        'customer_id',
        'type',
        'reference_model',
        'reference_id',
        'project_id',
        'ware_house_id',
        'currency_id',
        'exchange_rate',
        'reference',
        'date',
        'hpp_total',
        'subtotal',
        'tax_total',
        'total',
        'status',
        'reject_reason',
        'created_by',
        'tax_number',
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
        $activity_logs = ActivityLog::where('subject_type', InvoiceReturn::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);

        $status_logs = ActivityStatusLog::where('reference_model', InvoiceReturn::class)
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
            'customer_id' => 'required',
            'ware_house_id' => 'required',
            'type' => 'required',
            'reference_model' => 'required',
            'reference_id' => 'required',
            'date' => 'required',
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
                    $journal = new JournalHelpers('invoice-return', $model->id);
                    $journal->generate();


                    if ($model->type == "general") {
                        $invoice = InvoiceGeneral::whereHas('invoice_general_details', function ($q) use ($model) {
                            $q->whereHas('delivery_order_general_detail', function ($d) use ($model) {
                                $d->whereHas('delivery_order_general', function ($i) use ($model) {
                                    $i->where('id', $model->reference_id);
                                });
                            });
                        })
                            ->first();
                    } else {
                        $invoice = InvoiceTrading::whereHas('invoice_trading_details', function ($d) use ($model) {
                            $d->whereHas('delivery_order', function ($i) use ($model) {
                                $i->where('id', $model->reference_id);
                            });
                        })->first();
                    }

                    foreach ($model->invoice_return_details as $key => $invoice_return_detail) {
                        // STOCK MUTATION
                        $stock_mutation = new StockMutation();
                        $stock_mutation->ware_house_id = $model->ware_house_id;
                        $stock_mutation->branch_id = $model->ware_house->branch_id;
                        $stock_mutation->item_id = $invoice_return_detail->item_id;
                        $stock_mutation->document_model = get_class($invoice_return_detail);
                        $stock_mutation->document_id = $invoice_return_detail->id;
                        $stock_mutation->date = $model->date;
                        $stock_mutation->document_code = $model->code;
                        $stock_mutation->type = 'invoice return';
                        $stock_mutation->in = $invoice_return_detail->qty;
                        $stock_mutation->price_unit = $invoice_return_detail->hpp;
                        $stock_mutation->subtotal = $invoice_return_detail->hpp_total;
                        $stock_mutation->note = 'Invoice Return';
                        $stock_mutation->save();
                    }
                } else {
                    $journals = Journal::where('reference_id', $model->id)
                        ->where('reference_model', InvoiceReturn::class)
                        ->get();

                    foreach ($journals as $journal) {
                        $journal->delete();
                        $journal->journal_details->each(function ($detail) {
                            $detail->delete();
                        });
                    }

                    StockMutation::where('document_model', InvoiceReturnDetail::class)
                        ->whereIn('document_id', $model->invoice_return_details->pluck('id'))
                        ->delete();

                    InvoiceTax::where('reference_parent_model', InvoiceReturn::class)
                        ->where('reference_parent_id', $model->id)
                        ->delete();
                }
            }
        });

        static::deleted(function ($model) {
            $journals = Journal::where('reference_id', $model->id)
                ->where('reference_model', InvoiceReturn::class)
                ->get();

            foreach ($journals as $journal) {
                $journal->delete();
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
            }

            StockMutation::where('document_model', InvoiceReturnDetail::class)
                ->whereIn('document_id', $model->invoice_return_details->pluck('id'))
                ->delete();

            InvoiceTax::where('reference_parent_model', InvoiceReturn::class)
                ->where('reference_parent_id', $model->id)
                ->delete();
        });
    }

    public function reference_data()
    {
        return $this->belongsTo($this->reference_model, 'reference_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    public function ware_house()
    {
        return $this->belongsTo(WareHouse::class)->withTrashed();
    }

    public function project()
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function invoice_return_details()
    {
        return $this->hasMany(InvoiceReturnDetail::class);
    }


    public function invoice_return_histories()
    {
        return $this->hasMany(InvoiceReturnHistory::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    /**
     * getCheckAvailableDateAttribute for generated invoice return
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute()
    {
        return checkAvailableDate($this->date);
    }
}
