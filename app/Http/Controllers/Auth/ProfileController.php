<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Actions\Fortify\UpdateUserPassword;

class ProfileController extends Controller
{
    private function getLoggedUser() {
        return User::where('id', Auth::user()->id)->with('roles')->first();
    }
    public function index(Request $request)
    {
        return $this->getLoggedUser();
    }

    public function abilities(Request $request) {
        $user = $this->getLoggedUser();
        /*return response()->json(['roles' => $user->roles(), 'permissions' => $user->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray()
            ]);*/
        return response()->json(['roles' => $user->getRoleNames(), 'permissions' => $user->getAllPermissions()->pluck('name')]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($request->user()->id)],
        ]);

        $request->user()->update($validated);

        return response()->json(['success' => true]);
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('profile_picture')) {
            $previousPath = $request->user()->getRawOriginal('avatar');

            $link = Storage::put('/' . config('app.photos_folder'), $request->file('profile_picture'));

            $request->user()->update(['avatar' => $link]);

            if ( ! is_null($previousPath) ) {
                Storage::delete($previousPath);
            }

            return response()->json(['message' => 'Profile picture uploaded successfully!']);
        }
    }

    public function changePassword(Request $request, UpdateUserPassword $updater)
    {
        $updater->update(
            auth()->user(),
            [
                'current_password' => $request->currentPassword,
                'password' => $request->password,
                'password_confirmation' => $request->passwordConfirmation,
            ]
        );

        return response()->json(['message' => 'Password changed successfully!']);
    }
}
