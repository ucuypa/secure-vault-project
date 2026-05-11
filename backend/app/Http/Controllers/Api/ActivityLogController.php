<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Menampilkan riwayat aktivitas.
     */
    public function index(Request $request)
    {
        // Hanya ambil log milik user yang login, urutkan dari yang terbaru
        $logs = ActivityLog::with('vaultFile')->where('user_id', $request->user()->id)
            ->latest()
            ->limit(50) 
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Your Activity Logs retrieved successfully.',
            'data'    => $logs
        ], 200);
    }

    /**
     * Mencatat log baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'action'        => 'required|string|max:255',
            'vault_file_id' => 'nullable|exists:vault_files,id',
        ]);

        // Ambil IP dan User Agent secara otomatis dari sistem request Laravel
        $log = ActivityLog::create([
            'user_id'       => $request->user()->id,
            'vault_file_id' => $request->vault_file_id,
            'action'        => $request->action,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Activity logged successfully.',
            'data'    => $log
        ], 201);
    }

    /**
     * Menampilkan 1 log spesifik.
     */
    public function show(Request $request, $id)
    {
        $log = ActivityLog::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $log
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FITUR UPDATE & DESTROY DIHAPUS (IMMUTABLE AUDIT TRAIL)
    |--------------------------------------------------------------------------
    | Sesuai dengan best practice keamanan sistem informasi, tabel Log Aktivitas
    | (Audit Trail) bersifat "Immutable". Artinya, data log yang sudah tercatat
    | tidak boleh diedit (Update) atau dihapus (Destroy) oleh siapa pun, bahkan
    | oleh user itu sendiri. Hal ini bertujuan untuk mencegah user nakal
    | menghilangkan jejak digital mereka setelah menghapus file penting.
    */

    // public function update(Request $request, $id) { ... }
    // public function destroy(Request $request, $id) { ... }
}
