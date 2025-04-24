<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'skill_id',
        'message',
        'status', // pending, accepted, rejected
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}