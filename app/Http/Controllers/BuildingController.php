<?php

namespace App\Http\Controllers;

use App\Building;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use App\Services\BuildingService;

class BuildingController extends Controller
{
    // protected $buildingService;

    // public function __construct(BuildingService $buildingService)
    // {
    //     $this->buildingService = $buildingService;
    // }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // in case of might need to display nested resources while listing
        // ex:  $responseData->relations(['rooms'])

        $responseData = new NestedRelationResponser();
        $responseData
            ->index('buildings',
                $this->limitRecords(Building::with($request->withNested))
            )
            ->relations($request->withNested);

        return view('buildings.index', $responseData->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $responseData = new FormDataResponser();
        $data = $responseData
            ->create(Building::class, 'buildings.store')
            ->get();

        return view('buildings.form', $data);
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
            'title' => 'required|max:255',
            'city' => [
                'required',
                Rule::in(array_keys(config('enums.cities')))
            ],
            'district' => [
                'bail',
                'required_with:city',
                Rule::in(config('enums.cities.' . $request->city))
            ],
            'address' => 'required|max:255',
            'tax_number' => 'required|max:255',
            'building_type' => 'required|max:255',
            'floor' => 'required|integer|digits_between:1,11',
            'legal_usage' => 'required|max:255',
            'has_elevator' => 'required|boolean',
            'security_guard' => 'required|max:255',
            'management_count' => 'required|max:255',
            'first_floor_door_opening' => 'required|max:255',
            'public_area_door_opening' => 'required|max:255',
            'room_door_opening' => 'required|max:255',
            'main_ammeter_location' => 'required|max:255',
            'ammeter_serial_number_1' => 'required|max:255',
            'shared_electricity' => 'required|max:255',
            'taiwan_electricity_payment_method' => 'required|max:255',
            'private_ammeter_location' => 'required|max:255',
            'water_meter_location' => 'required|max:255',
            'water_meter_serial_number' => 'required|max:255',
            'water_payment_method' => 'required|max:255',
            'water_meter_reading_date' => 'required|date',
            'gas_meter_location' => 'required|max:255',
            'garbage_collection_location' => 'required|max:255',
            'garbage_collection_time' => 'required|max:255',
            'management_fee_payment_method' => 'required|max:255',
            'management_fee_contact' => 'required|max:255',
            'management_fee_contact_phone' => 'required|max:255',
            'distribution_method' => 'required|max:255',
            'administrative_number' => 'required|max:255',
            'accounting_group' => 'required|max:255',
            'rental_receipt' => 'required|max:255',
            'commissioner_id' => 'nullable|exists:users,id',
            'administrator_id' => 'nullable|exists:users,id',
            'comment' => 'required|max:255'
        ]);

        $newBuilding = BuildingService::create($validatedData);

        return redirect($request->_redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Building  $building
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Building $building)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($building->load($request->withNested))
            ->relations($request->withNested);

        return view('buildings.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Building  $building
     * @return \Illuminate\Http\Response
     */
    public function edit(Building $building)
    {
        $responseData = new FormDataResponser();
        return view(
            'buildings.form',
            $responseData->edit($building, 'buildings.update')->get()
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Building  $building
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Building $building)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'city' => [
                'required',
                Rule::in(array_keys(config('enums.cities')))
            ],
            'district' => [
                'bail',
                'required_with:city',
                Rule::in(config('enums.cities.' . $request->city))
            ],
            'address' => 'required|max:255',
            'tax_number' => 'required|max:255',
            'building_type' => 'required|max:255',
            'floor' => 'required|integer|digits_between:1,11',
            'legal_usage' => 'required|max:255',
            'has_elevator' => 'required|boolean',
            'security_guard' => 'required|max:255',
            'management_count' => 'required|max:255',
            'first_floor_door_opening' => 'required|max:255',
            'public_area_door_opening' => 'required|max:255',
            'room_door_opening' => 'required|max:255',
            'main_ammeter_location' => 'required|max:255',
            'ammeter_serial_number_1' => 'required|max:255',
            'shared_electricity' => 'required|max:255',
            'taiwan_electricity_payment_method' => 'required|max:255',
            'private_ammeter_location' => 'required|max:255',
            'water_meter_location' => 'required|max:255',
            'water_meter_serial_number' => 'required|max:255',
            'water_payment_method' => 'required|max:255',
            'water_meter_reading_date' => 'required|date',
            'gas_meter_location' => 'required|max:255',
            'garbage_collection_location' => 'required|max:255',
            'garbage_collection_time' => 'required|max:255',
            'management_fee_payment_method' => 'required|max:255',
            'management_fee_contact' => 'required|max:255',
            'management_fee_contact_phone' => 'required|max:255',
            'distribution_method' => 'required|max:255',
            'administrative_number' => 'required|max:255',
            'accounting_group' => 'required|max:255',
            'rental_receipt' => 'required|max:255',
            'commissioner_id' => 'nullable|exists:users,id',
            'administrator_id' => 'nullable|exists:users,id',
            'comment' => 'required|max:255'
        ]);

        $building->update($validatedData);

        return redirect($request->_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Building  $building
     * @return \Illuminate\Http\Response
     */
    public function destroy(Building $building)
    {
        $building->delete();
        return response()->json(true);
    }

    public function electricityPaymentReport(Building $building, int $year, int $month) {
        $reportRows = $this->buildElectricityPaymentReportData($building, $year, $month);

        return view('buildings.electricity_payment_report', [
            'reportRows' => $reportRows,
            'year' => $year,
            'month' => $month,
        ]);
    }

    /**
     * 組前三個月的租客電費報表資料
     *
     * @param Building $building
     * @param int $year
     * @param int $month \
     *
     * @return array
     */
    private function buildElectricityPaymentReportData(Building $building, int $year, int $month): Collection {
        $rooms = $building->rooms()->get();

        return $rooms->map(function ($room) use ($year, $month) { return $room->buildElectricityPaymentData($year, $month); });
    }
}
