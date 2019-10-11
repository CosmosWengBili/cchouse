<?php

namespace App\Http\Controllers;

use App\Room;
use Illuminate\Http\Request;
use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use App\Deposit;
use App\Services\DepositService;
use App\Services\ReceiptService;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Boolean;

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

        ReceiptService::compareReceipt($deposit, $validatedData);
        DepositService::update($deposit, $validatedData);

        return redirect($request->_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposit $deposit)
    {
        $deposit->delete();
        return response()->json(true);
    }

    private function validatedData(Request $request, bool $checkCollected = false) {
        $room_id = $request->input('room_id');

        return $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'tenant_contract_id' => 'nullable|exists:tenant_contract,id',
            'deposit_collection_date' => 'required|date',
            'deposit_collection_serial_number' => 'required|max:255',
            'deposit_confiscated_amount' => 'required|integer|digits_between:1,11',
            'deposit_returned_amount' => 'required|integer|digits_between:1,11',
            'confiscated_or_returned_date' => 'required|date',
            'invoicing_amount' => 'required|integer|digits_between:1,11',
            'invoice_date' => 'required|date',
            'is_deposit_collected' => [
                'required',
                'boolean',
                function ($attribute, $value, $fail) use($room_id, $checkCollected) {
                    if (!$checkCollected) return;

                    $room = Room::find($room_id);
                    $invalid = $room->deposits()->where('is_deposit_collected', true)->first();
                    if ($invalid) { $fail("房編號 {$room_id} 已簽約"); }
                },
            ],
            'comment' => 'required',
            'payer_name' => 'nullable',
            'payer_certification_number' => 'nullable',
            'payer_is_legal_person' => 'boolean',
            'payer_phone' => 'nullable',
            'receiver' => 'nullable|exists:users,id',
            'appointment_date' => 'nullable|date',
        ]);
    }
}
