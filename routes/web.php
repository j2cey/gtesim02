<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Esims\EsimController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Esims\EsimStateController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\Esims\StatutEsimController;
use App\Http\Controllers\Esims\ClientEsimController;
use App\Http\Controllers\Employes\EmployeController;
use App\Http\Controllers\Employes\PhoneNumController;
use App\Http\Controllers\Admin\DashboardStatController;
use App\Http\Controllers\Employes\DepartementController;
use App\Http\Controllers\Esims\TechnologieEsimController;
use App\Http\Controllers\Employes\EmailAddressController;
use App\Http\Controllers\Employes\FonctionEmployeController;
use App\Http\Controllers\Employes\TypeDepartementController;

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

Route::get('clientesims.previewpdf/{id}',[ClientEsimController::class,'previewPDF'])
    ->name('clientesims.previewpdf')
    ->middleware('auth');
Route::get('clientesims.preprintpdf/{id}',[ClientEsimController::class,'preprintPDF'])
    ->name('clientesims.preprintpdf')
    ->middleware('auth');
Route::get('clientesims.generatepdf/{id}',[ClientEsimController::class,'generatePDF'])
    ->name('clientesims.generatepdf')
    ->middleware('auth');

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
    Route::get('/api/users/', [UserController::class, 'index'])->name('users.');
    Route::post('/api/users/', [UserController::class, 'store'])->name('users.store');
    Route::put('/api/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/api/users/{user}/edit', [UserController::class, 'edit']);
    Route::patch('/api/users/{user}/change-role', [UserController::class, 'changeRole']);
    Route::delete('/api/users/{user}', [UserController::class, 'destory'])->name('users.destory');
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);
    #endregion

    #region Status
    Route::get('/api/statuses', [StatusController::class, 'index'])->name('statuses.');
    Route::get('/api/statuses/{status}/edit', [StatusController::class, 'edit'])->name('statuses.edit');
    Route::post('/api/statuses/', [StatusController::class, 'store'])->name('statuses.store');
    Route::put('/api/statuses/{status}', [StatusController::class, 'update'])->name('statuses.update');
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
    Route::get('/api/esims/{esim}/edit', [EsimController::class, 'edit']);
    Route::post('/api/esims/', [EsimController::class, 'store']);
    Route::put('/api/esims/{esim}', [EsimController::class, 'update']);
    Route::delete('/api/esims/{esim}', [EsimController::class, 'destroy']);

    Route::get('/api/esimstatuses', [StatutEsimController::class, 'index']);

    Route::get('/api/esimstates/{esim}/esimindex', [EsimStateController::class, 'esimindex']);

    Route::get('/api/esimtechnologies', [TechnologieEsimController::class, 'index']);

    #endregion

    #region ClientEsims
    Route::get('/api/clientesims', [ClientEsimController::class, 'index'])->name('clientesims.index');
    Route::get('/api/clientesims/{clientesim}/edit', [ClientEsimController::class, 'edit'])->name('clientesims.edit');

    Route::post('/api/clientesims/', [ClientEsimController::class, 'store'])->name('clientesims.store');
    Route::post('/api/clientesims.checkbeforecreate',[ClientEsimController::class,'checkbeforecreate'])->name('clientesims.checkbeforecreate');

    Route::get('/api/clientesims/{clientesim}/show', [ClientEsimController::class, 'show'])->name('clientesims.show');
    Route::get('/api/clientesims/{clientesim}/phonenumindex', [ClientEsimController::class, 'phonenumindex']);
    Route::get('/api/clientesims/{clientesim}/emailaddressindex', [ClientEsimController::class, 'emailaddressindex']);
    Route::post('/api/clientesim/phonenums/add', [ClientEsimController::class, 'addphone'])->name('clientesimphonenums.add');
    Route::put('/api/clientesim/{clientesim}/phonenums/add', [ClientEsimController::class, 'phonenumadd'])->name('clientesims.phonenumadd');
    Route::put('/api/clientesim/{clientesim}/phonenums/{phonenum}', [ClientEsimController::class, 'phonenumupdate'])->name('clientesimphonenums.update');
    Route::put('/api/clientesim/{clientesim}/emailaddresses/add', [ClientEsimController::class, 'emailaddressadd'])->name('clientesims.emailaddressadd');
    Route::put('/api/clientesim/{clientesim}/emailaddresses/{emailaddress}', [ClientEsimController::class, 'emailaddressupdate'])->name('clientesimemailaddresses.update');
    #endregion

    #region PhoneNum
    Route::get('/api/phonenums', [PhoneNumController::class, 'index'])->name('phonenums.');
    Route::get('/api/phonenums/{phonenum}/edit', [PhoneNumController::class, 'edit'])->name('phonenums.edit');
    Route::get('/api/phonenums/{phonenum}/show', [PhoneNumController::class, 'show'])->name('phonenums.show');
    Route::post('/api/phonenums/', [PhoneNumController::class, 'store'])->name('phonenums.store');
    Route::delete('/api/phonenums/{phonenum}', [PhoneNumController::class, 'destroy'])->name('phonenums.destory');
    Route::put('/api/phonenums/{phonenum}', [PhoneNumController::class, 'update'])->name('phonenums.update');
    Route::put('/api/phonenums/{phonenum}/esimrecycle', [PhoneNumController::class, 'esimrecycle'])->name('phonenums.esimrecycle');
    #endregion

    #region EmailAddress
    Route::get('/api/emailaddresses', [EmailAddressController::class, 'index'])->name('emailaddresses.');
    Route::get('/api/emailaddresses/{emailaddress}/edit', [EmailAddressController::class, 'edit'])->name('emailaddresses.edit');
    Route::get('/api/emailaddresses/{emailaddress}/show', [EmailAddressController::class, 'show'])->name('emailaddresses.show');
    Route::post('/api/emailaddresses/', [EmailAddressController::class, 'store'])->name('emailaddresses.store');
    Route::delete('/api/emailaddresses/{emailaddress}', [EmailAddressController::class, 'destroy'])->name('emailaddresses.destory');
    #endregion

    #region FonctionEmploye
    Route::get('/api/fonctionemployes', [FonctionEmployeController::class, 'index'])->name('fonctionemployes.');
    Route::get('/api/fonctionemployes/{fonctionemploye}/edit', [FonctionEmployeController::class, 'edit'])->name('fonctionemployes.edit');
    Route::get('/api/fonctionemployes/{fonctionemploye}/show', [FonctionEmployeController::class, 'show'])->name('fonctionemployes.show');
    Route::post('/api/fonctionemployes/', [FonctionEmployeController::class, 'store'])->name('fonctionemployes.store');
    #endregion

    #region TypeDepartement
    Route::get('/api/typedepartements', [TypeDepartementController::class, 'index'])->name('typedepartements.');
    Route::get('/api/typedepartements/{typedepartement}/edit', [TypeDepartementController::class, 'edit'])->name('typedepartements.edit');
    Route::get('/api/typedepartements/{typedepartement}/show', [TypeDepartementController::class, 'show'])->name('typedepartements.show');
    Route::post('/api/typedepartements/', [TypeDepartementController::class, 'store'])->name('typedepartements.store');
    #endregion

    #region Departement
    Route::get('/api/departements', [DepartementController::class, 'index'])->name('departements.');
    Route::get('/api/departements/{departement}/edit', [TypeDepartementController::class, 'edit'])->name('departements.edit');
    Route::get('/api/departements/{departement}/show', [TypeDepartementController::class, 'show'])->name('departements.show');
    Route::post('/api/departements/', [TypeDepartementController::class, 'store'])->name('departements.store');
    #endregion

    #region Employe
    Route::get('/api/employes', [EmployeController::class, 'index'])->name('employes.');
    Route::get('/api/employes/{employe}/edit', [EmployeController::class, 'edit'])->name('employes.edit');
    Route::get('/api/employes/{employe}/show', [EmployeController::class, 'show'])->name('employes.show');
    Route::post('/api/employes/', [EmployeController::class, 'store'])->name('employes.store');
    #endregion

});
Route::get('{view}', ApplicationController::class)->where('view', '(.*)')->middleware('auth');
