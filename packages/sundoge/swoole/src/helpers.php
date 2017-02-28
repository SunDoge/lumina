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

if (! function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param  string  $content
     * @param  int     $status
     * @param  array   $headers
     * @return
     */
    function response($content = '', $status = 200, array $headers = [])
    {
        $factory = new SunDoge\Swoole\Http\ResponseFactory;

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }
}