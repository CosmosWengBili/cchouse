<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $responser = new FormDataResponser();
        $responseData = $responser->create(Tenant::class, 'tenants.store')->get();

        return view('tenants.form', $responseData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function edit(Tenant $tenant)
    {
        $responseData = new FormDataResponser();
        return view('tenants.form', $responseData->edit($tenant, 'tenants.update')->get());
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
            'certificate_number' => 'required|max:255',
            'is_legal_person' => 'nullable',
            'line_id' => 'required',
            'residence_address' => 'required',
            'company' => 'required',
            'job_position' => 'required',
            'company_address' => 'required',
        ]);
        $tenant = Tenant::create($validatedData);

        return redirect()->route('tenants.show', ['id' => $tenant->id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'certificate_number' => 'required|max:255',
            'is_legal_person' => 'required',
            'line_id' => 'required',
            'residence_address' => 'required',
            'company' => 'required',
            'job_position' => 'required',
            'company_address' => 'required',
        ]);
        $tenant->update($validatedData);

        return redirect()->route('tenants.show', ['id' => $tenant->id]);
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
