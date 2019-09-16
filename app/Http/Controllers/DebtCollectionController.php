<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\DebtCollection;
use App\Tenant;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use App\Services\ReceiptService;

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
        $owner_data = new NestedRelationResponser();

        $debtCollections = $this->limitRecords(
            DebtCollection::select($this->whitelist('debt_collections'))
                ->with($request->withNested)
        );

        $responseData
            ->index('DebtCollections', $debtCollections)
            ->relations($request->withNested);

        $owner_query = $this->limitRecords(
            DebtCollection::select($this->whitelist('debt_collections'))
                ->where(['collector_id' => Auth::id()])
                ->with($request->withNested),
            false
        );

        $owner_data
            ->index(
                'DebtCollections',
                $owner_query->get()
            )
            ->relations($request->withNested);

        return view('debt_collections.index', $responseData->get())->with(
            'owner_data',
            $owner_data->get()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param DebtCollection $debtCollection
     * @return Response
     */
    public function show(Request $request, DebtCollection $debtCollection)
    {
        $responser = new NestedRelationResponser();
        $responser
            ->show($debtCollection->load($request->withNested))
            ->relations($request->withNested);
        $data = $responser->get();

        return view('debt_collections.show', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $responser = new FormDataResponser();
        $data = $responser
            ->create(DebtCollection::class, 'debtCollections.store')
            ->get();
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
            'collector_id' => 'nullable'
        ]);
        $debtCollection = DebtCollection::create($validatedData);

        return redirect($request->_redirect);
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
        $data = $responseData
            ->edit($debtCollection, 'debtCollections.update')
            ->get();

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
            'collector_id' => 'nullable'
        ]);
        ReceiptService::compareReceipt($debtCollection, $validatedData);
        $debtCollection->update($validatedData);

        return redirect($request->_redirect);
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
