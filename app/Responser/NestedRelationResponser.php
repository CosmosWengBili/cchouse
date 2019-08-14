<?php

namespace App\Responser;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class NestedRelationResponser
{
    private $makeHidden = ['created_at', 'updated_at', 'deleted_at'];

    private $responseData = [
        'data' => [],
        'relations' => [],
        'model_name' => ''
    ];

    // for displaying a list of models
    public function index($name, Collection $list)
    {
        $this->responseData['data'][$name] = $list->toArray();
        return $this;
    }

    // for showing a required model
    public function show(Model $model)
    {
        $this->responseData['data'] = $model
            ->makeHidden($this->makeHidden)
            ->toArray();
        $this->responseData['model_name'] = class_basename($model);
        return $this;
    }

    public function relations(array $relations)
    {
        $this->responseData['relations'] = $relations;
        return $this;
    }

    // output data
    public function get()
    {
        return $this->responseData;
    }
}
