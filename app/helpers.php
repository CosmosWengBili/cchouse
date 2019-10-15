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
    function getClassNameWithoutNamespace($instance) {
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
    function getLayer(string $relation) {
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
    function makeArrayToQueryString(array $qs=[]) {
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
