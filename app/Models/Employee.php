<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Employee extends Model
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
        'employment_status_id',
        'education_id',
        'degree_id',
        'email',
        'name',
        'NIK',
        'no_ktp',
        'alamat',
        'alamat_domisili',
        'nomor_telepone',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'non_taxable_income_id',
        'join_date',
        'end_date',
        'npwp',
        'start_contract',
        'end_contract',
        'employee_status',
        'leave',
        'position_id',
        'staff_type', // staff, crew, driver
        'religion',
        'weight',
        'height',
        'blood_type',
        'hobby',
        'marriage_date',
        'vehicle',
        'parents_phone_number',
        'file',
        'reason_for_choosing_the_major',
        'thesis_topic',
        'reason_for_not_passing',
        'parents_residence_address',
        'postal_code',
        'house_phone',
        'occupied_house',
        'vehicle_ownership',
        'vihicle_details',
    ];

    protected $casts = [
        'current_residential_address' => 'array',
        'parents_residence_address' => 'array',
        'vehicle_details' => 'array',
    ];

    protected $append = [
        'vehicle_brand',
        'vehicle_year',
        'current_postal_code',
        'parents_postal_code',
        'current_address',
        'parents_address',
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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->NIK)) {
                $date = date('Ymd');
                $str = Str::random(4);
                $model->NIK = "USI-$date$str";
            }

            if (Auth::check()) {
                if (!$model->branch_id) {
                    $model->branch_id = Auth::user()->branch_id;
                }
            }
        });

        static::updated(function ($model) {
            if ($model->user && $model->user->division_id != $model->division_id) {
                $model->user->division_id = $model->division_id;
                $model->user->save();
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

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all of the employee_banks for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employee_banks(): HasMany
    {
        return $this->hasMany(EmployeeBank::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class)->withTrashed();
    }

    /**
     * Get the education that owns the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class)->withTrashed();
    }

    /**
     * Get the degree that owns the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class)->withTrashed();
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

    /**
     * Get the employeeHealthHistory associated with the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employeeHealthHistory(): HasOne
    {
        return $this->hasOne(EmployeeHealthHistory::class);
    }

    public function employeeDocument()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    /**
     * Get the employment_status that owns the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employment_status(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class)->withTrashed();
    }

    public function roleHistory()
    {
        return $this->hasMany(EmployeeRoleHistory::class);
    }

    public function branchHistory()
    {
        return $this->hasMany(EmployeeBranchHistory::class);
    }

    /**
     * Get all of the employee_emergency_contacts for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employee_emergency_contacts(): HasMany
    {
        return $this->hasMany(EmployeeEmergencyContact::class);
    }

    /**
     * Get all of the employeeFamilyTrees for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeFamilyTrees(): HasMany
    {
        return $this->hasMany(EmployeeFamilyTree::class);
    }

    /**
     * Get all of the employeeFormalEducations for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeFormalEducations(): HasMany
    {
        return $this->hasMany(EmployeeFormalEducation::class);
    }

    /**
     * Get all of the employeeInformalEducations for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeInformalEducations(): HasMany
    {
        return $this->hasMany(EmployeeInformalEducation::class);
    }

    /**
     * Get all of the employeeLanguages for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeLanguages(): HasMany
    {
        return $this->hasMany(EmployeeLanguage::class);
    }

    /**
     * Get all of the employeeSpecialEducations for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeSpecialEducations(): HasMany
    {
        return $this->hasMany(EmployeeSpecialEducation::class);
    }

    /**
     * Get all of the employeeWorkExperiences for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeWorkExperiences(): HasMany
    {
        return $this->hasMany(EmployeeWorkExperience::class);
    }

    /**
     * Get all of the employeeInterests for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeInterests(): HasMany
    {
        return $this->hasMany(EmployeeInterest::class);
    }

    /**
     * Get all of the employeeReferences for the employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeReferences(): HasMany
    {
        return $this->hasMany(EmployeeReference::class);
    }

    /**
     * Get all of the employeeInsiders for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeInsiders(): HasMany
    {
        return $this->hasMany(EmployeeInsider::class);
    }

    /**
     * Get all of the employeePsikotests for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeePsikotests(): HasMany
    {
        return $this->hasMany(EmployeePsikotest::class);
    }

    /**
     * Get all of the employeeOrganizations for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeOrganizations(): HasMany
    {
        return $this->hasMany(EmployeeOrganization::class);
    }

    /**
     * Get all of the employeeStrengthAndWeaknesses for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeStrengthAndWeaknesses(): HasMany
    {
        return $this->hasMany(EmployeeStrengthWeaknesses::class);
    }

    public function contractExtension($status = 'approve'): HasMany
    {
        if ($status == 'all') {
            return $this->hasMany(ContractExtension::class);
        }
        return $this->hasMany(ContractExtension::class)->where('contract_extensions.status', $status);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function non_taxable_income()
    {
        return $this->belongsTo(NonTaxableIncome::class);
    }

    public function getVehicleBrandAttribute()
    {
        if ($this->vehicle_details) {
            $vehicle = is_array($this->vehicle_details) ? $this->vehicle_details : json_decode($this->vehicle_details);
            return isset($vehicle['brand']) ? $vehicle['brand'] : null;
        }
        return null;
    }

    public function getVehicleYearAttribute()
    {
        if ($this->vehicle_details) {
            $vehicle = is_array($this->vehicle_details) ? $this->vehicle_details : json_decode($this->vehicle_details);
            return isset($vehicle['year']) ? $vehicle['year'] : null;
        }
        return null;
    }

    public function getCurrentAddressAttribute()
    {
        if ($this->current_residential_address) {
            $address = is_array($this->current_residential_address) ? $this->current_residential_address : json_decode($this->current_residential_address);
            return isset($address['address']) ? $address['address'] : null;
        }
        return null;
    }

    public function getParentsAddressAttribute()
    {
        if ($this->parents_residence_address) {
            $address = is_array($this->parents_residence_address) ? $this->parents_residence_address : json_decode($this->parents_residence_address);
            return isset($address['address']) ? $address['address'] : null;
        }
        return null;
    }

    public function getParentsPostalCodeAttribute()
    {
        if ($this->parents_residence_address) {
            $address = is_array($this->parents_residence_address) ? $this->parents_residence_address : json_decode($this->parents_residence_address);
            return isset($address['postal_code']) ? $address['postal_code'] : null;
        }
        return null;
    }

    public function getCurrentPostalCodeAttribute()
    {
        if ($this->current_residential_address) {
            $address = is_array($this->current_residential_address) ? $this->current_residential_address : json_decode($this->current_residential_address);
            return isset($address['postal_code']) ? $address['postal_code'] : null;
        }
        return null;
    }
}
