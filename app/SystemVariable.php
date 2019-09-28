<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SystemVariable extends Model
{
    const VARIABLES = [
        ['name' => '維修清潔狀態通知天數', 'code' => 'MaintenanceNotifyRequiredDays', 'type' => 'integer', 'defaultValue' => 10, 'group' => 'Maintenance', 'order' => 1],
        ['name' => '結帳鎖', 'code' => 'PaymentLock', 'type' => 'boolean', 'defaultValue' => true, 'group' => 'Payment', 'order' => 1],

        # Group `Reversal`
        ['name' => '履約保證金', 'code' => '履約保證金', 'type' => 'string', 'defaultValue' => '履約保證金', 'group' => 'Reversal', 'order' => 1],
        ['name' => '更改繳款日租金', 'code' => '更改繳款日租金', 'type' => 'string', 'defaultValue' => '更改繳款日租金', 'group' => 'Reversal', 'order' => 2],
        ['name' => '仲介費', 'code' => '仲介費', 'type' => 'string', 'defaultValue' => '仲介費', 'group' => 'Reversal', 'order' => 3],
        ['name' => '顧問費', 'code' => '顧問費', 'type' => 'string', 'defaultValue' => '顧問費', 'group' => 'Reversal', 'order' => 4],
        ['name' => '管理費', 'code' => '管理費', 'type' => 'string', 'defaultValue' => '管理費', 'group' => 'Reversal', 'order' => 5],
        ['name' => '清潔費', 'code' => '清潔費', 'type' => 'string', 'defaultValue' => '清潔費', 'group' => 'Reversal', 'order' => 6],
        ['name' => '清潔費(公司)', 'code' => '清潔費(公司)', 'type' => 'string', 'defaultValue' => '清潔費(公司)', 'group' => 'Reversal', 'order' => 7],
        ['name' => '瓦斯費', 'code' => '瓦斯費', 'type' => 'string', 'defaultValue' => '瓦斯費', 'group' => 'Reversal', 'order' => 8],
        ['name' => '管理服務費', 'code' => '管理服務費', 'type' => 'string', 'defaultValue' => '管理服務費', 'group' => 'Reversal', 'order' => 9],
        ['name' => '滯納金', 'code' => '滯納金', 'type' => 'string', 'defaultValue' => '滯納金', 'group' => 'Reversal', 'order' => 10],
        ['name' => '轉房費', 'code' => '轉房費', 'type' => 'string', 'defaultValue' => '轉房費', 'group' => 'Reversal', 'order' => 11],
        ['name' => '換約費', 'code' => '換約費', 'type' => 'string', 'defaultValue' => '換約費', 'group' => 'Reversal', 'order' => 12],
        ['name' => '轉換承租人', 'code' => '轉換承租人', 'type' => 'string', 'defaultValue' => '轉換承租人', 'group' => 'Reversal', 'order' => 13],
        ['name' => '維修費', 'code' => '維修費', 'type' => 'string', 'defaultValue' => '維修費', 'group' => 'Reversal', 'order' => 14],
        ['name' => '車馬費', 'code' => '車馬費', 'type' => 'string', 'defaultValue' => '車馬費', 'group' => 'Reversal', 'order' => 15],
        ['name' => '放鳥費', 'code' => '放鳥費', 'type' => 'string', 'defaultValue' => '放鳥費', 'group' => 'Reversal', 'order' => 16],
        ['name' => '磁扣費', 'code' => '磁扣費', 'type' => 'string', 'defaultValue' => '磁扣費', 'group' => 'Reversal', 'order' => 17],
        ['name' => '水雜費', 'code' => '水雜費', 'type' => 'string', 'defaultValue' => '水雜費', 'group' => 'Reversal', 'order' => 18],
        ['name' => '垃圾費', 'code' => '垃圾費', 'type' => 'string', 'defaultValue' => '垃圾費', 'group' => 'Reversal', 'order' => 19],
        ['name' => '第四台', 'code' => '第四台', 'type' => 'string', 'defaultValue' => '第四台', 'group' => 'Reversal', 'order' => 20],
        ['name' => '鍋爐費', 'code' => '鍋爐費', 'type' => 'string', 'defaultValue' => '鍋爐費', 'group' => 'Reversal', 'order' => 21],
        ['name' => '租金', 'code' => '租金', 'type' => 'string', 'defaultValue' => '租金', 'group' => 'Reversal', 'order' => 22],
        ['name' => '電費', 'code' => '電費', 'type' => 'string', 'defaultValue' => '電費', 'group' => 'Reversal', 'order' => 23],
        # Group `Reversal`
    ];

    protected $fillable = ['group', 'code', 'value', 'order'];

    public static function get(string $group, string $code)
    {
        $defaultVariable = self::search($group, $code);
        if (is_null($defaultVariable)) {
            return null;
        }

        $variable = self::where(['group' => $group, 'code' => $code])->first();
        if (is_null($variable)) {
            return $defaultVariable['defaultValue'];
        }

        return self::castValue($defaultVariable['type'], $variable->value);
    }

    public static function groups()
    {
        return array_unique(array_map(function ($group) {
            return $group['group'];
        }, self::VARIABLES));
    }

    private static function castValue(string $type, string $valueString)
    {
        switch ($type) {
            case 'integer':
                return intval($valueString);
            case 'float':
                return floatval($valueString);
            case 'boolean':
                return boolval($valueString);
            case 'datetime':
                return Carbon::parse($valueString);
            default:
                return $valueString;
        }
    }

    private static function search(string $group, string $code)
    {
        foreach (self::VARIABLES as $variable) {
            if ($variable['code'] == $code && $variable['group'] == $group) {
                return $variable;
            }
        }

        return null;
    }

    /**
     * Scope a query to only include variables of a given group.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfGroup($query, $group)
    {
        return $query->where('group', $group)->orderBy('order');
    }
}
