<?php

namespace App\Http\Controllers\ImportModels;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\ImportModels\ImportModelFieldString;
use App\Http\Requests\StoreImportModelFieldStringRequest;
use App\Http\Requests\UpdateImportModelFieldStringRequest;

class ImportModelFieldStringController extends Controller
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
     * @param StoreImportModelFieldStringRequest $request
     * @return Response
     */
    public function store(StoreImportModelFieldStringRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param ImportModelFieldString $importModelFieldString
     * @return void
     */
    public function show(ImportModelFieldString $importModelFieldString)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ImportModelFieldString $importModelFieldString
     * @return void
     */
    public function edit(ImportModelFieldString $importModelFieldString)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateImportModelFieldStringRequest $request
     * @param ImportModelFieldString $importModelFieldString
     * @return void
     */
    public function update(UpdateImportModelFieldStringRequest $request, ImportModelFieldString $importModelFieldString)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ImportModelFieldString $importModelFieldString
     * @return void
     */
    public function destroy(ImportModelFieldString $importModelFieldString)
    {
        //
    }
}
