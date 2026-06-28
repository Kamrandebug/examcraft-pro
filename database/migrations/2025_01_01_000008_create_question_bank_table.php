<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('source_paper_code')->nullable();   // e.g. 5054/11
            $table->string('session')->nullable();              // e.g. May/June
            $table->year('year')->nullable();
            $table->string('subject')->nullable();
            $table->string('topic')->nullable();
            $table->string('difficulty')->nullable();           // easy, medium, hard
            $table->text('stem');
            $table->string('stem_image_url')->nullable();
            $table->string('option_type')->nullable();
            $table->string('correct_answer')->nullable();
            $table->integer('marks')->default(1);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_bank');
    }
};
