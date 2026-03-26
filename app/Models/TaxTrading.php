<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxTrading extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
        'coa_sale_id',
        'coa_purchase_id',
        'type',
        'is_show_percent',
    ];

    protected $appends = ['tax_name_without_percent'];

    /**
     * Get the coa_sale that owns the TaxTrading
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa_sale(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_sale_id')->withTrashed();
    }

    /**
     * Get the coa_purchase that owns the TaxTrading
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa_purchase(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_purchase_id')->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function getTaxNameWithoutPercentAttribute()
    {
        $explodes = explode(' ', $this->name);

        $final_string = '';
        foreach ($explodes as $key => $explode) {
            if (strpos($explode, '%') == false) {
                if ($key != 0) {
                    $final_string .= ' ';
                }
                $final_string .= $explode;
            }
        }

        return trim($final_string);
    }
}
