<?php

namespace App\Http\Controllers\HowTos;

use App\Models\HowTos\HowTo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HowTos\HowToResource;
use App\Http\Requests\HowTo\StoreHowToRequest;
use App\Http\Requests\HowTo\UpdateHowToRequest;
use App\Http\Requests\HowTo\StoreHtmlHowToRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HowToController extends Controller
{
    public function fetchall() {
        return HowTo::all();
    }

    public function edithtml($id) {
        $howto = HowTo::where('id', $id)->first();
        return view('howtos.edithtml')
            ->with('howto', $howto);
    }

    public function storehtml(StoreHtmlHowToRequest $request, HowTo $howto) {

        $howto->saveHtmlBody($request->htmlbody, $request->images);

        return response()->json('Success');
    }

    public function readhtml($id) {
        $howto = HowTo::where('id', $id)->first();
        return view('howtos.readhtml')
            ->with('howto', $howto);
    }

    /**
     * Display products page.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $howtos = HowTo::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('title', 'like', "%{$searchQuery}%")
                    ->orWhere('code', 'like', "%{$searchQuery}%")
                    ->orWhere('view', 'like', "%{$searchQuery}%")
                    ->orWhere('description', 'like', "%{$searchQuery}%")
                ;
            })
            ->with(["status", "creator"])
            ->latest()
            ->paginate(50);

        return HowToResource::collection( $howtos );
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
     * @param StoreHowToRequest $request
     * @return HowToResource
     */
    public function store(StoreHowToRequest $request): HowToResource
    {
        $howto = HowTo::createNew(
            $request->howtotype,
            $request->title,
            null,//$request->view,
            $request->description,
            $request->code,
            $request->tags
        );
        return new HowToResource($howto);
    }

    /**
     * Display the specified resource.
     *
     * @param HowTo $howto
     * @return void
     */
    public function show(HowTo $howto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param HowTo $howto
     * @return HowToResource
     */
    public function edit(HowTo $howto)
    {
        return New HowToResource( $howto->load(['howtotype', 'status', 'tags']) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateHowToRequest $request
     * @param HowTo $howto
     * @return HowToResource
     */
    public function update(UpdateHowToRequest $request, HowTo $howto)
    {
        $howto->updateOne(
            $request->howtotype,
            $request->title,
            null,//$request->view,
            $request->description,
            $request->code,
            //$request->tags
        );
        return new HowToResource($howto);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param HowTo $howto
     * @return void
     */
    public function destroy(HowTo $howto)
    {
        //
    }
}
