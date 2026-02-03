<?php

use App\Http\Controllers\AcarsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\DiscordAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispatcherController;
use App\Http\Controllers\OfpController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\Webhooks\DiscordWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/auth/discord', [DiscordAuthController::class, 'redirect'])->name('auth.discord.redirect');
    Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback'])->name('auth.discord.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('discord.role:pilot')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/ofp', [OfpController::class, 'index'])->name('ofp');
        Route::get('/performance', [PerformanceController::class, 'index'])->name('performance');
        Route::get('/weather', [WeatherController::class, 'index'])->name('weather');
        Route::get('/acars', [AcarsController::class, 'index'])->name('acars');
    });

    Route::middleware('discord.role:staff')->group(function () {
        Route::get('/dispatcher', [DispatcherController::class, 'index'])->name('dispatcher');
        Route::post('/dispatcher/messages', [DispatcherController::class, 'sendMessage'])->name('dispatcher.message');
        Route::get('/dispatcher/history', [DispatcherController::class, 'history'])->name('dispatcher.history');
    });

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/discord/disconnect', [SettingsController::class, 'disconnectDiscord'])->name('settings.discord.disconnect');
    Route::delete('/settings', [SettingsController::class, 'destroy'])->name('settings.destroy');
});

Route::post('/webhooks/discord-dm', [DiscordWebhookController::class, 'receiveDm'])
    ->name('webhooks.discord.dm');
