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
use App\TenantPayment;
use App\Traits\Controllers\HandleDocumentsUpload;
use App\Services\TenantContractService;
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
        $responseData  = new NestedRelationResponser();
        $selectColumns = array_merge(['tenant_contract.*'], TenantContract::extraInfoColumns());
        $selectStr     = DB::raw(join(', ', $selectColumns));

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
    public function create(Request $request)
    {
        $responseData = new FormDataResponser();
        $data         = $responseData
            ->create(TenantContract::class, 'tenantContracts.store')
            ->get();
        $data['data']['original_files'] = [];
        $data['data']['carrier_files']  = [];

        if ($request->old()) {
            $data['data'] = array_merge($data['data'], $request->old());
        }

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
            'room_id'                => 'required|exists:rooms,id',
            'tenant_id'              => 'required|exists:tenants,id',
            'contract_serial_number' => 'required|max:255',
            'set_other_rights'       => 'required|boolean',
            'other_rights'           => 'required|max:255',
            'sealed_registered'      => 'required|boolean',
            'car_parking_floor'      => 'required|max:255',
            'car_parking_type'       => [
                'required',
                Rule::in(config('enums.tenant_contract.car_parking_type'))
            ],
            'car_parking_space_number'        => 'required|max:255',
            'motorcycle_parking_floor'        => 'required|max:255',
            'motorcycle_parking_space_number' => 'required|max:255',
            'motorcycle_parking_count'        => 'required|integer|digits_between:1,11',
            'contract_start'                  => 'required|date',
            'contract_end'                    => 'required|date',
            'rent'                            => 'required|integer|digits_between:1,11',
            'rent_pay_day'                    => 'required|integer|between:1,31',
            'deposit'                         => 'required|integer|digits_between:1,11',
            'deposit_paid'                    => 'required|integer|digits_between:1,11',
            'electricity_calculate_method'    => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.electricity_calculate_method')
                )
            ],
            'electricity_price_per_degree'        => 'required|numeric|between:0,99.99',
            'electricity_price_per_degree_summer' => 'required|numeric|between:0,99.99',
            '110v_start_degree'                   => 'required|integer|digits_between:1,11',
            '220v_start_degree'                   => 'nullable|integer|digits_between:1,11',
            '110v_end_degree'                     => 'sometimes|nullable|integer|digits_between:1,11|gt:110v_start_degree',
            '220v_end_degree'                     => 'sometimes|nullable|integer|digits_between:1,11|gt:220v_end_degree',
            'invoice_collection_method'           => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.invoice_collection_method')
                )
            ],
            'invoice_collection_number' => 'nullable',
            'commissioner_id'           => 'exists:users,id',
            'comment'                   => 'present',
            'overdue_fine'              => 'required'
        ]);

        $validatedPaymentData = $request->validate([
            'payments'           => 'nullable|array',
            'payments.*.subject' => [
                'required_with:payments',
                Rule::in(config('enums.tenant_payments.subject'))
            ],
            'payments.*.period' => [
                'required_with:payments',
                Rule::in(config('enums.tenant_payments.period'))
            ],
            'payments.*.amount'       => 'required_with:payments|integer|digits_between:1,11',
            'payments.*.collected_by' => [
                'required_with:payments',
                Rule::in(config('enums.tenant_payments.collected_by'))
            ]
        ]);

        $deposit =  $validatedData['deposit'];
        $payments =  $validatedPaymentData['payments'] ?? [];

        // default payment option
        $payment = [
            'subject'      => '履約保證金',
            'period'       => '次',
            'amount'       => $deposit,
            'collected_by' => '房東'
        ];

        // extend
        if ($request->get('old_tenant_contract_id', 0) > 0) {
            $tenantPayment = TenantPayment::where('tenant_contract_id', $request->get('old_tenant_contract_id'))
                                ->where('subject', '履約保證金')
                                ->orderBy('id', 'DESC')
                                ->first();

            if ($tenantPayment) {
                $diff_deposit = $deposit - $tenantPayment->amount;
                if ($diff_deposit !== 0) {
                    $payment['amount']             = $diff_deposit;
                    $payment['is_charge_off_done'] = true;
                    $payment['charge_off_date'] = Carbon::now();
                }
            }
        }

        $payments[] = $payment;

        $tenantContract = $this->tenantContractService->create(
            $validatedData,
            $payments
        );

        // old deposit relate new payment
        if (isset($tenantPayment)) {
            $tenantPayment->is_charge_off_done = true;
            $tenantContract->tenantPayments()->save($tenantPayment);
            $tenantPayment->save();
        }

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
        $tenantContract->load($request->withNested)
                    ->load(['payLogs' => function ($query) {
                        $query->addSelect([
                            'pay_logs.id',
                            'pay_logs.loggable_type',
                            'pay_logs.loggable_id',
                            'pay_logs.subject',
                            DB::raw('due_time AS due_time'),
                            'pay_logs.*'
                        ])
                        ->join('tenant_payments', function ($join) {
                            $join->on('tenant_payments.id', '=', 'pay_logs.loggable_id')
                                ->where('pay_logs.loggable_type', 'App\TenantPayment');
                        });
                    }]);

        $responseData = new NestedRelationResponser();
        $responseData
            ->show($tenantContract)
            ->relations($request->withNested);

        $paid_diff = $tenantContract->sum_paid - $tenantContract->payLogs()->get()->sum(function ($p) {
            return $p->amount ?? 0;
        });

        $responseData = $responseData->get();

        $responseData['data'] = makeDateFormatByKeys($responseData['data'], ['contract_start', 'contract_end'], 'Y-m-d');

        return view('tenant_contracts.show', array_merge($responseData, ['paid_diff' => $paid_diff]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TenantContract  $tenantContract
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, TenantContract $tenantContract)
    {
        $responseData = new FormDataResponser();
        $data         = $responseData
            ->edit($tenantContract, 'tenantContracts.update')
            ->get();
        $data['data'][
            'original_files'
        ]                              = $tenantContract->originalFiles()->get();
        $data['data']['carrier_files'] = $tenantContract->carrierFiles()->get();

        if ($request->old()) {
            $data['data'] = array_merge($data['data'], $request->old());
        }

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
        $data         = $responseData
                            ->edit($tempContract, 'tenantContracts.create')
                            ->get();

        $data['data']['original_files'] = null;
        $data['data']['carrier_files']  =  null;

        if ($request->old()) {
            $data['data'] = array_merge($data['data'], $request->old());
        }

        // 租客帳單
        $firstPaymentDate               = $tenantContract->tenantPayments->first()->due_time;
        $data['data']['tenant_payment'] = $tenantContract->tenantPayments
                                            ->where('due_time', $firstPaymentDate)
                                            ->where('subject', '<>', '租金')
                                            ->where('subject', '<>', '履約保證金')
                                            ->toArray() or [];
        $data['data']['tenant_payment'] = array_values($data['data']['tenant_payment']);
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
            'room_id'                => 'required|exists:rooms,id',
            'tenant_id'              => 'required|exists:tenants,id',
            'contract_serial_number' => 'required|max:255',
            'set_other_rights'       => 'required|boolean',
            'other_rights'           => 'required|max:255',
            'sealed_registered'      => 'required|boolean',
            'car_parking_floor'      => 'required|max:255',
            'car_parking_type'       => [
                'required',
                Rule::in(config('enums.tenant_contract.car_parking_type'))
            ],
            'car_parking_space_number'        => 'required|max:255',
            'motorcycle_parking_floor'        => 'required|max:255',
            'motorcycle_parking_space_number' => 'required|max:255',
            'motorcycle_parking_count'        => 'required|integer|digits_between:1,11',
            'contract_start'                  => 'required|date',
            'contract_end'                    => 'required|date',
            'rent'                            => 'required|integer|digits_between:1,11',
            'rent_pay_day'                    => 'required|integer|between:1,31',
            'deposit'                         => 'required|integer|digits_between:1,11',
            'deposit_paid'                    => 'required|integer|digits_between:1,11',
            'electricity_calculate_method'    => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.electricity_calculate_method')
                )
            ],
            'electricity_price_per_degree'        => 'required|numeric|between:0,99.99',
            'electricity_price_per_degree_summer' => 'required|numeric|between:0,99.99',
            '110v_start_degree'                   => 'required|integer|digits_between:1,11',
            '220v_start_degree'                   => 'nullable|integer|digits_between:1,11',
            '110v_end_degree'                     => 'sometimes|nullable|integer|digits_between:1,11|gt:110v_start_degree',
            '220v_end_degree'                     => 'sometimes|nullable|integer|digits_between:1,11|gt:220v_end_degree',
            'invoice_collection_method'           => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.invoice_collection_method')
                )
            ],
            'invoice_collection_number' => 'nullable',
            'commissioner_id'           => 'exists:users,id',
            'comment'                   => 'present',
            'overdue_fine'              => 'required'
        ]);

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
        $data           = explode('|', base64_decode($data));
        $tenantContract = TenantContract::find(intval($data[0], 10));
        $year = intval($data[1], 10);
        $month =  intval($data[2], 10);
        $now = Carbon::parse("${year}-${month}-01");
        $fromDate = $now->copy()->startOfMonth();
        $tillDate =  $now->copy()->endOfMonth();
        $createdAt = Carbon::createFromTimestamp($data[3]);
        $tenantElectricityPayment = $tenantContract->tenantElectricityPayments()
                                                   ->whereBetween('due_time', [$fromDate, $tillDate])
                                                   ->first();
        $ammeterReadDate = $tenantElectricityPayment ? $tenantElectricityPayment->ammeter_read_date : null;
        $room = $tenantContract->room()->first();
        $row = $room->buildElectricityPaymentData($year, $month);

        return view('tenant_contracts.electricity_payment_report', [
            'reportRows' => [$row],
            'year' => $year,
            'month' => $month,
            'createdAt' => $createdAt,
            'ammeterReadDate' => $ammeterReadDate,
        ]);
    }

    public function sendElectricityPaymentReportSMS(Request $request)
    {
        $tenantContractId = intval($request->input('tenantContractId'));
        $year             = intval($request->input('year'));
        $month            = intval($request->input('month'));

        $tenantContract = TenantContract::find($tenantContractId);
        $tenantContract->sendElectricityPaymentReportSMS($year, $month);

        return response()->json(true);
    }

    public function electricityDegree(TenantContract $tenantContract)
    {
        return response()->json([
            'method'               => $tenantContract->electricity_calculate_method,
            'pricePerDegree'       => $tenantContract->electricity_price_per_degree,
            'pricePerDegreeSummer' => $tenantContract->electricity_price_per_degree_summer,
        ]);
    }

    public function payment_recheck(TenantContract $tenantContract)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($tenantContract->load(['tenantPayments', 'tenantElectricityPayments', 'payLogs']))
            ->relations(['tenantPayments', 'tenantElectricityPayments', 'payLogs']);

        return view('tenant_contracts.payment_recheck', $responseData->get());
    }
}
