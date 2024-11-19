<?php

namespace App\Http\Controllers\Esims;

use Exception;
use App\Models\User;
use Illuminate\View\View;
use App\Models\Esims\Esim;

use Illuminate\Http\Request;
use App\Models\Esims\StatutEsim;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\Esims\TechnologieEsim;
use Illuminate\Contracts\View\Factory;
use App\Http\Requests\Esim\FetchRequest;
use App\Http\Requests\Esim\StoreEsimRequest;

use App\Models\Esims\EsimHeadFile;
use App\Models\Esims\EsimBodyFile;
use App\Http\Resources\Esims\EsimResource;
use App\Http\Requests\Esim\UpdateEsimRequest;
use Symfony\Component\HttpKernel\HttpCache\Esi;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EsimController extends Controller
{
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
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $esims = Esim::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('imsi', 'like', "%{$searchQuery}%")
                    ->orWhere('iccid', 'like', "%{$searchQuery}%")
                    ->orWhereHas('statutesim', function ($query) use ($searchQuery) {
                        $query->where( 'code', 'like', '%'.$searchQuery.'%' );
                    })
                ;
            })
            ->with("statutesim")
            ->latest()
            ->paginate(50);

        return EsimResource::collection( $esims );
    }

    public function esimsattributed(User $user)
    {
        $esims = Esim::query()->where('attributed_by', $user->id)
            ->where(function ($query) {
                $query->when(request('query'), function ($query, $searchQuery) {
                    $query->where('imsi', 'like', "%{$searchQuery}%")
                        ->orWhere('iccid', 'like', "%{$searchQuery}%")
                        ->orWhereHas('statutesim', function ($query) use ($searchQuery) {
                            $query->where( 'code', 'like', '%'.$searchQuery.'%' );
                        })
                    ;
                });
            })
            ->with("statutesim")
            ->latest()
            ->paginate(50);

        return EsimResource::collection( $esims );
        //return ['user' => $user,'esims' => EsimResource::collection( $esims )];
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
     * @param StoreEsimRequest $request
     * @return EsimResource
     */
    public function store(StoreEsimRequest $request)
    {
        $esim = Esim::createNew(
            $request->imsi,
            $request->iccid,
            $request->ac,
            $request->pin,
            $request->puk,
            $request->eki,
            $request->pin2,
            $request->puk2,
            $request->adm1,
            $request->opc,
            $request->statutesim ? StatutEsim::find($request->statutesim['id']) : null,
            $request->technologieesim ? TechnologieEsim::find($request->technologieesim['id']) : null
        );

        return New EsimResource( $esim );
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
     * @return EsimResource
     */
    public function edit(Esim $esim) {
        return New EsimResource( $esim->load(['statutesim', 'technologieesim', 'phonenum', 'states']) );
    }

    public function pickup($esimid = null) {
        $esim = Esim::getByUuid($esimid);
        $new_esim = Esim::pickupFirstFree($esim);

        return New EsimResource( $new_esim->load(['statutesim', 'technologieesim', 'phonenum', 'states', 'lateststate']) );
    }

    public function release(Esim $esim) {
        $esim->setStatutFree();
        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateEsimRequest $request
     * @param Esim $esim
     * @return Esim
     */
    public function update(UpdateEsimRequest $request, Esim $esim): Esim
    {
        $esim->updateOne(
            $request->imsi,
            $request->iccid,
            $request->ac,
            $request->pin,
            $request->puk,
            $request->eki,
            $request->pin2,
            $request->puk2,
            $request->adm1,
            $request->opc,
            $request->statutesim ? StatutEsim::find($request->statutesim['id']) : null,
            $request->technologieesim ? TechnologieEsim::find($request->technologieesim['id']) : null
        );
        return $esim;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Esim $esim
     * @return void
     */
    public function destroy(Esim $esim): void
    {
        $esim->delete();
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
