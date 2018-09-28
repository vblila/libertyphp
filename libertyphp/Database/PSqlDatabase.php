<?php

namespace Libertyphp\Database;

use Libertyphp\Profiler\ResponseProfiler;
use Libertyphp\Profiler\SqlQueryProfilerResult;
use PDO;

class PSqlDatabase implements SqlDatabaseInterface
{
    /** @var PDO */
    private $pdoConnection;

    /** @var ResponseProfiler */
    private $responseProfiler;

    public function __construct($dsn, $user, $password, ResponseProfiler $responseProfiler = null)
    {
        $this->pdoConnection = new PDO($dsn, $user, $password);
        $this->responseProfiler = $responseProfiler;
    }

    public function getLastInsertedId()
    {
        return $this->pdoConnection->lastInsertId();
    }

    public function select($sql, array $binds = [])
    {
        $sqlQueryProfilerResult = new SqlQueryProfilerResult($sql, $binds);

        $pdoStatement = $this->pdoConnection->prepare($sql);
        if (!$pdoStatement->execute($binds)) {
            throw new SqlQueryException(join("\n", $pdoStatement->errorInfo()));
        }

        $rows = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

        $sqlQueryProfilerResult->setFinishMicroTimestamp(microtime(true));
        if ($this->responseProfiler) {
            $this->responseProfiler->addSqlQueryProfilerResult($sqlQueryProfilerResult);
        }

        return $rows;
    }

    public function selectRow($sql, array $binds = [])
    {
        $result = $this->select($sql, $binds);
        return $result ? $result[0] : null;
    }

    public function execute($sql, array $binds = [])
    {
        $sqlQueryProfilerResult = new SqlQueryProfilerResult($sql, $binds);

        $pdoStatement = $this->pdoConnection->prepare($sql);
        $result = $pdoStatement->execute($binds);

        $sqlQueryProfilerResult->setFinishMicroTimestamp(microtime(true));
        if ($this->responseProfiler) {
            $this->responseProfiler->addSqlQueryProfilerResult($sqlQueryProfilerResult);
        }

        if (!$result) {
            throw new SqlQueryException(join("\n", $pdoStatement->errorInfo()));
        }
    }

    public function batchInsert($tableName, array $batchColumnValues)
    {
        $columns = array_keys($batchColumnValues[0]);
        $columnsString = join(', ', $columns);

        $manyValues = [];
        $binds = [];

        foreach ($batchColumnValues as $index => $columnValues) {
            $values = [];
            foreach ($columnValues as $column => $value) {
                $values[] = ":{$column}_{$index}";
                $binds["{$column}_{$index}"] = $value;
            }

            $manyValues[] = '(' . join(',', $values) . ')';
        }

        $valuesString = join(',', $manyValues);

        $this->execute("INSERT INTO {$tableName} ({$columnsString}) VALUES {$valuesString}", $binds);
    }

    public function beginTransaction()
    {
        if (!$this->pdoConnection->inTransaction()) {
            $this->pdoConnection->beginTransaction();
        }
    }

    public function commitTransaction()
    {
        if ($this->pdoConnection->inTransaction()) {
            $this->pdoConnection->commit();
        }
    }

    public function rollbackTransaction()
    {
        if ($this->pdoConnection->inTransaction()) {
            $this->pdoConnection->rollBack();
        }
    }
}
