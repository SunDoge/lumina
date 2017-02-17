<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/17
 * Time: 下午10:25
 */

$app = include('app.php');

$app->createDispatcher();

$http = new swoole_http_server("127.0.0.1", 8888);

$action = function ($request, $response) use ($app) {

    $app->onRequest($request, $response, function ($response, $data) {
        $response->end($data);
    });

};


$http->on('request', $action);

$http->start();