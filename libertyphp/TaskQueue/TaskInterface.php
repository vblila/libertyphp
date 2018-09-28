<?php

namespace Libertyphp\TaskQueue;

use Psr\Container\ContainerInterface;

interface TaskInterface
{
    /**
     * @param ContainerInterface $di
     * @return void
     */
    public function execute(ContainerInterface $di);
}
