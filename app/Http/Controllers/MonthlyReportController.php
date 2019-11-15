<?php

namespace App\Http\Controllers;

use App\TenantContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use Maatwebsite\Excel\Facades\Excel;

use App\Responser\NestedRelationResponser;
use App\Services\MonthlyReportService;
use App\Exports\MonthlyTenantExport;
use App\Building;
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
        $columns      = array_map(function ($column) {
            return "buildings.{$column}";
        }, $this->whitelist('buildings'));
        $selectColumns = array_merge($columns, Building::extraInfoColumns());
        $selectStr     = DB::raw(join(', ', $selectColumns));
        $buildings     = $this->limitRecords(
            Building::withExtraInfo()
                ->select($selectStr)
                ->where('landlord_contracts.commission_start_date', '<', Carbon::today())
                ->where('landlord_contracts.commission_end_date', '>', Carbon::today())
                ->groupBy('id')
        );

        $responseData
            ->index('buildings', $buildings)
            ->relations($request->withNested);

        return view('monthly_reports.index', $responseData->get());
    }
    public function show(building $building)
    {
        $month = Input::get('month') ?? Carbon::now()->month;
        $year  = Input::get('year') ?? Carbon::now()->year;

        // set object which would be used on blade date data
        $report_used_date = [
            'month'      => $month,
            'year'       => $year,
            'next_month' => ($month == 12) ? 1 : $month + 1,
        ];

        // generate the month before, current, and the month after data
        // eg: current 2019/08, generate 2019/07, 2019/08, 2019/09
        $month_counter = Carbon::now()->subMonth();
        $month_options = [];
        for ($k = 0; $k < 3; $k ++) {
            $month_options[] = [
                'year'  => $month_counter->year,
                'month' => $month_counter->month,
            ];
            $month_counter->addMonth();
        }

        // call service to generate data
        $service                     = new MonthlyReportService();
        $landlord_contract           = $building->activeContracts();
        $monthly_data                = $service->getMonthlyReport($landlord_contract, $report_used_date['month'], $report_used_date['year']);
        $eletricity_data             = $service->getEletricityReport($landlord_contract, $report_used_date['month'], $report_used_date['year']);
        $monthly_data['building_id'] = $building->id;

        // get tenant

        $building_lazy_load  = $building->load(['rooms' => function ($query) {
            $query->where('room_layout', '!=', '公區');
        }]);

        $MonthlyTenantExport = new MonthlyTenantExport($building_lazy_load, $year, $month);
        // dd($MonthlyTenantExport->headings(), $MonthlyTenantExport->collection());

        return view('monthly_reports.show')
                ->with('data', $monthly_data)
                ->with('eletricity_data', $eletricity_data)
                ->with('month_options', $month_options)
                ->with('tenant_date', $MonthlyTenantExport)
                ->with('report_used_date', $report_used_date)
                ->with('file_name', $report_used_date['year'].$report_used_date['month'].'_'.$building->title.'月結單.pdf');
    }

    public function print(building $building)
    {
        $month = Input::get('month');

        // set object which would be used on blade date data
        $report_used_date = [
            'month'      => $month,
            'year'       => Input::get('year'),
            'next_month' => ($month == 12) ? 1 : $month + 1,
        ];

        // call service to generate data
        $service           = new MonthlyReportService();
        $landlord_contract = $building->activeContracts();
        $data              = $service->getMonthlyReport($landlord_contract, $report_used_date['month'], $report_used_date['year']);
        $eletricity_data   = $service->getEletricityReport($landlord_contract, $report_used_date['month'], $report_used_date['year']);

        $data['report_used_date'] = $report_used_date;

        return view('monthly_reports.pdf')
                ->with('data', $data)
                ->with('eletricity_data', $eletricity_data)
                ->with('file_name', $report_used_date['year'].$report_used_date['month'].'_'.$building->title.'月結單.pdf');
    }

    public function print_tenant(building $building)
    {
        $month               = Input::get('month');
        $year                = Input::get('year');
        $building_lazy_load  = $building->load(['rooms']);
        $MonthlyTenantExport = new MonthlyTenantExport($building_lazy_load, $year, $month);

        return Excel::download(
            $MonthlyTenantExport,
            $MonthlyTenantExport->getFileName()
        );
    }
}
