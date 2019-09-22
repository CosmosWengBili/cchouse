<?php

namespace App\Http\Controllers;

use App\LandlordContract;
use Illuminate\Http\Request;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use App\Responser\SubTableResponser;

use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditor;

use App\Traits\Controllers\HandleDocumentsUpload;

class LandlordContractController extends Controller
{
    use HandleDocumentsUpload;
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $responseData = new NestedRelationResponser();
        $selectColumns = array_merge($this->whitelist('landlord_contracts'), LandlordContract::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));

        $responseData
            ->index(
                'landlord_contracts',
                $this->limitRecords(
                    LandlordContract::extraInfo()->select($selectStr)->with($request->withNested)
                )
            )
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
        $data = $responseData
            ->create(LandlordContract::class, 'landlordContracts.store')
            ->get();
        $data['data']['original_files'] = [];
        return view('landlord_contracts.form', $data);
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
            'commissioner_id' => 'required|exists:users,id',
            'commission_type' => 'required|max:255',
            'commission_start_date' => 'required|date',
            'commission_end_date' => 'required|date',
            'warranty_start_date' => 'required|date',
            'warranty_end_date' => 'required|date',
            'rental_decoration_free_start_date' => 'required|date',
            'rental_decoration_free_end_date' => 'required|date',
            'annual_service_fee_month_count' =>
                'required|integer|digits_between:1,11',
            'charter_fee' => 'required|integer|digits_between:1,11',
            'taxable_charter_fee' => 'required|integer|digits_between:1,11',
            'agency_service_fee' => 'required',
            'rent_collection_frequency' => 'required|max:255',
            'rent_collection_time' => 'required|integer|digits_between:1,11',
            'rent_adjusted_date' => 'required|date',
            'adjust_ratio' => 'required|numeric|between:0,99.99',
            'deposit_month_count' => 'required|integer|digits_between:1,11',
            'is_collected_by_third_party' => 'required|boolean',
            'is_notarized' => 'required|boolean'
        ]);

        $landlordContract = LandlordContract::create($validatedData);
        if($request->landlord_ids != ''){
            $landlordContract->landlords()->sync(explode(",",$request->landlord_ids));
        }
        else{
            $landlordContract->landlords()->sync(array());
        }
        $this->handleDocumentsUpload($landlordContract, ['original_file']);
        return redirect($request->_redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LandlordContract  $landlordContract
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, LandlordContract $landlordContract)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($landlordContract->load($request->withNested))
            ->relations($request->withNested);
        $data = $responseData->get();
        $subtableResponser = new SubTableResponser();
        $data = $subtableResponser->whitelist('LandlordContract', $data, $data['relations']);
        return view('landlord_contracts.show', $data);
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
        $data = $responseData
            ->edit($landlordContract, 'landlordContracts.update')
            ->get();
        $data['data'][
            'original_files'
        ] = $landlordContract->originalFiles()->get();
        return view('landlord_contracts.form', $data);
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
            'commissioner_id' => 'required|exists:users,id',
            'commission_type' => 'required|max:255',
            'commission_start_date' => 'required|date',
            'commission_end_date' => 'required|date',
            'warranty_start_date' => 'required|date',
            'warranty_end_date' => 'required|date',
            'rental_decoration_free_start_date' => 'required|date',
            'rental_decoration_free_end_date' => 'required|date',
            'annual_service_fee_month_count' =>
                'required|integer|digits_between:1,11',
            'charter_fee' => 'required|integer|digits_between:1,11',
            'taxable_charter_fee' => 'required|integer|digits_between:1,11',
            'rent_collection_frequency' => 'required|max:255',
            'rent_collection_time' => 'required|integer|digits_between:1,11',
            'rent_adjusted_date' => 'required|date',
            'adjust_ratio' => 'required|numeric|between:0,99.99',
            'deposit_month_count' => 'required|integer|digits_between:1,11',
            'is_collected_by_third_party' => 'required|boolean',
            'is_notarized' => 'required|boolean',
        ]);

        $landlordContract->update($validatedData);
        if($request->landlord_ids != ''){
            $landlordContract->landlords()->sync(explode(",",$request->landlord_ids));
        }
        else{
            $landlordContract->landlords()->sync(array());
        }
        $this->handleDocumentsUpload($landlordContract, ['original_file']);
        return redirect($request->_redirect);
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
