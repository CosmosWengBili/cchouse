<?php

namespace App\Http\Controllers;

use Validator;
use App\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use App\Services\RoomService;

use App\Traits\Controllers\HandleDocumentsUpload;

class RoomController extends Controller
{
    use HandleDocumentsUpload;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->index(
                'rooms',
                $this->limitRecords(Room::with($request->withNested))
            )
            ->relations($request->withNested);

        return view('rooms.index', $responseData->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $responseData = new FormDataResponser();
        $data = $responseData->create(Room::class, 'rooms.store')->get();
        $data['data']['pictures'] = [];
        $data['data']['appliances'] = [];
        $data['data']['room_code'] = '系統產生';

        return view('rooms.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_id' => 'required|exists:buildings,id',
            'room_code' => 'required',
            'virtual_account' => 'required|max:255',
            'room_status' => [
                'required',
                Rule::in(config('enums.rooms.room_status'))
            ],
            'room_number' => 'required|max:255',
            'room_layout' => [
                'required',
                // Rule::in(config('enums.rooms.room_layout'))
            ],
            'living_room_count' => 'required|integer|digits_between:1,11',
            'room_count' => 'required|integer|digits_between:1,11',
            'bathroom_count' => 'required|integer|digits_between:1,11',
            'parking_count' => 'required|integer|digits_between:1,11',
            'rent_reserve_price' => 'required|integer|digits_between:1,11',
            'rent_actual' => 'required|integer|digits_between:1,11',
            'internet_form' => 'required|max:255',
            'management_fee_mode' => [
                'required',
                Rule::in(config('enums.rooms.management_fee_mode'))
            ],
            'management_fee' => 'required|numeric',
            'wifi_account' => 'required|max:255',
            'wifi_password' => 'required|max:255',
            'has_digital_tv' => 'required|boolean',
            'comment' => 'max:255'
        ]);

        $validator->sometimes('management_fee', 'between:0,99.99', function ($input) {
            return $input->management_fee_mode == '比例';
        });

        $validatedData = $validator->validate();

        $validatedApplianceData = $request->validate([
            'appliances' => 'nullable|array',
            'appliances.*.subject' => 'required',
            'appliances.*.spec_code' => 'required',
            'appliances.*.maintenance_phone' => 'required',
            'appliances.*.count' => 'required_with:appliances|integer|digits_between:1,11',
            'appliances.*.vendor' => 'required',
            'appliances.*.comment' => 'nullable',
        ]);

        $room = RoomService::create(
            $validatedData,
            $validatedApplianceData['appliances'] ?? []
        );
        $this->handleDocumentsUpload($room, ['picture']);

        return redirect($request->_redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Room $room)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($room->load($request->withNested))
            ->relations($request->withNested);

        return view('rooms.show', $responseData->get());
    }

    public function deposits(Room $room)
    {
        return response()->json($room->deposits()->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Room $room)
    {
        $responseData = new FormDataResponser();
        $data = $responseData->edit($room, 'rooms.update')->get();
        $data['data']['pictures'] = $room->pictures()->get();
        $data['data']['appliances'] = $room->appliances()->get();
        $data['data']['maintenances'] = $room->roomMaintenances()
                                        ->with('pictures')
                                        ->get()
                                        ->toArray();

        return view('rooms.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Room $room)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'building_id' => 'required|exists:buildings,id',
            'room_code' => 'required',
            'virtual_account' => 'required|max:255',
            'room_status' => [
                'required',
                Rule::in(config('enums.rooms.room_status'))
            ],
            'room_number' => 'required|max:255',
            'room_layout' => [
                'required',
                // Rule::in(config('enums.rooms.room_layout'))
            ],
            'living_room_count' => 'required|integer|digits_between:1,11',
            'room_count' => 'required|integer|digits_between:1,11',
            'bathroom_count' => 'required|integer|digits_between:1,11',
            'parking_count' => 'required|integer|digits_between:1,11',
            'rent_reserve_price' => 'required|integer|digits_between:1,11',
            'rent_actual' => 'required|integer|digits_between:1,11',
            'internet_form' => 'required|max:255',
            'management_fee_mode' => [
                'required',
                Rule::in(config('enums.rooms.management_fee_mode'))
            ],
            'management_fee' => 'required|numeric',
            'wifi_account' => 'required|max:255',
            'wifi_password' => 'required|max:255',
            'has_digital_tv' => 'required|boolean',
            'comment' => 'max:255',
        ]);

        $validator->sometimes('management_fee', 'between:0,99.99', function ($input) {
            return $input->management_fee_mode == '比例';
        });

        $validatedData = $validator->validate();

        $validatedApplianceData = $request->validate([
            'appliances' => 'nullable|array',
            'appliances.*.subject' => 'required',
            'appliances.*.spec_code' => 'required',
            'appliances.*.maintenance_phone' => 'required',
            'appliances.*.count' => 'required_with:appliances|integer|digits_between:1,11',
            'appliances.*.vendor' => 'required',
        ]);

        $validatedMaintenanceData = $request->validate([
            'maintenances' => 'nullable|array',
            'maintenances.*.maintainer' => 'required',
            'maintenances.*.maintained_location' => 'required',
            'maintenances.*.maintained_date' => 'required',
            // 'maintenances.*.picture' => 'nullable',
        ]);

        $validatedPictureData = $request->validate([
            'documents.picture' => 'required|array|min:5',
        ]);

        RoomService::update(
            $room,
            $validatedData,
            $validatedApplianceData['appliances'] ?? [],
            $validatedMaintenanceData['maintenances'] ?? []
        );
        $this->handleDocumentsUpload($room, ['picture']);

        return redirect($request->_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        $room->delete();

        return response()->json(true);
    }
}
