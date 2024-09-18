<?php

namespace App\Http\Controllers\Employes;

use App\Http\Controllers\Controller;
use App\Models\Employes\EmailAddress;
use App\Http\Requests\StoreEmailAddressRequest;
use App\Http\Requests\UpdateEmailAddressRequest;

class EmailAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(): void
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreEmailAddressRequest $request
     * @return void
     */
    public function store(StoreEmailAddressRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param EmailAddress $emailaddress
     * @return void
     */
    public function show(EmailAddress $emailaddress): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param EmailAddress $emailaddress
     * @return void
     */
    public function edit(EmailAddress $emailaddress): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateEmailAddressRequest $request
     * @param EmailAddress $emailaddress
     * @return void
     */
    public function update(UpdateEmailAddressRequest $request, EmailAddress $emailaddress): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param EmailAddress $emailaddress
     * @return void
     */
    public function destroy(EmailAddress $emailaddress): void
    {
        //
    }
}
