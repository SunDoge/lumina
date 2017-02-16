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

    public function header($key, $value)
    {
        $this->response->header($key, $value);

        return $this;
    }

    public function cookie($name, $value, $minutes, $path, $domain, $secure, $httpOnly)
    {
        $this->response->cookie($name, $value, $minutes, $path, $domain, $secure, $httpOnly);

        return $this;
    }

    public function status($statusCode)
    {
        $this->response->status($statusCode);

        return $this;
    }

    public function gzip($level)
    {
        $this->response->gzip($level);

        return $this;
    }

    public function write($data)
    {
        $this->response->write($data);

        return $this;
    }

    public function download($fileName)
    {
        $this->response->sendfile($fileName);

        return $this;
    }

    public function sendfile($fileName)
    {
        $this->response->sendfile($fileName);

        return $this;
    }

    public function end($html)
    {
        $this->response->end($html);

        return $this;
    }
}