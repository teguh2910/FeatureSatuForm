<?php

use Teguh\FeatureSatuForm\Http\Controllers\AdminAuthController;
use Teguh\FeatureSatuForm\Http\Controllers\AdminDashboardController;
use Teguh\FeatureSatuForm\Http\Controllers\AdminFormBuilderController;
use Teguh\FeatureSatuForm\Http\Controllers\AdminSubmissionController;
use Teguh\FeatureSatuForm\Http\Controllers\AdminUserManagementController;
use Teguh\FeatureSatuForm\Http\Controllers\PublicAuthController;
use Teguh\FeatureSatuForm\Http\Controllers\PublicFormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('web')->prefix('feature-satu-form')->group(function (): void {
    // Public form routes
    Route::get('/', [PublicFormController::class, 'index'])->name('public.forms.index');
    Route::get('/login', [PublicAuthController::class, 'showLogin'])->name('public.login');
    Route::post('/login', [PublicAuthController::class, 'login'])->name('public.login.submit');
    Route::post('/logout', [PublicAuthController::class, 'logout'])->name('public.logout');
    Route::get('/forms/{id}', [PublicFormController::class, 'show'])->name('public.forms.show');
    Route::post('/forms/{id}/verify-dependency', [PublicFormController::class, 'verifyDependency'])->name('public.forms.verify-dependency');
    Route::post('/forms/{id}', [PublicFormController::class, 'store'])->name('public.forms.store');
    Route::get('/track', [PublicFormController::class, 'track'])->name('public.forms.track');

    Route::prefix('admin')->group(function (): void {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

        Route::middleware('admin.auth')->group(function (): void {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

            // Form Builder Routes
            Route::get('/forms', [AdminFormBuilderController::class, 'index'])->name('admin.forms.index');
            Route::post('/forms', [AdminFormBuilderController::class, 'store'])->name('admin.forms.store');
            Route::get('/forms/{formTemplate}/edit', [AdminFormBuilderController::class, 'edit'])->name('admin.forms.edit');
            Route::put('/forms/{formTemplate}', [AdminFormBuilderController::class, 'update'])->name('admin.forms.update');
            Route::post('/forms/{formTemplate}/toggle-publish', [AdminFormBuilderController::class, 'togglePublish'])->name('admin.forms.togglePublish');
            Route::delete('/forms/{formTemplate}', [AdminFormBuilderController::class, 'destroy'])->name('admin.forms.destroy');

            // Submissions Routes
            Route::get('/submissions', [AdminSubmissionController::class, 'index'])->name('admin.submissions.index');
            Route::post('/submissions/{trackingId}/status', [AdminSubmissionController::class, 'updateStatus'])->name('admin.submissions.status');

            // User Management Routes (super admin only)
            Route::middleware('admin.super')->group(function (): void {
                Route::get('/users', [AdminUserManagementController::class, 'index'])->name('admin.users.index');
                Route::get('/users/create', [AdminUserManagementController::class, 'create'])->name('admin.users.create');
                Route::post('/users', [AdminUserManagementController::class, 'store'])->name('admin.users.store');
                Route::get('/users/{user}/edit', [AdminUserManagementController::class, 'edit'])->name('admin.users.edit');
                Route::put('/users/{user}', [AdminUserManagementController::class, 'update'])->name('admin.users.update');
                Route::delete('/users/{user}', [AdminUserManagementController::class, 'destroy'])->name('admin.users.destroy');
            });

            // Logout
            Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        });
    });
});
