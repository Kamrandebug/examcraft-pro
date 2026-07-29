<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('question_bank', function (Blueprint $table) {
            $table->renameColumn('stem', 'question_text');
            $table->renameColumn('stem_image_url', 'question_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_bank', function (Blueprint $table) {
            $table->renameColumn('question_text', 'stem');
            $table->renameColumn('question_image', 'stem_image_url');
        });
    }
};
