<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Asset extends Model
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
        'item_receiving_report_detail_id',
        'item_id',
        'item_category_id',
        'division_id',
        'asset_category_id',
        'asset_document_type_id',
        'asset_coa_id',
        'acumulated_depreciation_coa_id',
        'depreciation_coa_id',
        'code',
        'asset_name',
        'purchase_date',
        'usage_date',
        'estimated_life',
        'value',
        'residual_value',
        'depreciated_value',
        'depreciation_value',
        'depreciation_percentage',
        'depreciation_end_date',
        'is_fleet',
        'initial_location',
        'note',
        'status',
        'vehicle_type',
    ];

    protected $appends = [
        'outstanding_value',
        'book_value',
        'acumulated_depreciation',
        'is_complete',
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
            'nama' => 'required|max:50|string|unique:banks,id,' . $id,
            'item_category_id' => 'required',
            'asset_name' => 'required',
            'purchase_date' => 'required',
            'usage_date' => 'required',
            'depreciation_method' => 'required',
            'asset_account' => 'required',
            'acumulated_depreciation_account' => 'required',
            'depreciation_account' => 'required',
            'depreciation_end_date' => 'required',
            'value' => 'required',
            'residual_value' => 'required',
            'estimated_life' => 'required',
            'depreciation_value' => 'required',
            'division_id' => 'required',
            'initial_location' => 'required',
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
                $model->code = generate_code(Asset::class, 'code', 'created_at', 'AKT', branch_sort: $branch->sort ?? null);
            }

            if (!$model->status) {
                $model->status = "pending";
            }
            if ($model->estimated_life && !$model->depreciation_value) {
                $model->status = "active";
                $model->depreciation_value = self::calculateDepreciationValue($model)['depreciation_value'];
                $model->depreciation_end_date = self::calculateDepreciationValue($model)['depreciation_end_date'];
            }
        });

        static::updating(function ($model) {
            $model->depreciation_value = self::calculateDepreciationValue($model)['depreciation_value'];
            $model->depreciation_end_date = self::calculateDepreciationValue($model)['depreciation_end_date'];
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

    public function dispositions()
    {
        return $this->hasMany(Disposition::class);
    }

    public function item_receiving_report_detail()
    {
        return $this->belongsTo(ItemReceivingReportDetail::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function item_category()
    {
        return $this->belongsTo(ItemCategory::class)->withTrashed();
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

    /**
     * Get the assetDocumentType that owns the Asset
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assetDocumentType(): BelongsTo
    {
        return $this->belongsTo(AssetDocumentType::class)->withTrashed();
    }

    /**
     * Get the assetCategory that owns the Asset
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assetCategory(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class)->withTrashed();
    }

    public function depreciation_coa()
    {
        return $this->belongsTo(Coa::class, 'depreciation_coa_id')->withTrashed();
    }

    public function depreciations()
    {
        return $this->hasMany(Depreciation::class);
    }

    public function fleet()
    {
        return $this->hasOne(Fleet::class);
    }

    /**
     * Get all of the assetDocuments for the Asset
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assetDocuments(): HasMany
    {
        return $this->hasMany(AssetDocument::class);
    }

    public function asset_category()
    {
        return $this->belongsTo(AssetCategory::class)->withTrashed();
    }

    private static function calculateDepreciationValue($model)
    {
        if ($model->depreciated_value == 0 || $model->estimated_life == 0) {
            return [
                'depreciation_value' => 0,
                'depreciation_end_date' => null
            ];
        }

        $depreciation_value = $model->depreciated_value / $model->estimated_life;
        $depreciation_end_date = Carbon::parse($model->usage_date)->addMonth($model->estimated_life);

        return [
            'depreciation_value' => $depreciation_value,
            'depreciation_end_date' => $depreciation_end_date
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
        $depreciation = Depreciation::where('asset_id', $this->id)->sum('amount');

        return $this->value - $depreciation;
    }

    public function getBookValueAttribute()
    {
        $depreciation = Depreciation::where('asset_id', $this->id)->sum('amount');

        return $this->value - $this->residual_value - $depreciation;
    }

    public function getAcumulatedDepreciationAttribute()
    {
        $depreciation = Depreciation::where('asset_id', $this->id)->sum('amount');

        return $depreciation;
    }

    public function getIsCompleteAttribute()
    {
        $required_params = [
            'branch_id',
            'item_category_id',
            'asset_category_id',
            'asset_name',
            'purchase_date',
            'usage_date',
            'asset_coa_id',
            'acumulated_depreciation_coa_id',
            'depreciation_coa_id',
            'value',
            'residual_value',
            'depreciated_value',
            'depreciation_percentage',
            'estimated_life',
            'depreciation_end_date',
            'division_id',
            'initial_location',
            'vehicle_type',
        ];

        foreach ($required_params as $param) {
            if (is_null($this->$param) || $this->$param === '') {
                return false;
            }
        }

        return true;
    }
}
