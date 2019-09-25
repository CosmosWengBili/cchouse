<?php

namespace App\Http\Controllers;

use App\ContactInfo;
use App\Landlord;
use App\LandlordAgent;
use App\LandlordContract;
use App\Responser\FormDataResponser;
use App\Traits\Controllers\HandleDocumentsUpload;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LandlordFastController extends Controller
{
    use HandleDocumentsUpload;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            ->create(Landlord::class, 'landlordFast.store')
            ->get();
        $data['data']['third_party_files'] = [];
        $data['data']['original_files'] = [];
        $data['data']['agents'] = [];
        $data['data']['contact_infos'] = [];

//        dd($data);
        return view('landlord_fast.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedLandlordContracts = $request->validate([
            'building_id.*' => 'required|exists:buildings,id',
            'commissioner_id.*' => 'required|exists:users,id',
            'commission_type.*' => 'required|max:255',
            'commission_start_date.*' => 'required|date',
            'commission_end_date.*' => 'required|date',
            'warranty_start_date.*' => 'required|date',
            'warranty_end_date.*' => 'required|date',
            'rental_decoration_free_start_date.*' => 'required|date',
            'rental_decoration_free_end_date.*' => 'required|date',
            'annual_service_fee_month_count.*' =>
                'required|integer|digits_between:1,11',
            'charter_fee.*' => 'required|min:0',
            'taxable_charter_fee.*' => 'required|integer|digits_between:1,11',
            'agency_service_fee.*' => 'required',
            'rent_collection_frequency.*' => 'required|max:255',
            'rent_collection_time.*' => 'required|integer|digits_between:1,11',
            'rent_adjusted_date.*' => 'required|date',
            'adjust_ratio.*' => 'required|numeric|min:0',
            'deposit_month_count.*' => 'required|integer|digits_between:1,11',
            'is_collected_by_third_party.*' => 'required|boolean',
            'is_notarized.*' => 'required',
            'can_keep_pets.*' => 'required|boolean',
            'gender_limit.*' => [
                'required',
                Rule::in(config('enums.landlord_contracts.gender_limit'))
            ],
        ]);
        $validatedLandlords = $request->validate([
            'name.*' => 'required|max:255',
            'certificate_number.*' => 'required',
            'is_legal_person.*' => 'required|boolean',
            'is_collected_by_third_party.*' => 'required|boolean',

            'birth.*' => 'present',
            'note.*' => 'present',
            'bank_code.*' => 'present|digits:3',
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function inputArrayToRow($inputArray)
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
