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

        return redirect()->route('debtCollections.index');
    }
}
