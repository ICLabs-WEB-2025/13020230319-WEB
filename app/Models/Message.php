<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['sender_type', 'sender_id', 'receiver_id', 'message', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}