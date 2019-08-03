<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Schema;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function whitelist($model){
        if( config("whitelist.{$model}") != null ){
            return config("whitelist.{$model}");
        }
        else{
            return array_diff(Schema::getColumnListing($model), ['created_at', 'deleted_at', 'updated_at']);
        }
    }

}
