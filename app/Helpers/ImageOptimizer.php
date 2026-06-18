<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

class ImageOptimizer
{
    /**
     * Optimize an uploaded image: convert to WebP, constrain size, and generate thumbnail.
     *
     * @param UploadedFile|string $file
     * @param string $destinationDir
     * @param string $baseName
     * @return array Array containing 'main' and 'thumb' filenames.
     */
    public static function optimize($file, string $destinationDir, string $baseName): array
    {
        $pathname = $file instanceof UploadedFile ? $file->getPathname() : $file;
        $originalName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($pathname);
        $mime = $file instanceof UploadedFile ? $file->getMimeType() : mime_content_type($pathname);

        // Load source image based on mime type
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/pjpeg':
                $srcImage = @imagecreatefromjpeg($pathname);
                break;
            case 'image/png':
            case 'image/x-png':
                $srcImage = @imagecreatefrompng($pathname);
                if ($srcImage) {
                    imagealphablending($srcImage, false);
                    imagesavealpha($srcImage, true);
                }
                break;
            case 'image/gif':
                $srcImage = @imagecreatefromgif($pathname);
                if ($srcImage) {
                    imagealphablending($srcImage, false);
                    imagesavealpha($srcImage, true);
                }
                break;
            case 'image/webp':
                $srcImage = @imagecreatefromwebp($pathname);
                if ($srcImage) {
                    imagealphablending($srcImage, false);
                    imagesavealpha($srcImage, true);
                }
                break;
            case 'image/bmp':
            case 'image/x-ms-bmp':
                $srcImage = @imagecreatefrombmp($pathname);
                break;
            default:
                $srcImage = false;
        }

        // Fallback: if GD image creation fails, move original file
        if (!$srcImage) {
            $ext = $file instanceof UploadedFile ? $file->getClientOriginalExtension() : pathinfo($pathname, PATHINFO_EXTENSION);
            $filename = $baseName . '.' . ($ext ?: 'webp');
            if ($file instanceof UploadedFile) {
                $file->move($destinationDir, $filename);
            } else {
                copy($pathname, rtrim($destinationDir, '/') . '/' . $filename);
            }
            return [
                'main' => $filename,
                'thumb' => $filename
            ];
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // 1. Main Image Optimization (Constraint: max 1200px width/height)
        $mainMaxWidth = 1200;
        $mainMaxHeight = 1200;
        $mainWidth = $origWidth;
        $mainHeight = $origHeight;

        if ($origWidth > $mainMaxWidth || $origHeight > $mainMaxHeight) {
            $ratio = min($mainMaxWidth / $origWidth, $mainMaxHeight / $origHeight);
            $mainWidth = (int)($origWidth * $ratio);
            $mainHeight = (int)($origHeight * $ratio);
        }

        $mainImage = imagecreatetruecolor($mainWidth, $mainHeight);
        imagealphablending($mainImage, false);
        imagesavealpha($mainImage, true);
        imagecopyresampled($mainImage, $srcImage, 0, 0, 0, 0, $mainWidth, $mainHeight, $origWidth, $origHeight);

        $mainFilename = $baseName . '.webp';
        $mainPath = rtrim($destinationDir, '/') . '/' . $mainFilename;
        
        // Ensure folder exists
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0775, true);
        }

        imagewebp($mainImage, $mainPath, 80);
        imagedestroy($mainImage);

        // 2. Thumbnail Generation (Constraint: max 320px width/height)
        $thumbMaxWidth = 320;
        $thumbMaxHeight = 320;
        $thumbWidth = $origWidth;
        $thumbHeight = $origHeight;

        if ($origWidth > $thumbMaxWidth || $origHeight > $thumbMaxHeight) {
            $ratio = min($thumbMaxWidth / $origWidth, $thumbMaxHeight / $origHeight);
            $thumbWidth = (int)($origWidth * $ratio);
            $thumbHeight = (int)($origHeight * $ratio);
        }

        $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagealphablending($thumbImage, false);
        imagesavealpha($thumbImage, true);
        imagecopyresampled($thumbImage, $srcImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $origWidth, $origHeight);

        $thumbsDir = rtrim($destinationDir, '/') . '/thumbs';
        if (!file_exists($thumbsDir)) {
            mkdir($thumbsDir, 0775, true);
        }

        $thumbFilename = $thumbsDir . '/' . $mainFilename;
        imagewebp($thumbImage, $thumbFilename, 75);
        imagedestroy($thumbImage);

        imagedestroy($srcImage);

        return [
            'main' => $mainFilename,
            'thumb' => 'thumbs/' . $mainFilename
        ];
    }
}
