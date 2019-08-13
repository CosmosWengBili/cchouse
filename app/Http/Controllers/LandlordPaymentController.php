<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\LandlordPayment;
use Illuminate\Http\Request;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;

use OwenIt\Auditing\Contracts\Auditor;

class LandlordPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $responseData = new NestedRelationResponser();

        $whitelist = $this->whitelist('landlord_payments');
        foreach($whitelist as $key => $value){
            $whitelist[$key] = 'landlord_payments.'.$value;
        }

        $landlordPayment = LandlordPayment::select(
            $whitelist
        )
            ->join('rooms', 'landlord_payments.room_id', '=', 'rooms.id')
            ->join('buildings', 'buildings.id', '=', 'rooms.building_id')
            ->rightJoin(
                'landlord_contract',
                'buildings.id',
                '=',
                'landlord_contract.building_id'
            )
            ->where('commission_end_date', '>', Carbon::today())
            ->get();
        $responseData
            ->index('landlord_payments', $landlordPayment)
            ->relations($request->withNested);

        return view('landlord_payments.index', $responseData->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $responseData = new FormDataResponser();
        $data = $responseData
            ->create(LandlordPayment::class, 'landlordPayments.store')
            ->get();

        return view('landlord_payments.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'subject' => 'required|max:255',
            'bill_serial_number' => 'required|max:255',
            'bill_start_date' => 'required|date',
            'bill_end_date' => 'required|date',
            'collection_date' => 'required|date',
            'billing_vendor' => 'required',
            'amount' => 'required|integer|digits_between:1,11',
            'comment' => 'required'
        ]);

        $landlordPayment = LandlordPayment::create($validatedData);
        return redirect()->route('landlordPayments.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LandlordPayment  $landlordPayment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, LandlordPayment $landlordPayment)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($landlordPayment->load($request->withNested))
            ->relations($request->withNested);

        // print_r($responseData->get());
        return view('landlord_payments.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LandlordPayment  $landlordPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(LandlordPayment $landlordPayment)
    {
        $responseData = new FormDataResponser();
        return view(
            'landlord_payments.form',
            $responseData
                ->edit($landlordPayment, 'landlordPayments.update')
                ->get()
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LandlordPayment  $landlordPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LandlordPayment $landlordPayment)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'subject' => 'required|max:255',
            'bill_serial_number' => 'required|max:255',
            'bill_start_date' => 'required|date',
            'bill_end_date' => 'required|date',
            'collection_date' => 'required|date',
            'billing_vendor' => 'required',
            'amount' => 'required|integer|digits_between:1,11',
            'comment' => 'required'
        ]);

        $landlordPayment->update($validatedData);
        return redirect()->route('landlordPayments.edit', [
            'id' => $landlordPayment->id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LandlordPayment  $landlord_payment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    { 
        $landlord_payment = LandlordPayment::find($id);
        $landlord_payment->delete();
        return response()->json(true);
    }
}