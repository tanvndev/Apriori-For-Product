<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RunAprioriAnalysis extends Command
{
    protected $signature = 'apriori:run';
    protected $description = 'Run Apriori analysis using Python script';

    public function handle()
    {
        // ...
        $csvPath = $this->exportDataToCSV();
        $this->info("CSV created at: $csvPath");
        $this->info("Running Python script...");

        $output = shell_exec("python3 ./python/apriori_analysis.py '$csvPath' 2>&1");

        $this->info("Python script output:");
        $this->info($output);

        // Tìm vị trí của JSON trong output
        $jsonStart = strpos($output, '[');
        $jsonEnd = strrpos($output, ']');

        if ($jsonStart === false || $jsonEnd === false) {
            $this->error("Could not find valid JSON in Python script output. The script may have encountered an error.");
            $this->info("Full Python output:");
            $this->info($output);
            return;
        }

        $jsonOutput = substr($output, $jsonStart, $jsonEnd - $jsonStart + 1);

        // Parse kết quả JSON
        $rules = json_decode($jsonOutput, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("JSON decode error: " . json_last_error_msg());
            $this->info("Problematic JSON output:");
            $this->info($jsonOutput);
            return;
        }

        if (empty($rules)) {
            $this->warn("No rules were generated. This may be due to high min_support or insufficient data.");
            return;
        }

        // Lưu kết quả vào file
        $this->saveResults($rules);

        $this->info('Apriori analysis completed.');
    }

    private function exportDataToCSV()
    {
        $data = DB::table('user_actions')
            ->select('user_id', 'product_id', 'action')
            ->get();

        $csv = storage_path('app/user_actions.csv');
        $file = fopen($csv, 'w');
        fputcsv($file, ['user_id', 'product_id', 'action']);

        foreach ($data as $row) {
            fputcsv($file, (array)$row);
        }

        fclose($file);

        return $csv; // Trả về đường dẫn đầy đủ của file CSV
    }

    // private function saveResults($rules)
    // {
    //     if (empty($rules)) {
    //         $this->warn("No rules to save.");
    //         return;
    //     }

    //     foreach ($rules as $rule) {
    //         DB::table('apriori_rules')->insert([
    //             'antecedents' => json_encode($rule['antecedents']),
    //             'consequents' => json_encode($rule['consequents']),
    //             'support' => $rule['support'],
    //             'confidence' => $rule['confidence'],
    //             'lift' => $rule['lift'],
    //         ]);
    //     }

    //     $this->info("Saved " . count($rules) . " rules to database.");
    // }

    // Schema::create('apriori_rules', function (Blueprint $table) {
    //     $table->id();
    //     $table->json('antecedents');
    //     $table->json('consequents');
    //     $table->float('support');
    //     $table->float('confidence');
    //     $table->float('lift');
    //     $table->timestamps();
    // });


    private function saveResults($rules)
    {
        if (empty($rules)) {
            $this->warn("No rules to save.");
            return;
        }

        $jsonOutput = json_encode($rules, JSON_PRETTY_PRINT);

        if ($jsonOutput === false) {
            $this->error("Failed to encode rules to JSON.");
            return;
        }

        // Lưu kết quả ra file JSON trong thư mục storage
        $path = storage_path('app/apriori_rules.json');
        file_put_contents($path, $jsonOutput);

        $this->info("Saved rules to JSON file at: " . $path);
    }

    // pip install pandas mlxtend
}
