<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para retroalimentación y reseñas rápidas enviadas por clientes.
 */
class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = ['name', 'email', 'rating', 'type', 'message'];
}
