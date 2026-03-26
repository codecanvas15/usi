<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;

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
            'division_id' => 'nullable|exists:divisions,id',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, [
                'username' => 'required|max:30|unique:users,username,NULL,id,deleted_at,NULL',
                'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
                'password' => 'required|string|min:8',
                'confirm_password' => 'required|string|min:8|same:password',
            ]);
        } else {
            $validate = array_merge($validate, [
                'username' => 'required|max:30|unique:users,username,' . $id . ',id,deleted_at,NULL',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id . ',id,deleted_at,NULL',
                'password' => 'nullable|string|min:8',
                'confirm_password' => 'nullable|string|min:8|same:password',
            ]);
        }

        return $validate;
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
                if ($model->branch_id == null) {
                    $model->branch_id = Auth::user()->branch_id;
                }
            }
        });

        static::updated(function ($model) {
            if ($model->employee && $model->employee->division_id != $model->division_id) {
                $model->employee->division_id = $model->division_id;
                $model->employee->save();
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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_type',
        'username',
        'name',
        'email',
        // 'password',
        'branch_id',
        'division_id',
        'project_id',
        'device_token',
        'employee_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * get property UserVendor
     *
     * @return
     */
    public function getUserVendorAttribute()
    {
        $vendor_user = VendorUser::where('user_id', $this->id)->first();
        if ($vendor_user) {
            return $vendor_user->vendor;
        }
        return null;
    }

    /**
     * Get the branch that owns the data.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the division that owns the data.
     */
    public function division()
    {
        return $this->belongsTo(Division::class)->withTrashed();
    }

    public function project()
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    public function nonEmployee()
    {
        return $this->hasOne(NonEmployee::class);
    }

    /**
     * The vendor that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function vendor(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'vendor_users');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
