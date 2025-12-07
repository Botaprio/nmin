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
        Schema::create('ai_credits', function (Blueprint $table) {
            $table->id();
            $table->enum('service', ['kling', 'midjourney', 'suno']);
            $table->integer('total_credits')->default(0);
            $table->integer('used_credits')->default(0);
            $table->integer('remaining_credits')->default(0);
            $table->date('billing_period_start')->nullable();
            $table->date('billing_period_end')->nullable();
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_credits');
    }
};
