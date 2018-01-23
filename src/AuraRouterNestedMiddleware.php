<?php declare(strict_types=1);

namespace Jerowork\AuraRouterNestedMiddleware;

use Aura\Router\RouterContainer;
use Jerowork\AuraRouterNestedMiddleware\Exception\RouteNotFoundException;
use Jerowork\AuraRouterNestedMiddleware\Middleware\ParentRequestHandlerMiddleware;
use Jerowork\MiddlewareDispatcher\MiddlewareRequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuraRouterNestedMiddleware implements MiddlewareInterface
{
    /** @var RouterContainer */
    private $routeContainer;

    /**
     * @param RouterContainer $routeContainer
     */
    public function __construct(RouterContainer $routeContainer)
    {
        $this->routeContainer = $routeContainer;
    }

    /**
     * @inheritDoc
     *
     * @throws RouteNotFoundException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // get route
        $route = $this->routeContainer->getMatcher()->match($request);

        if (!$route) {
            throw new RouteNotFoundException('Route not found.');
        }

        // add query parameters to request
        foreach ($route->attributes as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        // get route middleware
        $middlewares = !is_array($route->handler) ? [$route->handler] : $route->handler;

        // add remaining parent middlewares to nested middleware stack
        $middlewares[] = new ParentRequestHandlerMiddleware($handler);

        // add and process route handlers via middleware request handler
        return (new MiddlewareRequestHandler($middlewares))->handle($request);
    }
}
