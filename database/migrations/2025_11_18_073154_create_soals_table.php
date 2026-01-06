<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_id')->constrained()->cascadeOnDelete();
            $table->text('pertanyaan');
            $table->string('jawaban_a');
            $table->string('jawaban_b');
            $table->string('jawaban_c')->nullable();
            $table->string('jawaban_d')->nullable();
            $table->string('jawaban_benar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
