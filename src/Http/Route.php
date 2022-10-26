<?php

namespace Libertyphp\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class Route
{
    private string $method;

    private string $pattern;

    private ActionController $actionController;

    /** @var MiddlewareInterface[] */
    private array $middlewares;

    /** @var string[] */
    private array $parameterRules;

    /**
     * @param MiddlewareInterface[] $middlewares
     * @param string[] $parameterRules
     */
    public function __construct(string $rule, string $actionControllerClass, Router $router, array $middlewares = [], array $parameterRules = [])
    {
        [$method, $pattern] = explode(' ', $rule);

        $this->method           = $method;
        $this->pattern          = $pattern;
        $this->actionController = new $actionControllerClass($router->getDi());
        $this->middlewares      = $middlewares;
        $this->parameterRules   = $parameterRules;
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
        return (new ActionRequestHandler($this->middlewares, $this->actionController, $params))->handle($serverRequest);
    }
}
