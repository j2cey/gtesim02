<?php

namespace App\Http\Controllers\Esims;

use PDF;
use \Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Esims\ClientEsim;
use App\Models\Employes\PhoneNum;
use Illuminate\Support\Collection;
use App\Jobs\ClientEsimSendMailJob;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use App\Http\Resources\SearchCollection;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\ClientEsim\FetchRequest;
use App\Http\Resources\Esims\ClientEsimResource;
use Illuminate\Contracts\Foundation\Application;
use App\Http\Requests\ClientEsim\StoreClientEsimRequest;
use App\Http\Requests\ClientEsim\UpdateClientEsimRequest;
use App\Repositories\Contracts\IClientEsimRepositoryContract;
use App\Http\Requests\ClientEsim\StoreClientEsimPhonenumRequest;

class ClientEsimController extends Controller
{
    /**
     * @var IClientEsimRepositoryContract
     */
    private IClientEsimRepositoryContract $repository;

    /**
     * ClientEsimController constructor.
     *
     * @param IClientEsimRepositoryContract $repository [description]
     */
    public function __construct(IClientEsimRepositoryContract $repository) {
        $this->repository = $repository;
    }

    public function previewPDF($id) {

        $phonenum = PhoneNum::with('hasphonenum')->where('id', $id)->first();
        //$client = new ClientEsimResource(ClientEsim::where('id', $id)->first());
        //dd($client);
        //$acqrcode = QrCode::size(100)->generate($client->esim->ac);
        return view('clientesims.preview')
            ->with('phonenum', $phonenum);
    }

    public function generatePDF($id): Response
    {
        //$client = new ClientEsimResource(ClientEsim::where('id', $id)->first());
        $phonenum = PhoneNum::with('hasphonenum')->where('id', $id)->first();
        $acqrcode = QrCode::size(100)->generate($phonenum->esim->ac);

        $pdf = PDF::loadView('clientesims.preview', ['phonenum' => $phonenum, 'acqrcode' => $acqrcode, 'generate_now' => true])->setPaper('a4', 'portrait');

        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> TRUE,
                    'verify_peer' => FALSE,
                    'verify_peer_name' => FALSE,
                ]
            ])
        );

        return $pdf->download('clientesims.pdf');
    }

    public function preprintPDF($id): Response
    {
        //$client = new ClientEsimResource(ClientEsim::where('id', $id)->first());
        $phonenum = PhoneNum::with('hasphonenum')->where('id', $id)->first();
        $acqrcode = QrCode::size(100)->generate($phonenum->esim->ac);

        $pdf = PDF::loadView('clientesims.preview', ['phonenum' => $phonenum, 'acqrcode' => $acqrcode, 'generate_now' => true]);

        return $pdf->setPaper('a4', 'portrait')->stream();
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
            $this->repository->search($request), ClientEsimResource::class
        );
    }

    public function fetchall() {
        return ClientEsim::all();
    }

    /**
     * Display products page.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function index()
    {
        return view('clientesims.index')
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

    public function checkbeforecreate(StoreClientEsimRequest $request)
    {
        //dd($request->all());
        $clientsesims_matched = ClientEsim::where('nom_raison_sociale','LIKE', '%' . $request->nom_raison_sociale . '%')
            ->where('prenom', 'LIKE', '%' . $request->prenom . '%')
            ->get();

        $res = [
            'action_type' => 0,
            'val' => null,
        ];

        if ( $clientsesims_matched->count() > 0 ) {
            // some matches
            $res['action_type'] = 1;
            $res['val'] = $clientsesims_matched;
        } else {
            // no match
            $res['action_type'] = 2;
            $res['val'] = $this->storeclientesim($request);
        }

        return response()->json([
            'data' => $res
        ], 200);
    }

    public function store(StoreClientEsimRequest $request)
    {
        //dd($request->all());
        return $this->storeclientesim($request);
    }

    public function storeclientesim(StoreClientEsimRequest $request) {

        /*
        Validator::make($request->all(), [
            'numero_telephone' => Rule::unique('phone_nums', 'numero')
                ->where(function ($query, $request) {
                    $query->where('numero', $request->numero_telephone) ->where('hasphonenum_type', ClientEsim::class);
                })->ignore($request->numero_telephone),
        ]);
        */

        if ( is_null($request->client_matched_selected) ) {
            $clientesim = ClientEsim::createNew(
                $request->nom_raison_sociale,
                $request->prenom,
                $request->email,
                $request->numero
            );
        } else {
            $clientesim = $request->client_matched_selected;
        }
        $clientesim->addNewEmailAddress($request->email);
        $phonenum = $clientesim->addNewPhoneNum($request->numero,true,$request->esim_id);

        ClientEsimSendMailJob::dispatch($phonenum);

        return [new ClientEsimResource($clientesim),$phonenum];
    }

    /*
    public function phonenumstore(StoreClientEsimPhonenumRequest $request) {
        return $request->client_esim->addNewPhoneNum($request->numero,true);
    }
    */

    public function phonenumschangeesim(StoreClientEsimPhonenumRequest $request) {
        $phonenum = $request->client_esim->phonenums()->where('numero', $request->numero)->first();
        //dd($phonenum, $request->all());
        return $phonenum->changeEsim(null);
    }

    public function mailtest($id): void
    {
        $clientesim = ClientEsim::where('id', $id)->first();
        $clientesim->esim->saveQrcode();
        //Mail::to($clientesim->email)->send(new NotifyProfileEsim($clientesim));
    }

    public function sendMail($id): void
    {
        $clientesim = ClientEsim::where('id', $id)->first();

        $response = $clientesim->sendmailprofile();

        //dd($response);
    }

    /**
     * Display the specified resource.
     *
     * @param ClientEsim $clientesim
     * @return Application|Factory|\Illuminate\Contracts\View\View|Response
     */
    public function show(ClientEsim $clientesim)
    {
        return view('clientesims.show')
            ->with('clientesim', new ClientEsimResource($clientesim));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  ClientEsim  $clientEsim
     * @return Response
     */
    public function edit(ClientEsim $clientEsim)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClientEsimRequest $request
     * @param ClientEsim $clientesim
     * @return ClientEsimResource
     */
    public function update(UpdateClientEsimRequest $request, ClientEsim $clientesim)
    {
        $clientesim = $clientesim->updateOne(
            $request->esim_id,
            $request->nom_raison_sociale,
            $request->prenom,
            $request->email,
            $request->numero_telephone
        );
        return new ClientEsimResource($clientesim);
    }

    public function deletephone(Request $request, ClientEsim $clientesim) {

        $rslt = $clientesim->removePhonenum($request->numero);

        $data = [ "success" => $rslt ];

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ClientEsim $clientesim
     * @return RedirectResponse|Response
     */
    public function destroy(ClientEsim $clientesim)
    {
        $clientesim->delete();

        return redirect()->route('clientesims.index');
    }
}
