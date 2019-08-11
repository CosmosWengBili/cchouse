<?php

namespace App\Http\Controllers;

use App\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Room;

use App\Responser\FormDataResponser;

class MaintenanceController extends Controller
{

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
            $workType = $maintenance->work_type;
            if (!isset($groupedMaintances[$status])) { $groupedMaintances[$status] = []; }
            if(!isset($groupedMaintances[$status][$workType])) { $groupedMaintances[$status][$workType] = []; }

            $groupedMaintenances[$status][$workType][] = $maintenance->toArray();
        }

        return view('maintenances.index', [
            'groupedMaintenances' => $groupedMaintenances,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $room = isset($request->prefill['rooms']) ? Room::find($request->prefill['rooms']) : null;
        $tenant_contract_id = ($room && !$room->activeContracts->isEmpty()) ? $room->activeContracts()->first()->id : null;

        $responseData = new FormDataResponser();
        return view('maintenances.form', $responseData->create(Maintenance::class, 'maintenances.store')->get())
                    ->with(['tenant_contract_id' => $tenant_contract_id ]);
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
            'closed_comment' => 'required',
            'service_comment' => 'required',
            'status' => 'required|max:255',
            'incident_details' => 'required',
            'incident_type' => 'required|max:255',
            'work_type' => 'required|max:255',
            'number_of_times' => 'required|integer|digits_between:1,11',
            'closing_serial_number' => 'required|max:255',
            'billing_details' => 'required',
            'payment_request_serial_number' => 'required|max:255',
            'cost' => 'required|integer|digits_between:1,11',
            'price' => 'required|integer|digits_between:1,11',
            'is_recorded' => 'required|boolean',
            'invoice_serail_number' => 'required|max:255',
            'comment' => 'required',
        ]);

        $maintenance = Maintenance::create($validatedData);

        return redirect()->route('maintenances.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Maintenance  $maintenance
     * @return \Illuminate\Http\Response
     */
    public function show(Maintenance $maintenance)
    {
        //
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
        return view('maintenances.form', $responseData->edit($maintenance, 'maintenances.update')->get());
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
            'closed_comment' => 'required',
            'service_comment' => 'required',
            'status' => 'required|max:255',
            'incident_details' => 'required',
            'incident_type' => 'required|max:255',
            'work_type' => 'required|max:255',
            'number_of_times' => 'required|integer|digits_between:1,11',
            'closing_serial_number' => 'required|max:255',
            'billing_details' => 'required',
            'payment_request_serial_number' => 'required|max:255',
            'cost' => 'required|integer|digits_between:1,11',
            'price' => 'required|integer|digits_between:1,11',
            'is_recorded' => 'required|boolean',
            'invoice_serail_number' => 'required|max:255',
            'comment' => 'required',
        ]);
        $maintenance = $maintenance->update($validatedData);

        return redirect()->route('maintenances.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Maintenance  $maintenance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Maintenance $maintenance)
    {
        //
    }

    /**
     * @return mixed
     */
    private function getMaintenancesByGroup()
    {
        $user = Auth::user();

        if ($user->belongsToGroup('帳務組')) {
            $threeMonthsAgo = Carbon::now()->subMonth(3);
            $threeMonthsFromNow = Carbon::now()->addMonth(3);

            $doneMaintenances = Maintenance::where('status', 'done')->get();
            $requestMaintenances = Maintenance::where('status', 'request')
                ->whereBetween('payment_request_date', [$threeMonthsAgo, $threeMonthsFromNow])
                ->get();

            return $doneMaintenances->merge($requestMaintenances);
        } else if ($user->belongsToGroup('管理組')){
            return Maintenance::all();
        }

        return [];
    }
}
