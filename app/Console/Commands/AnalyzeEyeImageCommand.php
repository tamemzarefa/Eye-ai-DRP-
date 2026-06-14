<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\FastApiService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Throwable;

/**
 * AnalyzeEyeImageCommand
 *
 * Artisan command to send a local image file to the live running FastAPI service
 * and print the response. Useful for quick end-to-end sanity tests.
 *
 * Usage: php artisan app:analyze-eye "C:\path\to\eye.jpg"
 */
class AnalyzeEyeImageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:analyze-eye {image : The absolute path to the local image file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a local image to the running FastAPI service for analysis';

    /**
     * Execute the console command.
     *
     * @param  FastApiService  $fastApiService
     * @return int
     */
    public function handle(FastApiService $fastApiService): int
    {
        $imagePath = $this->argument('image');

        if (!is_string($imagePath)) {
            $this->error('Please specify a valid image path.');
            return Command::FAILURE;
        }

        // 1. Check if file exists locally
        if (!file_exists($imagePath)) {
            $this->error("File not found at: {$imagePath}");
            return Command::FAILURE;
        }

        $this->info("Found image: {$imagePath}");
        $this->info("Mime Type  : " . mime_content_type($imagePath));
        $this->info("File Size  : " . round(filesize($imagePath) / 1024, 2) . " KB");

        // 2. Wrap file in an UploadedFile object so we can use FastApiService's analyzeEyeImage
        $uploadedFile = new UploadedFile(
            path: $imagePath,
            originalName: basename($imagePath),
            mimeType: mime_content_type($imagePath),
            error: null,
            test: true // Enables testing/reading files that aren't uploaded via HTTP requests
        );

        $this->warn('Sending request to FastAPI service at: ' . config('services.fastapi.base_url') . '/predict');

        try {
            // 3. Make real HTTP request
            $result = $fastApiService->analyzeEyeImage($uploadedFile);

            $this->newLine();
            $this->info('🚀 Response received successfully!');
            $this->line('----------------------------------------------');
            $this->line(' Prediction : ' . $result['prediction']);
            $this->line(' Confidence : ' . ($result['confidence'] * 100) . '%');
            $this->line('----------------------------------------------');

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->newLine();
            $this->error('❌ Request failed!');
            $this->line('Reason: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
