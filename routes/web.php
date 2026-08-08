<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\WelcomeController;

Route::get('/', [WelcomeController::class, 'index'])->name('site.welcome');
