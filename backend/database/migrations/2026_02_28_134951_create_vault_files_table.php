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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('original_name');    // Nama file asli (misal: tugas.pdf)
            $table->string('encrypted_path');   // Nama file acak di storage (UUID)
            $table->string('file_hash');        // SHA-256 untuk cek integritas
            $table->bigInteger('file_size');    // Ukuran file dalam bytes
            $table->string('mime_type');        // Tipe file (application/pdf, dsb)
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
