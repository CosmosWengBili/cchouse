<?php

namespace App\Http\Controllers;

use App\SystemVariable;
use App\EditorialReview;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

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

     /**
     * must be called by method named index
     * @param Model $model
     * @param Array $validatedData
     * @param Array $extraData
     *
     */
    protected function generateEditorialReview($model, $validatedData, $extraData=Null){

        $oldRow = $model->getAttributes();
        $newRow = $validatedData;

        // 判斷如果 array key 數量不同 要補滿 這樣使用者查看差異性 會比較直觀
        if (collect($oldRow)->keys()->count() !== collect($newRow)->keys()->count()) {
            $newRow = array_merge($oldRow, $newRow); // 用舊的欄位填補新的欄位
        }

        // 需要審核 所以不做 Shareholder 的 update Observer要發通知
        EditorialReview::create([
            'editable_id' => $model->id,
            'editable_type' => get_class($model),
            'original_value' => $oldRow,
            'edit_value' => $newRow,
            'edit_user' => Auth::id(),
            'extra_data' => $extraData,
            'comment' => '',
        ]);        
    }
}
