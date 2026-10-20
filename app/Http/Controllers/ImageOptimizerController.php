<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageOptimizerController extends Controller
{
    /**
     * Optimizes, resizes, caches, and converts external images to WebP.
     */
    public function optimize(Request $request): Response
    {
        $rawUrl = $request->query('url');
        if (empty($rawUrl) || !filter_var($rawUrl, FILTER_VALIDATE_URL)) {
            abort(400, 'Invalid or missing image URL.');
        }

        // Whitelist allowed domains to prevent open proxy abuse
        $parsed = parse_url($rawUrl);
        $host = strtolower($parsed['host'] ?? '');
        $allowedHosts = [
            'whc.unesco.org',
            'upload.wikimedia.org',
            'commons.wikimedia.org',
            'en.wikipedia.org',
            'wikidata.org',
            'www.wikidata.org',
        ];

        $isAllowed = false;
        foreach ($allowedHosts as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            abort(403, 'Image host is not permitted.');
        }

        // Target width & quality
        $width = max(50, min(1920, (int) $request->query('w', 800)));
        $quality = max(40, min(95, (int) $request->query('q', 80)));

        // Unique cache key based on URL, width, and quality
        $cacheKey = md5($rawUrl . '_w' . $width . '_q' . $quality);
        $cacheDir = storage_path('app/public/image_cache');
        $cachedFilePath = $cacheDir . DIRECTORY_SEPARATOR . $cacheKey . '.webp';

        // 1. Serve directly from cache if exists
        if (File::exists($cachedFilePath)) {
            return response()->file($cachedFilePath, [
                'Content-Type'  => 'image/webp',
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }

        // 2. Fetch image from external source with appropriate headers
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer'    => '',
                'Accept'     => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
            ])->timeout(8)->get($rawUrl);

            if (!$response->successful()) {
                abort($response->status(), 'Failed to fetch source image.');
            }

            $imageBytes = $response->body();
        } catch (\Throwable $e) {
            abort(502, 'Could not retrieve external image.');
        }

        // 3. Process image with GD (Resize & WebP convert)
        $sourceImage = @imagecreatefromstring($imageBytes);
        if (!$sourceImage) {
            abort(500, 'Unable to decode source image.');
        }

        $origWidth  = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        // Calculate proportional height
        if ($origWidth > $width) {
            $targetWidth  = $width;
            $targetHeight = (int) round(($origHeight / $origWidth) * $width);
        } else {
            $targetWidth  = $origWidth;
            $targetHeight = $origHeight;
        }

        // Create resized canvas
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve alpha transparency
        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);
        $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
        imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0, 0, 0, 0,
            $targetWidth,
            $targetHeight,
            $origWidth,
            $origHeight
        );

        // Ensure cache directory exists
        if (!File::exists($cacheDir)) {
            File::makeDirectory($cacheDir, 0755, true);
        }

        // Save as WebP
        if (function_exists('imagewebp')) {
            imagewebp($targetImage, $cachedFilePath, $quality);
            imagedestroy($sourceImage);
            imagedestroy($targetImage);

            return response()->file($cachedFilePath, [
                'Content-Type'  => 'image/webp',
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }

        // Fallback to JPEG if WebP unsupported
        $cachedJpegPath = $cacheDir . DIRECTORY_SEPARATOR . $cacheKey . '.jpg';
        imagejpeg($targetImage, $cachedJpegPath, $quality);
        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return response()->file($cachedJpegPath, [
            'Content-Type'  => 'image/jpeg',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}

