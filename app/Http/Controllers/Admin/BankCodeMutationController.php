<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BankCodeMutationController extends Controller
{
    public function check_bank_code(Request $request)
    {
        $code = generate_bank_code(
            ref_model: '',
            ref_id: '',
            coa_id: $request->coa_id,
            type: $request->type,
            date: $request->date,
            is_save: false,
            code: $request->code
        );

        return $code;
    }
}
