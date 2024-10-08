<?php

namespace App\Http\Controllers\HowTos;

use App\Models\HowTos\HowToType;
use App\Http\Controllers\Controller;
use App\Http\Requests\HowToType\StoreHowToTypeRequest;
use App\Http\Requests\HowToType\UpdateHowToTypeRequest;

class HowToTypeController extends Controller
{
    public function fetchall() {
        return HowToType::all();
    }
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreHowToTypeRequest $request
     * @return void
     */
    public function store(StoreHowToTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param HowToType $howtotype
     * @return void
     */
    public function show(HowToType $howtotype)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param HowToType $howtotype
     * @return void
     */
    public function edit(HowToType $howtotype)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateHowToTypeRequest $request
     * @param HowToType $howtotype
     * @return void
     */
    public function update(UpdateHowToTypeRequest $request, HowToType $howtotype)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param HowToType $howtotype
     * @return void
     */
    public function destroy(HowToType $howtotype)
    {
        //
    }
}
