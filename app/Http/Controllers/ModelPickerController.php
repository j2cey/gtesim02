<?php

namespace App\Http\Controllers;

use App\Models\Esims\Esim;
use App\Models\Esims\StatutEsim;
use App\Models\ModelPickers\ModelPicker;
use App\Http\Requests\StoreModelPickerRequest;
use App\Http\Requests\UpdateModelPickerRequest;

class ModelPickerController extends Controller
{

    public function test() {
        $statutesim_libre = StatutEsim::nouveau()->first();
        $model_picked = ModelPicker::pick(Esim::class, [['field'=>"statut_esim_id",'value'=>$statutesim_libre->id]]);
        $first_model_picker = ModelPicker::find(1);
        //$first_model_picker->setFree();
        dd($model_picked);
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
     * @param  \App\Http\Requests\StoreModelPickerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreModelPickerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ModelPicker  $modelPicker
     * @return \Illuminate\Http\Response
     */
    public function show(ModelPicker $modelPicker)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ModelPicker  $modelPicker
     * @return \Illuminate\Http\Response
     */
    public function edit(ModelPicker $modelPicker)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateModelPickerRequest  $request
     * @param  \App\Models\ModelPicker  $modelPicker
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateModelPickerRequest $request, ModelPicker $modelPicker)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ModelPicker  $modelPicker
     * @return \Illuminate\Http\Response
     */
    public function destroy(ModelPicker $modelPicker)
    {
        //
    }
}
