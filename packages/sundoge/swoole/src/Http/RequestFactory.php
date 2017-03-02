<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/3/2
 * Time: 下午6:01
 */

namespace SunDoge\Swoole\Http;

//use Zend\Diactoros\ServerRequest;

use Zend\Diactoros\ServerRequestFactory;

class RequestFactory extends ServerRequestFactory
{
    public static function from($request)
    {
//        $headers = isset($request->header) ? array_change_key_case($request->header, CASE_UPPER) : [];
        $server = isset($request->server) ? array_change_key_case($request->server, CASE_UPPER) : [];
        $files = isset($request->files) ? array_change_key_case($request->files, CASE_UPPER) : [];
        $cookies = isset($request->cookie) ? array_change_key_case($request->cookie, CASE_UPPER) : [];
        $query = isset($request->get) ? array_change_key_case($request->get, CASE_UPPER) : [];
        $body = isset($request->post) ? array_change_key_case($request->post, CASE_UPPER) : [];

        $server  = static::normalizeServer($server);
        $files   = static::normalizeFiles($files);
        $headers = static::marshalHeaders($server);

        return new Request(
            $server,
            $files,
            static::marshalUriFromServer($server, $headers),
            static::get('REQUEST_METHOD', $server, 'GET'),
            'php://input',
            $headers,
            $cookies ?: $_COOKIE,
            $query ?: $_GET,
            $body ?: $_POST,
            static::marshalProtocolVersion($server)
        );
    }

    private static function marshalProtocolVersion(array $server)
    {
        if (! isset($server['SERVER_PROTOCOL'])) {
            return '1.1';
        }

        if (! preg_match('#^(HTTP/)?(?P<version>[1-9]\d*(?:\.\d)?)$#', $server['SERVER_PROTOCOL'], $matches)) {
            throw new UnexpectedValueException(sprintf(
                'Unrecognized protocol version (%s)',
                $server['SERVER_PROTOCOL']
            ));
        }

        return $matches['version'];
    }

}