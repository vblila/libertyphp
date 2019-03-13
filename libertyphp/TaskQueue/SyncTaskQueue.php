<?php

namespace Libertyphp\TaskQueue;

use Psr\Container\ContainerInterface;

class SyncTaskQueue implements TaskQueueInterface
{
    /** @var ContainerInterface */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function add(TaskInterface $task): TaskQueueInterface
    {
        // В синхронной очереди выполняем добавленные таски сразу
        $task->execute($this->di);
        return $this;
    }
}
