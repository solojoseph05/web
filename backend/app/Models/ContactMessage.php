<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para almacenar mensajes y consultas enviadas desde la Landing Page.
 */
class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'message',
        'phone',
        'source',
        'status',
        'notes',
    ];
}
