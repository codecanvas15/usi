<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayableCustomer extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fund_submission_customer_id',
        'account_payable_id',
        'invoice_parent_id',
        'coa_id',
        'exchange_rate',
        'outstanding_amount',
        'receive_amount',
        'receive_amount_foreign',
        'receive_amount_gap_foreign',
        'is_clearing',
        'exchange_rate_gap',
        'note',
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

    public function fund_submission_customer()
    {
        return $this->belongsTo(FundSubmissionCustomer::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function account_payable()
    {
        return $this->belongsTo(AccountPayable::class);
    }

    public function invoice_parent()
    {
        return $this->belongsTo(InvoiceParent::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
