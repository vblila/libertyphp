<?php

namespace Libertyphp\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ActionRequestHandler implements RequestHandlerInterface
{
    /** @var MiddlewareInterface[] */
    protected array $middlewares;

    protected ActionController $actionController;

    protected array $routeParams;

    /**
     * @param MiddlewareInterface[] $middlewares
     */
    public function __construct(array $middlewares, ActionController $actionController, array $routeParams)
    {
        $this->middlewares      = $middlewares;
        $this->actionController = $actionController;
        $this->routeParams      = $routeParams;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = array_shift($this->middlewares);
        if ($middleware) {
            return $middleware->process($request, $this);
        }

        return $this->actionController->execute($this->routeParams, $request);
    }
}
