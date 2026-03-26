<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierInvoicePayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supplier_invoice_model',
        'supplier_invoice_id',
        'item_receiving_report_id',
        'currency_id',
        'exchange_rate',
        'model',
        'reference_id',
        'date',
        'amount_to_pay',
        'pay_amount',
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

    public function supplier_invoice_model()
    {
        return $this->belongsTo($this->supplier_invoice_model::class, 'supplier_invoice_id');
    }

    public function supplier_invoice_model_ref()
    {
        return $this->belongsTo($this->supplier_invoice_model, 'supplier_invoice_id');
    }

    public function item_receiving_report()
    {
        return $this->belongsTo(ItemReceivingReport::class);
    }

    public function checkInvoice($model)
    {
        if ($model->supplier_invoice_id) {
            $total = SupplierInvoicePayment::where('supplier_invoice_model', $model->supplier_invoice_model)
                ->where('supplier_invoice_id', $model->supplier_invoice_id)
                ->sum('amount_to_pay');

            $paid = SupplierInvoicePayment::where('supplier_invoice_model', $model->supplier_invoice_model)
                ->where('supplier_invoice_id', $model->supplier_invoice_id)
                ->sum('pay_amount');

            $supplier_invoice = $model->supplier_invoice_model::find($model->supplier_invoice_id);

            $total = number_format($total, 2, '.', '');
            $paid = number_format($paid, 2, '.', '');

            if ($paid == 0) {
                $supplier_invoice->payment_status = "unpaid";
            } elseif ($paid > 0 && $paid < $total) {
                $supplier_invoice->payment_status = "partial-paid";
            } elseif ($paid == $total) {
                $supplier_invoice->payment_status = "paid";
            } else {
                $supplier_invoice->payment_status = "unpaid";
            }

            $supplier_invoice->save();

            SupplierInvoiceParent::where('reference_id', $model->supplier_invoice_id)
                ->where('model_reference', $model->supplier_invoice_model)
                ->update([
                    'payment_status' => $supplier_invoice->payment_status,
                ]);
        }
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
