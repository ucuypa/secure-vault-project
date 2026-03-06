<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('vault_files', function (Blueprint $table) {
            $table->id();
            // Relasi ke user pemilik file
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('original_name');    // Nama asli: 'kunci_jawaban.pdf'
            $table->string('encrypted_path');   // Nama di storage: 'vault/3f2a1...enc'
            $table->string('file_hash');        // SHA-256 untuk Integrity Check
            $table->bigInteger('file_size');    // Ukuran file dalam bytes
            $table->string('mime_type');        // Contoh: 'application/pdf'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vault_files');
    }
};
