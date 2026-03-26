<?php

namespace App\Models;

use App\Http\Helpers\JournalHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseReturn extends Model
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
        'vendor_id',
        'item_receiving_report_id',
        'project_id',
        'ware_house_id',
        'currency_id',
        'exchange_rate',
        'reference',
        'date',
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
        $activity_logs = ActivityLog::where('subject_type', PurchaseReturn::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);

        $status_logs = ActivityStatusLog::where('reference_model', PurchaseReturn::class)
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
            'vendor_id' => 'required',
            'ware_house_id' => 'required',
            'item_receiving_report_id' => 'required',
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

            if (!checkAvailableDate($model->date)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }
        });

        static::created(function ($model) {});

        static::updated(function ($model) {
            if ($model->getOriginal('status') != $model->status) {
                if ($model->status == 'approve') {

                    $supplier_invoice = SupplierInvoice::whereHas('detail', function ($d) use ($model) {
                        $d->whereHas('item_receiving_report', function ($i) use ($model) {
                            $i->where('id', $model->item_receiving_report_id);
                        });
                    })
                        ->where('status', 'approve')
                        ->first();

                    foreach ($model->purchase_return_details as $key => $purchase_return_detail) {
                        if ($purchase_return_detail->item->item_category->item_type->nama == 'purchase item') {
                            // STOCK MUTATION
                            $stock_mutation = new StockMutation();
                            $stock_mutation->ware_house_id = $model->ware_house_id;
                            $stock_mutation->branch_id = $model->ware_house->branch_id;
                            $stock_mutation->item_id = $purchase_return_detail->item_id;
                            $stock_mutation->document_model = get_class($purchase_return_detail);
                            $stock_mutation->document_id = $purchase_return_detail->id;
                            $stock_mutation->document_code = $model->code;
                            $stock_mutation->date = $model->date;
                            $stock_mutation->vendor_model = Vendor::class;
                            $stock_mutation->vendor_id = $model->vendor_id;
                            $stock_mutation->type = 'purchase return';
                            $stock_mutation->out = $purchase_return_detail->qty;
                            $stock_mutation->note = 'Purchase Return';
                            $stock_mutation->save();
                        }
                    }

                    // item_receiving_report detail count (sum each quantity of item_receiving_report_detail)
                    $item_receiving_report = ItemReceivingReport::find($model->item_receiving_report_id);
                    $itemReceivingReportQuantity = $item_receiving_report->item_receiving_report_details->sum('jumlah_diterima');

                    // purchase_return detail count (sum each quantity of purchase_return_detail)
                    $purchaseReturnQuantity = $model->purchase_return_details->sum('qty');

                    // set status item_receiving_report to return-all if all item is returned
                    if ($itemReceivingReportQuantity == $purchaseReturnQuantity) {
                        $item_receiving_report->status = 'return-all';
                        $item_receiving_report->save();
                    }

                    $journal = new JournalHelpers('purchase-return', $model->id);
                    $journal->generate();
                } else {
                    $journals = Journal::where('reference_id', $model->id)
                        ->where('reference_model', PurchaseReturn::class)
                        ->get();

                    foreach ($journals as $journal) {
                        $journal->delete();
                        $journal->journal_details->each(function ($detail) {
                            $detail->delete();
                        });
                    }

                    StockMutation::where('document_model', PurchaseReturnDetail::class)
                        ->whereIn('document_id', $model->purchase_return_details->pluck('id'))
                        ->delete();

                    ItemReceivingReportTax::where('reference_parent_model', PurchaseReturn::class)
                        ->where('reference_parent_id', $model->id)
                        ->delete();


                    //  get the supplier invoice and item receiving report
                    $item_receiving_report = ItemReceivingReport::find($model->item_receiving_report_id);
                    $supplier_invoice = SupplierInvoice::whereHas('detail', function ($d) use ($model) {
                        $d->whereHas('item_receiving_report', function ($i) use ($model) {
                            $i->where('id', $model->item_receiving_report_id);
                        });
                    })
                        ->whereNotIn('status', ['reject', "void"])
                        ->first();

                    // set status item_receiving_report to approve if supplier_invoice is not null
                    if ($supplier_invoice) {
                        $item_receiving_report->status = 'done';
                    } else {
                        $item_receiving_report->status = 'approve';
                    }
                }
            }
        });

        static::deleted(function ($model) {
            $journals = Journal::where('reference_id', $model->id)
                ->where('reference_model', PurchaseReturn::class)
                ->get();

            foreach ($journals as $journal) {
                $journal->delete();
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
            }

            StockMutation::where('document_model', PurchaseReturnDetail::class)
                ->whereIn('document_id', $model->purchase_return_details->pluck('id'))
                ->delete();

            ItemReceivingReportTax::where('reference_parent_model', PurchaseReturn::class)
                ->where('reference_parent_id', $model->id)
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

    public function item_receiving_report()
    {
        return $this->belongsTo(ItemReceivingReport::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
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

    public function vendor()
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    public function purchase_return_details()
    {
        return $this->hasMany(PurchaseReturnDetail::class);
    }

    public function purchase_return_histories()
    {
        return $this->hasMany(PurchaseReturnHistory::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
