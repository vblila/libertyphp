<?php

namespace Libertyphp\Database;

use Libertyphp\Profiler\ResponseProfiler;

interface SqlDatabaseInterface
{
    public function __construct(string $dsn, string $user, string $password, ResponseProfiler $responseProfiler = null);

    public function getLastInsertedId(): string;

    /**
     * @param string $sql
     * @param array $binds
     *
     * @return array
     *
     * @throws SqlQueryException
     */
    public function select(string $sql, array $binds = []): array;

    /**
     * @param string $sql
     * @param array $binds
     *
     * @return array|null
     *
     * @throws SqlQueryException
     */
    public function selectRow(string $sql, array $binds = []): ?array;

    /**
     * @param string $sql
     * @param array $binds
     *
     * @throws SqlQueryException
     */
    public function execute(string $sql, array $binds = []);

    /**
     * @param string $tableName
     * @param array $batchColumnValues
     *
     * @throws SqlQueryException
     */
    public function batchInsert(string $tableName, array $batchColumnValues);

    public function beginTransaction();

    public function commitTransaction();

    public function rollbackTransaction();
}
