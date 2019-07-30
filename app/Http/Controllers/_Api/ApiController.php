<?php

namespace App\Http\Controllers\_Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests;
use View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class ApiController extends Controller
{
    public function selectize()
    {
        $table = Input::get ('table');
        $text = Input::get('text');
        
        $data = \DB::table($table)->select('id', $text)->get();

        return response ()->json ( $data );
    }
}
?>