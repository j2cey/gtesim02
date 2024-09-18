<?php

namespace App\Http\Controllers\ImportModels;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImportModelFieldTypeRequest;
use App\Http\Requests\UpdateImportModelFieldTypeRequest;
use App\Models\ImportModels\ImportModelFieldType;

class ImportModelFieldTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreImportModelFieldTypeRequest $request
     * @return Response
     */
    public function store(StoreImportModelFieldTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param ImportModelFieldType $importModelFieldType
     * @return void
     */
    public function show(ImportModelFieldType $importModelFieldType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ImportModelFieldType $importModelFieldType
     * @return void
     */
    public function edit(ImportModelFieldType $importModelFieldType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateImportModelFieldTypeRequest $request
     * @param ImportModelFieldType $importModelFieldType
     * @return void
     */
    public function update(UpdateImportModelFieldTypeRequest $request, ImportModelFieldType $importModelFieldType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ImportModelFieldType $importModelFieldType
     * @return void
     */
    public function destroy(ImportModelFieldType $importModelFieldType)
    {
        //
    }
}
