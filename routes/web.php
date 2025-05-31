<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SimController;
use App\Http\Controllers\ChatManagementController;
use App\Models\Sim;

Route::get('/admin/login', [AuthController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [SimController::class, 'dashboard'])->name('dashboard');
    Route::get('/sim/create', [SimController::class, 'createSimForm'])->name('sim.create');
    Route::post('/sim/store', [SimController::class, 'storeSim'])->name('sim.store');
    Route::get('/sim/edit/{sim_id}', [SimController::class, 'editSimForm'])->name('sim.edit');
    Route::post('/sim/update/{sim_id}', [SimController::class, 'updateSim'])->name('sim.update');
    Route::delete('/sim/delete/{sim_id}', [SimController::class, 'deleteSim'])->name('sim.delete');
    Route::get('/sim/view/{sim_id}', [SimController::class, 'viewSim'])->name('sim.view');
    Route::get('/sim/export/pdf', [SimController::class, 'exportSimsToPDF'])->name('sims.export.pdf');
    Route::get('/sim/export/csv', [SimController::class, 'exportSimsToCSV'])->name('sims.export.csv');
    Route::get('/sim/chat', [ChatManagementController::class, 'index'])->name('chat.index');
    Route::get('/sim/mark-as-read/{message_id}', [ChatManagementController::class, 'markAsRead'])->name('chat.mark');
});

// Route Pusher
Route::post('/sim/send-message', [ChatManagementController::class, 'sendMessage'])->name('chat.send');
Route::get('/sim/get-messages', [ChatManagementController::class, 'getMessages'])->name('chat.get');
Route::get('/sim/chat-user', [ChatManagementController::class, 'user'])->name('chat.user')->middleware('guest'); // Menggunakan 'guest' untuk mencegah akses admin

Route::get('/', [SimController::class, 'welcome'])->name('welcome');
Route::post('/sim/public-search', [SimController::class, 'publicSearch'])->name('sim.public-search');