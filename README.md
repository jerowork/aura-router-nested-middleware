# Aura Router nested middleware
PSR-15 middleware for Aura Router with nested-middleware route handling.

## Installation
Install via Composer: 
```
$ composer require jerowork/aura-router-nested-middleware:dev-master
```

## Usage
```php
<?php declare(strict_types=1);

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
$router->get('home', '/', [
    new SomeMiddleware(),
    new AnotherMiddleware(),
    new HomeAction(),
]);

// route with no other middleware than the blog action
$router->get('blog', '/blog', new BlogAction());
```

A http action has to implement ```Interop\Http\ServerMiddleware\MiddlewareInterface```.
Example:
```php
<?php declare(strict_types=1);

use Interop\Http\ServerMiddleware\MiddlewareInterface;

class HomeAction implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $response = $delegate->process($request);
        $response->getBody()->write('Hello world!');
        return $response;
    }
}
```