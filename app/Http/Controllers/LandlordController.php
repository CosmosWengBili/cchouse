<?php

namespace App\Http\Controllers;

use App\Landlord;
use App\LandlordAgent;
use App\ContactInfo;
use App\LandlordContract;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;

use App\Services\NestedToAttributeService;

use App\Traits\Controllers\HandleDocumentsUpload;
use Illuminate\Validation\Rule;

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

        $landlords = $this->limitRecords(
            Landlord::select($this->whitelist('landlords'))
                ->with($request->withNested)
        );

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
            'is_legal_person' => 'required|boolean',
            'is_collected_by_third_party' => 'required|boolean',
            'birth' => 'present',
            'note' => 'present',
            'bank_code' => 'present',
            'branch_code' => 'present',
            'account_name' => 'present|max:255',
            'account_number' => 'present|max:255',
            'invoice_collection_method' => 'present|max:255',
            'invoice_collection_number' => 'present',
            'invoice_mailing_address' => 'present|max:255',
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
            'name' => 'required|max:255',
            'certificate_number' => 'required',
            'is_legal_person' => 'required|boolean',
            'is_collected_by_third_party' => 'required|boolean',

            'birth' => 'present',
            'note' => 'present',
            'bank_code' => 'present|digits:3',
            'branch_code' => 'present',
            'account_name' => 'present|max:255',
            'account_number' => 'present|max:255',
            'invoice_collection_method' => 'present|max:255',
            'invoice_collection_number' => 'present',
            'invoice_mailing_address' => 'present|max:255',
        ]);

        $landlord->update($validatedData);

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

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createMulti()
    {
        $responseData = new FormDataResponser();
        $data = $responseData
            ->create(Landlord::class, 'landlordMulti.store')
            ->get();
        $data['data']['third_party_files'] = [];
        $data['data']['original_files'] = [];
        $data['data']['agents'] = [];
        $data['data']['contact_infos'] = [];

        return view('landlord_fast.form', $data);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeMulti(Request $request)
    {
        $validatedLandlordContracts = $request->validate([
            'building_id.*' => 'required|exists:buildings,id',
            'commissioner_id.*' => 'required|exists:users,id',
            'commission_type.*' => 'required|max:255',
            'commission_start_date.*' => 'required|date',
            'commission_end_date.*' => 'required|date',
            'warranty_start_date.*' => 'nullable',
            'warranty_end_date.*' => 'nullable',
            'rental_decoration_free_start_date.*' => 'required|date',
            'rental_decoration_free_end_date.*' => 'required|date',
            'annual_service_fee_month_count.*' => 'nullable',
            'charter_fee.*' => 'required|min:0',
            'taxable_charter_fee.*' => 'required|integer|digits_between:1,11',
            'agency_service_fee.*' => 'nullable',
            'rent_collection_frequency.*' => 'required|max:255',
            'rent_collection_time.*' => 'required|integer|digits_between:1,11',
            'rent_adjusted_date.*' => 'nullable',
            'adjust_ratio.*' => 'nullable',
            'deposit_month_count.*' => 'required|integer|digits_between:1,11',
            'is_collected_by_third_party.*' => 'required|boolean',
            'is_notarized.*' => [
                'required',
                Rule::in(config('enums.landlord_contracts.is_notarized'))
            ],
            'can_keep_pets.*' => 'required|boolean',
            'gender_limit.*' => [
                'required',
                Rule::in(config('enums.landlord_contracts.gender_limit'))
            ],
            'withdrawal_revenue_distribution' => 'nullable'
        ]);
        $validatedLandlords = $request->validate([
            'name.*' => 'required|max:255',
            'certificate_number.*' => 'required',
            'is_legal_person.*' => 'required|boolean',
            'is_collected_by_third_party.*' => 'required|boolean',

            'birth.*' => 'present',
            'note.*' => 'present',
            'bank_code.*' => 'present',
            'branch_code.*' => 'present',
            'account_name.*' => 'present|max:255',
            'account_number.*' => 'present|max:255',
            'invoice_collection_method.*' => 'present|max:255',
            'invoice_collection_number.*' => 'present',
            'invoice_mailing_address.*' => 'present|max:255',
        ]);

        $landlordContractsArray = $this->inputArrayToRow($validatedLandlordContracts);
        $landlordsArray = $this->inputArrayToRow($validatedLandlords);
        $agents = $request->input('agents.*');
        $contacts = $request->input('contact_infos.*');

        if (!empty($landlordsArray)) {
            $landlords = $this->createLandlords($landlordsArray, $agents, $contacts);
        }

        if (!empty($landlordContractsArray)) {
            $landlordContracts = $this->createLandlordContracts($landlordContractsArray);
        }

        if (!empty($landlords) && !empty($landlordContracts)) {
            $landlord_ids = collect($landlords)->map->id->toArray();
            // set relations
            foreach ($landlordContracts as $landlordContract) {
                $landlordContract->landlords()->sync($landlord_ids);
            }
        }

        return redirect()->route('landlords.index');
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


    private function inputArrayToRow($inputArray)
    {
        $landlordContracts = [];
        foreach ($inputArray as $attribute => $validatedLandlordContract) {
            $index = 0;
            foreach ($validatedLandlordContract as $value) {
                $landlordContracts[$index][$attribute] = is_null($value) ? '' : $value;
                $index++;
            }
        }

        return $landlordContracts;
    }
    private function createLandlords($landlordsArray, $agents, $contacts)
    {
        /** @var Landlord[] $landlords */
        $landlords = [];
        foreach ($landlordsArray as $formIndex => $landlordArray) {
            /** @var Landlord $landlord */
            $landlords[] = $landlord = Landlord::create($landlordArray);

            // 表示 第 $formIndex 張 form 有填寫 agent
            if (isset($agents[$formIndex])) {
                $this->updateAgents($landlord, [ 'agents' => $agents[$formIndex] ]);
            }

            // 表示 第 $formIndex 張 form 有填寫 agent
            if (isset($contacts[$formIndex])) {
                $this->updateContactInfos($landlord, [ 'contact_infos' => $contacts[$formIndex] ]);
            }

            // deal with third agent files
            $this->handleMultiDocumentsUpload($landlord, ['third_party_file'], $formIndex);
        }

        return $landlords;
    }
    private function createLandlordContracts($landlordContractsArray)
    {
        /** @var LandlordContract[] $landlordContracts */
        $landlordContracts = [];
        foreach ($landlordContractsArray as $formIndex => $landlordContractArray) {
            $landlordContracts[] = $landlordContract = LandlordContract::create($landlordContractArray);

            // deal with third agent files
            $this->handleMultiDocumentsUpload($landlordContract, ['original_file'], $formIndex);
        }

        return $landlordContracts;
    }
}
