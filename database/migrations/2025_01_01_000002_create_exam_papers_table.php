<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('subject')->nullable();
            $table->string('exam_code')->nullable();
            $table->string('organization')->nullable();
            $table->string('session')->nullable();
            $table->year('year')->nullable();
            $table->string('duration')->nullable();
            $table->text('instructions')->nullable();
            $table->text('materials')->nullable();
            $table->string('typography_preset')->nullable();
            $table->json('typography_state')->nullable();
            $table->json('style_state')->nullable();
            $table->json('cover_footer')->nullable();
            $table->json('page_footer')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('last_saved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_papers');
    }
};
