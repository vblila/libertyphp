<?php

namespace Libertyphp\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller with many actions is bad solution (violates the Single Responsibility Principle).
 * Each action must be in separated class.
 */
abstract class ActionController
{
    protected ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    abstract public function execute(array $routeParams, ServerRequestInterface $request): ResponseInterface;
}
