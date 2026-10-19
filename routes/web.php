<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\QuranController;
use App\Http\Controllers\Customer\AITeacherController;
use App\Http\Controllers\Customer\HifzController;
use App\Http\Controllers\Customer\ProgressController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;

/*
|--------------------------------------------------------------------------
| Public & Landing Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/page/{slug}', [LandingController::class, 'page'])->name('page.show');

/*
|--------------------------------------------------------------------------
| Customer Dashboard & Features (Protected by Auth & Active status)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Quran Reader
    Route::prefix('quran')->name('quran.')->group(function () {
        Route::get('/', [QuranController::class, 'index'])->name('index');
        Route::get('/bookmarks', [QuranController::class, 'bookmarks'])->name('bookmarks');
        Route::get('/{surahNumber}', [QuranController::class, 'show'])->name('show');
        Route::get('/{surahNumber}/{ayahNumber}', [QuranController::class, 'getAyah'])->name('ayah');
        Route::post('/bookmark', [QuranController::class, 'toggleBookmark'])->name('bookmark.toggle');
        Route::post('/progress', [QuranController::class, 'updateProgress'])->name('progress.update');
    });

    // AI Quran Teacher
    Route::prefix('ai-teacher')->name('ai-teacher.')->group(function () {
        Route::get('/', [AITeacherController::class, 'index'])->name('index');
        Route::get('/ayah/{surahNumber}/{ayahNumber}', [AITeacherController::class, 'getAyahForPractice'])->name('ayah');
        Route::post('/start-session', [AITeacherController::class, 'startSession'])->name('start-session');
        Route::post('/analyze', [AITeacherController::class, 'analyzeRecitation'])->name('analyze');
        Route::post('/end-session', [AITeacherController::class, 'endSession'])->name('end-session');
    });

    // Hifz & Memorization Module
    Route::prefix('hifz')->name('hifz.')->group(function () {
        Route::get('/', [HifzController::class, 'index'])->name('index');
        Route::get('/practice/{surahNumber}', [HifzController::class, 'practice'])->name('practice');
        Route::post('/status', [HifzController::class, 'updateStatus'])->name('status');
        Route::post('/record-practice', [HifzController::class, 'recordPractice'])->name('record-practice');
        Route::post('/complete-revision', [HifzController::class, 'completeRevision'])->name('complete-revision');
        Route::get('/progress-data', [HifzController::class, 'progressData'])->name('progress-data');
    });

    // Progress & History
    Route::prefix('progress')->name('progress.')->group(function () {
        Route::get('/', [ProgressController::class, 'index'])->name('index');
        Route::get('/chart-data', [ProgressController::class, 'chartData'])->name('chart-data');
        Route::post('/delete-history', [ProgressController::class, 'deleteHistory'])->name('delete-history');
    });

    // Breeze Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Auth + Active + Admin/Owner role)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
    });

    // Content Management (FAQs, Pages, Announcements)
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/', [AdminContentController::class, 'index'])->name('index');

        // FAQs
        Route::post('/faqs', [AdminContentController::class, 'storeFaq'])->name('faqs.store');
        Route::put('/faqs/{faq}', [AdminContentController::class, 'updateFaq'])->name('faqs.update');
        Route::delete('/faqs/{faq}', [AdminContentController::class, 'destroyFaq'])->name('faqs.destroy');

        // Site Pages
        Route::put('/pages/{page}', [AdminContentController::class, 'updatePage'])->name('pages.update');

        // Announcements
        Route::post('/announcements', [AdminContentController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::put('/announcements/{announcement}', [AdminContentController::class, 'updateAnnouncement'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AdminContentController::class, 'destroyAnnouncement'])->name('announcements.destroy');
    });

    // System Settings & API Logs
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
        Route::post('/', [AdminSettingsController::class, 'update'])->name('update');
        Route::post('/clear-cache', [AdminSettingsController::class, 'clearCache'])->name('clear-cache');
    });
});

require __DIR__.'/auth.php';
