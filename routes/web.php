<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Counter\CounterController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
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


// =======================================================
// ADMIN ROUTES (ADMIN ONLY)
// =======================================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'index'])
            ->name('dashboard');

        Route::get('/display-screen', [AdminController::class, 'displayScreen'])
            ->name('displayScreen');

        Route::get('/get-counters', [AdminController::class, 'getCounters'])
            ->name('getCounters');

        Route::get('/get-counter-status', [AdminController::class, 'getCounterStatus'])
            ->name('getCounterStatus');

        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/create-user', [AdminController::class, 'createUserForm'])->name('createUserForm');
        Route::post('/create-user', [AdminController::class, 'storeUser'])->name('storeUser');
        Route::get('/edit-user/{id}', [AdminController::class, 'editUser'])->name('editUser');
        Route::put('/update-user/{id}', [AdminController::class, 'updateUser'])->name('updateUser');
        Route::delete('/delete-user/{id}', [AdminController::class, 'deleteUser'])->name('deleteUser');

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


// =======================================================
// COUNTER ROUTES (COUNTER ONLY)
// =======================================================
Route::middleware(['auth', 'role:counter'])
    ->prefix('counter')
    ->name('counter.')
    ->group(function () {

        Route::get('/dashboard', [CounterController::class, 'index'])
            ->name('dashboard');

        Route::post('/tickets/serve', [CounterController::class, 'serveNextTicket'])
            ->name('serveTicket');

        Route::post('/tickets/complete', [CounterController::class, 'completeCurrentTicket'])
            ->name('completeTicket');

        Route::get('/status', [CounterController::class, 'getStatus'])
            ->name('status');

        Route::post('/logout', [CounterController::class, 'logout'])
            ->name('logout');
    });
