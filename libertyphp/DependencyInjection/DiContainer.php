<?php

namespace Libertyphp\DependencyInjection;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DiContainer implements ContainerInterface
{
    /** @var array */
    private $initializedServices = [];

    /** @var array */
    private $serviceFabrics = [];

    public function get($id)
    {
        return $this->getServiceFabric($id)->call($this);
    }

    public function has($id)
    {
        return isset($this->serviceFabrics[$id]);
    }

    /**
     * @param string $id
     * @param Closure $fabricClosure
     *
     * @return $this
     */
    public function set($id, Closure $fabricClosure)
    {
        $this->serviceFabrics[$id] = $fabricClosure;
        return $this;
    }

    /**
     * @param string $id
     * @return Closure
     *
     * @throws NotFoundExceptionInterface
     */
    protected function getServiceFabric($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException();
        }

        return $this->serviceFabrics[$id];
    }

    /**
     * @param string $id
     * @param mixed $service
     *
     * @return $this
     */
    protected function setInitializedService($id, $service)
    {
        $this->initializedServices[$id] = $service;
        return $this;
    }

    /**
     * @param string $id
     * @return mixed
     */
    protected function getInitializedService($id)
    {
        if (!isset($this->initializedServices[$id])) {
            return null;
        }

        return $this->initializedServices[$id];
    }
}
