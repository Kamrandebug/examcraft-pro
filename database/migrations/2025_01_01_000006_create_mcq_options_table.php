<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcq_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mcq_block_id')->constrained('mcq_blocks')->cascadeOnDelete();
            $table->string('label');                  // A, B, C, D
            $table->text('option_text')->nullable();
            $table->string('option_image_url')->nullable();
            $table->json('option_cells')->nullable();  // for table-type options
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcq_options');
    }
};
