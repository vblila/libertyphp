<?php

namespace Libertyphp\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ActionRequestHandler implements RequestHandlerInterface
{
    /** @var MiddlewareInterface */
    protected $middlewares;

    /** @var ActionController */
    protected $actionController;

    /** @var array */
    protected $routeParams;

    /**
     * @param MiddlewareInterface[] $middlewares
     * @param ActionController $actionController
     * @param array $routeParams
     */
    public function __construct(array $middlewares, ActionController $actionController, array $routeParams)
    {
        $this->middlewares = $middlewares;
        $this->actionController = $actionController;
        $this->routeParams = $routeParams;
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