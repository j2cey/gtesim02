<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\LdapUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Auth\LdapUserResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LdapUser\StoreLdapUserRequest;
use App\Http\Requests\LdapUser\UpdateLdapUserRequest;

class LdapUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = LdapUser::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('login', 'like', "%{$searchQuery}%")
                    ->orWhere('email', 'like', "%{$searchQuery}%")
                    ->orWhere('telephone', 'like', "%{$searchQuery}%")
                    ->orWhere('distinguishedname', 'like', "%{$searchQuery}%")
                    ->orWhere('title', 'like', "%{$searchQuery}%")
                ;
            })
            ->latest()
            ->paginate(50);

        return LdapUserResource::collection( $users );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLdapUserRequest $request, User $user = null)
    {
        $ldapuser = LdapUser::import($request->email);
        if ( is_null($ldapuser) ) {
            throw ValidationException::withMessages([ 'email' => __('User NOT FOUND in LDAP !') ]);
        }
        if ( $user ) {
            $ldapuser->integrate($user);
        }

        return New LdapUserResource( $ldapuser );
    }

    /**
     * Display the specified resource.
     */
    public function show(LdapUser $ldapuser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LdapUser $ldapuser)
    {
        $ldapuser->formatTitle();
        $ldapuser->formatDepartment();

        return New LdapUserResource( $ldapuser );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLdapUserRequest $request, LdapUser $ldapuser)
    {
        $ldapuser->updateOne(
            $request->guid,
            $request->firstname,
            $request->lastname,
            $request->login,
            $request->email,
            $request->telephone,
            $request->domain,
            $request->distinguishedname,
            $request->department,
            $request->fonction
        );
        return New LdapUserResource( $ldapuser );
    }

    public function integrate(UpdateLdapUserRequest $request, LdapUser $ldapuser)
    {
        $ldapuser->updateOne(
            $request->guid,
            $request->firstname,
            $request->lastname,
            $request->login,
            $request->email,
            $request->telephone,
            $request->domain,
            $request->distinguishedname,
            $request->department,
            $request->fonction
        );
        return New UserResource( $ldapuser->integrate() );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LdapUser $ldapuser)
    {
        //
    }
}
