<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use App\Http\Helpers\JournalHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockUsage extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLogHelper;

    protected $fillable = [
        'item_receiving_report_id',
        'project_id',
        'ware_house_id',
        'branch_id',
        'employee_id',
        'division_id',
        'fleet_id',
        'purchase_request_id',
        'coa_id',
        'fleet_type',
        'code',
        'date',
        'type',
        'note',
        'status',
        'created_by',
        'file',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = auth()->user()->id;

            if (is_null($model->date)) {
                $model->date = Carbon::now()->format('Y-m-d');
            }

            if (!checkAvailableDate($model->date)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }

            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(self::class, 'code', 'date', 'SUS', branch_sort: $branch->sort ?? null, date: $model->date);
            }

            if (is_null($model->status)) {
                $model->status = 'pending';
            }
        });

        static::updated(function ($model) {
            // if status changed
            if ($model->isDirty('status')) {
                // is status is approve create stock mutation
                if ($model->status == 'approve') {
                    // * create stock mutation
                    foreach ($model->stock_usage_details as $stock_usage_detail) {
                        $item = $stock_usage_detail->item;
                        $last_stock = StockMutation::where('item_id', $stock_usage_detail->item_id)
                            ->orderByDesc('ordering')
                            ->first();

                        $all_stock = StockMutation::where('item_id', $stock_usage_detail->item_id)
                            // ->where('price_id', $stock_usage_detail->price_id)
                            ->orderByDesc('ordering')
                            ->get();

                        $in = $all_stock->sum('in');
                        $out = $all_stock->sum('out');
                        $left = $in - $out;
                        $left_final = $left - $stock_usage_detail->quantity;
                        $price_unit = $item->getCurrentValue();
                        $subtotal = $price_unit * $stock_usage_detail->quantity;
                        $total = $last_stock?->total - $subtotal;
                        $last_value = $left_final != 0 ? ($total / $left_final) : 0;

                        $stock_mutation = StockMutation::create([
                            'ware_house_id' => $model->ware_house_id,
                            'branch_id' => $model->branch_id,
                            'item_id' => $stock_usage_detail->item_id,
                            'price_id' => $last_stock?->price_id,
                            'document_model' => StockUsageDetail::class,
                            'document_id' => $stock_usage_detail->id,
                            'document_code' => $model->code,
                            'date' => $model->date,
                            'type' => "stock usage",
                            'in' => 0,
                            'out' => $stock_usage_detail->quantity,
                            'note' => "stock usage {$stock_usage_detail->necessity}",
                            'price_unit' => $price_unit,  // stock mutasi paling akhir
                            'subtotal' => $subtotal, // stock mutasi paling akhir * qty
                            'total' => $total, // total paling akhir stock mutasi - subtotal
                            'value' => $last_value, // total / sisa stock
                        ]);

                        $stock_mutation->save();
                    }

                    // * create journal
                    $journal = new JournalHelpers("stock-usage", $model->id);
                    $journal->generate();

                    // update purchase request data
                    if ($model->purchase_request_id) {
                        foreach ($model->purchaseRequest->purchase_request_details as $purchase_request_detail) {
                            $stock_usage = $model->stock_usage_details->where('item_id', $purchase_request_detail->item_id)->first();

                            if ($stock_usage) {
                                $purchase_request_detail->update([
                                    'quantity_used' => $purchase_request_detail->quantity_used + $stock_usage->quantity,
                                ]);
                            }
                        }
                    }
                } else {
                    Journal::where('reference_model', StockUsage::class)
                        ->where('reference_id', $model->id)
                        ->delete();

                    StockMutation::where('document_model', StockUsageDetail::class)
                        ->whereIn('document_id', $model->stock_usage_details->pluck('id')->toArray())
                        ->delete();

                    if ($model->purchase_request_id) {
                        foreach ($model->purchaseRequest->purchase_request_details as $purchase_request_detail) {
                            $stock_usage = $model->stock_usage_details->where('item_id', $purchase_request_detail->item_id)->first();

                            if ($stock_usage) {
                                $purchase_request_detail->update([
                                    'quantity_used' => $purchase_request_detail->quantity_used - $stock_usage->quantity,
                                ]);
                            }
                        }
                    }
                }
            }
        });

        static::deleted(function ($model) {
            Journal::where('reference_model', StockUsage::class)
                ->where('reference_id', $model->id)
                ->delete();

            StockMutation::where('document_model', StockUsageDetail::class)
                ->whereIn('document_id', $model->stock_usage_details->pluck('id')->toArray())
                ->delete();
        });
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', StockUsage::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', StockUsage::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    /**
     * checkAvailableDate for generated journal
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }

    /**
     * Get the project that owns the StockUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    /**
     * Get the ware_house that owns the StockUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ware_house(): BelongsTo
    {
        return $this->belongsTo(WareHouse::class)->withTrashed();
    }

    /**
     * Get the branch that owns the StockUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the employee that owns the StockUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    /**
     * Get the division that owns the StockUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class)->withTrashed();
    }

    /**
     * Get the fleet that owns the StockUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fleet(): BelongsTo
    {
        return $this->belongsTo(Fleet::class)->withTrashed();
    }

    /**
     * Get the purchaseRequest that owns the StockUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class)->withTrashed();
    }

    /**
     * Get the coa that owns the StockUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    /**
     * Get all of the stock_usage_details for the StockUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stock_usage_details(): HasMany
    {
        return $this->hasMany(StockUsageDetail::class);
    }

    public function stock_usage_purchase_requests()
    {
        return $this->hasMany(StockUsagePurchaseRequest::class);
    }
}
