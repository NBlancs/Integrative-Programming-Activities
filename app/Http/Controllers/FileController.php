<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Upload a file to the authenticated user's private directory.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $validated['file'];
        $originalName = $file->getClientOriginalName();
        $storedName = now()->format('YmdHis') . '_' . uniqid() . '_' . $originalName;
        $path = $file->storeAs($this->userDirectory($request), $storedName, 'local');

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'data' => [
                'name' => $storedName,
                'original_name' => $originalName,
                'path' => $path,
                'size' => $file->getSize(),
            ],
        ], 201);
    }

    /**
     * List files for the authenticated user.
     */
    public function index(Request $request)
    {
        $directory = $this->userDirectory($request);
        $files = Storage::disk('local')->files($directory);

        $data = array_map(function (string $filePath): array {
            $absolutePath = Storage::disk('local')->path($filePath);

            return [
                'name' => basename($filePath),
                'path' => $filePath,
                'size' => Storage::disk('local')->size($filePath),
                'last_modified' => Storage::disk('local')->lastModified($filePath),
                'mime_type' => File::mimeType($absolutePath),
            ];
        }, $files);

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    /**
     * Get metadata of a specific file.
     */
    public function show(Request $request, string $filename)
    {
        if (!$this->isSafeFilename($filename)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid filename',
            ], 422);
        }

        $path = $this->userDirectory($request) . '/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        $absolutePath = Storage::disk('local')->path($path);

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $filename,
                'path' => $path,
                'size' => Storage::disk('local')->size($path),
                'last_modified' => Storage::disk('local')->lastModified($path),
                'mime_type' => File::mimeType($absolutePath),
            ],
        ], 200);
    }

    /**
     * Download a file that belongs to the authenticated user.
     */
    public function download(Request $request, string $filename)
    {
        if (!$this->isSafeFilename($filename)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid filename',
            ], 422);
        }

        $path = $this->userDirectory($request) . '/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        return response()->download(Storage::disk('local')->path($path), $filename);
    }

    /**
     * Delete a file that belongs to the authenticated user.
     */
    public function destroy(Request $request, string $filename)
    {
        if (!$this->isSafeFilename($filename)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid filename',
            ], 422);
        }

        $path = $this->userDirectory($request) . '/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        Storage::disk('local')->delete($path);

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully',
        ], 200);
    }

    private function userDirectory(Request $request): string
    {
        return 'users/' . $request->user()->id;
    }

    private function isSafeFilename(string $filename): bool
    {
        return preg_match('/^[A-Za-z0-9._-]+$/', $filename) === 1;
    }
}
