# Aura Router nested middleware
PSR-15 middleware for Aura Router with nested-middleware route handling.

## Installation
Install via Composer: 
```
$ composer require jerowork/aura-router-nested-middleware
```

## Usage
```php
use Aura\Router\RouterContainer;
use Jerowork\AuraRouterNestedMiddleware\AuraRouterNestedMiddleware;

// setup Aura Router
$router = new RouteContainer();

// add middleware to your general middleware queue
$middleware[] = new AuraRouterNestedMiddleware($router);

// add routes with middleware
$map = $router->getMap();

$map->get('home', '/', [
    new SomeMiddleware(),
    new AnotherMiddleware(),
    new HomeAction(),
]);

// route with no other middleware than the blog action
$map->get('blog', '/blog', new BlogAction());
```

### Http actions with PSR-15 implementation
From now on every http action can implement ```Interop\Http\Server\MiddlewareInterface```.

Example action:
```php
use Interop\Http\Server\MiddlewareInterface;

class HomeAction implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $response->getBody()->write('Hello world!');
        return $response;
    }
}
```
