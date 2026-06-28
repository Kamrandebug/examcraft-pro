<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_paper_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_paper_id')->constrained('exam_papers')->cascadeOnDelete();
            $table->string('topic_name');
            $table->string('chapter')->nullable();
            $table->integer('question_count')->default(0);
            $table->timestamp('created_at')->useCurrent();
            // no updated_at — schema only has created_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_paper_topics');
    }
};
