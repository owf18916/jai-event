<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportCsvCommand extends Command
{
    protected $signature = 'db:export 
                            {table : The main table to export} 
                            {--columns= : Comma-separated list of columns} 
                            {--delimiter=, : CSV delimiter} 
                            {--join= : Join option in format table,foreign,local (e.g., profiles,user_id,id)}';

    protected $description = 'Export data from a table (with optional columns and join) to CSV using native PHP.';

    public function handle()
    {
        $table = $this->argument('table');
        $columnsOption = $this->option('columns');
        $delimiter = $this->option('delimiter') ?: ',';
        $joinOption = $this->option('join');

        // Siapkan nama file output
        $filename = $table . '_export_' . date('Ymd_His') . '.csv';
        $filePath = storage_path('app/csv/' . $filename);

        // Ambil kolom
        $columns = $columnsOption ? explode(',', $columnsOption) : ['*'];

        // Build query
        $query = DB::table($table);

        if ($joinOption) {
            $joinParts = explode(',', $joinOption);
            if (count($joinParts) === 3) {
                [$joinTable, $foreignKey, $localKey] = $joinParts;
                $query->join($joinTable, "$table.$localKey", '=', "$joinTable.$foreignKey");
            } else {
                $this->error("Invalid join format. Use table,foreign,local");
                return Command::FAILURE;
            }
        }

        $data = $query->select($columns)->get();

        if ($data->isEmpty()) {
            $this->error("No data found in table '$table'");
            return Command::FAILURE;
        }

        // Pastikan folder csv ada
        if (!is_dir(storage_path('app/csv'))) {
            mkdir(storage_path('app/csv'), 0755, true);
        }

        // Buat file CSV
        $fp = fopen($filePath, 'w');
        if (!$fp) {
            $this->error("Cannot open file for writing: $filePath");
            return Command::FAILURE;
        }

        // Tulis header
        fputcsv($fp, array_keys((array)$data->first()), $delimiter);

        // Tulis data
        foreach ($data as $row) {
            fputcsv($fp, (array)$row, $delimiter);
        }

        fclose($fp);

        $this->info("Export completed: $filePath");
        return Command::SUCCESS;
    }
}
