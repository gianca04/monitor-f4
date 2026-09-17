<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageConversionService
{
    /**
     * Convert an image to WebP format and fix EXIF orientation
     *
     * @param string $path Path relative to the storage disk
     * @param string $disk Storage disk name (default: 'public')
     * @param int $quality WebP quality (0-100, default: 85)
     * @return string|null New path if converted, original path if already WebP, null on error
     */
    public static function convertToWebP(string $path, string $disk = 'public', int $quality = 85): ?string
    {
        try {
            // Verify file exists
            if (!Storage::disk($disk)->exists($path)) {
                Log::warning("ImageConversionService: File not found: {$path}");
                return null;
            }

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            // If already WebP, no conversion needed
            if ($extension === 'webp') {
                Log::info("ImageConversionService: File already WebP: {$path}");
                return $path;
            }

            // Only process image files
            if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                Log::warning("ImageConversionService: Unsupported format: {$extension} for file: {$path}");
                return $path; // Return original path for unsupported formats
            }

            // Read the image content safely across any disk (Local, S3)
            $fileContent = Storage::disk($disk)->get($path);
            
            // Create a temporary file to safely handle EXIF and ImageManager across disks
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $tempFilePath = $tempDir . '/' . uniqid('img_') . '.' . $extension;
            file_put_contents($tempFilePath, $fileContent);

            // Create image manager instance
            $manager = new ImageManager(new Driver());
            $image = $manager->read($tempFilePath);

            // Fix EXIF orientation for JPEG images
            if ($extension === 'jpg' || $extension === 'jpeg') {
                if (function_exists('exif_read_data')) {
                    $exif = @exif_read_data($tempFilePath);
                    if ($exif && isset($exif['Orientation'])) {
                        $orientation = $exif['Orientation'];
                        switch ($orientation) {
                            case 3: $image->rotate(180); break;
                            case 6: $image->rotate(-90); break;
                            case 8: $image->rotate(90); break;
                        }
                    }
                }
            }

            // Encode to WebP
            $encoded = $image->toWebp($quality);

            // Generate new WebP path
            $pathInfo = pathinfo($path);
            // Si $pathInfo['dirname'] es '.', entonces no agregar barra
            $dir = ($pathInfo['dirname'] === '.') ? '' : $pathInfo['dirname'] . '/';
            $newPath = $dir . $pathInfo['filename'] . '.webp';

            // Upload the WebP image to the original disk
            Storage::disk($disk)->put($newPath, (string) $encoded);

            // Delete original file after successful conversion
            Storage::disk($disk)->delete($path);
            
            // Clean up temp file
            @unlink($tempFilePath);

            Log::info("ImageConversionService: Successfully converted {$path} to {$newPath}");

            return $newPath;
        } catch (\Exception $e) {
            Log::error("ImageConversionService: Error converting {$path}: " . $e->getMessage());
            return $path; // Return original path on error
        }
    }

    /**
     * Batch convert multiple images to WebP
     *
     * @param array $paths Array of paths relative to the storage disk
     * @param string $disk Storage disk name (default: 'public')
     * @param int $quality WebP quality (0-100, default: 85)
     * @return array Array of converted paths
     */
    public static function batchConvertToWebP(array $paths, string $disk = 'public', int $quality = 85): array
    {
        $convertedPaths = [];

        foreach ($paths as $path) {
            if ($path) {
                $converted = self::convertToWebP($path, $disk, $quality);
                $convertedPaths[] = $converted;
            }
        }

        return $convertedPaths;
    }
}
