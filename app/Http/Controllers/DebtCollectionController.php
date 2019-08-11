<?php

namespace App\Http\Controllers;

use App\DebtCollection;
use App\Responser\FormDataResponser;
use App\Tenant;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Responser\NestedRelationResponser;
use Illuminate\Support\Facades\Auth;

class DebtCollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $responseData = new NestedRelationResponser();
        $debtCollections = DebtCollection::select($this->whitelist('debt_collections'))
            ->with($request->withNested)
            ->get();

        $responseData
            ->index('DebtCollections', $debtCollections)
            ->relations($request->withNested);
        return view('debt_collections.index', $responseData->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $responser = new FormDataResponser();
        $data = $responser->create(DebtCollection::class, 'debtCollections.store')->get();
        return view('debt_collections.form', $data);
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
            'tenant_contract_id' => 'required',
            'details' => 'nullable',
            'status' => 'required|max:255',
            'is_penalty_collected' => 'required',
            'comment' => 'nullable',
        ]);
        $validatedData = array_merge($validatedData, ['collector_id' => Auth::user()->id]);
        $debtCollection = DebtCollection::create($validatedData);

        return redirect()->route('debt_collections.show', ['id' => $debtCollection->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param DebtCollection $debtCollection
     * @return \Illuminate\Http\Response
     */
    public function edit(DebtCollection $debtCollection)
    {
        $responseData = new FormDataResponser();
        $data = $responseData->edit($debtCollection, 'debtCollections.update')->get();

        return view('debt_collections.form', $data);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param DebtCollection $debtCollection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DebtCollection $debtCollection)
    {
        $validatedData = $request->validate([
            'tenant_contract_id' => 'required',
            'details' => 'nullable',
            'status' => 'required|max:255',
            'is_penalty_collected' => 'required',
            'comment' => 'nullable',
        ]);
        $debtCollection->update($validatedData);

        return redirect()->route('debt_collections.show', ['id' => $debtCollection->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DebtCollection $debtCollection
     * @return Response
     * @throws \Exception
     */
    public function destroy(DebtCollection $debtCollection)
    {
        $debtCollection->delete();
        return response()->json(true);
    }
}
