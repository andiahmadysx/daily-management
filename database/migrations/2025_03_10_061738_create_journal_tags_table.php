<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('journal_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->foreignUuid('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_tags');
    }
};
