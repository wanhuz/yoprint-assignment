<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessCsvJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $uploadId;

    public function __construct($uploadId)
    {
        $this->uploadId = $uploadId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {

        $upload = Upload::find($this->uploadId);

        if (!$upload) {
            \Log::error("Upload {$this->uploadId} not found.");
            return;
        }

        $upload->update(['status' => 'processing']);

        try {
            $path = storage_path("app/private/{$upload->filename}");
            $handle = fopen($path, 'r');
            $header = fgetcsv($handle);

            // Clean header: trim whitespace, remove BOM and invisible chars
            $header = array_map(function ($h) {
                // Remove BOM (Byte Order Mark)
                $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);
                // Trim whitespace and normalize encoding
                $h = trim(mb_convert_encoding($h, 'UTF-8', 'UTF-8'));
                return $h;
            }, $header);


            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);

                // Clean up UTF-8
                $data = array_map(fn($v) => mb_convert_encoding($v, 'UTF-8', 'UTF-8'), $data);

                $product = Product::updateOrCreate(
                    ['unique_key' => $data['UNIQUE_KEY']],
                    [
                        'product_title' => $data['PRODUCT_TITLE'] ?? null,
                        'product_description' => $data['PRODUCT_DESCRIPTION'] ?? null,
                        'style' => $data['STYLE#'] ?? null,
                        'sanmar_mainframe_color' => $data['SANMAR_MAINFRAME_COLOR'] ?? null,
                        'size' => $data['SIZE'] ?? null,
                        'color_name' => $data['COLOR_NAME'] ?? null,
                        'piece_price' => $data['PIECE_PRICE'] ?? null,
                    ]
                );

            }

            fclose($handle);
            $upload->update(['status' => 'completed']);
            \Log::info("Upload {$this->uploadId} processed successfully.");

        } catch (\Exception $e) {
            $upload->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            \Log::error($e->getMessage());
        }
    }
}
