<?php

namespace App\Http\Controllers;

use App\DebtCollection;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Responser\NestedRelationResponser;
use App\Services\NestedToAttributeService;

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
}
