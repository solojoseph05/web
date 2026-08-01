<?php

namespace App\Http\Controllers;

use App\Models\AdminChangeLog;
use App\Models\ContactMessage;
use App\Models\LandingBranch;
use App\Models\LandingJob;
use App\Models\LandingMenuItem;
use App\Models\LandingPromotion;
use App\Models\LandingSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class AdminLandingContentController extends Controller
{
    private const SETTING_KEYS = [
        'hero_title',
        'hero_description',
        'hero_image_url',
        'company_address',
    ];

    private const DEFAULT_SETTINGS = [
        'hero_title' => 'El mejor Pollo Asado de Durango',
        'hero_description' => 'Descubre el sabor que nos hace unicos. Pollo asado con receta especial y tradicion familiar.',
        'hero_image_url' => null,
        'company_address' => 'Av. Principal #123, Centro, Durango',
    ];

    public function publicContent()
    {
        return response()->json($this->composePayload(true));
    }

    public function dashboardContent()
    {
        return response()->json($this->composePayload(false));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'hero_title' => ['required', 'string', 'max:160'],
            'hero_description' => ['required', 'string', 'max:1000'],
            'hero_image_url' => ['nullable', 'string', 'max:600'],
            'company_address' => ['required', 'string', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            LandingSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->logAdminChange(
            $request,
            'landing.settings.update',
            'LandingSetting',
            null,
            'Se actualizaron los ajustes principales del landing.',
            $data
        );

        return response()->json([
            'message' => 'Configuracion del landing actualizada.',
            'settings' => $this->getSettings(),
        ]);
    }

    public function storeJob(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:2000'],
            'employment_type' => ['required', 'string', 'max:80'],
            'location' => ['nullable', 'string', 'max:160'],
            'schedule' => ['nullable', 'string', 'max:120'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $job = LandingJob::create([
            ...$data,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $this->logAdminChange(
            $request,
            'landing.job.create',
            'LandingJob',
            (string) $job->id,
            'Se creó una nueva vacante.',
            $job->toArray()
        );

        return response()->json([
            'message' => 'Vacante creada correctamente.',
            'job' => $job,
        ], 201);
    }

    public function updateJob(Request $request, LandingJob $landingJob)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:2000'],
            'employment_type' => ['required', 'string', 'max:80'],
            'location' => ['nullable', 'string', 'max:160'],
            'schedule' => ['nullable', 'string', 'max:120'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $landingJob->fill($data);
        $landingJob->save();

        $this->logAdminChange(
            $request,
            'landing.job.update',
            'LandingJob',
            (string) $landingJob->id,
            'Se actualizó una vacante.',
            $landingJob->toArray()
        );

        return response()->json([
            'message' => 'Vacante actualizada correctamente.',
            'job' => $landingJob->fresh(),
        ]);
    }

    public function deleteJob(Request $request, LandingJob $landingJob)
    {
        $snapshot = $landingJob->toArray();
        $landingJob->delete();

        $this->logAdminChange(
            $request,
            'landing.job.delete',
            'LandingJob',
            (string) ($snapshot['id'] ?? ''),
            'Se eliminó una vacante.',
            $snapshot
        );

        return response()->json([
            'message' => 'Vacante eliminada correctamente.',
        ]);
    }

    public function jobsTrash()
    {
        return response()->json([
            'jobs' => LandingJob::onlyTrashed()->orderByDesc('deleted_at')->get(),
        ]);
    }

    public function restoreJob(Request $request, string $jobId)
    {
        $job = LandingJob::withTrashed()->findOrFail($jobId);
        $job->restore();

        $this->logAdminChange(
            $request,
            'landing.job.restore',
            'LandingJob',
            (string) $job->id,
            'Se restauró una vacante desde la papelera.',
            $job->toArray()
        );

        return response()->json([
            'message' => 'Vacante restaurada correctamente.',
            'job' => $job->fresh(),
        ]);
    }

    public function storeMenuItem(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'badge' => ['nullable', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $item = LandingMenuItem::create([
            ...$data,
            'is_featured' => $data['is_featured'] ?? true,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $this->logAdminChange(
            $request,
            'landing.menu.create',
            'LandingMenuItem',
            (string) $item->id,
            'Se creó un nuevo producto del menú.',
            $item->toArray()
        );

        return response()->json([
            'message' => 'Producto del menu creado correctamente.',
            'item' => $item,
        ], 201);
    }

    public function updateMenuItem(Request $request, LandingMenuItem $landingMenuItem)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'badge' => ['nullable', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'is_featured' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $landingMenuItem->fill($data);
        $landingMenuItem->save();

        $this->logAdminChange(
            $request,
            'landing.menu.update',
            'LandingMenuItem',
            (string) $landingMenuItem->id,
            'Se actualizó un producto del menú.',
            $landingMenuItem->toArray()
        );

        return response()->json([
            'message' => 'Producto del menu actualizado correctamente.',
            'item' => $landingMenuItem->fresh(),
        ]);
    }

    public function deleteMenuItem(Request $request, LandingMenuItem $landingMenuItem)
    {
        $snapshot = $landingMenuItem->toArray();
        $landingMenuItem->delete();

        $this->logAdminChange(
            $request,
            'landing.menu.delete',
            'LandingMenuItem',
            (string) ($snapshot['id'] ?? ''),
            'Se eliminó un producto del menú.',
            $snapshot
        );

        return response()->json([
            'message' => 'Producto del menu eliminado correctamente.',
        ]);
    }

    public function menuItemsTrash()
    {
        return response()->json([
            'items' => LandingMenuItem::onlyTrashed()->orderByDesc('deleted_at')->get(),
        ]);
    }

    public function restoreMenuItem(Request $request, string $menuItemId)
    {
        $item = LandingMenuItem::withTrashed()->findOrFail($menuItemId);
        $item->restore();

        $this->logAdminChange(
            $request,
            'landing.menu.restore',
            'LandingMenuItem',
            (string) $item->id,
            'Se restauró un producto del menú desde la papelera.',
            $item->toArray()
        );

        return response()->json([
            'message' => 'Producto restaurado correctamente.',
            'item' => $item->fresh(),
        ]);
    }

    public function storePromotion(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'valid_until' => ['nullable', 'date'],
            'code' => ['nullable', 'string', 'max:80'],
            'badge' => ['nullable', 'string', 'max:80'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $promotion = LandingPromotion::create([
            ...$data,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $this->logAdminChange(
            $request,
            'landing.promotion.create',
            'LandingPromotion',
            (string) $promotion->id,
            'Se creó una nueva promoción.',
            $promotion->toArray()
        );

        return response()->json([
            'message' => 'Promoción creada correctamente.',
            'promotion' => $promotion,
        ], 201);
    }

    public function updatePromotion(Request $request, LandingPromotion $landingPromotion)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'valid_until' => ['nullable', 'date'],
            'code' => ['nullable', 'string', 'max:80'],
            'badge' => ['nullable', 'string', 'max:80'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $landingPromotion->fill($data);
        $landingPromotion->save();

        $this->logAdminChange(
            $request,
            'landing.promotion.update',
            'LandingPromotion',
            (string) $landingPromotion->id,
            'Se actualizó una promoción.',
            $landingPromotion->toArray()
        );

        return response()->json([
            'message' => 'Promoción actualizada correctamente.',
            'promotion' => $landingPromotion->fresh(),
        ]);
    }

    public function deletePromotion(Request $request, LandingPromotion $landingPromotion)
    {
        $snapshot = $landingPromotion->toArray();
        $landingPromotion->delete();

        $this->logAdminChange(
            $request,
            'landing.promotion.delete',
            'LandingPromotion',
            (string) ($snapshot['id'] ?? ''),
            'Se eliminó una promoción.',
            $snapshot
        );

        return response()->json(['message' => 'Promoción eliminada correctamente.']);
    }

    public function storeBranch(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:80'],
            'schedule' => ['nullable', 'string', 'max:120'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'map_link' => ['nullable', 'string', 'max:600'],
            'is_open' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $branch = LandingBranch::create([
            ...$data,
            'is_open' => $data['is_open'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $this->logAdminChange(
            $request,
            'landing.branch.create',
            'LandingBranch',
            (string) $branch->id,
            'Se creó una nueva sucursal.',
            $branch->toArray()
        );

        return response()->json([
            'message' => 'Sucursal creada correctamente.',
            'branch' => $branch,
        ], 201);
    }

    public function updateBranch(Request $request, LandingBranch $landingBranch)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:80'],
            'schedule' => ['nullable', 'string', 'max:120'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'map_link' => ['nullable', 'string', 'max:600'],
            'is_open' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $landingBranch->fill($data);
        $landingBranch->save();

        $this->logAdminChange(
            $request,
            'landing.branch.update',
            'LandingBranch',
            (string) $landingBranch->id,
            'Se actualizó una sucursal.',
            $landingBranch->toArray()
        );

        return response()->json([
            'message' => 'Sucursal actualizada correctamente.',
            'branch' => $landingBranch->fresh(),
        ]);
    }

    public function deleteBranch(Request $request, LandingBranch $landingBranch)
    {
        $snapshot = $landingBranch->toArray();
        $landingBranch->delete();

        $this->logAdminChange(
            $request,
            'landing.branch.delete',
            'LandingBranch',
            (string) ($snapshot['id'] ?? ''),
            'Se eliminó una sucursal.',
            $snapshot
        );

        return response()->json(['message' => 'Sucursal eliminada correctamente.']);
    }

    public function uploadImage(Request $request)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'max:10240'],
            'context' => ['nullable', 'string', 'max:80'],
        ]);

        $context = strtolower($data['context'] ?? 'general');
        $processed = $this->processImageUpload($data['image'], $context);

        $this->logAdminChange(
            $request,
            'landing.image.upload',
            'Media',
            null,
            'Se subió una imagen para el contenido administrable.',
            [
                'context' => $context,
                'path' => $processed['path'],
                'url' => $processed['url'],
            ]
        );

        return response()->json([
            'message' => 'Imagen subida correctamente.',
            'path' => $processed['path'],
            'url' => $processed['url'],
        ], 201);
    }

    public function gallery(Request $request)
    {
        $files = Storage::disk('public')->allFiles('landing');
        $images = collect($files)
            ->filter(fn (string $path) => in_array(Str::lower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true))
            ->map(function (string $path) {
                return [
                    'path' => $path,
                    'url' => Storage::disk('public')->url($path),
                    'name' => basename($path),
                    'context' => $this->inferImageContext($path),
                    'size' => Storage::disk('public')->size($path),
                ];
            })
            ->sortByDesc('path')
            ->values()
            ->all();

        return response()->json(['images' => $images]);
    }

    public function deleteImage(Request $request)
    {
        $data = $request->validate([
            'url' => ['required', 'string', 'max:1200'],
        ]);

        $relativePath = $this->extractStoragePath($data['url']);
        if ($relativePath) {
            Storage::disk('public')->delete($relativePath);
        }

        return response()->json(['message' => 'Imagen removida correctamente.']);
    }

    private function processImageUpload($image, string $context): array
    {
        $sourcePath = $image->getRealPath();
        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo) {
            throw new RuntimeException('La imagen no es válida.');
        }

        $width = (int) $imageInfo[0];
        $height = (int) $imageInfo[1];
        $rules = $this->getImageRules($context);

        if ($width < $rules['min_width'] || $height < $rules['min_height']) {
            throw new RuntimeException('La imagen es demasiado pequeña para este contexto.');
        }

        if ($width > $rules['max_width'] || $height > $rules['max_height']) {
            throw new RuntimeException('La imagen supera el tamaño máximo permitido.');
        }

        $ratio = $width / max(1, $height);
        if ($rules['aspect_ratio'] !== null) {
            $minRatio = $rules['aspect_ratio'] - 0.18;
            $maxRatio = $rules['aspect_ratio'] + 0.18;
            if ($ratio < $minRatio || $ratio > $maxRatio) {
                throw new RuntimeException('La relación de aspecto no es válida para este contexto.');
            }
        }

        $resource = @imagecreatefromstring(file_get_contents($sourcePath));
        if (!$resource) {
            throw new RuntimeException('La imagen no pudo ser procesada.');
        }

        [$targetWidth, $targetHeight] = $this->calculateTargetDimensions($width, $height, $rules['max_width'], $rules['max_height']);
        $optimized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($optimized, false);
        imagesavealpha($optimized, true);
        imagecopyresampled($optimized, $resource, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $fileName = Str::uuid()->toString() . '.webp';
        $path = 'landing/' . $context . '/' . date('Y/m/d') . '/' . $fileName;
        $temporaryHandle = fopen('php://temp', 'r+');
        imagewebp($optimized, $temporaryHandle, 80);
        rewind($temporaryHandle);
        $contents = stream_get_contents($temporaryHandle);
        fclose($temporaryHandle);

        Storage::disk('public')->put($path, $contents);
        imagedestroy($resource);
        imagedestroy($optimized);

        return [
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ];
    }

    private function inferImageContext(string $path): string
    {
        $segments = explode('/', $path);
        return count($segments) >= 2 ? $segments[1] : 'general';
    }

    private function extractStoragePath(string $url): ?string
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        if (str_contains($path, '/storage/')) {
            $path = substr($path, strpos($path, '/storage/') + 9);
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'landing/')) {
            return $path;
        }

        return null;
    }

    private function getImageRules(string $context): array
    {
        return match ($context) {
            'hero' => [
                'min_width' => 900,
                'min_height' => 600,
                'max_width' => 1800,
                'max_height' => 1200,
                'aspect_ratio' => 1.45,
            ],
            'job' => [
                'min_width' => 700,
                'min_height' => 500,
                'max_width' => 1400,
                'max_height' => 1000,
                'aspect_ratio' => 1.2,
            ],
            'menu' => [
                'min_width' => 300,
                'min_height' => 200,
                'max_width' => 1200,
                'max_height' => 900,
                'aspect_ratio' => 1.33,
            ],
            'promotion' => [
                'min_width' => 600,
                'min_height' => 400,
                'max_width' => 1400,
                'max_height' => 900,
                'aspect_ratio' => 1.5,
            ],
            'branch' => [
                'min_width' => 600,
                'min_height' => 400,
                'max_width' => 1400,
                'max_height' => 900,
                'aspect_ratio' => 1.5,
            ],
            default => [
                'min_width' => 400,
                'min_height' => 300,
                'max_width' => 1600,
                'max_height' => 1200,
                'aspect_ratio' => null,
            ],
        };
    }

    private function calculateTargetDimensions(int $width, int $height, int $maxWidth, int $maxHeight): array
    {
        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
        $targetWidth = max(1, (int) round($width * $ratio));
        $targetHeight = max(1, (int) round($height * $ratio));

        return [$targetWidth, $targetHeight];
    }

    private function composePayload(bool $onlyActive): array
    {
        $jobsQuery = LandingJob::query()->whereNull('deleted_at')->orderBy('sort_order')->orderBy('id');
        $menuQuery = LandingMenuItem::query()->whereNull('deleted_at')->orderBy('sort_order')->orderBy('id');
        $promotionsQuery = LandingPromotion::query()->orderBy('sort_order')->orderBy('id');
        $branchesQuery = LandingBranch::query()->orderBy('sort_order')->orderBy('id');

        if ($onlyActive) {
            $jobsQuery->where('is_active', true);
            $menuQuery->where('is_active', true);
            $promotionsQuery->where('is_active', true);
        }

        return [
            'settings' => $this->getSettings(),
            'jobs' => $jobsQuery->get(),
            'menu_items' => $menuQuery->get(),
            'promotions' => $promotionsQuery->get(),
            'branches' => $branchesQuery->get(),
            'summary' => [
                'active_jobs' => LandingJob::query()->whereNull('deleted_at')->where('is_active', true)->count(),
                'active_menu_items' => LandingMenuItem::query()->whereNull('deleted_at')->where('is_active', true)->count(),
                'contact_messages' => ContactMessage::query()->count(),
                'pending_contact_messages' => ContactMessage::query()->where('status', 'pending')->count(),
                'admin_users' => User::query()->where('is_admin', true)->count(),
                'change_logs' => AdminChangeLog::query()->count(),
            ],
        ];
    }

    private function getSettings(): array
    {
        return [
            ...self::DEFAULT_SETTINGS,
            ...array_intersect_key(
                LandingSetting::toKeyValueMap(),
                array_flip(self::SETTING_KEYS)
            ),
        ];
    }

    public function restoreDefaultMenu(Request $request)
    {
        try {
            \Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\LandingMenuItemSeeder']);

            $this->logAdminChange(
                $request,
                'restore',
                'landing_menu',
                null,
                'Se restauró el menú predeterminado de 20 items',
                ['action' => 'restore_default_menu']
            );

            return response()->json([
                'message' => 'Menú restaurado exitosamente con 20 items por defecto.',
                'items_restored' => 20,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo restaurar el menú. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function exportData(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:menu,jobs,promotions,branches,all'],
        ]);

        $handle = fopen('php://temp', 'r+');

        if ($data['type'] === 'menu' || $data['type'] === 'all') {
            fputcsv($handle, ['=== MENÚ ===']);
            fputcsv($handle, ['ID', 'Nombre', 'Categoría', 'Precio', 'Badge', 'Descripción', 'Activo', 'Destacado']);

            foreach (LandingMenuItem::query()->orderBy('sort_order')->get() as $item) {
                fputcsv($handle, [
                    $item->id,
                    $item->name,
                    $item->category,
                    $item->price,
                    $item->badge,
                    Str::limit($item->description, 100),
                    $item->is_active ? 'Sí' : 'No',
                    $item->is_featured ? 'Sí' : 'No',
                ]);
            }
            if ($data['type'] === 'all') {
                fputcsv($handle, []);
            }
        }

        if ($data['type'] === 'jobs' || $data['type'] === 'all') {
            fputcsv($handle, ['=== VACANTES ===']);
            fputcsv($handle, ['ID', 'Título', 'Ubicación', 'Tipo', 'Horario', 'Activo']);

            foreach (LandingJob::query()->whereNull('deleted_at')->orderBy('sort_order')->get() as $job) {
                fputcsv($handle, [
                    $job->id,
                    $job->title,
                    $job->location,
                    $job->employment_type,
                    $job->schedule,
                    $job->is_active ? 'Sí' : 'No',
                ]);
            }
            if ($data['type'] === 'all') {
                fputcsv($handle, []);
            }
        }

        if ($data['type'] === 'promotions' || $data['type'] === 'all') {
            fputcsv($handle, ['=== PROMOCIONES ===']);
            fputcsv($handle, ['ID', 'Título', 'Código', 'Descripción', 'Válido Hasta', 'Activo']);

            foreach (LandingPromotion::query()->orderBy('sort_order')->get() as $promo) {
                fputcsv($handle, [
                    $promo->id,
                    $promo->title,
                    $promo->code,
                    Str::limit($promo->description, 100),
                    $promo->valid_until?->toDateString() ?? 'Sin límite',
                    $promo->is_active ? 'Sí' : 'No',
                ]);
            }
            if ($data['type'] === 'all') {
                fputcsv($handle, []);
            }
        }

        if ($data['type'] === 'branches' || $data['type'] === 'all') {
            fputcsv($handle, ['=== SUCURSALES ===']);
            fputcsv($handle, ['ID', 'Nombre', 'Teléfono', 'Dirección', 'Abierta', 'Horario']);

            foreach (LandingBranch::query()->orderBy('sort_order')->get() as $branch) {
                fputcsv($handle, [
                    $branch->id,
                    $branch->name,
                    $branch->phone,
                    Str::limit($branch->address, 100),
                    $branch->is_open ? 'Sí' : 'No',
                    $branch->hours ?? 'No especificado',
                ]);
            }
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="admin-export-' . date('Y-m-d-His') . '.csv"',
        ]);
    }

    private function logAdminChange(
        Request $request,
        string $action,
        string $targetType,
        ?string $targetId,
        string $description,
        array $changes
    ): void {
        $adminUser = $request->attributes->get('adminUser');

        AdminChangeLog::create([
            'admin_user_id' => $adminUser?->id,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'description' => $description,
            'changes' => $changes,
        ]);
    }
}
