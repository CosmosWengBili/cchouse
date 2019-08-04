<?php

namespace App\Http\Controllers;

use App\RelatedPerson;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use App\Tenant;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

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
        $with = [
            'emergencyContacts',
            'guarantors',
            'tenantContracts.tenantPayments.payLog',
            'tenantContracts.tenantElectricityPayments.payLog',
        ];
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
        $data = $responser->create(Tenant::class, 'tenants.store')->get();
        $data['data']['emergency_contacts'] = [];
        $data['data']['guarantors'] = [];

        return view('tenants.form', $data);
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
        $data = $responseData->edit($tenant, 'tenants.update')->get();
        $data['data']['emergency_contacts'] = $tenant->emergencyContacts()->get()->toArray();
        $data['data']['guarantors'] = $tenant->guarantors()->get()->toArray();

        return view('tenants.form', $data);
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
        $this->updateRelatedPeople($tenant, [
            'emergency_contact' => is_array($request->input('emergency_contact')) ? $request->input('emergency_contact') : [],
            'guarantor' => is_array($request->input('guarantor')) ? $request->input('guarantor') : [],
        ]);

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
        $this->updateRelatedPeople($tenant, [
            'emergency_contact' => is_array($request->input('emergency_contact')) ? $request->input('emergency_contact') : [],
            'guarantor' => is_array($request->input('guarantor')) ? $request->input('guarantor') : [],
        ]);

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

    private function updateRelatedPeople(Tenant $tenant, array $relatedPeople) {

        foreach($relatedPeople as $type => $relatedPersonCollection) {
            $keepIds = array_map(function ($relatedPerson) {
                return isset($relatedPerson['id']) ? $relatedPerson['id'] : null;
            }, $relatedPersonCollection);

            // remove removed item
            $tenant->relatedPeople()->where('type', $type)->whereNotIn('id', $keepIds)->delete();

            foreach($relatedPersonCollection as $relatedPerson) {
                if(isset($relatedPerson['id'])) {
                    $id = $relatedPerson['id'];
                    $person = $tenant->relatedPeople()->find($id);
                    $person->update($relatedPerson);
                } else {
                    RelatedPerson::create(
                        array_merge(
                            $relatedPerson,
                            [
                                'type' => $type,
                                'related_person_type' => Tenant::class,
                                'related_person_id' => $tenant->id,
                            ]
                        )
                    );
                }
            }
        }
    }
}
