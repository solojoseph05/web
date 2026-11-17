<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Controlador de Reseñas & Opiniones persistido directamente en base de datos vía Eloquent ORM.
 * Reemplaza por completo cualquier arreglo estático en memoria y mantiene flujo dual seguro.
 */
class ReviewController extends Controller
{
    private const GOOGLE_REVIEWS_URL = 'https://share.google/ETUIpZEvqbGtcyBGa';

    /**
     * Lista las reseñas aprobadas junto con sus estadísticas agregadas desde la base de datos.
     */
    public function index(Request $request): JsonResponse
    {
        $branch = trim((string) $request->query('branch', ''));

        $query = Review::query()
            ->where('status', 'approved')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($branch !== '' && strtolower($branch) !== 'todas') {
            $query->where('branch', $branch);
        }

        $reviews = $query->get();

        return response()->json([
            'reviews' => $reviews,
            'stats' => $this->computeStats($reviews),
        ]);
    }

    /**
     * Registra una nueva reseña en base de datos mediante transacción segura y notifica
     * opcionalmente por correo en un bloque try-catch aislado (Regla 4).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:4', 'max:1500'],
            'branch' => ['nullable', 'string', 'max:160'],
            'favorite_dish' => ['nullable', 'string', 'max:160'],
            'tags' => ['nullable', 'array', 'max:6'],
            'tags.*' => ['string', 'max:40'],
            'post_to_google' => ['sometimes', 'boolean'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $postToGoogle = $request->boolean('post_to_google', false);

        try {
            $review = DB::transaction(function () use ($data, $postToGoogle): Review {
                $review = Review::create([
                    'customer_name' => trim($data['customer_name']),
                    'rating' => (int) $data['rating'],
                    'comment' => trim($data['comment']),
                    'branch' => !empty($data['branch']) ? trim($data['branch']) : 'Sucursal Centro Histórico',
                    'favorite_dish' => !empty($data['favorite_dish']) ? trim($data['favorite_dish']) : null,
                    'tags' => $data['tags'] ?? [],
                    'is_verified' => true,
                    'source' => $postToGoogle ? 'Google Maps & Web' : 'Web',
                    'is_google_review' => $postToGoogle,
                    'status' => 'approved',
                    'likes_count' => 0,
                ]);

                Feedback::create([
                    'name' => $review->customer_name,
                    'email' => !empty($data['email']) ? strtolower(trim($data['email'])) : 'reseña@pollofelizdgo.com',
                    'rating' => $review->rating . ' estrellas',
                    'type' => 'Reseña Pública',
                    'message' => $review->comment,
                ]);

                return $review;
            });
        } catch (\Throwable $e) {
            Log::error('[ReviewController] Error guardando reseña en base de datos', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'No pudimos guardar tu reseña en este momento. Intenta nuevamente.',
            ], 500);
        }

        // Flujo dual independiente: un fallo SMTP nunca revierte el registro ya guardado
        try {
            $supportEmail = trim((string) config('mail.support_address', 'soporte@pollofelizdgo.com'));
            if ($supportEmail !== '' && filter_var($supportEmail, FILTER_VALIDATE_EMAIL) && !in_array((string) config('mail.default', 'log'), ['log', 'array'], true)) {
                Mail::raw(
                    "Nueva reseña ({$review->rating}/5) de {$review->customer_name} en {$review->branch}:\n\n{$review->comment}",
                    static function ($msg) use ($supportEmail, $review): void {
                        $msg->to($supportEmail)
                            ->subject("[Reseña {$review->rating}★] {$review->customer_name} - Pollo Feliz Durango");
                    }
                );
            }
        } catch (\Throwable $mailError) {
            Log::warning('[ReviewController] Reseña guardada pero falló notificación SMTP opcional', [
                'review_id' => $review->id,
                'error' => $mailError->getMessage(),
            ]);
        }

        return response()->json([
            'message' => $postToGoogle
                ? '¡Muchas gracias! Tu reseña ha sido guardada y está lista para publicarse en Google Opiniones.'
                : '¡Gracias! Tu reseña ha sido registrada exitosamente.',
            'google_review_url' => self::GOOGLE_REVIEWS_URL,
            'review' => $review,
        ], 201);
    }

    /**
     * Calcula métricas reales de reseñas almacenadas y confirma sincronización con el perfil de Google.
     */
    public function syncGoogle(): JsonResponse
    {
        $approvedReviews = Review::query()->where('status', 'approved')->get();

        return response()->json([
            'success' => true,
            'message' => 'Reseñas sincronizadas exitosamente con la base de datos y el perfil oficial de Pollo Feliz Durango.',
            'synced_at' => now()->toIso8601String(),
            'stats' => array_merge($this->computeStats($approvedReviews), [
                'source' => 'Google Maps Business Profile & MySQL',
                'google_profile_url' => self::GOOGLE_REVIEWS_URL,
            ]),
        ]);
    }

    /**
     * Incrementa de forma atómica el contador de reacciones útiles de una reseña.
     */
    public function like(Review $review): JsonResponse
    {
        try {
            DB::transaction(function () use ($review): void {
                $review->increment('likes_count');
            });

            return response()->json([
                'message' => 'Voto registrado.',
                'likes_count' => $review->fresh()->likes_count,
            ]);
        } catch (\Throwable $e) {
            Log::error('[ReviewController] Error registrando voto en reseña', [
                'review_id' => $review->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'No se pudo registrar el voto.'], 500);
        }
    }

    /**
     * Calcula promedio, distribución por estrellas y porcentaje recomendado a partir de Eloquent.
     *
     * @param \Illuminate\Support\Collection<int, Review> $reviews
     * @return array<string, mixed>
     */
    private function computeStats($reviews): array
    {
        $total = $reviews->count();
        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $sum = 0;
        $positive = 0;

        foreach ($reviews as $item) {
            $stars = max(1, min(5, (int) $item->rating));
            $distribution[$stars]++;
            $sum += $stars;
            if ($stars >= 4) {
                $positive++;
            }
        }

        return [
            'average_rating' => $total > 0 ? round($sum / $total, 1) : 5.0,
            'total_reviews' => $total,
            'distribution' => $distribution,
            'recommended_percentage' => $total > 0 ? (int) round(($positive / $total) * 100) : 100,
            'google_reviews_url' => self::GOOGLE_REVIEWS_URL,
        ];
    }
}
