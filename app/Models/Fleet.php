<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fleet extends Model
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
        'project_id',
        'asset_id',
        'fleet_id',
        'name',
        'type',
        'merk',
        'quantity',
        'year',
        'longitude',
        'latitude',

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
            'branch_id' => 'nullable|integer',
            'name' => 'required|max:255|string',
            'type' => 'required|max:32|string',
            'merk' => 'required|max:60|string',
            'quantity' => 'required|integer',
            'year' => 'required',
            'longitude' => 'nullable|max:24|string',
            'latitude' => 'nullable|max:24|string',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (auth()->user()) {
                $model->branch_id = get_current_branch_id();
            }
        });
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

    /**
     * get branch
     *
     * @return void
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the asset that owns the Fleet
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the item that owns the Fleet
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    /**
     * Get the vechicle_fleet associated with the Fleet
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vechicle_fleet(): HasOne
    {
        return $this->hasOne(VechicleFleet::class);
    }

    /**
     * Get the marine_fleet associated with the Fleet
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function marine_fleet(): HasOne
    {
        return $this->hasOne(MarineFleet::class);
    }

    /**
     * Get all of the fleetDocuments for the Fleet
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fleetDocuments(): HasMany
    {
        return $this->hasMany(FleetDocument::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
