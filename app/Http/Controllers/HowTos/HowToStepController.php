<?php

namespace App\Http\Controllers\HowTos;

use App\Models\HowTos\HowToStep;
use Illuminate\Http\JsonResponse;
use App\Models\HowTos\HowToThread;
use App\Http\Controllers\Controller;
use App\Http\Resources\HowTos\HowToStepResource;
use App\Http\Requests\HowToStep\StoreHowToStepRequest;
use App\Http\Requests\HowToStep\UpdateHowToStepRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HowToStepController extends Controller
{
    public function read($id) {
        $howtostep = HowToStep::where('id', $id)->first();
        $howtostep->load(['howtothread','howto']);

        $howtostepprev = $howtostep->prevStep();
        $howtostepnext = $howtostep->nextStep();

        $howtostepprev = is_null($howtostepprev) ? $howtostepprev : new HowToStepResource($howtostepprev);
        $howtostepnext = is_null($howtostepnext) ? $howtostepnext : new HowToStepResource($howtostepnext);

        return view('howtosteps.read')
            ->with('howtostep', $howtostep)
            ->with('howtostepprev', $howtostepprev)
            ->with('howtostepnext', $howtostepnext);
    }

    public function relativesteps($id) {
        $howtostep = HowToStep::where('id', $id)->first();

        $posisteps = $howtostep->getRelativeSteps();

        $posisteps['current'] = new HowToStepResource($posisteps['current']);
        $posisteps['prev'] = is_null($posisteps['prev']) ? $posisteps['prev'] : new HowToStepResource($posisteps['prev']);
        $posisteps['next'] = is_null($posisteps['next']) ? $posisteps['next'] : new HowToStepResource($posisteps['next']);

        return $posisteps;
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $howtosteps = HowToStep::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('title', 'like', "%{$searchQuery}%")
                    ->orWhere('description', 'like', "%{$searchQuery}%")
                ;
            })
            ->with(["status", "creator"])
            ->latest()
            ->paginate(10);

        return HowToStepResource::collection( $howtosteps );
    }

    public function threadsteps(HowToThread $howtothread)
    {
        $howtosteps = HowToStep::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('title', 'like', "%{$searchQuery}%")
                    ->orWhere('code', 'like', "%{$searchQuery}%")
                    ->orWhere('description', 'like', "%{$searchQuery}%")
                ;
            })
            ->whereHas('howtothread', function ($query) use ($howtothread) {
                $query->where( 'id', $howtothread->id );
            })
            ->with(["status", "creator"])
            ->orderBy('posi')
            ->paginate(10);

        return HowToStepResource::collection( $howtosteps );
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
     * @param StoreHowToStepRequest $request
     * @return HowToStepResource
     */
    public function store(StoreHowToStepRequest $request)
    {
        return new HowToStepResource( $request->howtothread->addNewStep($request->howto, $request->posi, $request->title, $request->description) );
    }

    /**
     * Display the specified resource.
     *
     * @param HowToStep $howtostep
     * @return void
     */
    public function show(HowToStep $howtostep)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param HowToStep $howtostep
     * @return HowToStepResource
     */
    public function edit(HowToStep $howtostep)
    {
        return new HowToStepResource( $howtostep->load(['howtothread','howto', 'status', 'tags']) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateHowToStepRequest $request
     * @param HowToStep $howtostep
     * @return HowToStepResource
     */
    public function update(UpdateHowToStepRequest $request, HowToStep $howtostep)
    {
        //dd($request);
        return new HowToStepResource( $howtostep->updateOne($request->howto, $request->title, $request->posi, $request->description) );
        //updateOne(HowTo $howto, $title, $posi, $description, $tags = null) : HowToStep
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param HowToStep $howtostep
     * @return JsonResponse|void
     */
    public function destroy(HowToStep $howtostep)
    {
        $howtostep->load('howtothread');
        $howtostep->howtothread->shiftStepsUpTo($howtostep->posi);
        $howtostep->delete();

        return response()->json(['status' => 'ok'], 200);
    }
}
