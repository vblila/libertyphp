<?php

namespace Libertyphp\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;

class Router
{
    /** @var Route[] */
    private array $routes = [];

    /** @var Route[] */
    private array $routeByNames = [];

    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    private function addRoute(Route $route, string $name = null): Router
    {
        // It's important to follow order, bc in case of similar routes the first one wins
        $this->routes[] = $route;

        if ($name) {
            $this->routeByNames[$name] = $route;
        }

        return $this;
    }

    public function getRouteByName(string $name): ?Route
    {
        return $this->routeByNames[$name];
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param MiddlewareInterface[] $middlewares
     * @param string[] $parameterRules
     */
    public function addRule(string $rule, string $actionControllerClass, array $middlewares = [], ?string $name = null, array $parameterRules = []): Router {
        return $this->addRoute(
            new Route($rule, $actionControllerClass, $this, $middlewares, $parameterRules),
            $name
        );
    }

    /**
     * @throws HttpNotFoundException
     */
    public function respond(ServerRequestInterface $serverRequest): void
    {
        $response = $this->getResponseByRequest($serverRequest);
        if (!$response) {
            throw new HttpNotFoundException();
        }

        // Response includes HEADERS, BODY, and HTTP CODE by http_response_code() method
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), true);
            }
        }
        http_response_code($response->getStatusCode());
        echo $response->getBody();
    }

    public function getResponseByRequest(ServerRequestInterface $request): ?ResponseInterface
    {
        // Route search by pattern
        foreach ($this->getRoutes() as $route) {
            if ($route->getMethod() !== $request->getMethod()) {
                continue;
            }

            $path = $request->getUri()->getPath();

            // Rid of last "/" at the end of path
            if (str_ends_with($path, '/') && $path !== '/') {
                $path = substr($path, 0, -1);
            }

            $patternParts = explode('/', $route->getPattern());
            $requestedRouteParts = explode('/', $path);

            if (count($patternParts) !== count($requestedRouteParts)) {
                continue;
            }

            // Checking parts of requested route with current pattern
            $params = [];
            foreach ($patternParts as $index => $patternPart) {
                if (str_starts_with($patternPart, '@')) {
                    // Parameter was found. Keep it in $params dictionary
                    $paramName  = substr($patternPart, 1);
                    $paramValue = $requestedRouteParts[$index];

                    // When parameter requires to match with regexp
                    if (
                        isset($route->getParameterRules()[$paramName])
                        && !preg_match($route->getParameterRules()[$paramName], $paramValue)
                    ) {
                        continue 2;
                    }

                    $params[$paramName] = $paramValue;
                } else if ($patternPart != $requestedRouteParts[$index]) {
                    // Part of pattern isn't parameter and doesn't match with requested route
                    continue 2;
                }
            }

            return $route->execute($params, $request)
                ->withProtocolVersion($request->getProtocolVersion());
        }

        return null;
    }

    public function getDi(): ContainerInterface
    {
        return $this->di;
    }
}
