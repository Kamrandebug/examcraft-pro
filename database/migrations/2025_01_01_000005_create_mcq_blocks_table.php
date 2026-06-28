<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcq_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->unique()->constrained('blocks')->cascadeOnDelete();
            $table->text('stem');
            $table->string('stem_image_url')->nullable();
            $table->string('option_type')->nullable();         // text, image, table
            $table->json('option_table_headers')->nullable();
            $table->integer('marks')->default(1);
            $table->string('layout')->default('1col');         // 1col, 2col, inline
            $table->string('correct_answer')->nullable();
            $table->boolean('show_answer_boxes')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcq_blocks');
    }
};
