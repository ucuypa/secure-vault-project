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
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Link ke file (opsional, jika file dihapus log tetap ada)
            $table->foreignId('vault_file_id')->nullable()->constrained()->onDelete('set null');

            $table->string('action');           // 'UPLOAD', 'DOWNLOAD', 'DELETE', 'FAILED_DECRYPT'
            $table->string('ip_address');       // Melacak lokasi pengakses
            $table->text('user_agent');         // Melacak perangkat/browser

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
        Schema::dropIfExists('activity_logs');
    }
};
