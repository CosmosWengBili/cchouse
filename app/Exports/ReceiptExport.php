<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReceiptExport implements FromCollection, WithHeadings
{
    private $data;
    private $type;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function collection()
    {
        if( $this->type == 'invoice' ){
            $header = config('enums.invoice_en');
        }
        else{
            $header = config('enums.receipt_en');
        }
        

        $array = array_map(function($v)use($header){
            return array_replace(array_flip($header), $v);
        }, $this->data);
        return collect($array);
    }

    public function headings(): array
    {
        if( $this->type == 'invoice' ){
            return config('enums.invoice');
        }
        else{
            return config('enums.receipt');
        }
    }
}