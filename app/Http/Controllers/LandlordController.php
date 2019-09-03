<?php

namespace App\Http\Controllers;

use App\Landlord;
use App\LandlordAgent;
use App\ContactInfo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

use App\Services\LandlordService;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;

use App\Services\NestedToAttributeService;

use App\Traits\Controllers\HandleDocumentsUpload;

class LandlordController extends Controller
{
    use HandleDocumentsUpload;
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $responseData = new NestedRelationResponser();

        $landlords = Landlord::select($this->whitelist('landlords'))
            ->with($request->withNested)
            ->get();
        $landlords = NestedToAttributeService::contactInfoToAttribute(
            $landlords
        );

        $responseData
            ->index('landlords', $landlords)
            ->relations($request->withNested);

        return view('landlords.index', $responseData->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $responseData = new FormDataResponser();
        $data = $responseData
            ->create(Landlord::class, 'landlords.store')
            ->get();
        $data['data']['third_party_files'] = [];
        $data['data']['agents'] = [];
        $data['data']['contact_infos'] = [];
        return view('landlords.form', $data);
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
            'certificate_number' => 'required',
            'birth' => 'required',
            'note' => 'nullable',
            'is_legal_person' => 'required|boolean',
            'is_collected_by_third_party' => 'required|boolean',
            'bank_code' => 'required|integer|digits_between:1,11',
            'branch_code' => 'required|integer|digits_between:1,11',
            'account_name' => 'required|max:255',
            'account_number' => 'required|max:255',
            'invoice_collection_method' => 'required|max:255',
            'invoice_collection_number' => 'required|max:255',
            'invoice_mailing_address' => 'required|max:255',
        ]);

        $landlord = Landlord::create($validatedData);

        $this->handleDocumentsUpload($landlord, ['third_party_file']);
        $this->updateAgents($landlord, [
            'agents' => is_array($request->input('agents'))
                ? $request->input('agents')
                : []
        ]);
        $this->updateContactInfos($landlord, [
            'contact_infos' => is_array($request->input('contact_infos'))
                ? $request->input('contact_infos')
                : []
        ]);
        $this->createLandlordContractRelation(
            $landlord,
            $request->input('landlord_contract_id', null)
        );

        return redirect($request->_redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Landlord  $landlord
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Landlord $landlord)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($landlord->load($request->withNested))
            ->relations($request->withNested);

        return view('landlords.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Landlord  $landlord
     * @return \Illuminate\Http\Response
     */
    public function edit(Landlord $landlord)
    {
        $responseData = new FormDataResponser();
        $data = $responseData->edit($landlord, 'landlords.update')->get();
        $data['data'][
            'third_party_files'
        ] = $landlord->thirdPartyDocuments()->get();
        $data['data']['agents'] = $landlord
            ->agents()
            ->get()
            ->toArray();
        $data['data']['contact_infos'] = $landlord
            ->contactInfos()
            ->get()
            ->toArray();
        return view('landlords.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Landlord  $landlord
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Landlord $landlord)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|max:255',
            'certificate_number' => 'required',
            'birth' => 'required',
            'note' => 'nullable',
            'is_legal_person' => 'required|boolean',
            'is_collected_by_third_party' => 'required|boolean',
            'bank_code' => 'required|integer|digits_between:1,11',
            'branch_code' => 'required|integer|digits_between:1,11',
            'account_name' => 'required|max:255',
            'account_number' => 'required|max:255',
            'invoice_collection_method' => 'required|max:255',
            'invoice_collection_number' => 'required|max:255',
            'invoice_mailing_address' => 'required|max:255'
        ]);

        LandlordService::update($landlord, $validatedData);

        $this->handleDocumentsUpload($landlord, ['third_party_file']);

        $this->updateAgents($landlord, [
            'agents' => is_array($request->input('agents'))
                ? $request->input('agents')
                : []
        ]);
        $this->updateContactInfos($landlord, [
            'contact_infos' => is_array($request->input('contact_infos'))
                ? $request->input('contact_infos')
                : []
        ]);

        return redirect($request->_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Landlord  $landlord
     * @return \Illuminate\Http\Response
     */
    public function destroy(Landlord $landlord)
    {
        $landlord->delete();
        return response()->json(true);
    }
    private function updateAgents(Landlord $landlord, array $agents)
    {
        foreach ($agents as $type => $agentCollection) {
            $keepIds = array_map(function ($agent) {
                return isset($agent['id']) ? $agent['id'] : null;
            }, $agentCollection);

            // remove removed item
            $landlord
                ->agents()
                ->whereNotIn('id', $keepIds)
                ->delete();

            foreach ($agentCollection as $agent) {
                if (isset($agent['id'])) {
                    $id = $agent['id'];
                    $data = $landlord->agents()->find($id);
                    $data->update($agent);
                } else {
                    LandlordAgent::create(
                        array_merge($agent, [
                            'landlord_id' => $landlord->id
                        ])
                    );
                }
            }
        }
    }
    private function updateContactInfos(Landlord $landlord, array $contactInfos)
    {
        foreach ($contactInfos as $type => $contactInfoCollection) {
            $keepIds = array_map(function ($contactInfo) {
                return isset($contactInfo['id']) ? $contactInfo['id'] : null;
            }, $contactInfoCollection);

            // remove removed item
            $landlord
                ->contactInfos()
                ->where('contactable_type', $type)
                ->whereNotIn('id', $keepIds)
                ->delete();

            foreach ($contactInfoCollection as $contactInfo) {
                if (isset($contactInfo['id'])) {
                    $id = $contactInfo['id'];
                    $data = $landlord->contactInfos()->find($id);
                    $data->update($contactInfo);
                } else {
                    ContactInfo::create(
                        array_merge($contactInfo, [
                            'contactable_type' => Landlord::class,
                            'contactable_id' => $landlord->id
                        ])
                    );
                }
            }
        }
    }

    /**
     * create relation to landlord and landlordContact
     *
     * @param Landlord $landlord
     * @param $landlordContactId
     */
    private function createLandlordContractRelation(Landlord $landlord, $landlordContactId)
    {
        if (! is_null($landlordContactId)) {
            $landlordContactId = array_wrap($landlordContactId);
            $landlord->landlordContracts()->sync($landlordContactId);
        }
    }

}
