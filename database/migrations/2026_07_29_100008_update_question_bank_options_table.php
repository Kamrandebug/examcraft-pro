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
            $table->text('option_a_text')->nullable();
            $table->string('option_a_image')->nullable();
            $table->text('option_b_text')->nullable();
            $table->string('option_b_image')->nullable();
            $table->text('option_c_text')->nullable();
            $table->string('option_c_image')->nullable();
            $table->text('option_d_text')->nullable();
            $table->string('option_d_image')->nullable();
            $table->enum('correct_option', ['A','B','C','D'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_bank_options', function (Blueprint $table) {
            $table->dropColumn([
                'option_a_text', 'option_a_image',
                'option_b_text', 'option_b_image',
                'option_c_text', 'option_c_image',
                'option_d_text', 'option_d_image',
                'correct_option', 'created_at', 'updated_at'
            ]);
        });
    }
};
