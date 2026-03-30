<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VaultFile;
use App\Models\ActivityLog;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Notifications\Action;
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
                'profilepictures' => 'awkjdjakjdjaw.jpg'
            ]
        );

        // 2. Buat data file dummy menggunakan Factory
        VaultFile::factory(10)->create();
        ActivityLog::factory(20)->create();
    }
}
