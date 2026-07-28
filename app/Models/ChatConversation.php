<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ChatConversation — An AI copilot chat session for a doctor.
 *
 * Each conversation belongs to an authenticated doctor and holds
 * an ordered list of messages exchanged with the AI assistant.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string|null $title
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
    ];

    // ─── Relationships ────────────────────────────────────────────

    /** The doctor who owns this conversation. */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** All messages in this conversation, ordered chronologically. */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')
                    ->orderBy('created_at');
    }
}
