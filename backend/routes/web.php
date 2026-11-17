<?php

use App\Http\Controllers\LandingController;
use App\Models\AdminApprovalRequest;
use App\Models\AdminChangeLog;
use App\Models\ContactMessage;
use App\Models\LandingBranch;
use App\Models\LandingJob;
use App\Models\LandingMenuItem;
use App\Models\LandingPromotion;
use App\Models\LandingSetting;
use App\Models\Location;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

// Ruta principal dinámica con carga de sucursales, menú, promociones y vacantes
Route::get('/', [LandingController::class, 'index']);
Route::get('/landing', [LandingController::class, 'index']);

// Endpoint público del catálogo de menú consultado directamente desde Eloquent ORM
Route::get('/api/menu', function () {
    $items = collect([]);
    if (Schema::hasTable('landing_menu_items')) {
        $items = LandingMenuItem::query()
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'description', 'category', 'price', 'image_url', 'badge', 'is_featured', 'is_sold_out']);
    }

    return response()->json([
        'message' => 'Menú disponible',
        'items' => $items,
    ]);
});

// Panel administrativo Blade de respaldo con métricas reales desde base de datos
Route::get('/admin/panel', function () {
    $safeCount = static function (string $table, callable $query): int {
        try {
            return Schema::hasTable($table) ? (int) $query() : 0;
        } catch (\Throwable) {
            return 0;
        }
    };

    $settings = Schema::hasTable('landing_settings') ? LandingSetting::toKeyValueMap() : [];

    $stats = [
        'settings' => $safeCount('landing_settings', fn () => LandingSetting::count()),
        'menu_items' => $safeCount('landing_menu_items', fn () => LandingMenuItem::count()),
        'promotions' => $safeCount('landing_promotions', fn () => LandingPromotion::count()),
        'branches' => Schema::hasTable('locations')
            ? $safeCount('locations', fn () => Location::count())
            : $safeCount('landing_branches', fn () => LandingBranch::count()),
        'jobs' => $safeCount('landing_jobs', fn () => LandingJob::count()),
        'orders' => 0,
        'reviews' => $safeCount('reviews', fn () => Review::count()),
        'messages' => $safeCount('contact_messages', fn () => ContactMessage::count()),
        'logs' => $safeCount('admin_change_logs', fn () => AdminChangeLog::count()),
        'users' => $safeCount('users', fn () => User::where('is_admin', true)->count()),
        'approvals' => $safeCount('admin_approval_requests', fn () => AdminApprovalRequest::count()),
    ];

    return view('admin.dashboard', [
        'settings' => $settings,
        'stats' => $stats,
    ]);
});

// Enrutador de respaldo para Single Page Application (SPA /admin, /menu, etc.)
Route::get('/{any}', function () {
    if (file_exists(public_path('index.html'))) {
        return response(file_get_contents(public_path('index.html')))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    return app(LandingController::class)->index();
})->where('any', '^(?!api/).*');
