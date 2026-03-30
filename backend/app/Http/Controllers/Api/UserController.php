<?php

namespace App\Http\Controllers;

use App\Models\User; // <-- 1. Pastikan ini ADA!
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Tampilkan semua daftar user (GET /api/users)
     */
    public function index()
    {
        // Ambil semua data di tabel users, lalu kirimkan hasilnya
        return User::all(); 
    }

    /**
     * Catat user baru (POST /api/users)
     */
    public function store(Request $request)
    {
        // Ambil data dari formulir yang dikirim, lalu simpan ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Password harus diacak biar aman!
            'profile_picture' => $request->profile_picture,
        ]);

        return response()->json($user, 201); // 201 artinya "Berhasil dibuat"
    }

    /**
     * Lihat detail satu user saja (GET /api/users/{id})
     */
    public function show($id)
    {
        // Cari user berdasarkan ID, kalau tidak ketemu akan error 404
        return User::findOrFail($id);
    }

    /**
     * Ganti data user (PUT /api/users/{id})
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Update datanya dengan data baru yang dikirim
        $user->update($request->all());

        return response()->json($user, 200);
    }

    /**
     * Hapus user (DELETE /api/users/{id})
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User sudah dihapus!'], 200);
    }
}