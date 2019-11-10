<?php

namespace App\Http\Controllers;

use App\LandlordOtherSubject;
use App\Services\InvoiceService;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LandlordOtherSubjectController extends Controller
{
    public function index(Request $request)
    {

        $responseData = new NestedRelationResponser();
        $responseData
            ->index(
                'landlord_other_subjects',
                $this->limitRecords(LandlordOtherSubject::with($request->withNested))
            )
            ->relations($request->withNested);

        return view('landlord_other_subjects.index', $responseData->get());
    }

    public function create()
    {
        $responseData = new FormDataResponser();
        $data = $responseData->create(LandlordOtherSubject::class, 'landlordOtherSubjects.store')->get();

        return view('landlord_other_subjects.form', $data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'subject' => 'required',
            'subject_type' => 'required',
            'income_or_expense' => [
                'required',
                Rule::in(config('enums.landlord_other_subjects.income_or_expense'))
            ],
            'expense_date' => 'required|date',
            'amount' => 'required',
            'comment' => 'nullable',
            'room_id' => 'required|exists:rooms,id',
            'is_invoiced' => 'nullable',
            'invoice_item_name' => 'nullable',
        ]);

        LandlordOtherSubject::create($validatedData);

        return redirect($request->_redirect);
    }

    public function edit(LandlordOtherSubject $landlordOtherSubject)
    {
        $responseData = new FormDataResponser();
        $data = $responseData->edit($landlordOtherSubject, 'landlordOtherSubjects.update')->get();

        return view('landlord_other_subjects.form', $data);
    }

    public function update(Request $request, LandlordOtherSubject $landlordOtherSubject)
    {
        $validatedData = $request->validate([
            'subject' => 'required',
            'subject_type' => 'required',
            'income_or_expense' => [
                'required',
                Rule::in(config('enums.landlord_other_subjects.income_or_expense'))
            ],
            'expense_date' => 'required|date',
            'amount' => 'required',
            'comment' => 'nullable',
            'room_id' => 'required|exists:rooms,id',
            'is_invoiced' => 'nullable',
            'invoice_item_name' => 'nullable',
        ]);

        $result = InvoiceService::compareReceipt($landlordOtherSubject, $validatedData);
        if(!$result){
            $landlordOtherSubject->update($validatedData);
        }
        return redirect($request->_redirect);
    }

    public function show(Request $request, LandlordOtherSubject $landlordOtherSubject)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($landlordOtherSubject->load($request->withNested))
            ->relations($request->withNested);

        return view('landlord_other_subjects.show', $responseData->get());
    }

    public function destroy(LandlordOtherSubject $landlordOtherSubject)
    {
        $landlordOtherSubject->delete();
        return response()->json(true);
    }
}
