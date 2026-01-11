<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hasil_ujians', function (Blueprint $table) {
            // Cegah user submit multiple exam untuk materi yang sama dalam waktu dekat
            $table->unique(['user_id', 'materi_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('hasil_ujians', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'materi_id', 'created_at']);
        });
    }
};
