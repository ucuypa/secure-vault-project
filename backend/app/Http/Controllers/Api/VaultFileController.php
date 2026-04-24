<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VaultFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class VaultFileController extends Controller
{
    /**
     * 1. GET: List only current user's files
     */
    public function index(Request $request)
    {
        $files = VaultFile::where('user_id', $request->user()->id)->get();
        
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
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileContent = file_get_contents($file->getRealPath());

        // Security Logic: Hashing & Encryption
        $hash = hash('sha256', $fileContent);
        $encryptedContent = encrypt($fileContent); 
        
        $fileName = Str::uuid() . '.enc';
        Storage::put('vault/' . $fileName, $encryptedContent);

        $vaultFile = VaultFile::create([
            'user_id'        => $request->user()->id, // Ambil ID dari Token
            'original_name'  => $originalName,
            'encrypted_path' => 'vault/' . $fileName,
            'file_hash'      => $hash,
            'file_size'      => $file->getSize(),
            'mime_type'      => $file->getClientMimeType(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File secured and linked to your account.',
            'data'    => $vaultFile
        ], 201);
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