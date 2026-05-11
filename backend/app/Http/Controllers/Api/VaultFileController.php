<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VaultFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Models\ActivityLog;

class VaultFileController extends Controller
{
    /**
     * Only current user's files
     */
    public function index(Request $request)
    {
        $parentId = $request->query('parent_id');

        $files = VaultFile::where('user_id', $request->user()->id)
                          ->where('parent_id', $parentId)
                          ->latest()
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $files
        ]);
    }

    /**
     * Upload, Encrypt, and Associate with User
     */
    public function store(Request $request)
    {
        if ($request->has('name') && !$request->hasFile('file')) {
            $request->validate(['name' => 'required|string|max:255']);

            $folder = VaultFile::create([
                'user_id'        => $request->user()->id,
                'parent_id'      => $request->parent_id,
                'original_name'  => $request->name,
                'mime_type'      => 'directory',
                'encrypted_path' => 'none',
                'file_size'      => 0,
                'file_hash'      => 'none',
            ]);

            // Catat log pembuatan folder
            ActivityLog::log('CREATE_FOLDER', $folder->id, $folder->original_name);

            return response()->json(['success' => true, 'data' => $folder], 201);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();

        $fileContent = file_get_contents($file->getRealPath());

        // Encrypt Data
        $hash = hash('sha256', $fileContent);
        $encryptedContent = encrypt($fileContent);

        // Store Encrypted File
        $fileName = Str::uuid() . '.enc';
        $encryptedPath = 'vault_files/' . $fileName;
        Storage::put($encryptedPath, $encryptedContent);
        
        // Save Metadata
        $vaultFile = VaultFile::create([
            'user_id'        => $request->user()->id,
            'parent_id'      => $request->parent_id,
            'original_name'  => $originalName,
            'encrypted_path' => $encryptedPath,
            'file_hash'      => $hash,
            'file_size'      => $fileSize,
            'mime_type'      => $mimeType,
        ]);

        // Menyertakan nama asli file di parameter ke-3
        ActivityLog::log('UPLOAD_FILE', $vaultFile->id, $vaultFile->original_name);

        return response()->json(['success' => true, 'message' => 'File uploaded successfully.', 'data' => $vaultFile], 201);
    }

    /**
     * Show specific file
     */
    public function show(Request $request, $id)
    {
        $vaultFile = VaultFile::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json(['success' => true, 'data' => $vaultFile]);
    }

    /**
     * Update Metadata
     */
    public function update(Request $request, $id)
    {
        $vaultFile = VaultFile::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'original_name' => 'required|string|max:255',
        ]);
        
        $oldName = $vaultFile->original_name;
        $vaultFile->update(['original_name' => $request->original_name]);

        // Catat aktivitas rename
        ActivityLog::log('RENAME_FILE', $vaultFile->id, "{$oldName} to {$vaultFile->original_name}");

        return response()->json(['success' => true, 'message' => 'Metadata updated.']);
    }

    /**
     * Remove file & physical data
     */
    public function destroy(Request $request, $id)
    {
        $vaultFile = VaultFile::where('user_id', $request->user()->id)->findOrFail($id);
        $fileName = $vaultFile->original_name;

        // Hapus fisik HANYA jika ini bukan folder
        if ($vaultFile->mime_type !== 'directory' && Storage::exists($vaultFile->encrypted_path)) {
            Storage::delete($vaultFile->encrypted_path);
        }

        // Catat log SEBELUM file dihapus dari database
        // Kita kirimkan $fileName agar jika ID-nya hilang, nama file tetap terbaca di log
        $actionName = $vaultFile->mime_type === 'directory' ? 'DELETE_FOLDER' : 'DELETE_FILE';
        ActivityLog::log($actionName, null, $fileName);

        $vaultFile->delete();

        return response()->json(['success' => true, 'message' => 'File/Folder deleted permanently.']);
    }

    /**
     * Download & Decrypt
     */
    public function download(Request $request, $id)
    {
        $vaultFile = VaultFile::where('user_id', $request->user()->id)->findOrFail($id);

        // Cegah download jika ini adalah folder
        if ($vaultFile->mime_type === 'directory') {
            return response()->json(['message' => 'Cannot download a directory.'], 400);
        }

        if (!Storage::exists($vaultFile->encrypted_path)) {
            return response()->json(['message' => 'File not found on server.'], 404);
        }

        $decryptedContent = decrypt(Storage::get($vaultFile->encrypted_path));

        // Catat log download
        ActivityLog::log('DOWNLOAD_FILE', $vaultFile->id, $vaultFile->original_name);

        return Response::make($decryptedContent, 200, [
            'Content-Type'        => $vaultFile->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $vaultFile->original_name . '"',
        ]);
    }
}