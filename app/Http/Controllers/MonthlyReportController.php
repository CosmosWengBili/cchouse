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
        $month = Input::get('month') ?? Carbon::now()->month;
        $report_used_date = [
            'month' => $month,
            'year' => Input::get('year') ?? Carbon::now()->year,
            'next_month' => ( $month == 12 )? 1 : $month + 1,
        ];
        $month_counter = Carbon::now()->subMonth();
        $month_options = [];
        for( $k=0; $k < 3; $k ++ ){
            $month_options[]=[
                'year' => $month_counter->year,
                'month' => $month_counter->month,
            ];
            $month_counter->addMonth(); 
        }

        $service = new MonthlyReportService();
        $data = $service->getMonthlyReport( $landlord_contract, $report_used_date['month'], $report_used_date['year']); 
        return view('monthly_reports.show')
                ->with('data', $data)
                ->with('month_options', $month_options)
                ->with('report_used_date', $report_used_date);
    }

}