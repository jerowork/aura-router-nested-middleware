<?php declare(strict_types=1);

namespace Jerowork\AuraRouterNestedMiddleware\MiddlewarePipe;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface MiddlewarePipeInterface.
 */
interface MiddlewarePipeInterface
{
    /**
     * Add middleware to pipe.
     *
     * @param MiddlewareInterface $middleware
     */
    public function pipe(MiddlewareInterface $middleware);

    /**
     * Process middleware pipe.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}
