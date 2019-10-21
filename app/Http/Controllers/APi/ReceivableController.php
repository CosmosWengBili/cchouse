<?php

namespace App\Http\Controllers\API;

use App\Services\UbotPaymentService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Room;
use App\PayLog;
use Carbon\Carbon;

use App\Events\ReceivableArrived;
use App\Responser\ReceivableResponser;

class ReceivableController extends Controller
{
    public function incoming(Request $request) {
        // parse xml
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($request->getContent());

        if ($xml === false) {
            return response('Invalid XML format');
        }

        $responser = new ReceivableResponser($xml);

        $virtualAccount = trim($xml->PmtAddRq->PR_Key1);

        try {
            // find tenant contract via room
            $room = Room::with('activeContracts')->where('virtual_account', config('finance.bank_code') . $virtualAccount)->firstOrFail();

            $targetContract = $room->activeContracts->first();

            $data = [
                'virtual_account' => config('finance.bank_code') . $virtualAccount,
                'txTime' => Carbon::createFromFormat('YmdHis', $xml->PmtAddRq->TxnDate . $xml->PmtAddRq->TxnTime),
                'amount' => trim($xml->PmtAddRq->TxAmount),
                'from_bank' => trim($xml->PmtAddRq->BankID),
                'from_account' => trim($xml->PmtAddRq->ActNo),
            ];

            // fire event
            $result = event(new ReceivableArrived($targetContract, $data));

            // after the event has been handled
            if ($result[0]['success']) {
                return response($responser->success($result[0]['message'])->get())
                        ->header('Content-Type', 'text/xml');
            } else {
                return response($responser->error(1, $result[0]['message'])->get())
                        ->header('Content-Type', 'text/xml');
            }

            // return response($responser->success('123')->get())
            // ->header('Content-Type', 'text/xml');

        } catch (ModelNotFoundException $e) {
            $room = Room::with('activeContracts')->where('electricity_virtual_account', config('finance.bank_code') . $virtualAccount)->first();
            if(isset($room)){
                PayLog::create([
                    'loggable_type' => Room::class,
                    'loggable_id' => $room->id,
                    'subject' => '電費',
                    'payment_type' => '電費',
                    'amount' => trim($xml->PmtAddRq->TxAmount),
                    'virtual_account' => $virtualAccount,
                    'receipt_type' => '收據',
                    'paid_at' => Carbon::createFromFormat('YmdHis', $xml->PmtAddRq->TxnDate . $xml->PmtAddRq->TxnTime),
                    'tenant_contract_id' => $room->activeContracts->first()['id']
                ]);
                return response($responser->success(1)->get())
                        ->header('Content-Type', 'text/xml');
            }
            else{
                return 'no record';
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function ubotWebhook(Request $request) {
        $requestData = json_decode($request->getContent(), true);
        $service = new UbotPaymentService($requestData);
        $txSeq = $service->txSeq();

        // mac 或 signature 驗證失敗
        if (!$service->validate()) {
            return response()->json(['txseq' => $txSeq, 'ubnotify' => 'record', 'resmsg' => 'failed']);
        }

        $virtualAccount = $service->virtualAccount();
        $txTime = $service->txTime();
        $amount = $service->amount();
        $fromBank = $service->wdBank();
        $fromAccount = $service->wdAcc();

        try {
            // find tenant contract via room
            $room = Room::with('activeContracts')->where('virtual_account', config('finance.bank_code') . $virtualAccount)->firstOrFail();
            $targetContract = $room->activeContracts->first();
            $data = [
                'virtual_account' => $virtualAccount,
                'txTime' => $txTime,
                'amount' => $amount,
                'from_bank' => $fromBank,
                'from_account' => $fromAccount,
            ];

            // fire event
            $result = event(new ReceivableArrived($targetContract, $data));

            // after the event has been handled
            if ($result[0]['success']) {
                return response()->json(['txseq' => $txSeq, 'ubnotify' => 'record', 'resmsg' => 'succes']);

            } else {
                return response()->json(['txseq' => $txSeq, 'ubnotify' => 'record', 'resmsg' => 'failed']);
            }
        } catch (ModelNotFoundException $e) {
            return 'no record';
        } catch (\Exception $e) {
            return $e;
        }
    }
}
