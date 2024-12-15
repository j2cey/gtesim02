<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tags\TagController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Esims\EsimController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\HowTos\HowToController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\LdapUserController;
use App\Http\Controllers\Esims\EsimStateController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\Aris\ArisStatusController;
use App\Http\Controllers\Esims\StatutEsimController;
use App\Http\Controllers\Esims\ClientEsimController;
use App\Http\Controllers\Employes\EmployeController;
use App\Http\Controllers\Persons\PhoneNumController;
use App\Http\Controllers\HowTos\HowToTypeController;
use App\Http\Controllers\HowTos\HowToStepController;
use App\Http\Controllers\Comments\CommentController;
use App\Http\Controllers\HowTos\HowToThreadController;
use App\Http\Controllers\Admin\DashboardStatController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Employes\DepartementController;
use App\Http\Controllers\Persons\EmailAddressController;
use App\Http\Controllers\Esims\TechnologieEsimController;
use App\Http\Controllers\Employes\FonctionEmployeController;
use App\Http\Controllers\Employes\TypeDepartementController;

// TODO: Split Laravel routes in separated files

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

#region ArisStatus
Route::post('/api/arisstatuses/', [ArisStatusController::class, 'store'])->name('arisstatuses.store');
#endregion

Route::middleware('auth')->group(function () {
    Route::get('/api/abilities', [ProfileController::class, 'abilities']);

    #region ArisStatus
    Route::get('/api/arisstatuses', [ArisStatusController::class, 'index'])->name('arisstatuses.index');
    Route::get('/api/arisstatuses/{esim}', [ArisStatusController::class, 'esimindex'])->name('arisstatuses.esimindex');
    #endregion

    #region Dashboard
    Route::get('/api/dashboards.details',[DashboardController::class,'detailsget'])
        ->name('dashboards.detailsget')
        ->middleware('auth');
    Route::post('/api/dashboards.details',[DashboardController::class,'detailspost'])
        ->name('dashboards.detailspost')
        ->middleware('auth');

    Route::get('/api/dashboards.fetchrawstats',[DashboardController::class,'fetchrawstats'])
        ->name('dashboards.fetchrawstats')
        ->middleware('auth');

    Route::get('/api/dashboards.fetchmonthsofyear',[DashboardController::class,'fetchmonthsofyear'])
        ->name('dashboards.fetchmonthsofyear')
        ->middleware('auth');

    Route::get('/api/dashboards.fetchcurrentmonth',[DashboardController::class,'fetchcurrentmonth'])
        ->name('dashboards.fetchcurrentmonth')
        ->middleware('auth');

    Route::get('/api/dashboards.fetchweeksofyear',[DashboardController::class,'fetchweeksofyear'])
        ->name('dashboards.fetchweeksofyear')
        ->middleware('auth');

    Route::get('/api/dashboards.fetchcurrentweek',[DashboardController::class,'fetchcurrentweek'])
        ->name('dashboards.fetchcurrentweek')
        ->middleware('auth');

    Route::get('/api/dashboards.fetchmonthstats/{month}/{agence}',[DashboardController::class,'fetchmonthstats'])
        ->name('dashboards.fetchmonthstats')
        ->middleware('auth');

    Route::get('/api/dashboards.fetchweekstats/{week}/{agence}',[DashboardController::class,'fetchweekstats'])
        ->name('dashboards.fetchweekstats')
        ->middleware('auth');

    Route::get('/api/dashboards.fetchyears',[DashboardController::class,'fetchyears'])
        ->name('dashboards.fetchyears')
        ->middleware('auth');

    Route::get('/api/dashboards.fetchcurrentyear',[DashboardController::class,'fetchcurrentyear'])
        ->name('dashboards.fetchcurrentyear')
        ->middleware('auth');

    Route::get('/api/dashboards.fetchyearstats/{year}/{agence}',[DashboardController::class,'fetchyearstats'])
        ->name('dashboards.fetchyearstats')
        ->middleware('auth');

    Route::get('/api/dashboards.fetchagencestats/{agence}',[DashboardController::class,'fetchagencestats'])
        ->name('dashboards.fetchagencestats')
        ->middleware('auth');
    #endregion

    #region Permissions
    Route::get('permissions', PermissionController::class);
    Route::get('/api/permissions/', [PermissionController::class, 'index']);
    Route::get('/api/permissions/count/', [PermissionController::class, 'count']);
    //Route::get('roles', RoleController::class);
    Route::get('/api/roles/', [RoleController::class, 'index']);
    Route::post('/api/roles/', [RoleController::class, 'store']);
    Route::get('/api/roles/{role}/edit', [RoleController::class, 'edit']);
    Route::put('/api/roles/{role}', [RoleController::class, 'update']);
    Route::patch('/api/roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions']);
    Route::patch('/api/roles/{role}/revoke-permissions', [RoleController::class, 'revokePermissions']);
    #endregion

    Route::get('/api/stats/appointments', [DashboardStatController::class, 'appointments']);
    Route::get('/api/stats/users', [DashboardStatController::class, 'users']);

    #region LdapUsers
    Route::get('/api/ldapusers', [LdapUserController::class, 'index'])->name('ldapusers.index');
    Route::get('/api/ldapusers/{ldapuser}/edit', [LdapUserController::class, 'edit']);
    Route::put('/api/ldapusers/{ldapuser}', [LdapUserController::class, 'update']);
    Route::put('/api/ldapusers/{ldapuser}/integrate', [LdapUserController::class, 'integrate']);
    Route::post('/api/ldapusers/{user?}', [LdapUserController::class, 'store']);
    #endregion

    ##region Users
    Route::get('/api/users/', [UserController::class, 'index'])->name('users.index');
    Route::post('/api/users/', [UserController::class, 'store'])->name('users.store');
    Route::put('/api/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/api/users/{user}/resetpwd', [UserController::class, 'resetpwd'])->name('users.resetpwd');
    Route::get('/api/users/{user}/edit', [UserController::class, 'edit']);
    Route::patch('/api/users/{user}/change-role', [UserController::class, 'changeRole']);
    Route::delete('/api/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);
    Route::patch('/api/users/{user}/assign-roles', [UserController::class, 'assignRoles']);
    Route::patch('/api/users/{user}/revoke-roles', [UserController::class, 'revokeRoles']);
    Route::put('/api/users/{user}/accountinfossendmail', [UserController::class, 'accountinfossendmail'])->name('users.accountinfossendmail');
    #endregion

    #region Status
    Route::get('/api/statuses', [StatusController::class, 'index'])->name('statuses.index');
    Route::get('/api/statuses/codes', [StatusController::class, 'fetchCodes'])->name('statuses.codes');
    Route::get('/api/statuses/{status}/edit', [StatusController::class, 'edit'])->name('statuses.edit');
    Route::post('/api/statuses/', [StatusController::class, 'store'])->name('statuses.store');
    Route::put('/api/statuses/{status}', [StatusController::class, 'update'])->name('statuses.update');
    Route::get('/api/statuses/change', [StatusController::class, 'statuschange'])->name('statuses.change');
    #endregion

    #region Settings
    Route::get('/api/settings/fetch', [SettingController::class, 'fetch']);
    Route::get('/api/settings/test', [SettingController::class, 'test']);
    Route::get('/api/settings', [SettingController::class, 'index']);
    Route::put('/api/settings/{setting}', [SettingController::class, 'update']);
    Route::get('/api/settinggroups', [SettingController::class, 'groups']);
    Route::get('/api/settings/{setting}/edit', [SettingController::class, 'edit']);
    #endregion

    #region Profile
    Route::get('/api/profile', [ProfileController::class, 'index']);
    Route::put('/api/profile', [ProfileController::class, 'update']);
    Route::post('/api/upload-profile-image', [ProfileController::class, 'uploadImage']);
    Route::post('/api/change-user-password', [ProfileController::class, 'changePassword']);
    #endregion

    #region Esims
    Route::get('/api/esims', [EsimController::class, 'index']);
    Route::get('/api/esims/{user}/attributed', [EsimController::class, 'esimsattributed']);
    Route::get('/api/esims/{esim}/edit', [EsimController::class, 'edit']);
    Route::get('/api/esims/{esimid?}/pickup', [EsimController::class, 'pickup'])->name('esims.pickup');
    Route::get('/api/esims/{esimid}/release', [EsimController::class, 'release'])->name('esims.release');
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
    Route::get('/api/clientesims/{clientesim}/phonenums', [ClientEsimController::class, 'phonenums']);
    Route::get('/api/clientesims/{clientesim}/emailaddressindex', [ClientEsimController::class, 'emailaddressindex']);
    //Route::post('/api/clientesims/phonenums/add', [ClientEsimController::class, 'addphone'])->name('clientesimphonenums.add');
    Route::put('/api/clientesims/{clientesim}/phonenums/add', [ClientEsimController::class, 'phonenumadd'])->name('clientesims.phonenumadd');
    Route::put('/api/clientesims/{clientesim}/phonenums/validate', [ClientEsimController::class, 'phonenumvalidate'])->name('clientesims.phonenumvalidate');
    //Route::put('/api/clientesims/{clientesim}/phonenums/{phonenum}', [ClientEsimController::class, 'phonenumupdate'])->name('clientesimphonenums.update');
    Route::put('/api/clientesims/{clientesim}/emailaddresses/add', [ClientEsimController::class, 'emailaddressadd'])->name('clientesims.emailaddressadd');
    //Route::put('/api/clientesims/{clientesim}/emailaddresses/{emailaddress}', [ClientEsimController::class, 'emailaddressupdate'])->name('clientesims.emailaddressupdate');

    Route::get('/api/clientesims/{clientesim}/statuschange', [ClientEsimController::class, 'statuschange'])->name('clientesims.statuschange');
    #endregion

    #region PhoneNum
    Route::get('/api/phonenums', [PhoneNumController::class, 'index'])->name('phonenums.');
    Route::get('/api/phonenums/{phonenum}/edit', [PhoneNumController::class, 'edit'])->name('phonenums.edit');
    Route::get('/api/phonenums/{phonenum}/show', [PhoneNumController::class, 'show'])->name('phonenums.show');
    Route::post('/api/phonenums/', [PhoneNumController::class, 'store'])->name('phonenums.store');
    Route::delete('/api/phonenums/{phonenum}', [PhoneNumController::class, 'destroy'])->name('phonenums.destory');
    Route::put('/api/phonenums/{phonenum}', [PhoneNumController::class, 'update'])->name('phonenums.update');
    Route::put('/api/phonenums/{phonenum}/esimrecycle', [PhoneNumController::class, 'esimrecycle'])->name('phonenums.esimrecycle');
    Route::put('/api/phonenums/{phonenum}/esimsendmail', [PhoneNumController::class, 'esimsendmail'])->name('phonenums.esimsendmail');
    #endregion

    #region EmailAddress
    Route::get('/api/emailaddresses', [EmailAddressController::class, 'index'])->name('emailaddresses.index');
    Route::get('/api/emailaddresses/{emailaddress}/edit', [EmailAddressController::class, 'edit'])->name('emailaddresses.edit');
    Route::get('/api/emailaddresses/{emailaddress}/show', [EmailAddressController::class, 'show'])->name('emailaddresses.show');
    Route::post('/api/emailaddresses/', [EmailAddressController::class, 'store'])->name('emailaddresses.store');
    Route::put('/api/emailaddresses/{emailaddress}', [EmailAddressController::class, 'update'])->name('emailaddresses.update');
    Route::delete('/api/emailaddresses/{emailaddress}', [EmailAddressController::class, 'destroy'])->name('emailaddresses.destory');
    #endregion

    #region FonctionEmploye
    Route::get('/api/fonctionemployes', [FonctionEmployeController::class, 'index'])->name('fonctionemployes.index');
    Route::get('/api/fonctionemployes/all', [FonctionEmployeController::class, 'fetchall'])->name('fonctionemployes.fetchall');
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
    Route::get('/api/departements', [DepartementController::class, 'index'])->name('departements.index');
    Route::get('/api/departements/all', [DepartementController::class, 'fetchall'])->name('departements.fetchall');
    Route::get('/api/departements/{departement}/edit', [DepartementController::class, 'edit'])->name('departements.edit');
    Route::get('/api/departements/{departement}/show', [DepartementController::class, 'show'])->name('departements.show');
    Route::post('/api/departements/', [DepartementController::class, 'store'])->name('departements.store');
    #endregion

    #region Employe
    Route::get('/api/employes', [EmployeController::class, 'index'])->name('employes.index');
    Route::get('/api/employes/{employe}/phonenums', [EmployeController::class, 'phonenums'])->name('employes.phonenums');
    Route::put('/api/employes/{employe}/phonenums/add', [EmployeController::class, 'phonenumadd'])->name('employes.phonenumadd');
    Route::put('/api/employes/{employe}/emailaddresses/add', [EmployeController::class, 'emailaddressadd'])->name('employes.emailaddressadd');
    Route::get('/api/employes/{employe}/emailaddressindex', [EmployeController::class, 'emailaddressindex'])->name('employes.emailaddressindex');
    Route::get('/api/employes/{employe}/edit', [EmployeController::class, 'edit'])->name('employes.edit');
    Route::get('/api/employes/{employe}/show', [EmployeController::class, 'show'])->name('employes.show');
    Route::post('/api/employes/{user?}', [EmployeController::class, 'store'])->name('employes.store');
    Route::put('/api/employes/{employe}', [EmployeController::class, 'update'])->name('employes.update');
    #endregion

    #region HowToTypes
    Route::get('/api/howtotypes', [HowToTypeController::class, 'index'])->name('howtotypes.index');
    Route::get('/api/howtotypes/all', [HowToTypeController::class, 'fetchall'])->name('howtotypes.fetchall');
    Route::get('/api/howtotypes/{howtotype}/edit', [HowToTypeController::class, 'edit'])->name('howtotypes.edit');
    Route::get('/api/howtotypes/{howtotype}/show', [HowToTypeController::class, 'show'])->name('howtotypes.show');
    Route::post('/api/howtotypes/', [HowToTypeController::class, 'store'])->name('howtotypes.store');
    #endregion

    #region HowTos
    Route::get('/api/howtos', [HowToController::class, 'index'])->name('howtos.index');
    Route::get('/api/howtos/all', [HowToController::class, 'fetchall'])->name('howtos.fetchall');
    Route::get('/api/howtos/{howto}/edit', [HowToController::class, 'edit'])->name('howtos.edit');
    Route::get('/api/howtos/{howto}/show', [HowToController::class, 'show'])->name('howtos.show');
    Route::post('/api/howtos/', [HowToController::class, 'store'])->name('howtos.store');
    Route::put('/api/howtos/{howto}', [HowToController::class, 'update'])->name('howtos.update');
    Route::put('/api/howtos/{howto}/storehtml', [HowToController::class, 'storehtml'])->name('howtos.storehtml');
    #endregion

    #region HowToStep
    Route::get('/api/howtosteps', [HowToStepController::class, 'index'])->name('howtosteps.index');
    Route::get('/api/howtosteps/all', [HowToStepController::class, 'fetchall'])->name('howtosteps.fetchall');
    Route::get('/api/howtosteps/howtothread/{howtothread}/threadsteps', [HowToStepController::class, 'threadsteps'])->name('howtosteps.threadsteps');
    Route::get('/api/howtosteps/{howtostep}/edit', [HowToStepController::class, 'edit'])->name('howtosteps.edit');
    Route::get('/api/howtosteps/{howtostep}/show', [HowToStepController::class, 'show'])->name('howtosteps.show');
    Route::post('/api/howtosteps/', [HowToStepController::class, 'store'])->name('howtosteps.store');
    Route::put('/api/howtosteps/{howtostep}', [HowToStepController::class, 'update'])->name('howtosteps.update');
    #endregion

    #region HowToThread
    Route::get('/api/howtothreads', [HowToThreadController::class, 'index'])->name('howtothreads.index');
    Route::get('/api/howtothreads/all', [HowToThreadController::class, 'fetchall'])->name('howtothreads.fetchall');
    Route::get('/api/howtothreads/{howtothread}/edit', [HowToThreadController::class, 'edit'])->name('howtothreads.edit');
    Route::get('/api/howtothreads/{howtothread}/show', [HowToThreadController::class, 'show'])->name('howtothreads.show');
    Route::get('/api/howtothreads/{howtothread}/steps', [HowToThreadController::class, 'steps'])->name('howtothreads.steps');
    Route::post('/api/howtothreads/', [HowToThreadController::class, 'store'])->name('howtothreads.store');
    Route::put('/api/howtothreads/{howtothread}', [HowToThreadController::class, 'update'])->name('howtothreads.update');
    #endregion

    #region Tags
    Route::get('/api/tags', [TagController::class, 'index'])->name('tags.index');
    Route::get('/api/tags/all', [TagController::class, 'fetchall'])->name('tags.fetchall');
    Route::get('/api/tags/sync', [TagController::class, 'synctags'])->name('tags.sync');
    Route::put('/api/tags/add', [TagController::class, 'addtag'])->name('tags.add');
    Route::put('/api/tags/remove', [TagController::class, 'removetag'])->name('tags.remove');
    #endregion

    #region Comments
    Route::resource('/api/comments',CommentController::class);
    Route::get('/api/comments/fetchall',[CommentController::class,'fetchall'])
        ->name('comments.fetchall')
        ->middleware('auth');
    #endregion

});
Route::get('{view}', ApplicationController::class)->where('view', '(.*)')->middleware('auth');
