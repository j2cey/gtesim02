<?php

namespace App\Http\Controllers\Employes;

use Illuminate\Http\Response;
use App\Models\Employes\PhoneNum;
use App\Jobs\ClientEsimSendMailJob;
use App\Http\Controllers\Controller;
use App\Http\Resources\Employes\PhoneNumResource;
use App\Http\Requests\PhoneNum\StorePhoneNumRequest;
use App\Http\Requests\PhoneNum\UpdatePhoneNumRequest;

class PhoneNumController extends Controller
{
    public function fetchall() {
        return PhoneNum::all()->count();
    }
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(): void
    {
        //
    }

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
     * @param StorePhoneNumRequest $request
     * @return PhoneNum|void
     */
    public function store(StorePhoneNumRequest $request)
    {
        return $request->hasphonenum->addNewPhoneNum($request->numero,true);
    }

    public function changeesim(UpdatePhoneNumRequest $request, PhoneNum $phonenum)
    {
        $phonenum->changeEsim(null);

        ClientEsimSendMailJob::dispatch($phonenum);

        return response()->json(['data' => $phonenum]);
    }

    public function getchangeesim($id) {
        $phonenum = PhoneNum::where('id', $id)->first();

        $phonenum->changeEsim(null);

        ClientEsimSendMailJob::dispatch($phonenum);

        return response()->json(['phonenum'=> new PhoneNumResource($phonenum) ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param PhoneNum $phonenum
     * @return void
     */
    public function show(PhoneNum $phonenum): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param PhoneNum $phonenum
     * @return void
     */
    public function edit(PhoneNum $phonenum): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePhoneNumRequest $request
     * @param PhoneNum $phonenum
     * @return PhoneNumResource|PhoneNum|void
     */
    public function update(UpdatePhoneNumRequest $request, PhoneNum $phonenum)
    {
        $phonenum->update([
            'numero' => $request->numero
        ]);

        return new PhoneNumResource($phonenum);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param PhoneNum $phonenum
     * @return Response|null|void
     */
    public function destroy(PhoneNum $phonenum): ?Response
    {
        //
    }
}
