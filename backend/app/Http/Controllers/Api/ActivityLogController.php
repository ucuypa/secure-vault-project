<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * 1. GET: Menampilkan riwayat aktivitas MILIK USER yang sedang login saja.
     */
    public function index(Request $request)
    {
        // Security Check: Hanya ambil log milik user yang login
        $logs = ActivityLog::where('user_id', $request->user()->id)
                            ->latest()
                            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Your Activity Logs retrieved successfully.',
            'data'    => $logs
        ], 200);
    }

    /**
     * 2. POST: Mencatat log baru (Otomatis menggunakan ID user yang login).
     */
    public function store(Request $request)
    {
        $request->validate([
            'action'        => 'required|string|max:255',
            'vault_file_id' => 'nullable|exists:vault_files,id',
        ]);

        // Security logic: Ambil IP dan User Agent secara otomatis dari sistem
        $log = ActivityLog::create([
            'user_id'       => $request->user()->id, // Mengambil ID dari token Sanctum
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
     * 3. GET: Show specific log (dengan pengecekan kepemilikan).
     */
    public function show(Request $request, $id)
    {
        $log = ActivityLog::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json(['success' => true, 'data' => $log]);
    }

    /**
     * 4. PUT: Update log (Biasanya hanya untuk menambahkan catatan/remarks).
     */
    public function update(Request $request, $id)
    {
        $log = ActivityLog::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate(['action' => 'required|string']);

        $log->update(['action' => $request->action]);

        return response()->json(['success' => true, 'message' => 'Log updated.']);
    }

    /**
     * 5. DELETE: Menghapus log (hanya milik sendiri).
     */
    public function destroy(Request $request, $id)
    {
        $log = ActivityLog::where('user_id', $request->user()->id)->findOrFail($id);
        $log->delete();

        return response()->json(['success' => true, 'message' => 'Log deleted.']);
    }
}