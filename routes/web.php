<?php

use App\Http\Controllers\AdministrateurController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group 
| containing the "web" middleware. 
|
*/

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout route
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard routes
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::view('/sup_adm_dashboard', "supadmin.dashboard");
    Route::view('/adm_dashboard', "admin.dashboard");

    // Administrator Management
    Route::view('/formateur_calendar', "formateur.calendar");
    Route::get('/gestion_adm', [AdministrateurController::class, 'index']);
    Route::view('/add_admin', 'supadmin.add_adm')->name('add_admin');
    Route::post('/add_admin', [AdministrateurController::class, 'add']);
    Route::get('/edit_adm/{id}', [AdministrateurController::class, 'edit'])->name('edit_adm');
    Route::put('/edit_adm/{id}', [AdministrateurController::class, 'update'])->name("update_admin");
    Route::delete('/delete_admin/{id}', [AdministrateurController::class, 'delete'])->name("delete_admin");

    // Trainer (Formateur) Management
    Route::get('/gestion_formateur', [FormateurController::class, 'index']);
    Route::view('/add_formateur', 'admin.add_frm')->name('add_formateur');
    Route::post('/add_formateur', [FormateurController::class, 'add']);
    Route::post('/gestion_formateur', [FormateurController::class, 'import'])->name('import_file');
    Route::get('/search', [FormateurController::class, 'search'])->name('formateurs.search');

    // Module and Calendar Routes
    Route::prefix('modules')->group(function () {
        Route::get('/', [ModuleController::class, 'getModules']);
        Route::get('/{groupId}', [ModuleController::class, 'getModules']);
        Route::post('/update-progress', [ModuleController::class, 'updateWeeklyProgress']);
        Route::post('/update-date', [ModuleController::class, 'updateModuleDate']);
        Route::post('/update-session-date', [ModuleController::class, 'updateProgressSessionDate']);
        Route::post('/save-all', [ModuleController::class, 'saveAllChanges']);
    });

    // vacances routes
    Route::view('/gestion_calendrier', 'admin.gestion_calendrier')->name("gestion_calendrier");
    Route::post('/gestion_calendrier', [CalendarController::class, 'add'])->name('add_vacances');
    Route::get('/fetch-vacations', [CalendarController::class, 'fetchVacations']);
    Route::delete('/delete-vacation/{id}', [CalendarController::class, 'destroy'])->name('delete_vacation');

    // Formateur Calendar

    Route::get('/formateur_calendar', [ModuleController::class, 'showCalendar'])->name('calendar');
    Route::post('/save-calendar-data', [ModuleController::class, 'saveCalendarData'])->name('calendar.save');
    Route::get('/download/{filename}', [FormateurController::class, 'downloadFile']);

});

// Include authentication routes
require __DIR__ . '/auth.php';
