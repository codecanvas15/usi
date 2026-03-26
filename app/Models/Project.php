<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
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
        'project_id',
        'branch_id',
        'date',
        'code',
        'name',
        'description',
        'status',
        'location',
        'budget',
        'date_end',
        'file',
    ];

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
     * validation rules
     *
     * @param  string  $method
     * @param  int  $id
     * @return array
     */
    public static function rules($method = 'create', $id = null)
    {
        $validate = [
            'date' => 'required|date',
            'name' => 'required|max:50|string',
            'description' => 'nullable|max:65535|string',
            'location' => 'nullable|max:100|string',
            'budget' => 'nullable',
            'file' => 'nullable|file|mimes:pdf,docx,doc,xlsx|max:6000'
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

    public function fleets()
    {
        return $this->hasMany(Fleet::class);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->code == null) {
                $last_data = Project::where('branch_id', $model->branch_id)
                    ->whereMonth('created_at', date('m'))
                    ->orderBy('id', 'desc')
                    ->first();

                if ($last_data) {
                    $model->code = generate_code_transaction("PRJ", $last_data->code, branch_sort: $model->branch->sort);
                } else {
                    $model->code = generate_code_transaction("PRJ", "0000-0000-0000-0000", branch_sort: $model->branch->sort);
                }
            }

            $model->status = 'pending';
            $model->date_end = null;
        });

        static::updating(function ($model) {
            if ($model->getOriginal('status') != $model->status and $model->status == 'done') {
                $model->date_end = Carbon::today()->format('Y-m-d');
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
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', Project::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', Project::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
