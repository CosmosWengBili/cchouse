<?php

namespace App\Http\Controllers;

use App\LandlordContract;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Validation\Rule;

use App\TenantContract;
use App\Building;
use App\Traits\Controllers\HandleDocumentsUpload;
use App\Services\TenantContractService;
use App\Services\InvoiceService;
use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;

class TenantContractController extends Controller
{
    use HandleDocumentsUpload;
    protected $tenantContractService;

    public function __construct(TenantContractService $tenantContractService)
    {
        $this->tenantContractService = $tenantContractService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $responseData = new NestedRelationResponser();
        $selectColumns = array_merge(['tenant_contract.*'], TenantContract::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));

        $responseData
            ->index(
                'tenant_contracts',
                $this->limitRecords(
                    TenantContract::withExtraInfo()->select($selectStr)->with($request->withNested)
                )
            )
            ->relations($request->withNested);

        return view('tenant_contracts.index', $responseData->get());
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
            ->create(TenantContract::class, 'tenantContracts.store')
            ->get();
        $data['data']['original_files'] = [];
        $data['data']['carrier_files'] = [];

        return view('tenant_contracts.form', $data);
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
            'tenant_id' => 'required|exists:tenants,id',
            'contract_serial_number' => 'required|max:255',
            'set_other_rights' => 'required|boolean',
            'other_rights' => 'required|max:255',
            'sealed_registered' => 'required|boolean',
            'car_parking_floor' => 'required|max:255',
            'car_parking_type' => [
                'required',
                Rule::in(config('enums.tenant_contract.car_parking_type'))
            ],
            'car_parking_space_number' => 'required|max:255',
            'motorcycle_parking_floor' => 'required|max:255',
            'motorcycle_parking_space_number' => 'required|max:255',
            'motorcycle_parking_count' =>
                'required|integer|digits_between:1,11',
            'effective' => 'required|boolean',
            'contract_start' => 'required|date',
            'contract_end' => 'required|date',
            'rent' => 'required|integer|digits_between:1,11',
            'rent_pay_day' => 'required|integer|between:1,31',
            'deposit' => 'required|integer|digits_between:1,11',
            'deposit_paid' => 'required|integer|digits_between:1,11',
            'electricity_calculate_method' => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.electricity_calculate_method')
                )
            ],
            'electricity_price_per_degree' =>
                'required|numeric|between:0,99.99',
            'electricity_price_per_degree_summer' =>
                'required|numeric|between:0,99.99',
            '110v_start_degree' => 'required|integer|digits_between:1,11|lte:110v_end_degree',
            '220v_start_degree' => 'nullable|integer|digits_between:1,11|lte:220v_end_degree',
            '110v_end_degree' => 'required|integer|digits_between:1,11',
            '220v_end_degree' => 'nullable|integer|digits_between:1,11',
            'invoice_collection_method' => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.invoice_collection_method')
                )
            ],
            'invoice_collection_number' => 'required|max:255',
            'commissioner_id' => 'exists:users,id',
            'comment' => 'present',
        ]);

        $validatedPaymentData = $request->validate([
            'payments' => 'nullable|array',
            'payments.*.subject' => [
                'required_with:payments',
                Rule::in(config('enums.tenant_payments.subject'))
            ],
            'payments.*.period' => [
                'required_with:payments',
                Rule::in(config('enums.tenant_payments.period'))
            ],
            'payments.*.amount' =>
                'required_with:payments|integer|digits_between:1,11',
            'payments.*.collected_by' => [
                'required_with:payments',
                Rule::in(config('enums.tenant_payments.collected_by'))
            ]
        ]);

        $tenantContract = $this->tenantContractService->create(
            $validatedData,
            $validatedPaymentData['payments']
        );
        $this->handleDocumentsUpload($tenantContract, [
            'original_file',
            'carrier_file'
        ]);
        return redirect($request->_redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TenantContract  $tenantContract
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, TenantContract $tenantContract)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($tenantContract->load($request->withNested))
            ->relations($request->withNested);
        $paid_diff = $tenantContract->sum_paid - $tenantContract->payLogs()->sum('amount');

        return view('tenant_contracts.show', array_merge($responseData->get(), ['paid_diff' => $paid_diff]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TenantContract  $tenantContract
     * @return \Illuminate\Http\Response
     */
    public function edit(TenantContract $tenantContract)
    {
        $responseData = new FormDataResponser();
        $data = $responseData
            ->edit($tenantContract, 'tenantContracts.update')
            ->get();
        $data['data'][
            'original_files'
        ] = $tenantContract->originalFiles()->get();
        $data['data']['carrier_files'] = $tenantContract->carrierFiles()->get();
        return view('tenant_contracts.form', $data);
    }

    /**
     * Show the form for extending a contract.
     * @param Request        $request
     * @param TenantContract $tenantContract
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function extend(Request $request, TenantContract $tenantContract)
    {
        /** @var TenantContract $tempContract */
        $tempContract = $this->tenantContractService->makeExtendedContract(
            $tenantContract
        );
        $responseData = new FormDataResponser();
        $data = $responseData
            ->edit($tempContract, 'tenantContracts.create')
            ->get();
        $data['data']['original_files'] = null;
        $data['data']['carrier_files'] =  null;

        // 租客帳單
        $data['data']['tenant_payment'] = $tenantContract->tenantPayments->toArray() or [];
//dd($data['data']['tenant_payment']);
        $data['method'] = 'POST';
        $data['action'] = route('tenantContracts.store');
        return view('tenant_contracts.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TenantContract  $tenantContract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TenantContract $tenantContract)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'tenant_id' => 'required|exists:tenants,id',
            'contract_serial_number' => 'required|max:255',
            'set_other_rights' => 'required|boolean',
            'other_rights' => 'required|max:255',
            'sealed_registered' => 'required|boolean',
            'car_parking_floor' => 'required|max:255',
            'car_parking_type' => [
                'required',
                Rule::in(config('enums.tenant_contract.car_parking_type'))
            ],
            'car_parking_space_number' => 'required|max:255',
            'motorcycle_parking_floor' => 'required|max:255',
            'motorcycle_parking_space_number' => 'required|max:255',
            'motorcycle_parking_count' =>
                'required|integer|digits_between:1,11',
            'effective' => 'required|boolean',
            'contract_start' => 'required|date',
            'contract_end' => 'required|date',
            'rent' => 'required|integer|digits_between:1,11',
            'rent_pay_day' => 'required|integer|between:1,31',
            'deposit' => 'required|integer|digits_between:1,11',
            'deposit_paid' => 'required|integer|digits_between:1,11',
            'electricity_calculate_method' => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.electricity_calculate_method')
                )
            ],
            'electricity_price_per_degree' =>
                'required|numeric|between:0,99.99',
            'electricity_price_per_degree_summer' =>
                'required|numeric|between:0,99.99',
            '110v_start_degree' => 'required|integer|digits_between:1,11|lte:110v_end_degree',
            '220v_start_degree' => 'nullable|integer|digits_between:1,11|lte:220v_end_degree',
            '110v_end_degree' => 'required|integer|digits_between:1,11',
            '220v_end_degree' => 'nullable|integer|digits_between:1,11',
            'invoice_collection_method' => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.invoice_collection_method')
                )
            ],
            'invoice_collection_number' => 'required|max:255',
            'commissioner_id' => 'exists:users,id',
            'comment' => 'present',
        ]);

        InvoiceService::compareReceipt($tenantContract, $validatedData);

        $tenantContract->update($validatedData);
        $this->handleDocumentsUpload($tenantContract, [
            'original_file',
            'carrier_file'
        ]);
        return redirect($request->_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TenantContract  $tenantContract
     * @return \Illuminate\Http\Response
     */
    public function destroy(TenantContract $tenantContract)
    {
        $tenantContract->delete();
        return response()->json(true);
    }

    public function electricityPaymentReport(string $data)
    {
        $data = explode('|', base64_decode($data));
        $tenantContract = TenantContract::find(intval($data[0], 10));
        $year = intval($data[1], 10);
        $month =  intval($data[2], 10);
        $createdAt = Carbon::createFromTimestamp($data[3]);
        $room = $tenantContract->room()->first();
        $row = $room->buildElectricityPaymentData($year, $month);

        return view('tenant_contracts.electricity_payment_report', [
            'reportRows' => [$row],
            'year' => $year,
            'month' => $month,
            'createdAt' => $createdAt,
        ]);
    }

    public function sendElectricityPaymentReportSMS(Request $request)
    {
        $tenantContractId = intval($request->input('tenantContractId'));
        $year = intval($request->input('year'));
        $month = intval($request->input('month'));

        $tenantContract = TenantContract::find($tenantContractId);
        $tenantContract->sendElectricityPaymentReportSMS($year, $month);
        return response()->json(true);
    }

    public function electricityDegree(TenantContract $tenantContract)
    {
        return response()->json([
            'method' => $tenantContract->electricity_calculate_method,
            'pricePerDegree' => $tenantContract->electricity_price_per_degree,
            'pricePerDegreeSummer' => $tenantContract->electricity_price_per_degree_summer,
        ]);
    }

    public function payment_recheck(TenantContract $tenantContract)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($tenantContract->load(['tenantPayments','tenantElectricityPayments','payLogs']))
            ->relations(['tenantPayments','tenantElectricityPayments','payLogs']);

        return view('tenant_contracts.payment_recheck', $responseData->get());
    }
}
