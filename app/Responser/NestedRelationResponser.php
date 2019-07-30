<?php

namespace App\Responser;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class NestedRelationResponser {

    private $responseData = [
        'data' => [],
        'relations' => []
    ];

    // for displaying a list of models
    public function index($name, Collection $list) {
        $this->responseData['data'][$name] = $list->toArray();
        return $this;
    }

    // for showing a required model
    public function show(Model $model) {
        $this->responseData['data'] = $model->toArray();
        return $this;
    }

    public function relations(Array $relations) {
        $this->responseData['relations'] = $relations;
        return $this;
    }

    // output data
    public function get() {
        return $this->responseData;
    }
}
