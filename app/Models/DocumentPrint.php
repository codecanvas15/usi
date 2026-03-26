<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentPrint extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document_print_approvals()
    {
        return $this->hasMany(DocumentPrintApproval::class);
    }

    public function reference()
    {
        return $this->belongsTo($this->model, 'model_id');
    }
}
