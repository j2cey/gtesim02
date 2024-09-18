<?php

namespace App\Http\Controllers\Ldap;

use App\Http\Controllers\Controller;
use App\Models\Ldap\LdapAccountImportResult;
use App\Http\Requests\LdapAccountImportResult\StoreLdapAccountImportResultRequest;
use App\Http\Requests\LdapAccountImportResult\UpdateLdapAccountImportResultRequest;

class LdapAccountImportResultController extends Controller
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
    public function store(StoreLdapAccountImportResultRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LdapAccountImportResult $ldapAccountImportResult)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LdapAccountImportResult $ldapAccountImportResult)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLdapAccountImportResultRequest $request, LdapAccountImportResult $ldapAccountImportResult)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LdapAccountImportResult $ldapAccountImportResult)
    {
        //
    }
}
