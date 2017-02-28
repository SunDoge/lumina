<?php

/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/16
 * Time: 下午7:15
 */

namespace SunDoge\Swoole\Http;

use Closure;
use Zend\Diactoros\ServerRequest as ZendRequest;

class Request extends ZendRequest
{
    /**
     * Set the route resolver callback.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function setRouteResolver(Closure $callback)
    {
        $this->routeResolver = $callback;

        return $this;
    }

    /**
     * Get the route handling the request.
     *
     * @param  string|null  $param
     *
     * @return
     */
    public function route($param = null)
    {
        $route = call_user_func($this->getRouteResolver());

        if (is_null($route) || is_null($param)) {
            return $route;
        } else {
            return $route->parameter($param);
        }
    }

    /**
     * Set the user resolver callback.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function setUserResolver(Closure $callback)
    {
        $this->userResolver = $callback;

        return $this;
    }
}