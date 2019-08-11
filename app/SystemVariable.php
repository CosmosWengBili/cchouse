<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SystemVariable extends Model
{
    const VARIABLES = [
        ['name' => '維修清潔狀態通知天數', 'code' => 'MaintenanceNotifyRequiredDays', 'type' => 'integer', 'defaultValue' => 10],
    ];

    public static function get(string $code) {
        $option = self::searchArray($code, 'code', self::VARIABLES);
        if (is_null($option)) {
            return null;
        }

        $variable = self::where('code', $code)->first();
        if (is_null($variable)) {
            return $option['defaultValue'];
        }

        return self::castValue($option['type'], $variable->value);
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

    private static function searchArray($value, $key, $array) {
        foreach ($array as $val) {
            if ($val[$key] == $value) {
                return $val;
            }
        }
        return null;
    }
}
