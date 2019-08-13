<?php

namespace App\Traits\Database\Seeder;

use Illuminate\Database\Eloquent\Model;
use App;

trait UpdateOrCreate {

    /**
     * 根據 id 來決定建立或更新資料
     * 主要用於 Seeder 避免重複建立資料
     *
     * @param Model $model
     * @param array $data
     *
     */
    function updateOrCreate(string $model, array $data) {
        $id = $data['id'];
        $instance = $model::find($id);

        if (is_null($instance)) {
            $model::create($data);
        } else {
            $instance->update($data);
        }
    }
}
