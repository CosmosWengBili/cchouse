<?php
/**
 * Created by PhpStorm.
 * User: lichengxiu
 * Date: 2019-10-05
 * Time: 13:28
 */

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ShareholderTotalSheet implements WithHeadings, WithTitle, FromArray
{
    /** @var array key對應到的value必須為數字 */
    const MUST_COUNT_TOTAL = [
        'carry_forward', // 月結單金額
        'actual_money', // 實收金額
        'money', // 應付業主
        'amount_receivable', // 應收金額
        'exchange_fee' // 匯費
    ];
    /** @var array key對應到的value須為字串 */
    const MUST_GROUP_BY_COUNT = [
        'method', // 方式
    ];

    /** @var int  */
    private $month;
    /** @var int  */
    private $year;
    /** @var string $transferFrom 為空則為總表 */
    private $transferFrom;
    /** @var array  */
    private $excelData;

    public function __construct(int $year, int $month, array $excelData, string $transferFrom='')
    {
        $this->month = $month;
        $this->year  = $year;
        $this->excelData = $excelData;
        $this->transferFrom = $transferFrom;
    }

    public function array(): array
    {
        $transferFrom = $this->transferFrom;
        if ($transferFrom !== '') {
            // 在這裡根據匯款銀行作分表
            $rows = collect($this->excelData)->filter(function ($value, $key) use ($transferFrom) {
                return collect($value)->contains($transferFrom);
            })->toArray();
        } else {
            $rows = $this->excelData;
        }

        usort($rows, function($a, $b) {
            return $a[1] <=> $b[1];
        });
        $rows = $this->appendCountTotal($rows);
        

        return $rows;
    }

    public function headings(): array
    {
        /*
         * 使用多表頭 需要注意 不能直接填寫空值 eg. $str=''
         * 需要改成 $str=' '
         */

        $field = $this->transferFrom === '' ? '業主總帳&匯款總表': "{$this->transferFrom}匯款總表";
        $headings[] = [
            ' ',
            ' ',
            ' ',
            "$field",
            ' ',
            "{$this->year}年{$this->month}月",
            ' ',
            "{$this->month}月出帳",
        ];
        $headings[] = [
            '物件屬性',
            '物件代碼',
            '物件地址',
            '月結單金額',
            '實收金額',
            '業主',
            '應付業主',
            '方式',
            '匯出銀行',
            '應收金額',
            '匯費',
            '備註',
            '銀行/分行',
            '銀行/分行代碼',
            '戶名（受款人）',
            '帳號',
            '聯絡方式',
            '郵寄/傳真',
    ];

        return collect($headings)->toArray();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->transferFrom === '' ? '總表': $this->transferFrom;
    }

    /**
     * 根據excel內的金額欄位計算總和
     * 根據方式(method)做總和
     * @param array $rows
     *
     * @return array
     */
    private function appendCountTotal(array $rows)
    {
        // 需要做總合計算的欄位名稱
        $countInKey = self::MUST_COUNT_TOTAL;
        // 需要根據不同“字串” 做統計的欄位名稱
        $groupCountByKeys = self::MUST_GROUP_BY_COUNT;
        // copied from row[0] and clean data
        $copied = collect($rows[0])->map(function($item, $key){ return ''; });

        $countByMethod = [];
        // 計算 total
        foreach ($rows as $key => $row) {
            // 計算總和
            foreach ($countInKey as $row_key) {
                empty($copied[$row_key]) and ($copied[$row_key] = 0);
                $copied[$row_key] += $row[$row_key];
            }

            // 根據method計算總和
            foreach ($groupCountByKeys as $groupCountByKey) {
                ! isset($countByMethod[$row[$groupCountByKey]]) and ($countByMethod[$row[$groupCountByKey]] = 0);
                $countByMethod[$row[$groupCountByKey]] +=  $row['money'];
            }
        }

        $rows[] = $copied;
        $rows[] = array_keys($countByMethod);
        $rows[] = $countByMethod;

        return $rows;
    }

}
