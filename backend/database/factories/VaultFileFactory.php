<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VaultFile>
 */
class VaultFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1, // Kita asumsikan user ID 1 sudah ada
            'original_name' => $this->faker->word() . '.pdf',
            'encrypted_path' => 'vault/' . $this->faker->uuid() . '.enc',
            'file_hash' => hash('sha256', $this->faker->sentence()),
            'file_size' => $this->faker->numberBetween(100000, 5000000), // 100KB - 5MB
            'mime_type' => 'application/pdf',
        ];
    }
}
