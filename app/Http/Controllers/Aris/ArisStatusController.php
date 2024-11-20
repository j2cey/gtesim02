<?php

namespace App\Http\Controllers\Aris;


use App\Models\Aris\ArisStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArisStatus\StoreArisStatusRequest;
use App\Http\Requests\ArisStatus\UpdateArisStatusRequest;

class ArisStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ArisStatus::all();
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
    public function store(StoreArisStatusRequest $request)
    {
        return ArisStatus::createNew($request->iccid, $request->icc, $request->status, $request->status_change_date, $request->requested_at, $request->responded_at, $request->response_message);
    }

    /**
     * Display the specified resource.
     */
    public function show(ArisStatus $arisstatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ArisStatus $arisstatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArisStatusRequest $request, ArisStatus $arisstatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArisStatus $arisstatus)
    {
        //
    }
}
