<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SystemVariable extends Model
{
    const VARIABLES = [
        ['name' => '維修清潔狀態通知天數', 'code' => 'MaintenanceNotifyRequiredDays', 'type' => 'integer', 'defaultValue' => 10, 'group' => 'Maintenance', 'order' => 1],
    ];

    protected $fillable = ['group', 'code', 'value', 'order'];

    public static function get(string $group, string $code) {
        $defaultVariable = self::search($group, $code);
        if (is_null($defaultVariable)) {
            return null;
        }

        $variable = self::where(['group' => $group, 'code' => $code])->first();
        if (is_null($variable)) {
            return $variable['defaultValue'];
        }

        return self::castValue($defaultVariable['type'], $variable->value);
    }

    private static function castValue(string $type, string $valueString) {
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

    private static function search(string $group, string $code) {
        foreach (self::VARIABLES as $variable) {
            if ($variable['code'] == $code && $variable['group'] == $group) {
                return $variable;
            }
        }

        return null;
    }
}
