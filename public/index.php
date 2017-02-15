<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/14
 * Time: 下午7:23
 */

require __DIR__.'/../bootstrap/autoload.php';

$app = new Illuminate\Container\Container();

Illuminate\Support\Facades\Facade::setFacadeApplication($app);

$app['app'] = $app;

with(new Illuminate\Events\EventServiceProvider($app))->register();
with(new Illuminate\Routing\RoutingServiceProvider($app))->register();

$basePath = str_finish(dirname(__FILE__), '/');
require $basePath.'../routes/web.php';

$request = Illuminate\Http\Request::createFromGlobals();
$response = $app['router']->dispatch($request);

$response->send();