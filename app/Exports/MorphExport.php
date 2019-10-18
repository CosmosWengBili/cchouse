<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MorphExport implements FromCollection, WithHeadings
{
    private $model;
    private $isExample;
    private $makeHidden = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'email_verified_at',
        'password',
        'remember_token'
    ];
    private $selectedColumns;

    public function __construct(string $model, bool $isExample = false)
    {
        $this->model = $model;
        $this->isExample = $isExample;

        $this->selectedColumns = array_diff(
            Schema::getColumnListing((new $this->model())->getTable()),
            $this->makeHidden
        );
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->isExample) {
            return collect([]);
        }

        // 取表名
        $tableName = (new $this->model())->getTable();
        // 取得client 給的 querystring
        $qsArr = request()->all();
        if (! empty($qsArr)) {
            foreach ($qsArr as $attribute => $value) {
                // 進入則表示該table內有該 attribute
                if (Schema::hasColumn($tableName, $attribute)) {
                    if (! isset($builder)) {
                        $builder = $this->model::where($attribute,$value);
                    } else {
                        $builder->where($attribute,$value);
                    }
                }
            }

            isset($builder) and ($data = $builder->get());
        }

        $data = $data->map->only($this->selectedColumns);
        return isset($data)
            ? $data
            : $this->model::all();
    }

    public function headings(): array
    {
        // From App\Model to Model
        $model = substr($this->model, 4);
        $displayColumns = array_map(function($column)use($model){
            return __('model.'.$model.'.'.$column);
        }, $this->selectedColumns);
        return $displayColumns;
    }
}
