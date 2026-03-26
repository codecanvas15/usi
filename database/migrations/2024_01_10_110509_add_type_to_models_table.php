<?php

use App\Models\ItemReceivingReport;
use App\Models\ModelTable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('models', function (Blueprint $table) {
            $table->string('type')->after('alias')->nullable();
        });

        $model = ItemReceivingReport::class;
        $types = [
            'trading',
            'general',
            'service',
            'transport',
        ];

        foreach ($types as $key => $type) {
            if ($key == 0) {
                ModelTable::where('name', $model)->update([
                    'type' => $type,
                    'alias' => 'penerimaan-barang-' . $type,
                ]);
            } else {
                ModelTable::create([
                    'name' => $model,
                    'alias' => 'penerimaan-barang-' . $type,
                    'type' => $type,
                    'group' => 'pembelian'
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('models', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
