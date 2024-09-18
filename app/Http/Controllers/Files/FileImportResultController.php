<?php

namespace App\Http\Controllers\Files;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileImportResult\StoreFileImportResultRequest;
use App\Http\Requests\FileImportResult\UpdateFileImportResultRequest;
use App\Models\Files\FileImportResult;

class FileImportResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreFileImportResultRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(FileImportResult $fileImportResult)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FileImportResult $fileImportResult)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFileImportResultRequest $request, FileImportResult $fileImportResult)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FileImportResult $fileImportResult)
    {
        //
    }
}
