<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Message;
use App\Models\SkillRequest;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'location',
        'title',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['profile_photo_url', 'average_rating'];

    /**
     * Get the user's profile photo URL.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            $path = 'storage/' . $this->profile_photo_path;
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the skills that the user can teach.
     */
    public function teachingSkills(): HasMany
    {
        return $this->hasMany(Skill::class)->where('type', 'teach');
    }

    /**
     * Get the skills that the user wants to learn.
     */
    public function learningSkills(): HasMany
    {
        return $this->hasMany(Skill::class)->where('type', 'learn');
    }

    /**
     * Get all skills associated with the user.
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    /**
     * Get the swap requests sent by the user.
     */
    public function sentRequests(): HasMany
    {
        return $this->hasMany(SwapRequest::class, 'user_id');
    }

    /**
     * Get the swap requests received by the user.
     */
    public function receivedRequests(): HasMany
    {
        return $this->hasMany(SwapRequest::class, 'recipient_id');
    }

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'user_id');
    }

    /**
     * Get the messages received by the user.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    /**
     * Get the skill requests sent by the user.
     */
    public function sentSkillRequests()
    {
        return $this->hasMany(SkillRequest::class, 'sender_id');
    }

    /**
     * Get the skill requests received by the user.
     */
    public function receivedSkillRequests()
    {
        return $this->hasMany(SkillRequest::class, 'recipient_id');
    }

    /**
     * Get ratings given by the user.
     */
    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    /**
     * Get ratings received by the user.
     */
    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'rated_user_id');
    }

    /**
     * Get the user's average rating.
     */
    public function getAverageRatingAttribute()
    {
        $avg = $this->receivedRatings()->avg('score');
        return $avg ? round($avg, 1) : null;
    }
}