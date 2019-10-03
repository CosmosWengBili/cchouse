<?php

namespace App\Responser;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class SubTableResponser
{
    public function whitelist($main_model, $data, $relations)
    {
        // foreach by every sub data module
        foreach($relations as $relation){
            // check this data module is singular(belongs_to) or association (has_many) 
            if($this->isAssoc($data['data'][$relation])){
                $data['data'][$relation] = $this->filter($main_model, $relation, $data['data'][$relation]);
            }
            else{
                foreach( $data['data'][$relation] as $key => $data_row  ){
                    $data['data'][$relation][$key] = $this->filter($main_model, $relation, $data_row);
                }
            }
        }

        return $data;
    }

    public function filter($main_model, $model, $data){
        // filter base on whitelist_sub.php or the initial column list without deleted_at & updated_at
        $new_data = array_filter($data, function($key) use ($main_model, $model){
            return in_array($key, config('whitelist_sub')[$main_model][$model] ?? array_diff(Schema::getColumnListing($model), [
                'deleted_at',
                'updated_at'
            ]));
        },
        // paramter for array_filter to get key instead of value
        ARRAY_FILTER_USE_KEY);
        
        return $new_data;
    }

    public function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

}
