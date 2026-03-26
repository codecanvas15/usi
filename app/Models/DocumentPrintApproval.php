<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentPrintApproval extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document_print()
    {
        return $this->belongsTo(DocumentPrint::class);
    }
}
