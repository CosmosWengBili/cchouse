<?php

namespace App\Http\Controllers;

use App\CompanyIncome;
use App\DebtCollection;
use App\LandlordPayment;
use App\Maintenance;
use App\Room;
use App\Notifications\TextNotify;
use App\TenantContract;
use App\User;
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
            if (! isset($groupedMaintenances[$status])) {
                $groupedMaintenances[$status] = [];
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

        $responseData = new FormDataResponser();
        $data = $responseData
            ->create(Maintenance::class, 'maintenances.store')
            ->get();
        $data['data']['pictures'] = [];

        return view('maintenances.form', $data)->with([
            'room' => $room
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
            'room_id' => 'required|exists:rooms,id',
            'reported_at' => 'required|date',
            'commissioner_id' => 'sometimes|exists:users,id',
            'service_comment' => 'required',
            'incident_type' => 'required|max:255',
            'work_type' => 'required|max:255',
            'incident_details' => 'required',
        ]);

        $maintenance = Maintenance::create($validatedData);

        $room = Room::find($validatedData['room_id']);
        // update room status if needed
        $this->updateRoomStatusIfNeeded($room, $validatedData['incident_type']);

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

        if ($request->old()) {
            $data['data'] = array_merge($data['data'], $request->old());
        }

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
            'room_id' => 'required|exists:rooms,id',
            'reported_at' => 'required|date',
            'commissioner_id' => 'sometimes|exists:users,id',
            'service_comment' => 'required',
            // 'incident_type' => 'required|max:255',
            // 'work_type' => 'required|max:255',
            'incident_details' => 'required',

            'closed_comment' => 'nullable',
            'status' => 'required|max:255',
            'number_of_times' => 'required|integer|digits_between:1,11',
            'closing_serial_number' => 'nullable',
            'billing_details' => 'nullable',
            'payment_request_serial_number' => 'nullable',
            'payment_request_date' => 'nullable',
            'expected_service_date' => 'nullable',
            'expected_service_time' => 'nullable',
            'dispatch_date' => 'nullable',
            'maintenance_staff_id' => 'sometimes|exists:users,id',
            'closed_date' => 'nullable',
            'cost' => 'required|integer|digits_between:1,11',
            'price' => 'required|integer|digits_between:1,11',
            'is_recorded' => 'required|boolean',
            'comment' => 'nullable',
            'is_printed' => 'required',
        ]);
        $this->handleDocumentsUpload($maintenance, ['picture']);

        $result = InvoiceService::compareReceipt($maintenance, $validatedData);
        if (! $result) {
            $maintenance->update($validatedData);
        }

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

        $user = $request->user();
        $userInAccountGroup = $user->groups()->where('name', '帳務組')->count();

        DB::transaction(function () use ($who, $maintenancesRelation, $maintenanceIds, $userInAccountGroup) {
            $maintenances = $maintenancesRelation->get();
            if ($who === 'landlord') {
                $maintenancesRelation->update(['status' => '案件完成', 'afford_by' => '房東']);
                $this->createCompanyIncome($maintenances);
            } else {
                $maintenancesRelation->update(['status' => '案件完成', 'afford_by' => '公司']);
            }

            // 帳務組審核通過的資料，要能通知管理組，『維修清潔編號{id}已審核完畢』
            if ($userInAccountGroup) {
                User::group('管理組')->get()->each(function (User $user, $key) use ($maintenanceIds) {
                    $strIds = implode(',', $maintenanceIds);
                    $user->notify(
                        new TextNotify("維修清潔編號 {$strIds} 已審核完畢")
                    );
                });
            }
        });

        return response()->json(true);
    }

    /**
     * 根據 $id 找出 近三個月內的維護數據
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showRecord($id)
    {
        $room = Maintenance::find($id)->room;

        $columns = array_map(function ($column) {
            return "maintenances.{$column}";
        }, $this->whitelist('maintenances'));
        $selectColumns = array_merge($columns, Maintenance::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));

        $threeMonthsAgo = Carbon::now()->subMonth(3);
        $records = $room->maintenances()
                            ->withExtraInfo()
                            ->select($selectStr)
                            ->where('payment_request_date', '>', $threeMonthsAgo)
                            ->where('status', '案件完成')
                            ->get()
                            ->toArray();

        return response()->json($records);
    }

    /**
     * 檢查三個月內是否有同工種
     * true: 沒有同工種
     * false: 有同工種
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkHasSameWorkType(Request $request)
    {
        $request->validate([
            'room_id' => 'required',
            'work_type' => 'required',
        ]);

        $room_id = $request->input('room_id');
        $work_type = $request->input('work_type');
        $threeMonthsAgo = Carbon::now()->subMonth(3);

        $maintenance = Maintenance::where('room_id', $room_id)
            ->where('payment_request_date', '>', $threeMonthsAgo)
            ->where('work_type', $work_type)
            ->first();

        return is_null($maintenance)
            ? response()->json(true)
            : response()->json(false);
    }

    /**
     * 更新案件完成的確認已列印
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateIsPrinted(Request $request)
    {
        $maintenance_ids = $request->input('maintenance_ids');

        $user = $request->user();
        $userInGroup = $user->groups()->where('name', '管理組')->count();

        $successful = false;
        if ($userInGroup) {
            // 更新為已列印
            $successful = Maintenance::whereIn('id', $maintenance_ids)
                // 一定要是案件完成的 id 才能更新
                ->where('status', '案件完成')
                ->update([
                    'is_printed' => 1,
                ]);
        }

        return $successful
            ? response()->json(true)
            : response()->json(false);
    }

    private function createCompanyIncome($maintenances)
    {
        foreach ($maintenances as $maintenance) {
            if ($maintenance->incomeAmount() == 0) {
                return;
            }
            $companyIncome = new CompanyIncome();
            $companyIncome->incomable_id = $maintenance->id;
            $companyIncome->incomable_type = get_class($maintenance);
            $companyIncome->subject = "維修清潔編號: {$maintenance->id}";
            $companyIncome->income_date = Carbon::now();
            $companyIncome->amount = $maintenance->incomeAmount();
            $companyIncome->comment =
                '由維修清潔審核完畢自動產生';
            $companyIncome->save();
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

    private function updateRoomStatusIfNeeded($room, $incident_type)
    {
        if (! is_null($incident_type)) {
            if ($room->room_status === '待出租') {
                $room->room_status = $incident_type === '清潔'
                    ? '空屋清潔'
                    : '空屋維修';
                $room->save();
            }
        }
    }
}
