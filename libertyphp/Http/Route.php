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

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return string[]
     */
    public function getParameterRules(): array
    {
        return $this->parameterRules;
    }

    public function execute(array $params, ServerRequestInterface $serverRequest): ResponseInterface
    {
        $actionRequestHandler = new ActionRequestHandler($this->middlewares, $this->actionController, $params);
        $response = $actionRequestHandler->handle($serverRequest);

        return $response;
    }
}
