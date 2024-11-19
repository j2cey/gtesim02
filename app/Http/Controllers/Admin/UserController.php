<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\UserResource;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\User\ResetPasswordRequest;
use Illuminate\Routing\Controllers\HasMiddleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:users-list', only: ['index']),
            new Middleware('permission:users-create', only: ['store']),
            new Middleware('permission:users-update', only: ['update','changeRole']),
            new Middleware('permission:users-destory', only: ['destory','bulkDelete']),
        ];
    }

    public function index()
    {
        $users = User::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%");
            })
            ->with('roles')
            ->with(['ldapAccount','employe'])
            ->skip(10)->take(300)
            ->latest()
            ->paginate(50);

        return UserResource::collection( $users );
    }

    public function store(StoreUserRequest $request) {

        $user = User::createNew($request->name,$request->email,$request->login,$request->is_local,$request->is_ldap);

        $user->localPwdSet(request('password'));

        return New UserResource( $user );
    }

    public function edit(User $user)
    {
        return New UserResource( $user->load(['roles','ldapAccount','employe']) );
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'login' => $request->login,
            'is_local' => $request->is_local,
            'is_ldap' => $request->is_ldap,
        ]);

        $user->load(['roles','status']);

        return new UserResource($user);
    }
    public function resetpwd(ResetPasswordRequest $request, User $user)
    {
        $user->setPassword($request->newpwd);

        return $user;
    }

    public function changeRole(User $user)
    {
        $user->update([
            'role' => request('role'),
        ]);

        return response()->json(['success' => true]);
    }

    public function assignRoles(User $user)
    {
        $roles = Role::whereIn('id', request('rolesids'))
            ->whereNotIn('id', $user->roles()->pluck('id'))
            ->get();
        $nb = count($roles);
        $user->assignRole($roles->pluck('name'));

        foreach ($roles as $role) {
            $role->is_in_role = true;
        }

        return response()->json(['success' => true, 'roles' => $roles,'message' => $nb === 0 ? 'NO role to assign' : $nb . ' Role(s) assign to User successfully!']);
    }

    public function revokeRoles(User $user)
    {
        $roles = Role::whereIn('id', request('rolesids'))
            ->whereIn('id', $user->roles()->pluck('id'))
            ->get();
        $nb = 0;
        foreach ($roles as $role) {
            $user->removeRole($role->name);
            $nb++;
        }

        return response()->json(['success' => true, 'roles' => $roles,'message' => $nb === 0 ? 'NO role to revoke' : $nb . ' Role(s) remove from User successfully!']);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }

    public function bulkDelete()
    {
        User::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Users deleted successfully!']);
    }
}
