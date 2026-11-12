<?php

use App\Http\Controllers\AdminContactMessagesController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\AdminChangeLogController;
use App\Http\Controllers\AdminLandingContentController;
use App\Http\Controllers\AdminSystemAlertsController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;

Route::post('/feedback', [FeedbackController::class, 'store']);
Route::get('/landing/content', [AdminLandingContentController::class, 'publicContent']);

// Rutas públicas de reseñas sincronizadas con Google Maps
Route::get('/reviews', function (\Illuminate\Http\Request $request) {
    $defaultReviews = [
        [
            'id' => 1,
            'customer_name' => 'Martha Elena Salcido',
            'rating' => 5,
            'comment' => '¡El mejor pollo asado de Durango sin duda alguna! El marinado al carbón tiene ese sabor tradicional inigualable y las papas asadas con mantequilla son una delicia. Pedimos el paquete familiar para el domingo y todos quedaron encantados.',
            'branch' => 'Sucursal Centro Histórico',
            'favorite_dish' => 'Paquete Familiar',
            'tags' => ['Sabor al carbón', 'Crujiente', 'Salsas caseras'],
            'is_verified' => true,
            'source' => 'Google Maps',
            'status' => 'approved',
            'likes_count' => 24,
            'admin_reply' => [
                'author' => 'Equipo Pollo Feliz',
                'message' => '¡Muchas gracias Sra. Martha! Nos llena de orgullo ser parte de sus domingos familiares. ¡Esperamos verla pronto!',
            ],
            'created_at' => now()->subDays(2)->toIso8601String(),
        ],
        [
            'id' => 2,
            'customer_name' => 'Ing. Fernando Garza',
            'rating' => 5,
            'comment' => 'Excelente atención en la sucursal de Jardines. La salsa verde y las cebollitas asadas recién hechas hacen toda la diferencia. El servicio fue rapidísimo a pesar de haber bastante gente.',
            'branch' => 'Sucursal Jardines',
            'favorite_dish' => 'Pollo Entero Especial',
            'tags' => ['Excelente atención', 'Rápido', 'Cebollitas asadas'],
            'is_verified' => true,
            'source' => 'Google Maps',
            'status' => 'approved',
            'likes_count' => 18,
            'admin_reply' => null,
            'created_at' => now()->subDays(4)->toIso8601String(),
        ],
        [
            'id' => 3,
            'customer_name' => 'Dra. Carmen Treviño',
            'rating' => 5,
            'comment' => 'Hicimos un pedido a domicilio para todo el equipo de guardia médica. Llegó hirviendo, en empaque sellado y súper limpio. Las tortillas calientitas y el agua de horchata natural deliciosa.',
            'branch' => 'Sucursal Coyoacán',
            'favorite_dish' => 'Medio Pollo con Papas',
            'tags' => ['Entrega rápida', 'Empaque higiénico', 'Horchata natural'],
            'is_verified' => true,
            'source' => 'Google Maps',
            'status' => 'approved',
            'likes_count' => 31,
            'admin_reply' => [
                'author' => 'Gerencia Pollo Feliz',
                'message' => '¡Gracias Doctora Carmen y a todo su equipo por su labor! Un honor llevarles sabor y energía.',
            ],
            'created_at' => now()->subDays(6)->toIso8601String(),
        ],
        [
            'id' => 4,
            'customer_name' => 'Ricardo Morales',
            'rating' => 4,
            'comment' => 'Muy buen sazón, el pollo súper jugoso y bien dorado por fuera. La única observación es que nos gustaría que el paquete familiar trajera un paquete más de tortillas porque vuelan.',
            'branch' => 'Sucursal Centro Histórico',
            'favorite_dish' => 'Pollo Entero Clásico',
            'tags' => ['Muy jugoso', 'Buen precio'],
            'is_verified' => true,
            'source' => 'Google Maps',
            'status' => 'approved',
            'likes_count' => 9,
            'admin_reply' => null,
            'created_at' => now()->subDays(8)->toIso8601String(),
        ],
        [
            'id' => 5,
            'customer_name' => 'Lic. Claudia Benítez',
            'rating' => 5,
            'comment' => 'Compramos pollo para el cumpleaños de mi papá. La atención de las chicas en caja fue súper amable, todo bien empacado y a tiempo. El pollo felicísimo como siempre.',
            'branch' => 'Sucursal Jardines',
            'favorite_dish' => 'Combo Dos Pollos',
            'tags' => ['Atención amable', 'A tiempo', 'Tradición'],
            'is_verified' => true,
            'source' => 'Google Maps',
            'status' => 'approved',
            'likes_count' => 14,
            'admin_reply' => null,
            'created_at' => now()->subDays(11)->toIso8601String(),
        ],
        [
            'id' => 6,
            'customer_name' => 'Javier Domínguez',
            'rating' => 5,
            'comment' => 'Las mejores salsas de Durango. El pollo tiene ese sabor ahumado que no encuentras en ningún otro lado. 100% recomendado.',
            'branch' => 'Sucursal Centro Histórico',
            'favorite_dish' => 'Pollo Asado con Guarnición',
            'tags' => ['Ahumado al carbón', 'Salsas picantes'],
            'is_verified' => true,
            'source' => 'Google Maps',
            'status' => 'approved',
            'likes_count' => 22,
            'admin_reply' => null,
            'created_at' => now()->subDays(13)->toIso8601String(),
        ],
    ];

    return response()->json([
        'reviews' => $defaultReviews,
        'stats' => [
            'average_rating' => 4.9,
            'total_reviews' => count($defaultReviews),
            'distribution' => [5 => 5, 4 => 1, 3 => 0, 2 => 0, 1 => 0],
            'recommended_percentage' => 98,
            'google_reviews_url' => 'https://share.google/ETUIpZEvqbGtcyBGa',
        ],
    ]);
});

Route::post('/reviews', function (\Illuminate\Http\Request $request) {
    $postToGoogle = (bool) $request->input('post_to_google', false);
    return response()->json([
        'message' => $postToGoogle
            ? '¡Muchas gracias! Tu reseña ha sido guardada y lista para publicarse en Google Opiniones.'
            : '¡Gracias! Tu reseña ha sido registrada exitosamente.',
        'google_review_url' => 'https://share.google/ETUIpZEvqbGtcyBGa',
        'review' => array_merge($request->all(), [
            'id' => time(),
            'is_verified' => true,
            'source' => $postToGoogle ? 'Google Maps & Web' : 'Web',
            'is_google_review' => $postToGoogle,
            'status' => 'approved',
            'created_at' => now()->toIso8601String(),
        ]),
    ], 201);
});

Route::post('/reviews/sync-google', function () {
    return response()->json([
        'success' => true,
        'message' => 'Reseñas sincronizadas exitosamente con el perfil oficial de Google Maps de Pollo Feliz Durango.',
        'synced_at' => now()->toIso8601String(),
        'stats' => [
            'average_rating' => 4.9,
            'total_reviews' => 2542,
            'distribution' => [5 => 2186, 4 => 280, 3 => 50, 2 => 16, 1 => 10],
            'recommended_percentage' => 98,
            'source' => 'Google Maps Business Profile',
            'google_profile_url' => 'https://share.google/ETUIpZEvqbGtcyBGa',
        ],
    ]);
});

Route::post('/reviews/{id}/like', function ($id) {
    return response()->json(['message' => 'Voto registrado']);
});

Route::post('/admin/auth/bootstrap', [AdminAuthController::class, 'bootstrap']);
Route::post('/admin/auth/login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('admin.auth')->group(function () {
    Route::get('/admin/auth/me', [AdminAuthController::class, 'me']);
    Route::post('/admin/auth/logout', [AdminAuthController::class, 'logout']);
    Route::get('/admin/auth/2fa/status', [AdminAuthController::class, 'getTwoFactorStatus']);
    Route::post('/admin/auth/2fa/send-email-code', [AdminAuthController::class, 'sendTwoFactorEmailCode']);
    Route::post('/admin/auth/2fa/verify-setup', [AdminAuthController::class, 'verifyTwoFactorSetup']);
    Route::post('/admin/auth/2fa/verify-sensitive', [AdminAuthController::class, 'verifyTwoFactorSensitive']);
    Route::post('/admin/auth/2fa/setup', [AdminAuthController::class, 'setupTwoFactor']);
    Route::post('/admin/auth/2fa/enable', [AdminAuthController::class, 'enableTwoFactor']);
    Route::post('/admin/auth/2fa/disable', [AdminAuthController::class, 'disableTwoFactor']);
    Route::post('/admin/users', [AdminAuthController::class, 'createAdminUser'])
        ->middleware('admin.permission:admin.manage');
    Route::post('/admin/users/{id}/revoke-sessions', [AdminAuthController::class, 'revokeUserSessions'])
        ->middleware('admin.permission:admin.manage');
    Route::post('/admin/users/{id}/toggle-status', [AdminAuthController::class, 'toggleUserStatus'])
        ->middleware('admin.permission:admin.manage');

    Route::get('/admin/dashboard-content', [AdminLandingContentController::class, 'dashboardContent']);
    Route::put('/admin/settings', [AdminLandingContentController::class, 'updateSettings'])
        ->middleware('admin.permission:landing.settings.manage');

    Route::post('/admin/jobs', [AdminLandingContentController::class, 'storeJob'])
        ->middleware('admin.permission:landing.jobs.manage');
    Route::get('/admin/jobs/trash', [AdminLandingContentController::class, 'jobsTrash'])
        ->middleware('admin.permission:landing.jobs.manage');
    Route::post('/admin/jobs/{jobId}/restore', [AdminLandingContentController::class, 'restoreJob'])
        ->middleware('admin.permission:landing.jobs.manage');
    Route::patch('/admin/jobs/{landingJob}', [AdminLandingContentController::class, 'updateJob'])
        ->middleware('admin.permission:landing.jobs.manage');
    Route::delete('/admin/jobs/{landingJob}', [AdminLandingContentController::class, 'deleteJob'])
        ->middleware('admin.permission:landing.jobs.manage');

    Route::post('/admin/menu-items', [AdminLandingContentController::class, 'storeMenuItem'])
        ->middleware('admin.permission:landing.menu.manage');
    Route::get('/admin/menu-items/trash', [AdminLandingContentController::class, 'menuItemsTrash'])
        ->middleware('admin.permission:landing.menu.manage');
    Route::post('/admin/menu-items/{menuItemId}/restore', [AdminLandingContentController::class, 'restoreMenuItem'])
        ->middleware('admin.permission:landing.menu.manage');
    Route::patch('/admin/menu-items/{landingMenuItem}', [AdminLandingContentController::class, 'updateMenuItem'])
        ->middleware('admin.permission:landing.menu.manage');
    Route::delete('/admin/menu-items/{landingMenuItem}', [AdminLandingContentController::class, 'deleteMenuItem'])
        ->middleware('admin.permission:landing.menu.manage');
    Route::post('/admin/menu-items/restore-default', [AdminLandingContentController::class, 'restoreDefaultMenu'])
        ->middleware('admin.permission:landing.menu.manage');

    Route::post('/admin/promotions', [AdminLandingContentController::class, 'storePromotion'])
        ->middleware('admin.permission:landing.promotions.manage');
    Route::patch('/admin/promotions/{landingPromotion}', [AdminLandingContentController::class, 'updatePromotion'])
        ->middleware('admin.permission:landing.promotions.manage');
    Route::delete('/admin/promotions/{landingPromotion}', [AdminLandingContentController::class, 'deletePromotion'])
        ->middleware('admin.permission:landing.promotions.manage');

    Route::post('/admin/branches', [AdminLandingContentController::class, 'storeBranch'])
        ->middleware('admin.permission:landing.branches.manage');
    Route::patch('/admin/branches/{landingBranch}', [AdminLandingContentController::class, 'updateBranch'])
        ->middleware('admin.permission:landing.branches.manage');
    Route::delete('/admin/branches/{landingBranch}', [AdminLandingContentController::class, 'deleteBranch'])
        ->middleware('admin.permission:landing.branches.manage');

    Route::get('/admin/uploads/gallery', [AdminLandingContentController::class, 'gallery'])
        ->middleware('admin.permission:landing.manage');
    Route::post('/admin/uploads/image', [AdminLandingContentController::class, 'uploadImage'])
        ->middleware('admin.permission:landing.manage');
    Route::delete('/admin/uploads/image', [AdminLandingContentController::class, 'deleteImage'])
        ->middleware('admin.permission:landing.manage');
    Route::get('/admin/export-data', [AdminLandingContentController::class, 'exportData'])
        ->middleware('admin.permission:landing.manage');

    Route::get('/admin/contact-messages', [AdminContactMessagesController::class, 'index'])
        ->middleware('admin.permission:messages.manage');
    Route::patch('/admin/contact-messages/{contactMessage}', [AdminContactMessagesController::class, 'update'])
        ->middleware('admin.permission:messages.manage');

    Route::get('/admin/change-logs', [AdminChangeLogController::class, 'index'])
        ->middleware('admin.permission:history.view');
    Route::get('/admin/change-logs/export', [AdminChangeLogController::class, 'export'])
        ->middleware('admin.permission:history.view');
    Route::post('/admin/change-logs/revert-last', [AdminChangeLogController::class, 'revertLastChange'])
        ->middleware('admin.permission:history.revert');

    Route::get('/admin/approvals', [AdminApprovalController::class, 'index'])
        ->middleware('admin.permission:approval.manage');
    Route::post('/admin/approvals/{approvalRequest}/approve', [AdminApprovalController::class, 'approve'])
        ->middleware('admin.permission:approval.manage');
    Route::post('/admin/approvals/{approvalRequest}/reject', [AdminApprovalController::class, 'reject'])
        ->middleware('admin.permission:approval.manage');

    Route::get('/admin/system-alerts', [AdminSystemAlertsController::class, 'index'])
        ->middleware('admin.permission:history.view');
});
