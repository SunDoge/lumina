<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/28
 * Time: 下午11:31
 */

namespace SunDoge\Swoole\Http;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse as BaseResponse;


class JsonResponse extends BaseResponse implements ResponseInterface
{

}