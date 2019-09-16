<?php

namespace App\Http\Controllers;

use App\Audit;
use Illuminate\Http\Request;
use App\Responser\NestedRelationResponser;

class AuditController extends Controller
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
            ->index(
                'Audits',
                $this->limitRecords(
                    Audit::select($this->whitelist('audits'))
                    ->with($request->withNested)
                )
            )
            ->relations($request->withNested);

        return view('audit.index', $responseData->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Audit  $audit
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Audit $audit)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($audit->load($request->withNested))
            ->relations($request->withNested);

        return view('audit.show', $responseData->get());
    }
}
