<?php

namespace App\Http\Controllers\Esims;

use PDF;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Person\PhoneNum;
use App\Models\Esims\ClientEsim;
use App\Jobs\ClientEsimSendMailJob;
use App\Models\Person\EmailAddress;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use App\Traits\PhoneNum\ModelPhoneNums;
use App\Http\Resources\Persons\PhoneNumResource;
use App\Http\Requests\Status\StatusChangeRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Resources\Esims\ClientEsimResource;
use Illuminate\Contracts\Foundation\Application;
use App\Traits\EmailAddress\ModelEmailAddresses;
use App\Http\Resources\Persons\EmailAddressResource;
use App\Http\Requests\ClientEsim\StoreClientEsimRequest;
use App\Http\Requests\ClientEsim\UpdateClientEsimRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Http\Requests\ClientEsim\AddClientEsimPhoneNumRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\ClientEsim\AddClientEsimEmailAddressRequest;

class ClientEsimController extends Controller
{
    use ModelPhoneNums, ModelEmailAddresses;

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

    public function fetchall() {
        return ClientEsim::all();
    }

    /**
     * Display products page.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $clientesims = ClientEsim::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('nom_raison_sociale', 'like', "%{$searchQuery}%")
                    ->orWhere('prenom', 'like', "%{$searchQuery}%")
                    ->orWhere('phone_number_list', 'like', "%{$searchQuery}%")
                    ->orWhere('email_address_list', 'like', "%{$searchQuery}%")
                ;
            })
            ->with("status")
            ->with("creator")
            ->latest()
            ->paginate(50);

        return ClientEsimResource::collection($clientesims);
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function phonenums(ClientEsim $clientesim)
    {
        $phonenums = $this->modelPhoneNumQuery(request('query'),ClientEsim::class, $clientesim->id)
            ->latest()
            ->paginate(5);

        return PhoneNumResource::collection( $phonenums );
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function emailaddressindex(ClientEsim $clientesim)
    {
        $emailaddresses = $this->modelEmailAddressQuery(request('query'),ClientEsim::class, $clientesim->id)
            ->latest()
            ->paginate(5);

        return EmailAddressResource::collection( $emailaddresses );
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
            $res['val'] = null;//$this->store($request);
        }

        return $res;
    }

    public function store(StoreClientEsimRequest $request)
    {
        $clientesim = ClientEsim::createNew(
            $request->nom_raison_sociale,
            $request->prenom,
            $request->email_address,
            $request->phone_number
        );
        $clientesim->addNewEmailAddress($request->email_address);
        $phonenum = $clientesim->addNewPhoneNum($request->phone_number,true,$request->esim_id);

        $clientesim->load(['phonenums','emailaddresses']);
        $clientesim->setPhonenumList();
        $clientesim->setEmailAddressList();

        return $this->notifyclientesim($clientesim, $phonenum);
    }

    public function addphone(AddClientEsimPhoneNumRequest $request)
    {
        $phonenum = $request->clientesim->addNewPhoneNum($request->phone_number,true,$request->esim_id);
        $clientesim = $request->clientesim;
        $clientesim->load(['phonenums']);
        $clientesim->setPhonenumList();

        return $this->notifyclientesim($clientesim, $phonenum);
    }
    public function phonenumvalidate(AddClientEsimPhoneNumRequest $request, ClientEsim $clientesim)
    {
        return response()->json(['status' => 'ok'], 200);
    }
    public function phonenumadd(AddClientEsimPhoneNumRequest $request, ClientEsim $clientesim)
    {
        $phonenum = $clientesim->addNewPhoneNum($request->phone_number,true,$request->esim_id);
        $clientesim->load(['phonenums']);
        $clientesim->setPhonenumList();

        return $this->notifyclientesim($clientesim, $phonenum);
    }
    public function emailaddressadd(AddClientEsimEmailAddressRequest $request, ClientEsim $clientesim)
    {
        $emailaddress = $clientesim->addNewEmailAddress($request->email_address);
        $clientesim->load(['emailaddresses']);
        $clientesim->setEmailAddressList();

        return ['clientesim' => $clientesim, 'emailaddress' => $emailaddress];
    }

    public function notifyclientesim(ClientEsim $clientesim, PhoneNum $phonenum) {

        ClientEsimSendMailJob::dispatch($phonenum);

        return ['clientesim' => $clientesim, 'phonenum' => New PhoneNumResource($phonenum)];
    }

    /*
    public function phonenumstore(StoreClientEsimPhonenumRequest $request) {
        return $request->client_esim->addNewPhoneNum($request->numero,true);
    }
    */

    /*public function phonenumschangeesim(StoreClientEsimPhonenumRequest $request) {
        $phonenum = $request->client_esim->phonenums()->where('phonenum', $request->phonenum)->first();
        return $phonenum->changeEsim(null);
    }*/

    public function mailtest($id): void
    {
        $clientesim = ClientEsim::where('id', $id)->first();
        $clientesim->esim->saveQrcode();
        //Mail::to($clientesim->email_address)->send(new NotifyProfileEsim($clientesim));
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
     * @param  ClientEsim  $clientesim
     * @return ClientEsimResource
     */
    public function edit(ClientEsim $clientesim)
    {
        return new ClientEsimResource($clientesim->load(['status','creator']));
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
            $request->emailaddress,
            $request->phonenum
        );
        return new ClientEsimResource($clientesim);
    }
    /*public function phonenumupdate(UpdateClientEsimPhoneNumRequest $request, ClientEsim $clientesim, PhoneNum $phonenum)
    {
        $phonenum->phonenum = $request->phonenum;
        $phonenum->posi = $request->posi;

        $phonenum->save();
        $clientesim->fresh();
        $clientesim->setPhonenumList();

        return $phonenum;
    }*/
    /*public function emailaddressupdate(UpdateClientEsimEmailAddressRequest $request, ClientEsim $clientesim, EmailAddress $emailaddress)
    {
        $emailaddress->updateOne($request->email_address, $request->posi);

        $clientesim->fresh();
        $clientesim->setEmailAddressList();

        return New EmailAddressResource($emailaddress);
    }*/
    public function statuschange(StatusChangeRequest $request, ClientEsim $clientesim)
    {
        $clientesim->changeStatus($request->status);

        return $clientesim->status;
    }

    public function deletephone(Request $request, ClientEsim $clientesim) {

        $rslt = $clientesim->removePhonenum($request->phonenum);

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
