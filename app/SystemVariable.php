<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


class SystemVariable extends Model
{
    protected $fillable = ['group', 'code', 'value', 'order'];

    public static function variables() {
        return collect([
            [ // Maintenance Group
                ['name' => '維修清潔狀態通知天數', 'code' => 'MaintenanceNotifyRequiredDays', 'type' => 'integer', 'defaultValue' => 10, 'group' => 'Maintenances', 'order' => 0],
            ],
            [ // Payment Group
                ['name' => '結帳鎖', 'code' => 'PaymentLock', 'type' => 'boolean', 'defaultValue' => true, 'group' => 'Payment', 'order' => 0],
            ],
            [ // Payment Group
                ['name' => '押金設算息', 'code' => 'depositRate', 'type' => 'float', 'defaultValue' => 0.00087, 'group' => 'Tenant', 'order' => 1],
            ],
            // Reversal Group
            collect(config('finance.reversal'))->map(function($v, $i) {
                return ['name' => $v, 'code' => $v, 'type' => 'string', 'defaultValue' => $v, 'group' => 'Reversal', 'order' => $i];
            })->all(),
        ])
            ->flatMap(function ($v) { return $v; })
            ->all();
    }

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
        }, self::variables()));
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
        foreach (self::variables() as $variable) {
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
