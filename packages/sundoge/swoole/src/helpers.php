<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/16
 * Time: 下午4:37
 */

//use Illuminate\Support\Str;
use Illuminate\Container\Container;
//use Illuminate\Contracts\Bus\Dispatcher;

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string  $make
     * @param  array   $parameters
     * @return mixed|\SunDoge\Swoole\Application
     */
    function app($make = null, $parameters = [])
    {
        if (is_null($make)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($make, $parameters);
    }
}