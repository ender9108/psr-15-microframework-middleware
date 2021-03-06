<?php

namespace Tests\EnderLab\MiddleEarth\Application;

use DI\ContainerBuilder;
use EnderLab\MiddleEarth\Dispatcher\Dispatcher;
use EnderLab\MiddleEarth\Dispatcher\DispatcherMiddleware;
use EnderLab\MiddleEarth\Router\Route;
use EnderLab\MiddleEarth\Router\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatcherMiddlewareTest extends TestCase
{
    public function testProcessWithRoute()
    {
        $request = new ServerRequest('GET', '/');
        $dispatcher = new Dispatcher();
        $container = ContainerBuilder::buildDevContainer();
        $router = new Router();
        $route = new Route(
            '/',
            function (ServerRequestInterface $request, RequestHandlerInterface $delegate) {
                $response = $delegate->handle($request);
                $response->getBody()->write('Test phpunit process app !');

                return $response;
            },
            'GET',
            'test_route'
        );
        $request = $request->withAttribute(Route::class, $route);
        $middleware = new DispatcherMiddleware($container, $router);

        $response = $middleware->process($request, $dispatcher);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testProcessWithoutRoute()
    {
        $request = new ServerRequest('GET', '/');
        $dispatcher = new Dispatcher();
        $container = ContainerBuilder::buildDevContainer();
        $router = new Router();
        $middleware = new DispatcherMiddleware($container, $router);

        $response = $middleware->process($request, $dispatcher);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
