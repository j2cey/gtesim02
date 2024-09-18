<?php

namespace App\Http\Controllers;

use App\Models\LdapAccount;
use App\Http\Requests\LdapAccount\StoreLdapAccountRequest;
use App\Http\Requests\LdapAccount\UpdateLdapAccountRequest;

class LdapAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreLdapAccountRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LdapAccount $ldapAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LdapAccount $ldapAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLdapAccountRequest $request, LdapAccount $ldapAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LdapAccount $ldapAccount)
    {
        //
    }
}
