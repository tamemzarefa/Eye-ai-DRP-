<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the `chat_conversations` table.
 *
 * Each row represents a single AI copilot chat session started by
 * an authenticated doctor. The title is optional and can be auto-generated
 * from the first message.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table): void {
            $table->id();

            // The doctor who owns this conversation
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Optional auto-generated or user-defined title for the session
            $table->string('title', 255)->nullable();

            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
