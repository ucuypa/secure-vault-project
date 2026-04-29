<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VaultFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;

class VaultFileController extends Controller
{
    /**
     * 1. GET: List only current user's files
     */
    public function index(Request $request)
    {
        $files = VaultFile::where('user_id', $request->user()->id)->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $files
        ]);
    }

    /**
     * 2. POST: Upload, Encrypt, and Associate with User
     */
    public function store(Request $request)
    {
        // 1. Validate Input
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();

        // 2. Extract Raw Data
        // Baca seluruh file content ke dalam bentuk string/byte
        $fileContent = file_get_contents($file->getRealPath());

        // 3. Encrypt Data
        $hash = hash('sha256', $fileContent);
        $encryptedContent = encrypt($fileContent);

        // 4. Generate Unique Path & Store Encrypted File
        $fileName = Str::uuid() . '.enc';
        $encryptedPath = 'vault_files/' . $fileName;

        Storage::put($encryptedPath, $encryptedContent);

        // 5. Save Metadata to Database
        $vaultFile = VaultFile::create([
            'user_id' => $request->user()->id,
            'original_name' => $originalName,
            'encrypted_path' => $encryptedPath,
            'file_hash' => $hash,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
        ]);

        return response()->json(['success' => true, 'message' => 'File uploaded successfully.', 'data' => $vaultFile], 201);
    }

    /**
     * 3. GET: Show specific file (with ownership check)
     */
    public function show(Request $request, $id)
    {
        $vaultFile = VaultFile::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json(['success' => true, 'data' => $vaultFile]);
    }

    /**
     * 4. PUT: Update Metadata
     */
    public function update(Request $request, $id)
    {
        $vaultFile = VaultFile::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'original_name' => 'required|string|max:255',
        ]);

        $vaultFile->update(['original_name' => $request->original_name]);

        return response()->json(['success' => true, 'message' => 'Metadata updated.']);
    }

    /**
     * 5. DELETE: Remove file & physical data
     */
    public function destroy(Request $request, $id)
    {
        $vaultFile = VaultFile::where('user_id', $request->user()->id)->findOrFail($id);

        if (Storage::exists($vaultFile->encrypted_path)) {
            Storage::delete($vaultFile->encrypted_path);
        }

        $vaultFile->delete();

        return response()->json(['success' => true, 'message' => 'File deleted permanently.']);
    }

    /**
     * 6. GET: Download & Decrypt (with ownership check)
     */
    public function download(Request $request, $id)
    {
        $vaultFile = VaultFile::where('user_id', $request->user()->id)->findOrFail($id);

        if (!Storage::exists($vaultFile->encrypted_path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        $decryptedContent = decrypt(Storage::get($vaultFile->encrypted_path));

        return Response::make($decryptedContent, 200, [
            'Content-Type'        => $vaultFile->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $vaultFile->original_name . '"',
        ]);
    }
}