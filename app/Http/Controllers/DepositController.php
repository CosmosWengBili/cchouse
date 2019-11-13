<?php

namespace App\Http\Controllers;

use App\PayLog;
use App\TenantContract;
use App\TenantPayment;
use Carbon\Carbon;
use App\Room;
use App\Deposit;
use App\User;
use App\CompanyIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use phpDocumentor\Reflection\Types\Boolean;
use App\Classes\NotifyUsers;
use App\Classes\TextContent;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $responseData = new NestedRelationResponser();
        $selectColumns = array_merge(['deposits.*'], Deposit::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));

        $responseData
            ->index(
                'deposits',
                $this->limitRecords(
                    Deposit::withExtraInfo()->select($selectStr)->with($request->withNested)
                )
            )
            ->relations($request->withNested);
        return view('deposits.index', $responseData->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $responseData = new FormDataResponser();
        return view('deposits.form', $responseData->create(Deposit::class, 'deposits.store')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $this->validatedData($request, true);
        $deposit = Deposit::create($validatedData);
        $deposit->room->update(['room_status' => '已收訂']);

        return redirect($request->_redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Deposit $deposit)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($deposit->load($request->withNested))
            ->relations($request->withNested);

        return view('deposits.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Deposit $deposit)
    {
        $responseData = new FormDataResponser();
        return view('deposits.form', $responseData->edit($deposit, 'deposits.update')->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deposit $deposit)
    {
        $validatedData = $this->validatedData($request);
        $this->generateEditorialReview($deposit, $validatedData);

        //Notify specific manager
        $user = User::find(1);
        $notify = new NotifyUsers($user);
        $content = new TextContent($this->makeDepositUpdatedContent('updated', $deposit));
        $notify->notifySelf($content);

        return redirect($request->_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param \App\Deposit $deposit
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request, Deposit $deposit)
    {
        $reason = $request->input('reason');
        $deposit->update(['reason_of_deletions' => $reason]);

        $this->generateEditorialReviewWithCommand($deposit, '刪除');

        //Notify specific manager
        $user = User::find(1);
        $notify = new NotifyUsers($user);
        $content = new TextContent($this->makeDepositUpdatedContent('deleted', $deposit));
        $notify->notifySelf($content);

        return response()->json(true);
    }

    public function close(Request $request, Deposit $deposit){
        $validatedData = $request->validate([
            "deposit_returned_amount" => 'nullable',
            "confiscated_or_returned_date" => 'nullable',
            "returned_method" => 'nullable',
            "returned_bank" => 'nullable',
            "returned_serial_number" => 'nullable',
            "deposit_confiscated_amount" => 'nullable',
            "company_allocation_amount" => 'nullable',
        ]);

        DB::transaction(function () use ($deposit, $validatedData) {
            if (isset($validatedData['confiscated_or_returned_date']) && isset($validatedData['deposit_confiscated_amount']) && $validatedData['deposit_confiscated_amount'] != 0 ) {
                // make new company income
                CompanyIncome::create([
                    'incomable_type' => Deposit::class,
                    'incomable_id' => $deposit->id,
                    'subject' => '訂金',
                    'income_date' => $validatedData['confiscated_or_returned_date'],
                    'amount' => $validatedData['company_allocation_amount'] ?? $validatedData['deposit_confiscated_amount'],
                ]);

                $companyAllocationAmount = $validatedData['company_allocation_amount'] ?? $validatedData['deposit_confiscated_amount'];
                
                if( $companyAllocationAmount > 0 ){
                    if($deposit->isManagedByCompany()){
                        $subject = '訂金(房東)';
                    }
                    else{
                        $subject = '訂金';
                    }
                    PayLog::create([
                        'loggable_type' => Deposit::class,
                        'loggable_id' =>  $deposit->id,
                        'subject' => $subject,
                        'receipt_type' => '發票',
                        'payment_type' => '租金雜費',
                        'amount' => $companyAllocationAmount,
                        'paid_at' => $validatedData['confiscated_or_returned_date']
                    ]);
                }
            }
            $deposit->update($validatedData);
            $deposit->room->update(['room_status' => '未出租']);
        });

        return redirect('/deposits');
    }

    // 轉履保
    public function transform(Deposit $deposit) {
        $contract = $deposit->room->activeContracts->first();
        $payment =  $contract->tenantPayments()->where('subject', '履約保證金')->first();
        
        PayLog::create([
            'loggable_type' => TenantPayment::class,
            'loggable_id' =>  $payment->id,
            'subject' => '履約保證金',
            'paid_at' => $deposit->deposit_collection_date,
            'receipt_type' => '收據',
            'payment_type' => '租金雜費',
            'amount' => $deposit->invoicing_amount,
            'pay_sum' => $deposit->invoicing_amount,
            'come_from_bank' => '訂金轉履保',
            'tenant_contract_id' => $contract->id
        ]);
        return response()->json(true);
    }

    private function validatedData(Request $request, bool $checkCollected = false) {
        $room_id = $request->input('room_id');

        return $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'tenant_contract_id' => 'nullable|exists:tenant_contract,id',
            'deposit_collection_date' => 'required|date',
            'deposit_collection_serial_number' => 'required|max:255',
            'invoicing_amount' => 'required|integer|digits_between:1,11',
            'is_deposit_collected' => [
                'required',
                'boolean',
                function ($attribute, $value, $fail) use($room_id, $checkCollected) {
                    if (!$checkCollected) return;

                    $room = Room::find($room_id);
                    if (!$room) return;

                    $invalid = $room->deposits()->where('is_deposit_collected', true)->first();
                    if ($invalid) { $fail("房代碼 {$room->room_code} 已簽約"); }
                },
            ],
            'comment' => 'nullable',
            'payer_name' => 'nullable',
            'payer_certification_number' => 'nullable',
            'payer_is_legal_person' => 'boolean',
            'payer_phone' => 'nullable',
            'receiver' => 'nullable|exists:users,id',
            'appointment_date' => 'nullable|date',
        ]);
    }

    private function makeDepositUpdatedContent(string $type, Deposit $deposit)
    {
        $now = Carbon::now();
        $id = $deposit->id;

        switch ($type) {
            case 'deleted':
                $reason = $deposit->reason_of_deletions;
                $content = "訂金編號: {$id} 資料被申請刪除，原因：{$reason}。";
                break;
            default:
                $comment = $deposit->comment;
                $content = "訂金編號: {$id} 資料被申請更新，請立即前往確認，備註: {$comment}。";
        }

        return $content;
    }
}
