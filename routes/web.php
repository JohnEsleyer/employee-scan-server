<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DTRController;
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
    return view('welcome');
});

Route::get('/dtr', function () {
    return view('dtr');
});

Route::get('/add_user', function () {
    return view('add_user');
});

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'loginWeb']);

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

// Pages
Route::middleware(['auth'])->group(function () {
    Route::get('/test', [DTRController::class, 'test']);
    Route::post('/search-employee', [DTRController::class, 'search'])->name('dtr.search');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dtr', [DTRController::class, 'index']);
    Route::delete('/employee/{id}', [EmployeeController::class, 'destroyEmployeeView'])->name('employees.destroy');
    Route::post('/employee', [EmployeeController::class, 'storeEmployeeView'])->name('employees.store');
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroyAttendanceView'])->name('attendances.destroy');
    Route::get('/add_user', [UsersController::class, 'index'])->name('users');
    Route::get('/users/{id}', 'UserController@show')->name('users.show');
});