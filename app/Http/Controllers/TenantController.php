<?php

namespace App\Http\Controllers;

use App\Responser\NestedRelationResponser;
use Illuminate\Http\Request;
use App\Tenant;

class TenantController extends Controller
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
        $tenants = Tenant::select($this->whitelist('tenants'))->with($request->withNested)->get();
        $responseData->index('Tenants',$tenants)->relations($request->withNested);

        return view('tenants.index', $responseData->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Tenant $tenant)
    {
        $with = ['emergencyContacts', 'guarantors'];
        $responseData = new NestedRelationResponser();
        $responseData->show($tenant->load($with))->relations($with);

        return view('tenants.show', $responseData->get());
    }

}
