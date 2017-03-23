<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/16
 * Time: 下午11:21
 */

namespace SunDoge\Lumina\Http;


class HttpServer
{
    protected $server;

    public function __construct($httpServer)
    {
        $this->server = $httpServer;
    }

    public function on($event, $callback)
    {
        $this->server->on($event, $callback);
    }

    public function start()
    {
        $this->server->start();
    }
}