<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2019-09-25
 * Time: 10:21
 */
if (! function_exists('getClassNameWithoutNamespace')) {
    /**
     * Support you to get a class name of an instance without namespace.
     * Example:
     *  getClassNameWithoutNamespace( Landlord::first() )
     *  this will return a string 'Landlord'
     *
     * @param $instance
     *
     * @return string
     * @throws ReflectionException
     */
    function getClassNameWithoutNamespace($instance)
    {
        return (new \ReflectionClass($instance))->getShortName();
    }
}

if (! function_exists('getLayer')) {
    /**
     * Used in blade for getting the layer.
     * @param string $relation
     *
     * @return string
     */
    function getLayer(string $relation)
    {
        $layer = explode('.', $relation);
        $layer = \Illuminate\Support\Str::snake(last($layer));
        $layer = \Illuminate\Support\Str::plural($layer);

        return $layer;
    }
}

if (! function_exists('makeArrayToQueryString')) {
    /**
     *
     * @param array $qs 要轉成query string 的陣列
     *
     * @return string
     */
    function makeArrayToQueryString(array $qs=[])
    {
        if (! empty($qs)) {
            $tmp = collect($qs)->map(function ($value, $key) {
                return "{$key}={$value}";
            })
                ->toArray();

            return implode('&', $tmp);
        }

        return '';
    }
}

if (! function_exists('makeDateFormatByKeys')) {
    /**
     *
     * @param array $qs 要轉成query string 的陣列
     *
     * @return string
     */
    function makeDateFormatByKeys(array $array = [], array $keys = [], $format ='Y-m-d')
    {
        if (
            $array && is_array($array) &&
            $keys && is_array($keys)
        ) {
            foreach ($keys as $key) {
                if (isset($array[$key])) {
                    $array[$key] = \Carbon\Carbon::parse($array[$key])->format($format);
                }
            }
        }

        return $array;
    }
}

if (! function_exists('getSqlLogs')) {
    function getSqlLogs()
    {
        $events =  DB::getQueryLog();
        $logs   = [];
        foreach ($events as $event) {
            $time   = $event['time']; // ms
            $sql    = str_replace('?', "'%s'", $event['query']);
            $log    = vsprintf($sql, $event['bindings']);
            $logs[] = '['.date('Y-m-d H:i:s').']'.'['.(int)$time.'] '.$log ;
        }

        return $logs;
    }
}
