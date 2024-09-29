<?php

namespace App\Http\Controllers\Employes;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Employes\PhoneNum;
use Illuminate\Http\JsonResponse;
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
        return $request->hasphonenum->addNewPhoneNum($request->phonenum,true);
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
     * @return PhoneNum
     */
    public function edit(PhoneNum $phonenum)
    {
        return $phonenum->load(['status','creator','esim']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePhoneNumRequest $request
     * @param PhoneNum $phonenum
     * @return PhoneNum
     */
    public function update(UpdatePhoneNumRequest $request, PhoneNum $phonenum)
    {
        $phonenum->update([
            'phonenum' => $request->phonenum,
            'posi' => $request->posi
        ]);

        return $phonenum;
    }

    public function esimrecycle(Request $request, PhoneNum $phonenum)
    {
        $phonenum->changeEsim(null);

        return $phonenum->load(['status','creator','esim']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param PhoneNum $phonenum
     * @return JsonResponse|null
     */
    public function destroy(PhoneNum $phonenum)
    {
        $phonenum->delete();
        return response()->json(['status' => 'ok'], 200);
    }
}
