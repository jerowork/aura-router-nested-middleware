# Aura Router nested middleware
PSR-15 middleware for Aura Router with nested-middleware route handling.

## Installation
Install via Composer: 
```
$ composer require jerowork/aura-router-nested-middleware
```

__NOTE about http-interop/http-middleware__

This package make use of Zend Stratigility as middleware pipe. Since Stratigility 2.1 you have to explicitly define an http-interop/http-middleware dependency in your composer.json. Depending on the used version of jerowork/aura-router-nested-middleware you have to choose:

|jerowork/aura-router-nested-middleware|http-interop/http-middleware|
|--|--|
|0.1.2|0.4.1|
|0.2.0|0.5.0|

## Usage
```php
use Aura\Router\RouterContainer;
use Jerowork\AuraRouterNestedMiddleware\AuraRouterNestedMiddleware;
use Jerowork\AuraRouterNestedMiddleware\MiddlewarePipe\StratigilityMiddlewarePipe;
use Zend\Stratigility\MiddlewarePipe;

// setup Aura Router
$router = new RouteContainer();

// add middleware to your general middleware queue
$middleware[] = new AuraRouterNestedMiddleware(
    $router,
    new StratigilityMiddlewarePipe(new MiddlewarePipe())
);

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
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $response = $handler->handle($request);
        $response->getBody()->write('Hello world!');
        return $response;
    }
}
```
