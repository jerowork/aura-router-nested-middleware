<?php declare(strict_types=1);

namespace Jerowork\AuraRouterNestedMiddleware\MiddlewarePipe;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Stratigility\MiddlewarePipe;

/**
 * Class StratigilityMiddlewarePipe.
 */
final class StratigilityMiddlewarePipe implements MiddlewarePipeInterface
{
    /** @var MiddlewarePipe */
    private $middlewarePipe;

    /**
     * @param MiddlewarePipe $middlewarePipe
     */
    public function __construct(MiddlewarePipe $middlewarePipe)
    {
        $this->middlewarePipe = $middlewarePipe;
    }

    /**
     * @inheritDoc
     */
    public function pipe(MiddlewareInterface $middleware)
    {
        $this->middlewarePipe->pipe($middleware);
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $middlewarePipe = $this->middlewarePipe;
        return $middlewarePipe($request, $response, function ($request, $response) {
            return $response;
        });
    }
}
