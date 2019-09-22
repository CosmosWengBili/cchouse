<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DebtCollectionExport implements FromCollection, WithHeadings
{
    private $date;

    public function __construct(Carbon $date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            '行政區',
            '物件',
            '房號',
            '租金',
            '管理費',
            '垃圾費',
            '水雜費',
            '服務費',
            '履保金',
            '已收',
            '110v',
            '220v',
            '承租人',
            '電話',
            '合約起',
            '合約迄',
            '續約',
            '繳款',
            '遲繳',
            '6月',
            '7月',
            '8月',
            '電費',
            '合計',
            '催收回報'
        ];
    }
}
