<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/14
 * Time: 下午7:30
 */

require __DIR__ . '/../vendor/autoload.php';

$app = new SunDoge\Swoole\Application(
    realpath(__DIR__ . '/../')
);



$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__ . '/../routes/routes.php';
});

return $app;