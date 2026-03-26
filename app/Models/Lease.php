<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Lease extends Model
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
        'branch_id',
        'division_id',
        'item_receiving_report_detail_id',
        'item_id',
        'asset_coa_id',
        'acumulated_depreciation_coa_id',
        'depreciation_coa_id',
        'code',
        'lease_name',
        'date',
        'from_date',
        'to_date',
        'month_duration',
        'value',
        'depreciation_value',
        'note',
        'status',
    ];

    protected $appends = [
        'outstanding_value',
        'book_value',
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
            'branch_id' => 'required|exists:branches,id',
            'lease_name' => 'required',
            'date' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'asset_coa_id' => 'required',
            'acumulated_depreciation_coa_id' => 'required',
            'depreciation_coa_id' => 'required',
            'value' => 'required',
            'division_id' => 'required',
            'note' => 'required',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->branch_id == null) {
                $model->branch_id = Auth::user()->branch_id;
            }

            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(Lease::class, 'code', 'created_at', 'BDM', branch_sort: $branch->sort ?? null);
            }

            if (!$model->depreciation_value) {
                $model->depreciation_value = floatFormat(self::calculateDepreciationValue($model)['depreciation_value']);
                $model->month_duration = self::calculateDepreciationValue($model)['month_duration'];
            }
        });

        static::updating(function ($model) {
            $model->depreciation_value = floatFormat(self::calculateDepreciationValue($model)['depreciation_value']);
            $model->month_duration = self::calculateDepreciationValue($model)['month_duration'];
        });

        static::deleted(function ($model) {});
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

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function division()
    {
        return $this->belongsTo(Division::class)->withTrashed();
    }

    public function asset_coa()
    {
        return $this->belongsTo(Coa::class, 'asset_coa_id')->withTrashed();
    }

    public function acumulated_depreciation_coa()
    {
        return $this->belongsTo(Coa::class, 'acumulated_depreciation_coa_id')->withTrashed();
    }

    public function depreciation_coa()
    {
        return $this->belongsTo(Coa::class, 'depreciation_coa_id')->withTrashed();
    }

    public function amortizations()
    {
        return $this->hasMany(Amortization::class);
    }

    private static function calculateDepreciationValue($model)
    {
        $month_duration = Carbon::parse($model->from_date)->startOfMonth()->diffInMonths(Carbon::parse($model->to_date)->endOfMonth());

        if ($month_duration == 0) {
            $month_duration = 1;
        }

        if ($model->value == 0 || $month_duration == 0) {
            return [
                'depreciation_value' => 0,
                'month_duration' => null
            ];
        }
        $depreciation_value = $model->value / $month_duration;

        return [
            'depreciation_value' => $depreciation_value,
            'month_duration' => $month_duration
        ];
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function getOutstandingValueAttribute()
    {
        $amortization = Amortization::where('lease_id', $this->id)->sum('amount');

        return $this->value - $amortization;
    }

    public function getBookValueAttribute()
    {
        $amortization = Amortization::where('lease_id', $this->id)->sum('amount');

        return $this->value - $amortization;
    }

    /**
     * Get all of the leaseDocuments for the Lease
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leaseDocuments(): HasMany
    {
        return $this->hasMany(LeaseDocument::class);
    }

    public function item_receiving_report_detail()
    {
        return $this->belongsTo(ItemReceivingReportDetail::class, 'item_receiving_report_detail_id');
    }
}
