<?php declare(strict_types=1);

namespace Jerowork\AuraRouterNestedMiddleware\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
