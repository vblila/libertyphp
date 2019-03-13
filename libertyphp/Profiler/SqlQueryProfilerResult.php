<?php

namespace Libertyphp\Profiler;

class SqlQueryProfilerResult
{
    /** @var string */
    private $sql;

    /** @var array */
    private $binds;

    /** @var float */
    private $startMicroTimestamp;

    /** @var float */
    private $finishMicroTimestamp;

    public function __construct(string $sql, array $binds = [])
    {
        $this->sql = $sql;
        $this->binds = $binds;
        $this->startMicroTimestamp = microtime(true);
    }

    public function setFinishMicroTimestamp(float $microTimestamp): SqlQueryProfilerResult
    {
        $this->finishMicroTimestamp = $microTimestamp;
        return $this;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function getBinds(): array
    {
        return $this->binds;
    }

    public function getStartMicroTimestamp(): float
    {
        return $this->startMicroTimestamp;
    }

    public function getFinishMicroTimestamp(): float
    {
        return $this->finishMicroTimestamp;
    }

    public function getSqlTime(): float
    {
        return round($this->getFinishMicroTimestamp() - $this->getStartMicroTimestamp(), 4);
    }

}
