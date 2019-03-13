<?php

namespace Libertyphp\TaskQueue;

use Psr\Container\ContainerInterface;

interface TaskInterface
{
    public function execute(ContainerInterface $di): void;
}
