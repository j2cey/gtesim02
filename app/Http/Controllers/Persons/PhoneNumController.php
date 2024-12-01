<?php

namespace App\Http\Controllers\Persons;

use Illuminate\Http\Request;
use App\Models\Person\PhoneNum;
use Illuminate\Http\JsonResponse;
use App\Jobs\ClientEsimSendMailJob;
use App\Http\Controllers\Controller;
use App\Traits\PhoneNum\ModelPhoneNums;
use App\Http\Resources\Persons\PhoneNumResource;
use App\Http\Requests\PhoneNum\RecycleEsimRequest;
use App\Http\Requests\PhoneNum\StorePhoneNumRequest;
use App\Http\Requests\PhoneNum\UpdatePhoneNumRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PhoneNumController extends Controller
{
    use ModelPhoneNums;

    public function fetchall() {
        return PhoneNum::all()->count();
    }
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $phonenums = $this->modelPhoneNumQuery(request('query'))
            ->latest()
            ->paginate(50);

        return PhoneNumResource::collection($phonenums);
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
     * @return PhoneNum|null
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
     * @return PhoneNumResource
     */
    public function edit(PhoneNum $phonenum)
    {
        return New PhoneNumResource( $phonenum->load(['status','creator','esim']) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePhoneNumRequest $request
     * @param PhoneNum $phonenum
     * @return PhoneNumResource
     */
    public function update(UpdatePhoneNumRequest $request, PhoneNum $phonenum)
    {
        $phonenum->updateOne($request->phone_number,$request->posi);

        return New PhoneNumResource( $phonenum->load(['status','creator','esim']) );
    }

    public function esimrecycle(RecycleEsimRequest $request, PhoneNum $phonenum)
    {
        $phonenum->changeEsim($request->esim_id);

        ClientEsimSendMailJob::dispatch($phonenum);

        return New PhoneNumResource( $phonenum->load(['status','creator','esim']) );
    }

    public function esimsendmail(Request $request, PhoneNum $phonenum)
    {
        ClientEsimSendMailJob::dispatch($phonenum);

        return New PhoneNumResource( $phonenum->load(['status','creator','esim']) );
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
