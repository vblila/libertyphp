<?php

namespace Libertyphp\TaskQueue;

interface TaskQueueInterface
{
    public function add(TaskInterface $task): TaskQueueInterface;
}

