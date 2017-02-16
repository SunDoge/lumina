<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/14
 * Time: 下午7:23
 */

require __DIR__.'/../bootstrap/app.php';

//$app['app'] = $app;
//
//with(new Illuminate\Events\EventServiceProvider($app))->register();
//with(new Illuminate\Routing\RoutingServiceProvider($app))->register();
//
//$basePath = str_finish(dirname(__FILE__), '/');
//require $basePath.'../routes/web.php';
//

//dd($app);

//$request = Illuminate\Http\Request::createFromGlobals();
//$response = $app['router']->dispatch($request);
//$response = $app->dispatch($request);

//echo (string) $response;
//$response->send();
$app->createDispatcher();
$http = new swoole_http_server("127.0.0.1", 8888);
$http->on('request', function ($request, $response) use ($app) {

    $app->onRequest($request, $response, function ($response, $data) {
        $response->end($data);
    });

//    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});
$http->start();
