<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Esims\EsimController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\Admin\DashboardStatController;

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

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/api/locale', function () {
    return App::currentLocale();
});
Route::post('/api/locale', function () {
    // Validate
    $validated = request()->validate([
        'language' => ['required'],
    ]);
    // Set locale
    App::setLocale($validated['language']);
    // Session
    session()->put('locale', $validated['language']);
    // Response
    //return redirect()->back();
    return App::currentLocale();
});

Route::middleware('auth')->group(function () {
    Route::get('/api/abilities', [ProfileController::class, 'abilities']);

    #region PERMISSIONS
    Route::get('permissions', PermissionController::class);
    Route::get('/api/permissions/', [PermissionController::class, 'index']);
    Route::get('/api/permissions/count/', [PermissionController::class, 'count']);
    Route::get('roles', RoleController::class);
    Route::get('/api/roles/', [RoleController::class, 'index']);
    Route::post('/api/roles/', [RoleController::class, 'store']);
    Route::get('/api/roles/{role}/edit', [RoleController::class, 'edit']);
    Route::put('/api/roles/{role}', [RoleController::class, 'update']);
    Route::patch('/api/roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions']);
    Route::patch('/api/roles/{role}/revoke-permissions', [RoleController::class, 'revokePermissions']);
    #endregion

    Route::get('/api/stats/appointments', [DashboardStatController::class, 'appointments']);
    Route::get('/api/stats/users', [DashboardStatController::class, 'users']);

    #region USERS
    Route::get('/api/users/', [UserController::class, 'index']);
    Route::post('/api/users/', [UserController::class, 'store']);
    Route::put('/api/users/{user}', [UserController::class, 'update']);
    Route::patch('/api/users/{user}/change-role', [UserController::class, 'changeRole']);
    Route::delete('/api/users/{user}', [UserController::class, 'destory']);
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);
    #endregion

    #region SETTINGS
    Route::get('/api/settings/fetch', [SettingController::class, 'fetch']);
    Route::get('/api/settings', [SettingController::class, 'index']);
    Route::put('/api/settings/{setting}', [SettingController::class, 'update']);
    Route::get('/api/settinggroups', [SettingController::class, 'groups']);
    Route::get('/api/settings/{setting}/edit', [SettingController::class, 'edit']);
    #endregion

    #region PROFILE
    Route::get('/api/profile', [ProfileController::class, 'index']);
    Route::put('/api/profile', [ProfileController::class, 'update']);
    Route::post('/api/upload-profile-image', [ProfileController::class, 'uploadImage']);
    Route::post('/api/change-user-password', [ProfileController::class, 'changePassword']);
    #endregion

    #region ESIMS
    Route::get('/api/esims', [EsimController::class, 'index']);
    #endregion
});
Route::get('{view}', ApplicationController::class)->where('view', '(.*)')->middleware('auth');
