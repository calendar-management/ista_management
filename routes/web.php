<?php

use App\Http\Controllers\AdministrateurController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', function () {
    return redirect()->route('dashboard');
});



Route::middleware('auth')->group(function () {
    Route::post('/logout',[AuthenticatedSessionController::class,'destroy']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::view('/dashboard','admin.dashboard')->name('dashboard');
    Route::view('/sup_adm_dashboard',"supadmin.dashboard");
    Route::view('/adm_dashboard',"admin.dashboard");    

    Route::get('/gestion_formateur', [FormateurController::class, 'index']);

    Route::get('/gestion_adm', [AdministrateurController::class, 'index']);
    // hsnnnnnnnnnnnn
    Route::get('/formateur_calendar',function(){
        return view("formateur.calendar");
    });

    Route::view('/add_admin','supadmin.add_adm')->name('add_admin');

    Route::post('/add_admin',[AdministrateurController::class, 'add']);
    Route::get('/edit_adm/{id}',[AdministrateurController::class, 'edit'])->name('edit_adm');
    Route::put('/edit_adm/{id}',[AdministrateurController::class, 'update'])->name("update_admin");
    Route::delete('/delete_admin/{id}',[AdministrateurController::class, 'delete'])->name("delete_admin");

});

require __DIR__.'/auth.php';

// Module and Calendar Routes
Route::prefix('modules')->group(function () {
    // Get all modules for current user
    Route::get('/', [ModuleController::class, 'getModules']);
    Route::get('/modules/{groupId}', [ModuleController::class, 'getModules']);
    
    // Update weekly progress for a module
    Route::post('/update-progress', [ModuleController::class, 'updateWeeklyProgress']);
    
    // Update module dates (start date or exam date)
    Route::post('/update-date', [ModuleController::class, 'updateModuleDate']);
    
    // Update progress session date (for custom scheduling)
    Route::post('/update-session-date', [ModuleController::class, 'updateProgressSessionDate']);
    
    // Save all changes to database
    Route::post('/save-all', [ModuleController::class, 'saveAllChanges']);
});

Route::get('/formateur_calendar', [ModuleController::class, 'showCalendar'])->name('calendar');



Route::post('/save-calendar-data', [ModuleController::class, 'saveCalendarData'])->name('calendar.save');

