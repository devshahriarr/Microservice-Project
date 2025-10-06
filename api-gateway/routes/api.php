<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProxyController;

/*
|--------------------------------------------------------------------------
| API Gateway Routes
|--------------------------------------------------------------------------
|
| These routes live in routes/api.php, so they are served under '/api/*'.
| We define simple prefixes and set a default 'service' route parameter
| so ProxyController receives the correct $service and $path.
|
*/

// // AUTH routes (no token check)
// Route::prefix('auth')->group(function () {
//     Route::any('{service}/{path?}', [ProxyController::class, 'proxy'])->where('path', '.*');
// });

// // CRUD routes (with token middleware)
// Route::prefix('crud')->middleware('validate.auth')->group(function () {
//     Route::any('{service}/{path?}', [ProxyController::class, 'proxy'])->where('path', '.*');
// });

// // Generic fallback for all others
// Route::any('{service}/{path?}', [ProxyController::class, 'proxy'])->where('path', '.*');

// AUTH routes (no token check)
// Route::prefix('auth')->group(function () {
//     Route::any('{path?}', [ProxyController::class, 'proxy'])
//         ->where('path', '.*')
//         ->defaults('service', 'auth');
// });

// // CRUD routes (with token middleware)
// Route::prefix('crud')->middleware('validate.auth')->group(function () {
//     Route::any('{path?}', [ProxyController::class, 'proxy'])
//         ->where('path', '.*')
//         ->defaults('service', 'crud');
// });

// Generic fallback for all others (kept as last)
Route::any('{service}/{path?}', [ProxyController::class, 'proxy'])->where('path', '.*');

// AUTH routes (no token check)
Route::prefix('auth')->group(function () {
    Route::any('{path?}', [ProxyController::class, 'proxy'])
        ->defaults('service', 'auth')
        ->where('path', '.*');
});

// CRUD routes (with token middleware)
Route::prefix('crud')->middleware('validate.auth')->group(function () {
    Route::any('{path?}', [ProxyController::class, 'proxy'])
        ->defaults('service', 'crud')
        ->where('path', '.*');
});
