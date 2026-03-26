<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoicePayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_model',
        'invoice_id',
        'currency_id',
        'exchange_rate',
        'model',
        'reference_id',
        'date',
        'amount_to_receive',
        'receive_amount',
        'note',
    ];

    protected static function booted()
    {
        self::created(function ($model) {
            $model->checkInvoice($model);
        });

        self::updated(function ($model) {
            $model->checkInvoice($model);
        });

        self::deleted(function ($model) {
            $model->checkInvoice($model);
        });
    }

    public function reference_model()
    {
        return $this->belongsTo($this->reference_model::class, 'reference_id');
    }

    public function reference_model_ref()
    {
        return $this->belongsTo($this->model, 'reference_id');
    }

    public function invoice_model()
    {
        return $this->belongsTo($this->invoice_model::class, 'invoice_id');
    }

    public function checkInvoice($model)
    {
        if ($model->invoice_id) {
            $total = InvoicePayment::where('invoice_model', $model->invoice_model)
                ->where('invoice_id', $model->invoice_id)
                ->sum('amount_to_receive');

            $paid = InvoicePayment::where('invoice_model', $model->invoice_model)
                ->where('invoice_id', $model->invoice_id)
                ->sum('receive_amount');

            $invoice = $model->invoice_model::find($model->invoice_id);

            $total = number_format($total, 2, '.', '');
            $paid = number_format($paid, 2, '.', '');

            if ($paid == 0) {
                $invoice->payment_status = "unpaid";
            } elseif ($paid > 0 && $paid < $total) {
                $invoice->payment_status = "partial-paid";
            } elseif ($paid == $total) {
                $invoice->payment_status = "paid";
            } else {
                $invoice->payment_status = "unpaid";
            }

            $invoice->save();
        }
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
