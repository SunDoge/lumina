<?php

/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/16
 * Time: 下午7:15
 */

namespace SunDoge\Swoole\Http;


class Request
{
    protected $request;


    public function __construct($request)
    {
        $this->request = $request;
    }
}