<?php

namespace Libertyphp\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Используем вместо контроллеров с кучей экшенов один класс на каждый экшен
 */
abstract class ActionController
{
    /** @var ContainerInterface */
    protected $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @param array $routeParams
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    abstract public function execute(array $routeParams, ServerRequestInterface $request);

    /**
     * @return View
     */
    public function createView()
    {
        return new View($this->di);
    }
}
