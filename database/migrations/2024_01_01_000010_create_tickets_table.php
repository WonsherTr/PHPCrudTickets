<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('OPEN');       // OPEN | IN_PROGRESS | RESOLVED | CLOSED
            $table->string('priority')->default('MEDIUM');    // LOW | MEDIUM | HIGH | URGENT
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
