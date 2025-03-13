<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware('auth:sanctum')->group(function () {
//     // Get user info
//     Route::get('/user', function (Request $request) {
//         return $request->user();
//     });
    
//     // Module and Calendar Routes
//     Route::prefix('modules')->group(function () {
//         // Get all modules for current user
//         Route::get('/', [ModuleController::class, 'getModules']);
//         Route::get('/modules/{groupId}', [ModuleController::class, 'getModules']);
        
//         // Update weekly progress for a module
//         Route::post('/update-progress', [ModuleController::class, 'updateWeeklyProgress']);
        
//         // Update module dates (start date or exam date)
//         Route::post('/update-date', [ModuleController::class, 'updateModuleDate']);
        
//         // Update progress session date (for custom scheduling)
//         Route::post('/update-session-date', [ModuleController::class, 'updateProgressSessionDate']);
        
//         // Save all changes to database
//         Route::post('/save-all', [ModuleController::class, 'saveAllChanges']);
//     });
// });




