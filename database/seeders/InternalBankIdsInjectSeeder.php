<?php

namespace Database\Seeders;

use App\Models\InvoiceGeneral;
use Illuminate\Database\Seeder;

class InternalBankIdsInjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InvoiceGeneral::all()->each(function ($invoice) {
            $invoice->update([
                'bank_internal_ids' => [$invoice->bank_internal_id],
            ]);
        });
    }
}
