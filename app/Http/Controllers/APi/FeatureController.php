<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests;
use App;

use Illuminate\Http\Request;
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
            if( $table == 'LandlordPayment' && $value == 'subject' ){
                $data = $model::select($text, $value)->distinct($text)->where('subject', 'not like', "%案件%")->get();
            }
            else{
                $data = $model::select($text, $value)->distinct($text)->get();
            }
            return response()->json($data);
        } else {
            return response('invalid');
        }
    }

    public function shareHolders(Request $request)
    {
        $building_code = explode(',', $request->input('building_code', 0));

        $data = [];
        $buildings = App\Building::whereIn('building_code', $building_code)->get();

        foreach ($buildings as $building) {
            if ($building->shareholders->count()) {
                foreach($building->shareholders as $shareholder) {
                    if (!isset($data[$shareholder['id']])) {
                        $data[$shareholder['id']] = $shareholder;
                    }
                }
            }
        }

        return response()->json($data);
    }
}
