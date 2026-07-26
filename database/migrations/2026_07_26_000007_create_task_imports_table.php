<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_imports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('original_file_name');
            $table->string('file_path');
            $table->json('column_mapping');
            $table->string('status')->default('pending'); // pending|processing|completed|failed
            $table->unsignedInteger('total_rows')->nullable();
            $table->unsignedInteger('imported_rows')->nullable();
            $table->unsignedInteger('failed_rows')->nullable();
            $table->json('errors')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_imports');
    }
};
