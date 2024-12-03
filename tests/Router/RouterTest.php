<?php declare(strict_types=1);

namespace Libertyphp\Tests\Router;

use GuzzleHttp\Psr7\ServerRequest;
use JetBrains\PhpStorm\Pure;
use Libertyphp\DependencyInjection\DiContainer;
use Libertyphp\Http\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    #[Pure] private function createEmptyRouter(): Router
    {
        $di = new DiContainer();
        return new Router($di);
    }

    public function testSimpleGetRoute(): void
    {
        $router = self::createEmptyRouter();

        $router->addRule('GET /root-get-method', SimpleActionController::class);

        $request = new ServerRequest('GET', '/root-get-method');
        $response = $router->getResponseByRequest($request);

        $this->assertNotNull($response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Simple response', $response->getBody()->getContents());

        $request = new ServerRequest('POST', '/root-get-method');
        $response = $router->getResponseByRequest($request);

        $this->assertNull($response);
    }

    public function testSimplePostRoute(): void
    {
        $router = self::createEmptyRouter();

        $router->addRule('POST /root-post-method', SimpleActionController::class);

        $request = new ServerRequest('POST', '/root-post-method', [], 'it is body');
        $response = $router->getResponseByRequest($request);

        $this->assertNotNull($response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Simple response with request body it is body', $response->getBody()->getContents());

        $request = new ServerRequest('GET', '/root-post-method');
        $response = $router->getResponseByRequest($request);

        $this->assertNull($response);
    }

    public function testIgnoredSlashAtTheEndOfRoute(): void
    {
        $router = self::createEmptyRouter();

        $router->addRule('GET /root-get-method', SimpleActionController::class);

        $request = new ServerRequest('GET', '/root-get-method');
        $response = $router->getResponseByRequest($request);
        $this->assertNotNull($response);

        $request = new ServerRequest('GET', '/root-get-method?');
        $response = $router->getResponseByRequest($request);
        $this->assertNotNull($response);

        $request = new ServerRequest('GET', '/root-get-method/');
        $response = $router->getResponseByRequest($request);
        $this->assertNotNull($response);

        $request = new ServerRequest('GET', '/root-get-method/?a=3');
        $response = $router->getResponseByRequest($request);
        $this->assertNotNull($response);

        $request = new ServerRequest('GET', '/root-get-method//');
        $response = $router->getResponseByRequest($request);
        $this->assertNull($response);
    }

    public function testWithRouteParams(): void
    {
        $router = self::createEmptyRouter();

        $router->addRule('GET /orders/@sort/@status', SimpleActionController::class);

        $request = new ServerRequest('GET', '/orders/price/new');
        $response = $router->getResponseByRequest($request);

        $this->assertNotNull($response);
        $this->assertSame('Simple response with route params sort=price status=new', $response->getBody()->getContents());

        $request = new ServerRequest('GET', '/orders');
        $response = $router->getResponseByRequest($request);

        $this->assertNull($response);
    }

    public function testWithRouteParamsAndRegexpConstraints(): void
    {
        $router = self::createEmptyRouter();

        $router->addRule('GET /orders/@sort', SimpleActionController::class, [], null, ['sort' => '/^(price|date)$/']);
        $router->addRule('GET /orders/@status', SimpleActionController::class, [], null, ['status' => '/^(new|paid|finished)$/']);

        $request = new ServerRequest('GET', '/orders');
        $response = $router->getResponseByRequest($request);
        $this->assertNull($response);

        $request = new ServerRequest('GET', '/orders/unknown');
        $response = $router->getResponseByRequest($request);
        $this->assertNull($response);

        $request = new ServerRequest('GET', '/orders/date');
        $response = $router->getResponseByRequest($request);
        $this->assertNotNull($response);
        $this->assertSame('Simple response with route params sort=date', $response->getBody()->getContents());

        $request = new ServerRequest('GET', '/orders/paid');
        $response = $router->getResponseByRequest($request);
        $this->assertNotNull($response);
        $this->assertSame('Simple response with route params status=paid', $response->getBody()->getContents());

        $request = new ServerRequest('GET', '/orders/paids');
        $response = $router->getResponseByRequest($request);
        $this->assertNull($response);
    }

    public function testRouteWithMiddleware(): void
    {
        $router = self::createEmptyRouter();

        $router->addRule('GET /profile', SimpleActionController::class, [new SimpleMiddleware()]);

        // Middleware without X-Auth-Token header respond with 307 Redirect

        $request = new ServerRequest('GET', '/profile');
        $response = $router->getResponseByRequest($request);
        $this->assertNotNull($response);
        $this->assertSame(307, $response->getStatusCode());

        $request = new ServerRequest('GET', '/profile', ['X-Auth-Token' => 'fake_token']);
        $response = $router->getResponseByRequest($request);
        $this->assertNotNull($response);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testWithRouteName(): void
    {
        $router = self::createEmptyRouter();

        $router->addRule('GET /orders/created', SimpleActionController::class, [], 'orders.created');
        $router->addRule('GET /orders/completed', SimpleActionController::class, [], 'orders.completed');

        $request = new ServerRequest('GET', '/orders/completed');
        $response = $router->getResponseByRequest($request);

        $this->assertNotNull($response);
        $this->assertSame('Simple response with route name orders.completed', $response->getBody()->getContents());

        $route = $router->getRouteByName('orders.created');
        $this->assertNotNull($route);

        $this->assertSame('orders.created', $route->getName());
    }
}
