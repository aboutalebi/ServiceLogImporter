<?php

use App\Http\Controllers\ServiceLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/logs/count', [ServiceLogController::class, 'getCount']);
