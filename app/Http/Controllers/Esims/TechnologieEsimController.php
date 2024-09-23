<?php

namespace App\Http\Controllers\Esims;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Esims\TechnologieEsim;
use App\Http\Requests\TechnologieEsim\StoreTechnologieEsimRequest;
use App\Http\Requests\TechnologieEsim\UpdateTechnologieEsimRequest;


class TechnologieEsimController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TechnologieEsim::active()->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTechnologieEsimRequest $request
     * @return Response
     */
    public function store(StoreTechnologieEsimRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param TechnologieEsim $technologieEsim
     * @return Response
     */
    public function show(TechnologieEsim $technologieEsim)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TechnologieEsim $technologieEsim
     * @return Response
     */
    public function edit(TechnologieEsim $technologieEsim)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTechnologieEsimRequest $request
     * @param TechnologieEsim $technologieEsim
     * @return Response
     */
    public function update(UpdateTechnologieEsimRequest $request, TechnologieEsim $technologieEsim)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TechnologieEsim $technologieEsim
     * @return Response
     */
    public function destroy(TechnologieEsim $technologieEsim)
    {
        //
    }
}
