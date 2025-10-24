<?php

use App\Http\Controllers\BalanceController;
use Illuminate\Support\Facades\Route;

Route::get('balance/{id}', [BalanceController::class, 'balance'])->name('balance.balance');
Route::post('deposit', [BalanceController::class, 'deposit'])->name('balance.deposit');
Route::post('withdraw', [BalanceController::class, 'withdraw'])->name('balance.withdraw');
Route::post('transfer', [BalanceController::class, 'transfer'])->name('balance.transfer');
