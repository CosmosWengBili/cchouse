<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class EngineerController extends Controller
{
    public function api()
    {
        return view('engineers.api');
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
                $start_date = Input::get('start_date');
                $end_date = Input::get('end_date');
                $invoiceData = ReceiptService::makeInvoiceData(Carbon::parse($start_date), Carbon::parse($end_date));

                return Excel::download(
                    new ReceiptExport($invoiceData, 'invoice'),
                    '發票報表.xlsx'
                );
            case 'receipt':
                $start_date = Input::get('start_date');
                $end_date = Input::get('end_date');
                $receiptData = ReceiptService::makeReceiptData(Carbon::parse($start_date), Carbon::parse($end_date));

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
