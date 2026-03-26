<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxReconciliationDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tax_reconciliation_id',
        'journal_detail_id',
        'tax_reconciliation_balance_id',
        'type',
        'out',
        'in',
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
            'tax_reconciliation_id' => 'required',
            'type' => 'required',
            'out' => 'required',
            'in' => 'required',
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

    public function reference()
    {
        return $this->belongsTo($this->reference_model, 'reference_id');
    }

    public function reference_parent()
    {
        return $this->belongsTo($this->reference_parent_model, 'reference_parent_id');
    }

    public function tax_reconciliation()
    {
        return $this->belongsTo(TaxReconciliation::class);
    }

    public function tax_reconciliation_balance()
    {
        return $this->belongsTo(TaxReconciliationBalance::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
