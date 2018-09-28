<?php

namespace Libertyphp\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Route
{
    /** @var string */
    private $method;

    /** @var string */
    private $pattern;

    /** @var ActionController */
    private $actionController;

    /** @var MiddlewareInterface[] */
    private $middlewares = [];

    /** @var string[] */
    private $parameterRules = [];

    /**
     * @param string $rule
     * @param string $actionControllerClass
     * @param Router $router
     * @param MiddlewareInterface[] $middlewares
     * @param string[] $parameterRules
     */
    public function __construct($rule, $actionControllerClass, Router $router, array $middlewares = [], array $parameterRules = [])
    {
        list($method, $pattern) = explode(' ', $rule);

        $this->method = $method;
        $this->pattern = $pattern;
        $this->actionController = new $actionControllerClass($router->getDi());
        $this->middlewares = $middlewares;
        $this->parameterRules = $parameterRules;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return string[]
     */
    public function getParameterRules()
    {
        return $this->parameterRules;
    }

    /**
     * @param array $params
     * @param ServerRequestInterface $serverRequest
     *
     * @return ResponseInterface
     */
    public function execute(array $params, ServerRequestInterface $serverRequest)
    {
        $headers = [];
        foreach ($this->middlewares as $middleware) {
            $response = $middleware->process($serverRequest);

            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    $headers[$name] = $value;
                }
            }
        }

        $response = $this->actionController->execute($params, $serverRequest);
        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }
}
