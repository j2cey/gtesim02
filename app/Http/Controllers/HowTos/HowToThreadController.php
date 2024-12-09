<?php

namespace App\Http\Controllers\HowTos;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\HowTos\HowToThread;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use App\Http\Resources\HowTos\HowToStepResource;
use Illuminate\Contracts\Foundation\Application;
use App\Http\Resources\HowTos\HowToThreadResource;
use App\Http\Requests\HowToStep\StoreHowToStepRequest;
use App\Http\Requests\HowToThread\StoreHowToThreadRequest;
use App\Http\Requests\HowToThread\UpdateHowToThreadRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HowToThreadController extends Controller
{
    public function fetchall() {
        return HowToThread::all();
    }

    public function read($id) {
        $howtothread = new HowToThreadResource( HowToThread::where('id', $id)->first() );

        /*
        $posisteps = $howtothread->getPosiSteps(1);

        $posisteps['current'] = new HowToStepResource($posisteps['current']);
        $posisteps['prev'] = is_null($posisteps['prev']) ? $posisteps['prev'] : new HowToStepResource($posisteps['prev']);
        $posisteps['next'] = is_null($posisteps['next']) ? $posisteps['next'] : new HowToStepResource($posisteps['next']);
        */
        return view('howtothreads.read')
            ->with('howtothread', $howtothread);
    }

    public function posimax($id) {
        $howtothread = HowToThread::where('id', $id)->first()->load('steps');
        return $howtothread->steps()->count();
    }

    public function storestep(StoreHowToStepRequest $request) {
        return $request->howtothread->addStep($request->howto, $request->posi, $request->step_title = null, $request->description = null);
    }

    public function fetchone($id) {
        return new HowToThreadResource(HowToThread::find($id));
    }

    /**
     * Display products page.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $howtothreads = HowToThread::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('title', 'like', "%{$searchQuery}%")
                    ->orWhere('code', 'like', "%{$searchQuery}%")
                    ->orWhere('description', 'like', "%{$searchQuery}%")
                ;
            })
            ->with(["status", "creator", "tags"])
            ->latest()
            ->paginate(10);

        return HowToThreadResource::collection( $howtothreads );
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
     * @param StoreHowToThreadRequest $request
     * @return HowToThreadResource
     */
    public function store(StoreHowToThreadRequest $request)
    {
        $howtothread = HowToThread::createNew(
            $request->title,
            $request->description,
            $request->code,
            $request->tags
        );

        $howtothread->saveImage($request->image);

        return new HowToThreadResource($howtothread);
    }

    /**
     * Display the specified resource.
     *
     * @param HowToThread $howtothread
     * @return Application|Factory|\Illuminate\Contracts\View\View|void
     */
    public function show(HowToThread $howtothread)
    {
        return view('howtothreads.show')
            ->with( 'howtothread', new HowToThreadResource($howtothread) );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param HowToThread $howtothread
     * @return HowToThreadResource
     */
    public function edit(HowToThread $howtothread)
    {
        return New HowToThreadResource( $howtothread->load(['steps', 'status', 'tags']) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateHowToThreadRequest $request
     * @param HowToThread $howtothread
     * @return HowToThreadResource
     */
    public function update(UpdateHowToThreadRequest $request, HowToThread $howtothread)
    {
        $howtothread->updateOne(
            $request->title,
            $request->description,
            $request->code,
            $request->tags
        );

        $howtothread->saveImage($request->image);

        return new HowToThreadResource($howtothread);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param HowToThread $howtothread
     * @return void
     */
    public function destroy(HowToThread $howtothread)
    {
        //
    }
}
