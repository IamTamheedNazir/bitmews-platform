<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CloudStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StorageController extends Controller
{
    protected $storageService;

    public function __construct(CloudStorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * Upload single file
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
            'path' => 'nullable|string',
            'disk' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->storageService->upload(
            $request->file('file'),
            $request->get('path', ''),
            $request->get('disk')
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ], 201);
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array',
            'files.*' => 'file|max:10240',
            'path' => 'nullable|string',
            'disk' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $results = $this->storageService->uploadMultiple(
            $request->file('files'),
            $request->get('path', ''),
            $request->get('disk')
        );

        return response()->json([
            'success' => true,
            'data' => $results,
        ], 201);
    }

    /**
     * Upload from URL
     */
    public function uploadFromUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'path' => 'nullable|string',
            'disk' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->storageService->uploadFromUrl(
            $request->get('url'),
            $request->get('path', ''),
            $request->get('disk')
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ], 201);
    }

    /**
     * Upload base64 image
     */
    public function uploadBase64(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'base64' => 'required|string',
            'path' => 'nullable|string',
            'disk' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->storageService->uploadBase64(
            $request->get('base64'),
            $request->get('path', ''),
            $request->get('disk')
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ], 201);
    }

    /**
     * Delete file
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
            'disk' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deleted = $this->storageService->delete(
            $request->get('path'),
            $request->get('disk')
        );

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'File deleted successfully' : 'Failed to delete file',
        ]);
    }

    /**
     * Get file URL
     */
    public function getUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
            'disk' => 'nullable|string',
            'temporary' => 'nullable|boolean',
            'minutes' => 'nullable|integer|min:1|max:1440',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if ($request->get('temporary', false)) {
                $url = $this->storageService->temporaryUrl(
                    $request->get('path'),
                    $request->get('minutes', 60),
                    $request->get('disk')
                );
            } else {
                $url = $this->storageService->url(
                    $request->get('path'),
                    $request->get('disk')
                );
            }

            return response()->json([
                'success' => true,
                'url' => $url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List files
     */
    public function listFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'directory' => 'nullable|string',
            'disk' => 'nullable|string',
            'recursive' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if ($request->get('recursive', false)) {
                $files = $this->storageService->allFiles(
                    $request->get('directory', ''),
                    $request->get('disk')
                );
            } else {
                $files = $this->storageService->files(
                    $request->get('directory', ''),
                    $request->get('disk')
                );
            }

            return response()->json([
                'success' => true,
                'files' => $files,
                'count' => count($files),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get storage statistics
     */
    public function getStats(Request $request)
    {
        $stats = $this->storageService->getStats($request->get('disk'));

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Test connection
     */
    public function testConnection(Request $request)
    {
        $result = $this->storageService->testConnection($request->get('disk'));

        return response()->json($result);
    }
}
