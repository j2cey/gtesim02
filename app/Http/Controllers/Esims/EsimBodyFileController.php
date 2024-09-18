<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEsimBodyFileRequest;
use App\Http\Requests\UpdateEsimBodyFileRequest;
use App\Models\EsimBodyFile;

class EsimBodyFileController extends Controller
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
     * @param  \App\Http\Requests\StoreEsimBodyFileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEsimBodyFileRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EsimBodyFile  $esimBodyFile
     * @return \Illuminate\Http\Response
     */
    public function show(EsimBodyFile $esimBodyFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EsimBodyFile  $esimBodyFile
     * @return \Illuminate\Http\Response
     */
    public function edit(EsimBodyFile $esimBodyFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEsimBodyFileRequest  $request
     * @param  \App\Models\EsimBodyFile  $esimBodyFile
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEsimBodyFileRequest $request, EsimBodyFile $esimBodyFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EsimBodyFile  $esimBodyFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(EsimBodyFile $esimBodyFile)
    {
        //
    }
}
