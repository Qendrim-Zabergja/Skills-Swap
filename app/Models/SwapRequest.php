<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SwapRequest extends Model
{
    use HasFactory;

    protected $table = 'swap_requests';

    protected $fillable = [
        'user_id',
        'recipient_id',
        'skill_wanted',
        'skill_offered',
        'message',
        'availability',
        'duration',
        'status'
    ];

    protected $attributes = [
        'status' => 'Pending'
    ];

    /**
     * Get the route key name for Laravel's implicit model binding.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the user who sent the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who received the request.
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Get the rating associated with this request.
     */
    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class, 'request_id');
    }
}
