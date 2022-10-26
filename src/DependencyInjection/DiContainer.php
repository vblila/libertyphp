<?php

namespace Libertyphp\DependencyInjection;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DiContainer implements ContainerInterface
{
    /** @var array */
    private array $initializedServices = [];

    /** @var Closure[] */
    private array $serviceFabrics = [];

    public function get(string $id)
    {
        return $this->getServiceFabric($id)->call($this);
    }

    public function has(string $id): bool
    {
        return isset($this->serviceFabrics[$id]);
    }

    public function set(string $id, Closure $fabricClosure): static
    {
        $this->serviceFabrics[$id] = $fabricClosure;
        return $this;
    }

    /**
     * @throws NotFoundExceptionInterface
     */
    protected function getServiceFabric(string $id): Closure
    {
        if (!$this->has($id)) {
            throw new NotFoundException();
        }

        return $this->serviceFabrics[$id];
    }

    protected function setInitializedService(string $id, mixed $service): self
    {
        $this->initializedServices[$id] = $service;
        return $this;
    }

    protected function getInitializedService(string $id): mixed
    {
        if (!isset($this->initializedServices[$id])) {
            return null;
        }

        return $this->initializedServices[$id];
    }

    public function singleton(string $id, Closure $fabricClosure): void
    {
        $this->set($id, function() use ($id, $fabricClosure) {
            $service = $this->getInitializedService($id);
            if ($service) {
                return $service;
            }

            $service = $fabricClosure->call($this);
            $this->setInitializedService($id, $service);

            return $service;
        });
    }
}
