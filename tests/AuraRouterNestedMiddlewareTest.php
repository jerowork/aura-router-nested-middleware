<?php declare(strict_types=1);

namespace Jerowork\AuraRouterNestedMiddleware\Test;

use Aura\Router\RouterContainer;
use Jerowork\AuraRouterNestedMiddleware\AuraRouterNestedMiddleware;
use Jerowork\AuraRouterNestedMiddleware\Exception\RouteNotFoundException;
use Jerowork\AuraRouterNestedMiddleware\Test\Stub\Middleware1Stub;
use Jerowork\AuraRouterNestedMiddleware\Test\Stub\Middleware2Stub;
use Jerowork\AuraRouterNestedMiddleware\Test\Stub\Middleware3Stub;
use Jerowork\AuraRouterNestedMiddleware\Test\Stub\MiddlewareWithQueryParamsStub;
use Jerowork\AuraRouterNestedMiddleware\Test\Stub\NestedMiddleware1Stub;
use Jerowork\AuraRouterNestedMiddleware\Test\Stub\NestedMiddleware2Stub;
use Jerowork\MiddlewareDispatcher\Middleware\FinalResponseMiddleware;
use Jerowork\MiddlewareDispatcher\MiddlewareRequestHandler;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class AuraRouterNestedMiddlewareTest extends TestCase
{
    public function testNoRouteFound()
    {
        $handler = new MiddlewareRequestHandler([
            new AuraRouterNestedMiddleware(new RouterContainer()),
            new FinalResponseMiddleware(new Response()),
        ]);

        $this->expectException(RouteNotFoundException::class);

        $handler->handle(ServerRequestFactory::fromGlobals());
    }

    public function testNestedMiddlewareOrder()
    {
        $routeContainer = new RouterContainer();

        $map = $routeContainer->getMap();
        $map->get('name', '/', [
            new NestedMiddleware1Stub(),
            new NestedMiddleware2Stub(),
        ]);

        $handler = new MiddlewareRequestHandler([
            new Middleware1Stub(),
            new Middleware2Stub(),
            new AuraRouterNestedMiddleware($routeContainer),
            new Middleware3Stub(),
            new FinalResponseMiddleware(new Response()),
        ]);

        $response = $handler->handle(ServerRequestFactory::fromGlobals());

        $this->assertSame('|3|N2|N1|2|1', (string)$response->getBody());
    }

    public function testAuraAttributesSet()
    {
        $routeContainer = new RouterContainer();

        $map = $routeContainer->getMap();
        $map->get('routename', '/{test}', [new MiddlewareWithQueryParamsStub()]);

        $handler = new MiddlewareRequestHandler([
            new AuraRouterNestedMiddleware($routeContainer),
            new FinalResponseMiddleware(new Response()),
        ]);

        $response = $handler->handle(ServerRequestFactory::fromGlobals([
            'REQUEST_URI' => '/success',
        ]));

        $this->assertSame('success', (string)$response->getBody());
    }
}
