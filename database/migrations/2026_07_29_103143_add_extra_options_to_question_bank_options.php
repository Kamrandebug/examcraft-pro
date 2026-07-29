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
        Schema::table('question_bank_options', function (Blueprint $table) {
            // Optional extras
            $table->text('option_e_text')->after('option_d_image')->nullable();
            $table->string('option_e_image')->after('option_e_text')->nullable();
            $table->text('option_f_text')->after('option_e_image')->nullable();
            $table->string('option_f_image')->after('option_f_text')->nullable();
            
            // Correct option updated - Note: changing enum might require raw SQL in some DBs or using change() if supported
            // Since we are adding values, we can just redeclare it if the DB allows or use a fresh column approach if needed.
            // For simplicity in this dev environment, we'll try the change approach.
            $table->enum('correct_option', ['A','B','C','D','E','F'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_bank_options', function (Blueprint $table) {
            $table->dropColumn(['option_e_text', 'option_e_image', 'option_f_text', 'option_f_image']);
            $table->enum('correct_option', ['A','B','C','D'])->nullable()->change();
        });
    }
};
