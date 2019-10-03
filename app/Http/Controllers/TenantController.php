<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use App\Tenant;
use App\ContactInfo;
use App\RelatedPerson;

use App\Services\NestedToAttributeService;

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
        $tenants = $this->limitRecords(
            Tenant::select($this->whitelist('tenants'))
                ->with($request->withNested)
        );

        $tenants = NestedToAttributeService::contactInfoToAttribute($tenants);

        $responseData
            ->index('Tenants', $tenants)
            ->relations($request->withNested);
        return view('tenants.index', $responseData->get());
    }

    /**
     * Display the specified resource.
     *
     * @param Tenant $tenant
     * @return Response
     */
    public function show(Request $request, Tenant $tenant)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($tenant->load($request->withNested))
            ->relations($request->withNested);

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
        $data['data']['contact_infos'] = [];
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
        $data['data']['contact_infos'] = $tenant
            ->contactInfos()
            ->get()
            ->toArray();
        $data['data']['emergency_contacts'] = $tenant
            ->emergencyContacts()
            ->get()
            ->toArray();
        $data['data']['guarantors'] = $tenant
            ->guarantors()
            ->get()
            ->toArray();

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
            'birth' => 'required|date',
            'confirm_by' => 'required|min:1',
            'confirm_at' => 'required|date',
        ]);
        $tenant = Tenant::create($validatedData);

        $this->updateContactInfos($tenant, [
            'contact_infos' => is_array($request->input('contact_infos'))
                ? $request->input('contact_infos')
                : []
        ]);
        $this->updateRelatedPeople($tenant, [
            'emergency_contact' => is_array(
                $request->input('emergency_contact')
            )
                ? $request->input('emergency_contact')
                : [],
            'guarantor' => is_array($request->input('guarantor'))
                ? $request->input('guarantor')
                : []
        ]);

        return redirect()->route('tenants.index');
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
            'confirm_by' => 'required|min:1',
            'confirm_at' => 'required|date',
            'birth' => 'required|date'
        ]);
        $tenant->update($validatedData);
        $this->updateRelatedPeople($tenant, [
            'emergency_contact' => is_array(
                $request->input('emergency_contact')
            )
                ? $request->input('emergency_contact')
                : [],
            'guarantor' => is_array($request->input('guarantor'))
                ? $request->input('guarantor')
                : []
        ]);
        $this->updateContactInfos($tenant, [
            'contact_infos' => is_array($request->input('contact_infos'))
                ? $request->input('contact_infos')
                : []
        ]);

        return redirect()->route('tenants.edit', ['id' => $tenant->id]);
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

    private function updateRelatedPeople(Tenant $tenant, array $relatedPeople)
    {
        foreach ($relatedPeople as $type => $relatedPersonCollection) {
            $keepIds = array_map(function ($relatedPerson) {
                return isset($relatedPerson['id'])
                    ? $relatedPerson['id']
                    : null;
            }, $relatedPersonCollection);

            // remove removed item
            $tenant
                ->relatedPeople()
                ->where('type', $type)
                ->whereNotIn('id', $keepIds)
                ->delete();

            foreach ($relatedPersonCollection as $relatedPerson) {
                if (isset($relatedPerson['id'])) {
                    $id = $relatedPerson['id'];
                    $person = $tenant->relatedPeople()->find($id);
                    $person->update($relatedPerson);
                } else {
                    RelatedPerson::create(
                        array_merge($relatedPerson, [
                            'type' => $type,
                            'related_person_type' => Tenant::class,
                            'related_person_id' => $tenant->id
                        ])
                    );
                }
            }
        }
    }

    private function updateContactInfos(Tenant $tenant, array $contactInfos)
    {
        foreach ($contactInfos as $type => $contactInfoCollection) {
            $keepIds = array_map(function ($contactInfo) {
                return isset($contactInfo['id']) ? $contactInfo['id'] : null;
            }, $contactInfoCollection);

            // remove removed item
            $tenant
                ->contactInfos()
                ->where('contactable_type', Tenant::class)
                ->whereNotIn('id', $keepIds)
                ->delete();

            foreach ($contactInfoCollection as $contactInfo) {
                if (isset($contactInfo['id'])) {
                    $id = $contactInfo['id'];
                    $data = $tenant->contactInfos()->find($id);
                    $data->update($contactInfo);
                } else {
                    ContactInfo::create(
                        array_merge($contactInfo, [
                            'contactable_type' => Tenant::class,
                            'contactable_id' => $tenant->id
                        ])
                    );
                }
            }
        }
    }
}
