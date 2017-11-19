<?php declare(strict_types=1);

namespace Jerowork\AuraRouterNestedMiddleware\Middleware;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ParentRequestHandlerMiddleware implements MiddlewareInterface
{
    /** @var RequestHandlerInterface */
    private $parentHandler;

    /**
     * @param RequestHandlerInterface $parentHandler
     */
    public function __construct(RequestHandlerInterface $parentHandler)
    {
        $this->parentHandler = $parentHandler;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->parentHandler->handle($request);
    }
}
