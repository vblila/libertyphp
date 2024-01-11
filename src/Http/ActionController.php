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
    public function __construct(protected ContainerInterface $di, protected Route $route) {}

    abstract public function execute(array $routeParams, ServerRequestInterface $request): ResponseInterface;
}
