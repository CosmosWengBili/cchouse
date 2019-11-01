<?php

namespace App\Http\Controllers;

use App\Building;
use App\LandlordContract;
use App\EditorialReview;
use App\Exports\ShareholderExport;
use App\Shareholder;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ShareholderController extends Controller
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
        $responseData
            ->index(
                'shareholders',
                $this->limitRecords(
                    Shareholder::select($this->whitelist('shareholders'))
                    ->with($request->withNested)
                )
            )
            ->relations($request->withNested);

        return view('shareholders.index', $responseData->get());
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
            'shareholders.form',
            $responseData
                ->create(Shareholder::class, 'shareholders.store')
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
            'name' => 'required|max:255',
            'contact_method' => 'required',
            'bank_name' => 'required',
            'bank_code' => 'required',
            'account_number' => 'required|integer',
            'account_name' => 'required',
            'is_remittance_fee_collected' => 'required|boolean',
            'transfer_from' => 'required',
            'bill_delivery' => 'required',
            'distribution_method' => 'required',
            'distribution_start_date' => 'required|date',
            'distribution_end_date' => 'required|date',
            'distribution_rate' => 'nullable',
            'distribution_amount' => 'nullable',
            'investment_amount' => 'required',
            'method' => 'required',
            'bank_branch' => 'required',
            'exchange_fee' => 'nullable',
        ]);

        is_null($validatedData['distribution_rate']) and ($validatedData['distribution_rate'] = 0);
        is_null($validatedData['distribution_amount']) and ($validatedData['distribution_amount'] = 0);
        is_null($validatedData['exchange_fee']) and ($validatedData['exchange_fee'] = 0);

        $shareholder = Shareholder::create($validatedData);

        $building_code = explode(',', str_replace(' ', '', $request->input('building_code')));
        // get building ids by building_code
        $building_ids = Building::whereIn('building_code', $building_code)->get()->pluck('id')->toArray();
        if (! empty($building_ids)) {
            $shareholder->buildings()->sync($building_ids);
        } else {
            $shareholder->buildings()->sync(array());
        }
        return redirect($request->_redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Shareholder  $Shareholder
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Shareholder $shareholder)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($shareholder->load($request->withNested))
            ->relations($request->withNested);

        return view('shareholders.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Shareholder  $Shareholder
     * @return \Illuminate\Http\Response
     */
    public function edit(Shareholder $shareholder)
    {
        $responseData = new FormDataResponser();
        $data = $responseData->edit($shareholder, 'shareholders.update')->get();
        $data['data']['building_code'] = $shareholder->buildings()->pluck('building_code')->unique()->implode(',');

        return view(
            'shareholders.form',
            $data
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Shareholder  $shareholder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shareholder $shareholder)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'contact_method' => 'required',
            'bank_name' => 'required',
            'bank_code' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'is_remittance_fee_collected' => 'required|boolean',
            'transfer_from' => 'required',
            'bill_delivery' => 'required',
            'distribution_method' => 'required',
            'distribution_start_date' => 'required|date',
            'distribution_end_date' => 'required|date',
            'distribution_rate' => 'nullable',
            'distribution_amount' => 'nullable',
            'investment_amount' => 'required',
            'method' => 'required',
            'bank_branch' => 'required',
            'exchange_fee' => 'nullable',
        ]);

        $oldRow = $shareholder->getAttributes();
        $newRow = $validatedData;

        // 判斷如果 array key 數量不同 要補滿 這樣使用者查看差異性 會比較直觀
        if (collect($oldRow)->keys()->count() !== collect($newRow)->keys()->count()) {
            $newRow = array_merge($oldRow, $newRow); // 用舊的欄位填補新的欄位
        }

        // 需要審核 所以不做 Shareholder 的 update Observer要發通知
        EditorialReview::create([
            'editable_id' => $shareholder->id,
            'editable_type' => Shareholder::class,
            'original_value' => $oldRow,
            'edit_value' => $newRow,
            'edit_user' => Auth::id(),
            'extra_data' => [
                'building_code' =>$request->input('building_code'),
            ],
            'comment' => '',
        ]);

        return redirect($request->_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Shareholder  $Shareholder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shareholder $shareholder)
    {
        $shareholder->delete();
        return response()->json(true);
    }

    public function exportReport(Request $request)
    {
        $year = $request->input('year', now()->format('Y'));
        $month = $request->input('month', now()->format('m'));
        $date = Carbon::create($year, $month)->subMonth();

        return Excel::download(
            new ShareholderExport($date),
            "出帳明細-{$year}-{$month}.xlsx"
        );
    }
}
