<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VaultFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VaultFileController extends Controller
{
    // 1. GET: List all files (Tanpa filter user_id)
    public function index()
    {
        $files = VaultFile::all(); 
        return response()->json(['success' => true, 'data' => $files]);
    }

    // 2. POST: Store (Upload & Encrypt)
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileContent = file_get_contents($file->getRealPath());

        $hash = hash('sha256', $fileContent);
        $encryptedContent = encrypt($fileContent); 
        
        $fileName = Str::uuid() . '.enc';
        Storage::put('vault/' . $fileName, $encryptedContent);

        $vaultFile = VaultFile::create([
            'user_id' => 1, // Tetap isi 1 agar tidak error database (karena foreign key)
            'original_name' => $originalName,
            'encrypted_path' => 'vault/' . $fileName,
            'file_hash' => $hash,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully (Public Access)',
            'data' => $vaultFile
        ], 201);
    }

    // 3. PUT: Update Metadata
    public function update(Request $request, $id)
    {
        $vaultFile = VaultFile::findOrFail($id);

        $request->validate([
            'original_name' => 'required|string|max:255',
        ]);

        $vaultFile->update([
            'original_name' => $request->original_name
        ]);

        return response()->json(['success' => true, 'data' => $vaultFile]);
    }

    // 4. DELETE: Destroy
    public function destroy($id)
    {
        $vaultFile = VaultFile::findOrFail($id);

        if (Storage::exists($vaultFile->encrypted_path)) {
            Storage::delete($vaultFile->encrypted_path);
        }

        $vaultFile->delete();

        return response()->json(['success' => true, 'message' => 'File deleted.']);
    }
}