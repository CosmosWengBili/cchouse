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

    public function __construct(string $model, bool $isExample = false)
    {
        $this->model = $model;
        $this->isExample = $isExample;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->isExample) {
            return collect([]);
        }
        return $this->model::all();
    }

    public function headings(): array
    {
        return array_values(
            array_diff(
                Schema::getColumnListing((new $this->model())->getTable()),
                $this->makeHidden
            )
        );
    }
}
