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

    /**
     * @param bool $isEnabled
     * @return $this
     */
    public function setEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param SqlQueryProfilerResult $result
     */
    public function addSqlQueryProfilerResult(SqlQueryProfilerResult $result)
    {
        $this->sqlQueryProfilerResults[] = $result;
    }

    /**
     * @return SqlQueryProfilerResult[]
     */
    public function getSqlQueryProfilerResults()
    {
        return $this->sqlQueryProfilerResults;
    }

    /**
     * @return int
     */
    public function getSqlQueriesCount()
    {
        return count($this->getSqlQueryProfilerResults());
    }

    /**
     * @return float
     */
    public function getSqlQueriesTotalTime()
    {
        $time = 0;
        foreach ($this->getSqlQueryProfilerResults() as $queryProfilerResult) {
            $time += $queryProfilerResult->getFinishMicroTimestamp() - $queryProfilerResult->getStartMicroTimestamp();
        }

        return round($time, 4);
    }
}
