<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// Garantizar existencia y permisos de .laravel/cache y bootstrap/cache
foreach ([
    '/var/www/workspace/backend/.laravel/cache',
    dirname(__DIR__) . '/.laravel/cache',
    __DIR__ . '/cache',
    dirname(__DIR__) . '/storage/framework/cache/data',
    dirname(__DIR__) . '/storage/framework/sessions',
    dirname(__DIR__) . '/storage/framework/views',
] as $cachePath) {
    if (!is_dir($cachePath)) {
        @mkdir($cachePath, 0777, true);
    }
    @chmod($cachePath, 0777);
}

if (!getenv('APP_PACKAGES_CACHE')) {
    $bootstrapCache = __DIR__ . '/cache';
    putenv('APP_PACKAGES_CACHE=' . $bootstrapCache . '/packages.php');
    putenv('APP_SERVICES_CACHE=' . $bootstrapCache . '/services.php');
    putenv('APP_CONFIG_CACHE=' . $bootstrapCache . '/config.php');
    putenv('APP_ROUTES_CACHE=' . $bootstrapCache . '/routes-v7.php');
    putenv('APP_EVENTS_CACHE=' . $bootstrapCache . '/events.php');
    $_ENV['APP_PACKAGES_CACHE'] = $bootstrapCache . '/packages.php';
    $_SERVER['APP_PACKAGES_CACHE'] = $bootstrapCache . '/packages.php';
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminTokenMiddleware::class,
            'admin.permission' => \App\Http\Middleware\AdminPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
