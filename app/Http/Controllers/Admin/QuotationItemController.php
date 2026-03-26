<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuotationItem;
use Illuminate\Support\Facades\DB;

class QuotationItemController extends Controller
{
    public function destroy($id)
    {
        $quotation_item = QuotationItem::findOrFail($id);
        try {
            $quotation_item->delete();
            return redirect(route('admin.quotation.index'))->with($this->ResponseMessageCRUD(true, 'delete', null, 'Sukses delete additional item'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Gagal delete additional item'));
        }
    }
}
