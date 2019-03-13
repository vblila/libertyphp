<?php

namespace Libertyphp\Profiler;

class ResponseProfiler
{
    /** @var float */
    private $startMicroTimestamp;

    /** @var SqlQueryProfilerResult[] */
    private $sqlQueryProfilerResults = [];

    /** @var bool */
    private $isEnabled = false;

    public function __construct()
    {
        $this->startMicroTimestamp = microtime(true);
    }

    public function setEnabled(bool $isEnabled): ResponseProfiler
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function addSqlQueryProfilerResult(SqlQueryProfilerResult $result): ResponseProfiler
    {
        $this->sqlQueryProfilerResults[] = $result;
        return $this;
    }

    /**
     * @return SqlQueryProfilerResult[]
     */
    public function getSqlQueryProfilerResults(): array
    {
        return $this->sqlQueryProfilerResults;
    }

    public function getSqlQueriesCount(): int
    {
        return count($this->getSqlQueryProfilerResults());
    }

    public function getSqlQueriesTotalTime(): float
    {
        $time = 0;
        foreach ($this->getSqlQueryProfilerResults() as $queryProfilerResult) {
            $time += $queryProfilerResult->getFinishMicroTimestamp() - $queryProfilerResult->getStartMicroTimestamp();
        }

        return round($time, 4);
    }
}
