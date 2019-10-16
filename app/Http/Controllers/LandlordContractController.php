<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\LandlordContract;
use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use App\Responser\SubTableResponser;
use App\Traits\Controllers\HandleDocumentsUpload;

use OwenIt\Auditing\Contracts\Auditor;



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

        $columns = array_map(function ($column) { return "landlord_contracts.{$column}"; }, $this->whitelist('landlord_contracts'));
        $selectColumns = array_merge($columns, LandlordContract::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));

        $data = $this->limitRecords(
            LandlordContract::withExtraInfo()->select($selectStr)->with($request->withNested)->with('landlords', 'building'),
            true
        );

        $data = $data->map(function ($item, $key) {
            return collect($item)->except(['building_id', 'landlord_ids'])->toArray();
        });


        $responseData
            ->index(
                'landlord_contracts',
                $data
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
            'commission_start_date' => 'present|date',
            'commission_end_date' => 'present|date',
            'warranty_start_date' => 'required|date',
            'warranty_end_date' => 'required|date',
            'rental_decoration_free_start_date' => 'required|date',
            'rental_decoration_free_end_date' => 'required|date',
            'annual_service_fee_month_count' =>
                'present|integer|digits_between:1,11',
            'charter_fee' => 'required|min:0',
            'taxable_charter_fee' => 'required|integer|digits_between:1,11',
            'agency_service_fee' => 'present',
            'rent_collection_frequency' => 'required|max:255',
            'rent_collection_time' => 'required|integer|digits_between:1,11',
            'rent_adjusted_date' => 'required|date',
            'adjust_ratio' => 'required|numeric|min:0',
            'deposit_month_count' => 'required|integer|digits_between:1,11',
            'is_collected_by_third_party' => 'required|boolean',
            'is_notarized' => 'required',
            'can_keep_pets' => 'required|boolean',
            'gender_limit' => [
                'required',
                Rule::in(config('enums.landlord_contracts.gender_limit'))
            ],
        ]);

        $landlordContract = LandlordContract::create($validatedData);
        if ($request->landlord_ids != '') {
            $landlordContract->landlords()->sync(explode(",", $request->landlord_ids));
        } else {
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
            'agency_service_fee' => 'nullable',
            'rent_collection_frequency' => 'required|max:255',
            'rent_collection_time' => 'required|integer|digits_between:1,11',
            'rent_adjusted_date' => 'required|date',
            'adjust_ratio' => 'required|numeric|between:0,99.99',
            'deposit_month_count' => 'required|integer|digits_between:1,11',
            'is_collected_by_third_party' => 'required|boolean',
            'is_notarized' => 'required',
            'can_keep_pets' => 'required|boolean',
            'gender_limit' => [
                'required',
                Rule::in(config('enums.landlord_contracts.gender_limit'))
            ],
        ]);

        $landlordContract->update($validatedData);
        if ($request->landlord_ids != '') {
            $landlordContract->landlords()->sync(explode(",", $request->landlord_ids));
        } else {
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
