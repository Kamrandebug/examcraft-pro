<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_bank_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')
                  ->constrained('question_bank')
                  ->cascadeOnDelete();
            $table->string('label');                   // A, B, C, D
            $table->text('option_text')->nullable();
            $table->string('option_image_url')->nullable();
            $table->json('option_cells')->nullable();
            $table->integer('sort_order')->default(0);
            // no timestamps — schema has no created_at / updated_at here
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_bank_options');
    }
};
