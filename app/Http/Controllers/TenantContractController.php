<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Validation\Rule;
use App\Services\TenantContractService;
use App\TenantContract;

use App\Traits\Controllers\HandleDocumentsUpload;

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
        $responseData
            ->index(
                'tenant_contracts',
                TenantContract::with($request->withNested)->get()
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
            'rent_pay_day' => 'required|integer|digits_between:1,11',
            'deposit' => 'required|integer|digits_between:1,11',
            'deposit_paid' => 'required|integer|digits_between:1,11',
            'electricity_payment_method' => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.electricity_payment_method')
                )
            ],
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
            '110v_start_degree' => 'required|integer|digits_between:1,11',
            '220v_start_degree' => 'required|integer|digits_between:1,11',
            '110v_end_degree' => 'required|integer|digits_between:1,11',
            '220v_end_degree' => 'required|integer|digits_between:1,11',
            'invoice_collection_method' => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.invoice_collection_method')
                )
            ],
            'invoice_collection_number' => 'required|max:255',
            'commissioner_id' => 'exists:users,id'
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
        return redirect()->route('tenantContracts.index');
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

        return view('tenant_contracts.show', $responseData->get());
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
     *
     * @param  \App\TenantContract  $tenantContract
     * @return \Illuminate\Http\Response
     */
    public function extend(TenantContract $tenantContract)
    {
        $tempContract = $this->tenantContractService->makeExtendedContract(
            $tenantContract
        );
        $responseData = new FormDataResponser();
        $data = $responseData
            ->edit($tempContract, 'tenantContracts.create')
            ->get();
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
            'rent_pay_day' => 'required|integer|digits_between:1,11',
            'deposit' => 'required|integer|digits_between:1,11',
            'deposit_paid' => 'required|integer|digits_between:1,11',
            'electricity_payment_method' => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.electricity_payment_method')
                )
            ],
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
            '110v_start_degree' => 'required|integer|digits_between:1,11',
            '220v_start_degree' => 'required|integer|digits_between:1,11',
            '110v_end_degree' => 'required|integer|digits_between:1,11',
            '220v_end_degree' => 'required|integer|digits_between:1,11',
            'invoice_collection_method' => [
                'required',
                Rule::in(
                    config('enums.tenant_contract.invoice_collection_method')
                )
            ],
            'invoice_collection_number' => 'required|max:255',
            'commissioner_id' => 'exists:users,id'
        ]);

        $tenantContract->update($validatedData);
        $this->handleDocumentsUpload($tenantContract, [
            'original_file',
            'carrier_file'
        ]);
        return redirect()->route('tenantContracts.show', $tenantContract);
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
}
