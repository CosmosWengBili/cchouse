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
use App\Exports\ReceiptExport;
use App\Imports\MorphImport;

use App\Services\ReceiptService;

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
                $service = new ReceiptService();
                $start_date = Input::get('start_date');
                $end_date = Input::get('end_date');
                $invoiceData = $service->makeInvoiceData(Carbon::parse($start_date), Carbon::parse($end_date));

                return Excel::download(
                    new ReceiptExport($invoiceData, 'invoice'),
                    '發票報表.xlsx'
                );
            case 'receipt':
                $service = new ReceiptService();
                $start_date = Input::get('start_date');
                $end_date = Input::get('end_date');
                $receiptData = $service->makeReceiptData(Carbon::parse($start_date), Carbon::parse($end_date));

                return Excel::download(
                    new ReceiptExport($receiptData, 'receipt'),
                    '收據報表.xlsx'
                );
                break;
            default:
                break;
        }
    }
}
