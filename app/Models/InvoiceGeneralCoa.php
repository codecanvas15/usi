<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceGeneralCoa extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_general_id',
        'coa_id',
        'type',
        'reference_id',
        'reference_model',
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
        $validate = [];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

    /**
     * set attributes model
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

    /**
     * Get the invoice_general that owns the InvoiceGeneralCoa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice_general(): BelongsTo
    {
        return $this->belongsTo(InvoiceGeneral::class);
    }

    /**
     * Get the coa that owns the InvoiceGeneralCoa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    /**
     * Get the reference that owns the InvoiceGeneralCoa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo($this->reference_model, 'reference_id');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
