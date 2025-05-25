<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'learning_skill',
        'teaching_skill',
        'message',
        'availability',
        'duration',
        'status'
    ];

    protected $attributes = [
        'status' => 'Pending'
    ];

    protected $casts = [
        'learning_skill' => 'string',
        'teaching_skill' => 'string'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
