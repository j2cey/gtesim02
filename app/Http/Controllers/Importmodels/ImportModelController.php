<?php

namespace App\Http\Controllers\ImportModels;

use App\Http\Controllers\Controller;
use App\Models\ImportModels\ImportModel;
use App\Http\Requests\StoreImportModelRequest;
use App\Http\Requests\UpdateImportModelRequest;

class ImportModelController extends Controller
{
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
     * @param StoreImportModelRequest $request
     * @return void
     */
    public function store(StoreImportModelRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param ImportModel $importModel
     * @return void
     */
    public function show(ImportModel $importModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ImportModel $importModel
     * @return void
     */
    public function edit(ImportModel $importModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateImportModelRequest $request
     * @param ImportModel $importModel
     * @return void
     */
    public function update(UpdateImportModelRequest $request, ImportModel $importModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ImportModel $importModel
     * @return void
     */
    public function destroy(ImportModel $importModel)
    {
        //
    }
}
