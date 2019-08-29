<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Responser\NestedRelationResponser;
use App\Services\MonthlyReportService;
use App\Building;
use App\LandlordContract;

class MonthlyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $responseData = new NestedRelationResponser();
        $whitelist = $this->whitelist('buildings');
        $buildings = Building::select($whitelist)->select('buildings.*')
            ->join('landlord_contracts', 'landlord_contracts.building_id', '=', 'buildings.id')
            ->where('landlord_contracts.commission_start_date', '<', Carbon::today())
            ->where('landlord_contracts.commission_end_date', '>', Carbon::today())
            ->groupBy('id')
            ->get();

        $responseData
            ->index('buildings',$buildings)
            ->relations($request->withNested);

        return view('monthly_reports.index', $responseData->get());    
    }
    public function show(landlordContract $landlord_contract)
    {
        $month = Input::get('month');
        $service = new MonthlyReportService();
        $data = $service->getMonthlyReport( $landlord_contract, $month ); 
        return view('monthly_reports.show')
                ->with('data', $data);
    }

}