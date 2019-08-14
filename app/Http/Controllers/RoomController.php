<?php

namespace App\Http\Controllers;

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
            ->index('rooms', Room::with($request->withNested)->get())
            ->relations($request->withNested);
        
        return view('rooms.index', $responseData->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $responseData = new FormDataResponser();
        $data = $responseData->create(Room::class, 'rooms.store')->get();
        $data['data']['pictures'] = [];

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
        $validatedData = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'needs_decoration' => 'required|boolean',
            'room_code' => 'required|max:255',
            'virtual_account' => 'required|max:255',
            'room_status' => [
                'required',
                Rule::in(config('enums.rooms.room_status')),
            ],
            'room_number' => 'required|max:255',
            'room_layout' => [
                'required',
                Rule::in(config('enums.rooms.room_layout')),
            ],
            'room_attribute' => 'required|max:255',
            'living_room_count' => 'required|integer|digits_between:1,11',
            'room_count' => 'required|integer|digits_between:1,11',
            'bathroom_count' => 'required|integer|digits_between:1,11',
            'parking_count' => 'required|integer|digits_between:1,11',
            'ammeter_reading_date' => 'required|date',
            'rent_list_price' => 'required|integer|digits_between:1,11',
            'rent_reserve_price' => 'required|integer|digits_between:1,11',
            'rent_landlord' => 'required|integer|digits_between:1,11',
            'rent_actual' => 'required|integer|digits_between:1,11',
            'internet_form' => 'required|max:255',
            'management_fee_mode' => [
                'required',
                Rule::in(config('enums.rooms.management_fee_mode')),
            ],
            'management_fee' => 'required|numeric|between:0,99.99',
            'wifi_account' => 'required|max:255',
            'wifi_password' => 'required|max:255',
            'has_digital_tv' => 'required|boolean',
            'can_keep_pets' => 'required|boolean',
            'gender_limit' => [
                'required',
                Rule::in(config('enums.rooms.gender_limit')),
            ],
            'comment' => 'required',
        ]);

        $newRoom = RoomService::create($validatedData);
        $this->handleDocumentsUpload($newRoom, ['picture']);

        return redirect()->route('rooms.index');
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        $responseData = new FormDataResponser();
        $data = $responseData->edit($room, 'rooms.update')->get();
        $data['data']['pictures'] = $room
            ->pictures()
            ->get();
        
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
        $validatedData = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'needs_decoration' => 'required|boolean',
            'room_code' => 'required|max:255',
            'virtual_account' => 'required|max:255',
            'room_status' => [
                'required',
                Rule::in(config('enums.rooms.room_status')),
            ],
            'room_number' => 'required|max:255',
            'room_layout' => [
                'required',
                Rule::in(config('enums.rooms.room_layout')),
            ],
            'room_attribute' => 'required|max:255',
            'living_room_count' => 'required|integer|digits_between:1,11',
            'room_count' => 'required|integer|digits_between:1,11',
            'bathroom_count' => 'required|integer|digits_between:1,11',
            'parking_count' => 'required|integer|digits_between:1,11',
            'ammeter_reading_date' => 'required|date',
            'rent_list_price' => 'required|integer|digits_between:1,11',
            'rent_reserve_price' => 'required|integer|digits_between:1,11',
            'rent_landlord' => 'required|integer|digits_between:1,11',
            'rent_actual' => 'required|integer|digits_between:1,11',
            'internet_form' => 'required|max:255',
            'management_fee_mode' => [
                'required',
                Rule::in(config('enums.rooms.management_fee_mode')),
            ],
            'management_fee' => 'required|numeric|between:0,99.99',
            'wifi_account' => 'required|max:255',
            'wifi_password' => 'required|max:255',
            'has_digital_tv' => 'required|boolean',
            'can_keep_pets' => 'required|boolean',
            'gender_limit' => [
                'required',
                Rule::in(config('enums.rooms.gender_limit')),
            ],
            'comment' => 'required',
        ]);

        $room->update($validatedData);
        $this->handleDocumentsUpload($room, ['picture']);

        return redirect()->route('rooms.show', $room);
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
