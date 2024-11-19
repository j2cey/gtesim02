<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(function () {
            return view('admin.layouts.app');
        });

        Fortify::authenticateUsing(function ($request) {

            $user = User::whereEmail($request->email)->first();
            if ( ! $user ) {
                $user = User::whereLogin($request->email)->first();
            }

            if ( ! $user ) {
                throw ValidationException::withMessages([ 'email' => __('Invalid User.') ]);
            }
            if ( ! $user->isActive() ) {
                throw ValidationException::withMessages([ 'email' => __('User has been desactivated.') ]);
            }

            $validated = false;

            // Validate LDAP
            if ( $user->is_ldap ) {
                $user->ldapPwdSwitch();
                $validated = $this->tryAuthenticate($request);
                $user->ldapPwdSaveValidated($validated);
            }

            // Validate Eloquent
            if ( !$validated && $user->is_local ) {
                $user->localPwdSwitch();
                $validated = $this->tryAuthenticate($request);
                $user->localPwdSaveValidated($validated);
            }


            if ( $validated ) {
                // TODO: store the connection type, and the last pwd.
                return Auth::getLastAttempted();
            } else {
                return null;
            }
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }

    private function tryAuthenticate($request) {
        return Auth::validate([
            'samaccountname' => $request->email,
            'password' => $request->password,
            'fallback' => [
                'email' => $request->email,
                'password' => $request->password,
            ],
        ]);
    }
}
