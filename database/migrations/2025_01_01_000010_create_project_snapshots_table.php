<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_paper_id')->constrained('exam_papers')->cascadeOnDelete();
            $table->string('version_label');
            $table->json('full_state');
            $table->timestamp('created_at')->useCurrent();
            // no updated_at — snapshots are immutable once saved
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_snapshots');
    }
};
