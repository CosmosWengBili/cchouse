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
        $text = preg_replace("/[\'\"]+/", '', Input::get('text'));
        $value = preg_replace("/[\'\"]+/", '', Input::get('value'));
        
        $whitelist = Schema::getColumnListing($table);

        if (in_array($text, $whitelist)) {
            $table = ucfirst(camel_case(str_singular($table)));
            $model = app("App\\{$table}");
            $data = $model::select($text, $value)->get();

            return response()->json($data);
        } else {
            return response('invalid');
        }
    }
}
?>
