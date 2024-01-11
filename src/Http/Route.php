<?php

namespace Libertyphp\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class Route
{
    private string $method;

    private string $pattern;

    private ContainerInterface $di;

    private string $actionControllerClass;

    private ?string $name;

    /** @var MiddlewareInterface[] */
    private array $middlewares;

    /** @var string[] */
    private array $parameterRules;

    /**
     * @param MiddlewareInterface[] $middlewares
     * @param string[] $parameterRules
     */
    public function __construct(string $rule, string $actionControllerClass, Router $router, array $middlewares = [], ?string $name = null, array $parameterRules = [])
    {
        [$method, $pattern] = explode(' ', $rule);

        $this->method                = $method;
        $this->pattern               = $pattern;
        $this->actionControllerClass = $actionControllerClass;
        $this->di                    = $router->getDi();
        $this->middlewares           = $middlewares;
        $this->name                  = $name;
        $this->parameterRules        = $parameterRules;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getName(): ?string
    {
        return $this->name;
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
        /** @var ActionController $actionController */
        $actionController = new $this->actionControllerClass($this->di, $this);

        return (new ActionRequestHandler($this->middlewares, $actionController, $params))->handle($serverRequest);
    }
}
