<?php

namespace App\Http\Controllers;

use App\SystemVariable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Schema;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function whitelist($model)
    {
        if (config("whitelist.{$model}") != null) {
            return config("whitelist.{$model}");
        } else {
            return array_diff(Schema::getColumnListing($model), [
                'created_at',
                'deleted_at',
                'updated_at'
            ]);
        }
    }

    /**
     * must be called by method named index
     * @param Builder $builder
     * @param bool    $useGET
     *
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function limitRecords(Builder $builder, bool $useGET=true)
    {
        // only allow function named index to access this function
        $methodAllowedFrom = ['index'];
        $callerFunction = debug_backtrace()[1]['function'] ?? null;

        // caller's function must in array
        $validCall = ! is_null($callerFunction) && in_array($callerFunction, $methodAllowedFrom);
        // is show all records
        $showAll = request()->input('showAll', null) == 1;

        if ($validCall && !$showAll) {
            $recordLimit = SystemVariable::where('code', 'default_records_in_index_blade')->first('value')['value'] ?? 200;

            return $useGET
                ? $builder->orderBy('id', 'desc')->limit($recordLimit)->get()
                : $builder->orderBy('id', 'desc')->limit($recordLimit);
        }

        return $useGET ? $builder->get(): $builder;
    }
}
