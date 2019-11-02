<?php

namespace App\Http\Controllers;

use App\RoomMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use App\Services\InvoiceService;

use App\Traits\Controllers\HandleDocumentsUpload;

class RoomMaintenanceController extends Controller
{
    use HandleDocumentsUpload;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'roomId' => 'required'
        ]);

        $responseData = new FormDataResponser();
        $data = $responseData
            ->create(RoomMaintenance::class, 'roomMaintenances.store')
            ->get();

        $data['data']['pictures'] = [];

        if ($request->old()) {
            $data['data'] = array_merge($data['data'], $request->old());
        }

        return view('room_maintenances.form', $data)->with([
            'room_id' => $validatedData['roomId']
        ]);
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
            'room_id' => 'required',
            'maintainer' => 'required',
            'maintained_location' => 'required',
            'maintained_date' => 'required',
        ]);

        $validatedPhotoData = $request->validate([
            //
        ]);

        $roomMaintenance = RoomMaintenance::create($validatedData);
        $this->handleDocumentsUpload($roomMaintenance, ['picture']);

        return redirect($request->_redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RoomMaintenance  $roomMaintenance
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, RoomMaintenance $roomMaintenance)
    {
        $responseData = new NestedRelationResponser();
        $data = $responseData->show($roomMaintenance->load($request->withNested))
                            ->relations($request->withNested)
                            ->get();

        $data['documents'] = $roomMaintenance->documents;

        return view('room_maintenances.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RoomMaintenance  $roomMaintenance
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, RoomMaintenance $roomMaintenance)
    {
        $responseData = new FormDataResponser();
        $data = $responseData->edit($roomMaintenance, 'roomMaintenances.update')->get();
        $data['data']['pictures'] = $roomMaintenance->pictures()->get();

        if ($request->old()) {
            $data['data'] = array_merge($data['data'], $request->old());
        }

        return view('room_maintenances.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RoomMaintenance  $roomMaintenance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RoomMaintenance $roomMaintenance)
    {
        $validatedData = $request->validate([
            'room_id' => 'required',
            'maintainer' => 'required',
            'maintained_location' => 'required',
            'maintained_date' => 'required',
        ]);

        $validatedPhotoData = $request->validate([
            //
        ]);

        $roomMaintenance->update($validatedData);
        $this->handleDocumentsUpload($roomMaintenance, ['picture']);

        return redirect($request->_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RoomMaintenance  $roomMaintenance
     * @return \Illuminate\Http\Response
     */
    public function destroy(RoomMaintenance $roomMaintenance)
    {
        $roomMaintenance->delete();

        return response()->json(true);
    }
}
