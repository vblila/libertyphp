<?php

namespace Libertyphp\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    /** @var Route[] */
    private $routes = [];

    /** @var array */
    private $aliasIndexes = [];

    /** @var ContainerInterface */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    private function addRoute(Route $route, string $alias = null): Router
    {
        // Роуты сохраняем в порядке, в котором они настроены, для соблюдения приоритета
        $this->routes[] = $route;

        // Связываем порядковые номер роута и алиаса
        if ($alias) {
            $this->aliasIndexes[$alias] = count($this->routes) - 1;
        }

        return $this;
    }

    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param string $rule
     * @param string $actionControllerClass
     * @param MiddlewareInterface[] $middlewares
     * @param string|null $alias
     * @param string[] $parameterRules
     *
     * @return $this
     */
    public function addRule(
        string $rule,
        string $actionControllerClass,
        array $middlewares = [],
        string $alias = null,
        array $parameterRules = []
    ): Router {
        return $this->addRoute(new Route($rule, $actionControllerClass, $this, $middlewares, $parameterRules), $alias);
    }

    /**
     * @throws HttpNotFoundException
     */
    public function renderResponse()
    {
        /** @var ServerRequestInterface $serverRequest */
        $serverRequest = $this->di->get('serverRequest');

        $response = $this->getResponseByRequest($serverRequest);
        if (!$response) {
            throw new HttpNotFoundException();
        }

        // Отвечаем клиенту заголовки + тело ответа
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
        // Находим роут, соответствующий паттерну
        foreach ($this->getRoutes() as $route) {
            if ($route->getMethod() != $request->getMethod()) {
                continue;
            }

            // Удаляем последний / в конце URI
            $uri = $request->getUri();
            if (substr($uri, -1) == '/' && mb_strlen(parse_url($uri)['path']) > 1) {
                $uri = substr($uri, 0, -1);
            }
            $parsedRequestUri = parse_url($uri);

            $patternParts = explode('/', $route->getPattern());
            $requestedRouteParts = explode('/', $parsedRequestUri['path']);

            if (count($patternParts) != count($requestedRouteParts)) {
                continue;
            }

            // Сверим части запрошенного роута с паттерном
            $params = [];
            foreach ($patternParts as $index => $patternPart) {
                if (strpos($patternPart, '@') !== false) {
                    // Нашли параметр, запомним запрошенное значение в apiRoute
                    $paramName = str_replace('@', '', $patternPart);
                    $paramValue = $requestedRouteParts[$index];

                    // Если для параметра задано регулярное выражение, проверим на соответствие правилу
                    if (
                        isset($route->getParameterRules()[$paramName])
                        && !preg_match($route->getParameterRules()[$paramName], $paramValue)
                    ) {
                        continue 2;
                    }

                    $params[$paramName] = $paramValue;
                } else if ($patternPart != $requestedRouteParts[$index]) {
                    // Часть паттерна не является параметром и не соответствует запрошенному роуту
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
