<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\Security\askForPasswordReset;
use App\Http\Controllers\Security\PasswordResetting;

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
    return view('welcome');
});

//user account creation
Route::get('login',[UserAuthController::class, 'login'])->name('logIn'); //route pour la page de connexion
Route::get('register',[UserAuthController::class, 'register']); //route pour la page d'inscription
Route::get('logout',[UserAuthController::class, 'logout']);//route pour la "page" de déconnexion
Route::post('create',[UserAuthController::class, 'create'])->name('auth/create');//route pour la vérification du formulaire d'inscription
Route::post('check',[UserAuthController::class, 'check'])->name('auth/check');//route pour la vérification du formulaire de connexion
Route::get('dashboard',[UserAuthController::class, 'dashboard'])->middleware('isLogged');//route pour la page de bievenue de l'utilisateur


//user data edit
Route::get('user/edit',[UserController::class, 'editUser'])->middleware('isLogged');;
Route::post('user/edit',[UserController::class, 'checkEditUser']);
Route::get('user/delete',[UserController::class, 'deleteAccountUser']);


//BEGINING OF 'CHANGE PASSWORD' ROUTES (Edit by FAUGIER Elliot 22/03/2021)
Route::get('change-password', [askForPasswordReset::class, 'form']);
Route::post('change-password', [askForPasswordReset::class, 'formSubmission']);
Route::get('reset-password/{token}', [PasswordResetting::class, 'form']);
Route::post('reset-password/', [PasswordResetting::class, 'formSubmission']);
//END OF 'CHANGE PASSWORD'  ROUTES (Edit by FAUGIER Elliot 22/03/2021)
