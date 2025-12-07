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
        Schema::create('google_drive_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained()->onDelete('cascade');
            $table->string('drive_file_id'); // Google Drive file ID
            $table->string('name');
            $table->string('mime_type');
            $table->string('file_type')->nullable(); // thumbnail, script, asset, final_video
            $table->bigInteger('size')->nullable();
            $table->string('web_view_link')->nullable();
            $table->string('web_content_link')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_drive_files');
    }
};
