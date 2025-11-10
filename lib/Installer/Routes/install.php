<?php

use Illuminate\Support\Facades\Route;
use Installer\Controllers\InstallerController;

Route::middleware('check.installation')->group(function () {
    Route::get('/install', [InstallerController::class, 'welcome'])->name('install.welcome');
    Route::get('/install/check', [InstallerController::class, 'check'])->name('install.check');
    Route::match(['get', 'post'], '/install/database', [InstallerController::class, 'database'])->name('install.database');
    Route::get('/install/finish', [InstallerController::class, 'finish'])->name('install.finish');
});
