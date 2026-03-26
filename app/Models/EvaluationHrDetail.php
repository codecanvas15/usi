<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluationHrDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['evaluation_id', 'kpi_id', 'name', 'weight', 'unit', 'target', 'actual', 'score', 'final_score'];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function kpi()
    {
        return $this->belongsTo(Kpi::class);
    }
}
