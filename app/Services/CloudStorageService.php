<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class CloudStorageService
{
    /**
     * Upload file to configured cloud storage
     */
    public function upload(UploadedFile $file, string $path = '', string $disk = null): array
    {
        $disk = $disk ?? config('filesystems.default');
        
        try {
            // Generate unique filename
            $filename = $this->generateFilename($file);
            $fullPath = $path ? $path . '/' . $filename : $filename;
            
            // Upload file
            $uploaded = Storage::disk($disk)->put($fullPath, file_get_contents($file), 'public');
            
            if (!$uploaded) {
                throw new \Exception('Failed to upload file');
            }
            
            // Get URL
            $url = Storage::disk($disk)->url($fullPath);
            
            return [
                'success' => true,
                'path' => $fullPath,
                'url' => $url,
                'filename' => $filename,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'disk' => $disk,
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(array $files, string $path = '', string $disk = null): array
    {
        $results = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $results[] = $this->upload($file, $path, $disk);
            }
        }
        
        return $results;
    }

    /**
     * Delete file from cloud storage
     */
    public function delete(string $path, string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default');
        
        try {
            return Storage::disk($disk)->delete($path);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete multiple files
     */
    public function deleteMultiple(array $paths, string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default');
        
        try {
            return Storage::disk($disk)->delete($paths);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if file exists
     */
    public function exists(string $path, string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default');
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Get file URL
     */
    public function url(string $path, string $disk = null): string
    {
        $disk = $disk ?? config('filesystems.default');
        return Storage::disk($disk)->url($path);
    }

    /**
     * Get temporary URL (for private files)
     */
    public function temporaryUrl(string $path, int $minutes = 60, string $disk = null): string
    {
        $disk = $disk ?? config('filesystems.default');
        return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes($minutes));
    }

    /**
     * Get file size
     */
    public function size(string $path, string $disk = null): int
    {
        $disk = $disk ?? config('filesystems.default');
        return Storage::disk($disk)->size($path);
    }

    /**
     * Get file last modified time
     */
    public function lastModified(string $path, string $disk = null): int
    {
        $disk = $disk ?? config('filesystems.default');
        return Storage::disk($disk)->lastModified($path);
    }

    /**
     * List files in directory
     */
    public function files(string $directory = '', string $disk = null): array
    {
        $disk = $disk ?? config('filesystems.default');
        return Storage::disk($disk)->files($directory);
    }

    /**
     * List all files recursively
     */
    public function allFiles(string $directory = '', string $disk = null): array
    {
        $disk = $disk ?? config('filesystems.default');
        return Storage::disk($disk)->allFiles($directory);
    }

    /**
     * Copy file
     */
    public function copy(string $from, string $to, string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default');
        
        try {
            return Storage::disk($disk)->copy($from, $to);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Move file
     */
    public function move(string $from, string $to, string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default');
        
        try {
            return Storage::disk($disk)->move($from, $to);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate unique filename
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '', $basename);
        
        return $basename . '_' . time() . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Upload from URL
     */
    public function uploadFromUrl(string $url, string $path = '', string $disk = null): array
    {
        $disk = $disk ?? config('filesystems.default');
        
        try {
            // Download file
            $contents = file_get_contents($url);
            
            if ($contents === false) {
                throw new \Exception('Failed to download file from URL');
            }
            
            // Generate filename from URL
            $filename = basename(parse_url($url, PHP_URL_PATH));
            $fullPath = $path ? $path . '/' . $filename : $filename;
            
            // Upload
            $uploaded = Storage::disk($disk)->put($fullPath, $contents, 'public');
            
            if (!$uploaded) {
                throw new \Exception('Failed to upload file');
            }
            
            return [
                'success' => true,
                'path' => $fullPath,
                'url' => Storage::disk($disk)->url($fullPath),
                'filename' => $filename,
                'disk' => $disk,
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload base64 image
     */
    public function uploadBase64(string $base64, string $path = '', string $disk = null): array
    {
        $disk = $disk ?? config('filesystems.default');
        
        try {
            // Decode base64
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                $extension = $matches[1];
                $base64 = substr($base64, strpos($base64, ',') + 1);
            } else {
                $extension = 'png';
            }
            
            $contents = base64_decode($base64);
            
            if ($contents === false) {
                throw new \Exception('Invalid base64 string');
            }
            
            // Generate filename
            $filename = 'image_' . time() . '_' . uniqid() . '.' . $extension;
            $fullPath = $path ? $path . '/' . $filename : $filename;
            
            // Upload
            $uploaded = Storage::disk($disk)->put($fullPath, $contents, 'public');
            
            if (!$uploaded) {
                throw new \Exception('Failed to upload file');
            }
            
            return [
                'success' => true,
                'path' => $fullPath,
                'url' => Storage::disk($disk)->url($fullPath),
                'filename' => $filename,
                'disk' => $disk,
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get storage statistics
     */
    public function getStats(string $disk = null): array
    {
        $disk = $disk ?? config('filesystems.default');
        
        try {
            $files = Storage::disk($disk)->allFiles();
            $totalSize = 0;
            
            foreach ($files as $file) {
                $totalSize += Storage::disk($disk)->size($file);
            }
            
            return [
                'disk' => $disk,
                'total_files' => count($files),
                'total_size' => $totalSize,
                'total_size_mb' => round($totalSize / 1024 / 1024, 2),
                'total_size_gb' => round($totalSize / 1024 / 1024 / 1024, 2),
            ];
            
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Test connection to cloud storage
     */
    public function testConnection(string $disk = null): array
    {
        $disk = $disk ?? config('filesystems.default');
        
        try {
            // Try to list files
            Storage::disk($disk)->files();
            
            return [
                'success' => true,
                'disk' => $disk,
                'message' => 'Connection successful',
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'disk' => $disk,
                'error' => $e->getMessage(),
            ];
        }
    }
}
