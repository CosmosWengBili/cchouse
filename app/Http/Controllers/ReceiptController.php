<?php

namespace App\Http\Controllers;

use App\TenantContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;

use Carbon\Carbon;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;

use App\Services\ReceiptService;

class ReceiptController extends Controller
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
        $start_date = Input::get('start_date');
        $end_date = Input::get('end_date');
        $invoiceData = [];
        $tenant_contracts = $responseData
            ->index(
                'tenant_contracts',
                TenantContract::select($this->whitelist('tenant_contracts'))
                    ->where('contract_end', '>', Carbon::today()->subWeek())
                    ->with($request->withNested)
                    ->get()
            )
            ->relations($request->withNested)->get();
            
        if(isset($start_date) && isset($end_date)){
            $invoiceData = ReceiptService::makeInvoiceData(Carbon::parse($start_date), Carbon::parse($end_date));
        }

        return view('receipts.index', $tenant_contracts)
            ->with('receiptData', $invoiceData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $responseData = new FormDataResponser();
        return view(
            'tenant_contracts.form',
            $responseData
                ->create(TenantContract::class, 'tenant_contracts.store')
                ->get()
        );
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
            'name' => 'nullable|max:255',
            'mobile' => 'nullable',
            'email' => 'required',
            'password' => 'required'
        ]);

        TenantContract::create($validatedData);

        return redirect('tenant_contracts');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TenantContract  $TenantContract
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, TenantContract $user)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($user->load($request->withNested))
            ->relations($request->withNested);

        return view('tenant_contracts.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TenantContract  $TenantContract
     * @return \Illuminate\Http\Response
     */
    public function edit(TenantContract $user)
    {
        $responseData = new FormDataResponser();
        return view(
            'tenant_contracts.form',
            $responseData->edit($user, 'tenant_contracts.update')->get()
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TenantContract  $TenantContract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TenantContract $user)
    {
        $input = $request->input();

        $request->validate([
            'name' => 'nullable|max:255',
            'mobile' => 'nullable',
            'email' => 'required'
        ]);

        $input['password'] = Hash::make($input['password']);
        $user->update($input);

        return redirect()->route('tenant_contracts.show', ['id' => $user->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TenantContract  $TenantContract
     * @return \Illuminate\Http\Response
     */
    public function destroy(TenantContract $user)
    {
        $user->delete();
        return response()->json(true);
    }
}
