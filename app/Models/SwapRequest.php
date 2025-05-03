<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SwapRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_id',
        'skill_wanted',
        'skill_offered',
        'status',
    ];

    /**
     * Get the user who sent the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who received the request.
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
