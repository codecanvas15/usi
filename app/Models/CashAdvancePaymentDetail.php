<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashAdvancePaymentDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cash_advance_payment_id',
        'coa_id',
        'type',
        'note',
        'debit',
        'credit',
    ];

    protected $appends = [
        // 'local_debit',
        // 'local_credit',
        'cash_advance_return_total',
    ];

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
            'nama' => 'required|max:50|string|unique:banks,id,' . $id,
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

    public function cash_advance_payment()
    {
        return $this->belongsTo(CashAdvancePayment::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    /**
     * getCashAdvanceReturnAttribute
     *
     * @return
     */
    public function getCashAdvanceReturnTotalAttribute()
    {
        $data = CashAdvancedReturnDetail::select(['cash_advanced_return_details.*'])
            ->leftJoin('cash_advanced_returns', 'cash_advanced_returns.id', '=', 'cash_advanced_return_details.cash_advanced_return_id')

            ->where('cash_advanced_return_details.reference_id', $this->cash_advance_payment_id)
            ->where('cash_advanced_return_details.type', 'vendor')
            ->where('cash_advanced_return_details.reference_model', CashAdvancePayment::class)
            ->where('cash_advanced_returns.status', 'approve')

            ->sum('cash_advanced_return_details.amount_to_return');

        $incoming_payment = IncomingPaymentDetail::join('incoming_payments', 'incoming_payments.id', '=', 'incoming_payment_details.incoming_payment_id')
            ->where('incoming_payments.cash_advance_payment_id', $this->cash_advance_payment_id)
            ->whereNotIn('incoming_payments.status', ['rejected', 'void'])
            ->where('incoming_payment_details.type', 'cash_advance')
            ->sum('incoming_payment_details.credit');

        return $data + $incoming_payment;
    }

    public function getLocalDebitAttribute()
    {
        // return $this->debit * $this->cash_advance_payment->exchange_rate;
        // return $this->debit * $this->cash_advance_payment()->exchange_rate;
    }

    public function getLocalCreditAttribute()
    {
        // return $this->credit * $this->cash_advance_payment->exchange_rate;
        // return $this->credit * $this->cash_advance_payment()->exchange_rate;
    }
}
