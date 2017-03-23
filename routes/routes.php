<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/15
 * Time: 下午11:01
 */

$app->get('/', function () {
    return 'lumina';
});

$app->get('/test', function () {
    return 'test';
});

$app->get('/controller', 'TestController@index');