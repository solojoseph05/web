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

Route::post('/admin/auth/bootstrap', [AdminAuthController::class, 'bootstrap']);
Route::post('/admin/auth/login', [AdminAuthController::class, 'login']);

Route::middleware('admin.auth')->group(function () {
    Route::get('/admin/auth/me', [AdminAuthController::class, 'me']);
    Route::post('/admin/auth/logout', [AdminAuthController::class, 'logout']);
    Route::post('/admin/auth/2fa/setup', [AdminAuthController::class, 'setupTwoFactor']);
    Route::post('/admin/auth/2fa/enable', [AdminAuthController::class, 'enableTwoFactor']);
    Route::post('/admin/auth/2fa/disable', [AdminAuthController::class, 'disableTwoFactor']);
    Route::post('/admin/users', [AdminAuthController::class, 'createAdminUser'])
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
