<?php

namespace App\Http\Controllers;

use App\Appliance;
use Illuminate\Http\Request;

use App\Responser\FormDataResponser;

class ApplianceController extends Controller
{
    public function __construct()
    {
        $this->middleware('with.prefill')->only('create');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $responseData = new FormDataResponser();
        return view(
            'appliances.form',
            $responseData->create(Appliance::class, 'appliances.store')->get()
        )->with(['room_id' => $request->prefill['rooms'] ?? null]);
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
            'spec_code' => 'required|max:255',
            'vendor' => 'required|max:255',
            'count' => 'required|integer|digits_between:1,11',
            'maintenance_phone' => 'required|max:255',
            'comment' => 'nullable'
        ]);

        $appliance = Appliance::create($validatedData);

        return redirect()->route('appliances.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Appliance  $appliance
     * @return \Illuminate\Http\Response
     */
    public function show(Appliance $appliance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Appliance  $appliance
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Appliance $appliance)
    {
        $responseData = new FormDataResponser();
        return view(
            'appliances.form',
            $responseData->edit($appliance, 'appliances.update')->get()
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Appliance  $appliance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appliance $appliance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Appliance  $appliance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appliance $appliance)
    {
        //
    }
}
