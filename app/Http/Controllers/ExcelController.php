<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

use App\Exports\MorphExport;
use App\Imports\MorphImport;

class ExcelController extends Controller
{

    // get upload page
    public function upload($model) {
        return view('excel.upload', ['model' => $model]);
    }

    // import file
    public function import(Request $request, $model) {
        // some other validations here
        if ($request->hasFile('excel')) {
            try {
                Excel::import(new MorphImport('App\\'.$model), $request->file('excel'));
            } catch (\Throwable $th) {
                return redirect()->back()->with('status', 'error');
            }
            return redirect()->back()->with('status', 'success');
        }
        return redirect()->back()->with('status', 'error');
    }

    // download file
    public function export($model) {
        return Excel::download(new MorphExport('App\\'.$model), $model . '.xlsx');;
    }

    // download example file
    public function example($model) {
        return Excel::download(new MorphExport('App\\'.$model, true), $model . '.xlsx');;
    }
}
