<?php

namespace App\Responser;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class FormDataResponser
{
    private $responseData = [
        'data' => [],
        'method' => '',
        'action' => '',
        'model_name' => ''
    ];

    private $globalMakeHidden = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * pack data for edit page.
     *
     * $model: the model instance
     * $route: the route name for creating
     *
     * @param String $model
     * @param String $route
     */
    public function edit(Model $model, string $route)
    {
        $this->responseData['data'] = $model
            ->makeHidden($this->globalMakeHidden)
            ->toArray();
        $this->responseData['method'] = 'PUT';
        $this->responseData['action'] = route($route, $model);
        $this->responseData['model_name'] = class_basename($model);
        return $this;
    }

    /**
     * pack data for create page.
     *
     * $modelClass: the model's class name for getting it's table name
     * $route: the route name for creating
     *
     * @param String $modelClass
     * @param String $route
     */
    public function create(string $modelClass, string $route)
    {
        $pureModelName = explode("\\", $modelClass)[1];
        $hiddenList = array_merge(
            $this->globalMakeHidden,
            config("form.{$pureModelName}")
        );

        $this->responseData['data'] = array_diff(
            Schema::getColumnListing((new $modelClass())->getTable()),
            $hiddenList
        );
        $this->responseData['method'] = 'POST';
        $this->responseData['action'] = route($route);
        $this->responseData['model_name'] = $pureModelName;
        return $this;
    }

    // output data
    public function get()
    {
        return $this->responseData;
    }
}
