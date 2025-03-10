<?php
use App\Http\Controllers\AdministrateurController;

use App\Http\Controllers\FormateurController;
use App\Http\Controllers\FormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::get('/dashboard',function(){
    return view("admin.dashboard");
});
Route::get('/sup_adm_dashboard',function(){
    return view("supadmin.dashboard");
});
Route::get('/adm_dashboard',function(){
    return view("admin.dashboard");
});
// Route::get('/gestion_adm',function(){
//     return view("supadmin.gestion_admin");
// });


Route::get('/gestion_formateur', [FormateurController::class, 'index']);

Route::get('/gestion_adm', [AdministrateurController::class, 'index']);



Route::view('/add_admin','supadmin.add_adm')->name('add_admin');

Route::post('/add_admin',[AdministrateurController::class, 'add']);
Route::get('/edit_adm/{id}',[AdministrateurController::class, 'edit'])->name('edit_adm');
Route::put('/edit_adm/{id}',[AdministrateurController::class, 'update'])->name("update_admin");
Route::delete('/delete_admin/{id}',[AdministrateurController::class, 'delete'])->name("delete_admin");

Route::view('/add_formateur','admin.add_frm')->name('add_formateur');
Route::post('/add_formateur',[FormateurController::class, 'add']);
Route::post('/gestion_formateur',[FormateurController::class, 'import'])->name('import_file');
Route::view('/gestion_calendrier', 'admin.gestion_calendrier')->name('gestion_calendrier');

