<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEsimStateRequest;
use App\Http\Requests\UpdateEsimStateRequest;
use App\Models\EsimState;

class EsimStateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreEsimStateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEsimStateRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EsimState  $esimState
     * @return \Illuminate\Http\Response
     */
    public function show(EsimState $esimState)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EsimState  $esimState
     * @return \Illuminate\Http\Response
     */
    public function edit(EsimState $esimState)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEsimStateRequest  $request
     * @param  \App\Models\EsimState  $esimState
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEsimStateRequest $request, EsimState $esimState)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EsimState  $esimState
     * @return \Illuminate\Http\Response
     */
    public function destroy(EsimState $esimState)
    {
        //
    }
}
