<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VaultFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VaultFileController extends Controller
{
    // 1. GET List all files (Tanpa filter user_id)
    public function index()
    {
        $files = VaultFile::all(); 
        return response()->json(['success' => true, 'data' => $files]);
    }
    
    public function show($id)
    {
        $file = VaultFile::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail File Found',
            'data'    => $file
        ]);
    }

    // 2. POST Store (Upload & Encrypt)
    public function store(Request $request)
    {
        $request->validate([
            'original_name' => 'required|string|max:255',
            'mime_type'     => 'required|string',
            'file_size'     => 'required|integer',
        ]);

        // Simulasi Enkripsi (karena masih belum adafile asli, enkripsi nama filenya saja)
        $simulatedContent = "Content of " . $request->original_name;
        $hash = hash('sha256', $simulatedContent);
        $encryptedContent = encrypt($simulatedContent); 
        
        $fileName = Str::uuid() . '.enc';
        Storage::put('vault/' . $fileName, $encryptedContent);

        $vaultFile = VaultFile::create([
            'user_id'        => 1, 
            'original_name'  => $request->original_name,
            'encrypted_path' => 'vault/' . $fileName,
            'file_hash'      => $hash,
            'file_size'      => $request->file_size,
            'mime_type'      => $request->mime_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File metadata stored and encrypted successfully',
            'data'    => $vaultFile
        ], 201);
    }

    // 3. PUT Update Metadata
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

    // 4. DELETE Destroy
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