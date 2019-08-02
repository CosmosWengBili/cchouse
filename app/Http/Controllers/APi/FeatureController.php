<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests;
use App;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Schema;

class FeatureController extends Controller
{
    public function selectize()
    {
        $table = Input::get('table');
        $text = preg_replace("/[\'\"]+/" , '' ,Input::get('text'));
        
        $whitelist = Schema::getColumnListing("{$table}s");

        if (in_array($text, $whitelist)) {
            $table = ucfirst($table);
            $model = app("App\\{$table}");
            $data = $model::select('id', $text)->get();

            return response()->json($data);
        } else {
            return response("invalid");
        }
    }
}
?>
