<?php

namespace App\Http\Controllers;

use App\Models\AdminChangeLog;
use App\Models\AdminApprovalRequest;
use App\Models\ContactMessage;
use App\Models\LandingBranch;
use App\Models\LandingJob;
use App\Models\LandingMenuItem;
use App\Models\LandingPromotion;
use App\Models\LandingSetting;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Controlador principal del Panel Administrativo para gestionar la Landing Page y sus catálogos.
 */
class AdminLandingContentController extends Controller
{
    private const SETTING_KEYS = [
        'hero_title',
        'hero_subtitle',
        'hero_badge',
        'hero_description',
        'hero_image_url',
        'hero_cta_primary',
        'hero_cta_secondary',
        'stat_1_value',
        'stat_1_label',
        'stat_2_value',
        'stat_2_label',
        'stat_3_value',
        'stat_3_label',
        'menu_section_title',
        'menu_section_subtitle',
        'promotions_section_title',
        'promotions_section_subtitle',
        'branches_section_title',
        'branches_section_subtitle',
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'company_whatsapp',
        'schedule_weekday',
        'social_facebook',
        'social_instagram',
    ];

    private const DEFAULT_SETTINGS = [
        'hero_title' => 'El mejor Pollo Asado de Durango',
        'hero_subtitle' => 'Tradición Familiar al Carbón',
        'hero_badge' => '🔥 Recién saliendo del carbón',
        'hero_description' => 'Descubre el sabor que nos hace únicos. Pollo asado con receta especial y tradición familiar en Durango.',
        'hero_image_url' => null,
        'hero_cta_primary' => 'Ver Menú Completo',
        'hero_cta_secondary' => 'Ordenar Ahora',
        'stat_1_value' => '100%',
        'stat_1_label' => 'Asado al Carbón',
        'stat_2_value' => '+25',
        'stat_2_label' => 'Años de Tradición',
        'stat_3_value' => '618',
        'stat_3_label' => 'Orgullo Duranguense',
        'menu_section_title' => 'Nuestro Menú al Carbón',
        'menu_section_subtitle' => 'Especialidades preparadas al momento con nuestra receta secreta.',
        'promotions_section_title' => 'Promociones Especiales',
        'promotions_section_subtitle' => 'Descuentos y paquetes exclusivos por tiempo limitado.',
        'branches_section_title' => 'Nuestras Sucursales',
        'branches_section_subtitle' => 'Encuentra tu sucursal más cercana en Durango y traza tu ruta GPS.',
        'company_name' => 'Pollo Feliz Durango',
        'company_address' => 'Av. 5 de Mayo #123, Centro, Durango',
        'company_phone' => '618-123-4567',
        'company_email' => 'soporte@pollofelizdgo.com',
        'company_whatsapp' => '526181234567',
        'schedule_weekday' => 'Lun - Dom: 10:00 AM - 10:00 PM',
        'social_facebook' => 'https://facebook.com/pollofelizdgo',
        'social_instagram' => 'https://instagram.com/pollofelizdgo',
    ];

    public function publicContent()
    {
        return response()->json($this->composePayload(true));
    }

    public function dashboardContent()
    {
        return response()->json($this->composePayload(false));
    }

    /**
     * Actualiza los ajustes generales de la Landing Page dentro de una transacción segura.
     */
    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'hero_title' => ['required', 'string', 'max:160'],
            'hero_subtitle' => ['nullable', 'string', 'max:160'],
            'hero_badge' => ['nullable', 'string', 'max:120'],
            'hero_description' => ['required', 'string', 'max:1000'],
            'hero_image_url' => ['nullable', 'string', 'max:600'],
            'hero_cta_primary' => ['nullable', 'string', 'max:80'],
            'hero_cta_secondary' => ['nullable', 'string', 'max:80'],
            'stat_1_value' => ['nullable', 'string', 'max:40'],
            'stat_1_label' => ['nullable', 'string', 'max:80'],
            'stat_2_value' => ['nullable', 'string', 'max:40'],
            'stat_2_label' => ['nullable', 'string', 'max:80'],
            'stat_3_value' => ['nullable', 'string', 'max:40'],
            'stat_3_label' => ['nullable', 'string', 'max:80'],
            'menu_section_title' => ['nullable', 'string', 'max:160'],
            'menu_section_subtitle' => ['nullable', 'string', 'max:400'],
            'promotions_section_title' => ['nullable', 'string', 'max:160'],
            'promotions_section_subtitle' => ['nullable', 'string', 'max:400'],
            'branches_section_title' => ['nullable', 'string', 'max:160'],
            'branches_section_subtitle' => ['nullable', 'string', 'max:400'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'company_address' => ['required', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:60'],
            'company_email' => ['nullable', 'email', 'max:160'],
            'company_whatsapp' => ['nullable', 'string', 'max:40'],
            'schedule_weekday' => ['nullable', 'string', 'max:160'],
            'social_facebook' => ['nullable', 'string', 'max:255'],
            'social_instagram' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $previousHeroImage = LandingSetting::query()->where('key', 'hero_image_url')->value('value');

            DB::transaction(function () use ($request, $data): void {
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
            });

            if (!empty($previousHeroImage) && $previousHeroImage !== ($data['hero_image_url'] ?? null)) {
                $this->deleteLocalImageSafe($previousHeroImage);
            }

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Configuracion del landing actualizada.',
                'settings' => $this->getSettings(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error actualizando ajustes de Landing Page', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo actualizar la configuración del landing.'], 500);
        }
    }

    /**
     * Crea una nueva vacante en la bolsa de trabajo e invalida la caché.
     */
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

        try {
            $job = DB::transaction(function () use ($request, $data): LandingJob {
                $job = LandingJob::create([
                    ...$data,
                    'is_active' => $request->boolean('is_active', true),
                    'sort_order' => (int) ($data['sort_order'] ?? 0),
                ]);

                $this->logAdminChange(
                    $request,
                    'landing.job.create',
                    'LandingJob',
                    (string) $job->id,
                    'Se creó una nueva vacante.',
                    $job->toArray()
                );

                return $job;
            });

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Vacante creada correctamente.',
                'job' => $job,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error creando vacante laboral', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo registrar la vacante.'], 500);
        }
    }

    /**
     * Actualiza una vacante existente y limpia su imagen anterior si fue reemplazada.
     */
    public function updateJob(Request $request, LandingJob $landingJob)
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

        try {
            $previousImage = $landingJob->image_url;

            DB::transaction(function () use ($request, $landingJob, $data): void {
                $data['is_active'] = $request->has('is_active')
                    ? $request->boolean('is_active')
                    : $landingJob->is_active;

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
            });

            if (!empty($previousImage) && $previousImage !== $landingJob->image_url) {
                $this->deleteLocalImageSafe($previousImage);
            }

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Vacante actualizada correctamente.',
                'job' => $landingJob->fresh(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error actualizando vacante laboral', ['id' => $landingJob->id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo actualizar la vacante.'], 500);
        }
    }

    /**
     * Envía una vacante a la papelera de reciclaje (SoftDelete).
     */
    public function deleteJob(Request $request, LandingJob $landingJob)
    {
        try {
            DB::transaction(function () use ($request, $landingJob): void {
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
            });

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Vacante eliminada correctamente.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error eliminando vacante laboral', ['id' => $landingJob->id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo eliminar la vacante.'], 500);
        }
    }

    public function jobsTrash()
    {
        return response()->json([
            'jobs' => LandingJob::onlyTrashed()->orderByDesc('deleted_at')->get(),
        ]);
    }

    /**
     * Restaura una vacante previamente enviada a papelera.
     */
    public function restoreJob(Request $request, string $jobId)
    {
        try {
            $job = DB::transaction(function () use ($request, $jobId): LandingJob {
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

                return $job;
            });

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Vacante restaurada correctamente.',
                'job' => $job->fresh(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error restaurando vacante laboral', ['id' => $jobId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo restaurar la vacante.'], 500);
        }
    }

    /**
     * Registra un nuevo platillo en el menú público.
     */
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
            'is_sold_out' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $item = DB::transaction(function () use ($request, $data): LandingMenuItem {
                $item = LandingMenuItem::create([
                    ...$data,
                    'is_featured' => $request->boolean('is_featured', true),
                    'is_sold_out' => $request->boolean('is_sold_out', false),
                    'is_active' => $request->boolean('is_active', true),
                    'sort_order' => (int) ($data['sort_order'] ?? 0),
                ]);

                $this->logAdminChange(
                    $request,
                    'landing.menu.create',
                    'LandingMenuItem',
                    (string) $item->id,
                    'Se creó un nuevo producto del menú.',
                    $item->toArray()
                );

                return $item;
            });

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Producto del menu creado correctamente.',
                'item' => $item,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error creando platillo del menú', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo crear el producto del menú.'], 500);
        }
    }

    /**
     * Actualiza un platillo del menú y elimina la imagen anterior si fue reemplazada.
     */
    public function updateMenuItem(Request $request, LandingMenuItem $landingMenuItem)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'badge' => ['nullable', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_sold_out' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $previousImage = $landingMenuItem->image_url;

            DB::transaction(function () use ($request, $landingMenuItem, $data): void {
                $data['is_featured'] = $request->has('is_featured')
                    ? $request->boolean('is_featured')
                    : $landingMenuItem->is_featured;
                $data['is_sold_out'] = $request->has('is_sold_out')
                    ? $request->boolean('is_sold_out')
                    : (bool) ($landingMenuItem->is_sold_out ?? false);
                $data['is_active'] = $request->has('is_active')
                    ? $request->boolean('is_active')
                    : $landingMenuItem->is_active;

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
            });

            if (!empty($previousImage) && $previousImage !== $landingMenuItem->image_url) {
                $this->deleteLocalImageSafe($previousImage);
            }

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Producto del menu actualizado correctamente.',
                'item' => $landingMenuItem->fresh(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error actualizando platillo del menú', ['id' => $landingMenuItem->id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo actualizar el producto del menú.'], 500);
        }
    }

    /**
     * Elimina (SoftDelete) un platillo del menú.
     */
    public function deleteMenuItem(Request $request, LandingMenuItem $landingMenuItem)
    {
        try {
            DB::transaction(function () use ($request, $landingMenuItem): void {
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
            });

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Producto del menu eliminado correctamente.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error eliminando platillo del menú', ['id' => $landingMenuItem->id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo eliminar el producto del menú.'], 500);
        }
    }

    public function menuItemsTrash()
    {
        return response()->json([
            'items' => LandingMenuItem::onlyTrashed()->orderByDesc('deleted_at')->get(),
        ]);
    }

    /**
     * Restaura un platillo desde la papelera.
     */
    public function restoreMenuItem(Request $request, string $menuItemId)
    {
        try {
            $item = DB::transaction(function () use ($request, $menuItemId): LandingMenuItem {
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

                return $item;
            });

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Producto restaurado correctamente.',
                'item' => $item->fresh(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error restaurando platillo del menú', ['id' => $menuItemId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo restaurar el producto.'], 500);
        }
    }

    /**
     * Crea una nueva promoción vigente.
     */
    public function storePromotion(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date'],
            'code' => ['nullable', 'string', 'max:80'],
            'terms_conditions' => ['nullable', 'string', 'max:3000'],
            'badge' => ['nullable', 'string', 'max:80'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'promo_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $promotion = DB::transaction(function () use ($request, $data): LandingPromotion {
                $promotion = LandingPromotion::create([
                    ...$data,
                    'is_active' => $request->boolean('is_active', true),
                    'sort_order' => (int) ($data['sort_order'] ?? 0),
                ]);

                $this->logAdminChange(
                    $request,
                    'landing.promotion.create',
                    'LandingPromotion',
                    (string) $promotion->id,
                    'Se creó una nueva promoción.',
                    $promotion->toArray()
                );

                return $promotion;
            });

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Promoción creada correctamente.',
                'promotion' => $promotion,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error creando promoción', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo crear la promoción.'], 500);
        }
    }

    /**
     * Actualiza una promoción y elimina la imagen anterior si fue reemplazada.
     */
    public function updatePromotion(Request $request, LandingPromotion $landingPromotion)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date'],
            'code' => ['nullable', 'string', 'max:80'],
            'terms_conditions' => ['nullable', 'string', 'max:3000'],
            'badge' => ['nullable', 'string', 'max:80'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'promo_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $previousImage = $landingPromotion->image_url;

            DB::transaction(function () use ($request, $landingPromotion, $data): void {
                $data['is_active'] = $request->has('is_active')
                    ? $request->boolean('is_active')
                    : $landingPromotion->is_active;

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
            });

            if (!empty($previousImage) && $previousImage !== $landingPromotion->image_url) {
                $this->deleteLocalImageSafe($previousImage);
            }

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Promoción actualizada correctamente.',
                'promotion' => $landingPromotion->fresh(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error actualizando promoción', ['id' => $landingPromotion->id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo actualizar la promoción.'], 500);
        }
    }

    /**
     * Elimina una promoción y limpia su archivo de imagen local asociado.
     */
    public function deletePromotion(Request $request, LandingPromotion $landingPromotion)
    {
        try {
            $imageToDelete = $landingPromotion->image_url;

            DB::transaction(function () use ($request, $landingPromotion): void {
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
            });

            if (!empty($imageToDelete)) {
                $this->deleteLocalImageSafe($imageToDelete);
            }

            $this->clearLandingCache();

            return response()->json(['message' => 'Promoción eliminada correctamente.']);
        } catch (\Throwable $e) {
            Log::error('Error eliminando promoción', ['id' => $landingPromotion->id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo eliminar la promoción.'], 500);
        }
    }

    /**
     * Crea una sucursal sincronizando `landing_branches` y `locations` dentro de una transacción.
     */
    public function storeBranch(Request $request)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:160'],
            'branch_name' => ['nullable', 'string', 'max:160'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:80'],
            'schedule' => ['nullable', 'string', 'max:120'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'map_link' => ['nullable', 'string', 'max:600'],
            'google_maps_url' => ['nullable', 'string', 'max:600'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'map_zoom' => ['nullable', 'integer', 'between:1,22'],
            'is_active' => ['sometimes', 'boolean'],
            'is_open' => ['sometimes', 'boolean'],
            'is_coming_soon' => ['sometimes', 'boolean'],
            'is_new' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $name = trim($data['name'] ?? $data['branch_name'] ?? 'Sucursal Pollo Feliz');
        $mapUrl = $data['map_link'] ?? $data['google_maps_url'] ?? null;
        if (!$mapUrl && !empty($data['latitude']) && !empty($data['longitude'])) {
            $mapUrl = "https://www.google.com/maps/dir/?api=1&destination={$data['latitude']},{$data['longitude']}";
        }

        $payload = [
            'name' => $name,
            'branch_name' => $name,
            'address' => trim($data['address'] ?? ''),
            'phone' => trim($data['phone'] ?? '618-123-4567'),
            'schedule' => trim($data['schedule'] ?? 'Lun - Dom: 10:00 AM - 10:00 PM'),
            'image_url' => $data['image_url'] ?? null,
            'map_link' => $mapUrl,
            'google_maps_url' => $mapUrl,
            'latitude' => isset($data['latitude']) ? (float) $data['latitude'] : 24.0277,
            'longitude' => isset($data['longitude']) ? (float) $data['longitude'] : -104.6532,
            'map_zoom' => isset($data['map_zoom']) ? (int) $data['map_zoom'] : 16,
            'is_active' => $request->boolean('is_active', true),
            'is_open' => $request->boolean('is_open', true),
            'is_coming_soon' => $request->boolean('is_coming_soon', false),
            'is_new' => $request->boolean('is_new', true),
            'sort_order' => isset($data['sort_order']) ? (int) $data['sort_order'] : 0,
        ];

        try {
            $branch = DB::transaction(function () use ($request, $payload): LandingBranch {
                $branch = LandingBranch::create($payload);

                try {
                    Location::create($payload);
                } catch (\Throwable $e) {
                    Log::info('Tabla locations no disponible al crear sucursal: ' . $e->getMessage());
                }

                $this->logAdminChange(
                    $request,
                    'landing.branch.create',
                    'LandingBranch',
                    (string) $branch->id,
                    'Se creó una nueva sucursal: ' . $branch->name,
                    $branch->toArray()
                );

                return $branch;
            });

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Sucursal creada correctamente.',
                'branch' => $branch,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error creando sucursal', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo crear la sucursal.'], 500);
        }
    }

    /**
     * Actualiza una sucursal y sincroniza su estado e imagen con `locations`.
     */
    public function updateBranch(Request $request, LandingBranch $landingBranch)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:160'],
            'branch_name' => ['nullable', 'string', 'max:160'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:80'],
            'schedule' => ['nullable', 'string', 'max:120'],
            'image_url' => ['nullable', 'string', 'max:600'],
            'map_link' => ['nullable', 'string', 'max:600'],
            'google_maps_url' => ['nullable', 'string', 'max:600'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'map_zoom' => ['nullable', 'integer', 'between:1,22'],
            'is_active' => ['sometimes', 'boolean'],
            'is_open' => ['sometimes', 'boolean'],
            'is_coming_soon' => ['sometimes', 'boolean'],
            'is_new' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (isset($data['name']) || isset($data['branch_name'])) {
            $name = trim($data['name'] ?? $data['branch_name']);
            $data['name'] = $name;
            $data['branch_name'] = $name;
        }

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }
        if ($request->has('is_open')) {
            $data['is_open'] = $request->boolean('is_open');
        }
        if ($request->has('is_coming_soon')) {
            $data['is_coming_soon'] = $request->boolean('is_coming_soon');
        }
        if ($request->has('is_new')) {
            $data['is_new'] = $request->boolean('is_new');
        }

        try {
            $previousImage = $landingBranch->image_url;

            DB::transaction(function () use ($request, $landingBranch, $data): void {
                $landingBranch->fill($data);
                $landingBranch->save();

                try {
                    Location::where('id', $landingBranch->id)->update($data);
                } catch (\Throwable $e) {
                    Log::info('No se pudo sincronizar locations en actualización: ' . $e->getMessage());
                }

                $this->logAdminChange(
                    $request,
                    'landing.branch.update',
                    'LandingBranch',
                    (string) $landingBranch->id,
                    'Se actualizó la sucursal: ' . $landingBranch->name,
                    $landingBranch->toArray()
                );
            });

            if (!empty($previousImage) && $previousImage !== $landingBranch->image_url) {
                $this->deleteLocalImageSafe($previousImage);
            }

            $this->clearLandingCache();

            return response()->json([
                'message' => 'Sucursal actualizada correctamente.',
                'branch' => $landingBranch->fresh(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error actualizando sucursal', ['id' => $landingBranch->id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo actualizar la sucursal.'], 500);
        }
    }

    /**
     * Elimina una sucursal de `landing_branches` y `locations` e invalida caché.
     */
    public function deleteBranch(Request $request, LandingBranch $landingBranch)
    {
        try {
            $imageToDelete = $landingBranch->image_url;

            DB::transaction(function () use ($request, $landingBranch): void {
                $snapshot = $landingBranch->toArray();
                $landingBranch->delete();

                try {
                    Location::where('id', $snapshot['id'] ?? 0)->delete();
                } catch (\Throwable $e) {
                    Log::info('No se pudo sincronizar eliminación en locations: ' . $e->getMessage());
                }

                $this->logAdminChange(
                    $request,
                    'landing.branch.delete',
                    'LandingBranch',
                    (string) ($snapshot['id'] ?? ''),
                    'Se eliminó la sucursal: ' . ($snapshot['name'] ?? ''),
                    $snapshot
                );
            });

            if (!empty($imageToDelete)) {
                $this->deleteLocalImageSafe($imageToDelete);
            }

            $this->clearLandingCache();

            return response()->json(['message' => 'Sucursal eliminada correctamente.']);
        } catch (\Throwable $e) {
            Log::error('Error eliminando sucursal', ['id' => $landingBranch->id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo eliminar la sucursal.'], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
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

    /**
     * Elimina de forma segura una imagen local previa para evitar archivos huérfanos.
     */
    private function deleteLocalImageSafe(?string $url): void
    {
        if (empty($url)) {
            return;
        }

        try {
            $relativePath = $this->extractStoragePath($url);
            if ($relativePath && Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo eliminar archivo de imagen previo: ' . $e->getMessage());
        }
    }

    /**
     * Invalida todas las claves de caché asociadas a la Landing Page pública.
     */
    private function clearLandingCache(): void
    {
        Cache::forget('landing_page_data');
        Cache::forget('landing_page_locations');
        Cache::forget('locations_active');
        Cache::forget('landing_page_menu');
        Cache::forget('landing_page_promos');
        Cache::forget('landing_page_jobs');
        Cache::forget('landing_page_settings');
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
            $promotionsQuery->currentlyValid();
            $branchesQuery->where(function ($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })->where('is_open', true);
        }

        $jobsTrend = $this->calculateWeeklyTrend(LandingJob::query()->whereNull('deleted_at'));
        $menuTrend = $this->calculateWeeklyTrend(LandingMenuItem::query()->whereNull('deleted_at'));
        $messagesTrend = $this->calculateWeeklyTrend(ContactMessage::query());
        $adminsTrend = $this->calculateWeeklyTrend(User::query()->where('is_admin', true));
        $changesTrend = $this->calculateWeeklyTrend(AdminChangeLog::query());

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
                'trends' => [
                    'active_jobs' => $jobsTrend,
                    'active_menu_items' => $menuTrend,
                    'contact_messages' => $messagesTrend,
                    'admin_users' => $adminsTrend,
                    'change_logs' => $changesTrend,
                ],
            ],
        ];
    }

    private function calculateWeeklyTrend(Builder $query): array
    {
        $now = now();
        $recentStart = (clone $now)->subDays(7);
        $previousStart = (clone $now)->subDays(14);

        $recent = (clone $query)->where('created_at', '>=', $recentStart)->count();
        $previous = (clone $query)
            ->where('created_at', '>=', $previousStart)
            ->where('created_at', '<', $recentStart)
            ->count();

        return [
            'recent' => $recent,
            'previous' => $previous,
            'delta' => $recent - $previous,
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

        if ($adminUser && $adminUser->role !== 'super_admin' && str_starts_with($action, 'landing.')) {
            AdminApprovalRequest::create([
                'admin_user_id' => $adminUser->id,
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'description' => $description,
                'changes' => $changes,
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Descarga segura de currículums almacenados en storage/app/private/resumes (solo administradores autorizados).
     */
    public function downloadResume(string $filename)
    {
        $safeFilename = basename($filename);
        if ($safeFilename === '' || str_contains($safeFilename, '..')) {
            abort(400, 'Nombre de archivo inválido.');
        }

        $relativePath = 'private/resumes/' . $safeFilename;
        if (!Storage::disk('local')->exists($relativePath)) {
            abort(404, 'El currículum solicitado no existe.');
        }

        return Storage::disk('local')->download($relativePath, $safeFilename);
    }
}
