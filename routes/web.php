<?php

use App\Http\Controllers\AdministrateurController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\ProfileController;
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






