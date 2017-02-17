<?php
/**
 * Created by PhpStorm.
 * User: sundoge
 * Date: 2017/2/15
 * Time: 下午8:12
 */

namespace SunDoge\Swoole\Concerns;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use FastRoute\Dispatcher;
use SunDoge\Swoole\Routing\Closure as RoutingClosure;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

trait RoutesRequests
{

    /**
     * All of the routes waiting to be registered.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * All of the named routes and URI pairs.
     *
     * @var array
     */
    public $namedRoutes = [];

    /**
     * All of the global middleware for the application.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * All of the route specific middleware short-hands.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    /**
     * The shared attributes for the current route group.
     *
     * @var array|null
     */
    protected $groupAttributes;

    /**
     * The current route being dispatched.
     *
     * @var array
     */
    protected $currentRoute;

    /**
     * The FastRoute dispatcher.
     *
     * @var \FastRoute\Dispatcher
     */
    protected $dispatcher;

    /**
     * Register a set of routes with a set of shared attributes.
     *
     * @param  array $attributes
     * @param  \Closure $callback
     * @return void
     */
    public function group(array $attributes, Closure $callback)
    {
        $parentGroupAttributes = $this->groupAttributes;

        if (isset($attributes['middleware']) && is_string($attributes['middleware'])) {
            $attributes['middleware'] = explode('|', $attributes['middleware']);
        }

        $this->groupAttributes = $attributes;

        call_user_func($callback, $this);

        $this->groupAttributes = $parentGroupAttributes;
    }

    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed $action
     * @return $this
     */
    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed $action
     * @return $this
     */
    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed $action
     * @return $this
     */
    public function put($uri, $action)
    {
        $this->addRoute('PUT', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed $action
     * @return $this
     */
    public function patch($uri, $action)
    {
        $this->addRoute('PATCH', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed $action
     * @return $this
     */
    public function delete($uri, $action)
    {
        $this->addRoute('DELETE', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed $action
     * @return $this
     */
    public function options($uri, $action)
    {
        $this->addRoute('OPTIONS', $uri, $action);

        return $this;
    }

    /**
     * Register a route for static files. Haven't decided yet.
     *
     * @param string $uri
     * @param mixed $action
     */
    public function asset($uri, $action)
    {
        $this->group(['middleware' => 'static'], function () {
//            $this->get('/', )
        });
    }

    /**
     * Add a route to the collection.
     *
     * @param  array|string $method
     * @param  string $uri
     * @param  mixed $action
     * @return void
     */
    public function addRoute($method, $uri, $action)
    {
        $action = $this->parseAction($action);

        if (isset($this->groupAttributes)) {
            if (isset($this->groupAttributes['prefix'])) {
                $uri = trim($this->groupAttributes['prefix'], '/') . '/' . trim($uri, '/');
            }

            if (isset($this->groupAttributes['suffix'])) {
                $uri = trim($uri, '/') . rtrim($this->groupAttributes['suffix'], '/');
            }

            $action = $this->mergeGroupAttributes($action);
        }

        $uri = '/' . trim($uri, '/');

        if (isset($action['as'])) {
            $this->namedRoutes[$action['as']] = $uri;
        }

        if (is_array($method)) {
            foreach ($method as $verb) {
                $this->routes[$verb . $uri] = ['method' => $verb, 'uri' => $uri, 'action' => $action];
            }
        } else {
            $this->routes[$method . $uri] = ['method' => $method, 'uri' => $uri, 'action' => $action];
        }
    }

    /**
     * Parse the action into an array format.
     *
     * @param  mixed $action
     * @return array
     */
    protected function parseAction($action)
    {
        if (is_string($action)) {
            return ['uses' => $action];
        } elseif (!is_array($action)) {
            return [$action];
        }

        if (isset($action['middleware']) && is_string($action['middleware'])) {
            $action['middleware'] = explode('|', $action['middleware']);
        }

        return $action;
    }

    /**
     * Merge the group attributes into the action.
     *
     * @param  array $action
     * @return array
     */
    protected function mergeGroupAttributes(array $action)
    {
        return $this->mergeNamespaceGroup(
            $this->mergeMiddlewareGroup($action)
        );
    }

    /**
     * Merge the namespace group into the action.
     *
     * @param  array $action
     * @return array
     */
    protected function mergeNamespaceGroup(array $action)
    {
        if (isset($this->groupAttributes['namespace']) && isset($action['uses'])) {
            $action['uses'] = $this->groupAttributes['namespace'] . '\\' . $action['uses'];
        }

        return $action;
    }

    /**
     * Merge the middleware group into the action.
     *
     * @param  array $action
     * @return array
     */
    protected function mergeMiddlewareGroup($action)
    {
        if (isset($this->groupAttributes['middleware'])) {
            if (isset($action['middleware'])) {
                $action['middleware'] = array_merge($this->groupAttributes['middleware'], $action['middleware']);
            } else {
                $action['middleware'] = $this->groupAttributes['middleware'];
            }
        }

        return $action;
    }

    /**
     * Add new middleware to the application.
     *
     * @param  Closure|array $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        if (!is_array($middleware)) {
            $middleware = [$middleware];
        }

        $this->middleware = array_unique(array_merge($this->middleware, $middleware));

        return $this;
    }

    /**
     * Define the route middleware for the application.
     *
     * @param  array $middleware
     * @return $this
     */
    public function routeMiddleware(array $middleware)
    {
        $this->routeMiddleware = array_merge($this->routeMiddleware, $middleware);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $response = $this->dispatch($request);

        if (count($this->middleware) > 0) {
            $this->callTerminableMiddleware($response);
        }

        return $response;
    }

    /**
     * Run the application and send the response.
     *
     * @param  SymfonyRequest|null $request
     * @return void
     */
    public function run($request = null)
    {
        $response = $this->dispatch($request);
//        dd($response);
//        dd(array_keys($response->headers->all())[0]);
        if ($response instanceof SymfonyResponse) {
            $response->send();
        } else {
            echo (string)$response;
//            print_r($response);
        }

        if (count($this->middleware) > 0) {
            $this->callTerminableMiddleware($response);
        }
    }

    public function onRequest($request, $response, $callback = null)
    {
        $uri = $request->server['request_uri'];
        $method = $request->server['request_method'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $res = $this->handleDispatcherResponse(
            $this->createDispatcher()->dispatch($method, $uri)
        );

        $content = $res->getContent();
        $headers = $res->headers->all();
        foreach ($headers as $key => $value) {
            $response->header($key, $value[0]);
        }
        $response->status($res->getStatusCode());
//        dd($res);
//        return call_user_func_array($callback, [$response, $content]);
        $response->end($content);
    }

    /**
     * Call the terminable middleware.
     *
     * @param  mixed $response
     * @return void
     */
    protected function callTerminableMiddleware($response)
    {
        if ($this->shouldSkipMiddleware()) {
            return;
        }

        $response = $this->prepareResponse($response);

        foreach ($this->middleware as $middleware) {
            if (!is_string($middleware)) {
                continue;
            }

            $instance = $this->make(explode(':', $middleware)[0]);

            if (method_exists($instance, 'terminate')) {
                $instance->terminate($this->make('request'), $response);
            }
        }
    }

    /**
     * Dispatch the incoming request.
     *
     * @param  SymfonyRequest|null $request
     * @return Response
     */
    public function dispatch($request = null)
    {
        list($method, $pathInfo) = $this->parseIncomingRequest($request);

        try {
            return $this->sendThroughPipeline($this->middleware, function () use ($method, $pathInfo) {
                if (isset($this->routes[$method . $pathInfo])) {
//                    print_r($this->routes);
                    return $this->handleFoundRoute([true, $this->routes[$method . $pathInfo]['action'], []]);
                }
//                print_r($this->routes);
                return $this->handleDispatcherResponse(
                    $this->createDispatcher()->dispatch($method, $pathInfo)
                );
            });
        } catch (Exception $e) {
            return $this->sendExceptionToHandler($e);
        } catch (Throwable $e) {
            return $this->sendExceptionToHandler($e);
        }
    }

    /**
     * Parse the incoming request and return the method and path info.
     *
     * @param  \Illuminate\Http\Request|null $request
     * @return array
     */
    protected function parseIncomingRequest($request)
    {
        if ($request) {
            $this->instance(Request::class, $this->prepareRequest($request));
            $this->ranServiceBinders['registerRequestBindings'] = true;

            return [$request->getMethod(), $request->getPathInfo()];
        } else {
            return [$this->getMethod(), $this->getPathInfo()];
        }
    }

    /**
     * Create a FastRoute dispatcher instance for the application.
     *
     * @return Dispatcher
     */
    public function createDispatcher()
    {
        return $this->dispatcher ?: \FastRoute\simpleDispatcher(function ($r) {
            foreach ($this->routes as $route) {
                $r->addRoute($route['method'], $route['uri'], $route['action']);
            }
        });
    }

    /**
     * Set the FastRoute dispatcher instance.
     *
     * @param  \FastRoute\Dispatcher $dispatcher
     * @return void
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle the response from the FastRoute dispatcher.
     *
     * @param  array $routeInfo
     * @return mixed
     */
    protected function handleDispatcherResponse($routeInfo)
    {
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new NotFoundHttpException;

            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedHttpException($routeInfo[1]);

            case Dispatcher::FOUND:
                return $this->handleFoundRoute($routeInfo);
        }
    }

    /**
     * Handle a route found by the dispatcher.
     *
     * @param  array $routeInfo
     * @return mixed
     */
    protected function handleFoundRoute($routeInfo)
    {
        $this->currentRoute = $routeInfo;

        $this['request']->setRouteResolver(function () {
            return $this->currentRoute;
        });

        $action = $routeInfo[1];

        // Pipe through route middleware...
        if (isset($action['middleware'])) {
            $middleware = $this->gatherMiddlewareClassNames($action['middleware']);

            return $this->prepareResponse($this->sendThroughPipeline($middleware, function () {
                return $this->callActionOnArrayBasedRoute($this['request']->route());
            }));
        }
//        dd($action);
        return $this->prepareResponse(
            $this->callActionOnArrayBasedRoute($routeInfo)
        );
    }

    /**
     * Call the Closure on the array based route.
     *
     * @param  array $routeInfo
     * @return mixed
     */
    protected function callActionOnArrayBasedRoute($routeInfo)
    {
        $action = $routeInfo[1];
//        dd($action);
        if (isset($action['uses'])) {
            return $this->prepareResponse($this->callControllerAction($routeInfo));
        }

        foreach ($action as $value) {
            if ($value instanceof Closure) {
                $closure = $value->bindTo(new RoutingClosure);
                break;
            }
        }

        try {
            return $this->prepareResponse($this->call($closure, $routeInfo[2]));
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Call a controller based route.
     *
     * @param  array $routeInfo
     * @return mixed
     */
    protected function callControllerAction($routeInfo)
    {
        $uses = $routeInfo[1]['uses'];

        if (is_string($uses) && !Str::contains($uses, '@')) {
            $uses .= '@__invoke';
        }

        list($controller, $method) = explode('@', $uses);

        if (!method_exists($instance = $this->make($controller), $method)) {
            throw new NotFoundHttpException;
        }

        if ($instance instanceof LumenController) {
            return $this->callLumenController($instance, $method, $routeInfo);
        } else {
            return $this->callControllerCallable(
                [$instance, $method], $routeInfo[2]
            );
        }
    }

    /**
     * Send the request through a Lumen controller.
     *
     * @param  mixed $instance
     * @param  string $method
     * @param  array $routeInfo
     * @return mixed
     */
    protected function callLumenController($instance, $method, $routeInfo)
    {
        $middleware = $instance->getMiddlewareForMethod($method);

        if (count($middleware) > 0) {
            return $this->callLumenControllerWithMiddleware(
                $instance, $method, $routeInfo, $middleware
            );
        } else {
            return $this->callControllerCallable(
                [$instance, $method], $routeInfo[2]
            );
        }
    }

    /**
     * Send the request through a set of controller middleware.
     *
     * @param  mixed $instance
     * @param  string $method
     * @param  array $routeInfo
     * @param  array $middleware
     * @return mixed
     */
    protected function callLumenControllerWithMiddleware($instance, $method, $routeInfo, $middleware)
    {
        $middleware = $this->gatherMiddlewareClassNames($middleware);

        return $this->sendThroughPipeline($middleware, function () use ($instance, $method, $routeInfo) {
            return $this->callControllerCallable([$instance, $method], $routeInfo[2]);
        });
    }

    /**
     * Call a controller callable and return the response.
     *
     * @param  callable $callable
     * @param  array $parameters
     * @return \Illuminate\Http\Response
     */
    protected function callControllerCallable(callable $callable, array $parameters = [])
    {
        try {
            return $this->prepareResponse(
                $this->call($callable, $parameters)
            );
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Gather the full class names for the middleware short-cut string.
     *
     * @param  string $middleware
     * @return array
     */
    protected function gatherMiddlewareClassNames($middleware)
    {
        $middleware = is_string($middleware) ? explode('|', $middleware) : (array)$middleware;

        return array_map(function ($name) {
            list($name, $parameters) = array_pad(explode(':', $name, 2), 2, null);

            return array_get($this->routeMiddleware, $name, $name) . ($parameters ? ':' . $parameters : '');
        }, $middleware);
    }

    /**
     * Send the request through the pipeline with the given callback.
     *
     * @param  array $middleware
     * @param  \Closure $then
     * @return mixed
     */
    protected function sendThroughPipeline(array $middleware, Closure $then)
    {
        if (count($middleware) > 0 && !$this->shouldSkipMiddleware()) {
            return (new Pipeline($this))
                ->send($this->make('request'))
                ->through($middleware)
                ->then($then);
        }

        return $then();
    }

    /**
     * Prepare the response for sending.
     *
     * @param  mixed $response
     * @return Response
     */
    public function prepareResponse($response)
    {
        if ($response instanceof PsrResponseInterface) {
//            dd('psr');
            $response = (new HttpFoundationFactory)->createResponse($response);
        } elseif (!$response instanceof SymfonyResponse) {
//            dd('symfony');
            $response = new Response($response);
        } elseif ($response instanceof BinaryFileResponse) {
            $response = $response->prepare(Request::capture());
        }
//        dd($response->getContent());
        return $response;
    }

    /**
     * Get the current HTTP request method.
     *
     * @return string
     */
    protected function getMethod()
    {
        if (isset($_POST['_method'])) {
            return strtoupper($_POST['_method']);
        } else {
            return $_SERVER['REQUEST_METHOD'];
        }
    }

    /**
     * Get the current HTTP path info.
     *
     * @return string
     */
    protected function getPathInfo()
    {
        $query = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';

        return '/' . trim(str_replace('?' . $query, '', $_SERVER['REQUEST_URI']), '/');
    }

    /**
     * Get the raw routes for the application.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Determines whether middleware should be skipped during request.
     *
     * @return bool
     */
    protected function shouldSkipMiddleware()
    {
        return $this->bound('middleware.disable') && $this->make('middleware.disable') === true;
    }

//    protected $routes = [];
//
//    protected $namedRoutes = [];
//
//    protected $middleware = [];
//
//    protected $routeMiddleware = [];
//
//    protected $groupAttributes;
//
//    protected $currentRoute;
//
//    protected $dispatcher;
//
//
//    public function group(array $attributes, Closure $callback)
//    {
//        $parentGroupAttributes = $this->groupAttributes;
//
//        if (isset($attributes['middleware']) && is_string($attributes['middleware'])) {
//            $attributes['middleware'] = explode('|', $attributes['middleware']);
//        }
//
//        $this->groupAttributes = $attributes;
//
//        call_user_func($callback, $this);
//
//        $this->groupAttributes = $parentGroupAttributes;
//    }
//
//    public function get($uri, $action)
//    {
//        $this->addRoute('GET', $uri, $action);
//
//        return $this;
//    }
//
//    public function post($uri, $action)
//    {
//        $this->addRoute('POST', $uri, $action);
//
//        return $this;
//    }
//
//    public function put($uri, $action)
//    {
//        $this->addRoute('PUT', $uri, $action);
//
//        return $this;
//    }
//
//    public function patch($uri, $action)
//    {
//        $this->addRoute('PATCH', $uri, $action);
//
//        return $this;
//    }
//
//    public function delete($uri, $action)
//    {
//        $this->addRoute('DELETE', $uri, $action);
//
//        return $this;
//    }
//
//    public function options($uri, $action)
//    {
//        $this->addRoute('OPTIONS', $uri, $action);
//
//        return $this;
//    }
//
//    public function addRoute($method, $uri, $action)
//    {
//        $action = $this->parseAction($action);
//
//        if (isset($this->groupAttributes)) {
//            if (isset($this->groupAttributes['prefix'])) {
//                $uri = trim($this->groupAttributes['prefix'], '/') . '/' . trim($uri, '/');
//            }
//
//            if (isset($this->groupAttributes['suffix'])) {
//                $uri = trim($uri, '/') . rtrim($this->groupAttributes['suffix'], '/');
//            }
//
//            $action = $this->mergeGroupAttributes($action);
//        }
//
//        $uri = '/' . trim($uri, '/');
//
//        if (isset($action['as'])) {
//            $this->namedRoutes[$action['as']] = $uri;
//        }
//
//        if (is_array($method)) {
//            foreach ($method as $verb) {
//                $this->routes[$verb . $uri] = ['method' => $verb, 'uri' => $uri, 'action' => $action];
//            }
//        } else {
//            $this->routes[$method . $uri] = ['method' => $method, 'uri' => $uri, 'action' => $action];
//        }
//    }
//
//    protected function parseAction($action)
//    {
//        if (is_string($action)) {
//            return ['uses' => $action];
//        } elseif (!is_array($action)) {
//            return [$action];
//        }
//
//        if (isset($action['middleware']) && is_string($action['middleware'])) {
//            $action['middleware'] = explode('|', $action['middleware']);
//        }
//
//        return $action;
//    }
//
//    protected function mergeGroupAttributes(array $action)
//    {
//        return $this->mergeNamespaceGroup(
//            $this->mergeMiddlewareGroup($action)
//        );
//    }
//
//    protected function mergeNamespaceGroup(array $action)
//    {
//        if (isset($this->groupAttributes['namespace']) && isset($action['uses'])) {
//            $action['uses'] = $this->groupAttributes['namespace'] . '\\' . $action['uses'];
//        }
//
//        return $action;
//    }
//
//    protected function mergeMiddlewareGroup($action)
//    {
//        if (isset($this->groupAttributes['middleware'])) {
//            if (isset($action['middleware'])) {
//                $action['middleware'] = array_merge($this->groupAttributes['middleware'], $action['middleware']);
//            } else {
//                $action['middleware'] = $this->groupAttributes['middleware'];
//            }
//        }
//
//        return $action;
//    }
//
//    public function dispatch($request = null)
//    {
//        list($method, $pathInfo) = $this->parseIncomingRequest($request);
//
//        return $this->sendThroughPipeline($this->middleware, function () use ($method, $pathInfo) {
//            if (isset($this->routes[$method . $pathInfo])) {
//                return $this->handleFoundRoute([true, $this->routes[$method . $pathInfo]['action'], []]);
//            }
//
//            return $this->handleDispatcherResponse(
//                $this->createDispatcher()->dispatch($method, $pathInfo)
//            );
//        });
//    }
//
//    protected function parseIncomingRequest($request)
//    {
//        if ($request) {
//            $this->instance(Request::class, $this->prepareRequest($request));
//            $this->ranServiceBinders['registerRequestBindings'] = true;
//
//            return [$request->getMethod(), $request->getPathInfo()];
//        } else {
//            return [$this->getMethod(), $this->getPathInfo()];
//        }
//    }
//
//    protected function getMethod()
//    {
//        if (isset($_POST['_method'])) {
//            return strtoupper($_POST['_method']);
//        } else {
//            return $_SERVER['REQUEST_METHOD'];
//        }
//    }
//
//    protected function getPathInfo()
//    {
//        $query = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
//
//        return '/' . trim(str_replace('?' . $query, '', $_SERVER['REQUEST_URI']));
//    }
//
//    protected function sendThroughPipeline(array $middleware, Closure $then)
//    {
//        if (count($middleware) > 0 && !$this->shouldSkipMiddleware()){
//            return (new Pipeline($this))
//                ->send($this->make('request'))
//                ->through($middleware)
//                ->then($then);
//        }
//
//        return $then();
//    }
//
//    protected function handleFoundRoute($routeInfo)
//    {
//        $this->currentRoute = $routeInfo;
//
//        $this['request']->setRouteResolve(function () {
//            return $this->currentRoute;
//        });
//
//        $action = $routeInfo[1];
//
//        if (isset($action['middleware'])) {
//            $middleware = $this->gatherMiddlewareClassNames($action['middleware']);
//
//            return $this->prepareResponse($this->sendThroughPipeline($middleware), function () {
//                 return $this->callActionOnArrayBasedRoute($this['request']->route());
//            });
//        }
//
//        return $this->prepareResponse(
//            $this->callActionOnArrayBasedRoute($routeInfo)
//        );
//    }
//
//    protected function callActionOnArrayBasedRoute($routeInfo)
//    {
//        $action = $routeInfo[1];
//
//        if (isset($action['uses'])) {
//            return $this->prepareResponse($this->callControllerAction($routeInfo));
//        }
//
//        foreach ($action as $value) {
//            if ($value instanceof Closure) {
//                $closure = $value->bindTo(new RoutingClosure);
//                break;
//            }
//        }
//
////        try {
////            return $this->prepareResponse($this->call($closure, $routeInfo[2]));
////        } catch (HttpResponseException $e) {
////            return $e->getResponse();
////        }
//    }
//
//    protected function callControllerAction($routeInfo)
//    {
//        $uses = $routeInfo[1]['uses'];
//
//        if (is_string($uses) && ! Str::contains($uses, '@')) {
//            $uses .= '@__invoke';
//        }
//
//        list($controller, $method) = explode('@', $uses);
//
//        if (! method_exists($instance = $this->make($controller), $method)) {
//            throw new NotFoundHttpException;
//        }
//
//        if ($instance instanceof LumenController) {
//            return $this->callLumenController($instance, $method, $routeInfo);
//        } else {
//            return $this->callControllerCallable(
//                [$instance, $method], $routeInfo[2]
//            );
//        }
//    }
}