<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class BankInternal extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLogHelper;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'type',
        'nama_bank',
        'branch_name',
        'no_rekening',
        'on_behalf_of',
        'branch_id',
        'coa_id'
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
            'type' => 'required',
            'code' => 'required',
            'nama_bank' => 'required',
            'no_rekening' => 'required|max:50|string|unique:bank_internals,no_rekening,' . $id,
            'on_behalf_of' => 'required|max:50|string',
            'coa_id' => 'required|exists:coas,id|unique:bank_internals,coa_id,' . $id,
        ];

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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->branch_id = Auth::user()->branch_id;
            }
        });
    }

    /**
     * Get the coa that owns the BankInternal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    /**
     * Get all of the bank_internal_details for the BankInternal
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bank_internal_details(): HasMany
    {
        return $this->hasMany(BankInternalDetail::class);
    }

    public function detail()
    {
        return $this->hasMany(BankInternalDetail::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
