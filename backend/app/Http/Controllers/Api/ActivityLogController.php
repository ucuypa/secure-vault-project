<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Mengambil semua riwayat aktivitas dari tabel activity_logs
        $logs = ActivityLog::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data Activity Logs',
            'data'    => $logs
        ], 200);
    }

    public function store(Request $request)
    {
        // Mencatat log baru
        $validated = $request->validate([
            'action' => 'required|string',
            'user_id' => 'nullable|integer',
            'vault_file_id' => 'nullable|integer',
        ]);

        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();

        $log = ActivityLog::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Log berhasil dibuat',
            'data'    => $log
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        // Mengubah status log
        $log = ActivityLog::findOrFail($id);
        $log->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Status log berhasil diubah',
            'data'    => $log
        ], 200);
    }

    public function destroy(string $id)
    {
        // Membersihkan log lama
        $log = ActivityLog::findOrFail($id);
        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Log berhasil dihapus',
            'data'    => null
        ], 200);
    }
}