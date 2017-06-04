<?php declare(strict_types=1);

namespace Jerowork\AuraRouterNestedMiddleware;

use Aura\Router\RouterContainer;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Jerowork\AuraRouterNestedMiddleware\Exception\RouteNotFoundException;
use Jerowork\AuraRouterNestedMiddleware\MiddlewarePipe\MiddlewarePipeInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AuraRouterNestedMiddleware.
 */
final class AuraRouterNestedMiddleware implements MiddlewareInterface
{
    /** @var RouterContainer */
    private $routeContainer;

    /** @var MiddlewarePipeInterface */
    private $middlewarePipe;

    /**
     * @param RouterContainer $routeContainer
     * @param MiddlewarePipeInterface $middlewarePipe
     */
    public function __construct(RouterContainer $routeContainer, MiddlewarePipeInterface $middlewarePipe)
    {
        $this->routeContainer = $routeContainer;
        $this->middlewarePipe = $middlewarePipe;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        // get route
        $route = $this->routeContainer->getMatcher()->match($request);

        if (!$route) {
            throw new RouteNotFoundException('Route not found.');
        }

        // add query parameters to request
        foreach ($route->attributes as $key => $val) {
            $request = $request->withAttribute($key, $val);
        }

        // get route handlers
        $handlers = !is_array($route->handler) ? [$route->handler] : $route->handler;

        // add route handlers to middleware pipe
        foreach ($handlers as $handler) {
            $this->middlewarePipe->pipe($handler);
        }

        // process middleware pipe
        return $this->middlewarePipe->process($request, $delegate->process($request));
    }
}
