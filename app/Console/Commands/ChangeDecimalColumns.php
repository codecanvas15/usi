<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeDecimalColumns extends Command
{
    protected $signature = 'change:decimal-columns {newType}';
    protected $description = 'Change all decimal columns to a new type';

    public function handle()
    {
        $newType = $this->argument('newType');

        $database = config('database.connections.mysql.database');
        $columns = DB::select("
            SELECT TABLE_NAME, COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE DATA_TYPE = 'decimal' 
            AND TABLE_SCHEMA = ?
        ", [$database]);

        foreach ($columns as $column) {
            $table = $column->TABLE_NAME;
            $columnName = $column->COLUMN_NAME;

            if (Schema::hasTable($table)) {
                Schema::table($table, function ($table) use ($columnName, $newType) {
                    $table->{$newType}($columnName)->change();
                });

                $this->info("Changed column `$columnName` in table `$table` to type `$newType`.");
            }
        }

        $this->info('All decimal columns have been updated.');
        return 0;
    }
}
