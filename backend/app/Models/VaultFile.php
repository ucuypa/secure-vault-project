<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini
use Illuminate\Database\Eloquent\Model;

class VaultFile extends Model
{
    use HasFactory; // Tambahkan ini agar bisa memanggil factory()

    protected $fillable = [
        'user_id',
        'original_name',
        'encrypted_path',
        'file_hash',
        'file_size',
        'mime_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
