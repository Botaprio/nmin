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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('card_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('board_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('action'); // created, updated, moved, deleted, commented, assigned
            $table->text('description');
            $table->json('metadata')->nullable(); // Additional data like old/new values
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
