<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'category',
        'user_id'
    ];

    protected $casts = [
        'type' => 'string'
    ];

    /**
     * Get the user that owns the skill.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}