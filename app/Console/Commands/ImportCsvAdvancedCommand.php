<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ImportCsvAdvancedCommand extends Command
{
    protected $signature = 'csv:import-advanced {filename} {table} {--queue} {--delimiter=,}';
    protected $description = 'Import CSV to specified table with validation, batch insert, error report in CSV, progress bar, optional queue, custom delimiter';

    public function handle()
    {
        $filename = $this->argument('filename');
        $table = $this->argument('table');
        $delimiter = $this->option('delimiter');
        $filePath = storage_path('app/csv/' . $filename);

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return Command::FAILURE;
        }

        if (!Schema::hasTable($table)) {
            $this->error("Table '$table' does not exist.");
            return Command::FAILURE;
        }

        if ($this->option('queue')) {
            dispatch(function () use ($filePath, $table, $delimiter) {
                $this->processCsv($filePath, $table, $delimiter);
            });
            $this->info("Import job queued.");
            return Command::SUCCESS;
        }

        return $this->processCsv($filePath, $table, $delimiter);
    }

    private function processCsv($filePath, $table, $delimiter)
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $this->error("Cannot open file: $filePath");
            return Command::FAILURE;
        }

        $header = fgetcsv($handle, 0, $delimiter);
        if ($header === false) {
            $this->error("Empty file or invalid format.");
            fclose($handle);
            return Command::FAILURE;
        }

        // Cek apakah kolom header ada di tabel
        $tableColumns = Schema::getColumnListing($table);
        foreach ($header as $col) {
            if (!in_array($col, $tableColumns)) {
                $this->error("Column '$col' from CSV does not exist in table '$table'.");
                fclose($handle);
                return Command::FAILURE;
            }
        }

        // Hitung total baris untuk progress bar
        $totalRows = 0;
        while (fgetcsv($handle, 0, $delimiter) !== false) {
            $totalRows++;
        }
        rewind($handle);
        fgetcsv($handle, 0, $delimiter); // skip header lagi

        $this->info("Total rows: $totalRows");
        $bar = $this->output->createProgressBar($totalRows);
        $bar->start();

        $batchSize = 1000;
        $batchData = [];
        $rowNumber = 1;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;

                $data = array_combine($header, $row);

                // Tambahkan timestamps jika tabel punya kolom created_at & updated_at
                if (in_array('created_at', $tableColumns)) {
                    $data['created_at'] = now();
                }
                if (in_array('updated_at', $tableColumns)) {
                    $data['updated_at'] = now();
                }

                // Validasi sederhana: semua kolom CSV harus diisi
                $validator = Validator::make($data, array_fill_keys($header, 'required'));

                if ($validator->fails()) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'errors' => implode('; ', $validator->errors()->all())
                    ];
                } else {
                    $batchData[] = $data;
                }

                if (count($batchData) >= $batchSize) {
                    DB::table($table)->insert($batchData);
                    $batchData = [];
                }

                $bar->advance();
            }

            if (!empty($batchData)) {
                DB::table($table)->insert($batchData);
            }

            if (!empty($errors)) {
                DB::rollBack();
                $errorFile = storage_path('app/csv/errors_' . time() . '.csv');
                $this->saveErrorsAsCsv($errorFile, $errors);

                $this->newLine();
                $this->error("Validation errors found. Import cancelled.");
                $this->info("Error report saved at: {$errorFile}");
                fclose($handle);
                return Command::FAILURE;
            }

            DB::commit();
            fclose($handle);
            $bar->finish();
            $this->newLine();
            $this->info("Import completed successfully into table: $table");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            $this->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function saveErrorsAsCsv($filePath, $errors)
    {
        $fp = fopen($filePath, 'w');
        fputcsv($fp, ['Row', 'Errors']);
        foreach ($errors as $error) {
            fputcsv($fp, [$error['row'], $error['errors']]);
        }
        fclose($fp);
    }
}
