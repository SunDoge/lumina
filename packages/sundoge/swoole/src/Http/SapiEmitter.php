<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/28
 * Time: 下午11:39
 */

namespace SunDoge\Swoole\Http;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\SapiEmitter as BaseEmitter;
use Zend\Diactoros\Response\SapiEmitterTrait;
use RuntimeException;

class SapiEmitter extends BaseEmitter
{
    use SapiEmitterTrait;

    protected $emitter;

    public function __construct($emitter = null)
    {
        $this->emitter = $emitter;
    }

    public function emitThrough(ResponseInterface $psrResponse, $maxBufferLevel = null)
    {
//        if (headers_sent()) {
//            throw new RuntimeException('Unable to emit response; headers already sent');
//        }

        $psrResponse = $this->injectContentLength($psrResponse);

//        $this->emitStatusLine($psrResponse);
        $reasonPhrase = $psrResponse->getReasonPhrase();

        $http = explode(' ', sprintf(
            'HTTP/%s %d%s',
            $psrResponse->getProtocolVersion(),
            $psrResponse->getStatusCode(),
            ($reasonPhrase ? ' ' . $reasonPhrase : '')
        ));
//        $this->emitter->header($http[0], $http[1]);

        foreach ($psrResponse->getHeaders() as $header => $values) {
            $name = $this->filterHeader($header);
//            $first = true;
            foreach ($values as $value) {
//                $res->header(sprintf(
//                    '%s: %s',
//                    $name,
//                    $value
//                ), $first);
//                $first = false;
                $this->emitter->header($name, $value);
            }
        }


        $this->flush($maxBufferLevel);
        $this->emitter->end($psrResponse->getBody());
    }

    public function emit(ResponseInterface $response, $maxBufferLevel = null)
    {
        if (headers_sent()) {
            throw new RuntimeException('Unable to emit response; headers already sent');
        }

        $response = $this->injectContentLength($response);

        $this->emitStatusLine($response);
        $this->emitHeaders($response);
        $this->flush($maxBufferLevel);
        $this->emitBody($response);

    }

    private function emitBody(ResponseInterface $response)
    {
        echo $response->getBody();
    }
}