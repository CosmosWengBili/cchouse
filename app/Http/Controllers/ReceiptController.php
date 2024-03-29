<?php

namespace App\Http\Controllers;

use App\TenantContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;

use Carbon\Carbon;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;

use App\Services\InvoiceService;
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

        $type= Input::get('type')? Input::get('type') : 'invoice';
        // for invoice search
        $start_date = Input::get('start_date');
        $end_date = Input::get('end_date');
        // for receipt search
        $receipt_year = Input::get('receipt_year');
        $receipt_month = Input::get('receipt_month');

        $invoiceData = [];
        $receiptData = [];
        $columns = array_map(function ($column) { return "tenant_contract.{$column}"; }, $this->whitelist('tenant_contracts'));
        $selectColumns = array_merge($columns, TenantContract::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));

        $tenant_contracts = $responseData
            ->index(
                'tenant_contracts',
                $this->limitRecords(
                    TenantContract::withExtraInfo()
                        ->select($selectStr)
                        ->where('contract_end', '>', Carbon::today()->subWeek())
                        ->with($request->withNested)
                )
            )
            ->relations($request->withNested)->get();
        
        if(isset($start_date) && isset($end_date)){
            if( $type == 'invoice'){
                $service = new InvoiceService();
                $invoiceData = $service->makeInvoiceData(Carbon::parse($start_date), Carbon::parse($end_date));
            }
        }
        else if(isset($receipt_year) && isset($receipt_month)){
            if( $type == 'receipt' ){
                $service = new ReceiptService();
                $receiptData = $service->makeReceiptData($receipt_year, $receipt_month);
            }
        }

        return view('receipts.index', $tenant_contracts)
            ->with('invoiceData', $invoiceData)
            ->with('receiptData', $receiptData)
            ->with('type', $type)
            ->with('start_date', $start_date)
            ->with('end_date', $end_date)
            ->with('receipt_year', $receipt_year)
            ->with('receipt_month', $receipt_month);
    }

    /**
     * show all receipts with specific period and invoice serial number input
     *
     * @return \Illuminate\Http\Response
     */
    public function edit_invoice()
    {
        $start_date = Input::get('start_date');
        $end_date = Input::get('end_date');
        $invoiceData = [];
        $service = new InvoiceService();
        if(isset($start_date) && isset($end_date)){
            $initData = $service->makeInvoiceData(Carbon::parse($start_date), Carbon::parse($end_date));
            foreach( $initData as $receiverData ){
                $invoiceData = array_merge($invoiceData, $receiverData);
            }
        }
        return view('receipts.edit_invoice')
            ->with('invoiceData', $invoiceData);
    }

    /**
     * update and create invoice number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update_invoice(Request $request)
    {
        $receipts = Input::get('receipts');
        $service = new InvoiceService();
        $service->updateInvoiceNumber($receipts);

        return redirect($request->_redirect);
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
