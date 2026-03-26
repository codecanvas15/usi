<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Coa extends Model
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
        'name',
        'account_code',
        'account_type',
        'account_category',
        'parent_id',
        'can_have_children',
        'is_parent',
        'branch_id',
        'currency_id',
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
            'name' => 'required|string|max:255',
            'account_code' => 'nullable|string|min:4|max:4|unique:coas,account_code,' . $id,
            'account_type' => 'nullable|string|max:60',
            'parent_id' => 'nullable|exists:coas,id',
            'can_have_children' => 'nullable|boolean',
            'currency_id' => 'nullable|exists:currencies,id',
            // 'branch_id' => '',
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

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->parent) {
                // * account code
                $child = $model->parent->child_latest;

                if ($child) {
                    $code = $child?->account_code;

                    if (strlen($code) == 4) {
                        $final_code = "$code" . "01";
                    } else {
                        $getTwoLastNumer = (int) substr($code, -2);
                        $getTwoLastNumer += 1;

                        if ($getTwoLastNumer < 10) {
                            $final_code = substr($code, 0, -2) . "0" . $getTwoLastNumer;
                        } else {
                            $final_code = substr($code, 0, -2) . $getTwoLastNumer;
                        }
                    }
                } else {
                    $final_code = $model->parent->account_code . "01";
                }

                $model->account_code = $final_code;
                $model->account_type = $model->parent->account_type;
            }

            // * set branch id
            if (Auth::check() && Auth::user()->branch_id != null && $model->branch_id == null) {
                $model->branch_id = Auth::user()->branch_id;
            }
        });

        static::created(function ($model) {
            $model->can_have_children = true;
            if ($model->parent_id == null) {
                $model->is_parent = true;
            } else {
                $model->is_parent = false;
                // * update parent
                $parent = $model->parent;
                if ($parent) {
                    if (!$parent->is_parent) {
                        $parent->is_parent = true;
                        $parent->save();
                    }
                }
            }

            if (in_array($model->account_category, [
                'pasiva',
                'equity',
                'revenue',
            ])) {
                $model->normal_balance = 'credit';
            } else {
                $model->normal_balance = 'debit';
            }

            $model->save();
        });

        static::updating(function ($model) {
            if (in_array($model->account_category, [
                'pasiva',
                'equity',
                'revenue',
            ])) {
                $model->normal_balance = 'credit';
            } else {
                $model->normal_balance = 'debit';
            }

            if ($model->journal_details()->exists()) {
                $model->can_have_children = false;
            } else {
                $model->can_have_children = true;
            }

            if ($model->parent_id == null) {
                $model->is_parent = true;
            } else {
                $model->is_parent = false;
                // * update parent
                $parent = $model->parent;
                if ($parent) {
                    if (!$parent->is_parent) {
                        $parent->is_parent = true;
                        $parent->save();
                    }
                }
            }
        });

        static::deleting(function ($model) {
            // * if parent delete all childs
            if ($model->is_parent || $model->childs) {
                foreach ($model->childs as $key => $value) {
                    $value->delete();
                }
            }

            // * check if child and parent dont have any child anymore
            if ($model->parent) {
                $parent = $model->parent;
                if ($parent) {
                    if ($parent->childs->count() == 0) {
                        $parent->is_parent = false;
                        $parent->save();
                    }
                }
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Coa::class, 'parent_id');
    }

    public function childs()
    {
        return $this->hasMany(Coa::class, 'parent_id');
    }

    public function child_latest()
    {
        return $this->hasOne(Coa::class, 'parent_id')->latestOfMany();
    }

    public function bank_internal()
    {
        return $this->hasOne(BankInternal::class);
    }

    public function getLastChildAttribute()
    {
        return Coa::where('parent_id', $this->parent_id)
            ->where('id', '!=', $this->id)
            ->orderByDesc('id')
            ->first();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);
        // $array['childs'] = $this->childs;

        return $array;
    }

    public function journal_details()
    {
        return $this->hasMany(JournalDetail::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
