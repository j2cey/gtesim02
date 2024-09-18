<?php

namespace App\Http\Controllers\Employes;

use App\Models\Employes\Employe;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employe\StoreEmployeRequest;
use App\Http\Requests\Employe\UpdateEmployeRequest;

class EmployeController extends Controller
{

    public function fetchall() {
        return Employe::all();
    }

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
     * @param StoreEmployeRequest $request
     * @return void
     */
    public function store(StoreEmployeRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Employe $employe
     * @return void
     */
    public function show(Employe $employe): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Employe $employe
     * @return void
     */
    public function edit(Employe $employe): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateEmployeRequest $request
     * @param Employe $employe
     * @return void
     */
    public function update(UpdateEmployeRequest $request, Employe $employe): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Employe $employe
     * @return void
     */
    public function destroy(Employe $employe): void
    {
        //
    }
}
