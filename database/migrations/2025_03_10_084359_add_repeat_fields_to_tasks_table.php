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
            Schema::table('tasks', function (Blueprint $table) {
                if (!Schema::hasColumn('tasks', 'repeat')) {
                    $table->string('repeat')->default('none');
                }
                if (!Schema::hasColumn('tasks', 'parent_id')) {
                    $table->uuid('parent_id')->nullable();
                    $table->foreign('parent_id')->references('id')->on('tasks')->onDelete('set null');
                }
                if (!Schema::hasColumn('tasks', 'completed')) {
                    $table->boolean('completed')->default(false);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
        });
    }
};
