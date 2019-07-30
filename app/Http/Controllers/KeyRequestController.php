<?php

namespace App\Http\Controllers;

use App\KeyRequest;
use Illuminate\Http\Request;

use App\Responser\NestedRelationResponser;

class KeyRequestController extends Controller
{
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
            ->index('keyRequests', KeyRequest::with($request->withNested)->get())
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KeyRequest  $keyRequest
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, KeyRequest $keyRequest)
    {

        $responseData = new NestedRelationResponser();
        $responseData
            ->show($keyRequest->load($request->withNested))
            ->relations($request->withNested);

        return view('shared.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\KeyRequest  $keyRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(KeyRequest $keyRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KeyRequest  $keyRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KeyRequest $keyRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KeyRequest  $keyRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(KeyRequest $keyRequest)
    {
        //
    }
}
