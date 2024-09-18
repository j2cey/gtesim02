<?php

namespace App\Http\Controllers\Employes;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Employes\FonctionEmploye;
use App\Http\Requests\FonctionEmploye\StoreFonctionEmployeRequest;
use App\Http\Requests\FonctionEmploye\UpdateFonctionEmployeRequest;


class FonctionEmployeController extends Controller
{

    public function fetchall() {
        return FonctionEmploye::all();
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
     * @param StoreFonctionEmployeRequest $request
     * @return void
     */
    public function store(StoreFonctionEmployeRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param FonctionEmploye $fonctionemploye
     * @return void
     */
    public function show(FonctionEmploye $fonctionemploye): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param FonctionEmploye $fonctionemploye
     * @return void
     */
    public function edit(FonctionEmploye $fonctionemploye): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateFonctionEmployeRequest $request
     * @param FonctionEmploye $fonctionemploye
     * @return void
     */
    public function update(UpdateFonctionEmployeRequest $request, FonctionEmploye $fonctionemploye): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FonctionEmploye $fonctionemploye
     * @return void
     */
    public function destroy(FonctionEmploye $fonctionemploye): void
    {
        //
    }
}
