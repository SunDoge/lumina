<?php

/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/16
 * Time: 下午7:15
 */

namespace SunDoge\Lumina\Http;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response as ZendResponse;

class Response extends ZendResponse implements ResponseInterface
{
    protected $response;

//
//    public function header($key, $value)
//    {
//        $this->response->header($key, $value);
//
//        return $this;
//    }
//
//    public function cookie($name, $value, $minutes, $path, $domain, $secure, $httpOnly)
//    {
//        $this->response->cookie($name, $value, $minutes, $path, $domain, $secure, $httpOnly);
//
//        return $this;
//    }
//
//    public function status($statusCode)
//    {
//        $this->response->status($statusCode);
//
//        return $this;
//    }
//
//    public function gzip($level)
//    {
//        $this->response->gzip($level);
//
//        return $this;
//    }
//
//    public function write($data)
//    {
//        $this->response->write($data);
//
//        return $this;
//    }
//
//    public function download($fileName)
//    {
//        $this->response->sendfile($fileName);
//
//        return $this;
//    }
//
//    public function sendfile($fileName)
//    {
//        $this->response->sendfile($fileName);
//
//        return $this;
//    }
//
//    public function end($html)
//    {
//        $this->response->end($html);
//
//        return $this;
//    }
}