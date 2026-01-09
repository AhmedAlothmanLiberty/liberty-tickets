<?php

use Illuminate\Support\Facades\Route;
use Liberty\Tickets\Http\Controllers\TicketController;

Route::middleware(['web', 'auth'])
    ->prefix('tickets')
    ->name('tickets.')
    ->group(function () {

        // User views
        Route::get('/',              [TicketController::class, 'index'])->name('index');
        Route::get('/create',        [TicketController::class, 'create'])->name('create');
        Route::post('/',             [TicketController::class, 'store'])->name('store');
        Route::get('/{ticket}',      [TicketController::class, 'show'])->name('show');

        // Actions (policy-protected)
        Route::patch('/{ticket}',                [TicketController::class, 'update'])->name('update');
        Route::post('/{ticket}/priority',        [TicketController::class, 'changePriority'])->name('priority');
        Route::post('/{ticket}/escalate',        [TicketController::class, 'escalate'])->name('escalate');

        // Admin-ish actions (still behind policies)
        Route::post('/{ticket}/verify',          [TicketController::class, 'verifyBug'])->name('verify');
        Route::post('/{ticket}/assign',          [TicketController::class, 'assign'])->name('assign');
        Route::post('/{ticket}/resolve',         [TicketController::class, 'resolve'])->name('resolve');

        // Comments
        Route::post('/{ticket}/comments', [TicketController::class, 'storeComment'])
            ->name('comments.store');
    });
