<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('projects', function (Blueprint $table) {
        $table->id();
        $table->string('name', 150);
        $table->text('description')->nullable();
        $table->foreignId('project_manager_id')->constrained('users');
        $table->date('start_date')->nullable();
        $table->date('deadline')->nullable();
        $table->enum('status', ['planned', 'active', 'on_hold', 'completed'])->default('planned');
        $table->timestamps();
        $table->softDeletes();
    });
}

public function down(): void
{
    Schema::dropIfExists('projects');
}
};
