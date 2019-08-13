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

        $whitelist = Schema::getColumnListing("{$table}s");

        if (in_array($text, $whitelist)) {
            if (strpos($table, '_') !== false) {
                // from landlord_payment to LandlordPayment
                $table = str_replace("_", " ", $table);
                $table = ucwords($table);
                $table = str_replace(" ", "", $table);
            } else {
                $table = ucfirst($table);
            }
            $model = app("App\\{$table}");
            $data = $model::select($text, $value)->get();

            return response()->json($data);
        } else {
            return response('invalid');
        }
    }
}
?>
