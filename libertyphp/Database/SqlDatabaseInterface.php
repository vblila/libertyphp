<?php

namespace Libertyphp\Database;

interface SqlDatabaseInterface
{
    public function __construct($dsn, $user, $password);

    /**
     * @return string
     */
    public function getLastInsertedId();

    /**
     * @param string $sql
     * @param array $binds
     *
     * @return array
     *
     * @throws SqlQueryException
     */
    public function select($sql, array $binds = []);

    /**
     * @param string $sql
     * @param array $binds
     *
     * @return array|null
     *
     * @throws SqlQueryException
     */
    public function selectRow($sql, array $binds = []);

    /**
     * @param string $sql
     * @param array $binds
     *
     * @throws SqlQueryException
     */
    public function execute($sql, array $binds = []);

    /**
     * @param string $tableName
     * @param array $batchColumnValues
     *
     * @throws SqlQueryException
     */
    public function batchInsert($tableName, array $batchColumnValues);

    public function beginTransaction();

    public function commitTransaction();

    public function rollbackTransaction();
}
