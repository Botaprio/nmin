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
        Schema::create('youtube_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained()->onDelete('cascade');
            $table->string('youtube_video_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'published', 'unlisted', 'private'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->bigInteger('view_count')->default(0);
            $table->bigInteger('like_count')->default(0);
            $table->bigInteger('comment_count')->default(0);
            $table->integer('watch_time_minutes')->default(0);
            $table->timestamp('analytics_last_updated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('youtube_videos');
    }
};
