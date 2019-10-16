<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;

class ReceiptExportSheet implements WithHeadings, WithTitle, FromArray
{
  private $data;

  public function __construct($data, $sheetName)
  {
      $this->data = $data;
      $this->sheetName = $sheetName;
  }

  public function title(): string
  {
      return $this->sheetName;
  }

  public function array(): array
  {
      if( $this->sheetName == '物件' ){
        $header = config('enums.receipt_building_en');
      }
      else if( $this->sheetName == '收據' ){
        $header = config('enums.receipt_en');
      }

      $array = array_map(function($v)use($header){
          return array_replace(array_flip($header), $v);
      }, $this->data);
      return $array;
  }

  public function headings(): array
  {
    if( $this->sheetName == '物件' ){
      return config('enums.receipt_building');
    }
    else if( $this->sheetName == '收據' ){
      return config('enums.receipt');
    }
  }
}