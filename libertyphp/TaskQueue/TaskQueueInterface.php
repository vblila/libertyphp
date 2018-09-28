<?php

namespace Libertyphp\TaskQueue;

interface TaskQueueInterface
{
    /**
     * @param TaskInterface $task
     * @return void
     */
    public function add(TaskInterface $task);
}

