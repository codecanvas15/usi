<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Authorization extends Model
{
    protected $fillable = [
        'branch_id',
        'user_id',
        'title',
        'subtitle',
        'model',
        'model_id',
        'note',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(AuthorizationDetail::class);
    }

    public function reference()
    {
        return $this->belongsTo($this->model, 'model_id');
    }

    // booted
    protected static function booted()
    {
        static::deleted(function ($model) {
            $model->details()->delete();
        });
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function getApprovalCount()
    {
        $all_approval = $this->details()->count();
        $approved = $this->details()->where('status', 'approve')->count();

        return $approved . ' dari ' . $all_approval;
    }
}
