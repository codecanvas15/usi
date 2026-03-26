<?php

namespace App\Models;

use App\Http\Controllers\Admin\FinanceReportController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClosingPeriod extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'to_date',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */

    public static function rules($method = 'create', $id = null)
    {
        $validate = [
            'to_date' => 'required',
            'status' => 'required',
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
            ->setDescriptionForEvent(fn(string $eventName) => "This data has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', ClosingPeriod::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', ClosingPeriod::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

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
     * Get all of the closingPeriodCurrencies for the ClosingPeriod
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function closingPeriodCurrencies(): HasMany
    {
        return $this->hasMany(ClosingPeriodCurrency::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth()->user()->id;

            if ($model->status == 'close') {
                // * check if closing period is balance
                $request = new Request([
                    'period' => Carbon::parse($model->to_date)->format('m-Y'),
                ]);

                // * get data from finance report
                $get_data = app('App\Http\Controllers\Admin\FinanceReportNeracaController')->get_data($request);

                // * get data from neraca
                $financeReport = new FinanceReportController();
                $data['aktiva'] = $financeReport->neraca($get_data['aktiva']);
                $data['pasiva'] = $financeReport->neraca($get_data['kewajiban_dan_ekuitas']);

                // * check if neraca is balance
                $sumActiva = number_format(collect($data['aktiva'])->where('is_parent', false)->sum('balance'), 2, '.', '');
                $sumPasiva = number_format(collect($data['pasiva'])->where('is_parent', false)->sum('balance'), 2, '.', '');

                if ($sumActiva != $sumPasiva) {
                    throw new \Exception('Neraca tidak balance');
                }
            }
        });

        static::updating(function ($model) {
            $model->updated_by = Auth()->user()->id;

            if ($model->wasChanged('status') && $model->status == 'close') {
                // * check if closing period is balance
                $request = new Request([
                    'period' => Carbon::parse($model->to_date)->format('m-Y'),
                ]);

                // * get data from finance report
                $get_data = app('App\Http\Controllers\Admin\FinanceReportNeracaController')->get_data($request);

                // * get data from neraca
                $financeReport = new FinanceReportController();
                $data['aktiva'] = $financeReport->neraca($get_data['aktiva']);
                $data['pasiva'] = $financeReport->neraca($get_data['kewajiban_dan_ekuitas']);


                dd($data);
                // * check if neraca is balance
                $sumActiva = number_format(collect($data['aktiva'])->where('is_parent', false)->sum('balance'), 2, '.', '');
                $sumPasiva = number_format(collect($data['pasiva'])->where('is_parent', false)->sum('balance'), 2, '.', '');

                if ($sumActiva != $sumPasiva) {
                    throw new \Exception('Neraca tidak balance');
                }
            }
        });
    }
}
