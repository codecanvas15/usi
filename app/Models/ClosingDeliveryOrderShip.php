<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClosingDeliveryOrderShip extends Model
{
    use HasFactory;
    use ActivityLogHelper;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'branch_id',
        'delivery_order_id',
        'losses_coa_id',
        'item_id',
        'date',
        'code',
        'status',
        'note',
        'losses_quantity',
        'amount_sent',
        'amount_losses',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->date)) {
                $model->date = Carbon::now()->format('Y-m-d');
            }

            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(self::class, 'code', 'date', "CDO", branch_sort: $branch->sort ?? null, date: $model->date);
            }
        });
    }

    /**
     * Get the branch that owns the ClosingDeliveryOrderShip
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the deliveryOrder that owns the ClosingDeliveryOrderShip
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    /**
     * Get the lossesCoa that owns the ClosingDeliveryOrderShip
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lossesCoa(): BelongsTo
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    /**
     * Get the item that owns the ClosingDeliveryOrderShip
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }
}
