<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LaborApplication extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'employee_id',
        'labor_demand_detail_id',
        'code',
        'date',
        'name',
        'email',
        'address',
        'address_domicil',
        'phone',
        'date_of_birth',
        'place_of_birth',
        'religion',
        'gender',
        'marital_status',
        'identity_card_number',
        'status',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->date)) {
                $model->date = Carbon::now()->format('Y-m-d');
            }

            if (is_null($model->branch_id)) {
                $model->branch_id = auth()->user()->branch_id;
            }

            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(self::class, 'code', 'date', "LA", branch_sort: $branch->sort ?? null, date: $model->date);
            }

            if (is_null($model->status)) {
                $model->status = 'pending';
            }

            if (is_null($model->religion)) {
                $model->religion = '';
            }
        });

        static::updated(function ($model) {
            if ($model->isDirty('status')) {
                if ($model->status == 'approve') {
                    // $labor_demand = $model->laborDemandDetail;

                    // $labor_demand->quantity_complete += 1;
                    // $labor_demand->save();

                    // * create data employee
                    // // $employee = new Employee();
                    // // $employee->fill([
                    // //     'branch_id' => $model->branch_id,
                    // //     'division_id' => $labor_demand->labor_demand->division_id,
                    // //     'employment_status_id',
                    // //     'education_id' => $labor_demand->education_id,
                    // //     'degree_id' => $labor_demand->degree_id,
                    // //     'email' => $model->email,
                    // //     'name' => $model->name,
                    // //     // 'NIK',
                    // //     'alamat' => $model->address,
                    // //     'alamat_domisili' => $model->address_domicil,
                    // //     'nomor_telepone' => $model->phone,
                    // //     'tempat_lahir' => $model->place_of_birth,
                    // //     'tanggal_lahir' => $model->date_of_birth,
                    // //     'jenis_kelamin' => $model->gender,
                    // //     'status_pernikahan' => $model->marital_status,
                    // //     'nomor_ktp' => $model->identity_card_number,
                    // //     // 'foto_id',
                    // //     'join_date' => \Carbon\Carbon::now()->format('Y-m-d'),
                    // //     // 'end_date',
                    // //     // 'npwp',
                    // //     // 'nomor_bpjs',
                    // //     'start_contract' => \Carbon\Carbon::now()->format('Y-m-d'),
                    // //     // 'end_contract',
                    // //     // 'bpjs_dues',
                    // //     // 'deposit_asset_employee',
                    // //     // 'deposit_asset_company',
                    // //     // 'exit_interview',
                    // //     'employee_status' => 'non_aktif',
                    // //     'leave' => 12,
                    // //     'position_id' => $labor_demand->position_id,
                    // // ]);

                    // // $employee->save();

                    // // * create employee emergency contact
                    // $data_emergencies = [];
                    // foreach ($model->laborApplicationEmergencyContacts as $laborApplicationEmergencyContact) {
                    //     $data_emergencies[] = [
                    //         'nama' => $laborApplicationEmergencyContact->name,
                    //         'hubungan' => $laborApplicationEmergencyContact->relationship,
                    //         'nomor_telepon' => $laborApplicationEmergencyContact->phone,
                    //         'alamat' => $laborApplicationEmergencyContact->address,
                    //     ];
                    // }

                    // $employee->employee_emergency_contacts()->createMany($data_emergencies);
                }
            }
        });
    }

    /**
     * init activity logs
     *
     * @return LogsOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['created_at', 'updated_at'])
            ->setDescriptionForEvent(fn (string $eventName) => "This data has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    /**
     * getCheckAvailableDateAttribute for generated Labor
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', self::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', self::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    /**
     * get property LaborDemandCodeAttribute
     *
     * @return string
     */
    public function getLaborDemandCodeAttribute()
    {
        return $this->laborDemandDetail->labor_demand->code;
    }

    /**
     * Get the branch that owns the LaborApplication
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the employee that owns the LaborApplication
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    /**
     * Get the laborDemandDetail that owns the LaborApplication
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function laborDemandDetail(): BelongsTo
    {
        return $this->belongsTo(LaborDemandDetail::class);
    }

    /**
     * Get all of the laborApplicationDocuments for the LaborApplication
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function laborApplicationDocuments(): HasMany
    {
        return $this->hasMany(LaborApplicationDocument::class);
    }

    /**
     * Get all of the laborApplicationEmergencyContacts for the LaborApplication
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function laborApplicationEmergencyContacts(): HasMany
    {
        return $this->hasMany(LaborApplicationEmergencyContact::class);
    }

    public function application(): HasOne
    {
        return $this->hasOne(Application::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
