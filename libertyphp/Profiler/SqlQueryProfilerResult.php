<?php

namespace Libertyphp\Profiler;

class SqlQueryProfilerResult
{
    /** @var string */
    private $sql;

    /** @var array|null */
    private $binds;

    /** @var float */
    private $startMicroTimestamp;

    /** @var float */
    private $finishMicroTimestamp;

    public function __construct($sql, array $binds = null)
    {
        $this->sql = $sql;
        $this->binds = $binds;
        $this->startMicroTimestamp = microtime(true);
    }

    public function setFinishMicroTimestamp($microTimestamp)
    {
        $this->finishMicroTimestamp = $microTimestamp;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @return array|null
     */
    public function getBinds()
    {
        return $this->binds;
    }

    /**
     * @return float
     */
    public function getStartMicroTimestamp()
    {
        return $this->startMicroTimestamp;
    }

    /**
     * @return float
     */
    public function getFinishMicroTimestamp()
    {
        return $this->finishMicroTimestamp;
    }

    /**
     * @return float
     */
    public function getSqlTime()
    {
        return round($this->getFinishMicroTimestamp() - $this->getStartMicroTimestamp(), 4);
    }

}
