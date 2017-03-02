<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/3/2
 * Time: 下午7:19
 */

namespace SunDoge\Swoole\Exceptions;

use RuntimeException;
use SunDoge\Swoole\Http\Response;

class HttpResponseException extends RuntimeException
{
    /**
     * The underlying response instance.
     *
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    /**
     * Create a new HTTP response exception instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get the underlying response instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}