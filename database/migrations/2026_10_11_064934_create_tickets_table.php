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
        Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('category_id')->constrained();
        $table->string('subject');
        $table->text('description');
        $table->string('status')->default('open')->index();
        $table->string('priority')->default('medium')->index();
        $table->timestamp('due_at')->nullable();
        $table->timestamp('resolved_at')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
