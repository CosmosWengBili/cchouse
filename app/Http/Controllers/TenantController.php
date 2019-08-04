<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Responser\NestedRelationResponser;
use App\Tenant;

class TenantController extends Controller
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
        $tenants = Tenant::select($this->whitelist('tenants'))->with($request->withNested)->get();
        $responseData->index('Tenants',$tenants)->relations($request->withNested);

        return view('tenants.index', $responseData->get());
    }

    /**
     * Display the specified resource.
     *
     * @param Tenant $tenant
     * @return Response
     */
    public function show(Tenant $tenant)
    {
        $with = ['emergencyContacts', 'guarantors'];
        $responseData = new NestedRelationResponser();
        $responseData->show($tenant->load($with))->relations($with);

        return view('tenants.show', $responseData->get());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tenant $tenant
     * @return Response
     * @throws Exception
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return response()->json(true);
    }
}
