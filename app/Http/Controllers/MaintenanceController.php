<?php

namespace App\Http\Controllers;

use App\CompanyIncome;
use App\DebtCollection;
use App\LandlordPayment;
use App\Maintenance;
use App\Room;
use App\TenantContract;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use App\Services\InvoiceService;

use App\Traits\Controllers\HandleDocumentsUpload;

class MaintenanceController extends Controller
{
    use HandleDocumentsUpload;

    public function __construct()
    {
        $this->middleware('with.prefill')->only(['create', 'edit']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groupedMaintenances = [];
        $maintenances = $this->getMaintenancesByGroup();
        foreach ($maintenances as $maintenance) {
            $status = $maintenance->status;
            if (!isset($groupedMaintances[$status])) {
                $groupedMaintances[$status] = [];
            }

            $groupedMaintenances[$status][] = $maintenance->toArray();
        }

        return view('maintenances.index', [
            'groupedMaintenances' => $groupedMaintenances
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $room = isset($request->prefill['rooms'])
            ? Room::find($request->prefill['rooms'])
            : null;
        $tenant_contract_id =
            $room && !$room->activeContracts->isEmpty()
                ? $room->activeContracts()->first()->id
                : null;

        $responseData = new FormDataResponser();
        $data = $responseData
            ->create(Maintenance::class, 'maintenances.store')
            ->get();
        $data['data']['pictures'] = [];

        return view('maintenances.form', $data)->with([
            'tenant_contract_id' => $tenant_contract_id
        ]);
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
            'tenant_contract_id' => 'required|exists:tenant_contract,id',
            'reported_at' => 'required|date',
            'commissioner_id' => 'sometimes|exists:users,id',
            'service_comment' => 'required',
            'incident_type' => 'required|max:255',
            'work_type' => 'required|max:255',
            'incident_details' => 'required',
        ]);

        $maintenance = Maintenance::create($validatedData);

        $room = TenantContract::find($validatedData['tenant_contract_id'])->room;
        // update room status if needed
        $this->updateRoomStatusIfNeeded($room, $validatedData['tenant_contract_id'], $validatedData['incident_type']);

        $this->handleDocumentsUpload($maintenance, ['picture']);

        return redirect($request->_redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Maintenance  $maintenance
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Maintenance $maintenance)
    {
        $responseData = new NestedRelationResponser();
        $data = $responseData->show($maintenance->load($request->withNested))
                            ->relations($request->withNested)
                            ->get();
        $data['documents'] = $maintenance->documents;

        return view('maintenances.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Maintenance  $maintenance
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Maintenance $maintenance)
    {
        $responseData = new FormDataResponser();
        $data = $responseData->edit($maintenance, 'maintenances.update')->get();
        $data['data']['pictures'] = $maintenance->pictures()->get();

        return view('maintenances.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Maintenance  $maintenance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        $validatedData = $request->validate([
            'tenant_contract_id' => 'required|exists:tenant_contract,id',
            'reported_at' => 'required|date',
            'commissioner_id' => 'sometimes|exists:users,id',
            'service_comment' => 'required',
            'incident_type' => 'required|max:255',
            'work_type' => 'required|max:255',
            'incident_details' => 'required',

            'closed_comment' => 'required',
            'status' => 'required|max:255',
            'number_of_times' => 'required|integer|digits_between:1,11',
            'closing_serial_number' => 'required|max:255',
            'billing_details' => 'required',
            'payment_request_serial_number' => 'required|max:255',
            'payment_request_date' => 'nullable',
            'expected_service_date' => 'nullable',
            'expected_service_time' => 'nullable',
            'dispatch_date' => 'nullable',
            'maintenance_staff_id' => 'sometimes|exists:users,id',
            'closed_date' => 'nullable',
            'cost' => 'required|integer|digits_between:1,11',
            'price' => 'required|integer|digits_between:1,11',
            'is_recorded' => 'required|boolean',
            'comment' => 'required'
        ]);
        $this->handleDocumentsUpload($maintenance, ['picture']);
        $maintenance = $maintenance->update($validatedData);

        InvoiceService::compareReceipt($maintenance, $validatedData);

        return redirect($request->_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Maintenance  $maintenance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Maintenance $maintenance)
    {
        $maintenance->status = Maintenance::STATUSES['cancel'];
        $maintenance->save();

        return response()->json(true);
    }

    public function markDone(Request $request)
    {
        $who = $request->input('who');
        $maintenanceIds = $request->input('maintenanceIds');
        $maintenancesRelation = Maintenance::where('status', '請款中')->whereIn(
            'id',
            $maintenanceIds
        );

        DB::transaction(function () use ($who, $maintenancesRelation) {
            $maintenances = $maintenancesRelation->get();
            if ($who == 'landlord') {
                $maintenancesRelation->update(['status' => '案件完成', 'afford_by' => '房東']);
                $this->createLandlordPaymentAndCompanyIncome($maintenances);
            }
            else{
                $maintenancesRelation->update(['status' => '案件完成', 'afford_by' => '公司']);
            }
        });
        return response()->json(true);
    }
    public function showRecord(Request $request)
    {
        $id = $request->input('id');
        $room = Maintenance::find($id)->tenantContract->room;
        $maintenances = [];

        $threeMonthsAgo = Carbon::now()->subMonth(3);

        foreach ($room->tenantContracts as $contractKey => $contract) {
            $maintenances = array_merge(
                $maintenances,
                $contract->maintenances
                    ->where('payment_request_date', '>', $threeMonthsAgo)
                    ->toArray()
            );
        }

        return response()->json($maintenances);
    }

    private function createLandlordPaymentAndCompanyIncome($maintenances)
    {
        foreach ($maintenances as $maintenance) {
            if ($maintenance->incomeAmount() == 0) {
                return;
            }

            $this->createCompanyIncome($maintenance);
            $this->createLandlordPayment($maintenance);
        }
    }

    /**
     * @return mixed
     */
    private function getMaintenancesByGroup()
    {
        $user = Auth::user();
        $selectColumns = array_merge(['maintenances.*'], DebtCollection::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));

        if ($user->belongsToGroup('管理組')) {
            return Maintenance::withExtraInfo()->select($selectStr)->get();
        } elseif ($user->belongsToGroup('帳務組')) {
//            $threeMonthsAgo = Carbon::now()->subMonth(3);
//            $threeMonthsFromNow = Carbon::now()->addMonth(3);

            $maintenances = Maintenance::withExtraInfo()
                ->select($selectStr)
                ->whereIn('status', ['案件完成', '請款中'])
                ->get();

            return $maintenances;
        }

        return [];
    }

    /**
     * @param $maintenance
     */
    private function createCompanyIncome($maintenance): void
    {
        $companyIncome = new CompanyIncome();
        $companyIncome->tenant_contract_id = $maintenance->tenant_contract_id;
        $companyIncome->subject = "Maintenance #{$maintenance->id}";
        $companyIncome->income_date = Carbon::now();
        $companyIncome->amount = $maintenance->incomeAmount();
        $companyIncome->comment =
            'Auto-generated by MaintenanceController#markDone';
        $companyIncome->save();
    }

    /**
     * @param $maintenance
     */
    private function createLandlordPayment($maintenance): void
    {
        $landlordPayment = new LandlordPayment();
        $landlordPayment->room_id = $maintenance
            ->tenantContract()
            ->first()->room_id;
        $landlordPayment->collection_date = Carbon::now();
        $landlordPayment->billing_vendor = 'CCHOUSE';
        $landlordPayment->bill_serial_number =
            $maintenance->payment_request_serial_number;
        $landlordPayment->subject = "維修案件 #{$maintenance->id}";
        $landlordPayment->amount = $maintenance->price;
        $landlordPayment->comment =
            '系統產生';
        $landlordPayment->save();
    }

    private function updateRoomStatusIfNeeded($room, $tenant_contract_id, $incident_type)
    {
        if (! is_null($tenant_contract_id) && ! is_null($incident_type)) {
            if ($room->room_status === '待出租') {
                $room->room_status = $incident_type === '清潔'
                    ? '空屋清潔'
                    : '空屋維修';
                $room->save();
            }
        }
    }
}
