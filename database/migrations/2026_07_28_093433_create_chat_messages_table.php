<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the `chat_messages` table.
 *
 * Stores individual messages within an AI copilot conversation.
 * The `sender` column distinguishes between the doctor's prompts
 * and the AI assistant's responses.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table): void {
            $table->id();

            // Parent conversation session
            $table->foreignId('conversation_id')
                  ->constrained('chat_conversations')
                  ->cascadeOnDelete();

            // Who sent this message: the doctor or the AI assistant
            $table->enum('sender', ['doctor', 'ai']);

            // The message text content
            $table->text('content');

            $table->timestamps();

            $table->index('conversation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
