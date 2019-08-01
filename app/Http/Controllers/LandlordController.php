<?php

namespace App\Http\Controllers;

use App\Landlord;
use Illuminate\Http\Request;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;

class LandlordController extends Controller
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
            ->index('landlords', Landlord::with($request->withNested)->get())
            ->relations($request->withNested);

        return view('shared.index', $responseData->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $responseData = new FormDataResponser();
        return view('shared.form', $responseData->create(Landlord::class, 'landlords.store')->get());
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
            'name' => 'required|max:255',
            'certificate_number' => 'required',
            'is_legal_person' => 'required|boolean',
            'is_collected_by_third_party' => 'required|boolean',
        ]);

        Landlord::create($validatedData);
        return response()->json(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Landlord  $landlord
     * @return \Illuminate\Http\Response
     */
    public function show(Request  $request, Landlord $landlord)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($landlord->load($request->withNested))
            ->relations($request->withNested);

        return view('shared.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Landlord  $landlord
     * @return \Illuminate\Http\Response
     */
    public function edit(Landlord $landlord)
    {
        $responseData = new FormDataResponser();
        return view('shared.form', $responseData->edit($landlord, 'landlords.update')->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Landlord  $landlord
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Landlord $landlord)
    {

        $validatedData = $request->validate([
            'name' => 'nullable|max:255',
            'certificate_number' => 'required',
            'is_legal_person' => 'required|boolean',
            'is_collected_by_third_party' => 'required|boolean',
        ]);

        $landlord->update($validatedData);
        return response()->json(true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Landlord  $landlord
     * @return \Illuminate\Http\Response
     */
    public function destroy(Landlord $landlord)
    {
        $landlord->delete();
        return response()->json(true);
    }
}
