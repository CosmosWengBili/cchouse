<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Services\ReceiptService;
use App\Services\InvoiceService;

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
    // download specific table file
    public function export_by_function($function)
    {
        switch($function){
            case 'invoice':
                $service = new InvoiceService();
                $start_date = Input::get('start_date');
                $end_date = Input::get('end_date');
                $invoiceData = $service->makeInvoiceData(Carbon::parse($start_date), Carbon::parse($end_date));

                return Excel::download(
                    new InvoiceExport($invoiceData),
                    '發票報表.xlsx'
                );
            case 'receipt':
                $service = new ReceiptService();
                $start_date = Input::get('start_date');
                $end_date = Input::get('end_date');
                $receiptData = $service->makeReceiptData(Carbon::parse($start_date), Carbon::parse($end_date));

                return Excel::download(
                    new ReceiptExport($receiptData, $buildingData),
                    '收據報表.xlsx'
                );
                break;
            default:
                break;
        }
    }
}
