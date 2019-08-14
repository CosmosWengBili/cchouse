<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MorphImport implements ToModel, WithHeadingRow
{
    private $model;

    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function model(array $row)
    {
        return $this->model::create($row);
    }
}
