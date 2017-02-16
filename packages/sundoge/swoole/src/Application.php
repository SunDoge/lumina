<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/15
 * Time: 下午7:14
 */

namespace SunDoge\Swoole;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use SunDoge\Swoole\Concerns\RegistersExceptionHandlers;
use SunDoge\Swoole\Concerns\RoutesRequests;

class Application extends Container
{
    use RoutesRequests, RegistersExceptionHandlers;

    protected $basePath;

    protected $ranServiceBinders = [];


    public function __construct($basePath = null)
    {
        $this->basePath = $basePath;

        $this->bootstrapContainer();

        $this->registerErrorHandling();
    }

    protected function bootstrapContainer()
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->instance('path', $this->path());

        $this->instance('SunDoge\Swoole\Application', $this);

        $this->registerContainerAliases();
    }

    public function path()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'app';
    }

    protected function prepareRequest(Request $request)
    {
        $request->setUserResolver(function () {
            return $this->make('auth')->user();
        })->setRouteResolver(function () {
            return $this->currentRoute;
        });

        return $request;
    }

    protected function registerContainerAliases()
    {
        $this->aliases = [
            'Illuminate\Contracts\Foundation\Application' => 'app',
            'Illuminate\Contracts\Auth\Factory' => 'auth',
            'Illuminate\Contracts\Auth\Guard' => 'auth.driver',
            'Illuminate\Contracts\Cache\Factory' => 'cache',
            'Illuminate\Contracts\Cache\Repository' => 'cache.store',
            'Illuminate\Contracts\Config\Repository' => 'config',
            'Illuminate\Container\Container' => 'app',
            'Illuminate\Contracts\Container\Container' => 'app',
//            'Illuminate\Database\ConnectionResolverInterface' => 'db',
//            'Illuminate\Database\DatabaseManager' => 'db',
            'Illuminate\Contracts\Encryption\Encrypter' => 'encrypter',
            'Illuminate\Contracts\Events\Dispatcher' => 'events',
            'Illuminate\Contracts\Hashing\Hasher' => 'hash',
            'log' => 'Psr\Log\LoggerInterface',
            'Illuminate\Contracts\Queue\Factory' => 'queue',
            'Illuminate\Contracts\Queue\Queue' => 'queue.connection',
            'request' => 'Illuminate\Http\Request',
            'Laravel\Lumen\Routing\UrlGenerator' => 'url',
            'Illuminate\Contracts\Validation\Factory' => 'validator',
            'Illuminate\Contracts\View\Factory' => 'view',
        ];
    }

//    public $availableBindings = [
//        'auth' => 'registerAuthBindings',
//        'auth.driver' => 'registerAuthBindings',
//        'Illuminate\Contracts\Auth\Guard' => 'registerAuthBindings',
//        'Illuminate\Contracts\Auth\Access\Gate' => 'registerAuthBindings',
//        'Illuminate\Contracts\Broadcasting\Broadcaster' => 'registerBroadcastingBindings',
//        'Illuminate\Contracts\Bus\Dispatcher' => 'registerBusBindings',
//        'cache' => 'registerCacheBindings',
//        'cache.store' => 'registerCacheBindings',
//        'Illuminate\Contracts\Cache\Factory' => 'registerCacheBindings',
//        'Illuminate\Contracts\Cache\Repository' => 'registerCacheBindings',
//        'composer' => 'registerComposerBindings',
//        'config' => 'registerConfigBindings',
//        'db' => 'registerDatabaseBindings',
//        'Illuminate\Database\Eloquent\Factory' => 'registerDatabaseBindings',
//        'encrypter' => 'registerEncrypterBindings',
//        'Illuminate\Contracts\Encryption\Encrypter' => 'registerEncrypterBindings',
//        'events' => 'registerEventBindings',
//        'Illuminate\Contracts\Events\Dispatcher' => 'registerEventBindings',
//        'files' => 'registerFilesBindings',
//        'hash' => 'registerHashBindings',
//        'Illuminate\Contracts\Hashing\Hasher' => 'registerHashBindings',
//        'log' => 'registerLogBindings',
//        'Psr\Log\LoggerInterface' => 'registerLogBindings',
//        'queue' => 'registerQueueBindings',
//        'queue.connection' => 'registerQueueBindings',
//        'Illuminate\Contracts\Queue\Factory' => 'registerQueueBindings',
//        'Illuminate\Contracts\Queue\Queue' => 'registerQueueBindings',
//        'request' => 'registerRequestBindings',
//        'Psr\Http\Message\ServerRequestInterface' => 'registerPsrRequestBindings',
//        'Psr\Http\Message\ResponseInterface' => 'registerPsrResponseBindings',
//        'Illuminate\Http\Request' => 'registerRequestBindings',
//        'translator' => 'registerTranslationBindings',
//        'url' => 'registerUrlGeneratorBindings',
//        'validator' => 'registerValidatorBindings',
//        'Illuminate\Contracts\Validation\Factory' => 'registerValidatorBindings',
//        'view' => 'registerViewBindings',
//        'Illuminate\Contracts\View\Factory' => 'registerViewBindings',
//    ];

}