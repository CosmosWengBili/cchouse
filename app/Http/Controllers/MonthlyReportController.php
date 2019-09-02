<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Responser\NestedRelationResponser;
use App\Services\MonthlyReportService;
use App\Building;
use App\LandlordContract;
use App\LandlordOtherSubject;

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
            ->where('landlord_contracts.is_collected_by_third_party', true)
            ->groupBy('id')
            ->get();

        $responseData
            ->index('buildings',$buildings)
            ->relations($request->withNested);

        return view('monthly_reports.index', $responseData->get());    
    }
    public function show(building $building)
    { 
        $month = Input::get('month') ?? Carbon::now()->month;

        // set object which would be used on blade date data
        $report_used_date = [
            'month' => $month,
            'year' => Input::get('year') ?? Carbon::now()->year,
            'next_month' => ( $month == 12 )? 1 : $month + 1,
        ];

        // generate the month before, current, and the month after data
        // eg: current 2019/08, generate 2019/07, 2019/08, 2019/09
        $month_counter = Carbon::now()->subMonth();
        $month_options = [];
        for( $k=0; $k < 3; $k ++ ){
            $month_options[]=[
                'year' => $month_counter->year,
                'month' => $month_counter->month,
            ];
            $month_counter->addMonth(); 
        }

        $public_room = $building->publicRoom();

        // call service to generate data
        $service = new MonthlyReportService();
        $landlord_contract = $building->activeContracts();
        $data = $service->getMonthlyReport( $landlord_contract, $report_used_date['month'], $report_used_date['year']); 
        return view('monthly_reports.show')
                ->with('data', $data)
                ->with('month_options', $month_options)
                ->with('report_used_date', $report_used_date)
                ->with('public_room', $public_room);
    }

    public function storeOtherSubjects(building $building, Request $request){
        $landlord_other_subjects = Input::get('data');
        $delete_ids = Input::get('deleteIds');

        foreach( $landlord_other_subjects as $landlord_other_subject){
            if( isset($landlord_other_subject['subject'])){
                LandlordOtherSubject::create([
                    'subject' => $landlord_other_subject['subject'],
                    'subject_type' => '月結單',
                    'income_or_expense' => ($landlord_other_subject['income'] != "") ? '收入' : '支出',
                    'expense_date' => $landlord_other_subject['date'],
                    'amount' => ($landlord_other_subject['income'] != "") ? $landlord_other_subject['income'] : $landlord_other_subject['expense'],
                    'room_id' => $building->publicRoom()->id
                ]);
            }
        }
        LandlordOtherSubject::destroy($delete_ids);
    }

    public function print(building $building){

        $month = Input::get('month');

        // set object which would be used on blade date data
        $report_used_date = [
            'month' => $month,
            'year' => Input::get('year'),
            'next_month' => ( $month == 12 )? 1 : $month + 1,
        ];

        // call service to generate data
        $service = new MonthlyReportService();
        $landlord_contract = $building->activeContracts();
        $data = $service->getMonthlyReport( $landlord_contract, $report_used_date['month'], $report_used_date['year']);
        $data['report_used_date'] = $report_used_date;
        $pdf_data = [
            'data' => $data
        ];

        $pdf = PDF::loadView('monthly_reports.pdf', $pdf_data);  
        return $pdf->download($report_used_date['year'].$report_used_date['month'].'_'.$building->title.'月結單.pdf');        
    }

}