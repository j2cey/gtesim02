<?php

namespace App\Http\Controllers\Esims;

use App\Models\Esims\Esim;
use Illuminate\Http\Response;
use App\Models\Esims\EsimState;
use App\Http\Controllers\Controller;
use App\Http\Requests\EsimState\StoreEsimStateRequest;
use App\Http\Requests\EsimState\UpdateEsimStateRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EsimStateController extends Controller
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
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator
     */
    public function esimindex(Esim $esim)
    {
        $esimstates = EsimState::query()->where('esim_id', $esim->id)
            ->where(function ($query) {
                $query->when(request('query'), function ($query, $searchQuery) {
                    $query->orWhere('details', 'like', "%{$searchQuery}%")
                        ->orWhereHas('statutesim', function ($query) use ($searchQuery) {
                            $query->where( 'libelle', 'like', '%'.$searchQuery.'%' );
                        })
                        ->orWhereHas('user', function ($query) use ($searchQuery) {
                            $query->where( 'name', 'like', '%'.$searchQuery.'%' );
                        })
                    ;
                });
            })
            ->latest()
            ->paginate(5);

        return $esimstates;
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
     * @param StoreEsimStateRequest $request
     * @return Response
     */
    public function store(StoreEsimStateRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param EsimState $esimState
     * @return Response
     */
    public function show(EsimState $esimstate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param EsimState $esimState
     * @return Response
     */
    public function edit(EsimState $esimstate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateEsimStateRequest $request
     * @param EsimState $esimState
     * @return Response
     */
    public function update(UpdateEsimStateRequest $request, EsimState $esimstate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param EsimState $esimstate
     * @return Response
     */
    public function destroy(EsimState $esimstate)
    {
        //
    }
}
