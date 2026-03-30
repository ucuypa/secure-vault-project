<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VaultFile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Dummy (untuk login nanti)
        User::updateOrCreate(
            ['email' => 'yusuf@example.com'], // Cek apakah email ini sudah ada
            [
                'name' => 'Solehudin Yusuf',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Buat 10 data file dummy menggunakan Factory
        VaultFile::factory(10)->create();

        // buat 10 orang palsu sekaligus
        \App\Models\User::factory(10)->create();
    }
}
