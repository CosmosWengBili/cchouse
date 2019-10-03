<?php

namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class RelationExport implements FromCollection, WithHeadings
{
    private $model;
    private $parent;
    private $relation;

    public function __construct(string $model, string $id, string $relation)
    {
        $this->parent = $model::find($id);
        $this->model = $model;
        $this->relation = $relation;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->buildCollection();
    }

    public function headings(): array
    {
        $instance = $this->collection()->first();
        if (is_null($instance)) { // fix $instance maybe null. Try using relation to get model.
            $relationModel = 'App\\' . ucfirst(Str::camel(Str::singular($this->relation)));
        } else {
            $relationModel = get_class($instance);
        }
        $table = (new $relationModel())->getTable();
        $columnNames = array_values(Schema::getColumnListing($table));
        return $columnNames;
    }

    private function buildCollection(): Collection {
        $relation = $this->relation;
        $data = $this->parent->$relation();
        $type = get_class($data);

        switch ($type) {
            case 'Illuminate\Database\Eloquent\Collection':
                return $data;
            default:
                return $data->get();
        }
    }
}
