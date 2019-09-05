<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\TenantContract;
use App\Room;
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
            return 'no record';
        } catch (\Exception $e) {
            return $e;
        }
    }
}
