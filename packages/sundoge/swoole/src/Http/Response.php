<?php

/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/16
 * Time: 下午7:15
 */

namespace SunDoge\Swoole\Http;

class Response
{
    protected $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

}