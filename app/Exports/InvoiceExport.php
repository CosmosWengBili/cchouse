<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoiceExport implements FromCollection, WithHeadings
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $header = config('enums.invoice_en');
        $header = array_merge($header, ['data_table_class', 'data_receipt_id']);
        $array = array_map(function($v)use($header){
            return array_replace(array_flip($header), $v);
        }, $this->data);
        return collect($array);
    }

    public function headings(): array
    {
        return array_merge(config('enums.invoice'), ['資料來源(程式用)', '發票ID(程式用)']);
    }
}