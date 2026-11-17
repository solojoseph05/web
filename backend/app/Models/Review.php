<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para las reseñas y calificaciones de clientes (web y Google Maps).
 */
class Review extends Model
{
    protected $table = 'reviews';

    /**
     * Atributos asignables en masa para reseñas moderadas y métricas sociales.
     */
    protected $fillable = [
        'customer_name',
        'rating',
        'comment',
        'branch',
        'favorite_dish',
        'tags',
        'is_verified',
        'source',
        'is_google_review',
        'status',
        'likes_count',
        'admin_reply',
    ];

    /**
     * Conversiones automáticas de tipos nativos para JSON y banderas booleanas.
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'tags' => 'array',
            'is_verified' => 'boolean',
            'is_google_review' => 'boolean',
            'likes_count' => 'integer',
            'admin_reply' => 'array',
        ];
    }
}
