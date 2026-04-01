<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data Activity Logs',
            'data'    => $logs
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'action'        => 'required|string',
            'user_id'       => 'required|exists:users,id', // Harus ada di tabel users
            'vault_file_id' => 'nullable|exists:vault_files,id', // Harus ada di tabel vault_files
        ]);

        $log = ActivityLog::create([
            'action'        => $validated['action'],
            'user_id'       => $validated['user_id'],
            'vault_file_id' => $validated['vault_file_id'],
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Log berhasil dibuat',
            'data'    => $log
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $log = ActivityLog::findOrFail($id);
        
        // Batasi apa yang boleh diubah, biasanya hanya action/catatan saja
        $log->update($request->only(['action']));

        return response()->json([
            'success' => true,
            'message' => 'Status log berhasil diubah',
            'data'    => $log
        ], 200);
    }

    public function destroy(string $id)
    {
        $log = ActivityLog::findOrFail($id);
        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Log berhasil dihapus'
        ], 200);
    }
}