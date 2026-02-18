<?php

use Illuminate\Support\Facades\Route;

// Auth Controller
use App\Http\Controllers\Auth\LoginController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\TicketController;

// Counter Controller
use App\Http\Controllers\Counter\CounterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ====================
// Redirect root to login
// ====================
Route::get('/', function () {
    return redirect()->route('login');
});

// ====================
// LOGIN ROUTES
// ====================
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.submit');

// ====================
// ADMIN ROUTES
// ====================
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'index'])
            ->name('dashboard');

        // Display Screen (TV)
        Route::get('/display-screen', [AdminController::class, 'displayScreen'])
            ->name('displayScreen');

        // AJAX: Get current counters and ticket numbers
        Route::get('/get-counters', [AdminController::class, 'getCounters'])
            ->name('getCounters');

        // AJAX: Get online/offline status of counters
        Route::get('/get-counter-status', [AdminController::class, 'getCounterStatus'])
            ->name('getCounterStatus');

        // User Management
        Route::get('/users', [AdminController::class, 'users'])
            ->name('users');

        Route::get('/create-user', [AdminController::class, 'createUserForm'])
            ->name('createUserForm');

        Route::post('/create-user', [AdminController::class, 'storeUser'])
            ->name('storeUser');

        // Edit User
        Route::get('/edit-user/{id}', [AdminController::class, 'editUser'])
            ->name('editUser');

        Route::put('/update-user/{id}', [AdminController::class, 'updateUser'])
            ->name('updateUser');

        // Delete User
        Route::delete('/delete-user/{id}', [AdminController::class, 'deleteUser'])
            ->name('deleteUser');

        // Ticket Management
        Route::get('/ticket-management', [TicketController::class, 'index'])
            ->name('ticket.management');

        Route::post('/tickets/add', [TicketController::class, 'add'])
            ->name('ticket.add');

        Route::delete('/tickets/delete/{id}', [TicketController::class, 'delete'])
            ->name('ticket.delete');

        Route::delete('/tickets/clear', [TicketController::class, 'clear'])
            ->name('ticket.clear');

        // Logout
        Route::post('/logout', [AdminController::class, 'logout'])
            ->name('logout');
    });

// ====================
// COUNTER ROUTES
// ====================
Route::middleware(['auth'])
    ->prefix('counter')
    ->name('counter.')
    ->group(function () {

        // Counter Dashboard
        Route::get('/dashboard', [CounterController::class, 'index'])
            ->name('dashboard');

        // Serve NEXT waiting ticket
        Route::post('/tickets/serve', [CounterController::class, 'serveNextTicket'])
            ->name('serveTicket');

        // Complete CURRENT serving ticket
        Route::post('/tickets/complete', [CounterController::class, 'completeCurrentTicket'])
            ->name('completeTicket');

        // Live Status (AJAX polling)
        Route::get('/status', [CounterController::class, 'getStatus'])
            ->name('status');

        // Logout
        Route::post('/logout', [CounterController::class, 'logout'])
            ->name('logout');
    });
