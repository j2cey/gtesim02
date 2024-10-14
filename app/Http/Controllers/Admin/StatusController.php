<?php

namespace App\Http\Controllers\Admin;

use App\Models\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Status\StoreStatusRequest;
use App\Http\Requests\Status\UpdateStatusRequest;
use App\Http\Requests\Status\StatusChangeRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return LengthAwarePaginator
     */
    public function index()
    {
        $statuses = Status::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('code', 'like', "%{$searchQuery}%")
                    ->orWhere('name', 'like', "%{$searchQuery}%")
                    ->orWhere('style', 'like', "%{$searchQuery}%")
                    ->orWhere('description', 'like', "%{$searchQuery}%")
                ;
            })
            ->latest()
            ->paginate(10);

        return $statuses;

    }

    public function fetchCodes() {
        return Status::all()->pluck('code');
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
     * @param StoreStatusRequest $request
     * @return Status
     */
    public function store(StoreStatusRequest $request)
    {
        return Status::createNew($request->code,$request->name,$request->style, $request->description);
    }

    /**
     * Display the specified resource.
     * @param Status $status
     * @return Status
     */
    public function show(Status $status)
    {
        return $status;
    }

    /**
     * Show the form for editing the specified resource.
     * @param Status $status
     * @return Status
     */
    public function edit(Status $status)
    {
        return $status;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStatusRequest $request, Status $status)
    {
        return $status->updateOne($request->code,$request->name,$request->style, $request->description);
    }

    public function statuschange(StatusChangeRequest $request) {
        $request->model->setStatus( $request->status, true );

        return $request->status;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        $status->delete();
        return response()->json(['status' => 'ok'], 200);
    }
}
