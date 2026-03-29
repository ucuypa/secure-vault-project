<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\VaultFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            // Mengambil ID user yang sudah ada
            'user_id' => User::pluck('id')->random() ?: User::factory(),
            
            // Mengambil ID file secara acak (bisa null jika aksinya cuma LOGIN)
            'vault_file_id' => $this->faker->boolean(70) 
                ? (VaultFile::pluck('id')->random() ?: VaultFile::factory()) 
                : null,
            
            // Jenis aksi yang realistis untuk sistem keamanan
            'action' => $this->faker->randomElement([
                'LOGIN', 
                'LOGOUT', 
                'UPLOAD_FILE', 
                'DOWNLOAD_FILE', 
                'DELETE_FILE', 
                'FAILED_DECRYPTION_ATTEMPT'
            ]),
            
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}