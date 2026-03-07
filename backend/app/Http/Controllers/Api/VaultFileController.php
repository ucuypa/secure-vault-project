<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VaultFile;
use Illuminate\Http\Request;

class VaultFileController extends Controller
{
    public function index()
    {
        // Mengambil semua data file dari tabel vault_files
        $files = VaultFile::all();

        return response()->json([
            'success' => true,
            'message' => 'List Data Files',
            'data'    => $files
        ], 200);
    }
}
