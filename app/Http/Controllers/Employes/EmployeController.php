<?php

namespace App\Http\Controllers\Employes;

use App\Models\User;
use App\Models\Employes\Employe;
use App\Models\Person\EmailAddress;
use App\Http\Controllers\Controller;
use App\Models\Employes\Departement;
use App\Traits\PhoneNum\ModelPhoneNums;
use App\Models\Employes\FonctionEmploye;
use App\Traits\EmailAddress\ModelEmailAddresses;
use App\Http\Resources\Persons\PhoneNumResource;
use App\Http\Resources\Employes\EmployeResource;
use App\Http\Requests\Employe\StoreEmployeRequest;
use App\Http\Requests\Employe\UpdateEmployeRequest;
use App\Http\Resources\Persons\EmailAddressResource;
use App\Http\Requests\Employe\AddEmployePhoneNumRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Http\Requests\Employe\AddEmployeEmailaddressRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeController extends Controller
{
    use ModelPhoneNums, ModelEmailAddresses;

    public function fetchall() {
        return Employe::all();
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $employes = Employe::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('nom', 'like', "%{$searchQuery}%")
                    ->orWhere('prenom', 'like', "%{$searchQuery}%")
                    ->orWhere('matricule', 'like', "%{$searchQuery}%")
                    ->orWhere('phone_number_list', 'like', "%{$searchQuery}%")
                    ->orWhere('email_address_list', 'like', "%{$searchQuery}%")
                ;
            })
            ->with(["status", "creator"])
            ->latest()
            ->paginate(50);

        return EmployeResource::collection( $employes );
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function phonenums(Employe $employe)
    {
        $phonenums = $this->modelPhoneNumQuery(request('query'),Employe::class, $employe->id)
            ->latest()
            ->paginate(5);

        return PhoneNumResource::collection( $phonenums );
    }

    public function phonenumadd(AddEmployePhoneNumRequest $request, Employe $employe)
    {
        $phonenum = $employe->addNewPhoneNum($request->phone_number,false,$request->esim_id);
        $employe->load(['phonenums']);
        $employe->setPhonenumList();

        return ['employe' => $employe, 'phonenum' => $phonenum];
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function emailaddressindex(Employe $employe)
    {
        $emailaddresses = $this->modelEmailAddressQuery(request('query'), Employe::class, $employe->id)
            ->latest()
            ->paginate(5);

        return EmailAddressResource::collection( $emailaddresses );
    }

    public function emailaddressadd(AddEmployeEmailaddressRequest $request, Employe $employe)
    {
        $emailaddress = $employe->addNewEmailAddress($request->email_address);
        $employe->load(['emailaddresses']);
        $employe->setEmailAddressList();

        return ['employe' => $employe, 'emailaddress' => $emailaddress];
    }

    /*public function emailaddressupdate(UpdateEmployeEmailAddressRequest $request, Employe $employe, EmailAddress $emailaddress)
    {
        $emailaddress->updateOne($request->email_address, $request->posi);

        $employe->fresh();
        $employe->setEmailAddressList();

        return New EmailAddressResource($emailaddress);
    }*/


    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreEmployeRequest $request
     * @param User|null $user
     * @return Employe
     */
    public function store(StoreEmployeRequest $request, User $user = null)
    {
        $employe = Employe::createNew(
            $request->nom, $request->prenom, $request->matricule, FonctionEmploye::find($request->fonction['id']), Departement::find($request->departement['id']), $request->adresse
        );

        if (! is_null($user)) {
            $employe->user()->associate($user)->save();
        } elseif (! is_null($request->userid)) {
            $employe->setUserByUuid($request->userid);
        }

        return $employe->load(['fonction','departement','departementsResponsable']);
    }

    /**
     * Display the specified resource.
     *
     * @param Employe $employe
     * @return void
     */
    public function show(Employe $employe): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Employe $employe
     * @return EmployeResource
     */
    public function edit(Employe $employe)
    {
        return New EmployeResource( $employe->load(['fonction','departement','departementsResponsable']) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateEmployeRequest $request
     * @param Employe $employe
     * @return Employe
     */
    public function update(UpdateEmployeRequest $request, Employe $employe)
    {
        $employe->updateOne(
            $request->nom, $request->prenom, $request->matricule, FonctionEmploye::find($request->fonction['id']), Departement::find($request->departement['id']), $request->adresse
        );

        return $employe->load(['fonction','departement','departementsResponsable']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Employe $employe
     * @return void
     */
    public function destroy(Employe $employe): void
    {
        //
    }
}
