<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\ReceiptExportSheet;

class ReceiptExport implements WithMultipleSheets 
{
    private $receiptData;
    private $buildingData;

    public function __construct($receiptData, $buildingData)
    {
        $this->receiptData = $receiptData;
        $this->buildingData = $buildingData;
    }
 
    public function sheets(): array
    {
        return [
            new ReceiptExportSheet($this->receiptData, '收據'),
            new ReceiptExportSheet($this->buildingData, '物件'),
        ];
    }
}