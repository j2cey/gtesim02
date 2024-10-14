<?php

namespace App\Http\Controllers\Persons;

use App\Models\Person\EmailAddress;
use App\Http\Controllers\Controller;
use App\Http\Resources\Persons\EmailAddressResource;
use App\Http\Requests\EmailAddress\StoreEmailAddressRequest;
use App\Http\Requests\EmailAddress\UpdateEmailAddressRequest;

class EmailAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $emailaddresses = EmailAddress::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->orWhere('email_address', 'like', "%{$searchQuery}%")
                    ->orWhereHas('creator', function ($query) use ($searchQuery) {
                        $query->where( 'name', 'like', '%'.$searchQuery.'%' );
                    })
                ;
            })
            ->with(["hasemailaddress","creator"])
            ->latest()
            ->paginate(50);

        return EmailAddressResource::collection($emailaddresses);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmailAddressRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailAddress $emailaddress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailAddress $emailaddress)
    {
        return New EmailAddressResource( $emailaddress->load(['status','creator']) );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmailAddressRequest $request, EmailAddress $emailaddress)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailAddress $emailaddress)
    {
        //
    }
}
