<?php

namespace App\Http\Controllers;

use App\LandlordContract;
use Illuminate\Http\Request;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;


class LandlordContractController extends Controller
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
        $responseData
            ->index('landlordContracts', LandlordContract::with($request->withNested)->get())
            ->relations($request->withNested);

        return view('landlord_contracts.index', $responseData->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $responseData = new FormDataResponser();
        return view('landlord_contracts.form', $responseData->create(LandlordContract::class, 'landlordContracts.store')->get());
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
            'building_id' => 'required|exists:buildings,id',
            'landlord_id' => 'required|exists:landlords,id',
            'commissioner_id' => 'required|exists:users,id',
            'commission_type' => 'required|max:255',
            'commission_start_date' => 'required|date',
            'commission_end_date' => 'required|date',
            'warranty_start_date' => 'required|date',
            'warranty_end_date' => 'required|date',
            'rental_decoration_free_start_date' => 'required|date',
            'rental_decoration_free_end_date' => 'required|date',
            'annual_service_fee_month_count' => 'required|integer|digits_between:1,11',
            'charter_fee' => 'required|integer|digits_between:1,11',
            'taxable_charter_fee' => 'required|integer|digits_between:1,11',
            'rent_collection_frequency' => 'required|max:255',
            'rent_collection_time' => 'required|integer|digits_between:1,11',
            'rent_adjusted_date' => 'required|date',
            'adjust_ratio' => 'required|numeric|between:0,99.99',
            'deposit_month_count' => 'required|integer|digits_between:1,11',
            'is_collected_by_third_party' => 'required|boolean',
            'is_notarized' => 'required|boolean',
            'bank_code' => 'required|integer|digits_between:1,11',
            'branch_code' => 'required|integer|digits_between:1,11',
            'account_name' => 'required|max:255',
            'account_number' => 'required|max:255',
            'invoice_collection_method' => 'required|max:255',
            'invoice_collection_number' => 'required|max:255',
            'invoice_mailing_address' => 'required|max:255',
        ]);


        LandlordContract::create($validatedData);
        return response()->json(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LandlordContract  $landlordContract
     * @return \Illuminate\Http\Response
     */
    public function show(Request  $request, LandlordContract $landlordContract)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($landlordContract->load($request->withNested))
            ->relations($request->withNested);

        return view('landlord_contracts.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LandlordContract  $landlordContract
     * @return \Illuminate\Http\Response
     */
    public function edit(LandlordContract $landlordContract)
    {
        $responseData = new FormDataResponser();
        return view('landlord_contracts.form', $responseData->edit($landlordContract, 'landlordContracts.update')->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LandlordContract  $landlordContract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LandlordContract $landlordContract)
    {

        $validatedData = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'landlord_id' => 'required|exists:landlords,id',
            'commissioner_id' => 'required|exists:users,id',
            'commission_type' => 'required|max:255',
            'commission_start_date' => 'required|date',
            'commission_end_date' => 'required|date',
            'warranty_start_date' => 'required|date',
            'warranty_end_date' => 'required|date',
            'rental_decoration_free_start_date' => 'required|date',
            'rental_decoration_free_end_date' => 'required|date',
            'annual_service_fee_month_count' => 'required|integer|digits_between:1,11',
            'charter_fee' => 'required|integer|digits_between:1,11',
            'taxable_charter_fee' => 'required|integer|digits_between:1,11',
            'rent_collection_frequency' => 'required|max:255',
            'rent_collection_time' => 'required|integer|digits_between:1,11',
            'rent_adjusted_date' => 'required|date',
            'adjust_ratio' => 'required|numeric|between:0,99.99',
            'deposit_month_count' => 'required|integer|digits_between:1,11',
            'is_collected_by_third_party' => 'required|boolean',
            'is_notarized' => 'required|boolean',
            'bank_code' => 'required|integer|digits_between:1,11',
            'branch_code' => 'required|integer|digits_between:1,11',
            'account_name' => 'required|max:255',
            'account_number' => 'required|max:255',
            'invoice_collection_method' => 'required|max:255',
            'invoice_collection_number' => 'required|max:255',
            'invoice_mailing_address' => 'required|max:255',
        ]);

        $landlordContract->update($validatedData);
        return response()->json(true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LandlordContract  $landlordContract
     * @return \Illuminate\Http\Response
     */
    public function destroy(LandlordContract $landlordContract)
    {
        $landlordContract->delete();
        return response()->json(true);
    }
}