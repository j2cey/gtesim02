<?php

namespace App\Http\Controllers\Esims;

use Illuminate\Http\Request;
use App\Models\Esims\StatutEsim;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatutEsim\StoreStatutEsimRequest;
use App\Http\Requests\StatutEsim\UpdateStatutEsimRequest;
use App\Http\Requests\StatutEsim\ModelUpdateStatutEsimRequest;

class StatutEsimController extends Controller
{
    public function fetchall()
    {
        return StatutEsim::get();
    }

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
     * @param StoreStatutEsimRequest $request
     * @return void
     */
    public function store(StoreStatutEsimRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param StatutEsim $statutEsim
     * @return void
     */
    public function show(StatutEsim $statutEsim)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StatutEsim $statutEsim
     * @return void
     */
    public function edit(StatutEsim $statutEsim)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStatutEsimRequest $request
     * @param StatutEsim $statutEsim
     * @return void
     */
    public function update(UpdateStatutEsimRequest $request, StatutEsim $statutEsim)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param StatutEsim $statutEsim
     * @return void
     */
    public function destroy(StatutEsim $statutEsim)
    {
        //
    }

    public function setnext(Request $request) {

    }

    public function modelupdate(ModelUpdateStatutEsimRequest $request)
    {
        $request->model->setStatutEsim($request->status);
        $request->model->load(['statutesim']);

        return $request->model->statutesim;
    }
}
