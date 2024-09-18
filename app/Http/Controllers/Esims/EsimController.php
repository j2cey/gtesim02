<?php

namespace App\Http\Controllers\Esims;

use Exception;
use \Illuminate\View\View;
use App\Models\Esims\Esim;

use Illuminate\Http\Request;
use App\Models\Esims\StatutEsim;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use App\Http\Requests\Esim\FetchRequest;
use App\Http\Requests\Esim\StoreEsimRequest;

use App\Models\Esims\EsimHeadFile;
use App\Models\Esims\EsimBodyFile;
use App\Http\Resources\Esims\EsimResource;
use App\Http\Requests\Esim\UpdateEsimRequest;
use Illuminate\Contracts\Foundation\Application;
use App\Http\Requests\ISearchFormRequest;




class EsimController extends Controller
{
    /**
     * @var IEsimRepositoryContract
     */
    private IEsimRepositoryContract $repository;

    /**
     * EsimController constructor.
     *
     * @param IEsimRepositoryContract $repository [description]
     */
    public function __construct(IEsimRepositoryContract $repository) {
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
            $this->repository->search($request), EsimResource::class
        );
    }

    public function fetchall() {
        return Esim::all();
    }

    public function fetchone($id) {
        $esim = Esim::find($id);
        $esim->load(['statutesim','technologieesim','phonenum','phonenum.hasphonenum']);

        return $esim;
    }

    /**
     * Display products page.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function index()
    {
        $statutesims = StatutEsim::all();
        return view('esims.index')
            ->with('perPage', new Collection(config('system.per_page')))
            ->with('defaultPerPage', config('system.default_per_page'))
            ->with('statutesims', $statutesims);
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
     * @param StoreEsimRequest $request
     * @return EsimResource
     */
    public function store(StoreEsimRequest $request): EsimResource
    {
        $esim = Esim::createNew(
            $request->imsi,
            $request->iccid,
            $request->ac,
            $request->pin,
            $request->puk
        );
        return new EsimResource($esim);
    }

    /**
     * Display the specified resource.
     *
     * @param Esim $esim
     * @return Application|Factory|\Illuminate\Contracts\View\View|void
     */
    public function show(Esim $esim)
    {
        $esim->load(['statutesim','technologieesim','phonenum','phonenum.hasphonenum']);
        return view('esims.show')
            ->with('esim', new EsimResource($esim));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Esim $esim
     * @return void
     */
    public function edit(Esim $esim): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateEsimRequest $request
     * @param Esim $esim
     * @return void
     */
    public function update(UpdateEsimRequest $request, Esim $esim): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Esim $esim
     * @return void
     */
    public function destroy(Esim $esim): void
    {
        //
    }

    public function headfiles()
    {
        return view('esims.headfiles');
    }

    public function headfilespost(Request $request): void
    {
        $formInput = $request->all();

        $new_esimheadfile = EsimHeadFile::create();

        $new_esimheadfile->verifyAndStoreFile( $request, "esim_fichier", "fichier", "esim_fichier_entete");
    }

    public function bodyfiles()
    {
        return view('esims.bodyfiles');
    }

    public function bodyfilespost(Request $request)
    {
        $formInput = $request->all();

        $new_esimbodyfile = EsimBodyFile::create();

        $new_esimbodyfile->verifyAndStoreFile( $request, "esim_fichier", "fichier", "esim_fichier_corps");
    }
}
