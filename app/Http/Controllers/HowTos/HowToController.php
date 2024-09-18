<?php

namespace App\Http\Controllers\HowTos;

use \Illuminate\View\View;
use App\Models\HowTos\HowTo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use App\Http\Resources\SearchCollection;
use App\Http\Requests\HowTo\FetchRequest;
use App\Http\Resources\HowTos\HowToResource;
use App\Http\Requests\HowTo\StoreHowToRequest;
use App\Http\Requests\HowTo\UpdateHowToRequest;
use Illuminate\Contracts\Foundation\Application;
use App\Repositories\Contracts\IHowToRepositoryContract;

class HowToController extends Controller
{
    /**
     * @var IHowToRepositoryContract
     */
    private $repository;

    /**
     * ClientEsimController constructor.
     *
     * @param IHowToRepositoryContract $repository [description]
     */
    public function __construct(IHowToRepositoryContract $repository) {
        $this->repository = $repository;
    }

    /**
     * Fetch records.
     *
     * @param  FetchRequest     $request [description]
     * @return SearchCollection          [description]
     */
    public function fetch(FetchRequest $request): SearchCollection
    {
        return new SearchCollection(
            $this->repository->search($request), HowToResource::class
        );
    }

    public function fetchall() {
        return HowTo::all();
    }

    public function edithtml($id) {
        $howto = HowTo::where('id', $id)->first();
        return view('howtos.edithtml')
            ->with('howto', $howto);
    }

    public function storehtml(Request $request) {
        $body = $request->body;
        $images = $request->images;
        $howto_id = $request->howto;

        // get and save HowTo
        $howto = HowTo::where('id', $howto_id)->first();
        $howto->update([
            'htmlbody' => $body
        ]);

        // If images not empty
        if ($images) {
            foreach ($images as $image)
            {
                // Create a new image from base64 string and attach it to article in article-images collection
                $howto->addMediaFromBase64($image)->toMediaCollection('howto-images');

                // Get all images as we will need the last one uploaded
                $mediaItems = $howto->load('media')->getMedia('howto-images');

                // Replace the base64 string in article body with the url of the last uploaded image
                $howto->htmlbody = str_replace($image, $mediaItems[count($mediaItems) - 1]->getFullUrl(), $howto->htmlbody);
            }
        }

        $howto->save();
        $this->removeImagesNotPresent($howto, $howto->htmlbody);

        return response()->json('Success');
    }

    private function removeImagesNotPresent($howto, $htmlbody): void
    {
        $mediaItems = $howto->load('media')->getMedia('howto-images');
        foreach ($mediaItems as $mediaItem)
        {
            if ( ! str_contains($htmlbody, $mediaItem->getFullUrl()) ) {
                $mediaItem->delete();
            }
        }
    }

    public function readhtml($id) {
        $howto = HowTo::where('id', $id)->first();
        return view('howtos.readhtml')
            ->with('howto', $howto);
    }

    /**
     * Display products page.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function index()
    {
        return view('howtos.index')
            ->with('perPage', new Collection(config('system.per_page')))
            ->with('defaultPerPage', config('system.default_per_page'));
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
     * @return void
     */
    public function edit(HowTo $howto)
    {
        //
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
            $request->tags
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
