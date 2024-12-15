<?php

namespace App\Http\Controllers\Aris;


use App\Models\Esims\Esim;
use App\Models\Aris\ArisStatus;
use App\Traits\Aris\ArisStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\Aris\ArisStatusResource;
use App\Http\Requests\ArisStatus\StoreArisStatusRequest;
use App\Http\Requests\ArisStatus\UpdateArisStatusRequest;

class ArisStatusController extends Controller
{
    use ArisStatusCode;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $arisstatuses = ArisStatus::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $arisstatuscode = self::unFormatStatus($searchQuery);
                $query->where('icc', 'like', "%{$searchQuery}%")
                    ->orWhere('status', 'like', "%{$arisstatuscode}%")
                    ->orWhere('response_message', 'like', "%{$searchQuery}%")
                ;
            })
            ->latest()
            ->paginate(50);

        return ArisStatusResource::collection( $arisstatuses );
    }

    public function esimindex(Esim $esim)
    {
        $arisstatuses = ArisStatus::query()
            ->where(function ($query) use ($esim) {
                $query->whereHas('esim', function ($query) use ($esim) {
                    $query->where( 'id', $esim->id);
                });
            })
            ->when(request('query'), function ($query, $searchQuery) {
                $arisstatuscode = self::unFormatStatus($searchQuery);
                $query->where('icc', 'like', "%{$searchQuery}%")
                    ->orWhere('status', 'like', "%{$arisstatuscode}%")
                    ->orWhere('response_message', 'like', "%{$searchQuery}%")
                ;
            })
            ->latest()
            ->paginate(50);

        return ArisStatusResource::collection( $arisstatuses );
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
        return ArisStatus::createNew($request->iccid, $request->icc, $request->status, $request->status_change_date, $request->requested_at, $request->responded_at, $request->response_message, $request->request_id);
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
