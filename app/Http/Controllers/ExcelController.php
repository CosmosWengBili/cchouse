<?php

namespace App\Http\Controllers;

use App\Exports\RelationExport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Input;

use App\Exports\MorphExport;
use App\Exports\InvoiceExport;
use App\Exports\ReceiptExport;
use App\Imports\MorphImport;
use App\Imports\InvoiceImport;

use App\Services\ReceiptService;
use App\Services\InvoiceService;

class ExcelController extends Controller
{
    // get upload page
    public function upload($model)
    {
        return view('excel.upload', ['model' => $model]);
    }

    // import file
    public function import(Request $request, $model)
    {
        // some other validations here
        if ($request->hasFile('excel')) {
            try {
                Excel::import(
                    new MorphImport('App\\' . $model),
                    $request->file('excel')
                );
            } catch (\Throwable $th) {
                return redirect()
                    ->back()
                    ->with('status', 'error');
            }
            return redirect()
                ->back()
                ->with('status', 'success');
        }
        return redirect()
            ->back()
            ->with('status', 'error');
    }

    // download file
    public function export($model)
    {
        return Excel::download(
            new MorphExport('App\\' . ucfirst($model)),
            $model . '.xlsx'
        );
    }

    // download example file
    public function example($model)
    {
        return Excel::download(
            new MorphExport('App\\' . $model, true),
            $model . '.xlsx'
        );
    }

    public function exportRelation(string $model, string $id, string $relation) {
        $model = ucfirst(Str::camel($model));
        $relation = Str::camel($relation);

        return Excel::download(
            new RelationExport('App\\' . $model, $id, $relation),
            ucfirst($relation) . '.xlsx'
        );
    }

    // download specific table file
    public function export_by_function($function)
    {
        switch($function){
            case 'invoice':
                $service = new InvoiceService();
                $start_date = Input::get('start_date');
                $end_date = Input::get('end_date');
                $invoiceData = [];
                $initData = $service->makeInvoiceData(Carbon::parse($start_date), Carbon::parse($end_date));
                foreach( $initData as $receiverData ){
                    $invoiceData = array_merge($invoiceData, $receiverData);
                }
                return Excel::download(
                    new InvoiceExport($invoiceData),
                    '發票報表.xlsx'
                );
            case 'receipt':
                $service = new ReceiptService();
                $receipt_year = Input::get('receipt_year');
                $receipt_month = Input::get('receipt_month');
                $receiptData = $service->makeReceiptData($receipt_year, $receipt_month);

                return Excel::download(
                    new ReceiptExport($receiptData['receipt'], $receiptData['receipt_building']),
                    '收據報表.xlsx'
                );
                break;
            default:
                break;
        }
    }

    // Import specific file
    public function import_by_function(Request $request, $function)
    {
        switch($function){
            case 'invoice':
                if ($request->hasFile('excel')) {
                    try {
                        Excel::import(
                            new InvoiceImport(),
                            $request->file('excel')
                        );

                    } catch (\Throwable $th) {
                        $request->session()->flash('status', '匯入失敗:'.$th->getMessage().' 檔案:'.$th->getFile().' 行數:'.$th->getLine());
                        return redirect()
                                ->back();
                    }
                    $request->session()->flash('status', '匯入成功');
                    return redirect()
                        ->back();
                }
                $request->session()->flash('status', '無匯入 Excel');
            default:
                break;
        }
        $request->session()->flash('status', '無對應匯入功能');
        return redirect()
                ->back();        
    }
}
