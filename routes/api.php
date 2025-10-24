<?php

use App\Http\Controllers\BalanceController;
use Illuminate\Support\Facades\Route;

Route::post('deposit', [BalanceController::class, 'deposit'])->name('balance.deposit');