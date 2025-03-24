<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdministrateurController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\MyCalendarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileControllerTest;
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



Route::get('/',[AccountController::class,'redirectUser']);
Route::get('/register',[AccountController::class,'redirectUser2']);

Route::middleware(['auth','formateur'])->group(function (){
    Route::get('/formateur_dashboard',[DashboardController::class,'index'])->name('formateur.dashboard');
    Route::get('/formateur_group/{group}', [DashboardController::class, 'groupDetail'])->name('formateur.group.detail');
    Route::get('/formateur_calendar', [ModuleController::class, 'showCalendar'])->name('calendar');
    Route::post('/save-calendar-data', [ModuleController::class, 'saveCalendarData'])->name('calendar.save');
    Route::get('/download/{filename}', [FormateurController::class, 'downloadFile']);
});

Route::middleware(['auth','admin'])->group(function (){
    Route::get('/export-weekly-progress/{id}', [FormateurController::class, 'exportExcel'])->name('export.weekly_progress');
    Route::delete('/delete_formateur/{id}',[FormateurController::class,'destroy'])->name('formateurs.destroy');
    Route::get('/formateur_progress/{id}',[FormateurController::class,'progress'])->name('teacher.progress');
    Route::get('/edit_formateur/{id}',[FormateurController::class,'edit'])->name('edit_formateur');
    Route::put('/edit_formateur/{id}',[FormateurController::class,'update'])->name('formateurs.update');
    Route::view('/adm_dashboard',"admin.dashboard")->name('adm_dashboard');    
    Route::get('/gestion_formateur', [FormateurController::class, 'index']);
    Route::view('/add_formateur', 'admin.add_frm')->name('add_formateur');
    Route::post('/add_formateur', [FormateurController::class, 'add']);
    Route::post('/gestion_formateur', [FormateurController::class, 'import'])->name('import_file');
    Route::get('/search', [FormateurController::class, 'search'])->name('formateurs.search');

    Route::view('/gestion_calendrier', 'admin.gestion_calendrier')->name("gestion_calendrier");
    Route::post('/gestion_calendrier', [CalendarController::class, 'add'])->name('add_vacances');
    Route::get('/fetch-vacations', [CalendarController::class, 'fetchVacations']);
    Route::delete('/delete-vacation/{id}', [CalendarController::class, 'destroy'])->name('delete_vacation');
});

Route::middleware(['auth','super_admin'])->group(function (){
    Route::view('/sup_adm_dashboard',"supadmin.dashboard")->name('sup_adm_dashboard');
    Route::get('/gestion_adm', [AdministrateurController::class, 'index']);
    Route::view('/add_admin', 'supadmin.add_adm')->name('add_admin');
    Route::post('/add_admin', [AdministrateurController::class, 'add']);
    Route::get('/edit_adm/{id}', [AdministrateurController::class, 'edit'])->name('edit_adm');
    Route::put('/edit_adm/{id}', [AdministrateurController::class, 'update'])->name("update_admin");
    Route::delete('/delete_admin/{id}', [AdministrateurController::class, 'delete'])->name("delete_admin");
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard',[AccountController::class,'redirectUser'])->name('dashboard');
    

    Route::prefix('modules')->group(function () {
        // Get all modules for current user
        // Route::get('/', [ModuleController::class, 'getModules']);
        // Route::get('/modules/{groupId}', [ModuleController::class, 'getModules']);
        
        // Update weekly progress for a module
        Route::post('/update-progress', [ModuleController::class, 'updateWeeklyProgress']);
        
        // Update module dates (start date or exam date)
        Route::post('/update-date', [ModuleController::class, 'updateModuleDate']);
        
        // Update progress session date (for custom scheduling)
        Route::post('/update-session-date', [ModuleController::class, 'updateProgressSessionDate']);
        
        // Save all changes to database
        Route::post('/save-all', [ModuleController::class, 'saveAllChanges']);
    });

    

    

});


require __DIR__.'/auth.php';




